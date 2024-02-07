<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service;

use DateTime;
use MergeOrg\Sort\Constants;
use MergeOrg\Sort\Data\Product;

final class ApiService {

	/**
	 * @var WpDataApiServiceInterface
	 */
	private WpDataApiServiceInterface $wpDataApiService;

	/**
	 * @param WpDataApiServiceInterface $wpDataApiService
	 */
	public function __construct(WpDataApiServiceInterface $wpDataApiService) {
		$this->wpDataApiService = $wpDataApiService;
	}

	/**
	 * @param int $productId
	 * @return Product
	 */
	public function getProduct(int $productId): Product {
		$sales = $this->getSales($productId);

		return $this->decorateProduct($productId,
			$sales,
			$this->getPreviousOrder($productId),
			$this->getExcludeFromSorting($productId));
	}

	/**
	 * @param int $productId
	 * @return array<string, int>
	 */
	public function getSales(int $productId): array {
		return $this->wpDataApiService->getPostMeta($productId, Constants::SALES_FIELD) ?: [];
	}

	/**
	 * @param int $productId
	 * @param array<string, int> $sales
	 * @param int $previousOrder
	 * @param bool $excludeFromOrder
	 * @return Product
	 */
	public function decorateProduct(int $productId, array $sales, int $previousOrder, bool $excludeFromOrder): Product {
		$periodSales = [];
		foreach(Constants::SALES_PERIODS as $period) {
			$periodSales[$period] = $this->getSinglePeriodProductSales($sales, $period);
		}

		return new Product($productId,
			$periodSales[1],
			$periodSales[7],
			$periodSales[15],
			$periodSales[30],
			$periodSales[90],
			$periodSales[180],
			$periodSales[365],
			$previousOrder,
			$excludeFromOrder);
	}

	/**
	 * @param array<string, int> $sales
	 * @param int $period
	 * @return int
	 */
	public function getSinglePeriodProductSales(array $sales, int $period = 7): int {
		$today = date("Y-m-d");
		$furthestDateInPast = date("Y-m-d", strtotime("-$period days"));

		$singlePeriodSales = 0;
		foreach($sales as $date => $dailyTotalSale) {
			if($date === $today && $period === 1) {
				$singlePeriodSales = $dailyTotalSale;
				break;
			}

			if($date < $furthestDateInPast) {
				continue;
			}

			$singlePeriodSales += $dailyTotalSale;
		}

		return $singlePeriodSales;
	}

	/**
	 * @param int $productId
	 * @return int
	 */
	public function getPreviousOrder(int $productId): int {
		return (int) $this->wpDataApiService->getPostMeta($productId, Constants::PREVIOUS_ORDER_FIELD) ?: -1;
	}

	/**
	 * @param int $productId
	 * @return bool
	 */
	public function getExcludeFromSorting(int $productId): bool {
		return ($this->wpDataApiService->getPostMeta($productId, Constants::EXCLUDE_FROM_SORTING_FIELD) ?: "no") === "yes";
	}

	/**
	 * @param int $lineItemId
	 * @param int $productId
	 * @param int $quantity
	 * @return void
	 */
	public function incrementSalesAndSave(int $lineItemId, int $productId, int $quantity = 1): void {
		if($this->wpDataApiService->getLineItemMeta($lineItemId, Constants::LINE_ITEM_RECORDED) === "yes") {
			return;
		}

		$sales = $this->getSales($productId);
		$incrementedSales = $this->incrementSales($sales, $quantity);
		$this->wpDataApiService->updatePostMeta($productId, Constants::SALES_FIELD, $incrementedSales);
		$this->wpDataApiService->updateLineItemMeta($lineItemId, Constants::LINE_ITEM_RECORDED, "yes");
	}

	/**
	 * @param array<string, int> $sales
	 * @param int $salesQuantity
	 * @param string $date
	 * @return array<string, int>
	 */
	public function incrementSales(array $sales, int $salesQuantity = 1, string $date = "TODAY"): array {
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

		$maxDaysAccepted = Constants::SALES_PERIODS[COUNT(Constants::SALES_PERIODS) - 1];
		$maxDaysAgo = date("Y-m-d", strtotime("-$maxDaysAccepted days"));
		if($date < $maxDaysAgo) {
			return $sales;
		}

		$sales[$date] = $sales[$date] ?? 0;
		$sales[$date] += $salesQuantity;

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
