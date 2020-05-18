<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die();

/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @return string
 */
function mai_demo_exporter_content() {
	if ( ! function_exists( 'export_wp' ) ) {
		require_once ABSPATH . '/wp-admin/includes/export.php';
	}

	$args = [
		'status' => 'publish',
	];

	ob_start();
	export_wp( $args );
	header( 'Content-Disposition: inline' );
	$source = ob_get_clean();

	$dom = new DOMDocument();

	$dom->preserveWhiteSpace = true; // Switch off in production.

	$dom->loadXML( $source );

	/**
	 * @var DOMXPath $xpath
	 */
	$xpath = new DOMXPath( $dom );

	/**
	 * Remove HTML comments.
	 *
	 * @var DOMElement $comment
	 */
	foreach ( $xpath->query( '//comment()' ) as $comment ) {
		$comment->parentNode->removeChild( $comment );
	}

	/**
	 * Get all featured image IDs.
	 *
	 * @var DOMElement $meta_key
	 */
	$featured_image_ids = [];

	foreach ( $xpath->query( '//wp:meta_key' ) as $meta_key ) {
		$postmeta = $meta_key->parentNode;

		foreach ( $postmeta->childNodes as $child_node ) {
			if ( 'wp:meta_value' === $child_node->nodeName ) {
				$featured_image_ids[] = $child_node->textContent;
			}
		}
	}

	/**
	 * Get all attachment IDs.
	 *
	 * @var DOMElement $post_type
	 */
	foreach ( $xpath->query( '//wp:post_type' ) as $post_type ) {
		$c_data = $post_type->textContent;

		if ( 'attachment' === $c_data ) {
			$post    = $post_type->parentNode;
			$post_id = '';

			/**
			 * @var $child_node DOMElement
			 */
			foreach ( $post->childNodes as $child_node ) {
				if ( 'wp:post_id' === $child_node->nodeName ) {
					$post_id = $child_node->textContent;
				}
			}

			if ( ! in_array( $post_id, $featured_image_ids, true ) ) {
				$post->parentNode->removeChild( $post );
			}
		}
	}

	/**
	 * Limit posts to 9.
	 *
	 * @var DOMElement $post_type
	 */
	$counter = 1;

	foreach ( $xpath->query( '//wp:post_type' ) as $post_type ) {
		$c_data = $post_type->textContent;

		if ( 'post' === $c_data ) {
			$item = $post_type->parentNode;

			if ( 6 < $counter ) {
				$item->parentNode->removeChild( $item );
			}

			$counter++;
		}
	}

	$body = $xpath->query( '//body' )->item( 0 );

	return $dom->saveXML( $body );
}
