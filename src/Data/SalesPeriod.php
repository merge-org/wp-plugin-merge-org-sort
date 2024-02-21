<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Data;

use JsonSerializable;

final class SalesPeriod implements JsonSerializable {

	/**
	 * @var int
	 */
	private int $periodInDays;

	/**
	 * @var int
	 */
	private int $sales;

	/**
	 * @var int
	 */
	private int $quantityBasedSales;

	/**
	 * @param int $periodInDays
	 * @param int $sales
	 * @param int $quantityBasedSales
	 */
	public function __construct(int $periodInDays, int $sales, int $quantityBasedSales) {
		$this->periodInDays = $periodInDays;
		$this->sales = $sales;
		$this->quantityBasedSales = $quantityBasedSales;
	}

	/**
	 * @return array<string, int>
	 */
	public function jsonSerialize(): array {
		return [
			"periodInDays" => $this->getPeriodInDays(),
			"sales" => $this->getSales(),
			"quantityBasedSales" => $this->getQuantityBasedSales(),
		];
	}

	/**
	 * @return int
	 */
	public function getPeriodInDays(): int {
		return $this->periodInDays;
	}

	/**
	 * @return int
	 */
	public function getSales(): int {
		return $this->sales;
	}

	/**
	 * @return int
	 */
	public function getQuantityBasedSales(): int {
		return $this->quantityBasedSales;
	}
}
