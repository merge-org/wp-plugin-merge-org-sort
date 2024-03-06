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
	public const SALES_PURCHASE_KEY = 'purchase';

	/**
	 *
	 */
	public const SALES_QUANTITY_KEY = 'quantity';

	/**
	 * @return string
	 */
	public function getRecordedMetaKey(): string {
		return $this->normalizeMetaKey( self::META_KEY_RECORDED );
	}

	/**
	 * @param string $metaKey
	 * @param bool   $hidden
	 *
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
	public function getRecordedDateTimeMetaKey(): string {
		return $this->normalizeMetaKey( self::META_KEY_RECORDED_DATE_TIME );
	}
}
