<?php
declare(strict_types=1);

namespace MergeOrg\Sort\WordPress;

use MergeOrg\Sort\Service\Namer;
use MergeOrg\Sort\Data\WordPress\Order;
use MergeOrg\Sort\Data\WordPress\Product;
use MergeOrg\Sort\Data\WordPress\LineItem;
use MergeOrg\Sort\Data\WordPress\AbstractProduct;
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
	 * @param Namer $namer
	 */
	public function __construct(Namer $namer) {
		$this->namer = $namer;
	}

	/**
	 * @param int $productId
	 * @return AbstractProduct|null
	 * @throws InvalidKeyNameException
	 */
	public function getProduct(int $productId): ?AbstractProduct {
		if(!function_exists("wc_get_product")) {
			return NULL;
		}

		$product = wc_get_product($productId);
		if(!$product) {
			return NULL;
		}

		$sales = $this->getPostMeta($productId, $this->namer->getSalesMetaKeyName(), []);
		if($product->get_parent_id()) {
			return new Product($product->get_id(),
				$sales,
				$this->getProductIsExcludedFromSorting($product->get_id()),
				$this->getProductPreviousOrder($product->get_id()));
		}

		return new ProductVariation($product->get_id(), $sales);
	}

	/**
	 * @param int $postId
	 * @param string $metaKey
	 * @param $default
	 * @return mixed
	 */
	public function getPostMeta(int $postId, string $metaKey, $default = NULL) {
		if(!function_exists("get_post_meta")) {
			return $default;
		}

		return get_post_meta($postId, $metaKey, TRUE) ?: $default;
	}

	/**
	 * @param int $productId
	 * @return bool
	 * @throws InvalidKeyNameException
	 */
	public function getProductIsExcludedFromSorting(int $productId): bool {
		return $this->getPostMeta($productId, $this->namer->getExcludeFromSortingMetaKeyName(), "no") === "yes";
	}

	/**
	 * @throws InvalidKeyNameException
	 */
	public function getProductPreviousOrder(int $productId): int {
		return (int) $this->getPostMeta($productId, $this->namer->getPreviousOrderMetaKeyName(), "-1");
	}

	/**
	 * @param int $orderId
	 * @return Order|null
	 * @throws InvalidKeyNameException
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
			$lineItems[] =
				new LineItem($orderItemData["id"] ?? 0,
					$orderItemData["product_id"] ?? 0,
					$orderItemData["quantity"] ?? 0,
					$orderItemData["variation_id"] ?? 0);
		}

		return new Order($order->get_id(),
			$order->get_date_created()->format("Y-m-d"),
			$lineItems,
			$this->getOrderIsRecorded($order->get_id()));
	}

	/**
	 * @throws InvalidKeyNameException
	 */
	public function getOrderIsRecorded(int $orderId): bool {
		return $this->getPostMeta($orderId, $this->namer->getRecordedMetaKeyName(), "no") === "yes";
	}
}
