<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Data;

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
	 * @param SalesPeriod[] $salesPeriods
	 * @param bool $excludedFromSorting
	 * @param int $previousMenuOrder
	 */
	public function __construct(int $id,
		array $salesPeriods = [],
		bool $excludedFromSorting = FALSE,
		int $previousMenuOrder = -1) {
		parent::__construct($id, $salesPeriods);
		$this->excludedFromSorting = $excludedFromSorting;
		$this->previousMenuOrder = $previousMenuOrder;
	}

	/**
	 * @return array<string, int|string|array<SalesPeriod>|bool|int>
	 */
	public function jsonSerialize(): array {
		$parentJson = parent::jsonSerialize();

		return array_merge($parentJson, [
			"excludedFromSorting" => $this->isExcludedFromSorting(),
			"previousMenuOrder" => $this->getPreviousMenuOrder(),
		]);
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
