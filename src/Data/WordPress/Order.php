<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Data\WordPress;

final class Order {

	/**
	 * @var int
	 */
	private int $id;

	/**
	 * @var string
	 */
	private string $date;

	/**
	 * @var LineItem[]
	 */
	private array $lineItems;

	/**
	 * @var bool
	 */
	private bool $recorded;

	/**
	 * @param int        $id
	 * @param string     $date
	 * @param LineItem[] $lineItems
	 * @param bool       $recorded
	 */
	public function __construct( int $id, string $date, array $lineItems, bool $recorded ) {
		$this->id        = $id;
		$this->date      = $date;
		$this->lineItems = $lineItems;
		$this->recorded  = $recorded;
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
	public function getDate(): string {
		return $this->date;
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
	public function isRecorded(): bool {
		return $this->recorded;
	}
}
