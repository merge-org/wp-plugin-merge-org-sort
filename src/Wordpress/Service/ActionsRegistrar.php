<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Wordpress\Service;

use MergeOrg\Sort\Constants;
use MergeOrg\Sort\Service\Module\Container;
use MergeOrg\Sort\Service\Repository\ProductRepository;
use MergeOrg\Sort\Service\IntegerEncoder\IntegerEncoderInterface;
use MergeOrg\Sort\Exception\InvalidSalesPeriodInProductConstructionException;
use MergeOrg\Sort\Exception\InvalidPeriodInDaysInSalesPeriodCreationException;

final class ActionsRegistrar {

	/**
	 * @var array<int, int>
	 */
	public static array $rowsShowed = [];

	/**
	 * @var Container
	 */
	private Container $container;

	/**
	 * @param Container $container
	 */
	public function __construct(Container $container) {
		$this->container = $container;
	}

	/**
	 * @return void
	 * @throws InvalidPeriodInDaysInSalesPeriodCreationException
	 * @throws InvalidSalesPeriodInProductConstructionException
	 */
	public function run(): void {
		add_action("woocommerce_thankyou", function(int $orderId): void {
			$this->container->get(OrderRecorder::class)->record($orderId);
		});

		add_action("manage_edit-product_columns", function(array $columns): array {
			foreach(Constants::SALES_PERIODS_IN_DAYS as $periodInDays => $label) {
				$columns["merge-org-sort-period_in_days_{$periodInDays}"] = "<span>$label</span>";
			}

			return $columns;
		}, 99);

		add_action("manage_product_posts_custom_column", function(string $column, int $productId): void {
			self::$rowsShowed[$productId] = self::$rowsShowed[$productId] ?? 1;

			$show = TRUE;
			if(count(self::$rowsShowed) > 5) {
				$show = FALSE;
			}

			$warning = count(self::$rowsShowed) === 6;

			/**
			 * @var ProductRepository $productRepository
			 */
			$productRepository = $this->container->get(ProductRepository::class);

			if(strpos($column, "merge-org-sort-period_in_days_") !== FALSE) {
				$periodInDays = str_replace("merge-org-sort-period_in_days_", "", $column);
				if($show) {
					$sales = $productRepository->get($productId)->getSalePeriodByPeriodInDays((int) $periodInDays)->getSales();
					$quantity =
						$productRepository->get($productId)
										  ->getSalePeriodByPeriodInDays((int) $periodInDays)
										  ->getQuantityBasedSales();
					echo "$sales ($quantity)";
				} else {
					echo "<pre style='font-size: 0.8em'>PRO<br/>Feature</pre>";
				}
			}
		}, 10, 2);
	}
}
