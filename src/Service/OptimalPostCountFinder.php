<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service;

final class OptimalPostCountFinder {

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
	 */
	public function getOptimalPostsCount(): int {
		$serverLoad             = $this->serverLoadCalculator->calculate();
		$optimalPostCountMemory =
			max( 5, floor( $serverLoad->getAvailableMemory() / 5000000000 ) );

		$optimalPostCountMemory > 50 && ( $optimalPostCountMemory = 50 );

		return (int) floor( $optimalPostCountMemory / 5 ) * 5;
	}
}
