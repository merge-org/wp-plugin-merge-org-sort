<?php
declare(strict_types=1);

namespace MergeOrg\Sort\WordPress;

use MergeOrg\Sort\Data\WordPress\Order;
use MergeOrg\Sort\Data\WordPress\AbstractProduct;

interface ApiInterface {

	/**
	 * @param int $postId
	 * @param string $metaKey
	 * @param mixed $default
	 * @return mixed
	 */
	public function getPostMeta(int $postId, string $metaKey, $default = NULL);

	/**
	 * @param int $productId
	 * @return AbstractProduct|null
	 */
	public function getProduct(int $productId): ?AbstractProduct;

	/**
	 * @param int $productId
	 * @return bool
	 */
	public function getProductIsExcludedFromSorting(int $productId): bool;

	/**
	 * @param int $productId
	 * @return int
	 */
	public function getProductPreviousOrder(int $productId): int;

	/**
	 * @param int $orderId
	 * @return Order|null
	 */
	public function getOrder(int $orderId): ?Order;
}
