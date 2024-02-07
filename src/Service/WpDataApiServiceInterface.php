<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service;

use WC_Order;
use WC_Order_Refund;

interface WpDataApiServiceInterface {

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
	 * @param int $orderId
	 * @return bool|WC_Order|WC_Order_Refund
	 */
	public function getOrder(int $orderId);
}
