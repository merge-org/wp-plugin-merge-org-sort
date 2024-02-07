<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service;

use WC_Order;
use Exception;
use WC_Order_Refund;

final class WpDataApiService implements WpDataApiServiceInterface {

	/**
	 * @param int $postId
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function getPostMeta(int $postId, string $key, $default = NULL) {
		return get_post_meta($postId, $key, TRUE) ?: $default;
	}

	/**
	 * @param int $postId
	 * @param string $metaKey
	 * @param mixed $metaValue
	 * @return bool
	 */
	public function updatePostMeta(int $postId, string $metaKey, $metaValue): bool {
		return (bool) update_post_meta($postId, $metaKey, $metaValue);
	}

	/**
	 * @param int $lineItemId
	 * @param string $key
	 * @param $default
	 * @return mixed
	 * @throws Exception
	 */
	public function getLineItemMeta(int $lineItemId, string $key, $default = NULL) {
		if(!function_exists("wc_get_order_item_meta")) {
			return $default;
		}

		return wc_get_order_item_meta($lineItemId, $key) ?: $default;
	}

	/**
	 * @param int $lineItemId
	 * @param string $metaKey
	 * @param $metaValue
	 * @return bool
	 * @throws Exception
	 */
	public function updateLineItemMeta(int $lineItemId, string $metaKey, $metaValue): bool {
		if(!function_exists("wc_update_order_item_meta")) {
			return FALSE;
		}

		return wc_update_order_item_meta($lineItemId, $metaKey, $metaValue);
	}

	/**
	 * @param int $orderId
	 * @return bool|WC_Order|WC_Order_Refund
	 */
	public function getOrder(int $orderId) {
		if(!function_exists("wc_get_order")) {
			return FALSE;
		}

		return wc_get_order($orderId);
	}
}
