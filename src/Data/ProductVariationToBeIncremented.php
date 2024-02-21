<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Data;

use MergeOrg\Sort\Constants;

final class ProductVariationToBeIncremented extends AbstractProductToBeIncremented {

	/**
	 * @return string
	 */
	function getType(): string {
		return Constants::POST_TYPE_PRODUCT_VARIATION;
	}
}
