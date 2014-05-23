<?php

use FintechFab\BankEmulator\Components\Helpers\Views;

/**
 * @var array $paymentParams
 */

?>
<div class="row container">

	<div class="col-xs-6">

		<div class="well well-lg">

			<?php if ($paymentParams['name']) { ?>
				<p><strong><?= e($paymentParams['name']) ?></strong></p>
			<?php } ?>

			<?php if ($paymentParams['desc']) { ?>
				<p>
					<small><?= e($paymentParams['desc']) ?></small>
				</p>
			<?php } ?>

		</div>

		<div class="well well-lg">
			<p>Сумма: <?= e($paymentParams['amount']) ?> <?= e($paymentParams['cur']) ?></p>

			<p>Номер платежа: <?= e($paymentParams['order']) ?></p>

		</div>

		<div class="well well-lg">
			<p><i class="fa fa-arrow-left"></i> <?= link_to($paymentParams['url'], 'Вернуться в магазин', array('class' => 'link-to-shop', 'target' => '_parent')) ?></p>
		</div>

	</div>


	<div class="col-xs-6">

		<?php


		echo Form::open(array(
			'method' => 'POST',
			'action' => 'ff-bank-em-endpoint-auth',
		));


		?>

		<div class="form-group">
			<?php
			Views::label('pan');
			Views::text('pan', '');
			?>
		</div>

		<div class="form-group">
			<?php
			Views::label('year', 'Год/месяц/cvc');
			Views::text('year', '', array('size' => 2, 'style' => 'width: 35px; display: inline; margin-left: 10px;'));
			Views::text('month', '', array('size' => 2, 'style' => 'width: 35px; display: inline; margin-left: 10px;'));
			Views::text('cvc', '', array('size' => 3, 'style' => 'width: 50px; display: inline; margin-left: 10px;'));
			?>
		</div>

		<?php

		foreach ($paymentParams as $key => $value) {

			switch ($key) {

				case 'pan';
				case 'cvc';
				case 'year';
				case 'month';
					break;

				default:

					Views::hidden($key, $value);

					break;

			}

		}

		?>

		<div class="form-group">
			<?= Form::submit('Оплатить', array('class' => 'submit-hide')) ?>
		</div>

		<?php

		echo Form::close();

		?>

	</div>


</div>