<?php

use FintechFab\BankEmulator\Components\Helpers\Time;
use FintechFab\BankEmulator\Components\Processor\BankCard;
use FintechFab\BankEmulator\Components\Processor\Processor;
use FintechFab\BankEmulator\Components\Processor\Secure;
use FintechFab\BankEmulator\Components\Processor\Status;
use FintechFab\BankEmulator\Components\Processor\Type;
use FintechFab\BankEmulator\Models\Payment;

class ProcessorPaymentTest extends BankEmulatorTestCase
{


	public function setUp()
	{
		parent::setUp();
		$this->mockTerminal();
		Payment::truncate();
	}


	public function testSale()
	{

		$input = $this->doPrepareAuthInput();
		$input['pan'] = BankCard::getValidCustomPan();

		$processor = $this->makeProcessor($input, 'sale');
		$processor->response();
		$payment = $processor->item();
		$this->assertEquals('00', $payment->rc);

	}

	public function testSaleRefund()
	{

		$input = $this->doPrepareAuthInput();
		$input['pan'] = BankCard::getValidCustomPan();

		$processor = $this->makeProcessor($input, 'sale');
		$processor->response();
		$payment = $processor->item();
		$this->assertEquals('00', $payment->rc);

		$input = array(
			'term'   => $processor->item()->term,
			'order'  => $processor->item()->order,
			'amount' => $processor->item()->amount,
			'cur'    => $processor->item()->cur,
			'rrn'    => $processor->item()->rrn,
			'irn'    => $processor->item()->irn,
			'time'   => Time::ts(),
		);

		$processor = $this->makeProcessor($input, 'refund');
		$processor->response();
		$payment = $processor->item();
		$this->assertEquals('00', $payment->rc);

	}

	public function testAuthCompleteRefund()
	{

		// auth
		$input = $this->doPrepareAuthInput();
		$input['pan'] = BankCard::getValidCustomPan();

		$processor = $this->makeProcessor($input, 'auth');
		$processor->response();
		$paymentAuth = $processor->item();
		$this->assertEquals('00', $paymentAuth->rc);

		// complete
		$input = array(
			'term'   => $processor->item()->term,
			'order'  => $processor->item()->order,
			'amount' => $processor->item()->amount,
			'cur'    => $processor->item()->cur,
			'rrn'    => $processor->item()->rrn,
			'irn'    => $processor->item()->irn,
			'time'   => Time::ts(),
		);

		$processor = $this->makeProcessor($input, 'complete');
		$processor->response();
		$paymentComplete = $processor->item();
		$this->assertEquals('00', $paymentComplete->rc);

		// refund
		$input = array(
			'term'   => $processor->item()->term,
			'order'  => $processor->item()->order,
			'amount' => $processor->item()->amount,
			'cur'    => $processor->item()->cur,
			'rrn'    => $processor->item()->rrn,
			'irn'    => $processor->item()->irn,
			'time'   => Time::ts(),
		);

		$processor = $this->makeProcessor($input, 'refund');
		$processor->response();
		$paymentRefund = $processor->item();
		$this->assertEquals('00', $paymentRefund->rc);

	}

	public function testDoubleAuth()
	{

		$input = $this->doPrepareAuthInput();
		$input['pan'] = BankCard::getValidCustomPan();

		$processor = $this->makeProcessor($input, 'auth');
		$processor->response();
		$payment = $processor->item();
		$this->assertEquals('00', $payment->rc);

		$processor = $this->makeProcessor($input, 'auth');
		$processor->response();
		$payment = $processor->item();
		$this->assertEquals('-3', $payment->rc);

	}


	public function testAuthComplete()
	{

		$input = $this->doPrepareAuthInput();
		$input['pan'] = BankCard::getValidCustomPan();

		$processor = $this->makeProcessor($input, 'auth');
		$processor->response();
		$payment = $processor->item();
		$this->assertEquals('00', $payment->rc);

		$input = array(
			'term'   => $processor->item()->term,
			'order'  => $processor->item()->order,
			'amount' => $processor->item()->amount,
			'cur'    => $processor->item()->cur,
			'rrn'    => $processor->item()->rrn,
			'irn'    => $processor->item()->irn,
			'time'   => Time::ts(),
		);

		$processor = $this->makeProcessor($input, 'complete');
		$processor->response();
		$payment = $processor->item();
		$this->assertEquals('00', $payment->rc);

	}


	/**
	 * @param string $type
	 * @param array  $input
	 * @param array  $dataPayment
	 *
	 * @return void
	 * @dataProvider response
	 */
	public function testPayment($type, $input, $dataPayment = array())
	{
		/**
		 * @var Processor $opProcessor
		 */

		Secure::sign($input, $type, 'secret');
		$opType = new Type($type, $input);
		$opProcessor = App::make(
			'FintechFab\BankEmulator\Components\Processor\Processor',
			array($opType)
		);
		$opProcessor->response();
		$payment = $opProcessor->item();

		if ($dataPayment) {
			foreach ($dataPayment as $key => $val) {

				if ($val === '#exists#') {
					// custom value, not empty
					$this->assertNotEmpty($payment->$key, 'Value of key ' . $key . ' is required here!');
				} else {
					// concrete value
					$this->assertEquals($val, $payment->$key, print_r($dataPayment, true) . print_r($payment->toArray(), true));
				}
			}
		}

	}

	public static function response()
	{

		$list = array(


			// bad card (rc=14)

			__LINE__ => array(
				'type'        => 'auth',
				'input'       => array(
					'term'   => '123456',
					'pan'    => '4024007162441306', // no such card number
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
				'dataPayment' => array(
					'term'     => '123456',
					'type'     => 'auth',
					'order'    => '234',
					'amount'   => '10.00',
					'cur'      => 'rub',
					'rc'       => '14',
					'approval' => '',
					'irn'      => '',
					'rrn'      => '',
					'status'   => Status::ERROR,
					'time'     => date('Y-m-d H:i:s', self::time()),
				),
			),

			// bad card cvc (rc=-1)

			__LINE__ => array(
				'type'        => 'auth',
				'input'       => array(
					'term'   => '123456',
					'pan'    => '5187523492954370', // Mastercard // success card
					'year'   => '16',
					'month'  => '06',
					'cvc'    => '123', // bad cvc
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
				'dataPayment' => array(
					'term'     => '123456',
					'type'     => 'auth',
					'order'    => '234',
					'amount'   => '10.00',
					'cur'      => 'rub',
					'rc'       => '-1',
					'approval' => '',
					'irn'      => '',
					'rrn'      => '',
					'status'   => Status::ERROR,
					'time'     => date('Y-m-d H:i:s', self::time()),
				),
			),

			// bad card (rc=96)

			__LINE__ => array(
				'type'        => 'auth',
				'input'       => array(
					'term'   => '123456',
					'pan'    => '4716404752339797', // VISA // System Malfunction
					'year'   => '16',
					'month'  => '06',
					'cvc'    => '777', // correct cvc
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
				'dataPayment' => array(
					'term'     => '123456',
					'type'     => 'auth',
					'order'    => '234',
					'amount'   => '10.00',
					'cur'      => 'rub',
					'rc'       => '96',
					'approval' => '',
					'irn'      => '',
					'rrn'      => '',
					'status'   => Status::ERROR,
					'time'     => date('Y-m-d H:i:s', self::time()),
				),
			),

			// good card (rc=00)

			__LINE__ => array(
				'type'        => 'auth',
				'input'       => array(
					'term'   => '123456',
					'pan'    => '4532274626451231', // VISA
					'year'   => '16',
					'month'  => '06',
					'cvc'    => '777', // correct cvc non 3ds
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
				'dataPayment' => array(
					'term'     => '123456',
					'type'     => 'auth',
					'order'    => '234',
					'amount'   => '10.00',
					'cur'      => 'rub',
					'rc'       => '00',
					'approval' => '#exists#',
					'irn'      => '#exists#',
					'rrn'      => '#exists#',
					'status'   => Status::SUCCESS,
					'time'     => date('Y-m-d H:i:s', self::time()),
				),
			),


		);

		// sign4all responses
		foreach ($list as &$value) {
			if (empty($value['input']['sign'])) {
				Secure::sign($value['input'], $value['type'], 'secret');
			}
		}

		return $list;

	}

	private function doPrepareAuthInput()
	{

		return array(
			'term'   => '123456',
			'pan'    => '',
			'year'   => '16',
			'month'  => '06',
			'cvc'    => '777',
			'amount' => '10.00',
			'cur'    => 'rub',
			'order'  => '234',
			'name'   => 'Shop Name',
			'desc'   => 'Excellent Shoes Boutique',
			'url'    => 'http://example.com',
			'email'  => 'bank-emulator@example.com',
			'time'   => self::time(),
			'back'   => 'http://example.com/payment/order/234',
		);

	}

	/**
	 * @param $input
	 * @param $type
	 *
	 * @return Processor
	 */
	private function makeProcessor($input, $type)
	{
		Secure::sign($input, $type, 'secret');
		$opType = new Type($type, $input);
		$opProcessor = App::make(
			'FintechFab\BankEmulator\Components\Processor\Processor',
			array($opType)
		);

		return $opProcessor;
	}

}