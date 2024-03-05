<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Data\WordPress;

use DateTime;
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
	 * @var DateTime
	 */
	private DateTime $lastIndexUpdate;

	/**
	 * @param int                            $id
	 * @param array<string, array<int, int>> $sales
	 * @param bool                           $excludedFromSorting
	 * @param int                            $previousMenuOrder
	 * @param DateTime                       $lastIndexUpdate
	 */
	public function __construct(
		int $id,
		array $sales,
		bool $excludedFromSorting,
		int $previousMenuOrder,
		DateTime $lastIndexUpdate
	) {
		parent::__construct( $id, $sales );
		$this->excludedFromSorting = $excludedFromSorting;
		$this->previousMenuOrder   = $previousMenuOrder;
		$this->lastIndexUpdate     = $lastIndexUpdate;
	}

	/**
	 * @return string
	 */
	function getType(): string {
		return Constants::POST_TYPE_PRODUCT;
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
	 * @return DateTime
	 */
	public function getLastIndexUpdate(): DateTime {
		return $this->lastIndexUpdate;
	}
}
