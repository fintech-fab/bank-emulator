<?php

namespace FintechFab\BankEmulator\Controllers;


use App;
use Config;
use Controller;
use FintechFab\BankEmulator\Components\Helpers\Time;
use FintechFab\BankEmulator\Components\Helpers\Views;
use FintechFab\BankEmulator\Components\Processor\Processor;
use FintechFab\BankEmulator\Components\Processor\ProcessorException;
use FintechFab\BankEmulator\Components\Processor\Secure;
use FintechFab\BankEmulator\Components\Processor\Type;
use FintechFab\BankEmulator\Models\Payment;
use FintechFab\BankEmulator\Models\Terminal;
use Input;
use Log;
use Redirect;
use Request;
use URL;
use Validator;
use View;

class DemoController extends Controller
{


	/**
	 * main info page
	 */
	public function index()
	{
		$this->make('index');
	}

	/**
	 * Terminal info
	 * auto-create new term if not exists
	 */
	public function term()
	{
		$terminal = Terminal::whereUserId($this->userId())->first();
		if (!$terminal) {
			$terminal = new Terminal;
			$terminal->user_id = $this->userId();
			$terminal->secret = md5($terminal->user_id . time() . uniqid());
			$terminal->mode = Terminal::C_STATE_OFFLINE;
			$terminal->save();
		}

		$this->make('term', compact('terminal'));
	}

	/**
	 * Change term options
	 *
	 * @return string
	 */
	public function postTerm()
	{
		$terminal = Terminal::whereUserId($this->userId())->first();
		$inputs = Input::get('input');

		if (empty($inputs['url'])) {
			$inputs['url'] = '';
		}
		if (empty($inputs['email'])) {
			$inputs['email'] = '';
		}

		$validator = Validator::make(
			$inputs,
			array(
				'url'   => 'url',
				'email' => 'email',
			)
		);

		if ($terminal && $validator->passes()) {
			$terminal->url = $inputs['url'];
			$terminal->email = $inputs['email'];
			$terminal->save();

			return 'ok';
		}

		return 'error';

	}

	/**
	 * error page
	 */
	public function error()
	{
		$this->make('error');
	}

	/**
	 * Create signature for payment form
	 */
	public function sign()
	{
		$type = Input::get('type');
		$input = Input::get('input');
		$termId = $input['term'];
		$term = Terminal::find($termId);

		$input = Type::clearInput($type, $input);
		Secure::sign($input, $type, $term->secret);

		return $input['sign'];

	}

	/**
	 * Pull payment callbacks
	 */
	public function callback()
	{
		$input = $this->getVerifiedInput('callback', Input::get('type'), Input::all());
		if ($input) {
			// your business processing
		}

	}

	/**
	 * Payments log
	 */
	public function payments()
	{
		$terminal = Terminal::whereUserId($this->userId())->first();
		$payments = Payment::orderBy('id', 'desc')->whereTerm($terminal->id)->paginate(50);

		$this->make('payments', compact('payments'));
	}

	/**
	 * Simple debug gate
	 * Current session only
	 */
	public function gate()
	{
		/**
		 * @var Processor $processor
		 */

		$type = Input::get('type');
		$input = Input::get('input');
		$secret = $input['secret'];
		unset($input['secret']);

		Secure::sign($input, $type, $secret);

		$input = $this->getVerifiedInput('gateway', $type, $input);

		$processor = $this->makeProcessor($type, $input);


		// debug response

		$responseData = null;
		$paymentData = null;

		try {
			$responseData = $processor->response()->data();
			$paymentData = $processor->item()->toArray();
		} catch (ProcessorException $e) {
			$responseData = array(
				'exception' => $e->getMessage(),
				'code'      => $e->getCode(),
			);
		}

		$return = array(
			'response' => $responseData,
			'payment'  => $paymentData,
		);

		$return = json_encode($return, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		echo $return;

	}

	/**
	 * Production gate
	 * Public access
	 */
	public function gateway()
	{
		/**
		 * @var Processor $processor
		 */

		$type = Input::get('type');
		$input = $this->getVerifiedInput('gateway', $type, Input::get('input'));
		try {

			if (!$input) {
				throw new ProcessorException(ProcessorException::INVALID_SIGN);
			}

			$term = Terminal::find($input['term']);
			Secure::sign($input, $type, $term->secret);

			$processor = $this->makeProcessor($type, $input);

			$response = $processor->response()->data();
		} catch (ProcessorException $e) {
			$response = array(
				'exception' => $e->getMessage(),
				'code'      => $e->getCode(),
			);
		}

		$return = json_encode($response);

		return $return;

	}

	/**
	 * Production gate
	 * Public access
	 *
	 */
	public function endpoint()
	{
		/**
		 * @var Processor $processor
		 */

		$type = Type::ENDPOINT;
		$input = $this->getVerifiedInput('endpoint', $type, Input::all());


		$errorMessage = 'Request error';
		$urlBack = '';

		if ($input) {

			$errorMessage = '';
			$urlBack = $input['back'];
			$opType = new Type($type, $input);

			try {
				$opType->validate();
			} catch (ProcessorException $e) {
				$errorMessage = $e->getMessage();
			}

		}

		if ($errorMessage) {
			return Redirect::route('ff-bank-em-error')->with(array(
				'errorMessage' => $errorMessage,
				'errorUrl'     => Views::url($urlBack, array('resultBankEmulatorPayment' => 'error')),
				'errorUrlName' => 'вернуться в магазин',
			));
		}

		$term = Terminal::find($input['term']);
		$paymentParams = $this->getPaymentFields($input);
		Secure::sign($paymentParams, $type, $term->secret);

		$this->make('endpoint', compact('paymentParams'));

		return null;

	}


	/**
	 * Production gate
	 * Public access
	 */
	public function endpointAuth()
	{
		/**
		 * @var Processor $processor
		 */

		$type = Type::PAYMENT;
		$input = Input::all();
		$input = Type::clearInput($type, $input);

		$term = Terminal::find($input['term']);
		Secure::sign($input, $type, $term->secret);

		$processor = $this->makeProcessor($type, $input);
		$errorMessage = null;

		try {
			$response = $processor->response();

			if ($response->rc !== '00') {
				$errorMessage = ProcessorException::getCodeMessage($response->rc);
			}

		} catch (ProcessorException $e) {
			$errorMessage = $e->getMessage();
		}

		if (!$errorMessage) {
			return Redirect::to(
				Views::url($input['back'],
					array('resultBankEmulatorPayment' => 'success')
				));
		}

		return Redirect::route('ff-bank-em-error')->with(array(
			'errorMessage' => $errorMessage,
			'errorUrl'     => Views::url($input['back'], array('resultBankEmulatorPayment' => 'error')),
			'errorUrlName' => 'вернуться в магазин',
		));

	}

	/**
	 * E-shop order page
	 */
	public function shop()
	{

		$terminal = Terminal::whereUserId($this->userId())->first();
		$endpointParams = $this->getEndpointFields($terminal);

		$status = Input::get('resultBankEmulatorPayment');
		$statusSuccess = ($status === 'success');
		$statusError = ($status === 'error');

		$this->make('shop', compact(
			'terminal',
			'endpointParams',
			'statusSuccess',
			'statusError'
		));

	}

	/**
	 * Current user id, terminal owner
	 *
	 * @return mixed
	 */
	protected function userId()
	{
		return Config::get('ff-bank-em::user_id');
	}

	/**
	 *
	 * Check, clear and verify input params
	 *
	 * @param $action
	 * @param $type
	 * @param $input
	 *
	 * @return null
	 */
	private function getVerifiedInput($action, $type, $input)
	{

		$input = Type::clearInput($type, $input);
		$baseInput = $input;
		$termId = $input['term'];
		$term = Terminal::find($termId);
		$sign = $input['sign'];

		Secure::sign($input, $type, $term->secret);

		$isCorrect = ($sign === $input['sign']);

		if (!$isCorrect) {

			Log::warning($action . 'pull', array(
				'message' => 'Invalid signature',
				'sign'    => $input['sign'],
				'input'   => Input::all(),
			));

			return null;

		}

		Log::info($action . 'pull', array(
			'message' => 'Correct signature',
			'input'   => Input::all(),
		));

		return $baseInput;

	}

	/**
	 * @param $type
	 * @param $input
	 *
	 * @return Processor
	 */
	private function makeProcessor($type, $input)
	{

		$opType = new Type($type, $input);

		return App::make(
			'FintechFab\BankEmulator\Components\Processor\Processor',
			array($opType)
		);

	}

	/**
	 *
	 * Generate params for payment shop form
	 *
	 * @param $terminal
	 *
	 * @return array
	 */
	private function getEndpointFields($terminal)
	{
		$fields = Type::$fields[Type::ENDPOINT];
		$params = array();

		foreach ($fields as $name) {

			$value = '';
			switch ($name) {
				case 'term':
					$value = $terminal->id;
					break;
				case 'amount':
					$value = '123.45';
					break;
				case 'cur':
					$value = 'RUB';
					break;
				case 'order':
					$value = '123456';
					break;
				case 'name':
					$value = 'Boogie-Woogie Shopping';
					break;
				case 'desc':
					$value = 'Beauty Dress & Smart Phone';
					break;
				case 'url':
					$value = URL::route('ff-bank-em-shop');
					break;
				case 'back':
					$value = URL::route('ff-bank-em-shop');
					break;
				case 'time':
					$value = Time::ts();
					break;
			}

			$params[$name] = $value;

		}

		Secure::sign($params, Type::ENDPOINT, $terminal->secret);

		return $params;

	}

	/**
	 *
	 * Generate params for payment endpoint form
	 *
	 * @param $input
	 *
	 * @return array
	 */
	private function getPaymentFields($input)
	{
		$fields = Type::$fields[Type::PAYMENT];
		$params = array();
		foreach ($fields as $name) {

			$params[$name] = '';
			if (!empty($input[$name])) {
				$params[$name] = $input[$name];
			}

		}

		return $params;

	}


	protected function setupLayout()
	{
		$this->layout = View::make('ff-bank-em::layouts.default');
	}

	protected function make($sTemplate, $aParams = array())
	{
		if (Request::ajax()) {
			return $this->makePartial($sTemplate, $aParams);
		} else {
			return $this->layout->nest('content', 'ff-bank-em::demo.' . $sTemplate, $aParams);
		}
	}

	protected function makePartial($sTemplate, $aParams = array())
	{
		return View::make('ff-bank-em::demo.' . $sTemplate, $aParams);
	}

} 