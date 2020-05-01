<?php

defined( 'ABSPATH' ) || die();

function mai_demo_exporter_customizer() {
	require_once ABSPATH . WPINC . '/class-wp-customize-manager.php';
	$wp_customize = new WP_Customize_Manager( compact( 'changeset_uuid', 'theme', 'messenger_channel', 'settings_previewed', 'autosaved', 'branching' ) );

	$template = get_template();
	$mods     = get_theme_mods();
	$data     = [
		'template' => $template,
		'mods'     => $mods ? $mods : [],
		'options'  => [],
	];

	$settings = $wp_customize->settings();

	/**
	 * @var WP_Customize_Setting $setting
	 */
	foreach ( $settings as $key => $setting ) {
		if ( 'option' == $setting->type ) {
			if ( 'widget_' === substr( strtolower( $key ), 0, 7 ) ) {
				continue;
			}

			if ( 'sidebars_' === substr( strtolower( $key ), 0, 9 ) ) {
				continue;
			}

			$data['options'][ $key ] = $setting->value();
		}
	}

	$options = mai_get_options();

	foreach ( $options as $key => $value ) {
		$data['options'][ mai_get_handle() ][$key] = $value;
	}

	if ( function_exists( 'wp_get_custom_css_post' ) ) {
		$data['wp_css'] = wp_get_custom_css();
	}

	return serialize( $data );
}
