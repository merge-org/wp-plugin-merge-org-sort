<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service;

final class WpDataApiService implements WpDataApiServiceInterface {

	/**
	 * @param int $postId
	 * @param string $key
	 * @param bool $single
	 * @return mixed
	 */
	public function getPostMeta(int $postId, string $key, bool $single = TRUE) {
		return get_post_meta($postId, $key, $single);
	}

	/**
	 * @param int $postId
	 * @param string $metaKey
	 * @param mixed $metaValue
	 * @return void
	 */
	public function updatePostMeta(int $postId, string $metaKey, $metaValue): void {
		update_post_meta($postId, $metaKey, $metaValue);
	}
}
