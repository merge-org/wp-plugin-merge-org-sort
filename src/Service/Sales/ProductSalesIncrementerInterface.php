<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service\Sales;

interface ProductSalesIncrementerInterface {

	/**
	 * @param array<string, array<int>> $sales
	 * @param int $quantity
	 * @param string $date
	 * @return array<string, array<int>>
	 */
	public function increment(array $sales, int $quantity, string $date = "TODAY"): array;
}
