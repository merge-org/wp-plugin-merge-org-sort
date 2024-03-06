<?php
declare(strict_types=1);

namespace MergeOrg\WpPluginSort\Data\UnrecordedOrder;

use DateTime;
use JsonSerializable;

/**
 * @codeCoverageIgnore
 */
final class Order implements JsonSerializable {

	/**
	 * @var int
	 */
	private int $id;

	/**
	 * @var DateTime
	 */
	private DateTime $dateTime;

	/**
	 * @var LineItem[]
	 */
	private array $lineItems;

	/**
	 * @param int        $id
	 * @param DateTime   $dateTime
	 * @param LineItem[] $lineItems
	 */
	public function __construct( int $id, DateTime $dateTime, array $lineItems ) {
		$this->id        = $id;
		$this->dateTime  = $dateTime;
		$this->lineItems = $lineItems;
	}

	/**
	 * @return array<string, int|DateTime|LineItem>
	 */
	public function jsonSerialize(): array {
		return array(
			'id'        => $this->getId(),
			'dateTime'  => $this->getDateTime(),
			'lineItems' => $this->getLineItems(),
		);
	}

	/**
	 * @return int
	 */
	public function getId(): int {
		return $this->id;
	}

	/**
	 * @return DateTime
	 */
	public function getDateTime(): DateTime {
		return $this->dateTime;
	}

	/**
	 * @return LineItem[]
	 */
	public function getLineItems(): array {
		return $this->lineItems;
	}
}
