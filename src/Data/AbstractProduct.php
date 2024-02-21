<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Data;

use JsonSerializable;

/**
 * @internal
 */
abstract class AbstractProduct implements JsonSerializable {

	/**
	 * @var int
	 */
	protected int $id;

	/**
	 * @var SalesPeriod[]
	 */
	protected array $salesPeriods;

	/**
	 * @param int $id
	 * @param SalesPeriod[] $salesPeriods
	 */
	public function __construct(int $id, array $salesPeriods = []) {
		$this->id = $id;
		foreach($salesPeriods as $salesPeriod) {
			$this->addSalesPeriod($salesPeriod);
		}
	}

	/**
	 * @param SalesPeriod $salesPeriod
	 * @return void
	 */
	public function addSalesPeriod(SalesPeriod $salesPeriod) {
		$this->salesPeriods[$salesPeriod->getPeriodInDays()] = $salesPeriod;
	}

	/**
	 * @return array<string, int|string|array<SalesPeriod>>
	 */
	public function jsonSerialize(): array {
		return [
			"id" => $this->getId(),
			"type" => $this->getType(),
			"salesPeriods" => $this->getSalesPeriods(),
		];
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
	 * @return SalesPeriod[]
	 */
	public function getSalesPeriods(): array {
		return $this->salesPeriods;
	}
}
