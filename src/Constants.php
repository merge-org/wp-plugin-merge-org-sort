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
	public const META_FIELD_PRODUCT_SALES = "merge-org-sort-sales";

	/**
	 *
	 */
	public const META_FIELD_PRODUCT_EXCLUDE_FROM_SORTING = "merge-org-sort-exclude_from_sorting";

	/**
	 *
	 */
	public const META_FIELD_PRODUCT_PREVIOUS_ORDER = "merge-org-sort-previous_order";

	/**
	 *
	 */
	public const META_FIELD_ORDER_RECORDED = "merge-org-sort-recorded";

	/**
	 *
	 */
	public const SALES_PERIODS_IN_DAYS = [
		1 => "daily",
		7 => "weekly",
		15 => "semiMonthly",
		30 => "monthly",
		90 => "quarterly",
		180 => "semiAnnually",
		365 => "yearly",
	];
}
