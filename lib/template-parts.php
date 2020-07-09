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
	return json_encode(
		get_posts(
			[
				'numberposts' => -1,
				'post_type'   => 'wp_template_part',
				'post_status' => 'publish',
			]
		)
	);
}
