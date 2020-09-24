<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die();

function maidemoexporter_get_array_html( $array, $indent = '' ) {
	$html = '';
	foreach ( $array as $key => $values ) {
		if ( is_array( $values ) ) {
			$html .= "{$indent}'{$key}' => [" . "\r\n";
				$html .= maidemoexporter_get_array_html( $values, $indent . '&nbsp;&nbsp;&nbsp;&nbsp;' );
			$html .= "{$indent}]," . "\r\n";
		} else {
			if ( is_numeric( $key ) ) {
				$html .= "{$indent}'{$values}'," . "\r\n";
			} elseif ( is_bool( $values ) ) {
				$string = $values ? 'true' : 'false';
				$html  .= "{$indent}'{$key}' => {$string}," . "\r\n";
			} else {
				$html .= "{$indent}'{$key}' => '{$values}'," . "\r\n";
			}
		}
	}
	return $html;
}

function maidemoexporter_config_cleanup( $array, $defaults ) {
	foreach ( $array as $key => $value ) {
		// Remove layout dividers. Not sure why they are getting saved to the db anyway.
		if ( mai_has_string( '-layout-divider', $key ) || mai_has_string( '-field-divider', $key ) ) {
			unset( $array[ $key ] );
		}
		// Recursive array.
		elseif ( is_array( $value ) ) {
			if ( maidemoexporter_has_string_keys( $value ) ) {
				$array[ $key ] = maidemoexporter_config_cleanup( $value, $defaults[ $key ] );
				if ( empty( $array[ $key ] ) ) {
					unset( $array[ $key ] );
				}
			} else {
				$array[ $key ] = array_values( $value );
			}
		}
		// Remove if empty, set to remove, or same as default.
		elseif ( ( '' === $value ) || ( isset( $defaults[ $key ] ) && ( $value === $defaults[ $key ] ) ) ) {
			unset( $array[ $key ] );
		}
	}

	return $array;
}

function maidemoexporter_has_string_keys( $array ) {
	return count( array_filter( array_keys( $array ), 'is_string' ) ) > 0;
}
