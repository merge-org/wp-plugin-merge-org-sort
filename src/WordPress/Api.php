<?php
declare(strict_types=1);

namespace MergeOrg\Sort\WordPress;

use WP_Post;
use WP_Query;
use MergeOrg\Sort\Service\Namer;
use MergeOrg\Sort\Data\WordPress\Order;
use MergeOrg\Sort\Data\WordPress\Product;
use MergeOrg\Sort\Data\WordPress\LineItem;
use MergeOrg\Sort\Data\WordPress\AbstractProduct;
use MergeOrg\Sort\Service\OptimalPostCountFinder;
use MergeOrg\Sort\Data\WordPress\ProductVariation;
use MergeOrg\Sort\Exception\InvalidKeyNameException;

/**
 * Class Api
 *
 * @package MergeOrg\Sort\WordPress
 * @codeCoverageIgnore
 */
final class Api implements ApiInterface {

	/**
	 * @var Namer
	 */
	private Namer $namer;

	/**
	 * @var OptimalPostCountFinder
	 */
	private OptimalPostCountFinder $optimalPostsCountFinder;

	/**
	 * @param Namer                  $namer
	 * @param OptimalPostCountFinder $optimalPostsCountFinder
	 */
	public function __construct( Namer $namer, OptimalPostCountFinder $optimalPostsCountFinder ) {
		$this->namer                   = $namer;
		$this->optimalPostsCountFinder = $optimalPostsCountFinder;
	}

	/**
	 * @param int    $postId
	 * @param string $metaKey
	 * @param mixed  $value
	 * @return bool
	 * @throws InvalidKeyNameException
	 */
	public function updatePostMeta( int $postId, string $metaKey, $value ): bool {
		if ( ! function_exists( 'update_post_meta' ) ) {
			return false;
		}

		if ( $this->getProduct( $postId ) ) {
			return $this->updateProductMeta( $postId, $metaKey, $value );
		}

		if ( $this->getOrder( $postId ) ) {
			return $this->updateOrderMeta( $postId, $metaKey, $value );
		}

		return (bool) update_post_meta( $postId, $metaKey, $value );
	}

	/**
	 * @param int $productId
	 * @return AbstractProduct|null
	 * @throws InvalidKeyNameException
	 */
	public function getProduct( int $productId ): ?AbstractProduct {
		if ( ! function_exists( 'wc_get_product' ) ) {
			return null;
		}

		$product = wc_get_product( $productId );
		if ( ! $product ) {
			return null;
		}

		$sales = $this->getPostMeta( $productId, $this->namer->getSalesMetaKeyName(), array() );
		if ( ! $product->get_parent_id() ) {
			return new Product(
				$product->get_id(),
				$sales,
				$this->getProductIsExcludedFromSorting( $product->get_id() ),
				$this->getProductPreviousOrder( $product->get_id() ),
				$this->getProductLastIndexUpdate( $product->get_id() ),
			);
		}

		return new ProductVariation( $product->get_id(), $sales );
	}

	/**
	 * @param int     $postId
	 * @param string  $metaKey
	 * @param $default
	 * @return mixed
	 */
	public function getPostMeta( int $postId, string $metaKey, $default = null ) {
		if ( ! function_exists( 'get_post_meta' ) ) {
			return $default;
		}

		return get_post_meta( $postId, $metaKey, true ) ?: $default;
	}

	/**
	 * @param int $productId
	 * @return bool
	 * @throws InvalidKeyNameException
	 */
	public function getProductIsExcludedFromSorting( int $productId ): bool {
		return $this->getPostMeta( $productId, $this->namer->getExcludeFromSortingMetaKeyName(), 'no' ) === 'yes';
	}

	/**
	 * @throws InvalidKeyNameException
	 */
	public function getProductPreviousOrder( int $productId ): int {
		return (int) $this->getPostMeta( $productId, $this->namer->getPreviousOrderMetaKeyName(), '-1' );
	}

	/**
	 * @throws InvalidKeyNameException
	 */
	public function getProductLastIndexUpdate( int $productId ): string {
		return $this->getPostMeta( $productId, $this->namer->getLastIndexUpdateMetaKeyName(), '1970-01-01' );
	}

	/**
	 * @param int    $postId
	 * @param string $metaKey
	 * @param mixed  $value
	 * @return bool
	 */
	public function updateProductMeta( int $postId, string $metaKey, $value ): bool {
		$product = wc_get_product( $postId );
		$product->update_meta_data( $metaKey, $value );

		return (bool) $product->save();
	}

	/**
	 * @param int $orderId
	 * @return Order|null
	 * @throws InvalidKeyNameException
	 */
	public function getOrder( int $orderId ): ?Order {
		if ( ! function_exists( 'wc_get_order' ) ) {
			return null;
		}

		$order = wc_get_order( $orderId );
		if ( ! $order ) {
			return null;
		}

		$lineItems = array();
		foreach ( $order->get_items() as $orderItem ) {
			$orderItemData = $orderItem->get_data();
			$lineItems[]   =
				new LineItem(
					$orderItemData['id'] ?? 0,
					$orderItemData['product_id'] ?? 0,
					$orderItemData['quantity'] ?? 0,
					$orderItemData['variation_id'] ?? 0
				);
		}

		return new Order(
			$order->get_id(),
			$order->get_status(),
			$order->get_date_paid()->format( 'Y-m-d H:i:s' ),
			$lineItems,
			$this->getOrderIsRecorded( $order->get_id() )
		);
	}

	/**
	 * @throws InvalidKeyNameException
	 */
	public function getOrderIsRecorded( int $orderId ): bool {
		return $this->getPostMeta( $orderId, $this->namer->getRecordedMetaKeyName(), 'no' ) === 'yes';
	}

	/**
	 * @param int    $postId
	 * @param string $metaKey
	 * @param mixed  $value
	 * @return bool
	 */
	public function updateOrderMeta( int $postId, string $metaKey, $value ): bool {
		$order = wc_get_order( $postId );
		$order->update_meta_data( $metaKey, $value );

		return (bool) $order->save();
	}

	/**
	 * @return Order[]
	 * @throws InvalidKeyNameException
	 */
	public function getOrdersNotRecorded(): array {
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
			'posts_per_page' => (int) ceil( $this->optimalPostsCountFinder->getOptimalPostsCount() / 2 ),
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'post_status'    => $statuses,
			'meta_query'     => array(
				array(
					'key'     => $this->namer->getRecordedMetaKeyName(),
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
			$orders[] = $this->getOrder( $order->ID );
		}

		return $orders;
	}

	/**
	 * @return AbstractProduct[]
	 * @throws InvalidKeyNameException
	 */
	public function getProductsWithNoRecentUpdatedIndex(): array {
		$date = date( 'Y-m-d 00:00:00' );

		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => $this->optimalPostsCountFinder->getOptimalPostsCount(),
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => $this->namer->getLastIndexUpdateMetaKeyName(),
					'value'   => $date,
					'compare' => '<',
					'type'    => 'DATE',
				),
				array(
					'key'     => $this->namer->getLastIndexUpdateMetaKeyName(),
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
			$products[] = $this->getProduct( $product->ID );
		}

		return $products;
	}
}
