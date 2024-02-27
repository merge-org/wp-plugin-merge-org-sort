<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Data\WordPress;

use JsonSerializable;

/**
 * @internal
 */
abstract class AbstractProduct implements JsonSerializable {

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
	 * @return array<string, int|string|array<int, string>>
	 */
	public function jsonSerialize(): array {
		return array(
			'id'    => $this->getId(),
			'type'  => $this->getType(),
			'sales' => $this->getSales(),
		);
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
	abstract function getType(): string;

	/**
	 * @return array<string, array<int, int>>
	 */
	public function getSales(): array {
		return $this->sales;
	}
}
