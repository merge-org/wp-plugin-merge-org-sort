<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Hooks;

use MergeOrg\Sort\Constants;
use MergeOrg\Sort\Service\ApiService;

final class ThankYouHook {

	/**
	 * @var ApiService
	 */
	private ApiService $apiService;

	/**
	 * @param ApiService $apiService
	 */
	public function __construct(ApiService $apiService) {
		$this->apiService = $apiService;
	}

	/**
	 * @param int $orderId
	 * @return void
	 */
	public function __invoke(int $orderId): void {
		$order = $this->apiService->getOrder($orderId);

		if(!$order) {
			return;
		}

		foreach($order->get_items() as $item) {
			$data = $item->get_data();
			$productId = (int) ($data["product_id"] ?? 0);
			$lineItemId = (int) ($data["id"] ?? 0);
			$quantity = $this->apiService->getOptionUseLineItemQuantity() ? (int) $data["quantity"] : 1;

			if(!$productId || !$lineItemId) {
				continue;
			}

			apply_filters(Constants::SORT_FILTER_CAN_RECORD_LINE_ITEM_SALES, TRUE, $lineItemId) &&
			$this->apiService->incrementSalesAndSave($lineItemId, $productId, $quantity);
		}
	}
}
