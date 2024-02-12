<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Wordpress\Api;

use Exception;
use MergeOrg\Sort\Constants;
use MergeOrg\Sort\Data\Wordpress\Order;
use MergeOrg\Sort\Data\Wordpress\Product;
use MergeOrg\Sort\Data\Wordpress\LineItem;
use MergeOrg\Sort\Service\IntegerEncoder\IntegerEncoderInterface;
use MergeOrg\Sort\Exception\InvalidInputForIntegerEncoderException;
use MergeOrg\Sort\Exception\InvalidLineItemInOrderCreationException;

final class Api implements ApiInterface {

	/**
	 * @var IntegerEncoderInterface
	 */
	private IntegerEncoderInterface $integerEncoder;

	/**
	 * @param IntegerEncoderInterface $integerEncoder
	 */
	public function __construct(IntegerEncoderInterface $integerEncoder) {
		$this->integerEncoder = $integerEncoder;
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

		return new Order($orderId, $lineItems);
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
	 * @throws InvalidInputForIntegerEncoderException
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
	 * @throws InvalidInputForIntegerEncoderException
	 */
	public function getProductSales(int $productId): array {
		$sales = $this->getPostMeta($productId, Constants::META_FIELD_SALES, []);
		$sales_ = [];
		foreach($sales as $date => $dateSales) {
			$sales_[$date] = [
				$this->integerEncoder->decode($dateSales[0]),
				$this->integerEncoder->decode($dateSales[1]),
			];
		}

		return $sales_;
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
	 * @param int $productId
	 * @return bool
	 */
	public function getProductIsExcludedFromSorting(int $productId): bool {
		return $this->getPostMeta($productId, Constants::META_FIELD_EXCLUDE_FROM_SORTING, "no") === "yes";
	}

	/**
	 * @param int $productId
	 * @return int
	 */
	public function getProductPreviousOrder(int $productId): int {
		return (int) $this->getPostMeta($productId, Constants::META_FIELD_PREVIOUS_ORDER, "-1");
	}

	/**
	 * @param int $productId
	 * @param array<string, array<int>> $sales
	 * @return bool
	 */
	public function setProductSales(int $productId, array $sales): bool {
		$sales_ = [];
		foreach($sales as $date => $dateSales) {
			$sales_[$date] = [
				$this->integerEncoder->encode($dateSales[0]),
				$this->integerEncoder->encode($dateSales[1]),
			];
		}

		return $this->updatePostMeta($productId, Constants::META_FIELD_SALES, $sales_);
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
}
