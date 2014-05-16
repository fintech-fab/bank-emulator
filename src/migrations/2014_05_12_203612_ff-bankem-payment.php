<?php

use FintechFab\BankEmulator\Models\Terminal;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class FfBankemPayment extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		Schema::connection('ff-bank-em')->dropIfExists('payments');
		Schema::connection('ff-bank-em')->dropIfExists('terminals');

		Schema::connection('ff-bank-em')->create('payments', function (Blueprint $table) {
			$table->increments('id');
			$table->string('pan', 19)->default('');
			$table->string('year', 2)->default('');
			$table->string('month', 2)->default('');
			$table->string('cvc', 3)->default('');
			$table->string('cur', 3)->default('');
			$table->double('amount', 8, 2)->default(0);
			$table->integer('order')->default(0);
			$table->string('name', 50)->default('');
			$table->string('desc', 100)->default('');
			$table->string('url', 255)->default('');
			$table->string('back', 255)->default('');
			$table->string('email', 50)->default('');
			$table->timestamp('time')->default('0000-00-00 00:00:00');
			$table->string('term', 11)->default('');
			$table->string('rrn', 12)->default('');
			$table->string('irn', 32)->default('');
			$table->string('to', 32)->default('');
			$table->string('type', 10)->default('');
			$table->string('rc', 3)->default('00');
			$table->string('approval', 12)->default('');
			$table->string('status', 10)->default('');
			$table->timestamps();
		});

		Schema::connection('ff-bank-em')->create('terminals', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->default(0);
			$table->string('secret')->default('');
			$table->tinyInteger('mode')->default(Terminal::C_STATE_OFFLINE);
			$table->string('url', 255)->default('');
			$table->string('email', 50)->default('');
			$table->timestamps();
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('ff-bank-em')->drop('payments');
		Schema::connection('ff-bank-em')->drop('terminals');
	}

}
