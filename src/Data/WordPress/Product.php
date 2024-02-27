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
	 * @var string
	 */
	private string $lastIndexUpdate;

	/**
	 * @param int                            $id
	 * @param array<string, array<int, int>> $sales
	 * @param bool                           $excludedFromSorting
	 * @param int                            $previousMenuOrder
	 * @param string                         $lastIndexUpdate
	 */
	public function __construct(
		int $id,
		array $sales = array(),
		bool $excludedFromSorting = false,
		int $previousMenuOrder = -1,
		string $lastIndexUpdate = '1970-01-01'
	) {
		parent::__construct( $id, $sales );
		$this->excludedFromSorting = $excludedFromSorting;
		$this->previousMenuOrder   = $previousMenuOrder;
		$this->lastIndexUpdate     = $lastIndexUpdate;
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

	/**
	 * @return string
	 */
	public function getLastIndexUpdate(): string {
		return $this->lastIndexUpdate;
	}
}
