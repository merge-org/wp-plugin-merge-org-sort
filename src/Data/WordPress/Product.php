<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Data\WordPress;

use MergeOrg\Sort\Constants;

final class Product extends AbstractProduct {

	/**
	 * @var bool
	 */
	private bool $excludedFromSorting;

	/**
	 * @var int
	 */
	private int $previousMenuOrder;

	/**
	 * @param int $id
	 * @param array<string, array<int, int>> $sales
	 * @param bool $excludedFromSorting
	 * @param int $previousMenuOrder
	 */
	public function __construct(int $id,
		array $sales = [],
		bool $excludedFromSorting = FALSE,
		int $previousMenuOrder = -1) {
		parent::__construct($id, $sales);
		$this->excludedFromSorting = $excludedFromSorting;
		$this->previousMenuOrder = $previousMenuOrder;
	}

	/**
	 * @return bool
	 */
	public function isExcludedFromSorting(): bool {
		return $this->excludedFromSorting;
	}

	/**
	 * @return int
	 */
	public function getPreviousMenuOrder(): int {
		return $this->previousMenuOrder;
	}

	/**
	 * @return string
	 */
	function getType(): string {
		return Constants::POST_TYPE_PRODUCT;
	}
}
