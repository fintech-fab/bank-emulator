<?php


use FintechFab\BankEmulator\Components\Processor\Processor;
use FintechFab\BankEmulator\Components\Processor\ProcessorException;
use FintechFab\BankEmulator\Components\Processor\Secure;
use FintechFab\BankEmulator\Components\Processor\Status;
use FintechFab\BankEmulator\Components\Processor\Type;

class ProcessorResponseTest extends BankEmulatorTestCase
{


	public function setUp()
	{
		parent::setUp();
		$this->mockTerminal();
	}

	/**
	 * @param string $type
	 * @param array  $input
	 * @param array  $dataResponse
	 *
	 * @return void
	 * @dataProvider response
	 */
	public function testResponse($type, $input, $dataResponse = array())
	{
		/**
		 * @var Processor $opProcessor
		 */

		$this->mockPayment($type, $input, $dataResponse);
		Secure::sign($input, $type, 'secret');
		$opType = new Type($type, $input);
		$opProcessor = App::make(
			'FintechFab\BankEmulator\Components\Processor\Processor',
			array($opType)
		);
		$response = $opProcessor->response();

		if ($dataResponse) {
			foreach ($dataResponse as $key => $val) {
				$this->assertEquals($val, $response->$key, print_r($dataResponse, true) . print_r($response, true));
			}
		}

	}

	/**
	 * @param string $type
	 * @param array  $input
	 * @param string $exceptionCode
	 *
	 * @return void
	 * @dataProvider responseSignFail
	 */
	public function testSignFail($type, $input, $exceptionCode = null)
	{
		/**
		 * @var Processor $opProcessor
		 */

		$opType = new Type($type, $input);
		$opProcessor = App::make(
			'FintechFab\BankEmulator\Components\Processor\Processor',
			array($opType)
		);

		try {
			$opProcessor->response();
			$this->assertFalse(true, 'Exception mus be here with code: ' . $exceptionCode);
		} catch (ProcessorException $e) {
			$this->assertEquals($exceptionCode, $e->getCode());
		}


	}

	public static function response()
	{

		$list = array(
			__LINE__ => array(
				'type'     => 'auth',
				'input'    => array(
					'term'   => '123456',
					'pan'    => '4024007162441306',
					'year'   => '16',
					'month'  => '06',
					'cvc'    => '123',
					'amount' => '10.00',
					'cur'    => 'rub',
					'order'  => '234',
					'name'   => 'Shop Name',
					'desc'   => 'Excellent Shoes Boutique',
					'url'    => 'http://example.com',
					'email'  => 'bank-emulator@example.com',
					'time'   => self::time(),
					'back'   => 'http://example.com/payment/order/234',
				),
				'response' => array(
					'pan'      => '4024***1306',
					'term'     => '123456',
					'type'     => 'auth',
					'order'    => '234',
					'amount'   => '10.00',
					'cur'      => 'rub',
					'rc'       => '00',
					'approval' => '456456',
					'irn'      => '12345',
					'rrn'      => 'ABCD',
					'status'   => Status::PROCESSED,
					'time'     => self::time(),
				),
			),

			__LINE__ => array(
				'type'     => 'complete',
				'input'    => array(
					'term'   => '123456',
					'order'  => '234',
					'amount' => '10.00',
					'cur'    => 'rub',
					'irn'    => '12345',
					'rrn'    => 'ABCD',
					'time'   => self::time(),
				),
				'response' => array(
					'pan'      => '4024***1306',
					'term'     => '123456',
					'type'     => 'complete',
					'order'    => '234',
					'amount'   => '10.00',
					'cur'      => 'rub',
					'rc'       => '00',
					'approval' => '456456',
					'irn'      => '12345',
					'rrn'      => 'ABCD',
					'status'   => Status::PROCESSED,
					'time'     => self::time(),
				),
			),

			__LINE__ => array(
				'type'     => 'refund',
				'input'    => array(
					'term'   => '123456',
					'order'  => '234',
					'amount' => '10.00',
					'cur'    => 'rub',
					'irn'    => '12345',
					'rrn'    => 'ABCD',
					'time'   => self::time(),
				),
				'response' => array(
					'pan'      => '4024***1306',
					'term'     => '123456',
					'type'     => 'refund',
					'order'    => '234',
					'amount'   => '10.00',
					'cur'      => 'rub',
					'rc'       => '00',
					'approval' => '456456',
					'irn'      => '12345',
					'rrn'      => 'ABCD',
					'status'   => Status::PROCESSED,
					'time'     => self::time(),
				),
			),

			__LINE__ => array(
				'type'     => 'sale',
				'input'    => array(
					'term'   => '123456',
					'pan'    => '4024007162441306',
					'year'   => '16',
					'month'  => '06',
					'cvc'    => '123',
					'to'     => '5488406258780047',
					'amount' => '10.00',
					'cur'    => 'rub',
					'order'  => '234',
					'name'   => 'Shop Name',
					'desc'   => 'Excellent Shoes Boutique',
					'url'    => 'http://example.com',
					'email'  => 'bank-emulator@example.com',
					'time'   => self::time(),
					'back'   => 'http://example.com/payment/order/234',
				),
				'response' => array(
					'pan'      => '4024***1306',
					'term'     => '123456',
					'type'     => 'sale',
					'order'    => '234',
					'amount'   => '10.00',
					'cur'      => 'rub',
					'rc'       => '00',
					'approval' => '456456',
					'irn'      => '12345',
					'rrn'      => 'ABCD',
					'status'   => Status::PROCESSED,
					'time'     => self::time(),
				),
			),

			__LINE__ => array(
				'type'     => 'payment',
				'input'    => array(
					'term'   => '123456',
					'pan'    => '4024007162441306',
					'year'   => '16',
					'month'  => '06',
					'cvc'    => '123',
					'amount' => '10.00',
					'cur'    => 'rub',
					'order'  => '234',
					'name'   => 'Shop Name',
					'desc'   => 'Excellent Shoes Boutique',
					'url'    => 'http://example.com',
					'email'  => 'bank-emulator@example.com',
					'time'   => self::time(),
					'back'   => 'http://example.com/payment/order/234',
				),
				'response' => array(
					'pan'      => '4024***1306',
					'term'     => '123456',
					'type'     => 'payment',
					'order'    => '234',
					'amount'   => '10.00',
					'cur'      => 'rub',
					'rc'       => '00',
					'approval' => '456456',
					'irn'      => '12345',
					'rrn'      => 'ABCD',
					'status'   => Status::PROCESSED,
					'time'     => self::time(),
				),
			),
		);

		// sign4all responses
		foreach ($list as &$value) {
			if (empty($value['response']['sign'])) {
				Secure::sign($value['response'], $value['type'], 'secret');
			}
		}

		return $list;

	}

	public static function responseSignFail()
	{

		$list = array(
			__LINE__ => array(
				'type'          => 'auth',
				'input'         => array(
					'term'   => '123456',
					'pan'    => '4024007162441306',
					'year'   => '16',
					'month'  => '06',
					'cvc'    => '123',
					'amount' => '10.00',
					'cur'    => 'rub',
					'order'  => '234',
					'name'   => 'Shop Name',
					'desc'   => 'Excellent Shoes Boutique',
					'url'    => 'http://example.com',
					'email'  => 'bank-emulator@example.com',
					'time'   => self::time(),
					'back'   => 'http://example.com/payment/order/234',
					'sign'   => '123',
				),
				'exceptionCode' => ProcessorException::INVALID_SIGN,
			),
			__LINE__ => array(
				'type'          => 'auth',
				'input'         => array(
					'term'   => '12345',
					'pan'    => '4024007162441306',
					'year'   => '16',
					'month'  => '06',
					'cvc'    => '123',
					'amount' => '10.00',
					'cur'    => 'rub',
					'order'  => '234',
					'name'   => 'Shop Name',
					'desc'   => 'Excellent Shoes Boutique',
					'url'    => 'http://example.com',
					'email'  => 'bank-emulator@example.com',
					'time'   => self::time(),
					'back'   => 'http://example.com/payment/order/234',
				),
				'exceptionCode' => ProcessorException::INVALID_TERMINAL,
			),


		);


		// sign4all requests
		foreach ($list as &$value) {
			if (empty($value['input']['sign'])) {
				Secure::sign($value['input'], $value['type'], 'secret');
			}
		}

		return $list;

	}

}