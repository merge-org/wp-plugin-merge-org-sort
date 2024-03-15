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
			'slug' => 'daily',
		),
		7   => array(
			'slug' => 'weekly',
		),
		15  => array(
			'slug' => 'semi_monthly',
		),
		30  => array(
			'slug' => 'monthly',
		),
		90  => array(
			'slug' => 'quarterly',
		),
		180 => array(
			'slug' => 'semi_annually',
		),
		365 => array(
			'slug' => 'yearly',
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

			$productColumns[ $this->getSalesPeriodPurchaseMetaKey( $salesPeriodDay ) ] =
				$this->getTranslatedProductColumnBySlug( self::SALES_PERIODS[ $salesPeriodDay ]['slug'] );
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
	 * @param string $slug
	 * @return string
	 */
	private function getTranslatedProductColumnBySlug( string $slug ): string {
		$labels = array(
			'daily'         => __( 'Daily', 'merge-org-sort' ),
			'weekly'        => __( 'Weekly', 'merge-org-sort' ),
			'semi_monthly'  => __( 'Semi-Monthly', 'merge-org-sort' ),
			'monthly'       => __( 'Monthly', 'merge-org-sort' ),
			'quarterly'     => __( 'Quarterly', 'merge-org-sort' ),
			'semi_annually' => __( 'Semi-Annually', 'merge-org-sort' ),
			'yearly'        => __( 'Yearly', 'merge-org-sort' ),
		);

		return $labels[ $slug ] ?? '';
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
