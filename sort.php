<?php
declare(strict_types=1);

/*
 * Plugin Name: Merge Sort
 * Plugin URI: https://sort.joinmerge.gr
 * Description: Merge Sort - Sales Order Ranking Tool
 * Author: Merge
 * Author URI: https://github.com/merge-org
 * Version: 1.0.3
 * Text Domain: merge_sort
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 6.0
 * Tested up to: 6.4
 * WC requires at least: 7.0
 * WC tested up to: 8.1.1
 */

namespace MergeOrg\Sort;

/**
 * Class Sort
 *
 * @package MergeOrg\Sort
 */
final class Sort {

	/**
	 * @var bool
	 */
	private static bool $bootstrapped = FALSE;

	/**
	 *
	 */
	public function __construct() {
		if(!$this->validate() || self::$bootstrapped) {
			return;
		}

		$this->bootstrap();
	}

	/**
	 * @return bool
	 */
	private function validate(): bool {
		// TODO VALIDATE
		//		add_action("admin_notices", function() {
		//			echo "
		//				<div class=\"notice notice-error\">
		//            		<p></p>
		//        		</div>";
		//		});

		return TRUE;
	}

	/**
	 * @return void
	 */
	private function bootstrap(): void {
		self::$bootstrapped = TRUE;
	}
}

new Sort();
new Sort();
