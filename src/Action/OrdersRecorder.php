<?php
declare(strict_types=1);

namespace MergeOrg\WpPluginSort\Action;

use MergeOrg\WpPluginSort\WordPress\ApiInterface;
use MergeOrg\WpPluginSort\Service\SalesIncrementer;
use MergeOrg\WpPluginSort\Data\UnrecordedOrder\Order;

/**
 * @codeCoverageIgnore
 */
final class OrdersRecorder {

	/**
	 * @var ApiInterface
	 */
	private ApiInterface $api;

	/**
	 * @var SalesIncrementer
	 */
	private SalesIncrementer $salesIncrementer;

	/**
	 * @param ApiInterface     $api
	 * @param SalesIncrementer $salesIncrementer
	 */
	public function __construct( ApiInterface $api, SalesIncrementer $salesIncrementer ) {
		$this->api              = $api;
		$this->salesIncrementer = $salesIncrementer;
	}

	/**
	 * @return Order[]
	 */
	public function record(): array {
		$nonRecordedOrders = $this->api->getUnrecordedOrders();
		foreach ( $nonRecordedOrders as $nonRecordedOrder ) {
			$this->api->setOrderRecorded( $nonRecordedOrder->getId() );
			foreach ( $nonRecordedOrder->getLineItems() as $lineItem ) {
				$incrementedSales =
					$this->salesIncrementer->increment(
						$lineItem->getProduct()->getSales(),
						$lineItem->getQuantity(),
						$nonRecordedOrder->getDateTime()->format( 'Y-m-d' )
					);
				$this->api->updateProductSales( $lineItem->getProduct()->getId(), $incrementedSales );
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

		return $nonRecordedOrders;
	}
}
