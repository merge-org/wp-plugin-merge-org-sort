<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service;

use MergeOrg\Sort\Constants;
use MergeOrg\Sort\Data\Product;
use MergeOrg\Sort\Data\AbstractProduct;
use MergeOrg\Sort\Data\ProductVariation;
use MergeOrg\Sort\WordPress\ApiInterface;
use MergeOrg\Sort\WordPress\CacheInterface;
use MergeOrg\Sort\Exception\InvalidKeyNameSortException;

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
	 * @var CacheInterface
	 */
	private CacheInterface $cache;

	/**
	 * @var Namer
	 */
	private Namer $namer;

	/**
	 * @param ApiInterface       $api
	 * @param SalesPeriodManager $salesPeriodManager
	 * @param CacheInterface     $cache
	 * @param Namer              $namer
	 */
	public function __construct(
		ApiInterface $api,
		SalesPeriodManager $salesPeriodManager,
		CacheInterface $cache,
		Namer $namer
	) {
		$this->api                = $api;
		$this->salesPeriodManager = $salesPeriodManager;
		$this->cache              = $cache;
		$this->namer              = $namer;
	}

	/**
	 * @param int $productId
	 * @return AbstractProduct|null
	 * @throws InvalidKeyNameSortException
	 */
	public function getProduct( int $productId ): ?AbstractProduct {
		$cacheKey = $this->namer->getProductCacheKey( $productId );
		if ( $product = $this->cache->get( $cacheKey ) ) {
			// @codeCoverageIgnoreStart
			return $product;
			// @codeCoverageIgnoreEnd
		}

		$wordPressProduct = $this->api->getProduct( $productId );
		if ( ! $wordPressProduct ) {
			return null;
		}

		$salesPeriods = $this->salesPeriodManager->getAllSalesPeriods( $wordPressProduct->getSales() );

		$product = null;
		if ( $wordPressProduct->getType() === Constants::POST_TYPE_PRODUCT ) {
			/**
			 * @var \MergeOrg\Sort\Data\WordPress\Product $wordPressProduct
			 */
			$product = new Product(
				$wordPressProduct->getId(),
				$salesPeriods,
				$wordPressProduct->isExcludedFromSorting(),
				$wordPressProduct->getPreviousMenuOrder(),
				$wordPressProduct->getLastIndexUpdate()
			);
		} else {
			/**
			 * @var \MergeOrg\Sort\Data\WordPress\ProductVariation $wordPressProduct
			 */
			$product = new ProductVariation( $wordPressProduct->getId(), $salesPeriods );
		}

		$this->cache->set( $cacheKey, $product );

		return $product;
	}
}
