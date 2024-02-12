<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Data\Native;

use JsonSerializable;
use MergeOrg\Sort\Constants;
use MergeOrg\Sort\Exception\InvalidPeriodInDaysInSalesPeriodCreationException;

final class SalesPeriod implements JsonSerializable {

	/**
	 * @var int
	 */
	private int $periodInDays;

	/**
	 * @var int
	 */
	private int $sales = 0;

	/**
	 * @var int
	 */
	private int $quantityBasedSales = 0;

	/**
	 * @param int $periodInDays
	 * @param int $sales
	 * @param int $quantityBasedSales
	 * @throws InvalidPeriodInDaysInSalesPeriodCreationException
	 */
	public function __construct(int $periodInDays, int $sales, int $quantityBasedSales) {
		if(!in_array($periodInDays, array_keys(Constants::SALES_PERIODS_IN_DAYS))) {
			throw new InvalidPeriodInDaysInSalesPeriodCreationException();
		}

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
	 * @param int $sales
	 * @param int $quantityBasedSales
	 * @return void
	 */
	public function setSales(int $sales, int $quantityBasedSales): void {
		$this->sales = $sales;
		$this->quantityBasedSales = $quantityBasedSales;
	}

	/**
	 * @return int
	 */
	public function getQuantityBasedSales(): int {
		return $this->quantityBasedSales;
	}
}
