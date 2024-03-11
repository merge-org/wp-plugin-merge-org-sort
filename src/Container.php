<?php
declare(strict_types=1);

namespace MergeOrg\WpPluginSort;

use MergeOrg\WpPluginSort\WordPress\Api;
use MergeOrg\WpPluginSort\Model\OrdersRecorder;
use MergeOrg\WpPluginSort\WordPress\ApiInterface;
use MergeOrg\WpPluginSort\Service\SalesIncrementer;
use MergeOrg\WpPluginSort\Service\SalesPeriodManager;
use MergeOrg\WpPluginSort\Model\ProductSalesPeriodsUpdater;

final class Container {

	/**
	 * @var bool
	 */
	private static bool $got = false;

	/**
	 * @var array<string, mixed>
	 */
	private static array $container = array();

	/**
	 * @param string $key
	 * @return mixed
	 */
	public static function get( string $key ) {
		if ( ! self::$got ) {
			$constants                  = new Constants();
			$salesPeriodManager         = new SalesPeriodManager( $constants );
			$api                        = new Api( $constants, $salesPeriodManager );
			$salesIncrementer           = new SalesIncrementer();
			$ordersRecorder             = new OrdersRecorder( $api, $salesIncrementer );
			$productSalesPeriodsUpdater = new ProductSalesPeriodsUpdater( $api );

			self::$container[ Constants::class ]                  = $constants;
			self::$container[ SalesPeriodManager::class ]         = $salesPeriodManager;
			self::$container[ ApiInterface::class ]               = $api;
			self::$container[ SalesIncrementer::class ]           = $salesIncrementer;
			self::$container[ OrdersRecorder::class ]             = $ordersRecorder;
			self::$container[ ProductSalesPeriodsUpdater::class ] = $productSalesPeriodsUpdater;

			self::$got = true;
		}

		return self::$container[ $key ];
	}
}
