<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service;

use MergeOrg\Sort\Constants;
use MergeOrg\Sort\Data\Product;
use MergeOrg\Sort\Data\AbstractProduct;
use MergeOrg\Sort\Data\ProductVariation;
use MergeOrg\Sort\WordPress\ApiInterface;
use MergeOrg\Sort\WordPress\CacheInterface;
use MergeOrg\Sort\Exception\InvalidKeyNameException;

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
	 * @return AbstractProduct[]
	 * @throws InvalidKeyNameException
	 * @codeCoverageIgnore
	 */
	public function setProductsIndexes(): array {
		$updatedProducts = array();

		/**
		 * @var Product $product
		 */
		foreach ( $this->getProductsWithNoRecentUpdatedIndex() as $product ) {
			$cacheKey = $this->namer->getProductCacheKey( $product->getId() );
			$this->cache->delete( $cacheKey );

			foreach ( $product->getIndexesMetaKeys( $this->namer ) as $indexMetaKey => $sales ) {
				$this->api->updateProductMeta( $product->getId(), $indexMetaKey, $sales );
			}

			$this->api->updateProductMeta(
				$product->getId(),
				$this->namer->getLastIndexUpdateMetaKeyName(),
				date( 'Y-m-d H:i:s' )
			);

			// Place back in cache
			$updatedProducts[] = $this->getProduct( $product->getId() );
		}

		return $updatedProducts;
	}

	/**
	 * @return AbstractProduct[]
	 * @throws InvalidKeyNameException
	 */
	public function getProductsWithNoRecentUpdatedIndex(): array {
		$products_ = $this->api->getProductsWithNoRecentUpdatedIndex();
		$products  = array();
		foreach ( $products_ as $product ) {
			$products[] = $this->getProduct( $product->getId() );
		}

		return $products;
	}

	/**
	 * @param int $productId
	 * @return AbstractProduct|null
	 * @throws InvalidKeyNameException
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
				$wordPressProduct->getSales(),
				$salesPeriods,
				$wordPressProduct->isExcludedFromSorting(),
				$wordPressProduct->getPreviousMenuOrder(),
				$wordPressProduct->getLastIndexUpdate()
			);
		} else {
			/**
			 * @var \MergeOrg\Sort\Data\WordPress\ProductVariation $wordPressProduct
			 */
			$product = new ProductVariation( $wordPressProduct->getId(), $wordPressProduct->getSales(), $salesPeriods );
		}

		$this->cache->set( $cacheKey, $product );

		return $product;
	}

	/**
	 * @param int                            $productId
	 * @param array<string, array<int, int>> $sales
	 * @return bool
	 * @throws InvalidKeyNameException
	 * @codeCoverageIgnore
	 */
	public function setProductSales( int $productId, array $sales ): bool {
		$cacheKey = $this->namer->getProductCacheKey( $productId );
		$this->cache->delete( $cacheKey );

		$result = $this->api->updateProductMeta( $productId, $this->namer->getSalesMetaKeyName(), $sales );

		// Place back in cache
		$this->getProduct( $productId );

		return $result;
	}
}
