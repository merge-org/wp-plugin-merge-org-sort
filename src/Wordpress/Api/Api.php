<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Wordpress\Api;

use Exception;
use MergeOrg\Sort\Constants;
use MergeOrg\Sort\Data\Wordpress\Order;
use MergeOrg\Sort\Data\Wordpress\Product;
use MergeOrg\Sort\Data\Wordpress\LineItem;
use MergeOrg\Sort\Exception\InvalidLineItemInOrderCreationException;

final class Api implements ApiInterface {

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
	 * @return ?Order
	 * @throws InvalidLineItemInOrderCreationException
	 */
	public function getOrder(int $orderId): ?Order {
		if(!function_exists("wc_get_order")) {
			return NULL;
		}

		$order = wc_get_order($orderId);
		if(!$order) {
			return NULL;
		}

		$lineItems = [];
		foreach($order->get_items() as $orderItem) {
			$orderItemData = $orderItem->get_data();
			$lineItems[] = new LineItem($orderItem->get_id(), $orderItemData["product_id"],
				$orderItemData["variation_id"] ?? 0, $orderItemData["quantity"]);
		}

		return new Order($orderId, $lineItems, $this->getOrderRecorded($orderId));
	}

	/**
	 * @param int $orderId
	 * @return bool
	 */
	public function getOrderRecorded(int $orderId): bool {
		return $this->getPostMeta($orderId, Constants::META_FIELD_ORDER_RECORDED, "no") === "yes";
	}

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
	 * @param string $option
	 * @param mixed $default
	 * @return mixed
	 */
	public function getOption(string $option, $default = NULL) {
		return get_option($option, $default);
	}

	/**
	 * @param string $option
	 * @param mixed $value
	 * @return bool
	 */
	public function updateOption(string $option, $value): bool {
		return update_option($option, $value, FALSE);
	}

	/**
	 * @param int $productId
	 * @return ?Product
	 */
	public function getProduct(int $productId): ?Product {
		if(!function_exists("wc_get_product")) {
			return NULL;
		}

		$product = wc_get_product($productId);
		if(!$product) {
			return NULL;
		}

		return new Product($productId,
			$product->get_parent_id() ? Constants::POST_TYPE_PRODUCT_VARIATION : Constants::POST_TYPE_PRODUCT,
			$this->getProductSales($productId),
			$this->getProductIsExcludedFromSorting($productId),
			$this->getProductPreviousOrder($productId));
	}

	/**
	 * @param int $productId
	 * @return array<string, array<int>>
	 */
	public function getProductSales(int $productId): array {
		return $this->getPostMeta($productId, Constants::META_FIELD_PRODUCT_SALES, []);
	}

	/**
	 * @param int $productId
	 * @return bool
	 */
	public function getProductIsExcludedFromSorting(int $productId): bool {
		return $this->getPostMeta($productId, Constants::META_FIELD_PRODUCT_EXCLUDE_FROM_SORTING, "no") === "yes";
	}

	/**
	 * @param int $productId
	 * @return int
	 */
	public function getProductPreviousOrder(int $productId): int {
		return (int) $this->getPostMeta($productId, Constants::META_FIELD_PRODUCT_PREVIOUS_ORDER, "-1");
	}

	/**
	 * @param int $orderId
	 * @return bool
	 */
	public function setOrderRecorded(int $orderId): bool {
		return $this->updatePostMeta($orderId, Constants::META_FIELD_ORDER_RECORDED, "yes");
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
	 * @param int $productId
	 * @param array<string, array<int>> $sales
	 * @return bool
	 */
	public function setProductSales(int $productId, array $sales): bool {
		return $this->updatePostMeta($productId, Constants::META_FIELD_PRODUCT_SALES, $sales);
	}
}
