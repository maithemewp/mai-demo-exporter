<?php
/**
 * Mai Demo Exporter.
 *
 * Plugin Name: Mai Demo Exporter
 * Plugin URI:  https://wordpress.org/plugins/mai-demo-exporter/
 * Description: The required plugin to power Mai child themes.
 * Version:     0.2.2
 * Author:      BizBudding Inc
 * Author URI:  https://bizbudding.com/
 * Text Domain: mai-demo-exporter
 * License:     GPL-2.0-or-later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
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

\add_action( 'init', __NAMESPACE__ . '\\init' );
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

	require_once __DIR__ . '/lib/' . 'functions.php';
	require_once __DIR__ . '/lib/' . 'compat.php';
	require_once __DIR__ . '/lib/' . 'admin.php';
	require_once __DIR__ . '/lib/' . 'content.php';
	require_once __DIR__ . '/lib/' . 'customizer-settings.php';
	require_once __DIR__ . '/lib/' . 'template-parts.php';
}
