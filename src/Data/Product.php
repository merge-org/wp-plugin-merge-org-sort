<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Data;

use MergeOrg\Sort\Constants;
use MergeOrg\Sort\Service\Namer;
use MergeOrg\Sort\Exception\InvalidKeyNameException;

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
	 * @param int                           $id
	 * @param array<string, array<int,int>> $sales
	 * @param SalesPeriod[]                 $salesPeriods
	 * @param bool                          $excludedFromSorting
	 * @param int                           $previousMenuOrder
	 * @param string                        $lastIndexUpdate
	 */
	public function __construct(
		int $id,
		array $sales,
		array $salesPeriods,
		bool $excludedFromSorting,
		int $previousMenuOrder,
		string $lastIndexUpdate = '1970-01-01'
	) {
		parent::__construct( $id, $sales, $salesPeriods );
		$this->excludedFromSorting = $excludedFromSorting;
		$this->previousMenuOrder   = $previousMenuOrder;
		$this->lastIndexUpdate     = $lastIndexUpdate;
	}

	/**
	 * @return array<string, int|string|array<SalesPeriod>|bool|int>
	 * @throws InvalidKeyNameException
	 */
	public function jsonSerialize(): array {
		$parentJson = parent::jsonSerialize();

		return array_merge(
			$parentJson,
			array(
				'excludedFromSorting' => $this->isExcludedFromSorting(),
				'previousMenuOrder'   => $this->getPreviousMenuOrder(),
				'lastIndexUpdate'     => $this->getLastIndexUpdate(),
				'indexesMetaKeys'     => $this->getIndexesMetaKeys( new Namer() ),
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
	 * @param Namer $namer
	 * @return array<string, int>
	 * @throws InvalidKeyNameException
	 */
	public function getIndexesMetaKeys( Namer $namer ) {
		$indexes = array();
		foreach ( $this->getSalesPeriods() as $salesPeriod ) {
			$indexes[ $namer->getPeriodInDaysMetaKeyName( $salesPeriod->getPeriodInDays() ) ] = $salesPeriod->getSales();
		}

		return $indexes;
	}

	/**
	 * @return string
	 */
	public function getType(): string {
		return Constants::POST_TYPE_PRODUCT;
	}
}
