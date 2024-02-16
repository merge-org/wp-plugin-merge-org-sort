<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Data\Native;

use JsonSerializable;
use MergeOrg\Sort\Exception\InvalidSalesPeriodInProductConstructionException;
use MergeOrg\Sort\Exception\InvalidPeriodInDaysInSalesPeriodCreationException;

final class Product implements JsonSerializable {

	/**
	 * @var int
	 */
	private int $id;

	/**
	 * @var string
	 */
	private string $type;

	/**
	 * @var bool
	 */
	private bool $excludedFromSorting;

	/**
	 * @var int
	 */
	private int $previousMenuOrder;

	/**
	 * @var SalesPeriod[]
	 */
	private array $salesPeriods;

	/**
	 * @param int $id
	 * @param SalesPeriod[] $salesPeriods
	 * @param string $type
	 * @param bool $excludedFromSorting
	 * @param int $previousMenuOrder
	 * @throws InvalidSalesPeriodInProductConstructionException
	 */
	public function __construct(int $id,
		array $salesPeriods = [],
		string $type = "product",
		bool $excludedFromSorting = FALSE,
		int $previousMenuOrder = -1) {
		foreach($salesPeriods as $salesPeriod) {
			if(!is_object($salesPeriod) || get_class($salesPeriod) !== SalesPeriod::class) {
				throw new InvalidSalesPeriodInProductConstructionException();
			}
		}

		$this->id = $id;
		$this->salesPeriods = $salesPeriods;
		$this->type = $type;
		$this->excludedFromSorting = $excludedFromSorting;
		$this->previousMenuOrder = $previousMenuOrder;
	}

	/**
	 * @param int $periodInDays
	 * @param int $sales
	 * @param int $quantityBasedSales
	 * @return void
	 * @throws InvalidPeriodInDaysInSalesPeriodCreationException
	 */
	public function addSalesPeriod(int $periodInDays, int $sales, int $quantityBasedSales): void {
		foreach(($this->salesPeriods ?? []) as $salesPeriod) {
			if($salesPeriod->getPeriodInDays() === $periodInDays) {
				$salesPeriod->setSales($sales, $quantityBasedSales);

				return;
			}
		}

		$this->salesPeriods[] = new SalesPeriod($periodInDays, $sales, $quantityBasedSales);
	}

	/**
	 * @return array<string, int|string|SalesPeriod|bool>
	 */
	public function jsonSerialize(): array {
		return [
			"id" => $this->getId(),
			"type" => $this->getType(),
			"salesPeriods" => $this->getSalesPeriods(),
			"excludedFromSorting" => $this->getExcludedFromSorting(),
			"previousMenuOrder" => $this->getPreviousMenuOrder(),
		];
	}

	public function getId(): int {
		return $this->id;
	}

	public function getType(): string {
		return $this->type;
	}

	/**
	 * @return SalesPeriod[]
	 */
	public function getSalesPeriods(): array {
		return $this->salesPeriods;
	}

	public function getExcludedFromSorting(): bool {
		return $this->excludedFromSorting;
	}

	public function getPreviousMenuOrder(): int {
		return $this->previousMenuOrder;
	}

	/**
	 * @param int $periodInDays
	 * @return SalesPeriod|null
	 */
	public function getSalePeriodByPeriodInDays(int $periodInDays): ?SalesPeriod {
		foreach($this->getSalesPeriods() as $salesPeriod) {
			if($salesPeriod->getPeriodInDays() === $periodInDays) {
				return $salesPeriod;
			}
		}

		return NULL;
	}
}
