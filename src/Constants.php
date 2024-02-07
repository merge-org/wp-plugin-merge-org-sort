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
	public const SALES_FIELD = "sort-sales";

	/**
	 *
	 */
	public const PREVIOUS_ORDER_FIELD = "sort-previous_order";

	/**
	 *
	 */
	public const EXCLUDE_FROM_SORTING_FIELD = "sort-exclude_from_sorting";

	/**
	 *
	 */
	public const LINE_ITEM_RECORDED = "_sort-line_item_recorded";

	/**
	 *
	 */
	public const SORT_FILTER_CAN_RECORD_LINE_ITEM_SALES = "sort-can_record_line_item_sales";

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
