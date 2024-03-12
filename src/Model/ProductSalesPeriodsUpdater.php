<?php
declare(strict_types=1);

namespace MergeOrg\WpPluginSort\Model;

use MergeOrg\WpPluginSort\Data\Sort\Product;
use MergeOrg\WpPluginSort\WordPress\ApiInterface;

/**
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
	 * @param Product $nonUpdatedSalesPeriodsProduct
	 * @return void
	 */
	public function update( Product $nonUpdatedSalesPeriodsProduct ): void {
		$this->api->updateProductSalesPeriodsLastUpdate( $nonUpdatedSalesPeriodsProduct->getId() );
		foreach ( $nonUpdatedSalesPeriodsProduct->getSalesPeriods() as $salesPeriod ) {
			$this->api->updateProductSalesPeriod(
				$nonUpdatedSalesPeriodsProduct->getId(),
				$salesPeriod->getDays(),
				$salesPeriod->getPurchaseSales(),
				$salesPeriod->getQuantitySales()
			);
		}
	}
}
