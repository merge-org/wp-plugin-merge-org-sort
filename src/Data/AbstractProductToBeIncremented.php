<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Data;

use MergeOrg\Sort\Service\Namer;
use MergeOrg\Sort\Exception\InvalidKeyNameException;

/**
 * Class AbstractProductToBeIncremented
 *
 * @package MergeOrg\Sort\Data
 * @internal
 */
abstract class AbstractProductToBeIncremented {

	/**
	 * @var int
	 */
	private int $id;

	/**
	 * @var array<string, array<int, int>>
	 */
	private array $salesToBeUpdated;

	/**
	 * @var SalesPeriod[]
	 */
	private array $salesPeriods;

	/**
	 * @param int $id
	 * @param array<string, array<int, int>> $salesToBeUpdated
	 * @param SalesPeriod[] $salesPeriods
	 */
	public function __construct(int $id, array $salesToBeUpdated, array $salesPeriods = []) {
		$this->id = $id;
		$this->salesToBeUpdated = $salesToBeUpdated;
		$this->salesPeriods = $salesPeriods;
	}

	/**
	 * @return int
	 */
	public function getId(): int {
		return $this->id;
	}

	/**
	 * @return string
	 */
	abstract function getType(): string;

	/**
	 * @param Namer $namer
	 * @return array<string, array<string, array<int,int>|string|int>>
	 * @throws InvalidKeyNameException
	 */
	public function generateMetaKeys(Namer $namer): array {
		$metaKeys = [$namer->getSalesMetaKeyName() => $this->getSalesToBeUpdated()];
		foreach($this->getSalesPeriods() as $salesPeriod) {
			$metaKeys[$namer->getPeriodInDaysMetaKeyName($salesPeriod->getPeriodInDays())] = $salesPeriod->getSales();
			$metaKeys[$namer->getPeriodInDaysQuantityMetaKeyName($salesPeriod->getPeriodInDays())] =
				$salesPeriod->getQuantityBasedSales();
		}

		return $metaKeys;
	}

	/**
	 * @return array<string, array<int, int>>
	 */
	public function getSalesToBeUpdated(): array {
		return $this->salesToBeUpdated;
	}

	/**
	 * @return SalesPeriod[]
	 */
	public function getSalesPeriods(): array {
		return $this->salesPeriods;
	}
}
