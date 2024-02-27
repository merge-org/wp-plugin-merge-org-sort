<?php
declare(strict_types=1);

/*
 * Plugin Name: Sort
 * Plugin URI: https://sort.joinmerge.gr
 * Description: 📊Sort - Sales Order Ranking Tool | Powered by Merge
 * Author: Merge
 * Author URI: https://github.com/merge-org
 * Version: 1.0.39
 * Text Domain: merge-org-sort
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 6.0
 * Tested up to: 6.4
 * WC requires at least: 7.0
 * WC tested up to: 8.1.1
 */

namespace MergeOrg\Sort;

use MergeOrg\Sort\Exception\SortException;
use MergeOrg\Sort\WordPress\ActionsRegistrar;

require_once __DIR__ . '/vendor/autoload.php';

try {
	ActionsRegistrar::construct();
} catch ( SortException $sortException ) {
	add_action(
		'admin_notices',
		function () use ( $sortException ) {
			echo "<div class='notice notice-error'><p>Sort | Error: {$sortException->getMessage()}{$sortException->getTraceAsString()}</p></div>";
		}
	);
	return;
}
