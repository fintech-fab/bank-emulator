<?php

use FintechFab\BankEmulator\Components\Helpers\Views;

?>
<div class="row container">
	<div class="col-md-7">

		<h2>About</h2>

		<p>Это имитация банковского шлюза (аналог ipsp-системы), где есть стандартные процедуры выполнения платежей по
			картам:</p>

		<ul>
			<li>Авторизационный платеж<br> &mdash; <i>
					<small>Проверка возможности списания средств с карты Клиента</small>
				</i></li>
			<li>Завершение продажи<br> &mdash; <i>
					<small>Исполнение Авторизационного платежа</small>
				</i></li>
			<li>Прямая продажа<br> &mdash; <i>
					<small>Списание средств с карты в пользу Продавца, либо перевод на другую карту</small>
				</i></li>
			<li>Отмена платежа<br> &mdash; <i>
					<small>Для возврата средств или отмены Авторизационного платежа</small>
				</i>
			</li>
			<li>Онлайн-платеж<br> &mdash; <i>
					<small>Перенаправление Клиента в Банк для оплаты товаров или услуг</small>
				</i></li>
			<li>Авторизация платежа владельцем<br> &mdash; <i>
					<small>Аналог процедуры 3D-Secure</small>
				</i></li>
		</ul>

	</div>

	<div class="col-md-5">

		<h2>Profit</h2>

		<p>Пользуйтесь шлюзом, чтобы отладить/протестировать платежные процессы в вашем проекте на "тестовой" платежной
			системе.</p>

		<p>Чтобы начать, <a href="<?= Views::link2Sign() ?>">авторизуйтесь здесь</a>, потом
			<a href="<?= URL::route('ff-bank-em-term') ?>">здесь</a> вам будет сгенерирован банковский терминал с
			id-шником и ключом.</p>

		<p>Там же находятся формы для отладки шлюза, а <a href="<?= URL::route('ff-bank-em-shop') ?>">здесь</a> - форма
			для онлайн-платежа.</p>

		<p>Для подключения шлюза к вашему проекту используйте <a href="<?= URL::route('ff-bank-em-sdk') ?>">PHP SDK</a>.
		</p>

		<h2>Tags</h2>

		<p>
			<a href="<?= URL::route('ff-bank-em-docs') ?>">Справочник</a>,
			<a href="<?= URL::route('ff-bank-em-sdk') ?>">PHP SDK</a>,
			<a href="https://github.com/fintech-fab/bank-emulator">GitHub</a>, <a href="http://laravel.com">Laravel</a>

		</p>

	</div>

</div>


<div class="row container">
	<div class="col-md-12">
		<h3>Simple usage example</h3>

		<p>&mdash; Если вы авторизованы на сайте, шлюз и кнопка платежа уже есть в вашем
			<a href="<?= URL::route('ff-bank-em-shop') ?>">тестовом магазине</a>.</p>

		<p>&mdash; Нажатие на кнопку отправит вас по пути "онлайн платежа" на страницу платежной формы вашего шлюза
			(endpointUrl).</p>

		<p>&mdash; Доступные для тестирования банковские карты <a href="<?= URL::route('ff-bank-em-docs') ?>">здесь</a>
			(вот например: <?= \FintechFab\BankEmulator\Components\Processor\BankCard::getValidCustomPan() ?>).</p>

		<p>&mdash; Год/месяц - из будущего, CVC-коды: 777 (успешный платеж), 333 (будет похоже на 3ds), и любой другой
			(будет ошибка).</p>

		<p>&mdash; После завершения платежа, вы вернетесь на страницу магазина (shopUrl) с GET-параметром
			resultBankEmulatorPayment.</p>

		<p>&mdash; Если был указан callbackUrl, от шлюза придет POST-запрос с результатом платежной операции.</p>

		<p>&mdash; Если был указан callbackEmail, от шлюза придет Email с результатом платежной операции.</p>
	</div>
</div>
