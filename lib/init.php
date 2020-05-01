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
