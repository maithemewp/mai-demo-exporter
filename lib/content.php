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
