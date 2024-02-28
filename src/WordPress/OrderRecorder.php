<?php
declare(strict_types=1);

namespace MergeOrg\Sort\WordPress;

use MergeOrg\Sort\Service\OrderRepository;
use MergeOrg\Sort\Service\ProductRepository;
use MergeOrg\Sort\Exception\InvalidKeyNameException;
use MergeOrg\Sort\Service\ProductToBeIncrementedCollectionGenerator;

/**
 * Class OrderRecorder
 *
 * @package MergeOrg\Sort\WordPress
 * @codeCoverageIgnore
 */
final class OrderRecorder {

	/**
	 * @var ProductToBeIncrementedCollectionGenerator
	 */
	private ProductToBeIncrementedCollectionGenerator $productToBeIncrementedCollectionGenerator;

	/**
	 * @var ProductRepository
	 */
	private ProductRepository $productRepository;

	/**
	 * @var OrderRepository
	 */
	private OrderRepository $orderRepository;

	/**
	 * @param ProductToBeIncrementedCollectionGenerator $productToBeIncrementedCollectionGenerator
	 * @param ProductRepository                         $productRepository
	 * @param OrderRepository                           $orderRepository
	 */
	public function __construct(
		ProductToBeIncrementedCollectionGenerator $productToBeIncrementedCollectionGenerator,
		ProductRepository $productRepository,
		OrderRepository $orderRepository
	) {
		$this->productToBeIncrementedCollectionGenerator = $productToBeIncrementedCollectionGenerator;
		$this->productRepository                         = $productRepository;
		$this->orderRepository                           = $orderRepository;
	}

	/**
	 * @param int $orderId
	 * @return void
	 * @throws InvalidKeyNameException
	 */
	public function record( int $orderId ): void {
		$productToBeIncrementedCollection = $this->productToBeIncrementedCollectionGenerator->generate( $orderId );

		foreach ( $productToBeIncrementedCollection->getCollection() as $productToBeIncremented ) {
			$this->productRepository->setProductSales(
				$productToBeIncremented->getId(),
				$productToBeIncremented->getSalesToBeUpdated()
			);
		}

		$this->orderRepository->setOrderRecorded( $orderId );
	}
}
