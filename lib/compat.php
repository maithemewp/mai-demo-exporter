<?php

namespace MaiDemoExporter;

\add_filter( 'wpforms_post_type_args', __NAMESPACE__ . '\\enable_wpforms', 10, 1 );
/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @param array $args
 *
 * @return array
 */
function enable_wpforms( $args ) {
	$args['can_export'] = true;

	return $args;
}
