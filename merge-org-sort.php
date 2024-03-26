<?php
declare(strict_types=1);

/*
 * Plugin Name: Sort
 * Plugin URI: https://sort.joinmerge.gr
 * Description: 📊Sort - Sales Order Ranking Tool | Powered by Merge
 * Author: Merge
 * Author URI: https://github.com/merge-org
 * Version: 2.3.1
 * Text Domain: merge-org-sort
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 6.0
 * Tested up to: 6.4.3
 * WC requires at least: 7.0
 * WC tested up to: 8.7.0
 */

namespace MergeOrg\Sort;

use Exception;
use MergeOrg\WpPluginSort\Model\ActionsRegistrar;

require_once __DIR__ . '/vendor/autoload.php';

file_exists( $devActionsFilePath = __DIR__ . '/src/dev-inc/dev-actions.php' ) && require_once $devActionsFilePath;

try {
	ActionsRegistrar::register();
} catch ( Exception $exception ) {
}

// TODO | MAKE SURE ALL REQUIREMENTS ARE MET
// TODO | WC INSTALLED | WC VERSION | WP VERSION | PHP VERSION | LIBS

add_action(
	'plugin_loaded',
	function () {
		$baseName = basename( __DIR__ );
		load_plugin_textdomain( 'merge-org-sort', false, "$baseName/languages/" );
	}
);
