<?php
declare(strict_types=1);

namespace MergeOrg\Sort\WordPress;

use MergeOrg\Sort\Service\Namer;
use MergeOrg\Sort\Data\WordPress\Product;
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
		if(function_exists("wc_get_product")) {
			$product = wc_get_product($productId);
			if(!$product) {
				return NULL;
			}

			$sales = $this->getPostMeta($productId, $this->namer->getSalesMetaKeyName(), []);
			if($product->get_parent_id()) {
				return new Product($productId,
					$sales,
					$this->getProductIsExcludedFromSorting($productId),
					$this->getProductPreviousOrder($productId));
			}

			return new ProductVariation($productId, $sales);
		}

		return NULL;
	}

	/**
	 * @param int $postId
	 * @param string $metaKey
	 * @param $default
	 * @return mixed
	 */
	public function getPostMeta(int $postId, string $metaKey, $default) {
		if(function_exists("get_post_meta")) {
			return get_post_meta($postId, $metaKey, TRUE) ?: $default;
		}

		return $default;
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
}
