<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Hooks;

use MergeOrg\Sort\Constants;
use MergeOrg\Sort\Service\ApiService;
use MergeOrg\Sort\Service\WpDataApiService;

final class ThankYouHook {

	/**
	 * @var ApiService
	 */
	private ApiService $apiService;

	/**
	 * @var WpDataApiService
	 */
	private WpDataApiService $wpDataApiService;

	/**
	 * @param ApiService $apiService
	 * @param WpDataApiService $wpDataApiService
	 */
	public function __construct(ApiService $apiService, WpDataApiService $wpDataApiService) {
		$this->apiService = $apiService;
		$this->wpDataApiService = $wpDataApiService;
	}

	/**
	 * @param int $orderId
	 * @return void
	 */
	public function __invoke(int $orderId): void {
		$order = $this->wpDataApiService->getOrder($orderId);

		if(!$order) {
			return;
		}

		foreach($order->get_items() as $item) {
			$data = $item->get_data();
			$productId = (int) ($data["product_id"] ?? 0);
			$lineItemId = (int) ($data["id"] ?? 0);
			// TODO CHECK IF QUANTITY PARSING WOULD BE CONFIGURABLE OR NOT
			$quantity = (int) $data["quantity"];

			if(!$productId || !$lineItemId) {
				continue;
			}

			apply_filters(Constants::SORT_FILTER_CAN_RECORD_LINE_ITEM_SALES, TRUE, $lineItemId) &&
			$this->apiService->incrementSalesAndSave($lineItemId, $productId, $quantity);
		}
	}
}
