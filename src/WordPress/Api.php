<?php
declare(strict_types=1);

namespace MergeOrg\Sort\WordPress;

use WC_Product;
use WC_Product_Query;
use MergeOrg\Sort\Service\Namer;
use MergeOrg\Sort\Data\WordPress\Order;
use MergeOrg\Sort\Data\WordPress\Product;
use MergeOrg\Sort\Data\WordPress\LineItem;
use MergeOrg\Sort\Data\WordPress\AbstractProduct;
use MergeOrg\Sort\Data\WordPress\ProductVariation;
use MergeOrg\Sort\Exception\InvalidKeyNameSortException;

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
	 * @param Namer $namer
	 */
	public function __construct( Namer $namer ) {
		$this->namer = $namer;
	}

	/**
	 * @param int    $postId
	 * @param string $metaKey
	 * @param mixed  $value
	 * @return bool
	 * @throws InvalidKeyNameSortException
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
	 * @throws InvalidKeyNameSortException
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
		if ( $product->get_parent_id() ) {
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
	 * @throws InvalidKeyNameSortException
	 */
	public function getProductIsExcludedFromSorting( int $productId ): bool {
		return $this->getPostMeta( $productId, $this->namer->getExcludeFromSortingMetaKeyName(), 'no' ) === 'yes';
	}

	/**
	 * @throws InvalidKeyNameSortException
	 */
	public function getProductPreviousOrder( int $productId ): int {
		return (int) $this->getPostMeta( $productId, $this->namer->getPreviousOrderMetaKeyName(), '-1' );
	}

	/**
	 * @throws InvalidKeyNameSortException
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
	 * @throws InvalidKeyNameSortException
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
			$order->get_date_paid()->format( 'Y-m-d' ),
			$lineItems,
			$this->getOrderIsRecorded( $order->get_id() )
		);
	}

	/**
	 * @throws InvalidKeyNameSortException
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
	 * @return AbstractProduct[]
	 * @throws InvalidKeyNameSortException
	 */
	public function getProductsWithNoRecentUpdatedIndex(): array {
		$args = array(
			'limit'      => 10,
			'orderby'    => 'ID',
			'order'      => 'ASC',
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key'     => 'merge-org-sort-last_index_update',
					'value'   => date( 'Y-m-d' ),
					'compare' => '<',
					'type'    => 'DATE',
				),
				array(
					'key'     => 'merge-org-sort-last_index_update',
					'value'   => '',
					'compare' => 'NOT EXISTS',
				),
			),
		);

		$query     = new WC_Product_Query( $args );
		$products_ = $query->get_products();
		$products  = array();

		/**
		 * @var WC_Product $product
		 */
		foreach ( $products_ as $product ) {
			$products[] = $this->getProduct( $product->get_id() );
		}

		return $products;
	}
}
