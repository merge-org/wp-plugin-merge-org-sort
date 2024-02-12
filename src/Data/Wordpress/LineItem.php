<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Data\Wordpress;

final class LineItem {

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
	private int $variationId = 0;
	/**
	 * @var int
	 */
	private int $quantity = 1;

	/**
	 * @param int $id
	 * @param int $productId
	 * @param int $variationId
	 * @param int $quantity
	 */
	public function __construct(int $id, int $productId, int $variationId = 0, int $quantity = 1) {
		$this->id = $id;
		$this->productId = $productId;
		$this->variationId = $variationId;
		$this->quantity = $quantity;
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
	public function getVariationId(): int {
		return $this->variationId;
	}

	/**
	 * @return int
	 */
	public function getQuantity(): int {
		return $this->quantity;
	}
}