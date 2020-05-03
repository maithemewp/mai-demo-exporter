<?php

defined( 'ABSPATH' ) || die();

function mai_demo_exporter_content() {
	if ( ! function_exists( 'export_wp' ) ) {
		require_once ABSPATH . '/wp-admin/includes/export.php';
	}

	ob_start();
	export_wp();
	header( 'Content-Disposition: inline' );

	return ob_get_clean();
}

add_filter('wpforms_post_type_args', 'mai_demo_exporter_enable_wpforms', 10, 1);
/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @param array $args
 *
 * @return void
 */
function mai_demo_exporter_enable_wpforms($args) {
	$args['can_export'] = true;

	return $args;
}
