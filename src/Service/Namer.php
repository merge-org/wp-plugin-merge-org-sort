<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service;

use MergeOrg\Sort\Constants;
use MergeOrg\Sort\Exception\InvalidKeyNameSortException;
use MergeOrg\Sort\Exception\InvalidPeriodInDaysSortException;

final class Namer {

	/**
	 * @var int[]
	 */
	private array $salesPeriodsInDays;

	/**
	 * @var string[]
	 */
	private array $salesPeriodsLabels;

	/**
	 * @var string[]
	 */
	private array $salesPeriodsSlugs;

	/**
	 * @return string
	 * @throws InvalidKeyNameSortException
	 */
	public function getSalesMetaKeyName(): string {
		return "{$this->getPluginName()}-{$this->getKeyNameFromConstants("sales")}";
	}

	/**
	 * @return string
	 */
	public function getPluginName(): string {
		return Constants::PLUGIN_NAME;
	}

	/**
	 * @param string $keyName
	 * @return string
	 * @throws InvalidKeyNameSortException
	 */
	private function getKeyNameFromConstants( string $keyName ): string {
		$keyName = strtoupper( $keyName );

		$keyName = constant( "\\MergeOrg\\Sort\\Constants::KEY_$keyName" );
		$this->validateKeyName( $keyName );

		return $keyName;
	}

	/**
	 * @param string $keyName
	 * @return void
	 * @throws InvalidKeyNameSortException
	 */
	public function validateKeyName( string $keyName ): void {
		$keyNameParts = explode( '-', $keyName );
		if ( count( $keyNameParts ) > 1 ) {
			throw new InvalidKeyNameSortException( "Invalid Meta Key Name: '$keyName'" );
		}
	}

	/**
	 * @param int $productId
	 * @return string
	 * @throws InvalidKeyNameSortException
	 */
	public function getProductCacheKey( int $productId ): string {
		$productCacheKey = "{$this->getPluginName()}-{$this->getKeyNameFromConstants("product_cache")}";

		return "$productCacheKey-$productId";
	}

	/**
	 * @return string
	 * @throws InvalidKeyNameSortException
	 */
	public function getServerLoadCacheKey(): string {
		return "{$this->getPluginName()}-{$this->getKeyNameFromConstants("server_load_cache")}";
	}

	/**
	 * @return string
	 * @throws InvalidKeyNameSortException
	 */
	public function getLastIndexUpdateMetaKeyName(): string {
		return "{$this->getPluginName()}-{$this->getKeyNameFromConstants("last_index_update")}";
	}

	/**
	 * @param int $index
	 * @return string
	 */
	public function getSalesPeriodSlugByIndex( int $index ): string {
		return $this->getSalesPeriodsSlugs()[ $index ];
	}

	/**
	 * @return string[]
	 */
	public function getSalesPeriodsSlugs(): array {
		if ( $this->salesPeriodsSlugs ?? null ) {
			return $this->salesPeriodsSlugs;
		}

		return $this->salesPeriodsSlugs = array_map(
			function ( $value ) {
				return $value[0];
			},
			array_values( Constants::SALES_PERIODS )
		);
	}

	/**
	 * @param int $periodInDays
	 * @return string
	 * @throws InvalidPeriodInDaysSortException
	 */
	public function getSalesPeriodLabelByPeriodInDays( int $periodInDays ): string {
		return $this->getSalesPeriodsLabels()[ $this->getSalesPeriodInDaysIndexByPeriodInDays( $periodInDays ) ];
	}

	/**
	 * @return string[]
	 */
	public function getSalesPeriodsLabels(): array {
		if ( $this->salesPeriodsLabels ?? null ) {
			return $this->salesPeriodsLabels;
		}

		return $this->salesPeriodsLabels = array_map(
			function ( $value ) {
				return $value[1];
			},
			array_values( Constants::SALES_PERIODS )
		);
	}

	/**
	 * @param int $periodInDays
	 * @return int
	 * @throws InvalidPeriodInDaysSortException
	 */
	public function getSalesPeriodInDaysIndexByPeriodInDays( int $periodInDays ): int {
		foreach ( $this->getSalesPeriodsInDays() as $index => $periodInDays_ ) {
			if ( $periodInDays === $periodInDays_ ) {
				return $index;
			}
		}

		throw new InvalidPeriodInDaysSortException( "Invalid Period In Days: '$periodInDays'" );
	}

	/**
	 * @return int[]
	 */
	public function getSalesPeriodsInDays(): array {
		if ( $this->salesPeriodsInDays ?? null ) {
			return $this->salesPeriodsInDays;
		}

		return $this->salesPeriodsInDays = array_keys( Constants::SALES_PERIODS );
	}

	/**
	 * @param int $index
	 * @return string
	 */
	public function getSalesPeriodLabelByIndex( int $index ): string {
		return $this->getSalesPeriodsLabels()[ $index ];
	}

	/**
	 * @return string
	 * @throws InvalidKeyNameSortException
	 */
	public function getExcludeFromSortingMetaKeyName(): string {
		return "{$this->getPluginName()}-{$this->getKeyNameFromConstants("exclude_from_sorting")}";
	}

	/**
	 * @return string
	 * @throws InvalidKeyNameSortException
	 */
	public function getPreviousOrderMetaKeyName(): string {
		return "{$this->getPluginName()}-{$this->getKeyNameFromConstants("previous_order")}";
	}

	/**
	 * @param bool $hidden
	 * @return string
	 * @throws InvalidKeyNameSortException
	 */
	public function getRecordedMetaKeyName( bool $hidden = true ): string {
		$prefix = '';
		$hidden && ( $prefix = '_' );
		return "$prefix{$this->getPluginName()}-{$this->getKeyNameFromConstants("recorded")}";
	}

	/**
	 * @param int $periodInDays
	 * @return string
	 * @throws InvalidKeyNameSortException
	 * @throws InvalidPeriodInDaysSortException
	 */
	public function getPeriodInDaysColumnName( int $periodInDays ): string {
		// TODO VALIDATE `periodInDays`
		$salesPeriodSlug = $this->getSalesPeriodSlugByPeriodInDays( $periodInDays );

		return "{$this->getPluginName()}-{$this->getKeyNameFromConstants("period_in_days")}-$salesPeriodSlug";
	}

	/**
	 * @param int $periodInDays
	 * @return string
	 * @throws InvalidPeriodInDaysSortException
	 */
	public function getSalesPeriodSlugByPeriodInDays( int $periodInDays ): string {
		return $this->getSalesPeriodsSlugs()[ $this->getSalesPeriodInDaysIndexByPeriodInDays( $periodInDays ) ];
	}

	/**
	 * @param int $periodInDays
	 * @return string
	 * @throws InvalidKeyNameSortException
	 */
	public function getPeriodInDaysMetaKeyName( int $periodInDays ): string {
		// TODO VALIDATE `periodInDays`
		return "{$this->getPluginName()}-{$this->getKeyNameFromConstants("period_in_days")}-$periodInDays";
	}

	/**
	 * @param int $periodInDays
	 * @return string
	 * @throws InvalidKeyNameSortException
	 */
	public function getPeriodInDaysQuantityMetaKeyName( int $periodInDays ): string {
		// TODO VALIDATE `periodInDays`
		return "{$this->getPluginName()}-{$this->getKeyNameFromConstants("period_in_days_quantity")}-$periodInDays";
	}
}
