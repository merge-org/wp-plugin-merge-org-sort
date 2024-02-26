<?php
declare(strict_types=1);

namespace MergeOrg\Sort;

final class Constants {

	/**
	 *
	 */
	public const PLUGIN_NAME = "merge-org-sort";

	/**
	 *
	 */
	public const POST_TYPE_PRODUCT = "product";

	/**
	 *
	 */
	public const POST_TYPE_PRODUCT_VARIATION = "product_variation";

	/**
	 *
	 */
	public const KEY_SALES = "sales";

	/**
	 *
	 */
	public const KEY_EXCLUDE_FROM_SORTING = "exclude_from_sorting";

	/**
	 *
	 */
	public const KEY_PREVIOUS_ORDER = "previous_order";

	/**
	 *
	 */
	public const KEY_RECORDED = "recorded";

	/**
	 *
	 */
	public const KEY_PERIOD_IN_DAYS = "period_in_days";

	/**
	 *
	 */
	public const KEY_PERIOD_IN_DAYS_QUANTITY = "period_in_days_quantity";

	/**
	 *
	 */
	public const KEY_LAST_INDEX_UPDATE = "last_index_update";

	/**
	 * TODO CHECK IF THIS NEEDS TO BE HERE OR HARD CODE IT
	 */
	public const SALES_PERIODS = [
		1 => [
			"daily",
			"Daily",
		],
		7 => [
			"weekly",
			"Weekly",
		],
		15 => [
			"semi_monthly",
			"Semi-Monthly",
		],
		30 => [
			"monthly",
			"Monthly",
		],
		90 => [
			"quarterly",
			"Quarterly",
		],
		180 => [
			"semi_annually",
			"Semi-Annually",
		],
		365 => [
			"yearly",
			"Yearly",
		],
	];

}
