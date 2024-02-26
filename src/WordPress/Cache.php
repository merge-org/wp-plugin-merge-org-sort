<?php
declare(strict_types=1);

namespace MergeOrg\Sort\WordPress;

/**
 * Class Cache
 *
 * @package MergeOrg\Sort\WordPress
 * @codeCoverageIgnore
 */
final class Cache implements CacheInterface {

	/**
	 * @param string $key
	 * @return mixed|null
	 */
	public function get(string $key) {
		if(!function_exists("wp_cache_get")) {
			return NULL;
		}

		$data = wp_cache_get($key);

		return $data ? unserialize(base64_decode($data)) : NULL;
	}

	/**
	 * @param string $key
	 * @param mixed $data
	 * @param int $ttl
	 * @return bool
	 */
	public function set(string $key, $data, int $ttl = 0): bool {
		if(!function_exists("wp_cache_set")) {
			return FALSE;
		}

		$data = base64_encode(serialize($data));

		return wp_cache_set($key, $data, "", $ttl);
	}
}
