<?php

/**
 * @var string $errorMessage
 */

if (empty($errorMessage)) {
	$errorMessage = Session::get('errorMessage');
	if (empty($errorMessage)) {
		$errorMessage = 'Undefined Error';
	}
}

if (empty($errorUrl)) {
	$errorUrl = Session::get('errorUrl');
}

if (empty($errorUrlName)) {
	$errorUrlName = Session::get('errorUrlName');
	if (!$errorUrlName) {
		$errorUrlName = 'back';
	}
}

?>


<div class="row container">
	<div class="col-md-8">

		<div class="panel panel-danger">
			<div class="panel-heading">
				<h3 class="panel-title">Im sorry, but:</h3>
			</div>
			<div class="panel-body">
				<?= $errorMessage ?>
				<?php
				if ($errorUrl) {
					echo '(' . link_to($errorUrl, $errorUrlName, array('target' => '_parent')) . ')';
				}
				?>
			</div>
		</div>

	</div>
</div>