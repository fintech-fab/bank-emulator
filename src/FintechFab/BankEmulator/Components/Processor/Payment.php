<?php

namespace FintechFab\BankEmulator\Components\Processor;

use FintechFab\BankEmulator\Components\Helpers\Time;
use FintechFab\BankEmulator\Models\Payment as PaymentModel;

class Payment
{

	/**
	 * @var PaymentModel
	 */
	private $payment;

	public function __construct($type, $data, PaymentModel $payment)
	{
		$data['type'] = $type;
		$data['time'] = Time::dt($data['time']);
		$this->payment = $payment->newInstance($data);
	}

	/**
	 * first, save payment
	 */
	public function saveAsInit()
	{

		$this->initDoubleBeforeSave();

		if (empty($this->payment->rrn)) {
			$this->payment->rrn = mt_rand(111111111111, 999999999999);
		}
		if (empty($this->payment->irn)) {
			$this->payment->irn = substr(strtoupper(md5($this->payment->rrn . time())), 10, 10);
		}
		$this->payment->status = Status::PROCESSED;
		$this->payment->save();
	}

	/**
	 * Init exists payment if double operation
	 */
	private function initDoubleBeforeSave()
	{

		$type = new Type($this->payment->type, $this->payment->toArray());

		switch ($type->sid()) {

			case Type::COMPLETE:
			case Type::REFUND:

				$payment = self::find(
					$this->payment->term,
					$this->payment->order,
					$this->payment->irn,
					$this->payment->rrn,
					$type->sid(),
					array(Status::SUCCESS)
				);

				if ($payment) {
					$this->payment = $payment;
				}

				break;

		}

	}

	/**
	 * payment processing
	 */
	public function doProcess()
	{

		// card errors
		$rc = $this->doProcessCards();
		if ($rc !== '00') {
			$this->payment->rc = $rc;
			$this->payment->rrn = '';
			$this->payment->irn = '';
			$this->payment->status = Status::ERROR;
			$this->payment->save();

			return;
		}

		// process type errors
		$rc = $this->doProcessType();
		if ($rc !== '00') {
			$this->payment->rc = $rc;
			$this->payment->status = Status::ERROR;
			$this->payment->save();

			return;
		}

		// approved
		$this->payment->status = Status::SUCCESS;
		$this->payment->rc = '00';
		$this->payment->approval = mt_rand(100000, 999999);
		$this->payment->save();

	}

	/**
	 * RC process code by card numbers
	 *
	 * @return string $rc
	 */
	public function doProcessCards()
	{
		$pan = $this->payment->pan;
		$this->payment->mask('pan');

		$type = new Type($this->payment->type, $this->payment->toArray());
		$fields = $type->fields();
		if (!in_array('pan', $fields)) {
			return '00';
		}

		$cvc = $this->payment->cvc;
		$rc = BankCard::doCheckCard($pan, $cvc);

		if ($rc !== '00') {
			return $rc;
		}

		if ($type->sid() == Type::SALE) {
			$to = $this->payment->to;
			if ($to) {
				$rc = BankCard::doCheckCard($to);
			}
		}

		return $rc;

	}

	/**
	 * Process logic by payment Type
	 *
	 * @return string $rc
	 */
	private function doProcessType()
	{
		$type = new Type($this->payment->type, $this->payment->toArray());

		$paymentExist = self::findDouble(
			$this->payment->id,
			$this->payment->term,
			$this->payment->order,
			$this->payment->amount,
			$type->sid(),
			array(Status::SUCCESS, Status::PROCESSED)
		);

		if ($paymentExist) {
			return '-3';
		}

		switch ($type->sid()) {

			case Type::AUTH:
			case Type::SALE:
			case Type::PAYMENT:
				break;

			case Type::COMPLETE:

				// completing auth request
				$payment = self::find(
					$this->payment->term,
					$this->payment->order,
					$this->payment->irn,
					$this->payment->rrn,
					Type::AUTH,
					Status::SUCCESS
				);

				if (!$payment) {
					return '-2';
				}
				break;

			case Type::REFUND:

				// refund for finished processing: sale, complete, payment
				$payment = self::find(
					$this->payment->term,
					$this->payment->order,
					$this->payment->irn,
					$this->payment->rrn,
					array(Type::SALE, Type::COMPLETE, Type::PAYMENT),
					Status::SUCCESS
				);

				if (!$payment) {

					// refund for not finishing processing: auth
					$payment = self::find(
						$this->payment->term,
						$this->payment->order,
						$this->payment->irn,
						$this->payment->rrn,
						Type::AUTH,
						Status::SUCCESS
					);

					if (!$payment) {
						return '-2';
					}

				}
				break;

		}

		return '00';

	}

	/**
	 * @return PaymentModel
	 */
	public function item()
	{
		return $this->payment;
	}


	/**
	 * search payment
	 *
	 * @param string       $term
	 * @param string       $order
	 * @param string       $irn
	 * @param string       $rrn
	 * @param string|array $type
	 * @param string|array $status
	 *
	 * @return PaymentModel|null
	 */
	public static function find($term, $order, $irn, $rrn, $type = null, $status = null)
	{
		$payment = PaymentModel::whereTerm($term)
			->whereOrder($order)
			->whereIrn($irn)
			->whereRrn($rrn);

		if (!is_array($type)) {
			$type = (array)$type;
		}
		if (!is_array($status)) {
			$status = (array)$status;
		}

		if ($type) {
			$payment->whereIn('type', $type);
		}

		if ($status) {
			$payment->whereIn('status', $status);
		}
		$payment->orderBy('id', 'desc');

		$payment = $payment->first();

		return $payment;

	}

	/**
	 * search double payment
	 *
	 * @param int          $id current payment id
	 * @param string       $term
	 * @param string       $order
	 * @param string       $amount
	 * @param string|array $type
	 * @param string|array $status
	 *
	 * @return PaymentModel|null
	 */
	public static function findDouble($id, $term, $order, $amount, $type = null, $status = null)
	{
		$payment = PaymentModel::whereTerm($term)
			->whereOrder($order)
			->whereAmount($amount)
			->where('id', '!=', $id);

		if (!is_array($type)) {
			$type = (array)$type;
		}
		if (!is_array($status)) {
			$status = (array)$status;
		}

		if ($status) {
			$payment->whereIn('status', $status);
		}
		if ($type) {
			$payment->whereIn('type', $type);
		}

		// last hour
		$payment->whereRaw('created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)');

		$payment = $payment->first();

		return $payment;

	}

}