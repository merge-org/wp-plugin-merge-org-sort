<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service;

use MergeOrg\Sort\Data\Order;
use MergeOrg\Sort\Data\LineItem;
use MergeOrg\Sort\WordPress\ApiInterface;
use MergeOrg\Sort\Exception\InvalidKeyNameException;

final class OrderRepository {

	/**
	 * @var ApiInterface
	 */
	private ApiInterface $api;

	/**
	 * @var Namer
	 */
	private Namer $namer;

	/**
	 * @param ApiInterface $api
	 * @param Namer        $namer
	 */
	public function __construct( ApiInterface $api, Namer $namer ) {
		$this->api   = $api;
		$this->namer = $namer;
	}

	/**
	 * @return Order[]
	 */
	public function getOrdersNotRecorded(): array {
		$orders_ = $this->api->getOrdersNotRecorded();
		$orders  = array();

		foreach ( $orders_ as $order ) {
			$orders[] = $this->getOrder( $order->getId() );
		}

		return $orders;
	}

	/**
	 * @param int $orderId
	 * @return Order|null
	 */
	public function getOrder( int $orderId ): ?Order {
		if ( ! $order = $this->api->getOrder( $orderId ) ) {
			return null;
		}

		$lineItems = array();
		foreach ( $order->getLineItems() as $lineItem ) {
			$lineItems[] =
				new LineItem(
					$lineItem->getId(),
					$lineItem->getProductId(),
					$lineItem->getQuantity(),
					$lineItem->getVariationId()
				);
		}

		return new Order( $order->getId(), $order->getDate(), $order->getStatus(), $lineItems, $order->isRecorded() );
	}

	/**
	 * @param int $orderId
	 * @return bool
	 * @throws InvalidKeyNameException
	 * @codeCoverageIgnore
	 */
	public function setOrderRecorded( int $orderId ): bool {
		return $this->api->updateOrderMeta( $orderId, $this->namer->getRecordedMetaKeyName(), 'yes' );
	}
}
