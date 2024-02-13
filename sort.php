<?php
declare(strict_types=1);

/*
 * Plugin Name: Merge Sort
 * Plugin URI: https://sort.joinmerge.gr
 * Description: Merge Sort - Sales Order Ranking Tool
 * Author: Merge
 * Author URI: https://github.com/merge-org
 * Version: 1.0.12
 * Text Domain: merge-org-sort
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 6.0
 * Tested up to: 6.4
 * WC requires at least: 7.0
 * WC tested up to: 8.1.1
 */

namespace MergeOrg\Sort;

use MergeOrg\Sort\Service\Module\Container;
use MergeOrg\Sort\Wordpress\Service\ActionsRegistrar;
use MergeOrg\Sort\Exception\InvalidSalesPeriodInProductConstructionException;
use MergeOrg\Sort\Exception\InvalidPeriodInDaysInSalesPeriodCreationException;

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
	 * @throws InvalidPeriodInDaysInSalesPeriodCreationException
	 * @throws InvalidSalesPeriodInProductConstructionException
	 */
	public function __construct() {
		if(!$this->validate() || self::$init) {
			return;
		}

		(new ActionsRegistrar(new Container()))->run();

		self::$init = TRUE;
	}

	/**
	 * @return bool
	 */
	private function validate(): bool {
		return TRUE;
	}

}

new Sort();
