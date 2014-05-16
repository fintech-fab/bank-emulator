<?php

namespace FintechFab\BankEmulator\Components\Processor;


use FintechFab\BankEmulator\Models\Terminal;

class Type
{

	const AUTH = 'auth';
	const COMPLETE = 'complete';
	const SALE = 'sale';
	const REFUND = 'refund';
	const PAYMENT = 'payment';
	const ENDPOINT = 'endpoint';

	private static $typeList = array(
		self::AUTH,
		self::SALE,
		self::COMPLETE,
		self::REFUND,
		self::PAYMENT,
		self::ENDPOINT,
	);

	public static $typeNames = array(
		self::AUTH     => 'Авторизационный',
		self::SALE     => 'Продажа',
		self::COMPLETE => 'Завершение продажи',
		self::REFUND   => 'Отмена платежа',
		self::PAYMENT  => 'Онлайн-платеж',
		self::ENDPOINT => 'Переход на онлайн-платеж',
	);


	public static $fields = array(

		self::AUTH     => array(
			'term',
			'pan',
			'year',
			'month',
			'cvc',
			'amount',
			'cur',
			'order',
			'name',
			'desc',
			'url',
			'email',
			'time',
			'sign',
			'back',
		),

		self::PAYMENT  => array(
			'term',
			'pan',
			'year',
			'month',
			'cvc',
			'amount',
			'cur',
			'order',
			'name',
			'desc',
			'url',
			'email',
			'time',
			'sign',
			'back',
		),

		self::ENDPOINT => array(
			'term',
			'amount',
			'cur',
			'order',
			'name',
			'desc',
			'url',
			'email',
			'time',
			'back',
			'sign',
		),

		self::SALE     => array(
			'term',
			'pan',
			'year',
			'month',
			'cvc',
			'to',
			'amount',
			'cur',
			'order',
			'name',
			'desc',
			'url',
			'email',
			'time',
			'sign',
			'back',
		),

		self::COMPLETE => array(
			'term',
			'order',
			'amount',
			'cur',
			'rrn',
			'irn',
			'time',
			'sign',
		),

		self::REFUND   => array(
			'term',
			'order',
			'amount',
			'cur',
			'rrn',
			'irn',
			'time',
			'sign',
		),

	);

	private $type;

	/**
	 * @var Input
	 */
	private $input;

	public function __construct($type, $input)
	{

		$this->type = $type;
		$this->input = new Input($this, $input);

		if (!in_array($this->type, self::$typeList)) {
			throw new ProcessorException(ProcessorException::INVALID_TYPE);
		}

	}

	public static function clearInput($type, $input)
	{
		$fields = self::$fields[$type];
		foreach ($input as $k => $v) {
			if (!in_array($k, $fields)) {
				unset($input[$k]);
			}
		}

		return $input;
	}


	public function validate()
	{
		$this->input->validate();

		return true;
	}

	public function error()
	{
		return $this->input->error();
	}

	public function sid()
	{
		return $this->type;
	}

	public function fields()
	{
		return self::$fields[$this->type];
	}

	public function termId()
	{
		return $this->input->term;
	}

	public function inputs()
	{
		$fields = self::$fields[$this->sid()];
		$inputs = $this->input->all();
		foreach ($inputs as $key => $value) {
			if (!in_array($key, $fields)) {
				unset($inputs[$key]);
			}
		}
		if (isset($inputs['sign'])) {
			unset($inputs['sign']);
		}

		if (empty($inputs)) {
			dd($inputs);
		}

		return $inputs;
	}

	/**
	 * @param string $secret
	 *
	 * @throws ProcessorException
	 */
	public function validateSign($secret)
	{

		$inputData = $this->input->all();
		Secure::sign($inputData, $this->sid(), $secret);
		if ($inputData['sign'] !== $this->input->sign) {
			throw new ProcessorException(ProcessorException::INVALID_SIGN);
		}

	}


	public function validateTermEnabled($mode)
	{

		if ($mode === Terminal::C_STATE_DISABLED) {
			throw new ProcessorException(ProcessorException::TERMINAL_DISABLED);
		}

		if ($mode === Terminal::C_STATE_ONLINE && $this->sid() !== Type::PAYMENT) {
			throw new ProcessorException(ProcessorException::TERMINAL_DISABLED_GATE);
		}

	}

}