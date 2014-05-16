<?php

namespace FintechFab\BankEmulator\Components\Processor;


use App;
use FintechFab\BankEmulator\Components\Helpers\Time;
use FintechFab\BankEmulator\Models\Terminal;
use Queue;

class Processor
{

	/**
	 * @var Type
	 */
	private $type;

	/**
	 * @var Terminal
	 */
	private $term;

	/**
	 * @var \FintechFab\BankEmulator\Components\Processor\Payment
	 */
	private $payment;

	/**
	 * @var Response
	 */
	private $response;

	public function __construct(Type $type, Terminal $term)
	{
		$this->type = $type;
		$this->term = $term;
	}

	private function run()
	{

		$this->type->validate();
		$this->initTerm();
		$this->type->validateSign($this->term->secret);
		$this->type->validateTermEnabled($this->term->mode);

		$this->initPayment();

		$this->payment->doProcess();
		$this->sendCallbackUrl();

	}

	private function initTerm()
	{
		$term = $this->term->newInstance();
		$this->term = $term->find($this->type->termId());

		if (!$this->term) {
			throw new ProcessorException(ProcessorException::INVALID_TERMINAL);
		}

	}

	private function initPayment()
	{
		/**
		 * @var Payment $payment
		 */
		$payment = App::make('FintechFab\BankEmulator\Components\Processor\Payment', array(
			$this->type->sid(),
			$this->type->inputs()
		));

		$this->payment = $payment;

		$this->payment->saveAsInit();

	}

	/**
	 * @return Response|null
	 */
	public function response()
	{

		$this->run();

		switch ($this->type->sid()) {

			case Type::AUTH:
				return $this->auth();
				break;

			case Type::COMPLETE:
				return $this->complete();
				break;

			case Type::SALE:
				return $this->sale();
				break;

			case Type::REFUND:
				return $this->refund();
				break;

			case Type::PAYMENT:
				return $this->payment();
				break;

		}

		return null;

	}

	/**
	 * @return Response
	 */
	private function auth()
	{
		$data = $this->getResponseData();
		$this->response = new Response($data, Type::AUTH, $this->term->secret);

		return $this->response;
	}

	/**
	 * @return Response
	 */
	private function sale()
	{

		$data = $this->getResponseData();
		$this->response = new Response($data, Type::SALE, $this->term->secret);

		return $this->response;

	}

	/**
	 * @return Response
	 */
	private function payment()
	{

		$data = $this->getResponseData();
		$this->response = new Response($data, Type::PAYMENT, $this->term->secret);

		return $this->response;

	}

	/**
	 * @return Response
	 */
	private function complete()
	{

		$data = $this->getResponseData();
		$this->response = new Response($data, Type::COMPLETE, $this->term->secret);

		return $this->response;

	}

	/**
	 * @return Response
	 */
	private function refund()
	{

		$data = $this->getResponseData();
		$this->response = new Response($data, Type::REFUND, $this->term->secret);

		return $this->response;

	}

	public function item()
	{
		return $this->payment->item();
	}

	private function getResponseData()
	{
		$data = array(
			'term'     => $this->payment->item()->term,
			'type'     => $this->payment->item()->type,
			'order'    => $this->payment->item()->order,
			'amount'   => $this->payment->item()->amount,
			'cur'      => $this->payment->item()->cur,
			'rc'       => $this->payment->item()->rc,
			'approval' => $this->payment->item()->approval,
			'irn'      => $this->payment->item()->irn,
			'rrn'      => $this->payment->item()->rrn,
			'status'   => $this->payment->item()->status,
			'time'     => Time::ts(),
		);

		$rc = $this->payment->item()->rc;
		$pan = $this->payment->item()->pan;

		if ($pan) {
			$data['pan'] = $pan;
		}
		if ($rc !== '00') {
			if (isset(ProcessorException::$errors[$rc])) {
				$data['message'] = ProcessorException::$errors[$rc];
			}
		}

		Secure::sign($data, $this->type->sid(), $this->term->secret);

		return $data;

	}


	private function sendCallbackUrl()
	{

		$email = $this->payment->item()->callbackEmail();
		if (!$email) {
			$email = $this->term->email;
		}

		$url = $this->term->url;

		if (!$email && !$url) {
			return;
		}

		Queue::connection('ff-bank-em')
			->push('FintechFab\BankEmulator\Components\Processor\CallbackQueue', array(
				'url'   => $url,
				'data'  => $this->getResponseData(),
				'email' => $email,
			));

	}

}