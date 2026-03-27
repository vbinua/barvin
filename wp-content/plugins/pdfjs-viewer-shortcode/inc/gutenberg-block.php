<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * Get all PDF.js plugin options as a normalized array.
 * Cached in static variable to prevent repeated database queries.
 *
 * @return array Plugin options with consistent keys and values.
 */
function pdfjs_get_options() {
	// Try to get from object cache first
	$cached_options = wp_cache_get( 'pdfjs_options', 'pdfjs' );
	if ( false !== $cached_options ) {
		return $cached_options;
	}
	
	// Build options array
	$cached_options = array(
		'pdfjs_viewer_url'             => plugin_dir_url( dirname( __FILE__ ) ) . 'pdfjs/web/viewer.php',
		'pdfjs_download_button'        => get_option( 'pdfjs_download_button', 'on' ),
		'pdfjs_print_button'           => get_option( 'pdfjs_print_button', 'on' ),
		'pdfjs_search_button'          => get_option( 'pdfjs_search_button', 'on' ),
		'pdfjs_editing_buttons'        => get_option( 'pdfjs_editing_buttons', 'on' ),
		'pdfjs_fullscreen_link'        => get_option( 'pdfjs_fullscreen_link', 'on' ),
		'pdfjs_fullscreen_link_text'   => get_option( 'pdfjs_fullscreen_link_text', 'View Fullscreen' ),
		'pdfjs_fullscreen_link_target' => get_option( 'pdfjs_fullscreen_link_target', '' ),
		'pdfjs_embed_height'           => get_option( 'pdfjs_embed_height', 800 ),
		'pdfjs_embed_width'            => get_option( 'pdfjs_embed_width', 0 ),
		'pdfjs_viewer_scale'           => get_option( 'pdfjs_viewer_scale', 0 ),
		'pdfjs_viewer_pagemode'        => get_option( 'pdfjs_viewer_pagemode', 'none' ),
	);
	
	// Cache for 1 hour
	wp_cache_set( 'pdfjs_options', $cached_options, 'pdfjs', 3600 );
	
	return $cached_options;
}

/**
 * Gutenberg Block
 */
function pdfjs_register_gutenberg_card_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$base_dir         = plugin_dir_path( __FILE__ ) . '../blocks/build/';
	$script_handle    = 'gutenberg-pdfjs';
	$style_handle           = null;
	$editor_style_handle    = null;
	$asset_file       = $base_dir . 'index.asset.php';
	$script_file      = $base_dir . 'index.js';
	$style_file       = $base_dir . 'style-index.css';
	$editor_style_file = $base_dir . 'index.css';

	$asset_data = array(
		'dependencies' => array( 'wp-blocks', 'wp-element', 'wp-editor' ),
		'version'      => file_exists( $script_file ) ? filemtime( $script_file ) : false,
	);

	if ( file_exists( $asset_file ) ) {
		$asset_data = include $asset_file;
	}

	wp_register_script(
		$script_handle,
		plugins_url( '../blocks/build/index.js', __FILE__ ),
		isset( $asset_data['dependencies'] ) ? $asset_data['dependencies'] : array(),
		isset( $asset_data['version'] ) ? $asset_data['version'] : ( file_exists( $script_file ) ? filemtime( $script_file ) : PDFJS_PLUGIN_VERSION ),
		true
	);

	wp_localize_script( $script_handle, 'pdfjs_options', pdfjs_get_options() );

	if ( file_exists( $style_file ) ) {
		$style_handle = 'gutenberg-pdfjs-style';
		wp_register_style(
			$style_handle,
			plugins_url( '../blocks/build/style-index.css', __FILE__ ),
			array(),
			file_exists( $style_file ) ? filemtime( $style_file ) : PDFJS_PLUGIN_VERSION
		);
	}

	if ( file_exists( $editor_style_file ) ) {
		$editor_style_handle = 'gutenberg-pdfjs-editor-style';
		wp_register_style(
			$editor_style_handle,
			plugins_url( '../blocks/build/index.css', __FILE__ ),
			array(),
			file_exists( $editor_style_file ) ? filemtime( $editor_style_file ) : PDFJS_PLUGIN_VERSION
		);
	}

	$block_args = array(
		'editor_script' => $script_handle,
	);

	if ( $editor_style_handle ) {
		$block_args['editor_style'] = $editor_style_handle;
	}

	if ( $style_handle ) {
		$block_args['style'] = $style_handle;
	}

	register_block_type(
		'blocks/pdfjs-block',
		$block_args
	);
}

add_action( 'init', 'pdfjs_register_gutenberg_card_block' );
