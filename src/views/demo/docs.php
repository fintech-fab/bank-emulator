<?php

use FintechFab\BankEmulator\Components\Processor\BankCard;
use FintechFab\BankEmulator\Components\Processor\Input;
use FintechFab\BankEmulator\Components\Processor\Response;
use FintechFab\BankEmulator\Components\Processor\Type;

?>

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
