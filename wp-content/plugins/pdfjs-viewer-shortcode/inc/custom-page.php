<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

add_filter( 'init', function() {
	if ( isset( $_GET['pdfjs_id'] ) ) {

		$nonce = sanitize_text_field( $_REQUEST['_wpnonce'] );
		if ( ! wp_verify_nonce( $nonce, 'pdfjs_full_screen' ) ) {
			die( esc_html__( 'Security Check Failed', 'pdfjs-viewer-shortcode' ) );
		}

		/**
		 * Custom Template
		 */

		$attachment_pdfjs_id = sanitize_text_field( $_GET['pdfjs_id'] );
		$attachment_id       = isset( $attachment_pdfjs_id ) && is_numeric( $attachment_pdfjs_id ) ? absint( $attachment_pdfjs_id ) : 0;

		if ( 0 !== $attachment_id ) {
			// Verify attachment exists and is valid
			$attachment = get_post( $attachment_id );
			if ( ! $attachment || 'attachment' !== $attachment->post_type ) {
				wp_die( esc_html__( 'Invalid attachment.', 'pdfjs-viewer-shortcode' ) );
			}
			
			// Check if attachment is accessible (not private/draft unless user has permission)
			if ( 'private' === $attachment->post_status && ! current_user_can( 'read_private_posts' ) ) {
				wp_die( esc_html__( 'You do not have permission to view this attachment.', 'pdfjs-viewer-shortcode' ) );
			}
			
			// Verify the file is actually a PDF
			$mime_type = get_post_mime_type( $attachment_id );
			if ( 'application/pdf' !== $mime_type ) {
				wp_die( esc_html__( 'This attachment is not a PDF file.', 'pdfjs-viewer-shortcode' ) );
			}
			
			$pdfjs_url = wp_get_attachment_url( $attachment_id );
		} else {
			$pdfjs_url = plugin_dir_url( __FILE__ ) . '../pdf-loading-error.pdf';
		}

		if ( ! $pdfjs_url ) {
			$pdfjs_url = plugin_dir_url( __FILE__ ) . '../pdf-loading-error.pdf';
		}

		include plugin_dir_path( __FILE__ ) . '../templates/fullscreen.php';
		die();
	}
});
