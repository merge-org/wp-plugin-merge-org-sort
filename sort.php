<?php
declare(strict_types=1);

/*
 * Plugin Name: Merge Sort
 * Plugin URI: https://sort.joinmerge.gr
 * Description: Merge Sort - Sales Order Ranking Tool
 * Author: Merge
 * Author URI: https://github.com/merge-org
 * Version: 1.0.4
 * Text Domain: merge_sort
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 6.0
 * Tested up to: 6.4
 * WC requires at least: 7.0
 * WC tested up to: 8.1.1
 */

namespace MergeOrg\Sort;

use MergeOrg\Sort\Service\ApiService;
use MergeOrg\Sort\Hooks\ThankYouHook;
use MergeOrg\Sort\Service\WpDataApiService;
use MergeOrg\Sort\Service\WpDataApiServiceInterface;

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
	private function init(): void {
		add_action("woocommerce_thankyou", function(int $orderId): void {
			($this->get(ThankYouHook::class))($orderId);
		});

		self::$init = TRUE;
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	private function get(string $key) {
		if(!self::$containerInit) {
			$wpDataApiService = new WpDataApiService();
			$apiService = new ApiService($wpDataApiService);
			$thankYouHook = new ThankYouHook($apiService);
			$this->container[WpDataApiServiceInterface::class] = $wpDataApiService;
			$this->container[ApiService::class] = $apiService;
			$this->container[ThankYouHook::class] = $thankYouHook;

			self::$containerInit = TRUE;
		}

		return $this->container[$key];
	}
}

new Sort();
new Sort();
