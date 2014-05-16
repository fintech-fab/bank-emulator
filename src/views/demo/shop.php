<?php

use FintechFab\BankEmulator\Components\Helpers\Views;
use FintechFab\BankEmulator\Components\Processor\Type;
use FintechFab\BankEmulator\Models\Terminal;

/**
 * @var boolean  $statusSuccess
 * @var boolean  $statusError
 * @var array    $endpointParams
 * @var array    $endpointParams
 * @var Terminal $terminal
 */


if ($statusError) {
	?>

	<div class="row container">
		<div class="col-md-8">
			<div class="panel panel-danger">
				<div class="panel-heading">
					<h3 class="panel-title">Ooops...</h3>
				</div>
				<div class="panel-body">
					Что-то не получилось... можете попробовать
					<a href="<?= URL::route('ff-bank-em-shop') ?>" class="alert-link">еще раз</a>.
				</div>
			</div>
		</div>
	</div>

<?php
}

if ($statusSuccess) {
	?>

	<div class="row container">
		<div class="col-md-8">
			<div class="panel panel-success">
				<div class="panel-heading">
					<h3 class="panel-title">Yuppi!!!</h3>
				</div>
				<div class="panel-body">
					Заказ оплачен. Можете <a href="<?= URL::route('ff-bank-em-shop') ?>" class="alert-link">купить что
						нибудь еще</a>.
				</div>
			</div>
		</div>
	</div>

<?php
}


if ($statusSuccess || $statusError) {
	return;
}

?>

<div class="row container">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">[<?= Type::PAYMENT ?>] Онлайн-платеж</h3>
			</div>
			<div class="panel-body">

				<?php
				echo Form::open(array(
					'method' => 'POST',
					'action' => 'ff-bank-em-endpoint',
					'class'  => 'post-endpoint',
				));
				?>

				<div class="col-md-4">

					<?php
					$count = 0;
					foreach ($endpointParams as $key => $value) {
						if ($count++ == ceil(count($endpointParams) / 2)) {
							echo '</div>';
							echo '<div class="col-md-4">';
						}

						?>
						<div class="form-group">
							<?php
							Views::label($key);
							Views::text($key, $value);
							?>
						</div>
					<?php
					}
					?>

				</div>

				<?php echo Form::close(); ?>

				<div class="form-group">
					<button class="btn btn-sm post-endpoint">Перейти к оплате</button>
				</div>

			</div>
		</div>
	</div>
</div>