<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Data;

use JsonSerializable;

/**
 * Class Sales
 *
 * @package MergeOrg\Data
 */
final class Product implements JsonSerializable {

	/**
	 * @var int
	 */
	private int $productId;

	/**
	 * @var int
	 */
	private int $dailySales;

	/**
	 * @var int
	 */
	private int $weeklySales;

	/**
	 * @var int
	 */
	private int $semiMonthlySales;

	/**
	 * @var int
	 */
	private int $monthlySales;

	/**
	 * @var int
	 */
	private int $quarterlySales;

	/**
	 * @var int
	 */
	private int $semiAnnualSales;

	/**
	 * @var int
	 */
	private int $yearlySales;

	/**
	 * @var int
	 */
	private int $previousOrder;

	/**
	 * @var bool
	 */
	private bool $excludeFromSorting;

	/**
	 * @param int $productId
	 * @param int $dailySales
	 * @param int $weeklySales
	 * @param int $semiMonthlySales
	 * @param int $monthlySales
	 * @param int $quarterlySales
	 * @param int $semiAnnualSales
	 * @param int $yearlySales
	 * @param int $previousOrder
	 * @param bool $excludeFromSorting
	 */
	public function __construct(int $productId,
		int $dailySales,
		int $weeklySales,
		int $semiMonthlySales,
		int $monthlySales,
		int $quarterlySales,
		int $semiAnnualSales,
		int $yearlySales,
		int $previousOrder,
		bool $excludeFromSorting) {
		$this->productId = $productId;
		$this->dailySales = $dailySales;
		$this->weeklySales = $weeklySales;
		$this->semiMonthlySales = $semiMonthlySales;
		$this->monthlySales = $monthlySales;
		$this->quarterlySales = $quarterlySales;
		$this->semiAnnualSales = $semiAnnualSales;
		$this->yearlySales = $yearlySales;
		$this->previousOrder = $previousOrder;
		$this->excludeFromSorting = $excludeFromSorting;
	}

	/**
	 * @return array<string, bool|int>
	 */
	public function jsonSerialize(): array {
		return [
			"productId" => $this->getProductId(),
			"dailySales" => $this->getDailySales(),
			"weeklySales" => $this->getWeeklySales(),
			"semiMonthlySales" => $this->getSemiMonthlySales(),
			"monthlySales" => $this->getMonthlySales(),
			"quarterlySales" => $this->getQuarterlySales(),
			"semiAnnualSales" => $this->getSemiAnnualSales(),
			"yearlySales" => $this->getYearlySales(),
			"previousOrder" => $this->getPreviousOrder(),
			"excludeFromSorting" => $this->isExcludedFromSorting(),
		];
	}

	/**
	 * @return int
	 */
	public function getProductId(): int {
		return $this->productId;
	}

	/**
	 * @return int
	 */
	public function getDailySales(): int {
		return $this->dailySales;
	}

	/**
	 * @return int
	 */
	public function getWeeklySales(): int {
		return $this->weeklySales;
	}

	/**
	 * @return int
	 */
	public function getSemiMonthlySales(): int {
		return $this->semiMonthlySales;
	}

	/**
	 * @return int
	 */
	public function getMonthlySales(): int {
		return $this->monthlySales;
	}

	/**
	 * @return int
	 */
	public function getQuarterlySales(): int {
		return $this->quarterlySales;
	}

	/**
	 * @return int
	 */
	public function getSemiAnnualSales(): int {
		return $this->semiAnnualSales;
	}

	/**
	 * @return int
	 */
	public function getYearlySales(): int {
		return $this->yearlySales;
	}

	/**
	 * @return int
	 */
	public function getPreviousOrder(): int {
		return $this->previousOrder;
	}

	/**
	 * @return bool
	 */
	public function isExcludedFromSorting(): bool {
		return $this->excludeFromSorting;
	}
}
