<?php


use FintechFab\BankEmulator\Models\Terminal;

Route::filter('ff-bank-em-term', function () {

	$terminal = Terminal::whereUserId(Config::get('ff-bank-em::user_id'))->first();
	if (!$terminal) {
		return Redirect::route('ff-bank-em-term');
	}

	return null;

});

Route::filter('ff-bank-em-auth', function () {

	$user_id = Config::get('ff-bank-em::user_id');
	$user_id = (int)$user_id;
	if ($user_id <= 0) {
		return Redirect::to(URL::route('ff-bank-em-error', array(), false))
			->with('errorMessage', 'Чтобы пользоваться шлюзом, необходима авторизация на сайте. Или, если вы установили шлюз к себе на сервер, настройте значение user_id в локальной конфигурации вашего проекта.');
	}

	return null;

});
