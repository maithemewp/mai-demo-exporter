<?php

namespace MaiDemoExporter;

// Prevent direct file access.
\defined( 'ABSPATH' ) || die();

\add_action( 'admin_menu', __NAMESPACE__ . '\\admin_menu', 999 );
/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @return void
 */
function admin_menu() {
	$args = [
		'parent_slug' => 'mai-theme',
		'page_title'  => __( 'Demo Generator', 'mai-engine' ),
		'menu_title'  => __( 'Demo Generator', 'mai-engine' ),
		'capability'  => 'manage_options',
		'menu_slug'   => 'mai-demo-exporter',
		'function'    => __NAMESPACE__ . '\\admin_page',
	];

	\add_submenu_page( ...\array_values( $args ) );
}

function admin_page() {
	global $title;

	$blog_id    = \get_current_blog_id();
	$export_url = \get_home_url( $blog_id, 'wp-content/uploads/sites/' . $blog_id . '/mai-engine/' );

	?>
	<div class="wrap">
		<h1><?php echo $title; ?></h1>
		<p>The content for this demo will be exported to the following URL
			<a target="_blank" href="<?php echo $export_url; ?>"><?php echo $export_url; ?></a>
		</p>
		<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
			<?php wp_nonce_field( 'mai_demo_export', 'mai_demo_export_nonce' ); ?>
			<input type="hidden" name="action" value="mai_demo_export">
			<input class="button button-primary button-hero" type="submit" value="Regenerate demo content">
		</form>
	</div>
	<?php
}

\add_action( 'admin_post_mai_demo_export', __NAMESPACE__ . '\\do_export' );
/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @return void
 */
function do_export() {
	if ( ! isset( $_POST['mai_demo_export_nonce'] ) || ! \wp_verify_nonce( $_POST['mai_demo_export_nonce'], 'mai_demo_export' ) ) {
		print 'Sorry, your nonce did not verify.';
		exit;
	}

	include_once ABSPATH . 'wp-admin/includes/file.php';
	\WP_Filesystem();
	global $wp_filesystem;

	$cache_dir = \wp_upload_dir()['basedir'] . '/mai-engine/';

	if ( ! \is_dir( $cache_dir ) ) {
		\wp_mkdir_p( $cache_dir );
	}

	$wp_filesystem->put_contents( "$cache_dir/content.xml", export_content() );
	$wp_filesystem->put_contents( "$cache_dir/customizer.dat", export_customizer() );

	\wp_redirect( \admin_url( 'admin.php?page=mai-demo-exporter&success=1' ) );
}

add_action( 'admin_notices', __NAMESPACE__ . '\\update_notice' );
/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @return void
 */
function update_notice() {
	if ( ! isset( $_GET['page'] ) || 'mai-demo-exporter' !== \sanitize_key( $_GET['page'] ) ) {
		return;
	}

	if ( ! isset( $_GET['success'] ) || ! \sanitize_key( $_GET['success'] ) ) {
		return;
	}

	?>
	<div class="notice notice-success is-dismissible">
		<p><?php _e( 'Content regenerated!', 'mai-demo-exporter' ); ?></p>
	</div>
	<?php
}

\add_action( 'admin_bar_menu', __NAMESPACE__ . '\\site_id_in_admin_bar', 40, 1 );
/**
 * Show site ID in admin bar.
 *
 * @since 1.0.0
 *
 * @param \WP_Admin_Bar $wp_admin_bar
 *
 * @return void
 */
function site_id_in_admin_bar( $wp_admin_bar ) {

	/**
	 * @var object $title_link
	 */
	$title_link = $wp_admin_bar->get_node( 'site-name' );

	if ( \is_super_admin() && ! \is_network_admin() ) {
		$title_link->title .= ' - (Site ID: ' . \get_current_blog_id() . ')';
	}

	$wp_admin_bar->add_node( $title_link );
}
