<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service\Module;

use MergeOrg\Sort\Wordpress\Api\Api;
use MergeOrg\Sort\Wordpress\Api\ApiInterface;
use MergeOrg\Sort\Wordpress\Service\OrderRecorder;
use MergeOrg\Sort\Service\Repository\ProductRepository;
use MergeOrg\Sort\Service\Sales\ProductSalesIncrementer;
use MergeOrg\Sort\Service\Sales\ProductSalesIncrementerInterface;
use MergeOrg\Sort\Service\IntegerEncoder\IntegerEncoderInterface;
use MergeOrg\Sort\Service\IntegerEncoder\LexiconBasedIntegerEncoder;

final class Container {

	/**
	 * @var bool
	 */
	private static bool $init = FALSE;

	/**
	 * @var bool
	 */
	private bool $got = FALSE;

	/**
	 * @var array<string, mixed>
	 */
	private array $definitions = [];

	/**
	 *
	 */
	public function __construct() {
		if(self::$init) {
			return;
		}

		self::$init = TRUE;
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function get(string $key) {
		if(!$this->got) {
			# Product Sales Incrementer
			$this->definitions[ProductSalesIncrementerInterface::class] =
			$productSalesIncrementer = new ProductSalesIncrementer();

			# Wordpress API
			$this->definitions[ApiInterface::class] =
			$api = new Api();

			# Product Repository
			$this->definitions[ProductRepository::class] =
			$productRepository = new ProductRepository($api, $productSalesIncrementer);

			# Order Recorder
			$this->definitions[OrderRecorder::class] = new OrderRecorder($api, $productRepository);
		}

		$this->got = TRUE;

		return $this->definitions[$key];
	}
}
