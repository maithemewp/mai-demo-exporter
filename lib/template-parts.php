<?php

namespace MaiDemoExporter;

// Prevent direct file access.
\defined( 'ABSPATH' ) || die();

/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @return string
 */
function export_template_parts() {
	if ( ! \function_exists( 'export_wp' ) ) {
		require_once ABSPATH . '/wp-admin/includes/export.php';
	}

	$args = [
		'content' => 'wp_template_part',
		'status'  => 'publish',
	];

	\ob_start();
	\export_wp( $args );
	\header( 'Content-Disposition: inline' );

	$xml = \ob_get_clean();

	return $xml;
}
