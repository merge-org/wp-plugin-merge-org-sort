<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service;

use MergeOrg\Sort\Exception\InvalidKeyNameSortException;

final class OptimalPostsCountFinder {

	/**
	 * @var ServerLoadCalculatorInterface
	 */
	private ServerLoadCalculatorInterface $serverLoadCalculator;

	/**
	 * @param ServerLoadCalculatorInterface $serverLoadCalculator
	 */
	public function __construct( ServerLoadCalculatorInterface $serverLoadCalculator ) {
		$this->serverLoadCalculator = $serverLoadCalculator;
	}

	/**
	 * @return int
	 * @throws InvalidKeyNameSortException
	 */
	public function getOptimalPostsCount(): int {
		$serverLoad              = $this->serverLoadCalculator->calculate();
		$optimalPostsCountMemory =
			max( 5, floor( $serverLoad->getAvailableMemory() / 5000000000 ) );

		$optimalPostsCountMemory > 50 && ( $optimalPostsCountMemory = 50 );

		return (int) $optimalPostsCountMemory;
	}
}
