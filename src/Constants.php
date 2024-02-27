<?php
declare(strict_types=1);

namespace MergeOrg\Sort;

final class Constants {

	/**
	 *
	 */
	public const PLUGIN_NAME = 'merge-org-sort';

	/**
	 *
	 */
	public const POST_TYPE_PRODUCT = 'product';

	/**
	 *
	 */
	public const POST_TYPE_PRODUCT_VARIATION = 'product_variation';

	/**
	 *
	 */
	public const KEY_SALES = 'sales';

	/**
	 *
	 */
	public const KEY_EXCLUDE_FROM_SORTING = 'exclude_from_sorting';

	/**
	 *
	 */
	public const KEY_PREVIOUS_ORDER = 'previous_order';

	/**
	 *
	 */
	public const KEY_RECORDED = 'recorded';

	/**
	 *
	 */
	public const KEY_PERIOD_IN_DAYS = 'period_in_days';

	/**
	 *
	 */
	public const KEY_PERIOD_IN_DAYS_QUANTITY = 'period_in_days_quantity';

	/**
	 *
	 */
	public const KEY_LAST_INDEX_UPDATE = 'last_index_update';

	/**
	 *
	 */
	public const KEY_PRODUCT_CACHE = 'product_cache';

	/**
	 *
	 */
	public const KEY_SERVER_LOAD_CACHE = 'server_load_cache';

	/**
	 * TODO CHECK IF THIS NEEDS TO BE HERE OR HARD CODE IT
	 */
	public const SALES_PERIODS = array(
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
}
