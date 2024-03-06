<?php
declare(strict_types=1);

namespace MergeOrg\WpPluginSort\WordPress;

use WP_Post;
use WP_Query;
use DateTime;
use Exception;
use MergeOrg\WpPluginSort\Constants;
use MergeOrg\WpPluginSort\Data\UnrecordedOrder\Order;
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
	 * @param Constants $constants
	 */
	public function __construct( Constants $constants ) {
		$this->constants = $constants;
	}

	/**
	 * @return Order[]
	 * @throws Exception
	 */
	public function getUnrecordedOrders(): array {
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
			'posts_per_page' => 50,
			'orderby'        => 'ID',
			'order'          => 'ASC',
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
					'after'     => date( 'Y-m-d 00:00:00', strtotime( '-365 days' ) ),
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
			$order = wc_get_order( $order->ID );
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
	 *
	 * @return void
	 */
	public function setOrderRecorded( int $orderId ): void {
		if ( ! ( $order = wc_get_order( $orderId ) )
			|| $order->get_meta( $this->constants->getRecordedMetaKey() ) === 'yes'
		) {
			return;
		}

		$order->update_meta_data( $this->constants->getRecordedMetaKey(), 'yes' );
		$order->update_meta_data( $this->constants->getRecordedDateTimeMetaKey(), date( 'Y-m-d H:i:s' ) );
		$order->save();
	}

	/**
	 * @param int                               $productId
	 * @param array<string, array<string, int>> $sales
	 *
	 * @return void
	 */
	public function updateProductSales( int $productId, array $sales ): void {
		if ( ! ( $product = wc_get_product( $productId ) ) ) {
			return;
		}

		$product->update_meta_data( $this->constants->getSalesMetaKey(), $sales );
		$product->save();
	}
}
