<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service\IntegerEncoder;

use MergeOrg\Sort\Exception\InvalidInputForIntegerEncoderException;

interface IntegerEncoderInterface {

	/**
	 * @param int $integer
	 * @return string
	 */
	public function encode(int $integer): string;

	/**
	 * @param string $encodedString
	 * @return int
	 * @throws InvalidInputForIntegerEncoderException
	 */
	public function decode(string $encodedString): int;
}
