<?php
declare(strict_types=1);

namespace MergeOrg\WpPluginSort\Data\Sort;

use JsonSerializable;

/**
 * Class Product
 *
 * @package MergeOrg\WpPluginSort\Data\NonUpdatedSalesPeriodsProduct
 * @codeCoverageIgnore
 */
final class Product implements JsonSerializable {

	/**
	 * @var int
	 */
	private int $id;

	/**
	 * @var SalesPeriod[]
	 */
	private array $salesPeriods;

	/**
	 * @param int           $id
	 * @param SalesPeriod[] $salesPeriods
	 */
	public function __construct( int $id, array $salesPeriods ) {
		$this->id           = $id;
		$this->salesPeriods = $salesPeriods;
	}

	/**
	 * @return array<string, int|SalesPeriod[]>
	 */
	public function jsonSerialize(): array {
		return array(
			'id'           => $this->getId(),
			'salesPeriods' => $this->getSalesPeriods(),
		);
	}

	/**
	 * @return int
	 */
	public function getId(): int {
		return $this->id;
	}

	/**
	 * @return SalesPeriod[]
	 */
	public function getSalesPeriods(): array {
		return $this->salesPeriods;
	}
}
