<?php
/**
 * Created by PhpStorm.
 * User: m.novikov
 * Date: 13.05.14
 * Time: 12:10
 */

namespace FintechFab\BankEmulator\Components\Processor;


class BankCard
{

	const C_GROUP_VISA = 'VISA';
	const C_GROUP_MASTERCARD = 'Mastercard';
	const C_GROUP_DINERS_CLUB = 'Diners Club';

	const C_CVV_SUCCESS = '777';
	const C_CVV_3DS = '333';

	public static $list = array(


		// ==== success cards ===== //

		'5187523492954370' => array(
			'pan'   => '5187523492954370',
			'group' => self::C_GROUP_MASTERCARD,
			'rc'    => '00',
		),
		'4532274626451231' => array(
			'pan'   => '4532274626451231',
			'group' => self::C_GROUP_VISA,
			'rc'    => '00',
		),
		'36054413210142'   => array(
			'pan'   => '36054413210142',
			'group' => self::C_GROUP_DINERS_CLUB,
			'rc'    => '00',
		),


		// ==== error cards ===== //

		// Transaction not permitted to cardholder
		'5414402089298761' => array(
			'pan'   => '5414402089298761',
			'group' => self::C_GROUP_MASTERCARD,
			'rc'    => '57',
		),

		// Expired card / target
		'5484246987741728' => array(
			'pan'   => '5484246987741728',
			'group' => self::C_GROUP_MASTERCARD,
			'rc'    => '54',
		),

		// System Malfunction
		'4716404752339797' => array(
			'pan'   => '4716404752339797',
			'group' => self::C_GROUP_VISA,
			'rc'    => '96',
		),

		// Transaction not permitted to terminal
		'4716788001836623' => array(
			'pan'   => '4716788001836623',
			'group' => self::C_GROUP_VISA,
			'rc'    => '58',
		),

		// Transaction failed
		'38347043143750'   => array(
			'pan'   => '38347043143750',
			'group' => self::C_GROUP_DINERS_CLUB,
			'rc'    => '83',
		),

		// Issuer signed-off
		'30391662717849'   => array(
			'pan'   => '30391662717849',
			'group' => self::C_GROUP_DINERS_CLUB,
			'rc'    => '31',
		),

	);


	public static function doCheckCard($pan, $cvv = null)
	{

		// No such card
		if (!isset(self::$list[$pan])) {
			return '14';
		}

		// cvc error
		if (
			$cvv !== null &&
			$cvv != self::C_CVV_SUCCESS &&
			$cvv != self::C_CVV_3DS
		) {
			return '-1';
		}

		// card error
		$card = self::$list[$pan];
		if ($card['rc'] !== '00') {
			return $card['rc'];
		}

		return '00';

	}

	public static function getValidCustomPan()
	{
		$keys = array_keys(self::$list);

		return self::$list[$keys[mt_rand(0, 2)]]['pan'];
	}

} 