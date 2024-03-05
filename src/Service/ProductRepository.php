<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service;

use MergeOrg\Sort\Constants;
use MergeOrg\Sort\Data\Product;
use MergeOrg\Sort\Data\AbstractProduct;
use MergeOrg\Sort\Data\ProductVariation;
use MergeOrg\Sort\WordPress\ApiInterface;
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
	 * @var Namer
	 */
	private Namer $namer;

	/**
	 * @param ApiInterface       $api
	 * @param SalesPeriodManager $salesPeriodManager
	 * @param Namer              $namer
	 */
	public function __construct(
		ApiInterface $api,
		SalesPeriodManager $salesPeriodManager,
		Namer $namer
	) {
		$this->api                = $api;
		$this->salesPeriodManager = $salesPeriodManager;
		$this->namer              = $namer;
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
	 */
	public function getProduct( int $productId ): ?AbstractProduct {
		$wordPressProduct = $this->api->getProduct( $productId );
		if ( ! $wordPressProduct ) {
			return null;
		}

		$salesPeriods = $this->salesPeriodManager->getAllSalesPeriods( $wordPressProduct->getSales() );

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
		return $this->api->updateProductMeta( $productId, $this->namer->getSalesMetaKeyName(), $sales );
	}
}
