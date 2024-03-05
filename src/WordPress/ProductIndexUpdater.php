<?php
declare(strict_types=1);

namespace MergeOrg\Sort\WordPress;

use MergeOrg\Sort\Data\Product;
use MergeOrg\Sort\Service\Namer;
use MergeOrg\Sort\Exception\InvalidKeyNameException;

final class ProductIndexUpdater {

	/**
	 * @var Namer
	 */
	private Namer $namer;

	/**
	 * @var ApiInterface
	 */
	private ApiInterface $api;

	/**
	 * @param Namer        $namer
	 * @param ApiInterface $api
	 */
	public function __construct( Namer $namer, ApiInterface $api ) {
		$this->namer = $namer;
		$this->api   = $api;
	}

	/**
	 * @param Product $product
	 * @return void
	 * @throws InvalidKeyNameException
	 */
	public function update( Product $product ): void {
		foreach ( $product->getIndexesMetaKeys( $this->namer ) as $indexesMetaKey => $sales ) {
			$this->api->updateProductMeta( $product->getId(), $indexesMetaKey, $sales );
		}

		$this->api->updateProductMeta( $product->getId(), $this->namer->getLastIndexUpdateMetaKeyName(), date( 'Y-m-d H:i:s' ) );
	}
}
