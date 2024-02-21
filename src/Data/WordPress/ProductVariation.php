<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Data\WordPress;

use MergeOrg\Sort\Constants;

final class ProductVariation extends AbstractProduct {

	/**
	 * @return string
	 */
	function getType(): string {
		return Constants::POST_TYPE_PRODUCT_VARIATION;
	}
}
