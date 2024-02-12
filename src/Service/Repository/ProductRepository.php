<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service\Repository;

use MergeOrg\Sort\Constants;
use MergeOrg\Sort\Data\Native\Product;
use MergeOrg\Sort\Data\Native\SalesPeriod;
use MergeOrg\Sort\Wordpress\Api\ApiInterface;
use MergeOrg\Sort\Exception\InvalidSalesPeriodInProductConstructionException;
use MergeOrg\Sort\Exception\InvalidPeriodInDaysInSalesPeriodCreationException;

final class ProductRepository {

	/**
	 * @var ApiInterface
	 */
	private ApiInterface $wordpressApi;

	/**
	 * @param ApiInterface $wordpressApi
	 */
	public function __construct(ApiInterface $wordpressApi) {
		$this->wordpressApi = $wordpressApi;
	}

	/**
	 * @param int $productId
	 * @return Product|null
	 * @throws InvalidSalesPeriodInProductConstructionException
	 * @throws InvalidPeriodInDaysInSalesPeriodCreationException
	 */
	public function get(int $productId): ?Product {
		if(!$product = $this->wordpressApi->getProduct($productId)) {
			return NULL;
		}

		return new Product($productId,
			$this->hydrateSalesToSalesPeriods($product->getSales()),
			$product->getType(),
			$product->getExcludeFromSorting(),
			$product->getPreviousOrder());
	}

	/**
	 * @param array<string, array<int>> $sales
	 * @return SalesPeriod[]
	 * @throws InvalidPeriodInDaysInSalesPeriodCreationException
	 */
	public function hydrateSalesToSalesPeriods(array $sales): array {
		$salesPeriods = [];
		foreach(Constants::SALES_PERIODS_IN_DAYS as $periodInDays => $name) {
			$salesPeriods[] = $this->getSalesPeriodForPeriodInDaysFromSales($sales, $periodInDays);
		}

		return $salesPeriods;
	}

	/**
	 * @param array<string, array<int>> $sales
	 * @param int $periodInDays
	 * @return SalesPeriod
	 * @throws InvalidPeriodInDaysInSalesPeriodCreationException
	 */
	public function getSalesPeriodForPeriodInDaysFromSales(array $sales, int $periodInDays): SalesPeriod {
		$today = date("Y-m-d");
		$furthestDateInPast = date("Y-m-d", strtotime("-$periodInDays days"));

		$salesForPeriod = 0;
		$quantitySalesForPeriod = 0;
		foreach($sales as $date => $dailyTotalSale) {
			$currentDataSales = $dailyTotalSale[0];
			$currentDataQuantitySales = $dailyTotalSale[1];

			if($date === $today && $periodInDays === 1) {
				$salesForPeriod = $currentDataSales;
				$quantitySalesForPeriod = $currentDataQuantitySales;
				break;
			}

			if($date < $furthestDateInPast) {
				continue;
			}

			$salesForPeriod += $currentDataSales;
			$quantitySalesForPeriod += $currentDataQuantitySales;
		}

		return new SalesPeriod($periodInDays, $salesForPeriod, $quantitySalesForPeriod);
	}
}
