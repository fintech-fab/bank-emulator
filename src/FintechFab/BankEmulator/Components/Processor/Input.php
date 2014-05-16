<?php

namespace FintechFab\BankEmulator\Components\Processor;


use Validator;

/**
 * @property string $term
 * @property string $sign
 */
class Input
{

	/**
	 * @var Type
	 */
	private $type;

	/**
	 * @var array
	 */
	private $input;

	/**
	 * @var \Illuminate\Validation\Validator
	 */
	private $validator = null;

	/**
	 * @var array
	 */
	public static $rules = array(
		'pan'      => 'required|digits_between:13,19',
		'year'     => 'required|digits:2',
		'month'    => 'required|digits:2',
		'cvc'      => 'required|digits:3',
		'amount'   => 'required|min:0.01|regex:/^\d+\.\d{2}$/',
		'cur'      => 'required|alpha|size:3',
		'order'    => 'required|digits_between:1,11',
		'name'     => 'max:30',
		'desc'     => 'max:100',
		'url'      => 'url|max:255',
		'email'    => 'email|max:50',
		'time'     => 'required|integer',
		'term'     => 'required|digits_between:1,11',
		'rrn'      => 'required|max:12',
		'irn'      => 'required|max:32',
		'to'       => 'max:50',
		'sign'     => 'required',
		'rc'       => 'size:2',
		'approval' => 'size:6',
		'auth'     => 'url',
		'back'     => 'url',
	);


	public static $paramNames = array(
		'pan'      => 'Номер банковской карты',
		'year'     => 'Год окончания действия карты',
		'month'    => 'Месяц окончания действия карты',
		'cvc'      => 'CVV/CVC',
		'amount'   => 'Сумма платежа',
		'cur'      => 'Валюта платежа',
		'order'    => 'Номер заказа Продавца',
		'name'     => 'Наименование заказа',
		'desc'     => 'Описание заказа',
		'url'      => 'Ссылка на интернет-магазин',
		'email'    => 'Email для сообщений об операциях',
		'time'     => 'Текущее время сервера продаца (timestamp)',
		'term'     => 'Терминал Продавца',
		'rrn'      => 'Retrieval reference number (ISO8583, 37)',
		'irn'      => 'Bank Emulation Internal Number',
		'to'       => 'Идентификатор назначения платежа',
		'sign'     => 'Подпись',
		'rc'       => 'Response code (ISO8583, 39)',
		'approval' => 'Authorization identification response (ISO8583, 38)',
		'auth'     => 'Ссылка для прохождения авторизации',
		'back'     => 'Ссылка на магазин после оплаты',
	);

	public function __construct(Type $type, array $input)
	{

		$this->type = $type;
		$this->input = $input;

	}

	public function __get($name)
	{
		return isset($this->input[$name])
			? $this->input[$name]
			: null;
	}

	public function validate()
	{

		$this->initValidator();

		if (!$this->validator->passes()) {
			throw new ProcessorException(ProcessorException::INVALID_PARAM, $this->error());
		}

		$this->validateAmount();
		$this->validatePan();
		$this->validateExpired();
		$this->validateTime();


		return true;

	}


	private function initValidator()
	{

		$fields = $this->type->fields();
		$rules = array();

		foreach ($fields as $value) {
			$rules[$value] = self::$rules[$value];
		}

		$this->validator = Validator::make($this->input, $rules);

	}

	public function error()
	{
		if ($this->validator->errors()) {
			return $this->validator->errors()->first();
		}

		return null;
	}


	private function validatePan()
	{
		if (empty($this->input['pan'])) {
			return;
		}

		if (!$this->isLuhn($this->input['pan'])) {
			throw new ProcessorException(
				ProcessorException::INVALID_PAN
			);
		}

	}

	public static function isLuhn($number)
	{

		settype($number, 'string');
		$sumTable = array(
			array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
			array(0, 2, 4, 6, 8, 1, 3, 5, 7, 9)
		);
		$sum = 0;
		$flip = 0;
		for ($i = strlen($number) - 1; $i >= 0; $i--) {
			$sum += $sumTable[$flip++ & 0x1][$number[$i]];
		}

		return $sum % 10 === 0;

	}

	private function validateExpired()
	{

		if (
			empty($this->input['year']) ||
			empty($this->input['month'])
		) {
			return;
		}

		$y = date('y');
		$m = date('m');
		$inputY = (int)$this->input['year'];
		$inputM = (int)$this->input['month'];

		if (
			(
				$inputY == $y &&
				$inputM <= $m
			) || (
				$inputY < $y
			)
		) {
			throw new ProcessorException(ProcessorException::INVALID_EXPIRED);
		}
	}

	private function validateTime()
	{
		if (
			$this->input['time'] < time() - 60 * 60 ||
			$this->input['time'] > time() + 60 * 60
		) {
			throw new ProcessorException(ProcessorException::INVALID_TIME);
		}
	}

	/**
	 * @return array
	 */
	public function all()
	{
		return $this->input;
	}


	public static function name($name)
	{
		return isset(self::$paramNames[$name])
			? self::$paramNames[$name]
			: null;
	}

	private function validateAmount()
	{
		if (empty($this->input['amount'])) {
			return;
		}

		$amount = intval(round($this->input['amount'], 2) * 100);
		if ($amount < 1) {
			throw new ProcessorException(ProcessorException::INVALID_PARAM, 'Invalid amount');
		}
	}

}