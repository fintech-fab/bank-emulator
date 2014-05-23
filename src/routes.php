<?php


Route::group(

	array(
		'prefix'    => 'bank/emulator/demo',
		'namespace' => 'FintechFab\BankEmulator\Controllers'
	),

	function () {

		Route::get('', array(
			'as'   => 'ff-bank-em-demo',
			'uses' => 'DemoController@index',
		));
		Route::get('error', array(
			'as'   => 'ff-bank-em-error',
			'uses' => 'DemoController@error',
		));

		Route::get('term', array(
			'before' => 'ff-bank-em-auth',
			'as'     => 'ff-bank-em-term',
			'uses'   => 'DemoController@term'
		));
		Route::post('term', array(
			'before' => 'ff-bank-em-auth',
			'as'     => 'ff-bank-em-term-post',
			'uses'   => 'DemoController@postTerm'
		));

		Route::post('gate', array(
			'before' => 'ff-bank-em-auth',
			'as'     => 'ff-bank-em-gate',
			'uses'   => 'DemoController@gate'
		));

		Route::post('gateway', array(
			'as'   => 'ff-bank-em-gateway',
			'uses' => 'DemoController@gateway'
		));

		Route::any('endpoint', array(
			'as'   => 'ff-bank-em-endpoint',
			'uses' => 'DemoController@endpoint'
		));
		Route::post('endpoint/auth', array(
			'as'   => 'ff-bank-em-endpoint-auth',
			'uses' => 'DemoController@endpointAuth'
		));
		Route::get('endpoint/auth/result', array(
			'as'   => 'ff-bank-em-endpoint-auth-result',
			'uses' => 'DemoController@endpointAuthResult'
		));
		Route::any('pay/auth/{payment}/{back}', array(
			'as'   => 'ff-bank-em-pay-auth',
			'uses' => 'DemoController@auth'
		));
		Route::get('shop', array(
			'before' => 'ff-bank-em-auth|ff-bank-em-term',
			'as'     => 'ff-bank-em-shop',
			'uses'   => 'DemoController@shop'
		));
		Route::post('sign', array(
			'before' => 'ff-bank-em-auth|ff-bank-em-term',
			'as'     => 'ff-bank-em-sign',
			'uses'   => 'DemoController@sign'
		));

		Route::post('callback', array(
			'uses' => 'DemoController@callback'
		));

		Route::get('payments', array(
			'before' => 'ff-bank-em-auth|ff-bank-em-term',
			'as'     => 'ff-bank-em-payments',
			'uses'   => 'DemoController@payments',
		));

	}

);
