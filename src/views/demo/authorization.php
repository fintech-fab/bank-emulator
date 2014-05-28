<div class="row container">

	<div class="col-xs-6">

		<?= Form::open(array('method' => 'POST')) ?>

		<div class="form-group">
			Enter your one-time code:
			<?php
			echo Form::label('hint', '(type `12345` for accept transaction)');
			echo Form::text('hint', '', array('size' => 6, 'class' => 'form-control', 'style' => 'width: 100px;'));
			?>
		</div>

		<div class="form-group">
			<?= Form::submit('submit', array('class' => 'submit-hide')) ?>
		</div>

		<?= Form::close() ?>

	</div>

</div>