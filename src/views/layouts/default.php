<?php



if (empty($content)) {
	return;
}


?>
<html>
<head>
	<title>Bank Emulator</title>
	<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
	<link href="//netdna.bootstrapcdn.com/bootswatch/3.1.1/yeti/bootstrap.min.css" rel="stylesheet">
	<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
</head>
<body>

<div class="navbar navbar-default">

	<div class="navbar-header">
		<a class="navbar-brand" href="#" onclick="return false;">Bank Emulator</a>
	</div>

	<div class="navbar-collapse collapse navbar-responsive-collapse">
		<ul class="nav navbar-nav">
			<li><a class="top-link" href="<?= URL::route('ff-bank-em-demo') ?>">Main</a></li>
			<li><a class="top-link" href="<?= URL::route('ff-bank-em-term') ?>">Term</a></li>
			<li><a class="top-link" href="<?= URL::route('ff-bank-em-shop') ?>">E-shop</a></li>
			<li><a class="top-link" href="<?= URL::route('ff-bank-em-payments') ?>">Payments</a></li>
		</ul>
	</div>

</div>

<?= $content ?>
<?php require(__DIR__ . '/js.php') ?>

</body>
</html>