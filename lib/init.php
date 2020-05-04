<?php

defined( 'ABSPATH' ) || die();

// Load includes.
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

/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @return string
 */
function mai_demo_exporter_cache_dir() {
	return wp_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . mai_get_handle() . DIRECTORY_SEPARATOR;
}

/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @return array
 */
function mai_demo_exporter_content_types() {
	return [
		'content'    => 'xml',
		'widgets'    => 'wie',
		'customizer' => 'dat',
	];
}

add_action( 'after_setup_theme', 'mai_demo_exporter_schedule' );
/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @return void
 */
function mai_demo_exporter_schedule() {
	$cache_dir   = mai_demo_exporter_cache_dir();
	$types       = mai_demo_exporter_content_types();
	$files_exist = true;

	foreach ( $types as $content_type => $file_type ) {
		$file = "$cache_dir/$content_type.$file_type";

		if ( ! file_exists( $file ) ) {
			$files_exist = false;
		}
	}

	if ( ! $files_exist ) {
		do_action( 'mai_demo_exporter_daily_event' );
	}

	if ( ! wp_next_scheduled( 'mai_demo_exporter_daily_event' ) ) {
		wp_schedule_event( time(), 'twicedaily', 'mai_demo_exporter_daily_event' );
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
		$file     = "$cache_dir/$content_type.$file_type";
		$function = "mai_demo_exporter_{$content_type}";

		$wp_filesystem->put_contents( $file, $function() );
	}
}
