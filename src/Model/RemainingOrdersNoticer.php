<?php
declare(strict_types=1);

namespace MergeOrg\WpPluginSort\Model;

use Exception;
use MergeOrg\WpPluginSort\WordPress\ApiInterface;

/**
 * @codeCoverageIgnore
 */
final class RemainingOrdersNoticer {

	/**
	 * @var ApiInterface
	 */
	private ApiInterface $api;

	/**
	 * @param ApiInterface $api
	 */
	public function __construct( ApiInterface $api ) {
		$this->api = $api;
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	public function notice(): void {
		$remainingOrders = $this->api->getUnrecordedOrdersCount();

		if ( ! $remainingOrders ) {
			return;
		}

		$class   = 'notice notice-info';
		$message = __( 'Currently updating product sales. %d orders are still queued for processing', 'merge-org-sort' );

		$message = sprintf( $message, $remainingOrders );

		printf( '<div class="%1$s"><p><b>SORT</b></p><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}
}
