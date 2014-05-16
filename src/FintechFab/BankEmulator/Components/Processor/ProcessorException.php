<?php

namespace FintechFab\BankEmulator\Components\Processor;

use Exception;

class ProcessorException extends Exception
{

	const UNDEFINED = '-99';

	const INVALID_TYPE = '900';
	const INVALID_PARAM = '901';
	const INVALID_PAN = '902';
	const INVALID_EXPIRED = '903';
	const INVALID_TIME = '904';
	const INVALID_TERMINAL = '905';
	const INVALID_SIGN = '906';
	const TERMINAL_DISABLED = '907';
	const TERMINAL_DISABLED_GATE = '908';

	const PAYMENT_NOT_FOUND = '104';

	const RC_CVC = '-1';
	const RC_PAYMENT_NOT_FOUND = '-2';
	const RC_PAYMENT_DOUBLE = '-3';
	const RC_UNDEFINED = '-4';
	const RC_NO_SUCH_CARD = '14';
	const RC_NOT_PERM_CARDHOLDER = '57';
	const RC_EXPIRED_CARD = '54';
	const RC_SYSTEM_FAIL = '96';
	const RC_MOT_PERM_TERM = '58';
	const RC_TRANSACTION_FAIL = '83';
	const RC_ISSUER_FAIL = '31';

	public static $errors = array(

		self::INVALID_TYPE           => 'Invalid type',
		self::INVALID_PARAM          => 'Invalid parameter value',
		self::INVALID_PAN            => 'Invalid card number',
		self::INVALID_EXPIRED        => 'Expired card',
		self::INVALID_TIME           => 'Mismatch time',
		self::INVALID_TERMINAL       => 'Invalid terminal number',
		self::INVALID_SIGN           => 'Invalid signature',
		self::TERMINAL_DISABLED      => 'Terminal disabled',
		self::TERMINAL_DISABLED_GATE => 'Terminal disabled as gateway',

		self::PAYMENT_NOT_FOUND      => 'Payment not found',

		self::UNDEFINED              => 'Undefined failure',
		self::RC_CVC                 => 'Error cvc code',
		self::RC_PAYMENT_NOT_FOUND   => 'Payment not found',
		self::RC_PAYMENT_DOUBLE      => 'Payment duplicated',
		self::RC_NO_SUCH_CARD        => 'No such card',
		self::RC_NOT_PERM_CARDHOLDER => 'Not permitted to cardholder',
		self::RC_EXPIRED_CARD        => 'Expired card',
		self::RC_SYSTEM_FAIL         => 'System failure',
		self::RC_MOT_PERM_TERM       => 'Not permitted to terminal',
		self::RC_TRANSACTION_FAIL    => 'Transaction failure',
		self::RC_ISSUER_FAIL         => 'Issuer failure',

	);

	public function __construct($code, $message = null)
	{
		$errorMessage = self::getCodeMessage($code);
		if ($message) {
			$errorMessage .= ' (' . $message . ')';
		}
		parent::__construct($errorMessage, $code);
	}

	public static function getCodeMessage($code)
	{
		return isset(self::$errors[$code])
			? self::$errors[$code]
			: null;
	}

}