<?php
declare(strict_types=1);

namespace MergeOrg\Sort\WordPress;

use MergeOrg\Sort\Service\ProductRepository;
use MergeOrg\Sort\Exception\InvalidKeyNameSortException;
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
	 * @param ProductToBeIncrementedCollectionGenerator $productToBeIncrementedCollectionGenerator
	 * @param ProductRepository                         $productRepository
	 */
	public function __construct(
		ProductToBeIncrementedCollectionGenerator $productToBeIncrementedCollectionGenerator,
		ProductRepository $productRepository
	) {
		$this->productToBeIncrementedCollectionGenerator = $productToBeIncrementedCollectionGenerator;
		$this->productRepository                         = $productRepository;
	}

	/**
	 * @param int $orderId
	 * @return void
	 * @throws InvalidKeyNameSortException
	 */
	public function record( int $orderId ): void {
		$productToBeIncrementedCollection = $this->productToBeIncrementedCollectionGenerator->generate( $orderId );

		foreach ( $productToBeIncrementedCollection->getCollection() as $productToBeIncremented ) {
			$this->productRepository->setProductSales(
				$productToBeIncremented->getId(),
				$productToBeIncremented->getSalesToBeUpdated()
			);
		}

		$this->productRepository->setOrderRecorded( $orderId );
	}
}
