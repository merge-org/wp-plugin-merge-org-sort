<?php
declare(strict_types=1);

namespace MergeOrg\Sort\WordPress;

use MergeOrg\Sort\Service\Namer;
use MergeOrg\Sort\Exception\SortException;
use MergeOrg\Sort\Service\SalesIncrementer;
use MergeOrg\Sort\Service\ProductRepository;
use MergeOrg\Sort\Service\SalesPeriodManager;
use MergeOrg\Sort\Exception\InvalidKeyNameSortException;
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
	private bool $got = FALSE;

	/**
	 * @var bool
	 */
	private bool $invoked = FALSE;

	/**
	 *
	 */
	private function __construct() {}

	/**
	 * @return self
	 * @throws InvalidKeyNameSortException
	 */
	public static function construct(): self {
		if(self::$instance ?? NULL) {
			return self::$instance;
		}

		$self = self::$instance = new self();
		$self();

		return $self;
	}

	/**
	 * @return void
	 * @throws InvalidKeyNameSortException
	 */
	public function __invoke() {
		if($this->invoked) {
			return;
		}

		$logger = $this->get(Logger::class);

		if(!function_exists("wc_get_order")) {
			add_action("admin_notices", function() {
				echo "<div class='notice notice-error'><p><strong>Woocommerce</strong> is not activated. <strong>Sort</strong> will not work until <strong>WooCommerce</strong> is activated back again.</p></div>";
			});
			return;
		}

		add_action("woocommerce_order_status_processing", function(int $orderId) use ($logger) {
			try {
				$this->get(OrderRecorder::class)->record($orderId);
			} catch(SortException $sortException) {
				/**
				 * @var Logger $logger
				 */
				$logger->log("error", "{$sortException->getMessage()}: {$sortException->getTraceAsString()}");

				throw $sortException;
			}
		});

		/**
		 * This should fire only in a dev environment
		 */
		file_exists(__DIR__ . "/../dev-inc/dev-actions.php") && require_once __DIR__ . "/../dev-inc/dev-actions.php";

		$this->invoked = TRUE;
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	private function get(string $key) {
		if(!$this->got) {
			$this->definitions[Namer::class] = $namer = new Namer();
			$this->definitions[Logger::class] = new Logger($namer);
			$this->definitions[ApiInterface::class] = $api = new Api($namer);
			$this->definitions[SalesPeriodManager::class] = $salesPeriodManager = new SalesPeriodManager($namer);
			$this->definitions[ProductRepository::class] = new ProductRepository($api, $salesPeriodManager);
			$this->definitions[SalesIncrementer::class] = $salesIncrementer = new SalesIncrementer();
			$this->definitions[ProductToBeIncrementedCollectionGenerator::class] =
			$productToBeIncrementedCollectionGenerator = new ProductToBeIncrementedCollectionGenerator($api, $salesIncrementer);
			$this->definitions[OrderRecorder::class] =
				new OrderRecorder($productToBeIncrementedCollectionGenerator, $api, $namer);
			$this->got = TRUE;
		}

		return $this->definitions[$key];
	}
}
