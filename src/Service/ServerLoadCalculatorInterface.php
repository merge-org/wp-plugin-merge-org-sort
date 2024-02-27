<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service;

use MergeOrg\Sort\Data\ServerLoad;

interface ServerLoadCalculatorInterface {

	/**
	 * @return ServerLoad
	 */
	public function calculate(): ServerLoad;
}
