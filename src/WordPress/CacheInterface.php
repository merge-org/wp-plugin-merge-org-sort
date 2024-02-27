<?php
declare(strict_types=1);

namespace MergeOrg\Sort\WordPress;

interface CacheInterface {

	/**
	 * @param string $key
	 * @return false|mixed|null
	 */
	public function get( string $key );

	/**
	 * @param string $key
	 * @param mixed  $data
	 * @param int    $ttl
	 * @return bool
	 */
	public function set( string $key, $data, int $ttl = 0 ): bool;

	/**
	 * @param string $key
	 * @return bool
	 */
	public function delete( string $key ): bool;
}
