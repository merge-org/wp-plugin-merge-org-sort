<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Data\Wordpress;

use MergeOrg\Sort\Exception\InvalidLineItemInOrderCreationException;

final class Order {

	/**
	 * @var int
	 */
	private int $id;

	/**
	 * @var LineItem[]
	 */
	private array $lineItems;

	/**
	 * @var bool
	 */
	private bool $recorded;

	/**
	 * @param int $id
	 * @param LineItem[] $lineItems
	 * @param bool $recorded
	 * @throws InvalidLineItemInOrderCreationException
	 */
	public function __construct(int $id, array $lineItems = [], bool $recorded = FALSE) {
		foreach($lineItems as $lineItem) {
			if(get_class($lineItem) !== LineItem::class) {
				throw new InvalidLineItemInOrderCreationException();
			}
		}

		$this->id = $id;
		$this->lineItems = $lineItems;
		$this->recorded = $recorded;
	}

	/**
	 * @return int
	 */
	public function getId(): int {
		return $this->id;
	}

	/**
	 * @return LineItem[]
	 */
	public function getLineItems(): array {
		return $this->lineItems;
	}

	/**
	 * @return bool
	 */
	public function getRecorded(): bool {
		return $this->recorded;
	}
}
