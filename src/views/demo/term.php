<?php

use FintechFab\BankEmulator\Components\Helpers\Time;
use FintechFab\BankEmulator\Components\Helpers\Views;
use FintechFab\BankEmulator\Components\Processor\BankCard;
use FintechFab\BankEmulator\Components\Processor\Type;
use FintechFab\BankEmulator\Models\Terminal;

/**
 * @var array $endpointParams
 * @var Terminal $terminal
 */

?>
<div class="row container">
	<div class="col-md-6">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Ваш терминал</h3>
			</div>
			<div class="panel-body">

				<table class="table table-striped table-hover">
					<tr>
						<td>Id</td>
						<td><?= $terminal->id ?></td>
					</tr>
					<tr>
						<td>Ключ secret</td>
						<td><?= $terminal->secret ?></td>
					</tr>
					<tr>
						<td>Режим</td>
						<td><?= $terminal->modeName() ?></td>
					</tr>
					<tr>
						<td>Callback url</td>
						<td>
							<?php Views::text('url', $terminal->url, array(
								'placeholder' => 'https://your.system.com/callback/',
								'class'       => 'term-options',
							)) ?>
						</td>
					</tr>
					<tr>
						<td>Callback email</td>
						<td>
							<?php Views::text('email', $terminal->email, array(
								'placeholder' => 'bank@example.com',
								'class'       => 'term-options',
							)) ?>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<button class="btn btn-sm term-options">применить</button>
						</td>
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
				<h3 class="panel-title">[<?= Type::AUTH ?>] Авторизация</h3>
			</div>
			<div class="panel-body">

				<div class="col-md-4 post-auth">

					<div class="form-group">
						<?php
						Views::label('pan');
						Views::text('pan', BankCard::getValidCustomPan());
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('year', 'Год/месяц/cvc');
						Views::text('year', 15, array('size' => 2, 'style' => 'width: 35px; display: inline; margin-left: 10px;'));
						Views::text('month', 12, array('size' => 2, 'style' => 'width: 35px; display: inline; margin-left: 10px;'));
						Views::text('cvc', 777, array('size' => 3, 'style' => 'width: 50px; display: inline; margin-left: 10px;'));
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('amount', 'Сумма/валюта');
						Views::text('amount', '123.45', array('size' => 10, 'style' => 'width: 100px; display: inline; margin-left: 10px;'));
						Views::text('cur', 'RUB', array('size' => 3, 'style' => 'width: 50px; display: inline; margin-left: 10px;'));
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('order');
						Views::text('order', '123456');
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('name');
						Views::text('name', 'Fine order');
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('desc');
						Views::text('desc', 'Fine order Description');
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('url');
						Views::text('url', URL::route('ff-bank-em-shop'));
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('email');
						Views::text('email', 'bank@example.com');
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('time');
						Views::text('time', Time::ts());
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('term');
						Views::text('term', $terminal->id, array('size' => 10, 'style' => 'width: 100px; display: inline; margin-left: 10px;'));
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('secret');
						Views::text('secret', $terminal->secret);
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('back');
						Views::text('back', URL::route('ff-bank-em-shop'));
						?>
					</div>

				</div>

				<div class="col-md-8">
					<button class="btn btn-sm post-auth">выполнить запрос</button>
					<div style="padding: 5px 0;">
						<pre class="post-auth" style="font-size: .8em;"></pre>
					</div>
				</div>


			</div>
		</div>
	</div>
</div>
