<?php
declare(strict_types=1);

namespace MergeOrg\Sort;

/**
 * Class Constants
 *
 * @package MergeOrg\Sort
 */
final class Constants {

	/**
	 *
	 */
	public const ADMIN_MAIN_PAGE_MENU_SLUG = "merge-org-sort-admin_main_page";

	/**
	 *
	 */
	public const OPTIONS_FIELD = "merge-org-sort-options";

	/**
	 *
	 */
	public const OPTIONS_DEBUG_FIELD = "debug";

	/**
	 *
	 */
	public const OPTIONS_USE_LINE_ITEM_QUANTITY_FIELD = "use_line_item_quantity";

	/**
	 *
	 */
	public const SALES_FIELD = "merge-org-sort-sales";

	/**
	 *
	 */
	public const PREVIOUS_ORDER_FIELD = "merge-org-sort-previous_order";

	/**
	 *
	 */
	public const EXCLUDE_FROM_SORTING_FIELD = "merge-org-sort-exclude_from_sorting";

	/**
	 *
	 */
	public const LINE_ITEM_RECORDED = "_merge-org-sort-line_item_recorded";

	/**
	 *
	 */
	public const SORT_FILTER_CAN_RECORD_LINE_ITEM_SALES = "merge-org-sort-can_record_line_item_sales";

	/**
	 *
	 */
	public const SALES_PERIODS = [
		1,
		7,
		15,
		30,
		60,
		90,
		180,
		365,
	];
}
