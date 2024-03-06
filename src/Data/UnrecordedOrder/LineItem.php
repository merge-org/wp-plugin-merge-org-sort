<?php
declare(strict_types=1);

namespace MergeOrg\WpPluginSort\Data\UnrecordedOrder;

use JsonSerializable;

/**
 * @codeCoverageIgnore
 */
final class LineItem implements JsonSerializable {

	/**
	 * @var int
	 */
	private int $id;

	/**
	 * @var Product
	 */
	private Product $product;

	/**
	 * @var int
	 */
	private int $quantity;

	/**
	 * @var ProductVariation|null
	 */
	private ?ProductVariation $productVariation;

	/**
	 * @param int                   $id
	 * @param Product               $product
	 * @param int                   $quantity
	 * @param ProductVariation|null $productVariation
	 */
	public function __construct( int $id, Product $product, int $quantity, ?ProductVariation $productVariation = null ) {
		$this->id               = $id;
		$this->product          = $product;
		$this->quantity         = $quantity;
		$this->productVariation = $productVariation;
	}

	/**
	 * @return array<string, int|Product|ProductVariation|null>
	 */
	public function jsonSerialize(): array {
		return array(
			'id'               => $this->getId(),
			'product'          => $this->getProduct(),
			'quantity'         => $this->getQuantity(),
			'productVariation' => $this->getProductVariation(),
		);
	}

	/**
	 * @return int
	 */
	public function getId(): int {
		return $this->id;
	}

	/**
	 * @return Product
	 */
	public function getProduct(): Product {
		return $this->product;
	}

	/**
	 * @return int
	 */
	public function getQuantity(): int {
		return $this->quantity;
	}

	/**
	 * @return ProductVariation|null
	 */
	public function getProductVariation(): ?ProductVariation {
		return $this->productVariation;
	}
}
