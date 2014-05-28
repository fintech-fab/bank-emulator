<?php

if (empty($content)) {
	return;
}

$title = 'Bank Emulator Authorization Center';

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
</div>

<?= $content ?>
<?php require(__DIR__ . '/js.php') ?>
</body>
</html>