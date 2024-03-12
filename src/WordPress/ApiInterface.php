<?php
declare(strict_types=1);

namespace MergeOrg\WpPluginSort\WordPress;

use MergeOrg\WpPluginSort\Data\Sort\Product;
use MergeOrg\WpPluginSort\Data\UnrecordedOrder\Order;

/**
 * @codeCoverageIgnore
 */
interface ApiInterface {

	/**
	 * @return Order[]
	 */
	public function getUnrecordedOrders( int $orders = 50 ): array;

	/**
	 * @return int
	 */
	public function getUnrecordedOrdersCount(): int;

	/**
	 * @param int $orderId
	 * @return void
	 */
	public function setOrderRecorded( int $orderId ): void;

	/**
	 * @param int                               $productId
	 * @param array<string, array<string, int>> $sales
	 * @return void
	 */
	public function updateProductSales( int $productId, array $sales ): void;

	/**
	 * @param int $products
	 * @return Product[]
	 */
	public function getNonUpdatedSalesPeriodsProducts( int $products = 10 ): array;

	/**
	 * @param int $productId
	 * @return Product
	 */
	public function getSortProduct( int $productId ): Product;

	/**
	 * @param int $productId
	 * @return void
	 */
	public function updateProductSalesPeriodsLastUpdate( int $productId ): void;

	/**
	 * @param int $productId
	 * @param int $days
	 * @param int $purchaseSales
	 * @param int $quantitySales
	 * @return void
	 */
	public function updateProductSalesPeriod( int $productId, int $days, int $purchaseSales, int $quantitySales ): void;
}
