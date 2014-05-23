<?php

namespace FintechFab\BankEmulator\Models;


use Eloquent;

/**
 * FintechFab\BankEmulator\Models\Payment
 *
 * @property integer        $id
 * @property string         $pan
 * @property string         $year
 * @property string         $month
 * @property string         $cvc
 * @property string         $cur
 * @property float          $amount
 * @property integer        $order
 * @property string         $name
 * @property string         $desc
 * @property string         $url
 * @property string         $email
 * @property string         $time
 * @property string         $term
 * @property string         $rrn
 * @property string         $irn
 * @property string         $to
 * @property string         $type
 * @property string         $rc
 * @property string         $approval
 * @property string         $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @method static Payment  where($column, $operator, $value)
 * @method static Payment  whereIrn($irn)
 * @method static Payment  whereRrn($rrn)
 * @method static Payment  whereAmount($amount)
 * @method static Payment  whereOrder($order)
 * @method static Payment  whereTerm($term)
 * @method static Payment  whereType($type)
 * @method static Payment  whereStatus($status)
 * @method static Payment  orderBy($column, $direction)
 * @method static Payment  first()
 * @method static Payment  find($id)
 */
class Payment extends Eloquent
{

	public $connection = 'ff-bank-em';
	public $table = 'payments';
	public $fillable = array(
		'pan',
		'year',
		'month',
		'cvc',
		'cur',
		'amount',
		'order',
		'name',
		'desc',
		'url',
		'email',
		'time',
		'term',
		'rrn',
		'irn',
		'to',
		'type',
	);

	public function success()
	{
		return $this->rc == '00';
	}

	public function mask($field)
	{
		if (!empty($this->$field)) {
			$this->$field = preg_replace('/^(\d{4})\d+(\d{4})$/', '$1***$2', $this->$field);
			$this->save();
		}
	}

	public function callbackEmail()
	{

		if (!$this->email) {
			return null;
		}
		if (strpos($this->email, '@example') !== false) {
			return null;
		}

		return $this->email;
	}

} 