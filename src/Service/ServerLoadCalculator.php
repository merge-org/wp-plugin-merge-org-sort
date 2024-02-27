<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service;

use MergeOrg\Sort\Data\ServerLoad;
use MergeOrg\Sort\WordPress\CacheInterface;
use MergeOrg\Sort\Exception\InvalidKeyNameSortException;

/**
 * Class ServerLoadCalculator
 *
 * @package MergeOrg\Sort\Service
 * @codeCoverageIgnore
 */
final class ServerLoadCalculator implements ServerLoadCalculatorInterface {

	/**
	 * @var CacheInterface
	 */
	private CacheInterface $cache;

	/**
	 * @var Namer
	 */
	private Namer $namer;

	/**
	 * @param CacheInterface $cache
	 * @param Namer          $namer
	 */
	public function __construct( CacheInterface $cache, Namer $namer ) {
		$this->cache = $cache;
		$this->namer = $namer;
	}

	/**
	 * @return ServerLoad
	 * @throws InvalidKeyNameSortException
	 */
	public function calculate(): ServerLoad {
		$cacheKey = $this->namer->getServerLoadCacheKey();
		if ( $serverLoad = $this->cache->get( $cacheKey ) ) {
			return $serverLoad;
		}

		// For later use
		$cpuLoad     = 0;
		$memoryLimit = wp_convert_hr_to_bytes( ini_get( 'memory_limit' ) );
		$memoryUsage = memory_get_peak_usage( true );

		$serverLoad = new ServerLoad( $cpuLoad, $memoryLimit - $memoryUsage );

		$this->cache->set( $cacheKey, $serverLoad, 60 * 60 );

		return $serverLoad;
	}
}
