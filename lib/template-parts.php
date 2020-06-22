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
		'content' => 'wp_template_part',
		'status'  => 'publish',
	];

	ob_start();
	export_wp( $args );
	$source = ob_get_clean();

	return $source;
}
