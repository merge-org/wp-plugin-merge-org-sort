<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service\Sales;

use DateTime;
use MergeOrg\Sort\Constants;

final class ProductSalesIncrementer implements ProductSalesIncrementerInterface {

	/**
	 * @param array<string, array<int>> $sales
	 * @param int $quantity
	 * @param string $date
	 * @return array<string, array<int>>
	 */
	public function increment(array $sales, int $quantity, string $date = "TODAY"): array {
		$date === "TODAY" &&
		$date = date("Y-m-d");

		if(!$this->dateIsIrrelevant($date)) {
			$sales[$date] =
				$sales[$date] ?? [
				0,
				0,
			];

			$sales[$date][0] += 1;
			$sales[$date][1] += $quantity;
		}

		return $this->normalizeSales($sales);
	}

	/**
	 * @param string $date
	 * @return bool
	 */
	public function dateIsIrrelevant(string $date): bool {
		$today = date("Y-m-d");
		$maxDaysAccepted = max(...array_keys(Constants::SALES_PERIODS_IN_DAYS));
		$maxDaysAgo = date("Y-m-d", strtotime("-$maxDaysAccepted days"));

		return !DateTime::createFromFormat("Y-m-d", $date) || $date < $maxDaysAgo || $date > $today;
	}

	/**
	 * @param array<string, array<int>> $sales
	 * @return array<string, array<int>>
	 */
	public function normalizeSales(array $sales): array {
		$unset = [];
		foreach($sales as $date => $sale) {
			$this->dateIsIrrelevant($date) && ($unset[] = $date);
		}

		foreach($unset as $date) {
			unset($sales[$date]);
		}

		krsort($sales, SORT_STRING);

		return $sales;
	}
}
