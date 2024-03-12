<?php
declare(strict_types=1);

namespace MergeOrg\WpPluginSort;

final class Constants {

	/**
	 *
	 */
	private const META_KEY_RECORDED = 'recorded';

	/**
	 *
	 */
	private const META_KEY_RECORDED_DATE_TIME = 'recorded_date_time';

	/**
	 *
	 */
	private const META_KEY_SALES = 'sales';

	/**
	 *
	 */
	private const META_KEY_SALES_PERIODS_LAST_UPDATE = 'sales_periods_last_update';

	/**
	 *
	 */
	private const META_KEY_SALES_PERIOD_PURCHASE = 'sales_period_purchase';

	/**
	 *
	 */
	private const META_KEY_SALES_PERIOD_QUANTITY = 'sales_period_quantity';

	/**
	 *
	 */
	private const SALES_PERIODS = array(
		1   => array(
			'daily',
			'Daily',
		),
		7   => array(
			'weekly',
			'Weekly',
		),
		15  => array(
			'semi_monthly',
			'Semi-Monthly',
		),
		30  => array(
			'monthly',
			'Monthly',
		),
		90  => array(
			'quarterly',
			'Quarterly',
		),
		180 => array(
			'semi_annually',
			'Semi-Annually',
		),
		365 => array(
			'yearly',
			'Yearly',
		),
	);

	/**
	 *
	 */
	public const SALES_PURCHASE_KEY = 'purchase';

	/**
	 *
	 */
	public const SALES_QUANTITY_KEY = 'quantity';

	/**
	 * @return string
	 */
	public function getRecordedMetaKey(): string {
		return $this->normalizeMetaKey( self::META_KEY_RECORDED, true );
	}

	/**
	 * @param string $metaKey
	 * @param bool   $hidden
	 * @return string
	 */
	private function normalizeMetaKey( string $metaKey, bool $hidden = false ): string {
		$prefix = '';
		$hidden && ( $prefix = '_' );

		return "{$prefix}merge-org-sort-$metaKey";
	}

	/**
	 * @return string
	 */
	public function getSalesMetaKey(): string {
		return $this->normalizeMetaKey( self::META_KEY_SALES );
	}

	/**
	 * @return string
	 */
	public function getSalesPeriodsLastUpdateMetaKey(): string {
		return $this->normalizeMetaKey( self::META_KEY_SALES_PERIODS_LAST_UPDATE );
	}

	/**
	 * @return string
	 */
	public function getRecordedDateTimeMetaKey(): string {
		return $this->normalizeMetaKey( self::META_KEY_RECORDED_DATE_TIME, true );
	}

	/**
	 * @return array<string, string>
	 */
	public function getProductColumns(): array {
		$productColumns = array();
		foreach ( $this->getSalesPeriodDays() as $salesPeriodDay ) {
			if ( $salesPeriodDay === 1 ) {
				continue;
			}

			$productColumns[ $this->getSalesPeriodPurchaseMetaKey( $salesPeriodDay ) ] = self::SALES_PERIODS[ $salesPeriodDay ][1];
		}

		return $productColumns;
	}

	/**
	 * @return int[]
	 */
	public function getSalesPeriodDays(): array {
		return array_keys( self::SALES_PERIODS );
	}

	/**
	 * @param int $days
	 * @return string
	 */
	public function getSalesPeriodPurchaseMetaKey( int $days ): string {
		$metaKey = $this->normalizeMetaKey( self::META_KEY_SALES_PERIOD_PURCHASE );

		return "$metaKey-$days";
	}

	/**
	 * @return array<string, string>
	 */
	public function getProductColumnsForSorting(): array {
		$productColumns = array();
		foreach ( $this->getSalesPeriodDays() as $salesPeriodDay ) {
			if ( $salesPeriodDay === 1 ) {
				continue;
			}

			$productColumns[ $this->getSalesPeriodPurchaseMetaKey( $salesPeriodDay ) ] =
				$this->getSalesPeriodPurchaseMetaKey( $salesPeriodDay );
		}

		return $productColumns;
	}

	/**
	 * @param int $days
	 * @return string
	 */
	public function getSalesPeriodQuantityMetaKey( int $days ): string {
		$metaKey = $this->normalizeMetaKey( self::META_KEY_SALES_PERIOD_QUANTITY );

		return "$metaKey-$days";
	}
}
