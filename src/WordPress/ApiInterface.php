<?php
declare(strict_types=1);

namespace MergeOrg\WpPluginSort\WordPress;

use MergeOrg\WpPluginSort\Data\UnrecordedOrder\Order;

/**
 * @codeCoverageIgnore
 */
interface ApiInterface {

	/**
	 * @return Order[]
	 */
	public function getUnrecordedOrders(): array;

	/**
	 * @param int $orderId
	 *
	 * @return void
	 */
	public function setOrderRecorded( int $orderId ): void;

	/**
	 * @param int                               $productId
	 * @param array<string, array<string, int>> $sales
	 *
	 * @return void
	 */
	public function updateProductSales( int $productId, array $sales ): void;
}
