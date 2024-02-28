<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Data;

use JsonSerializable;

final class LineItem implements JsonSerializable {

	/**
	 * @var int
	 */
	private int $id;

	/**
	 * @var int
	 */
	private int $productId;

	/**
	 * @var int
	 */
	private int $quantity;

	/**
	 * @var int
	 */
	private int $variationId;

	/**
	 * @param int $id
	 * @param int $productId
	 * @param int $variationId
	 * @param int $quantity
	 */
	public function __construct( int $id, int $productId, int $quantity, int $variationId = 0 ) {
		$this->id          = $id;
		$this->productId   = $productId;
		$this->quantity    = $quantity;
		$this->variationId = $variationId;
	}

	/**
	 * @return array<string, int>
	 */
	public function jsonSerialize(): array {
		return array(
			'id'          => $this->getId(),
			'productId'   => $this->getProductId(),
			'quantity'    => $this->getQuantity(),
			'variationId' => $this->getVariationId(),
		);
	}

	/**
	 * @return int
	 */
	public function getId(): int {
		return $this->id;
	}

	/**
	 * @return int
	 */
	public function getProductId(): int {
		return $this->productId;
	}

	/**
	 * @return int
	 */
	public function getQuantity(): int {
		return $this->quantity;
	}

	/**
	 * @return int
	 */
	public function getVariationId(): int {
		return $this->variationId;
	}
}
