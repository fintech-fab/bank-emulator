<?php

use FintechFab\BankEmulator\Components\Helpers\Time;
use FintechFab\BankEmulator\Components\Processor\BankCard;
use FintechFab\BankEmulator\Components\Processor\ProcessorException;
use FintechFab\BankEmulator\Components\Processor\Secure;
use FintechFab\BankEmulator\Components\Processor\Type;
use FintechFab\BankEmulator\Models\Payment;
use FintechFab\BankEmulator\Models\Terminal;

class ProcessorGatewayTest extends BankEmulatorTestCase
{

	/**
	 * @var Terminal
	 */
	private $term;

	public function setUp()
	{
		parent::setUp();

		Terminal::truncate();
		Payment::truncate();
		$this->term = new Terminal;
		$this->term->id = 123;
		$this->term->secret = 'secret';
		$this->term->save();
	}


	public function testCorrect()
	{

		// auth
		$input = $this->doPrepareAuthInput();
		$input['pan'] = BankCard::getValidCustomPan();
		$data = $this->callGateway(Type::AUTH, $input);
		$this->assertEquals('00', $data->rc, print_r($data, true));

		// double
		$dataError = $this->callGateway(Type::AUTH, $input);
		$this->assertEquals('-3', $dataError->rc, print_r($dataError, true));

		// complete
		$input = array(
			'term'   => $data->term,
			'order'  => $data->order,
			'amount' => $data->amount,
			'cur'    => $data->cur,
			'rrn'    => $data->rrn,
			'irn'    => $data->irn,
			'time'   => Time::ts(),
		);
		$data = $this->callGateway(Type::COMPLETE, $input);
		$this->assertEquals('00', $data->rc, print_r($data, true));
		$count = Payment::count();

		// double complete
		$dataError = $this->callGateway(Type::COMPLETE, $input);
		$this->assertEquals('00', $dataError->rc, print_r($dataError, true));
		$this->assertEquals($count, Payment::count());

		// refund
		$data = $this->callGateway(Type::REFUND, $input);
		$this->assertEquals('00', $data->rc, print_r($data, true));
		$count = Payment::count();

		// double refund
		$data = $this->callGateway(Type::REFUND, $input);
		$this->assertEquals('00', $data->rc, print_r($data, true));
		$this->assertEquals($count, Payment::count());

	}


	public function testFail()
	{

		$input = $this->doPrepareAuthInput();
		$input['pan'] = 12345678910123;
		$data = $this->callGateway(Type::AUTH, $input);
		$this->assertEquals(ProcessorException::INVALID_PAN, $data->code, print_r($data, true));

		$input = $this->doPrepareAuthInput();
		$input['pan'] = BankCard::getValidCustomPan();
		$input['amount'] = '0.00';
		$data = $this->callGateway(Type::AUTH, $input);
		$this->assertEquals(ProcessorException::INVALID_PARAM, $data->code, print_r($data, true));


	}


	private function callGateway($type, $input)
	{
		Secure::sign($input, $type, $this->term->secret);
		$response = $this->call(
			'POST',
			URL::route('ff-bank-em-gateway'),
			array(
				'type'  => $type,
				'input' => $input
			)
		);
		$json = $response->getContent();
		$data = json_decode($json);

		return $data;
	}

	private function doPrepareAuthInput()
	{

		return array(
			'term'   => '123',
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


}