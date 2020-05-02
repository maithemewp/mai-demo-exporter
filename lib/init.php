<?php

defined( 'ABSPATH' ) || die();

array_map(
	function ( $file ) {
		require_once __DIR__ . DIRECTORY_SEPARATOR . $file . '.php';
	},
	[
		'content',
		'widgets',
		'customizer',
	]
);

add_filter( 'template_include', 'mai_demo_exporter', 10, 1 );

function mai_demo_exporter( $template ) {
	if ( isset( $_REQUEST['mai-export'] ) ) {
		$types = [ 'content', 'widgets', 'customizer' ];
		$type  = sanitize_text_field( $_REQUEST['mai-export'] );

		if ( in_array( $type, $types, true ) ) {
			$template = null;
			$function = "mai_demo_exporter_{$type}";

			echo $function();
		}
	}

	return $template;
}

add_action( 'rest_api_init', 'mai_demo_exporter_list_demos' );

function mai_demo_exporter_list_demos() {
	register_rest_route( 'mai-demo-exporter/v2', '/sites/', [
		'methods'  => 'GET',
		'callback' => 'mai_demo_exporter_list_demos_callback',
	] );
}

function mai_demo_exporter_list_demos_callback( $request ) {
	$response = new WP_Error( 'not_multisite', __( 'Not a multisite install' ) );
	$sites    = get_sites();

	if ( $sites && isset( $request['theme'] ) ) {
		$response = [];
		$theme    = sanitize_text_field( $request['theme'] );

		foreach ( $sites as $site ) {
			if ( mai_has_string( $theme, $site->path ) ) {
				$response[] = str_replace( [ $theme, '-', '\\', '/' ], '', $site->path );
			}
		}
	}

	return new WP_REST_Response( $response );
}
