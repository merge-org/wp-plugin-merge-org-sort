<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Data\WordPress;

/**
 * @internal
 */
abstract class AbstractProduct {

	/**
	 * @var int
	 */
	protected int $id;

	/**
	 * @var array<string, array<int,int>>
	 */
	protected array $sales;

	/**
	 * @param int                            $id
	 * @param array<string, array<int, int>> $sales
	 */
	public function __construct( int $id, array $sales ) {
		$this->id    = $id;
		$this->sales = $sales;
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
	public function getSales(): array {
		return $this->sales;
	}

	/**
	 * @return string
	 */
	abstract function getType(): string;
}
