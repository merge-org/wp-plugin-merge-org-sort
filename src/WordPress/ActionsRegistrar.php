<?php
declare(strict_types=1);

namespace MergeOrg\Sort\WordPress;

use MergeOrg\Sort\Service\Namer;
use MergeOrg\Sort\Service\OrderRepository;
use MergeOrg\Sort\Service\SalesIncrementer;
use MergeOrg\Sort\Service\ProductRepository;
use MergeOrg\Sort\Service\SalesPeriodManager;
use MergeOrg\Sort\Exception\InvalidKeyNameException;
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

		if ( ! function_exists( 'wc_get_order' ) ) {
			add_action(
				'admin_notices',
				function () {
					echo "<div class='notice notice-error'><p><strong>Woocommerce</strong> is not activated. <strong>Sort</strong> will not work until <strong>WooCommerce</strong> is activated back again.</p></div>";
				}
			);
			return;
		}

		add_action(
			'init',
			function () {
				if ( wp_doing_cron() ) {
					$logger = $this->get( Logger::class );

					// Order Recording
					$orderRecorder   = $this->get( OrderRecorder::class );
					$orderRepository = $this->get( OrderRepository::class );

					$logger->log( 'info', 'Will try to get non recorded orders' );
					$orders      = $orderRepository->getOrdersNotRecorded();
					$ordersCount = count( $orders );
					$logger->log( 'info', "Found '$ordersCount' non recored orders" );
					foreach ( $orders as $order ) {
						$logger->log( 'info', "Recording order #{$order->getId()}" );
						$orderRecorder->record( $order->getId() );
						$logger->log( 'info', "Order #{$order->getId()} was recorded" );
					}

					// Products Indexing Update
					$productRepository   = $this->get( ProductRepository::class );
					$productIndexUpdater = $this->get( ProductIndexUpdater::class );

					$logger->log( 'info', 'Will try to get non updated products' );
					$products      = $productRepository->getProductsWithNoRecentUpdatedIndex();
					$productsCount = count( $products );
					$logger->log( 'info', "Found '$productsCount' non updated products" );
					foreach ( $products as $product ) {
						$logger->log( 'info', "Updating product #{$product->getId()}" );
						$productIndexUpdater->update( $product );
						$logger->log( 'info', "Product #{$product->getId()} was updated" );
					}
				}
			}
		);

		$development = file_exists( __DIR__ . '/../dev-inc/dev-actions.php' );

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
			$this->definitions[ Namer::class ]              = $namer = new Namer();
			$this->definitions[ Logger::class ]             = $logger = new Logger( $namer );
			$this->definitions[ Cache::class ]              = $cache = new Cache();
			$this->definitions[ ApiInterface::class ]       = $api = new Api( $namer, $cache );
			$this->definitions[ SalesPeriodManager::class ] = $salesPeriodManager = new SalesPeriodManager( $namer );
			$this->definitions[ ProductRepository::class ]  =
			$productRepository                              = new ProductRepository( $api, $salesPeriodManager, $cache, $namer );
			$this->definitions[ OrderRepository::class ]    =
			$orderRepository                                = new OrderRepository( $api, $namer, $cache );
			$this->definitions[ SalesIncrementer::class ]   = $salesIncrementer = new SalesIncrementer();
			$this->definitions[ ProductToBeIncrementedCollectionGenerator::class ] =
			$productToBeIncrementedCollectionGenerator                             =
				new ProductToBeIncrementedCollectionGenerator( $orderRepository, $productRepository, $salesIncrementer );
			$this->definitions[ OrderRecorder::class ]                             =
				new OrderRecorder( $productToBeIncrementedCollectionGenerator, $productRepository, $orderRepository );
			$this->definitions[ ProductIndexUpdater::class ]                       = new ProductIndexUpdater( $namer, $api );

			$this->got = true;
		}

		return $this->definitions[ $key ];
	}
}
