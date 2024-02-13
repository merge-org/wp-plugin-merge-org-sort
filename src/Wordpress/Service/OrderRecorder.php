<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Wordpress\Service;

use MergeOrg\Sort\Constants;
use MergeOrg\Sort\Wordpress\Api\ApiInterface;
use MergeOrg\Sort\Service\Repository\ProductRepository;
use MergeOrg\Sort\Exception\InvalidSalesPeriodInProductConstructionException;
use MergeOrg\Sort\Exception\InvalidPeriodInDaysInSalesPeriodCreationException;

final class OrderRecorder {

	/**
	 * @var ProductRepository
	 */
	protected ProductRepository $productRepository;

	/**
	 * @var ApiInterface
	 */
	private ApiInterface $api;

	/**
	 * @param ApiInterface $api
	 * @param ProductRepository $productRepository
	 */
	public function __construct(ApiInterface $api, ProductRepository $productRepository) {
		$this->api = $api;
		$this->productRepository = $productRepository;
	}

	/**
	 * @param int $orderId
	 * @return void
	 * @throws InvalidPeriodInDaysInSalesPeriodCreationException
	 * @throws InvalidSalesPeriodInProductConstructionException
	 */
	public function record(int $orderId): void {
		$order = $this->api->getOrder($orderId);
		if(!$order || $order->getRecorded()) {
			return;
		}

		foreach($order->getLineItems() as $lineItem) {
			$product = $this->productRepository->get($lineItem->getProductId());
			$quantity = $lineItem->getQuantity();
			if($product && $quantity) {
				$this->productRepository->incrementProductSales($product->getId(), $quantity);
			}

			if($variationId = $lineItem->getVariationId()) {
				$variation = $this->productRepository->get($variationId);
				if($variation && $quantity) {
					$this->productRepository->incrementProductSales($variation->getId(), $quantity);
				}
			}

			$product = $this->productRepository->get($lineItem->getProductId(), TRUE);
			foreach($product->getSalesPeriods() as $salesPeriod) {
				$salesPeriodMetaKey = Constants::META_FIELD_PRODUCT_SALES . "-{$salesPeriod->getPeriodInDays()}";
				$this->api->updatePostMeta($product->getId(), $salesPeriodMetaKey, $salesPeriod->getSales());
			}
		}

		$this->api->setOrderRecorded($orderId);
	}
}
