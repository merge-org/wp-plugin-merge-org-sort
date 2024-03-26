<?php
declare(strict_types=1);

namespace MergeOrg\WpPluginSort\WordPress;

use WP_Post;
use WP_Query;
use DateTime;
use WC_Order;
use Exception;
use MergeOrg\WpPluginSort\Constants;
use MergeOrg\WpPluginSort\Data\UnrecordedOrder\Order;
use MergeOrg\WpPluginSort\Service\SalesPeriodManager;
use MergeOrg\WpPluginSort\Data\UnrecordedOrder\Product;
use MergeOrg\WpPluginSort\Data\UnrecordedOrder\LineItem;
use MergeOrg\WpPluginSort\Data\UnrecordedOrder\ProductVariation;

/**
 * @codeCoverageIgnore
 */
final class Api implements ApiInterface {

	/**
	 * @var Constants
	 */
	private Constants $constants;

	/**
	 * @var SalesPeriodManager
	 */
	private SalesPeriodManager $salesPeriodManager;

	/**
	 * @param Constants          $constants
	 * @param SalesPeriodManager $salesPeriodManager
	 */
	public function __construct( Constants $constants, SalesPeriodManager $salesPeriodManager ) {
		$this->constants          = $constants;
		$this->salesPeriodManager = $salesPeriodManager;
	}

	/**
	 * @param int $products
	 * @return \MergeOrg\WpPluginSort\Data\Sort\Product[]
	 */
	public function getNonUpdatedSalesPeriodsProducts( int $products = 5 ): array {
		$dev  = ( $_ENV['APP_ENV'] ?? 'production' ) === 'dev';
		$date = date( 'Y-m-d 23:59:59', strtotime( '-1 days' ) );
		$dev && ( $date = date( 'Y-m-d 00:00:00', strtotime( '+1 days' ) ) );
		$dev && ( $products = 100 );

		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => $products,
			'post_status'    => array(
				'publish',
				'draft',
			),
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => $this->constants->getSalesPeriodsLastUpdateMetaKey(),
					'value'   => $date,
					'compare' => '<',
					'type'    => 'DATE',
				),
				array(
					'key'     => $this->constants->getSalesPeriodsLastUpdateMetaKey(),
					'value'   => '',
					'compare' => 'NOT EXISTS',
				),
			),
		);

		$query     = new WP_Query( $args );
		$products_ = $query->get_posts();
		$products  = array();

		/**
		 * @var WP_Post $product
		 */
		foreach ( $products_ as $product ) {
			$products[] = $this->getSortProduct( $product->ID );
		}

		return $products;
	}

	/**
	 * @param int $productId
	 * @return \MergeOrg\WpPluginSort\Data\Sort\Product
	 */
	public function getSortProduct( int $productId ): \MergeOrg\WpPluginSort\Data\Sort\Product {
		$sales        = get_post_meta( $productId, $this->constants->getSalesMetaKey(), true ) ?: array();
		$salesPeriods = $this->salesPeriodManager->getAllSalesPeriods( $sales );

		return new \MergeOrg\WpPluginSort\Data\Sort\Product( $productId, $salesPeriods );
	}

	/**
	 * @return int
	 * @throws Exception
	 */
	public function getUnrecordedOrdersCount(): int {
		$statuses = array_diff(
			array_keys( wc_get_order_statuses() ),
			array(
				'trash',
				'wc-pending',
				'wc-on-hold',
				'wc-refunded',
				'wc-failed',
				'wc-checkout-draft',
				'wc-cancelled',
			)
		);

		$args = array(
			'post_type'      => 'shop_order',
			'posts_per_page' => 5,
			'orderby'        => 'ID',
			'order'          => 'DESC',
			'post_status'    => $statuses,
			'meta_query'     => array(
				array(
					'key'     => $this->constants->getRecordedMetaKey(),
					'compare' => 'NOT EXISTS',
					'value'   => '',
				),
			),
			'date_query'     => array(
				array(
					'before'    => date( 'Y-m-d 23:59:59', strtotime( '-1 days' ) ),
					'inclusive' => true,
				),
			),
		);

		$query = new WP_Query( $args );

		return $query->found_posts;
	}

	/**
	 * @return Order[]
	 * @throws Exception
	 */
	public function getUnrecordedOrders( int $orders = 5 ): array {
		$dev  = ( $_ENV['APP_ENV'] ?? 'production' ) === 'dev';
		$date = date( 'Y-m-d 00:00:00', strtotime( '-1 days' ) );
		$dev && ( $date = date( 'Y-m-d 00:00:00', strtotime( '+1 days' ) ) );

		$statuses = array_diff(
			array_keys( wc_get_order_statuses() ),
			array(
				'trash',
				'wc-pending',
				'wc-on-hold',
				'wc-refunded',
				'wc-failed',
				'wc-checkout-draft',
				'wc-cancelled',
			)
		);

		$args = array(
			'post_type'      => 'shop_order',
			'posts_per_page' => $orders,
			'orderby'        => 'ID',
			'order'          => 'DESC',
			'post_status'    => $statuses,
			'meta_query'     => array(
				array(
					'key'     => $this->constants->getRecordedMetaKey(),
					'compare' => 'NOT EXISTS',
					'value'   => '',
				),
			),
			'date_query'     => array(
				array(
					'before'    => $date,
					'inclusive' => true,
				),
			),
		);

		$query   = new WP_Query( $args );
		$orders_ = $query->get_posts();
		$orders  = array();

		/**
		 * @var WP_Post $order
		 */
		foreach ( $orders_ as $order ) {
			$order = $this->getOrder( $order->ID );
			if ( ! $order ) {
				continue;
			}

			$lineItems = array();
			foreach ( $order->get_items() as $item ) {
				$data             = $item->get_data();
				$productId        = $data['product_id'];
				$productSales     = get_post_meta( $productId, $this->constants->getSalesMetaKey(), true ) ?: array();
				$variationId      = $data['variation_id'];
				$variationSales   =
					$variationId ? ( get_post_meta( $variationId, $this->constants->getSalesMetaKey(), true ) ?: array() ) : array();
				$productVariation = $variationId ? new ProductVariation( $variationId, $variationSales ) : null;
				$lineItems[]      =
					new LineItem( $item->get_id(), new Product( $productId, $productSales ), $data['quantity'], $productVariation );
			}

			$orders[] =
				new Order( $order->get_id(), new DateTime( $order->get_date_created()->format( 'Y-m-d H:i:s' ) ), $lineItems );
		}

		return $orders;
	}

	/**
	 * @param int $orderId
	 * @return WC_Order|null
	 */
	private function getOrder( int $orderId ): ?WC_Order {
		$order = wc_get_order( $orderId );

		return $order instanceof WC_Order ? $order : null;
	}

	/**
	 * @param int $productId
	 * @return array<string, array<string, int>>
	 */
	public function getProductSales( int $productId ): array {
		return get_post_meta( $productId, $this->constants->getSalesMetaKey(), true ) ?: array();
	}

	/**
	 * @param int $orderId
	 * @return void
	 */
	public function setOrderRecorded( int $orderId ): void {
		if ( $this->isOrderRecorded( $orderId ) ) {
			return;
		}

		update_post_meta( $orderId, $this->constants->getRecordedMetaKey(), 'yes' );
	}

	/**
	 * @param int $orderId
	 * @return bool
	 */
	public function isOrderRecorded( int $orderId ): bool {
		return get_post_meta( $orderId, $this->constants->getRecordedMetaKey(), true ) === 'yes';
	}

	/**
	 * @param int $productId
	 * @return void
	 */
	public function updateProductSalesPeriodsLastUpdate( int $productId ): void {
		update_post_meta( $productId, $this->constants->getSalesPeriodsLastUpdateMetaKey(), date( 'Y-m-d H:i:s' ) );
	}

	/**
	 * @param int $productId
	 * @param int $days
	 * @param int $purchaseSales
	 * @param int $quantitySales
	 * @return void
	 */
	public function updateProductSalesPeriod( int $productId, int $days, int $purchaseSales, int $quantitySales ): void {
		update_post_meta( $productId, $this->constants->getSalesPeriodPurchaseMetaKey( $days ), $purchaseSales );
		update_post_meta( $productId, $this->constants->getSalesPeriodQuantityMetaKey( $days ), $quantitySales );
	}

	/**
	 * @param int                               $productId
	 * @param array<string, array<string, int>> $sales
	 * @return void
	 */
	public function updateProductSales( int $productId, array $sales ): void {
		update_post_meta( $productId, $this->constants->getSalesMetaKey(), $sales );
	}
}
