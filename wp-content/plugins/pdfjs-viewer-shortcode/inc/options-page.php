<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * Sanitize checkbox and text inputs for PDFjs settings.
 */
function pdfjs_sanitize_option( $input ) {
	// For checkboxes, return 'on' or empty string
	if ( is_string( $input ) && 'on' === $input ) {
		return 'on';
	}
	// For text fields, sanitize
	if ( is_string( $input ) ) {
		// Check if it looks like a URL
		if ( filter_var( $input, FILTER_VALIDATE_URL ) !== false ) {
			return esc_url_raw( $input );
		}
		return sanitize_text_field( $input );
	}
	// For numbers
	if ( is_numeric( $input ) ) {
		return absint( $input );
	}
	return '';
}

/**
 * Settings Page in WP Admin
 */
function pdfjs_register_settings() {
	register_setting( 'pdfjs_options_group', 'pdfjs_download_button', 'pdfjs_sanitize_option' );
	register_setting( 'pdfjs_options_group', 'pdfjs_print_button', 'pdfjs_sanitize_option' );
	register_setting( 'pdfjs_options_group', 'pdfjs_search_button', 'pdfjs_sanitize_option' );
	register_setting( 'pdfjs_options_group', 'pdfjs_editing_buttons', 'pdfjs_sanitize_option' );
	register_setting( 'pdfjs_options_group', 'pdfjs_fullscreen_link', 'pdfjs_sanitize_option' );
	register_setting( 'pdfjs_options_group', 'pdfjs_fullscreen_link_text', 'pdfjs_sanitize_option' );
	register_setting( 'pdfjs_options_group', 'pdfjs_fullscreen_link_target', 'pdfjs_sanitize_option' );
	register_setting( 'pdfjs_options_group', 'pdfjs_embed_height', 'pdfjs_sanitize_option' );
	register_setting( 'pdfjs_options_group', 'pdfjs_embed_width', 'pdfjs_sanitize_option' );
	register_setting( 'pdfjs_options_group', 'pdfjs_viewer_scale', 'pdfjs_sanitize_option' );
	register_setting( 'pdfjs_options_group', 'pdfjs_viewer_pagemode', 'pdfjs_sanitize_option' );
	register_setting( 'pdfjs_options_group', 'pdfjs_custom_page', 'pdfjs_sanitize_option' );
}
add_action( 'admin_init', 'pdfjs_register_settings' );

/**
 * Clear cache when settings are updated.
 */
function pdfjs_clear_options_cache() {
	wp_cache_delete( 'pdfjs_options', 'pdfjs' );
	wp_cache_delete( 'pdfjs_viewer_options', 'pdfjs' );
}
add_action( 'update_option_pdfjs_download_button', 'pdfjs_clear_options_cache' );
add_action( 'update_option_pdfjs_print_button', 'pdfjs_clear_options_cache' );
add_action( 'update_option_pdfjs_fullscreen_link', 'pdfjs_clear_options_cache' );
add_action( 'update_option_pdfjs_fullscreen_link_text', 'pdfjs_clear_options_cache' );
add_action( 'update_option_pdfjs_fullscreen_link_target', 'pdfjs_clear_options_cache' );
add_action( 'update_option_pdfjs_embed_height', 'pdfjs_clear_options_cache' );
add_action( 'update_option_pdfjs_embed_width', 'pdfjs_clear_options_cache' );
add_action( 'update_option_pdfjs_viewer_scale', 'pdfjs_clear_options_cache' );
add_action( 'update_option_pdfjs_viewer_pagemode', 'pdfjs_clear_options_cache' );
add_action( 'update_option_pdfjs_search_button', 'pdfjs_clear_options_cache' );
add_action( 'update_option_pdfjs_editing_buttons', 'pdfjs_clear_options_cache' );

function pdfjs_register_options_page() {
	global $pdfjs_settings_page;
	$pdfjs_settings_page = add_options_page( 'PDFjs Settings', 'PDFjs Viewer', 'manage_options', 'pdfjs', 'pdfjs_options_page' );
}
add_action( 'admin_menu', 'pdfjs_register_options_page' );

// create the settings page.
function pdfjs_options_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'pdfjs-viewer-shortcode' ) );
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'PDFjs Viewer Options', 'pdfjs-viewer-shortcode' ); ?></h1>
		<form method="post" action="options.php">

			<?php
			settings_fields( 'pdfjs_options_group' );

			$download_button      = get_option( 'pdfjs_download_button', 'on' );
			$print_button         = get_option( 'pdfjs_print_button', 'on' );
			$search_button        = get_option( 'pdfjs_search_button', 'on' );
			$editing_buttons        = get_option( 'pdfjs_editing_buttons', 'on' );
			$fullscreen_link      = get_option( 'pdfjs_fullscreen_link', 'on' );
			$fullscreen_link_text = get_option( 'pdfjs_fullscreen_link_text', 'View Fullscreen' );
			$link_target          = get_option( 'pdfjs_fullscreen_link_target', '' );
			$embed_height         = get_option( 'pdfjs_embed_height', 800 );
			$embed_width          = get_option( 'pdfjs_embed_width', 0 );
			$viewer_scale         = get_option( 'pdfjs_viewer_scale', 'auto' );
			$viewer_pagemode      = get_option( 'pdfjs_viewer_pagemode', 'none' );
			$pdfjs_custom_page    = get_option( 'pdfjs_custom_page', '' );
			?>

			<h2 class="title"><?php esc_html_e( 'Defaults', 'pdfjs-viewer-shortcode' ); ?></h2>
			<p id="pdfjs-defaults-help">
				<?php esc_html_e( 'These are the initial settings applied when a PDF is embedded. You can adjust them in the editor at any time. Updates to these default settings only apply to new PDF embeds, not existing ones.', 'pdfjs-viewer-shortcode' ); ?>
			</p>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="pdfjs_download_button"><?php esc_html_e( 'Show Save Button', 'pdfjs-viewer-shortcode' ); ?></label></th>
					<td><input type="checkbox" id="pdfjs_download_button" name="pdfjs_download_button" aria-describedby="pdfjs-defaults-help" <?php checked( $download_button, 'on' ); ?> /></td>
				</tr>
				<tr>
					<th scope="row"><label for="pdfjs_print_button"><?php esc_html_e( 'Show Print Button', 'pdfjs-viewer-shortcode' ); ?></label></th>
					<td><input type="checkbox" id="pdfjs_print_button" name="pdfjs_print_button" aria-describedby="pdfjs-defaults-help" <?php checked( $print_button, 'on' ); ?> /></td>
				</tr>
				<tr>
					<th scope="row"><label for="pdfjs_fullscreen_link"><?php esc_html_e( 'Show Fullscreen Link', 'pdfjs-viewer-shortcode' ); ?></label></th>
					<td><input type="checkbox" id="pdfjs_fullscreen_link" name="pdfjs_fullscreen_link" aria-describedby="pdfjs-defaults-help" <?php checked( $fullscreen_link, 'on' ); ?> /></td>
				</tr>
				<tr>
					<th scope="row"><label for="pdfjs_fullscreen_link_text"><?php esc_html_e( 'Fullscreen Link Text', 'pdfjs-viewer-shortcode' ); ?></label></th>
					<td><input type="text" class="regular-text" id="pdfjs_fullscreen_link_text" name="pdfjs_fullscreen_link_text" aria-describedby="pdfjs-defaults-help" value="<?php echo esc_html( $fullscreen_link_text ? $fullscreen_link_text : 'View Fullscreen' ); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="pdfjs_fullscreen_link_target"><?php esc_html_e( 'Fullscreen Links in New Tabs', 'pdfjs-viewer-shortcode' ); ?></label></th>
					<td><input type="checkbox" id="pdfjs_fullscreen_link_target" name="pdfjs_fullscreen_link_target" aria-describedby="pdfjs-defaults-help" <?php checked( $link_target, 'on' ); ?> /></td>
				</tr>
				<tr>
					<th scope="row"><label for="pdfjs_embed_height"><?php esc_html_e( 'Embed Height', 'pdfjs-viewer-shortcode' ); ?></label></th>
					<td><input type="number" class="regular-text" id="pdfjs_embed_height" name="pdfjs_embed_height" aria-describedby="pdfjs-defaults-help" value="<?php echo esc_html( $embed_height ? $embed_height : 800 ); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="pdfjs_embed_width"><?php esc_html_e( 'Embed Width', 'pdfjs-viewer-shortcode' ); ?></label></th>
					<td>
						<input type="number" class="regular-text" id="pdfjs_embed_width" name="pdfjs_embed_width" aria-describedby="pdfjs-width-note pdfjs-defaults-help" value="<?php echo esc_html( $embed_width ? $embed_width : 0 ); ?>" />
						<p id="pdfjs-width-note"><?php esc_html_e( 'Note: 0 = 100%', 'pdfjs-viewer-shortcode' ); ?></p>
					</td>
				</tr>
				</table>

				<h2 class="title"><?php esc_html_e( 'Global Defaults', 'pdfjs-viewer-shortcode' ); ?></h2>
				<p id="pdfjs-defaults-help-g">
					<?php esc_html_e( 'These settings control how all PDFs appear on your site. Any changes you make here will affect all PDFs that use PDF.js.', 'pdfjs-viewer-shortcode' ); ?>
				</p>

				<table class="form-table" role="presentation">

				<tr>
					<th scope="row"><label for="pdfjs_search_button"><?php esc_html_e( 'Show Search Button', 'pdfjs-viewer-shortcode' ); ?></label></th>
					<td><input type="checkbox" id="pdfjs_search_button" name="pdfjs_search_button" aria-describedby="pdfjs-defaults-help-g" <?php checked( $search_button, 'on' ); ?> /></td>
				</tr>
				<tr>
					<th scope="row"><label for="pdfjs_editing_buttons"><?php esc_html_e( 'Show Editing Buttons', 'pdfjs-viewer-shortcode' ); ?></label></th>
					<td><input type="checkbox" id="pdfjs_editing_buttons" name="pdfjs_editing_buttons" aria-describedby="pdfjs-defaults-help-g" <?php checked( $editing_buttons, 'on' ); ?> /></td>
				</tr>
				
				<tr>
					<th scope="row"><label for="pdfjs_viewer_scale"><?php esc_html_e( 'Viewer Scale', 'pdfjs-viewer-shortcode' ); ?></label></th>
					<td>
						<select id="pdfjs_viewer_scale" name="pdfjs_viewer_scale" aria-describedby="pdfjs-defaults-help-g">
							<option value="auto" <?php selected( $viewer_scale, 'auto' ); ?>><?php esc_html_e( 'Auto', 'pdfjs-viewer-shortcode' ); ?></option>
							<option value="page-actual" <?php selected( $viewer_scale, 'page-actual' ); ?>><?php esc_html_e( 'Actual Size', 'pdfjs-viewer-shortcode' ); ?></option>
							<option value="page-fit" <?php selected( $viewer_scale, 'page-fit' ); ?>><?php esc_html_e( 'Page Fit', 'pdfjs-viewer-shortcode' ); ?></option>
							<option value="page-width" <?php selected( $viewer_scale, 'page-width' ); ?>><?php esc_html_e( 'Page Width', 'pdfjs-viewer-shortcode' ); ?></option>
							<option value="50" <?php selected( $viewer_scale, '50' ); ?>><?php esc_html_e( '50%', 'pdfjs-viewer-shortcode' ); ?></option>
							<option value="75" <?php selected( $viewer_scale, '75' ); ?>><?php esc_html_e( '75%', 'pdfjs-viewer-shortcode' ); ?></option>
							<option value="100" <?php selected( $viewer_scale, '100' ); ?>><?php esc_html_e( '100%', 'pdfjs-viewer-shortcode' ); ?></option>
							<option value="125" <?php selected( $viewer_scale, '125' ); ?>><?php esc_html_e( '125%', 'pdfjs-viewer-shortcode' ); ?></option>
							<option value="150" <?php selected( $viewer_scale, '150' ); ?>><?php esc_html_e( '150%', 'pdfjs-viewer-shortcode' ); ?></option>
							<option value="200" <?php selected( $viewer_scale, '200' ); ?>><?php esc_html_e( '200%', 'pdfjs-viewer-shortcode' ); ?></option>
						</select>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="pdfjs_viewer_pagemode"><?php esc_html_e( 'Page Mode (aka Sidebar)', 'pdfjs-viewer-shortcode' ); ?></label></th>
					<td>
						<select id="pdfjs_viewer_pagemode" name="pdfjs_viewer_pagemode" aria-describedby="pdfjs-defaults-help-g">
							<option value="none" <?php selected( $viewer_pagemode, 'none' ); ?>><?php esc_html_e( 'None', 'pdfjs-viewer-shortcode' ); ?></option>
							<option value="thumbs" <?php selected( $viewer_pagemode, 'thumbs' ); ?>><?php esc_html_e( 'Thumbs', 'pdfjs-viewer-shortcode' ); ?></option>
							<option value="bookmarks" <?php selected( $viewer_pagemode, 'bookmarks' ); ?>><?php esc_html_e( 'Bookmarks', 'pdfjs-viewer-shortcode' ); ?></option>
							<option value="attachments" <?php selected( $viewer_pagemode, 'attachments' ); ?>><?php esc_html_e( 'Attachments', 'pdfjs-viewer-shortcode' ); ?></option>
						</select>
					</td>
				</tr>
				<tr style="display:none;">
					<th scope="row"><label for="pdfjs_custom_page"><?php esc_html_e( 'Alternative PDF Loading', 'pdfjs-viewer-shortcode' ); ?></label></th>
					<td><input type="checkbox" id="pdfjs_custom_page" name="pdfjs_custom_page" <?php checked( $pdfjs_custom_page, 'on' ); ?> /> <span style="color:rebeccapurple;"> - <?php esc_html_e( 'Beta. Test with caution and', 'pdfjs-viewer-shortcode' ); ?> <a href="https://wordpress.org/support/plugin/pdfjs-viewer-shortcode/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'leave feedback', 'pdfjs-viewer-shortcode' ); ?></a> <?php esc_html_e( 'on how it works.', 'pdfjs-viewer-shortcode' ); ?></span></td>
				</tr>
			</table>
			<?php submit_button(); ?>
		
		</form>
	</div>
	<?php
}

/**
 * Add Settings Link to Plugins Page
 */
add_filter( 'plugin_action_links_pdfjs-viewer-shortcode/pdfjs-viewer.php', 'pdfjs_settings_link' );
function pdfjs_settings_link( $links ) {
	// Build and escape the URL.
	$url = esc_url(
		add_query_arg(
			'page',
			'pdfjs',
			get_admin_url() . 'admin.php'
		)
	);
	// Create the link.
	$settings_link = "<a href='$url'>" . __( 'Settings', 'pdfjs-viewer-shortcode' ) . '</a>';
	// Adds the link to the end of the array.
	array_push(
		$links,
		$settings_link
	);
	return $links;
}
