<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Data;

use MergeOrg\Sort\Constants;

/**
 * Class ProductToBeIncremented
 *
 * @package MergeOrg\Sort\Data
 * @internal
 */
final class ProductToBeIncremented extends AbstractProductToBeIncremented {

	/**
	 * @return string
	 */
	function getType(): string {
		return Constants::POST_TYPE_PRODUCT;
	}
}
