<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die();

add_action( 'admin_menu', 'mai_demo_exporter_admin_menu', 999 );
/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @return void
 */
function mai_demo_exporter_admin_menu() {
	$args = [
		'parent_slug' => 'mai-engine',
		'page_title'  => __( 'Demo Generator', 'mai-engine' ),
		'menu_title'  => __( 'Demo Generator', 'mai-engine' ),
		'capability'  => 'manage_options',
		'menu_slug'   => 'mai-demo-exporter',
		'function'    => 'mai_demo_exporter_admin_page',
	];

	add_submenu_page( ...array_values( $args ) );
}

function mai_demo_exporter_admin_page() {
	global $title;

	$blog_id    = get_current_blog_id();
	$export_url = get_home_url( $blog_id, 'wp-content/uploads/sites/' . $blog_id . '/mai-engine/' );

	?>
	<div class="wrap">
		<h1><?php echo $title; ?></h1>
		<p>The content for this demo will be exported to the following URL
			<a target="_blank" href="<?php echo $export_url; ?>"><?php echo $export_url; ?></a>
		</p>
		<p>Please select which content types to regenerate:</p>
		<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
			<?php wp_nonce_field( 'mai_demo_export', 'mai_demo_export_nonce' ); ?>
			<input type="hidden" name="action" value="mai_demo_export">
			<ul>
				<?php foreach ( [ 'content', 'widgets', 'customizer' ] as $type ) : ?>
					<li>
						<label for="<?php echo $type; ?>">
							<input type="checkbox" id="<?php echo $type; ?>" name="<?php echo $type; ?>" checked>
							<?php echo ucwords( $type ); ?>
						</label>
					</li>
				<?php endforeach; ?>
			</ul>
			<input class="button button-primary button-hero" type="submit" value="Regenerate demo content">
		</form>
	</div>
	<?php
}

add_action( 'admin_post_mai_demo_export', 'mai_demo_export_do_export' );
/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @return void
 */
function mai_demo_export_do_export() {
	if ( ! isset( $_POST['mai_demo_export_nonce'] ) || ! wp_verify_nonce( $_POST['mai_demo_export_nonce'], 'mai_demo_export' ) ) {
		print 'Sorry, your nonce did not verify.';
		exit;
	}

	include_once ABSPATH . 'wp-admin/includes/file.php';
	\WP_Filesystem();
	global $wp_filesystem;

	$cache_dir     = wp_upload_dir()['basedir'] . '/mai-engine/';
	$content_types = [
		'content'    => [
			'file_type' => 'xml',
			'selected'  => isset( $_REQUEST['content'] ) ? sanitize_key( $_REQUEST['content'] ) : false,
		],
		'widgets'    => [
			'file_type' => 'json',
			'selected'  => isset( $_REQUEST['widgets'] ) ? sanitize_key( $_REQUEST['widgets'] ) : false,
		],
		'customizer' => [
			'file_type' => 'dat',
			'selected'  => isset( $_REQUEST['customizer'] ) ? sanitize_key( $_REQUEST['customizer'] ) : false,
		],
	];

	if ( ! is_dir( $cache_dir ) ) {
		wp_mkdir_p( $cache_dir );
	}

	foreach ( $content_types as $content_type => $data ) {
		if ( $data['selected'] ) {
			$file     = "$cache_dir/$content_type.{$data['file_type']}";
			$function = "mai_demo_exporter_{$content_type}";

			$wp_filesystem->put_contents( $file, $function() );
		}
	}

	wp_redirect( admin_url( 'admin.php?page=mai-demo-exporter' ) );
}
