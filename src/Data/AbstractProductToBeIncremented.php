<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Data;

abstract class AbstractProductToBeIncremented {

	/**
	 * @var int
	 */
	private int $id;

	/**
	 * @var array<string, array<int, int>>
	 */
	private array $salesToBeUpdated;

	/**
	 * @param int $id
	 * @param array<string, array<int, int>> $salesToBeUpdated
	 */
	public function __construct(int $id, array $salesToBeUpdated) {
		$this->id = $id;
		$this->salesToBeUpdated = $salesToBeUpdated;
	}

	/**
	 * @return int
	 */
	public function getId(): int {
		return $this->id;
	}

	/**
	 * @return array<string, array<int, int>>
	 */
	public function getSalesToBeUpdated(): array {
		return $this->salesToBeUpdated;
	}

	/**
	 * @return string
	 */
	abstract function getType(): string;
}