<?php
declare(strict_types=1);

namespace MergeOrg\WpPluginSort\Model;

use MergeOrg\WpPluginSort\WordPress\ApiInterface;
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
	 * @var OrderRecorder
	 */
	private OrderRecorder $orderRecorder;

	/**
	 * @param ApiInterface  $api
	 * @param OrderRecorder $orderRecorder
	 */
	public function __construct( ApiInterface $api, OrderRecorder $orderRecorder ) {
		$this->api           = $api;
		$this->orderRecorder = $orderRecorder;
	}

	/**
	 * @return Order[]
	 */
	public function record( int $orders = 5 ): array {
		$nonRecordedOrders = $this->api->getUnrecordedOrders( $orders );
		foreach ( $nonRecordedOrders as $nonRecordedOrder ) {
			$this->orderRecorder->record( $nonRecordedOrder );
		}

		return $nonRecordedOrders;
	}
}
