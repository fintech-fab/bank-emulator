<?php

namespace FintechFab\BankEmulator\Components\Processor;


/**
 * @property string $code
 * @property string $rc
 */
class Response
{

	/**
	 * @var array
	 */
	private $data;


	public static $responseFields = array(
		'term',
		'type',
		'order',
		'amount',
		'cur',
		'rc',
		'approval',
		'irn',
		'rrn',
		'status',
		'time',
		'auth',
	);

	/**
	 * @param array  $data
	 * @param string $type
	 * @param string $secret
	 */
	public function __construct(array $data, $type, $secret)
	{

		Secure::sign($data, $type, $secret);
		$this->data = $data;

	}

	public function __get($name)
	{
		return isset($this->data[$name])
			? $this->data[$name]
			: null;
	}


	public function error()
	{

	}

	public function data()
	{
		return $this->data;
	}

}