<?php
declare(strict_types=1);

/*
 * Plugin Name: Merge Sort
 * Plugin URI: https://sort.joinmerge.gr
 * Description: Merge Sort - Sales Order Ranking Tool
 * Author: Merge
 * Author URI: https://github.com/merge-org
 * Version: 1.0.6
 * Text Domain: merge-org-sort
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 6.0
 * Tested up to: 6.4
 * WC requires at least: 7.0
 * WC tested up to: 8.1.1
 */

namespace MergeOrg\Sort;

use MergeOrg\Sort\Wordpress\Service\ActionsRegistrar;

require_once __DIR__ . "/vendor/autoload.php";

/**
 * Class Sort
 *
 * @package MergeOrg\Sort
 */
final class Sort {

	/**
	 * @var bool
	 */
	private static bool $init = FALSE;

	/**
	 * @var bool
	 */
	private static bool $containerInit = FALSE;

	/**
	 * @var array
	 */
	private array $container = [];

	/**
	 *
	 */
	public function __construct() {
		if(!$this->validate() || self::$init) {
			return;
		}

		$this->init();
	}

	/**
	 * @return bool
	 */
	private function validate(): bool {
		return TRUE;
	}

	/**
	 * @return void
	 */
	private function init(): void {
		/**
		 * We don't get the `ActionsRegistrar` from the container, because that would initiated all other instances.
		 * While here we just want to register (add) the required actions
		 */
		(new ActionsRegistrar())->run();

		self::$init = TRUE;
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	private function get(string $key) {
		if(!self::$containerInit) {
			self::$containerInit = TRUE;
		}

		return $this->container[$key];
	}
}

new Sort();
