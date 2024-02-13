<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service\Repository;

use MergeOrg\Sort\Constants;
use MergeOrg\Sort\Data\Native\Product;
use MergeOrg\Sort\Data\Native\SalesPeriod;
use MergeOrg\Sort\Wordpress\Api\ApiInterface;
use MergeOrg\Sort\Service\Sales\ProductSalesIncrementerInterface;
use MergeOrg\Sort\Exception\InvalidSalesPeriodInProductConstructionException;
use MergeOrg\Sort\Exception\InvalidPeriodInDaysInSalesPeriodCreationException;

final class ProductRepository {

	/**
	 * @var Product[]
	 */
	private array $cache = [];

	/**
	 * @var ApiInterface
	 */
	private ApiInterface $wordpressApi;

	/**
	 * @var ProductSalesIncrementerInterface
	 */
	private ProductSalesIncrementerInterface $productSalesIncrementer;

	/**
	 * @param ApiInterface $wordpressApi
	 * @param ProductSalesIncrementerInterface $productSalesIncrementer
	 */
	public function __construct(ApiInterface $wordpressApi, ProductSalesIncrementerInterface $productSalesIncrementer) {
		$this->wordpressApi = $wordpressApi;
		$this->productSalesIncrementer = $productSalesIncrementer;
	}

	/**
	 * @param int $productId
	 * @param bool $ignoreCache
	 * @return Product|null
	 * @throws InvalidPeriodInDaysInSalesPeriodCreationException
	 * @throws InvalidSalesPeriodInProductConstructionException
	 */
	public function get(int $productId, bool $ignoreCache = FALSE): ?Product {
		if(!$ignoreCache && ($product = ($this->cache[$productId] ?? FALSE))) {
			return $product;
		}

		if(!$product = $this->wordpressApi->getProduct($productId)) {
			return NULL;
		}

		return $this->cache[$productId] = new Product($productId,
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

	/**
	 * @param int $productId
	 * @param int $quantity
	 * @param string $date
	 * @return void
	 */
	public function incrementProductSales(int $productId, int $quantity, string $date = "TODAY"): void {
		$product = $this->wordpressApi->getProduct($productId);
		$incrementedSales = $this->productSalesIncrementer->increment($product->getSales(), $quantity, $date);
		$this->wordpressApi->setProductSales($productId, $incrementedSales);
	}

}
