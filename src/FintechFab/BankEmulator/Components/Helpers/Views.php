<?php

namespace FintechFab\BankEmulator\Components\Helpers;


use FintechFab\BankEmulator\Components\Processor\Input;
use Form;

class Views
{


	public static function label($name, $value = null, $options = array())
	{
		if (!empty($options['class'])) {
			$options['class'] .= ' control-label';
		} else {
			$options['class'] = 'control-label';
		}
		if (null === $value) {
			$value = Input::name($name);
		}
		echo Form::label($name, $value, $options);
	}

	public static function disabled($name, $value, $options = array())
	{
		if (!empty($options['class'])) {
			$options['class'] .= ' form-control';
		} else {
			$options['class'] = 'form-control';
		}
		$options['disabled'] = 'disabled';
		echo Form::text($name, $value, $options);
	}

	public static function text($name, $value, $options = array())
	{
		if (!empty($options['class'])) {
			$options['class'] .= ' form-control';
		} else {
			$options['class'] = 'form-control';
		}
		echo Form::text($name, $value, $options);
	}

	public static function hidden($key, $value, $options = array())
	{
		if (!empty($options['class'])) {
			$options['class'] .= ' form-control';
		} else {
			$options['class'] = 'form-control';
		}
		echo Form::hidden($key, $value, $options);
	}

	public static function url($url, $params = array())
	{

		$params = http_build_query($params);
		$url = strpos($url, '?') === false
			? $url . '?' . $params
			: $url . '&' . $params;

		return $url;

	}


}