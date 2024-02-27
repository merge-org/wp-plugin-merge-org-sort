<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Data;

use JsonSerializable;

final class ServerLoad implements JsonSerializable {

	/**
	 * @var float
	 */
	private float $cpuLoad;

	/**
	 * @var float
	 */
	private float $availableMemory;

	/**
	 * @param float $cpuLoad
	 * @param float $availableMemory
	 */
	public function __construct( float $cpuLoad, float $availableMemory ) {
		$this->cpuLoad         = $cpuLoad;
		$this->availableMemory = $availableMemory;
	}

	/**
	 * @return array<string, float>
	 */
	public function jsonSerialize(): array {
		return array(
			'cpuLoad'         => $this->getCpuLoad(),
			'availableMemory' => $this->getAvailableMemory(),
		);
	}

	/**
	 * @return float
	 */
	public function getCpuLoad(): float {
		return $this->cpuLoad;
	}

	/**
	 * @return float
	 */
	public function getAvailableMemory(): float {
		return $this->availableMemory;
	}
}
