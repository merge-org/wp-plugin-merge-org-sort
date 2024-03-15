<?php
declare(strict_types=1);

namespace MergeOrg\WpPluginSort\Data\UnrecordedOrder;

use JsonSerializable;
use MergeOrg\WpPluginSort\WordPress\ApiInterface;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractProduct implements JsonSerializable {

	/**
	 * @var int
	 */
	private int $id;

	/**
	 * @var array<string, array<string, int>>
	 */
	private array $sales;

	/**
	 * @param int                               $id
	 * @param array<string, array<string, int>> $sales
	 */
	public function __construct( int $id, array $sales ) {
		$this->id    = $id;
		$this->sales = $sales;
	}

	/**
	 * @return array<string, int|array<string, array<string, int>>>
	 */
	public function jsonSerialize(): array {
		return array(
			'id'    => $this->getId(),
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
	 * @return array<string, array<string, int>>
	 */
	public function getSales( bool $cached = true, ?ApiInterface $api = null ): array {
		return $cached ? $this->sales : $api->getProductSales( $this->getId() );
	}
}
