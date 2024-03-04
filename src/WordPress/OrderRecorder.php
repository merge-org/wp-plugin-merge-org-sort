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
	 * @var Logger
	 */
	private Logger $logger;

	/**
	 * @param ProductToBeIncrementedCollectionGenerator $productToBeIncrementedCollectionGenerator
	 * @param ProductRepository                         $productRepository
	 * @param OrderRepository                           $orderRepository
	 * @param Logger                                    $logger
	 */
	public function __construct(
		ProductToBeIncrementedCollectionGenerator $productToBeIncrementedCollectionGenerator,
		ProductRepository $productRepository,
		OrderRepository $orderRepository,
		Logger $logger
	) {
		$this->productToBeIncrementedCollectionGenerator = $productToBeIncrementedCollectionGenerator;
		$this->productRepository                         = $productRepository;
		$this->orderRepository                           = $orderRepository;
		$this->logger                                    = $logger;
	}

	/**
	 * @param int $orderId
	 * @return void
	 * @throws InvalidKeyNameException
	 */
	public function record( int $orderId ): void {
		$this->logger->log( 'info', "Starting gathering info for order '$orderId'" );
		$productToBeIncrementedCollection = $this->productToBeIncrementedCollectionGenerator->generate( $orderId );

		$productsCount = count( $products = $productToBeIncrementedCollection->getCollection() );

		$this->logger->log( 'info', "Order '$orderId' has '$productsCount' products" );

		foreach ( $products as $productToBeIncremented ) {
			$this->logger->log( 'info', "Trying to update sales for product '{$productToBeIncremented->getId()}'" );
			$this->productRepository->setProductSales(
				$productToBeIncremented->getId(),
				$productToBeIncremented->getSalesToBeUpdated()
			);
		}

		$this->orderRepository->setOrderRecorded( $orderId );
	}
}
