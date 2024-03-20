<?php
declare(strict_types=1);

namespace MergeOrg\WpPluginSort\Model;

use WP_Query;
use Exception;
use MergeOrg\WpPluginSort\Container;
use MergeOrg\WpPluginSort\Constants;

/**
 * @codeCoverageIgnore
 */
final class ActionsRegistrar {

	/**
	 *
	 */
	public const RECORD_ORDERS_AND_UPDATE_PRODUCTS_ACTION = array(
		'action'       => array(
			self::class,
			'recordOrdersAndUpdateProducts',
		),
		'priority'     => 10,
		'acceptedArgs' => 1,
	);

	/**
	 *
	 */
	public const DISPLAY_REMAINING_ORDERS_NOTICE_ACTIONS = array(
		'action' => array(
			self::class,
			'displayRemainingOrdersNotice',
		),
	);

	/**
	 *
	 */
	public const FILTER_PRODUCT_COLUMNS = array(
		'action' => array(
			self::class,
			'filterProductColumns',
		),
	);

	/**
	 *
	 */
	public const FILTER_SORTABLE_PRODUCT_COLUMNS = array(
		'action' => array(
			self::class,
			'filterSortableProductColumns',
		),
	);

	/**
	 *
	 */
	public const SHOW_SALES_IN_PRODUCT_CELL_ACTION = array(
		'action'       => array(
			self::class,
			'showSalesInProductCell',
		),
		'acceptedArgs' => 2,
	);

	/**
	 *
	 */
	public const HOOK_SALES_META_KEY_IN_WP_QUERY = array(
		'action' => array(
			self::class,
			'hookSalesMetaKeyInWpQuery',
		),
	);

	/**
	 * @return void
	 */
	public static function register(): void {
		add_action(
			'init',
			self::RECORD_ORDERS_AND_UPDATE_PRODUCTS_ACTION['action'],
			self::RECORD_ORDERS_AND_UPDATE_PRODUCTS_ACTION['priority'],
			self::RECORD_ORDERS_AND_UPDATE_PRODUCTS_ACTION['acceptedArgs']
		);

		add_action(
			'admin_notices',
			self::DISPLAY_REMAINING_ORDERS_NOTICE_ACTIONS['action'],
			self::DISPLAY_REMAINING_ORDERS_NOTICE_ACTIONS['priority'] ?? 10,
			self::DISPLAY_REMAINING_ORDERS_NOTICE_ACTIONS['acceptedArgs'] ?? 1
		);

		add_filter(
			'manage_edit-product_columns',
			self::FILTER_PRODUCT_COLUMNS['action'],
			self::FILTER_PRODUCT_COLUMNS['priority'] ?? 10,
			self::FILTER_PRODUCT_COLUMNS['acceptedArgs'] ?? 1
		);

		add_filter(
			'manage_edit-product_sortable_columns',
			self::FILTER_SORTABLE_PRODUCT_COLUMNS['action'],
			self::FILTER_SORTABLE_PRODUCT_COLUMNS['priority'] ?? 10,
			self::FILTER_SORTABLE_PRODUCT_COLUMNS['acceptedArgs'] ?? 1
		);

		add_action(
			'manage_product_posts_custom_column',
			self::SHOW_SALES_IN_PRODUCT_CELL_ACTION['action'],
			self::SHOW_SALES_IN_PRODUCT_CELL_ACTION['priority'] ?? 10,
			self::SHOW_SALES_IN_PRODUCT_CELL_ACTION['acceptedArgs'] ?? 1
		);

		add_action(
			'pre_get_posts',
			self::HOOK_SALES_META_KEY_IN_WP_QUERY['action'],
			self::HOOK_SALES_META_KEY_IN_WP_QUERY['priority'] ?? 10,
			self::HOOK_SALES_META_KEY_IN_WP_QUERY['acceptedArgs'] ?? 1
		);
	}

	/**
	 * @return void
	 */
	public static function recordOrdersAndUpdateProducts(): void {
		if ( wp_doing_cron() ) {
			if ( time() % 2 === 0 ) {
				// In order to avoid order sales collisions
				return;
			}

			/**
			 * @var OrdersRecorder $ordersRecorder
			 */
			$ordersRecorder = Container::get( OrdersRecorder::class );

			/**
			 * @var ProductsSalesPeriodsUpdater $productsSalesPeriodsUpdater
			 */
			$productsSalesPeriodsUpdater = Container::get( ProductsSalesPeriodsUpdater::class );

			$ordersRecorder->record();
			$productsSalesPeriodsUpdater->update();
		}
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	public static function displayRemainingOrdersNotice(): void {
		/**
		 * @var RemainingOrdersNoticer $remainingOrdersNoticer
		 */
		$remainingOrdersNoticer = Container::get( RemainingOrdersNoticer::class );

		$remainingOrdersNoticer->notice();
	}

	/**
	 * @param array<string, string> $columns
	 *
	 * @return array<string, string>
	 */
	public static function filterProductColumns( array $columns ): array {
		/**
		 * @var Constants $constants
		 */
		$constants = Container::get( Constants::class );

		return array_replace( $columns, $constants->getProductColumns() );
	}

	/**
	 * @param array<string, string> $columns
	 *
	 * @return array<string, string>
	 */
	public static function filterSortableProductColumns( array $columns ): array {
		/**
		 * @var Constants $constants
		 */
		$constants = Container::get( Constants::class );

		return array_replace( $columns, $constants->getProductColumnsForSorting() );
	}

	/**
	 * @param string $column
	 * @param int    $postId
	 *
	 * @return void
	 */
	public static function showSalesInProductCell( string $column, int $postId ) {
		/**
		 * @var Constants $constants
		 */
		$constants = Container::get( Constants::class );

		$productColumns = $constants->getProductColumns();

		if ( ! in_array( $column, array_keys( $productColumns ) ) ) {
			return;
		}

		echo get_post_meta( $postId, $column, true );
	}

	/**
	 * @param WP_Query $query
	 *
	 * @return void
	 */
	public static function hookSalesMetaKeyInWpQuery( WP_Query $query ): void {
		/**
		 * @var Constants $constants
		 */
		$constants = Container::get( Constants::class );

		$productColumns = $constants->getProductColumnsForSorting();

		$orderby = $query->get( 'orderby' );

		if ( in_array( $orderby, array_values( $productColumns ) ) ) {
			$query->set( 'meta_key', $orderby );
			$query->set( 'orderby', 'meta_value_num' );
		}
	}
}
