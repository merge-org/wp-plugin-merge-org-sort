<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service;

interface WpDataApiServiceInterface {

	/**
	 * @param int $postId
	 * @param string $key
	 * @param bool $single
	 * @return mixed
	 */
	public function getPostMeta(int $postId, string $key, bool $single = TRUE);

	/**
	 * @param int $postId
	 * @param string $metaKey
	 * @param mixed $metaValue
	 * @return void
	 */
	public function updatePostMeta(int $postId, string $metaKey, $metaValue): void;
}
