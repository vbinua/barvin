<?php
/**
Plugin Name: PDFjs Viewer - Embed PDFs
Plugin URI: https://github.com/TwisterMc/pdfjs-viewer-shortcode
Description: Embed PDFs with the gorgeous PDF.js viewer
Version: 3.0.2
Author: <a href="https://www.twistermc.com/">Thomas McMahon</a>, <a href="https://byterevel.com/">Ben Lawson</a>
Contributors: FalconerWeb, twistermc
License: GPLv2
 **/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

// Plugin version constant for cache busting - read from plugin header
if ( ! defined( 'PDFJS_PLUGIN_VERSION' ) ) {
	if ( ! function_exists( 'get_plugin_data' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$plugin_data = get_plugin_data( __FILE__, false, false );
	define( 'PDFJS_PLUGIN_VERSION', $plugin_data['Version'] );
}

// Admin notice control key: set to a non-empty string in releases
// where you want to show the block recovery notice. Leave empty to disable.
// Example: 'block-recovery-2025-12'. Users can still override via filter.
if ( ! defined( 'PDFJS_NOTICE_KEY' ) ) {
	define( 'PDFJS_NOTICE_KEY', 'block-recovery-2025-12' );
}

/**
 * Load plugin text domain for translations.
 */
function pdfjs_load_textdomain() {
	load_plugin_textdomain( 'pdfjs-viewer-shortcode', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'pdfjs_load_textdomain' );

/**
 * Generate the PDF embed code.
 */
require 'inc/embed.php';

/**
 * Shared PDF viewer rendering
 */
require 'inc/render-viewer.php';

/**
 * Cleanup hooks
 */
require 'inc/cleanup-hooks.php';

/**
 * Shortcode
 */
require 'inc/shortcode.php';

/**
 * Media Button for Classic Editor
 */
require 'inc/media-button.php';

/**
 * Gutenberg Block
 */
require 'inc/gutenberg-block.php';

/**
 * Options Page
 */
require 'inc/options-page.php';

/**
 * Custom URL - Work in Progress
 */
$pdfjs_custom_page = get_option( 'pdfjs_custom_page', 0 );

if ($pdfjs_custom_page) {
	require 'inc/custom-page.php';
}

/**
 * Admin Notices (version-tied dismissible banner)
 */
require 'inc/admin-notice.php';

/**
 * Activation hook: Mark current notice key as shown to avoid notice on fresh installs.
 */
register_activation_hook( __FILE__, 'pdfjs_notice_on_activate' );
