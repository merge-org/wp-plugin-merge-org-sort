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
	 * @var array<string, array<int,int>>
	 */
	protected array $sales;

	/**
	 * @var SalesPeriod[]
	 */
	protected array $salesPeriods;

	/**
	 * @param int                           $id
	 * @param array<string, array<int,int>> $sales
	 * @param SalesPeriod[]                 $salesPeriods
	 */
	public function __construct( int $id, array $sales, array $salesPeriods ) {
		$this->id    = $id;
		$this->sales = $sales;
		foreach ( $salesPeriods as $salesPeriod ) {
			$this->addSalesPeriod( $salesPeriod );
		}
	}

	/**
	 * @param SalesPeriod $salesPeriod
	 * @return void
	 */
	public function addSalesPeriod( SalesPeriod $salesPeriod ) {
		$this->salesPeriods[ $salesPeriod->getPeriodInDays() ] = $salesPeriod;
	}

	/**
	 * @return array<string, int|string|array<SalesPeriod>|array<string, array<int,int>>>
	 */
	public function jsonSerialize(): array {
		return array(
			'id'           => $this->getId(),
			'type'         => $this->getType(),
			'sales'        => $this->getSales(),
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
	 * @return string
	 */
	abstract function getType(): string;

	/**
	 * @return array<string, array<int,int>>
	 */
	public function getSales(): array {
		return $this->sales;
	}

	/**
	 * @return SalesPeriod[]
	 */
	public function getSalesPeriods(): array {
		return $this->salesPeriods;
	}
}
