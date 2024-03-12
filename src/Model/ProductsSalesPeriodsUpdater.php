<?php
declare(strict_types=1);

namespace MergeOrg\WpPluginSort\Model;

use MergeOrg\WpPluginSort\Data\Sort\Product;
use MergeOrg\WpPluginSort\WordPress\ApiInterface;

/**
 * @codeCoverageIgnore
 */
final class ProductsSalesPeriodsUpdater {

	/**
	 * @var ApiInterface
	 */
	private ApiInterface $api;

	/**
	 * @var ProductSalesPeriodsUpdater
	 */
	private ProductSalesPeriodsUpdater $productSalesPeriodsUpdater;

	/**
	 * @param ApiInterface               $api
	 * @param ProductSalesPeriodsUpdater $productSalesPeriodsUpdater
	 */
	public function __construct( ApiInterface $api, ProductSalesPeriodsUpdater $productSalesPeriodsUpdater ) {
		$this->api                        = $api;
		$this->productSalesPeriodsUpdater = $productSalesPeriodsUpdater;
	}

	/**
	 * @param int $products
	 * @return Product[]
	 */
	public function update( int $products = 5 ): array {
		$nonUpdatedSalesPeriodsProducts = $this->api->getNonUpdatedSalesPeriodsProducts( $products );
		foreach ( $nonUpdatedSalesPeriodsProducts as $nonUpdatedSalesPeriodsProduct ) {
			$this->productSalesPeriodsUpdater->update( $nonUpdatedSalesPeriodsProduct );
		}

		return $nonUpdatedSalesPeriodsProducts;
	}
}
