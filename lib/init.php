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
		'rest-api',
	]
);

function mai_demo_exporter_cache_dir() {
	$upload_dir = wp_get_upload_dir()['basedir'];

	return $upload_dir . DIRECTORY_SEPARATOR . mai_get_handle() . DIRECTORY_SEPARATOR;
}

function mai_demo_exporter_content_types() {
	return [
		'content'    => 'xml',
		'widgets'    => 'wie',
		'customizer' => 'dat',
	];
}

add_action( 'init', 'mai_demo_exporter_schedule' );
/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @return void
 */
function mai_demo_exporter_schedule() {
	if ( ! wp_next_scheduled( 'mai_demo_exporter_daily_event' ) ) {
		wp_schedule_event( time(), 'daily', 'mai_demo_exporter_daily_event' );
	}

	$cache_dir = mai_demo_exporter_cache_dir();
	$types     = mai_demo_exporter_content_types();

	foreach ( $types as $content_type => $file_type ) {
		$file = "$cache_dir/$content_type.$file_type";

		if ( ! file_exists( $file ) ) {
			mai_demo_exporter_generate_files();
		}
	}
}

add_action( 'mai_demo_exporter_daily_event', 'mai_demo_exporter_generate_files' );
/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @return void
 */
function mai_demo_exporter_generate_files() {
	include_once ABSPATH . 'wp-admin/includes/file.php';
	\WP_Filesystem();
	global $wp_filesystem;

	$cache_dir = mai_demo_exporter_cache_dir();
	$types     = mai_demo_exporter_content_types();

	if ( ! is_dir( $cache_dir ) ) {
		wp_mkdir_p( $cache_dir );
	}

	foreach ( $types as $content_type => $file_type ) {
		$file = "$cache_dir/$content_type.$file_type";
		$function = "mai_demo_exporter_{$content_type}";

		$wp_filesystem->put_contents( $file, $function() );
	}
}

