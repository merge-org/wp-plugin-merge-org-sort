<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service;

use MergeOrg\Sort\WordPress\ApiInterface;
use MergeOrg\Sort\Data\ProductToBeIncremented;
use MergeOrg\Sort\Data\ProductVariationToBeIncremented;
use MergeOrg\Sort\Data\ProductToBeIncrementedCollection;

final class ProductToBeIncrementedCollectionGenerator {

	/**
	 * @var ApiInterface
	 */
	private ApiInterface $api;

	/**
	 * @var SalesIncrementer
	 */
	private SalesIncrementer $salesIncrementer;

	/**
	 * @param ApiInterface $api
	 * @param SalesIncrementer $salesIncrementer
	 */
	public function __construct(ApiInterface $api, SalesIncrementer $salesIncrementer) {
		$this->api = $api;
		$this->salesIncrementer = $salesIncrementer;
	}

	/**
	 * @param int $orderId
	 * @return ProductToBeIncrementedCollection|null
	 */
	public function generate(int $orderId): ?ProductToBeIncrementedCollection {
		$order = $this->api->getOrder($orderId);
		if(!$order || !$order->getId() || $order->isRecorded()) {
			return NULL;
		}

		$productToBeIncrementedCollection = new ProductToBeIncrementedCollection();
		foreach($order->getLineItems() as $lineItem) {
			if(!$lineItem->getId()) {
				continue;
			}

			$product = $this->api->getProduct($lineItem->getProductId());
			if(!$product) {
				continue;
			}

			$productSalesToBeUpdated =
				$this->salesIncrementer->increment($product->getSales(), $lineItem->getQuantity(), $order->getDate());
			$productToBeIncrementedCollection->addProductToBeIncremented(new ProductToBeIncremented($product->getId(),
				$productSalesToBeUpdated));
			if($lineItem->getVariationId()) {
				$variation = $this->api->getProduct($lineItem->getVariationId());
				if(!$variation) {
					continue;
				}
				$variationSalesToBeUpdated =
					$this->salesIncrementer->increment($variation->getSales(), $lineItem->getQuantity(), $order->getDate());
				$productToBeIncrementedCollection->addProductToBeIncremented(new ProductVariationToBeIncremented($variation->getId(),
					$variationSalesToBeUpdated));
			}
		}

		return $productToBeIncrementedCollection;
	}
}
