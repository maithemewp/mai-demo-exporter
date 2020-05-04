<?php


add_action( 'rest_api_init', 'mai_demo_exporter_list_demos' );
/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @return void
 */
function mai_demo_exporter_list_demos() {
	register_rest_route( 'mai-demo-exporter/v2', '/sites/', [
		'methods'  => 'GET',
		'callback' => 'mai_demo_exporter_list_demos_callback',
	] );
}

/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @param $request
 *
 * @return WP_REST_Response
 */
function mai_demo_exporter_list_demos_callback( $request ) {
	$response = new WP_Error( 'not_multisite', __( 'Not a multisite install' ) );
	$sites    = get_sites();

	if ( $sites && isset( $request['theme'] ) ) {
		$response = [];
		$theme    = sanitize_text_field( $request['theme'] );

		foreach ( $sites as $site ) {
			if ( mai_has_string( $theme, $site->path ) ) {
				$response[ str_replace( [ $theme, '-', '\\', '/' ], '', $site->path ) ] = $site->id;
			}
		}
	}

	return new WP_REST_Response( $response );
}
