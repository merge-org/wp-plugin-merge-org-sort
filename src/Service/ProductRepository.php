<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service;

use MergeOrg\Sort\Constants;
use MergeOrg\Sort\Data\Product;
use MergeOrg\Sort\Data\AbstractProduct;
use MergeOrg\Sort\Data\ProductVariation;
use MergeOrg\Sort\WordPress\ApiInterface;

final class ProductRepository {

	/**
	 * @var ApiInterface
	 */
	private ApiInterface $api;

	/**
	 * @var SalesPeriodManager
	 */
	private SalesPeriodManager $salesPeriodManager;

	/**
	 * @param ApiInterface $api
	 * @param SalesPeriodManager $salesPeriodManager
	 */
	public function __construct(ApiInterface $api, SalesPeriodManager $salesPeriodManager) {
		$this->api = $api;
		$this->salesPeriodManager = $salesPeriodManager;
	}

	/**
	 * @param int $productId
	 * @return AbstractProduct|null
	 */
	public function getProduct(int $productId): ?AbstractProduct {
		$wordPressProduct = $this->api->getProduct($productId);
		if(!$wordPressProduct) {
			return NULL;
		}

		$salesPeriods = $this->salesPeriodManager->getAllSalesPeriods($wordPressProduct->getSales());

		if($wordPressProduct->getType() === Constants::POST_TYPE_PRODUCT) {
			/**
			 * @var \MergeOrg\Sort\Data\WordPress\Product $wordPressProduct
			 */
			return new Product($wordPressProduct->getId(),
				$salesPeriods,
				$wordPressProduct->isExcludedFromSorting(),
				$wordPressProduct->getPreviousMenuOrder(),
				$wordPressProduct->getLastIndexUpdate());
		}

		/**
		 * @var \MergeOrg\Sort\Data\WordPress\ProductVariation $wordPressProduct
		 */
		return new ProductVariation($wordPressProduct->getId(), $salesPeriods);
	}
}
