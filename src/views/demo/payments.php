<?php

/**
 * @var Payment|Illuminate\Pagination\Paginator $payments
 */

use FintechFab\BankEmulator\Components\Processor\ProcessorException;
use FintechFab\BankEmulator\Models\Payment;

?>
	<table class="table table-striped table-hover">
		<tr>
			<th>id</th>
			<th>created</th>
			<th>type</th>
			<th>order</th>
			<th>amount</th>
			<th>cur</th>
			<th>status</th>
			<th>rrn</th>
			<th>irn</th>
			<th>rc</th>
			<th>pan</th>
			<th>message</th>
		</tr>
		<?php foreach ($payments as $payment) { ?>
			<tr>
				<td><?= $payment->id ?></td>
				<td><?= $payment->created_at ?></td>
				<td><?= $payment->type ?></td>
				<td><?= $payment->order ?></td>
				<td><?= $payment->amount ?></td>
				<td><?= $payment->cur ?></td>
				<td><?= $payment->status ?></td>
				<td><?= $payment->rrn ?></td>
				<td><?= $payment->irn ?></td>
				<td><?= $payment->rc ?></td>
				<td><?= $payment->pan ?></td>
				<td><?= ProcessorException::getCodeMessage($payment->rc) ?></td>
			</tr>
		<?php } ?>
	</table>
<?= $payments->links();