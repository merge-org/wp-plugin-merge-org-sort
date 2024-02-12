<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Data\Wordpress;

final class Product {

	/**
	 * @var int
	 */
	private int $id;

	/**
	 * @var string
	 */
	private string $type;

	/**
	 * @var array<string, array<int>>
	 */
	private array $sales;

	/**
	 * @var bool
	 */
	private bool $excludeFromSorting;

	/**
	 * @var int
	 */
	private int $previousOrder;

	/**
	 * @param int $id
	 * @param string $type
	 * @param array<string, array<int>> $sales
	 * @param bool $excludeFromSorting
	 * @param int $previousOrder
	 */
	public function __construct(int $id, string $type, array $sales, bool $excludeFromSorting, int $previousOrder) {
		$this->id = $id;
		$this->type = $type;
		$this->sales = $sales;
		$this->excludeFromSorting = $excludeFromSorting;
		$this->previousOrder = $previousOrder;
	}

	/**
	 * @return int
	 */
	public function getId(): int {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getType(): string {
		return $this->type;
	}

	/**
	 * @return array<string, array<int>>
	 */
	public function getSales(): array {
		return $this->sales;
	}

	/**
	 * @return bool
	 */
	public function getExcludeFromSorting(): bool {
		return $this->excludeFromSorting;
	}

	/**
	 * @return int
	 */
	public function getPreviousOrder(): int {
		return $this->previousOrder;
	}

}
