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
	 * Limit posts to 9.
	 *
	 * @var DOMElement $post_type
	 */
	$counter = 1;

	foreach ( $xpath->query( '//wp:post_type' ) as $post_type ) {
		$item  = $post_type->parentNode;
		$inner = $post_type->textContent;

		if ( 'post' === $inner ) {
			if ( 6 < $counter ) {
				$item->parentNode->removeChild( $item );
			}

			$counter++;
		}
	}

	$body = $xpath->query( '//body' )->item( 0 );

	return $dom->saveXML( $body );
}
