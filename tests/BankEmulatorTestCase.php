<?php


use FintechFab\BankEmulator\Components\Helpers\Time;
use FintechFab\BankEmulator\Models\Terminal;

class BankEmulatorTestCase extends TestCase
{


	public static $time = null;

	public function setUp()
	{
		parent::setUp();
	}

	public static function time()
	{
		return Time::ts();
	}


	protected function mockTerminal()
	{
		$term = Mockery::mock('FintechFab\BankEmulator\Models\Terminal');

		// existing terminal
		$term->shouldReceive('find')->withArgs(array('123456'))->andReturn($term);

		// terminal with attributes
		$term->shouldReceive('getAttribute')->withArgs(array('email'))->andReturn('');
		$term->shouldReceive('getAttribute')->withArgs(array('url'))->andReturn('');
		$term->shouldReceive('getAttribute')->withArgs(array('id'))->andReturn('123456');
		$term->shouldReceive('getAttribute')->withArgs(array('secret'))->andReturn('secret');
		$term->shouldReceive('getAttribute')->withArgs(array('mode'))->andReturn(Terminal::C_STATE_OFFLINE);
		$term->shouldReceive('newInstance')->andReturn($term);

		// undefined terminal
		$term->shouldReceive('find')->withArgs(array('12345'))->andReturn(null);

		// ioc
		$this->app->bind('FintechFab\BankEmulator\Models\Terminal', function () use ($term) {
			return $term;
		});

	}

	protected function mockPayment($type, $input, $dataResponse)
	{
		$payment = Mockery::mock('FintechFab\BankEmulator\Models\Payment');

		$payment->shouldReceive('newInstance')->andReturn($payment);
		$payment->shouldReceive('hasGetMutator')->andReturn(false);
		$payment->shouldReceive('setAttribute')->andReturn($payment);
		$payment->shouldReceive('getAttribute')->withArgs(array('id'))->andReturn(1);
		$payment->shouldReceive('getAttribute')->withArgs(array('rrn'))->andReturn($dataResponse['rrn']);
		$payment->shouldReceive('getAttribute')->withArgs(array('irn'))->andReturn($dataResponse['irn']);
		$payment->shouldReceive('getAttribute')->withArgs(array('status'))->andReturn($dataResponse['status']);
		$payment->shouldReceive('getAttribute')->withArgs(array('type'))->andReturn($type);
		$payment->shouldReceive('getAttribute')->withArgs(array('pan'))->andReturn(@$dataResponse['pan']);
		$payment->shouldReceive('getAttribute')->withArgs(array('cvc'))->andReturn(@$input['cvc']);
		$payment->shouldReceive('getAttribute')->withArgs(array('term'))->andReturn(@$input['term']);
		$payment->shouldReceive('getAttribute')->withArgs(array('order'))->andReturn(@$input['order']);
		$payment->shouldReceive('getAttribute')->withArgs(array('amount'))->andReturn(@$input['amount']);
		$payment->shouldReceive('getAttribute')->withArgs(array('cur'))->andReturn(@$input['cur']);
		$payment->shouldReceive('getAttribute')->withArgs(array('rc'))->andReturn(@$dataResponse['rc']);
		$payment->shouldReceive('getAttribute')->withArgs(array('approval'))->andReturn(@$dataResponse['approval']);
		$payment->shouldReceive('toArray')->andReturn($dataResponse);
		$payment->shouldReceive('success')->andReturn(true);
		$payment->shouldReceive('save')->andReturn(true);
		$payment->shouldReceive('mask')->andReturn(null);
		$payment->shouldReceive('callbackEmail')->andReturn(null);

		// ioc
		$this->app->bind('FintechFab\BankEmulator\Models\Payment', function () use ($payment) {
			return $payment;
		});

	}


} 