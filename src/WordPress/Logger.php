<?php
declare(strict_types=1);

namespace MergeOrg\Sort\WordPress;

use WC_Logger_Interface;
use MergeOrg\Sort\Service\Namer;

/**
 * Class Logger
 *
 * @package MergeOrg\Sort\WordPress
 * @codeCoverageIgnore
 */
final class Logger {

	/**
	 * @var WC_Logger_Interface
	 */
	private WC_Logger_Interface $logger;

	/**
	 * @var Namer
	 */
	private Namer $namer;

	/**
	 *
	 */
	public function __construct( Namer $namer ) {
		if ( ! function_exists( 'wc_get_logger' ) || ! ( $logger = wc_get_logger() ) ) {
			return;
		}

		$this->logger = $logger;
		$this->namer  = $namer;
	}

	/**
	 * @param string $level
	 * @param string $message
	 * @return void
	 */
	public function log( string $level, string $message ): void {
		if ( ! ( $this->logger ?? null ) ) {
			return;
		}

		$this->logger->log( $level, $message, array( 'source' => $this->namer->getPluginName() ) );
	}
}
