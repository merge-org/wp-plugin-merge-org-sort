<?php
declare(strict_types=1);

namespace MergeOrg\Sort\WordPress;

use MergeOrg\Sort\Data\WordPress\Order;
use MergeOrg\Sort\Data\WordPress\AbstractProduct;
use MergeOrg\Sort\Exception\InvalidKeyNameSortException;

interface ApiInterface {

	/**
	 * @param int    $postId
	 * @param string $metaKey
	 * @param mixed  $default
	 * @return mixed
	 */
	public function getPostMeta( int $postId, string $metaKey, $default = null );

	/**
	 * @param int $productId
	 * @return AbstractProduct|null
	 */
	public function getProduct( int $productId ): ?AbstractProduct;

	/**
	 * @param int $productId
	 * @return bool
	 */
	public function getProductIsExcludedFromSorting( int $productId ): bool;

	/**
	 * @param int $productId
	 * @return int
	 */
	public function getProductPreviousOrder( int $productId ): int;

	/**
	 * @param int $orderId
	 * @return Order|null
	 */
	public function getOrder( int $orderId ): ?Order;

	/**
	 * @return AbstractProduct[]
	 * @throws InvalidKeyNameSortException
	 */
	public function getProductsWithNoRecentUpdatedIndex(): array;

	/**
	 * @param int    $postId
	 * @param string $metaKey
	 * @param mixed  $value
	 * @return bool
	 */
	public function updatePostMeta( int $postId, string $metaKey, $value ): bool;

	/**
	 * @param int    $postId
	 * @param string $metaKey
	 * @param mixed  $value
	 * @return bool
	 */
	public function updateProductMeta( int $postId, string $metaKey, $value ): bool;

	/**
	 * @param int    $postId
	 * @param string $metaKey
	 * @param mixed  $value
	 * @return bool
	 */
	public function updateOrderMeta( int $postId, string $metaKey, $value ): bool;
}
