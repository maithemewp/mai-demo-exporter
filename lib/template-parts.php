<?php

namespace MaiDemoExporter;

// Prevent direct file access.
\defined( 'ABSPATH' ) || die();

\add_filter( 'register_post_type_args', __NAMESPACE__ . '\\prevent_template_part_export', 10, 2 );
/**
 * Prevent template parts being included in content.xml.
 *
 * @since 1.0.0
 *
 * @param array  $args      Post type args.
 * @param string $post_type Post type name.
 *
 * @return array
 */
function prevent_template_part_export( $args, $post_type ) {
	if ( 'mai_template_part' === $post_type ) {
		$args['can_export'] = false;
	}

	return $args;
}

/**
 * Returns all template part data in JSON object.
 *
 * @since 1.0.0
 *
 * @return string
 */
function export_template_parts() {
	return json_encode(
		get_posts(
			[
				'numberposts' => -1,
				'post_type'   => 'mai_template_part',
				'post_status' => 'publish',
			]
		)
	);
}
