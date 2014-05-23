<?php

use FintechFab\MoneyTransferEmulator\Components\Helpers\Views;


?>
<div class="row container">

	<div class="col-xs-6">

		<?= Form::open(array('method' => 'POST')) ?>

		<div class="form-group">
			<?php
			Views::label('hint', 'Enter your one-time code (type `12345` for success)');
			Views::text('hint', '', array('size' => 6));
			?>
		</div>

		<div class="form-group">
			<?= Form::submit('OK', array('class' => 'submit-hide')) ?>
		</div>

		<?= Form::close() ?>

	</div>

</div>