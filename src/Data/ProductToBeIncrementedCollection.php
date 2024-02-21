<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Data;

final class ProductToBeIncrementedCollection {

	/**
	 * @var AbstractProductToBeIncremented[]
	 */
	private array $collection;

	/**
	 * @param AbstractProductToBeIncremented[] $collection
	 */
	public function __construct(array $collection = []) {
		$this->collection = $collection;
	}

	/**
	 * @return AbstractProductToBeIncremented[]
	 */
	public function getCollection(): array {
		return $this->collection;
	}

	/**
	 * @param AbstractProductToBeIncremented $productToBeIncremented
	 * @return void
	 */
	public function addProductToBeIncremented(AbstractProductToBeIncremented $productToBeIncremented): void {
		$this->collection[] = $productToBeIncremented;
	}
}
