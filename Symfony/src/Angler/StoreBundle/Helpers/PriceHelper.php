<?php
namespace Angler\StoreBundle\Helpers;

use Symfony\Component\Templating\Helper\Helper;

class PriceHelper extends Helper {

	public function getName() {
		return 'price';
	}

	public static function calcDeviation($deviation, $base) {
		$deviationParts = self::parseDeviation($deviation);

		if ($deviationParts['sign'] == '=') {
			$price = $deviationParts['number'];
		} else if ($deviationParts['percent']) {
			$price = $base + $base * $deviationParts['number'] * ($deviationParts['sign'] . '1') / 100;
		} else {
			$price = $base + $deviationParts['number'] * ($deviationParts['sign'] . '1');
		}
		return priceRound(max(0, $price));
	}

	public static function parseDeviation($price) {
		$price = trim($price);
		$firstSign = substr($price, 0, 1);
		$sign = in_array($firstSign, array('-', '+', '=')) ? $firstSign : '=';
		$lastSign = substr($price, -1);
		$isPercent = ($lastSign == '%' && $sign != '=') ? true : false;
		$number = (float)preg_replace('/[^0-9\.]/', '', $price);

		return array('number' => $number, 'percent' => $isPercent, 'sign' => $sign);
	}
}
