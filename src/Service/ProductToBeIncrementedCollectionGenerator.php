<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service;

use MergeOrg\Sort\Data\ProductToBeIncremented;
use MergeOrg\Sort\Exception\InvalidKeyNameException;
use MergeOrg\Sort\Data\ProductVariationToBeIncremented;
use MergeOrg\Sort\Data\ProductToBeIncrementedCollection;

final class ProductToBeIncrementedCollectionGenerator {

	/**
	 * @var OrderRepository
	 */
	private OrderRepository $orderRepository;

	/**
	 * @var ProductRepository
	 */
	private ProductRepository $productRepository;

	/**
	 * @var SalesIncrementer
	 */
	private SalesIncrementer $salesIncrementer;

	/**
	 * @param OrderRepository   $orderRepository
	 * @param ProductRepository $productRepository
	 * @param SalesIncrementer  $salesIncrementer
	 */
	public function __construct(
		OrderRepository $orderRepository,
		ProductRepository $productRepository,
		SalesIncrementer $salesIncrementer
	) {
		$this->orderRepository   = $orderRepository;
		$this->productRepository = $productRepository;
		$this->salesIncrementer  = $salesIncrementer;
	}

	/**
	 * @param int $orderId
	 * @return ProductToBeIncrementedCollection|null
	 * @throws InvalidKeyNameException
	 */
	public function generate( int $orderId ): ?ProductToBeIncrementedCollection {
		$order = $this->orderRepository->getOrder( $orderId );
		if ( ! $order || ! $order->getId() || $order->isRecorded() ) {
			return null;
		}

		$productToBeIncrementedCollection = new ProductToBeIncrementedCollection();
		foreach ( $order->getLineItems() as $lineItem ) {
			if ( ! $lineItem->getId() ) {
				continue;
			}

			$product = $this->productRepository->getProduct( $lineItem->getProductId() );
			if ( ! $product ) {
				continue;
			}

			$productSalesToBeUpdated =
				$this->salesIncrementer->increment( $product->getSales(), $lineItem->getQuantity(), $order->getDate() );
			$productToBeIncrementedCollection->addProductToBeIncremented(
				new ProductToBeIncremented(
					$product->getId(),
					$productSalesToBeUpdated
				)
			);

			if ( $lineItem->getVariationId() ) {
				$variation = $this->productRepository->getProduct( $lineItem->getVariationId() );
				if ( ! $variation ) {
					continue;
				}
				$variationSalesToBeUpdated =
					$this->salesIncrementer->increment( $variation->getSales(), $lineItem->getQuantity(), $order->getDate() );
				$productToBeIncrementedCollection->addProductToBeIncremented(
					new ProductVariationToBeIncremented(
						$variation->getId(),
						$variationSalesToBeUpdated
					)
				);
			}
		}

		return $productToBeIncrementedCollection;
	}
}
