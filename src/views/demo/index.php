<?php

use FintechFab\BankEmulator\Components\Processor\BankCard;
use FintechFab\BankEmulator\Components\Processor\Input;
use FintechFab\BankEmulator\Components\Processor\Response;
use FintechFab\BankEmulator\Components\Processor\Type;

?>

<div class="row container">
	<div class="col-md-7">

		<h2>About</h2>

		<p>Это имитация банковского шлюза (или ipsp), где есть стандартные процедуры выполнения платежей по картам:</p>

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
			<li>3DS - аналог<br> &mdash; <i>
					<small>Авторизации платежа владельцем карты</small>
				</i></li>
		</ul>

	</div>

	<div class="col-md-5">

		<h2>Profit</h2>

		<p>Пользуйтесь шлюзом, чтобы отладить/протестировать платежные процессы в вашем проекте.</p>

		<p>Чтобы начать, <a href="/registration">авторизуйтесь здесь</a>, потом
			<a href="<?= URL::route('ff-bank-em-term') ?>">здесь</a> вам будет сгенерирован банковский терминал с
			id-шником и ключом.</p>

		<p>Там же находятся формы для отладки шлюза, а <a href="<?= URL::route('ff-bank-em-shop') ?>">здесь</a> - форма
			для онлайн-платежа.</p>

		<h2>Tags</h2>

		<p>
			<a href="http://wiki.fintech-fab.ru/doku.php/lib:emulators:bank">Документация</a>,
			<a href="https://github.com/fintech-fab/money-transfer-emulator-sdk">PHP SDK</a>,
			<a href="https://github.com/fintech-fab/bank-emulator">GitHub</a>, <a href="http://laravel.com">Laravel</a>

		</p>

	</div>

</div>


<div class="row container">
	<div class="col-md-12">

		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Типы запросов</h3>
			</div>
			<div class="panel-body">
				<table class="table table-striped table-hover">

					<tr>
						<th>Тип запроса</th>
						<th>Комментарий</th>
						<th>Параметры</th>
					</tr>

					<?php foreach (Type::$typeNames as $key => $val) { ?>
						<tr>
							<td><?= $key ?></td>
							<td><?= $val ?></td>
							<td><?= implode(', ', Type::$fields[$key]) ?></td>
						</tr>
					<?php } ?>

				</table>
			</div>
		</div>

	</div>
</div>


<div class="row container">
	<div class="col-md-12">

		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Доступные процессы</h3>
			</div>
			<div class="panel-body">
				<table class="table table-striped table-hover">

					<tr>
						<th>Цепочка</th>
						<th>Комментарий</th>
					</tr>

					<tr>
						<td>auth <i class="fa fa-long-arrow-right"></i> complete</td>
						<td>Авторизация платежа <i class="fa fa-long-arrow-right"></i> Завершить платеж</td>
					</tr>

					<tr>
						<td>auth <i class="fa fa-long-arrow-right"></i> refund</td>
						<td>Авторизация платежа <i class="fa fa-long-arrow-right"></i> Отменить авторизацию</td>
					</tr>

					<tr>
						<td>auth <i class="fa fa-long-arrow-right"></i> complete <i class="fa fa-long-arrow-right"></i>
							refund
						</td>
						<td>Авторизация платежа <i class="fa fa-long-arrow-right"></i> Завершить платеж
							<i class="fa fa-long-arrow-right"></i> Отменить платеж
						</td>
					</tr>

					<tr>
						<td>sale <i class="fa fa-long-arrow-right"></i> refund</td>
						<td>Платеж <i class="fa fa-long-arrow-right"></i> Отменить платеж</td>
					</tr>

					<tr>
						<td>payment <i class="fa fa-long-arrow-right"></i> refund</td>
						<td>Онлайн-платеж <i class="fa fa-long-arrow-right"></i> Отменить платеж</td>
					</tr>

				</table>
			</div>
		</div>

	</div>
</div>


<div class="row container">
	<div class="col-md-12">

		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Параметры ответа</h3>
			</div>
			<div class="panel-body">
				<?= implode(', ', Response::$responseFields) ?>
			</div>
		</div>

	</div>
</div>

<div class="row container">
	<div class="col-md-12">

		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Описание параметров</h3>
			</div>
			<div class="panel-body">
				<table class="table table-striped table-hover">

					<tr>
						<th>Параметр</th>
						<th>Правило</th>
						<th>Описание</th>
					</tr>

					<?php foreach (Input::$rules as $key => $val) { ?>
						<tr>
							<td><?= $key ?></td>
							<td><?= $val ?></td>
							<td><?= Input::$paramNames[$key] ?></td>
						</tr>
					<?php } ?>

				</table>
			</div>
		</div>

	</div>
</div>


<div class="row container">
	<div class="col-md-12">

		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Карты для тестирования</h3>
			</div>
			<div class="panel-body">
				<table class="table table-striped table-hover">

					<tr>
						<th>Карта</th>
						<th>Система</th>
						<th>Код ответа</th>
					</tr>

					<?php foreach (BankCard::$list as $pan => $data) { ?>
						<tr>
							<td><?= $pan ?></td>
							<td><?= $data['group'] ?></td>
							<td><?= $data['rc'] ?></td>
						</tr>
					<?php } ?>

				</table>
			</div>
		</div>

	</div>
</div>


<div class="row container">
	<div class="col-md-12">

		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Коды CVC</h3>
			</div>
			<div class="panel-body">
				<table class="table table-striped table-hover">

					<tr>
						<th>Код</th>
						<th>Поведение</th>
					</tr>

					<tr>
						<td>333</td>
						<td>Требуется авторизация</td>
					</tr>
					<tr>
						<td>777</td>
						<td>Успешный платеж</td>
					</tr>
					<tr>
						<td>***</td>
						<td>Ошибка кода</td>
					</tr>

				</table>
			</div>
		</div>

	</div>
</div>


<div class="row container">
	<div class="col-md-12">

		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Формирование/проверка подписи</h3>
			</div>
			<div class="panel-body">
				<pre>
					<?php highlight_string(
						"<?php\n" .
						"/**\n" .
						" * @var string \$type   тип запроса\n" .
						" * @var array  \$params параметры запроса\n" .
						" * @var string \$secret ключ продавца\n" .
						" */\n" .
						"ksort(\$params);\n" .
						"\$str4sign = implode('|', \$params);\n" .
						"\$sign = md5(\$str4sign . \$type . \$secret);\n" .
						"?>"
					) ?>
				</pre>
			</div>
		</div>

	</div>
</div>
