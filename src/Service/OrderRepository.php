<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service;

use MergeOrg\Sort\Data\Order;
use MergeOrg\Sort\Data\LineItem;
use MergeOrg\Sort\WordPress\ApiInterface;
use MergeOrg\Sort\WordPress\CacheInterface;
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
	 * @var CacheInterface
	 */
	private CacheInterface $cache;

	/**
	 * @param ApiInterface   $api
	 * @param Namer          $namer
	 * @param CacheInterface $cache
	 */
	public function __construct( ApiInterface $api, Namer $namer, CacheInterface $cache ) {
		$this->api   = $api;
		$this->namer = $namer;
		$this->cache = $cache;
	}

	/**
	 * @return Order[]
	 * @throws InvalidKeyNameException
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
	 * @throws InvalidKeyNameException
	 */
	public function getOrder( int $orderId ): ?Order {
		$cacheKey = $this->namer->getOrderCacheKey( $orderId );
		if ( $order = $this->cache->get( $cacheKey ) ) {
			// @codeCoverageIgnoreStart
			return $order;
			// @codeCoverageIgnoreEnd
		}

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

		$order = new Order( $order->getId(), $order->getDate(), $order->getStatus(), $lineItems, $order->isRecorded() );

		$this->cache->set( $cacheKey, $order );

		return $order;
	}

	/**
	 * @param int $orderId
	 * @return bool
	 * @throws InvalidKeyNameException
	 * @codeCoverageIgnore
	 */
	public function setOrderRecorded( int $orderId ): bool {
		$cacheKey = $this->namer->getOrderCacheKey( $orderId );
		$this->cache->delete( $cacheKey );

		$update = $this->api->updateOrderMeta( $orderId, $this->namer->getRecordedMetaKeyName(), 'yes' );

		// Place back in cache
		$this->getOrder( $orderId );

		return $update;
	}
}
