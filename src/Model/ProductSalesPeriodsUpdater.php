<?php
declare(strict_types=1);

namespace MergeOrg\WpPluginSort\Model;

use MergeOrg\WpPluginSort\WordPress\ApiInterface;
use MergeOrg\WpPluginSort\Data\NonUpdatedSalesPeriodsProduct\Product;

/**
 * Class ProductSalesPeriodsUpdater
 *
 * @package MergeOrg\WpPluginSort\Action
 * @codeCoverageIgnore
 */
final class ProductSalesPeriodsUpdater {

	/**
	 * @var ApiInterface
	 */
	private ApiInterface $api;

	/**
	 * @param ApiInterface $api
	 */
	public function __construct( ApiInterface $api ) {
		$this->api = $api;
	}

	/**
	 * @param int $products
	 * @return Product[]
	 */
	public function update( int $products = 5 ): array {
		$nonUpdatedSalesPeriodsProducts = $this->api->getNonUpdatedSalesPeriodsProducts( $products );
		foreach ( $nonUpdatedSalesPeriodsProducts as $product ) {
			$this->api->updateProductSalesPeriodsLastUpdate( $product->getId() );
			foreach ( $product->getSalesPeriods() as $salesPeriod ) {
				$this->api->updateProductSalesPeriod(
					$product->getId(),
					$salesPeriod->getDays(),
					$salesPeriod->getPurchaseSales(),
					$salesPeriod->getQuantitySales()
				);
			}
		}

		return $nonUpdatedSalesPeriodsProducts;
	}
}
