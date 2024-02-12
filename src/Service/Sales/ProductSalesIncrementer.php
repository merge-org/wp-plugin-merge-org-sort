<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service\Sales;

use DateTime;
use MergeOrg\Sort\Constants;

final class ProductSalesIncrementer {

	/**
	 * @param array<string, array<int>> $sales
	 * @param int $quantity
	 * @param string $date
	 * @return array<string, array<int>>
	 */
	public function increment(array $sales, int $quantity, string $date = "TODAY"): array {
		$today = date("Y-m-d");
		if($date === "TODAY") {
			$date = date("Y-m-d");
		}

		$testDate = DateTime::createFromFormat("Y-m-d", $date);
		if(!($testDate && $testDate->format("Y-m-d") === $date)) {
			return $sales;
		}

		if($date > $today) {
			return $sales;
		}

		$maxDaysAccepted = max(...array_keys(Constants::SALES_PERIODS_IN_DAYS));
		$maxDaysAgo = date("Y-m-d", strtotime("-$maxDaysAccepted days"));
		if($date < $maxDaysAgo) {
			return $sales;
		}

		$sales[$date] =
			$sales[$date] ?? [
			0,
			0,
		];

		$sales[$date][0] += 1;
		$sales[$date][1] += $quantity;

		$unset = [];
		foreach($sales as $date => $sale) {
			if(!DateTime::createFromFormat("Y-m-d", $date) || $date < $maxDaysAgo) {
				$unset[] = $date;
			}
		}

		foreach($unset as $date) {
			unset($sales[$date]);
		}

		krsort($sales, SORT_STRING);

		return $sales;
	}
}
