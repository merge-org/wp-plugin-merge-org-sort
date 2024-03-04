<?php
declare(strict_types=1);

namespace MergeOrg\Sort\WordPress;

use MergeOrg\Sort\Service\Namer;
use MergeOrg\Sort\Exception\SortException;
use MergeOrg\Sort\Service\OrderRepository;
use MergeOrg\Sort\Service\SalesIncrementer;
use MergeOrg\Sort\Service\ProductRepository;
use MergeOrg\Sort\Service\SalesPeriodManager;
use MergeOrg\Sort\Service\ServerLoadCalculator;
use MergeOrg\Sort\Service\OptimalPostCountFinder;
use MergeOrg\Sort\Exception\InvalidKeyNameException;
use MergeOrg\Sort\Service\ServerLoadCalculatorInterface;
use MergeOrg\Sort\Service\ProductToBeIncrementedCollectionGenerator;

/**
 * Class ActionsRegistrar
 *
 * @package MergeOrg\Sort\WordPress
 * @codeCoverageIgnore
 */
final class ActionsRegistrar {

	/**
	 * @var self
	 */
	private static self $instance;

	/**
	 * @var array<string, mixed>
	 */
	private array $definitions;

	/**
	 * @var bool
	 */
	private bool $got = false;

	/**
	 * @var bool
	 */
	private bool $invoked = false;

	/**
	 *
	 */
	private function __construct() {}

	/**
	 * @return self
	 * @throws InvalidKeyNameException
	 */
	public static function construct(): self {
		if ( self::$instance ?? null ) {
			return self::$instance;
		}

		$self = self::$instance = new self();
		$self();

		return $self;
	}

	/**
	 * @return void
	 * @throws InvalidKeyNameException
	 */
	public function __invoke() {
		if ( $this->invoked ) {
			return;
		}

		$logger = $this->get( Logger::class );

		if ( ! function_exists( 'wc_get_order' ) ) {
			add_action(
				'admin_notices',
				function () {
					echo "<div class='notice notice-error'><p><strong>Woocommerce</strong> is not activated. <strong>Sort</strong> will not work until <strong>WooCommerce</strong> is activated back again.</p></div>";
				}
			);
			return;
		}

		// add_action("woocommerce_order_status_processing", function(int $orderId) use ($logger) {
		// try {
		// $this->get(OrderRecorder::class)->record($orderId);
		// } catch(SortException $sortException) {
		// **
		// * @var Logger $logger
		// */
		// $logger->log("error", "{$sortException->getMessage()}: {$sortException->getTraceAsString()}");
		//
		// throw $sortException;
		// }
		// });

		add_action(
			'init',
			function () {
				if ( wp_doing_cron() ) {
					$orderRecorder   = $this->get( OrderRecorder::class );
					$orderRepository = $this->get( OrderRepository::class );
					$orders          = $orderRepository->getOrdersNotRecorded();
					foreach ( $orders as $order ) {
						$orderRecorder->record( $order->getId() );
					}
				}
			}
		);

		add_action(
			'merge-org-sort-update_products_indexes',
			function () use ( $logger ) {
				try {
					$this->get( ProductRepository::class )->setProductsIndexes();
				} catch ( SortException $sortException ) {
					/**
					 * @var Logger $logger
					 */
					$logger->log( 'error', "{$sortException->getMessage()}: {$sortException->getTraceAsString()}" );

					throw $sortException;
				}
			}
		);

		$development = file_exists( __DIR__ . '/../dev-inc/dev-actions.php' );

		// add_action("init", function() use ($development) {
		// wp_schedule_single_event(time() + ($development ? 10 : 600),
		// "merge-org-sort-update_products_indexes");
		// });

		/**
		 * This should fire only in a dev environment
		 */
		$development && require_once __DIR__ . '/../dev-inc/dev-actions.php';

		$this->invoked = true;
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	private function get( string $key ) {
		if ( ! $this->got ) {
			$this->definitions[ Namer::class ]                         = $namer = new Namer();
			$this->definitions[ Logger::class ]                        = $logger = new Logger( $namer );
			$this->definitions[ Cache::class ]                         = $cache = new Cache();
			$this->definitions[ ServerLoadCalculatorInterface::class ] =
			$serverLoadCalculator                                      = new ServerLoadCalculator( $cache, $namer );
			$this->definitions[ OptimalPostCountFinder::class ]        =
			$optimalPostsCountFinder                                   = new OptimalPostCountFinder( $serverLoadCalculator );
			$this->definitions[ ApiInterface::class ]                  = $api = new Api( $namer, $optimalPostsCountFinder );
			$this->definitions[ SalesPeriodManager::class ]            = $salesPeriodManager = new SalesPeriodManager( $namer );
			$this->definitions[ ProductRepository::class ]             =
			$productRepository                            = new ProductRepository( $api, $salesPeriodManager, $cache, $namer );
			$this->definitions[ OrderRepository::class ]  =
			$orderRepository                              = new OrderRepository( $api, $namer, $cache );
			$this->definitions[ SalesIncrementer::class ] = $salesIncrementer = new SalesIncrementer();
			$this->definitions[ ProductToBeIncrementedCollectionGenerator::class ] =
			$productToBeIncrementedCollectionGenerator                             =
				new ProductToBeIncrementedCollectionGenerator( $orderRepository, $productRepository, $salesIncrementer, $logger );
			$this->definitions[ OrderRecorder::class ]                             =
				new OrderRecorder( $productToBeIncrementedCollectionGenerator, $productRepository, $orderRepository, $logger );
			$this->got = true;
		}

		return $this->definitions[ $key ];
	}
}
