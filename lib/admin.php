<?php

namespace MaiDemoExporter;

// Prevent direct file access.
\defined( 'ABSPATH' ) || die();

/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @return array
 */
function get_content_types() {
	return [
		'content'             => [
			'title'    => __( 'Content' ),
			'file'     => 'content.xml',
			'callback' => __NAMESPACE__ . '\\export_content',
		],
		'template_parts'      => [
			'title'    => __( 'Template Parts' ),
			'file'     => 'template-parts.json',
			'callback' => __NAMESPACE__ . '\\export_template_parts',
		],
		'customizer_settings' => [
			'title'    => __( 'Customizer Settings' ),
			'file'     => 'customizer.dat',
			'callback' => __NAMESPACE__ . '\\export_customizer_settings',
		],
	];
}

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

/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @return void
 */
function admin_page() {
	global $title;

	$blog_id       = \get_current_blog_id();
	$export_url    = \get_home_url( $blog_id, 'wp-content/uploads/sites/' . $blog_id . '/mai-engine/' );
	$content_types = get_content_types();

	?>
	<div class="wrap">
		<h1><?php echo $title; ?></h1>
		<p>The content for this demo will be exported to the following URL
			<a target="_blank" href="<?php echo $export_url; ?>"><?php echo $export_url; ?></a>
		</p>
		<?php foreach ( $content_types as $id => $args ): ?>
			<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
				<?php \wp_nonce_field( 'mai_demo_export', 'mai_demo_export_nonce' ); ?>
				<input type="hidden" name="action" value="mai_demo_export">
				<input type="hidden" name="content_type" value="<?php echo $id; ?>">
				<input class="button button-primary button-hero" type="submit" value="Regenerate <?php echo $args['title'] ?>">
			</form>
			<br>
		<?php endforeach; ?>
	</div>
	<?php

	/**
	 * Config generator.
	*/
	$config   = [];
	$options  = \mai_get_options();
	$defaults = require \mai_get_dir() . 'config/_default.php';
	$keepers  = []; // Any top level settings that transfer dirctly from options to our config. I didn't see any but thought I would.

	foreach ( $options as $key => $value ) {

		// Fonts.
		if ( \mai_has_string( '-typography', $key ) && $value ) {
			if ( isset( $value['font-family'] ) && isset( $value['font-weight'] ) ) {
				$config['global-styles']['fonts'][ str_replace( '-typography', '', $key ) ] = sprintf( '%s:%s', $value['font-family'], $value['font-weight'] );
			}
		}

		// Colors.
		if ( \mai_has_string( 'color-', $key ) && $value ) {
			$config['global-styles']['colors'][ str_replace( 'color-', '', $key ) ] = $value;
		}

		// Sticky Header.
		if ( ( 'site-header-sticky' === $key ) && $value ) {
			$config['theme-support']['add'] = 'sticky-header';
		}

		// Transparent Header.
		if ( ( 'site-header-transparent' === $key ) && $value ) {
			$config['theme-support']['add'] = 'transparent-header';
		}

		// Boxed Container, only if true.
		if ( ( 'boxed-container' === $key ) && $value ) {
			$config['theme-support']['add'] = $value;
		}

		// Page Header.
		if ( \mai_has_string( 'page-header-', $key ) && $value ) {
			$config['settings']['page-header'][ str_replace( 'page-header-', '', $key ) ] = $value;
		}

		// Content Archives.
		if ( ( 'content-archives' === $key ) && $value ) {
			$config['settings'][ $key ] = $value;
		}

		// Single Content.
		if ( ( 'single-content' === $key ) && $value ) {
			$config['settings'][ $key ] = $value;
		}

		// Archive Settings. Must be after Content Archives.
		if ( ( 'archive-settings' === $key ) && $value ) {
			$config['settings']['content-archives']['enable'] = $value;
		}

		// Single Settings. Must be after Single Content.
		if ( ( 'single-settings' === $key ) && $value ) {
			$config['settings']['single-content']['enable'] = $value;
		}

		// Site Layouts.
		if ( ( 'site-layouts' === $key ) && $value ) {
			$config['settings'][ $key ] = $value;
		}

		// After Header Menu Alignment.
		if ( ( 'after-header-menu-alignment' === $key ) && $value ) {
			$config['settings'][ $key ] = $value;
		}

		// Add keepers.
		if ( in_array( $key, $keepers ) && $value ) {
			$config[ $key ] = $value;
		}

	}

	// Unset if page header image is an ID.
	if ( isset( $config['settings']['page-header']['image'] ) && is_numeric( $config['settings']['page-header']['image'] ) ) {
		unset( $config['settings']['page-header']['image'] );
	}

	$config = \maidemoexporter_config_cleanup( $config, $defaults );

	$html  = '';
	$html .= '<pre style="line-height:1;">';
	$html .= 'return ['. "\r\n";
	$html .= \maidemoexporter_get_array_html( $config, '&nbsp;&nbsp;&nbsp;&nbsp;' );
	$html .= '];';
	$html .= '</pre>';

	echo $html;
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

	$content_types = get_content_types();

	if ( ! isset( $_POST['content_type'] ) || ! isset( $content_types[ \sanitize_key( $_POST['content_type'] ) ] ) ) {
		return;
	}

	include_once ABSPATH . 'wp-admin/includes/file.php';
	\WP_Filesystem();
	global $wp_filesystem;

	$cache_dir = \wp_upload_dir()['basedir'] . '/mai-engine/';

	if ( ! \is_dir( $cache_dir ) ) {
		\wp_mkdir_p( $cache_dir );
	}

	$content_type = \sanitize_key( $_POST['content_type'] );

	$wp_filesystem->put_contents(
		$cache_dir . DIRECTORY_SEPARATOR . $content_types[ $content_type ]['file'],
		$content_types[ $content_type ]['callback']()
	);

	\wp_redirect( \admin_url( 'admin.php?page=mai-demo-exporter&success=' . $content_type ) );
}

\add_action( 'admin_notices', __NAMESPACE__ . '\\update_notice' );
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

	$content_type  = \sanitize_key( $_GET['success'] );
	$content_types = get_content_types();

	?>
	<div class="notice notice-success is-dismissible">
		<p><?php echo $content_types[ $content_type ]['title'] . __( ' regenerated!', 'mai-demo-exporter' ); ?></p>
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

	$wp_admin_bar->add_node( (array) $title_link );
}

\add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\inline_styles' );
/**
 * Apply inline styles to all admin pages.
 *
 * @since 1.0.0
 *
 * @return void
 */
function inline_styles() {
	$css = <<<CSS
/*
 * Make sites sub menu scrollable.
 */
#wpadminbar .ab-top-menu > .menupop > .ab-sub-wrapper {
    overflow-y: scroll;
    max-height: 90vh;
}
CSS;

	\wp_register_style( __NAMESPACE__, false );
	\wp_enqueue_style( __NAMESPACE__ );
	\wp_add_inline_style( __NAMESPACE__, $css );
}
