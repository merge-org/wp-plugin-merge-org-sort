<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Wordpress\Api;

use MergeOrg\Sort\Data\Wordpress\Order;
use MergeOrg\Sort\Data\Wordpress\Product;

interface ApiInterface {

	/**
	 * @param int $postId
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function getPostMeta(int $postId, string $key, $default = NULL);

	/**
	 * @param int $postId
	 * @param string $metaKey
	 * @param mixed $metaValue
	 * @return bool
	 */
	public function updatePostMeta(int $postId, string $metaKey, $metaValue): bool;

	/**
	 * @param int $orderId
	 * @return ?Order
	 */
	public function getOrder(int $orderId): ?Order;

	/**
	 * @param int $lineItemId
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function getLineItemMeta(int $lineItemId, string $key, $default = NULL);

	/**
	 * @param int $lineItemId
	 * @param string $metaKey
	 * @param mixed $metaValue
	 * @return bool
	 */
	public function updateLineItemMeta(int $lineItemId, string $metaKey, $metaValue): bool;

	/**
	 * @param int $productId
	 * @return ?Product
	 */
	public function getProduct(int $productId): ?Product;

	/**
	 * @param string $option
	 * @param mixed $default
	 * @return mixed
	 */
	public function getOption(string $option, $default = NULL);

	/**
	 * @param string $option
	 * @param mixed $value
	 * @return bool
	 */
	public function updateOption(string $option, $value): bool;

	/**
	 * @param int $productId
	 * @return array<string, array<int>>
	 */
	public function getProductSales(int $productId): array;

	/**
	 * @param int $productId
	 * @param array<string, array<int>> $sales
	 * @return bool
	 */
	public function setProductSales(int $productId, array $sales): bool;

	/**
	 * @param int $orderId
	 * @return bool
	 */
	public function getOrderRecorded(int $orderId): bool;

	/**
	 * @param int $orderId
	 * @return bool
	 */
	public function setOrderRecorded(int $orderId): bool;
}
