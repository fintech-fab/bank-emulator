<?php
namespace FintechFab\BankEmulator\Components\Helpers;

use App;

class Time
{

	public static $time = null;

	public static function ts()
	{
		if (null === self::$time) {
			self::$time = time();
		}

		if (null === app() || App::environment() == 'testing') {
			return self::$time;
		}

		return time();

	}

	public static function dt($ts = null)
	{
		if ($ts === null) {
			$ts = self::ts();
		}
		if ($ts && !is_numeric($ts)) {
			$ts = strtotime($ts);
		}

		return date('Y-m-d H:i:s', $ts);
	}

} 