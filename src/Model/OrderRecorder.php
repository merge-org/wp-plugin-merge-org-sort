<?php
declare(strict_types=1);

namespace MergeOrg\WpPluginSort\Model;

use MergeOrg\WpPluginSort\WordPress\ApiInterface;
use MergeOrg\WpPluginSort\Service\SalesIncrementer;
use MergeOrg\WpPluginSort\Data\UnrecordedOrder\Order;

/**
 * @codeCoverageIgnore
 */
final class OrderRecorder {

	/**
	 * @var ApiInterface
	 */
	private ApiInterface $api;

	/**
	 * @var SalesIncrementer
	 */
	private SalesIncrementer $salesIncrementer;

	/**
	 * @var ProductSalesPeriodsUpdater
	 */
	private ProductSalesPeriodsUpdater $productSalesPeriodUpdater;

	/**
	 * @param ApiInterface               $api
	 * @param SalesIncrementer           $salesIncrementer
	 * @param ProductSalesPeriodsUpdater $productSalesPeriodsUpdater
	 */
	public function __construct(
		ApiInterface $api,
		SalesIncrementer $salesIncrementer,
		ProductSalesPeriodsUpdater $productSalesPeriodsUpdater
	) {
		$this->api                       = $api;
		$this->salesIncrementer          = $salesIncrementer;
		$this->productSalesPeriodUpdater = $productSalesPeriodsUpdater;
	}

	/**
	 * @param Order $nonRecordedOrder
	 * @return void
	 */
	public function record( Order $nonRecordedOrder ): void {
		$this->api->setOrderRecorded( $nonRecordedOrder->getId() );
		foreach ( $nonRecordedOrder->getLineItems() as $lineItem ) {
			$incrementedSales =
				$this->salesIncrementer->increment(
					$lineItem->getProduct()->getSales(),
					$lineItem->getQuantity(),
					$nonRecordedOrder->getDateTime()->format( 'Y-m-d' )
				);
			$this->api->updateProductSales( $lineItem->getProduct()->getId(), $incrementedSales );
			$this->productSalesPeriodUpdater->update(
				$this->api->getNonUpdatedSalesPeriodsProduct(
					$lineItem->getProduct()
																											->getId()
				)
			);
			if ( $lineItem->getProductVariation() ) {
				$incrementedSales =
					$this->salesIncrementer->increment(
						$lineItem->getProductVariation()->getSales(),
						$lineItem->getQuantity(),
						$nonRecordedOrder->getDateTime()->format( 'Y-m-d' )
					);
				$this->api->updateProductSales( $lineItem->getProductVariation()->getId(), $incrementedSales );
			}
		}
	}
}
