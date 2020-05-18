<?php
/**
 * Mai Demo Exporter.
 *
 * This is the plugin's bootstrap file. It is responsible for providing the plugin
 * meta information that WordPress needs, preparing the environment so that it's
 * ready to execute our code and kick off our composition root (Plugin class).
 *
 * Plugin Name: Mai Demo Exporter
 * Plugin URI:  https://wordpress.org/plugins/mai-demo-exporter/
 * Description: The required plugin to power Mai child themes.
 * Version:     0.1.1
 * Author:      BizBudding Inc
 * Author URI:  https://bizbudding.com/
 * Text Domain: mai-demo-exporter
 * License:     GPL-2.0-or-later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /assets/lang
 *
 * @package   BizBudding\MaiDemoExporter
 * @author    BizBudding <info@bizbudding.com>
 * @license   GPL-2.0-or-later
 * @link      https://bizbudding.com/
 * @copyright 2020 BizBudding
 */

namespace MaiDemoExporter;

// Prevent direct file access.
defined( 'ABSPATH' ) || die();

add_action( 'init', __NAMESPACE__ . '\\init' );
/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @return void
 */
function init() {
	if ( ! is_admin() || is_customize_preview() ) {
		return;
	}

	require_once __DIR__ . '/lib/' . 'admin.php';
	require_once __DIR__ . '/lib/' . 'content.php';
	require_once __DIR__ . '/lib/' . 'widgets.php';
	require_once __DIR__ . '/lib/' . 'customizer.php';
}

add_filter( 'wpforms_post_type_args', __NAMESPACE__ . '\\enable_wpforms', 10, 1 );
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
