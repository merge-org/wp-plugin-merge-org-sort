<?php
declare(strict_types=1);

namespace MergeOrg\Sort\WordPress;

use MergeOrg\Sort\Service\Namer;
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
	 * @var ApiInterface
	 */
	private ApiInterface $api;

	/**
	 * @var Namer
	 */
	private Namer $namer;

	/**
	 * @param ProductToBeIncrementedCollectionGenerator $productToBeIncrementedCollectionGenerator
	 * @param ApiInterface $api
	 * @param Namer $namer
	 */
	public function __construct(ProductToBeIncrementedCollectionGenerator $productToBeIncrementedCollectionGenerator,
		ApiInterface $api,
		Namer $namer) {
		$this->productToBeIncrementedCollectionGenerator = $productToBeIncrementedCollectionGenerator;
		$this->api = $api;
		$this->namer = $namer;
	}

	/**
	 * @param int $orderId
	 * @return void
	 * @throws InvalidKeyNameSortException
	 */
	public function record(int $orderId): void {
		$productToBeIncrementedCollection = $this->productToBeIncrementedCollectionGenerator->generate($orderId);

		foreach($productToBeIncrementedCollection->getCollection() as $productToBeIncremented) {
			// TODO SPECIFIC PRODUCT METHOD
			$this->api->updatePostMeta($productToBeIncremented->getId(),
				$this->namer->getSalesMetaKeyName(),
				$productToBeIncremented->getSalesToBeUpdated());
		}

		// TODO SPECIFIC ORDER METHOD
		$this->api->updatePostMeta($orderId, $this->namer->getRecordedMetaKeyName(), "yes");
	}
}
