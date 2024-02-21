<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service;

use MergeOrg\Sort\Data\SalesPeriod;

final class SalesPeriodManager {

	/**
	 * @var Namer
	 */
	private Namer $namer;

	/**
	 * @param Namer $namer
	 */
	public function __construct(Namer $namer) {
		$this->namer = $namer;
	}

	/**
	 * @param array<string, array<int,int>> $sales
	 * @return SalesPeriod[]
	 */
	public function getAllSalesPeriods(array $sales): array {
		$salesPeriods = [];
		foreach($this->namer->getSalesPeriodsInDays() as $periodsInDay) {
			$salesPeriods[] = $this->getSalesPeriodFromPeriodInDays($sales, $periodsInDay);
		}

		return $salesPeriods;
	}

	/**
	 * @param array<string, array<int,int>> $sales
	 * @param int $periodInDays
	 * @return SalesPeriod|null
	 */
	public function getSalesPeriodFromPeriodInDays(array $sales, int $periodInDays): ?SalesPeriod {
		$today = date("Y-m-d");
		$furthestDateInPast = date("Y-m-d", strtotime("-$periodInDays days"));

		$salesForPeriod = 0;
		$quantityBasedSalesForPeriod = 0;
		foreach($sales as $date => $dailySales) {
			if($date === $today && $periodInDays === 1) {
				$salesForPeriod = $dailySales[0];
				$quantityBasedSalesForPeriod = $dailySales[1];
				break;
			}

			if($date < $furthestDateInPast || $date > $today) {
				continue;
			}

			$salesForPeriod += $dailySales[0];
			$quantityBasedSalesForPeriod += $dailySales[1];
		}

		return new SalesPeriod($periodInDays, $salesForPeriod, $quantityBasedSalesForPeriod);
	}
}
