<?php
declare(strict_types=1);

/*
 * Plugin Name: Sort
 * Plugin URI: https://sort.joinmerge.gr
 * Description: 📊Sort - Sales Order Ranking Tool | Powered by Merge
 * Author: Merge
 * Author URI: https://github.com/merge-org
 * Version: 1.1.6
 * Text Domain: merge-org-sort
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 6.0
 * Tested up to: 6.4.3
 * WC requires at least: 7.0
 * WC tested up to: 8.7.0
 */

namespace MergeOrg\Sort;

use MergeOrg\WpPluginSort\Constants;
use MergeOrg\WpPluginSort\WordPress\Api;
use MergeOrg\WpPluginSort\Action\OrdersRecorder;
use MergeOrg\WpPluginSort\Service\SalesIncrementer;
use MergeOrg\WpPluginSort\Service\SalesPeriodManager;
use MergeOrg\WpPluginSort\Action\ProductSalesPeriodsUpdater;

require_once __DIR__ . '/vendor/autoload.php';

file_exists( $devActionsFilePath = __DIR__ . '/src/dev-inc/dev-actions.php' ) && require_once $devActionsFilePath;

add_action(
	'init',
	function () {
		if ( wp_doing_cron() ) {
			$constants          = new Constants();
			$salesPeriodManager = new SalesPeriodManager( $constants );
			$api                = new Api( $constants, $salesPeriodManager );
			$salesIncrementer   = new SalesIncrementer();

			$ordersRecorder             = new OrdersRecorder( $api, $salesIncrementer );
			$productSalesPeriodsUpdater = new ProductSalesPeriodsUpdater( $api );

			$ordersRecorder->record();
			$productSalesPeriodsUpdater->update();
		}
	}
);
