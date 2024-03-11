<?php
declare(strict_types=1);

namespace MergeOrg\WpPluginSort\Service;

use MergeOrg\WpPluginSort\Constants;
use MergeOrg\WpPluginSort\Data\NonUpdatedSalesPeriodsProduct\SalesPeriod;

final class SalesPeriodManager {

	/**
	 * @var Constants
	 */
	private Constants $constants;

	/**
	 * @param Constants $constants
	 */
	public function __construct( Constants $constants ) {
		$this->constants = $constants;
	}

	/**
	 * @param array<string, array<string, int>> $sales
	 * @return SalesPeriod[]
	 */
	public function getAllSalesPeriods( array $sales ): array {
		$salesPeriods = array();
		foreach ( $this->constants->getSalesPeriodDays() as $days ) {
			$salesPeriods[] = $this->getSalesPeriodFromDays( $sales, $days );
		}

		return $salesPeriods;
	}

	/**
	 * @param array<string, array<string, int>> $sales
	 * @param int                               $days
	 * @return SalesPeriod
	 */
	private function getSalesPeriodFromDays( array $sales, int $days ): SalesPeriod {
		$today              = date( 'Y-m-d' );
		$furthestDateInPast = date( 'Y-m-d', strtotime( "-$days days" ) );

		$purchaseSales = 0;
		$quantitySales = 0;
		foreach ( $sales as $date => $dailySales ) {
			if ( $date === $today && $days === 1 ) {
				$purchaseSales = $dailySales[ Constants::SALES_PURCHASE_KEY ];
				$quantitySales = $dailySales[ Constants::SALES_QUANTITY_KEY ];
				break;
			}

			if ( $date < $furthestDateInPast || $date > $today ) {
				continue;
			}

			$purchaseSales += $dailySales[ Constants::SALES_PURCHASE_KEY ];
			$quantitySales += $dailySales[ Constants::SALES_QUANTITY_KEY ];
		}

		return new SalesPeriod( $days, $purchaseSales, $quantitySales );
	}
}
