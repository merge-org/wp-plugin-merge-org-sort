<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Wordpress\Service;

final class ActionsRegistrar {

	/**
	 * @return void
	 */
	public function run(): void {
		add_action("woocommerce_thankyou", function(int $orderId): void {});

		add_action("admin_menu", function(): void {});

		add_action("manage_edit-product_columns", function(array $columns): array {
			return [];
		});

		add_action("manage_product_posts_custom_column", function(string $column, int $productId): void {}, 10, 2);
	}
}
