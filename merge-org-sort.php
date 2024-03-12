<?php
declare(strict_types=1);

/*
 * Plugin Name: Sort
 * Plugin URI: https://sort.joinmerge.gr
 * Description: 📊Sort - Sales Order Ranking Tool | Powered by Merge
 * Author: Merge
 * Author URI: https://github.com/merge-org
 * Version: 2.0.0
 * Text Domain: merge-org-sort
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 6.0
 * Tested up to: 6.4.3
 * WC requires at least: 7.0
 * WC tested up to: 8.7.0
 */

namespace MergeOrg\Sort;

use WP_Query;
use Exception;
use MergeOrg\WpPluginSort\Container;
use MergeOrg\WpPluginSort\Constants;
use MergeOrg\WpPluginSort\Model\OrdersRecorder;
use MergeOrg\WpPluginSort\Model\RemainingOrdersNoticer;
use MergeOrg\WpPluginSort\Model\ProductsSalesPeriodsUpdater;

require_once __DIR__ . '/vendor/autoload.php';

file_exists( $devActionsFilePath = __DIR__ . '/src/dev-inc/dev-actions.php' ) && require_once $devActionsFilePath;

add_action(
	'init',
	function () {
		if ( wp_doing_cron() ) {
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
);

add_action(
	'admin_notices',
	/**
	 * @throws Exception
	 */
	function (): void {
		/**
		 * @var RemainingOrdersNoticer $remainingOrdersNoticer
		 */
		$remainingOrdersNoticer = Container::get( RemainingOrdersNoticer::class );

		$remainingOrdersNoticer->notice();
	}
);

add_filter(
	'manage_edit-product_columns',
	function ( array $columns ): array {
		/**
		 * @var Constants $constants
		 */
		$constants = Container::get( Constants::class );

		return array_replace( $columns, $constants->getProductColumns() );
	}
);

add_action(
	'pre_get_posts',
	function ( WP_Query $query ): void {
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
);

add_filter(
	'manage_edit-product_sortable_columns',
	function ( array $columns ): array {
		/**
		 * @var Constants $constants
		 */
		$constants = Container::get( Constants::class );

		return array_replace( $columns, $constants->getProductColumnsForSorting() );
	}
);

add_action(
	'manage_product_posts_custom_column',
	function ( string $column, int $postId ): void {
		/**
		 * @var Constants $constants
		 */
		$constants = Container::get( Constants::class );

		$productColumns = $constants->getProductColumns();

		if ( ! in_array( $column, array_keys( $productColumns ) ) ) {
			return;
		}

		echo get_post_meta( $postId, $column, true );
	},
	10,
	2
);
