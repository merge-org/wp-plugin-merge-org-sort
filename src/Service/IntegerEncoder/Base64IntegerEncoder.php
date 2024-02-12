<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service\IntegerEncoder;

use MergeOrg\Sort\Exception\InvalidInputForBase64IntegerEncoderException;

final class Base64IntegerEncoder implements IntegerEncoderInterface {

	/**
	 *
	 */
	public const MAX_DIGITS = 9;

	/**
	 * @param int $integer
	 * @return string
	 */
	public function encode(int $integer): string {
		return base64_encode(str_pad((string) $integer, self::MAX_DIGITS, "0", STR_PAD_LEFT));
	}

	/**
	 * @param string $encodedString
	 * @return int
	 * @throws InvalidInputForBase64IntegerEncoderException
	 */
	public function decode(string $encodedString): int {
		if(strlen($base64Decoded = (base64_decode($encodedString) ?: "")) === self::MAX_DIGITS) {
			if($base64Decoded === str_repeat("0", self::MAX_DIGITS)) {
				return 0;
			}

			$integer = (int) $base64Decoded;
			if($integer === 0) {
				throw new InvalidInputForBase64IntegerEncoderException("Decoded string is not a number");
			}

			return $integer;
		}

		throw new InvalidInputForBase64IntegerEncoderException("Invalid decoded string '$base64Decoded'");
	}
}
