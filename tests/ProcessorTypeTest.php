<?php


use FintechFab\BankEmulator\Components\Processor\ProcessorException;
use FintechFab\BankEmulator\Components\Processor\Secure;
use FintechFab\BankEmulator\Components\Processor\Type;

class ProcessorTypeTest extends BankEmulatorTestCase
{


	public function setUp()
	{
		parent::setUp();
	}

	/**
	 * @param array  $input
	 * @param int    $code
	 * @param string $message
	 *
	 * @return void
	 *
	 * @dataProvider auth
	 */
	public function testAuth($input, $code = null, $message = null)
	{

		Secure::sign($input, 'auth', 'secret');

		$opType = new Type('auth', $input);

		try {
			$this->assertTrue($opType->validate());

			if ($code) {
				$this->assertFalse(true, 'Exception must be there with code ' . $code);
			}

		} catch (ProcessorException $e) {
			$this->assertEquals($code, $e->getCode(), $e->getMessage());
			$this->assertContains($message, $e->getMessage());
		}

	}


	public static function auth()
	{
		return array(
			__LINE__ => array(
				'input' => array(
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
			),
			__LINE__ => array(
				'input' => array(
					'term'   => '123456',
					'pan'    => '4024007162441306',
					'year'   => '16',
					'month'  => '06',
					'cvc'    => '123',
					'amount' => '0.01',
					'cur'    => 'rub',
					'order'  => '234',
					'name'   => 'Shop Name',
					'desc'   => 'Excellent Shoes Boutique',
					'url'    => 'http://example.com',
					'email'  => 'bank-emulator@example.com',
					'time'   => self::time(),
					'back'   => 'http://example.com/payment/order/234',
				),
			),
			__LINE__ => array(
				'input'   => array(
					'term'   => '123456',
					'pan'    => '4024007162441306',
					'year'   => '16',
					'month'  => '06',
					'cvc'    => '123',
					'amount' => '0.00',
					'cur'    => 'rub',
					'order'  => '234',
					'name'   => 'Shop Name',
					'desc'   => 'Excellent Shoes Boutique',
					'url'    => 'http://example.com',
					'email'  => 'bank-emulator@example.com',
					'time'   => self::time(),
					'back'   => 'http://example.com/payment/order/234',
				),
				'code'    => ProcessorException::INVALID_PARAM,
				'message' => 'amount',
			),
			__LINE__ => array(
				'input'   => array(
					'term'   => '123456',
					'pan'    => '4024007162442306',
					'year'   => '16',
					'month'  => '06',
					'cvc'    => '123',
					'amount' => '99.00',
					'cur'    => 'rub',
					'order'  => '234',
					'name'   => 'Shop Name',
					'desc'   => 'Excellent Shoes Boutique',
					'url'    => 'http://example.com',
					'email'  => 'bank-emulator@example.com',
					'time'   => self::time(),
					'back'   => 'http://example.com/payment/order/234',
				),
				'code'    => ProcessorException::INVALID_PAN,
				'message' => 'Invalid card number',
			),
			__LINE__ => array(
				'input'   => array(
					'term'   => '123456',
					'pan'    => '4024007162441306',
					'year'   => (date('n') == 1) ? date('y') - 1 : date('y'),
					'month'  => date('m'),
					'cvc'    => '123',
					'amount' => '99.00',
					'cur'    => 'rub',
					'order'  => '234',
					'name'   => 'Shop Name',
					'desc'   => 'Excellent Shoes Boutique',
					'url'    => 'http://example.com',
					'email'  => 'bank-emulator@example.com',
					'time'   => self::time(),
					'back'   => 'http://example.com/payment/order/234',
				),
				'code'    => ProcessorException::INVALID_EXPIRED,
				'message' => 'Expired card',
			),
			__LINE__ => array(
				'input'   => array(
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
					'time'   => self::time() - 60 * 60 - 3,
					'back'   => 'http://example.com/payment/order/234',
				),
				'code'    => ProcessorException::INVALID_TIME,
				'message' => 'Mismatch time',
			),
		);
	}

}