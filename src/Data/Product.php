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
	 * @var string
	 */
	private string $lastIndexUpdate;

	/**
	 * @param int           $id
	 * @param SalesPeriod[] $salesPeriods
	 * @param bool          $excludedFromSorting
	 * @param int           $previousMenuOrder
	 * @param string        $lastIndexUpdate
	 */
	public function __construct(
		int $id,
		array $salesPeriods,
		bool $excludedFromSorting,
		int $previousMenuOrder,
		string $lastIndexUpdate = '1970-01-01'
	) {
		parent::__construct( $id, $salesPeriods );
		$this->excludedFromSorting = $excludedFromSorting;
		$this->previousMenuOrder   = $previousMenuOrder;
		$this->lastIndexUpdate     = $lastIndexUpdate;
	}

	/**
	 * @return array<string, int|string|array<SalesPeriod>|bool|int>
	 */
	public function jsonSerialize(): array {
		$parentJson = parent::jsonSerialize();

		return array_merge(
			$parentJson,
			array(
				'excludedFromSorting' => $this->isExcludedFromSorting(),
				'previousMenuOrder'   => $this->getPreviousMenuOrder(),
				'lastIndexUpdate'     => $this->getLastIndexUpdate(),
			)
		);
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
	public function getLastIndexUpdate(): string {
		return $this->lastIndexUpdate;
	}

	/**
	 * @return string
	 */
	function getType(): string {
		return Constants::POST_TYPE_PRODUCT;
	}
}
