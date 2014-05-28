<?php


use FintechFab\BankEmulator\Components\Helpers\Views;

if (empty($content)) {
	return;
}

$title = 'Bank Emulator';

?>
<html lang="ru">
<?=
View::make('ff-bank-em::layouts.head', array(
	'title' => $title,
)); ?>
<body>

<div class="navbar navbar-default">

	<?=
	View::make('ff-bank-em::layouts.navbar-header', array(
		'title' => $title,
	)); ?>

	<div class="navbar-collapse collapse navbar-responsive-collapse">
		<ul class="nav navbar-nav">
			<li><a class="top-link" href="<?= URL::route('ff-bank-em-demo') ?>">About</a></li>
			<li><a class="top-link" href="<?= URL::route('ff-bank-em-sdk') ?>">SDK</a></li>
			<li><a class="top-link" href="<?= URL::route('ff-bank-em-docs') ?>">Documentation</a></li>
			<li><a class="top-link" href="<?= URL::route('ff-bank-em-term') ?>">Term</a></li>
			<li><a class="top-link" href="<?= URL::route('ff-bank-em-shop') ?>">E-shop</a></li>
			<li><a class="top-link" href="<?= URL::route('ff-bank-em-payments') ?>">Payments</a></li>
			<?php
			if (Config::get('ff-bank-em::ff.login.enabled')) {
				if (Config::get('ff-bank-em::user_id') > 0) {
					?>
					<li><a class="top-link" href="/logout"><i class="fa fa-sign-out"></i> Logout</a></li><?php
				} else {
					?>
					<li><a class="top-link" href="<?= Views::link2Sign() ?>"><i class="fa fa-sign-in"></i> Sign-in</a>
					</li><?php
				}
			}
			?>
		</ul>
	</div>

</div>

<?= $content ?>
<?php require(__DIR__ . '/js.php') ?>
</body>
</html>