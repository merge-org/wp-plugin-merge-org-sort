<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service\Sales;

use DateTime;
use MergeOrg\Sort\Constants;

final class ArrayProductSalesIncrementer {

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
	private function dateIsIrrelevant(string $date): bool {
		$today = date("Y-m-d");
		$maxDaysAccepted = max(...array_keys(Constants::SALES_PERIODS_IN_DAYS));
		$maxDaysAgo = date("Y-m-d", strtotime("-$maxDaysAccepted days"));

		return !DateTime::createFromFormat("Y-m-d", $date) || $date < $maxDaysAgo || $date > $today;
	}

	/**
	 * @param array<string, array<int>> $sales
	 * @return array<string, array<int>>
	 */
	private function normalizeSales(array $sales): array {
		$datesToUnset = [];
		foreach($sales as $date => $sale) {
			$this->dateIsIrrelevant($date) && ($datesToUnset[] = $date);
		}

		foreach($datesToUnset as $date) {
			unset($sales[$date]);
		}

		krsort($sales, SORT_STRING);

		return $sales;
	}
}
