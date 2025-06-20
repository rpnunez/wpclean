<?php
/**
 * Plugin Name:       WPClean
 * Plugin URI:        https://example.com/plugins/wpclean/
 * Description:       A plugin to help clean and optimize your WordPress site.
 * Version:           1.0.0
 * Author:            Your Name
 * Author URI:        https://example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wpclean
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'WPCLEAN_VERSION' ) ) {
	define( 'WPCLEAN_VERSION', '1.0.0' );
}

/**
 * Register WPClean Custom Post Type for Scans.
 */
function wpclean_register_scan_post_type() {
	$labels = array(
		'name'                  => _x( 'Scans', 'Post type general name', 'wpclean' ),
		'singular_name'         => _x( 'Scan', 'Post type singular name', 'wpclean' ),
		'menu_name'             => _x( 'Scans', 'Admin Menu text', 'wpclean' ),
		'name_admin_bar'        => _x( 'Scan', 'Add New on Toolbar', 'wpclean' ),
		'add_new'               => __( 'Add New', 'wpclean' ),
		'add_new_item'          => __( 'Add New Scan', 'wpclean' ),
		'new_item'              => __( 'New Scan', 'wpclean' ),
		'edit_item'             => __( 'Edit Scan', 'wpclean' ),
		'view_item'             => __( 'View Scan', 'wpclean' ),
		'all_items'             => __( 'All Scans', 'wpclean' ),
		'search_items'          => __( 'Search Scans', 'wpclean' ),
		'parent_item_colon'     => __( 'Parent Scans:', 'wpclean' ),
		'not_found'             => __( 'No scans found.', 'wpclean' ),
		'not_found_in_trash'    => __( 'No scans found in Trash.', 'wpclean' ),
		'featured_image'        => _x( 'Scan Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'wpclean' ),
		'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'wpclean' ),
		'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'wpclean' ),
		'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'wpclean' ),
		'archives'              => _x( 'Scan archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'wpclean' ),
		'insert_into_item'      => _x( 'Insert into scan', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'wpclean' ),
		'uploaded_to_this_item' => _x( 'Uploaded to this scan', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'wpclean' ),
		'filter_items_list'     => _x( 'Filter scans list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'wpclean' ),
		'items_list_navigation' => _x( 'Scans list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'wpclean' ),
		'items_list'            => _x( 'Scans list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'wpclean' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false, // Not publicly queryable, managed in admin.
		'publicly_queryable' => false,
		'show_ui'            => true,  // Show in admin UI.
		'show_in_menu'       => false, // We will add it to our custom menu.
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'wpclean_scan' ),
		'capability_type'    => 'post',
		'has_archive'        => false, // No archive page for now.
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'custom-fields' ), // 'editor' can store scan log/details.
		'show_in_admin_bar'  => true, // Show in "+ New" menu in admin bar.
	);

	register_post_type( 'wpclean_scan', $args );
}
add_action( 'init', 'wpclean_register_scan_post_type' );

/**
 * Add WPClean Admin Menu.
 */
function wpclean_add_admin_menu() {
	// Add top-level menu page.
	add_menu_page(
		__( 'WPClean', 'wpclean' ),
		__( 'WPClean', 'wpclean' ),
		'manage_options', // Capability.
		'wpclean',        // Menu slug.
		'wpclean_scan_page_html', // Callback function for the first page (Scan).
		'dashicons-shield-alt', // Icon.
		75 // Position.
	);

	// Add Scan submenu page (this will be the default page for WPClean menu).
	add_submenu_page(
		'wpclean',        // Parent slug.
		__( 'Scan', 'wpclean' ),
		__( 'Scan', 'wpclean' ),
		'manage_options',
		'wpclean',        // Menu slug (same as parent to make it the default).
		'wpclean_scan_page_html'
	);

	// Add Settings submenu page.
	add_submenu_page(
		'wpclean',        // Parent slug.
		__( 'WPClean Settings', 'wpclean' ),
		__( 'Settings', 'wpclean' ),
		'manage_options',
		'wpclean-settings', // Menu slug.
		'wpclean_settings_page_html'
	);
}
add_action( 'admin_menu', 'wpclean_add_admin_menu' );

/**
 * Run a new scan and save the results.
 */
function wpclean_run_scan() {
	// Check for nonce security.
	if ( ! isset( $_POST['wpclean_scan_nonce'] ) || ! wp_verify_nonce( $_POST['wpclean_scan_nonce'], 'wpclean_run_scan' ) ) {
		wp_die( esc_html__( 'Security check failed.', 'wpclean' ) );
	}

	// Simulate structured scan results (replace with actual scan logic later).
	$issues_found = array();

	// Example: Simulate finding an unused shortcode.
	$issues_found[] = array(
		'type'        => 'Unused Shortcode',
		'description' => 'The shortcode `[defunct_gallery]` appears to be unused.',
		'location'    => 'Post ID: 15, Title: "Old Gallery Page"',
		'severity'    => 'Medium',
		'data'        => array(
			'shortcode_tag' => 'defunct_gallery',
			'post_id'       => 15,
		),
	);

	// Example: Simulate finding a large image.
	$issues_found[] = array(
		'type'        => 'Large Image File',
		'description' => 'Image `big_photo.jpg` is over 2MB.',
		'location'    => 'Media Library ID: 120, URL: /wp-content/uploads/big_photo.jpg',
		'severity'    => 'Low',
		'data'        => array(
			'image_id'   => 120,
			'image_url'  => '/wp-content/uploads/big_photo.jpg',
			'image_size' => '2.5MB',
		),
	);

	// Generate a human-readable summary for post_content.
	$scan_summary_content = sprintf( '<h2>%s</h2>', __( 'Scan Summary', 'wpclean' ) );
	$scan_summary_content .= sprintf( '<p>%s: %d</p>', __( 'Total issues found', 'wpclean' ), count( $issues_found ) );
	$scan_summary_content .= '<ul>';
	foreach ( $issues_found as $issue ) {
		$scan_summary_content .= sprintf( '<li><strong>%s:</strong> %s (%s)</li>', esc_html( $issue['type'] ), esc_html( $issue['description'] ), esc_html( $issue['location'] ) );
	}
	$scan_summary_content .= '</ul>';

	// Create a new wpclean_scan post.
	$post_id = wp_insert_post(
		array(
			'post_title'   => sprintf( __( 'Scan Result - %s', 'wpclean' ), date( 'Y-m-d H:i:s' ) ),
			'post_content' => $scan_summary_content, // Store the human-readable summary.
			'post_status'  => 'publish', // Or 'private', etc. depending on your needs.
			'post_type'    => 'wpclean_scan',
		)
	);

	if ( is_wp_error( $post_id ) ) {
		// Handle error (e.g., log it, display a message).
		error_log( 'WPClean: Error creating scan post - ' . $post_id->get_error_message() );
		wp_die( esc_html__( 'Error running scan. Please try again.', 'wpclean' ) );
	} else {
		// Save the structured issues data as post meta.
		// The underscore prefix makes it a "hidden" custom field by default.
		update_post_meta( $post_id, '_wpclean_scan_issues_data', $issues_found );

		// Redirect to the scan results (optional).
		$redirect_url = admin_url( 'post.php?post=' . $post_id . '&action=edit' );
		wp_redirect( $redirect_url );
		exit;
	}
}
add_action( 'admin_post_wpclean_run_scan', 'wpclean_run_scan' ); // For logged-in users.
// add_action( 'admin_post_nopriv_wpclean_run_scan', 'wpclean_run_scan' ); // For non-logged-in users (if needed).

/**
 * Display the Scan page content.
 */
function wpclean_scan_page_html() {
	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'WPClean Scan', 'wpclean' ); ?></h1>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="wpclean_run_scan">
			<?php wp_nonce_field( 'wpclean_run_scan', 'wpclean_scan_nonce' ); ?>
			<button type="submit" class="button button-primary"><?php echo esc_html__( 'Run A New Scan', 'wpclean' ); ?></button>
		</form>
		<p><?php esc_html_e( 'This page will be used to initiate new scans and view past scan results.', 'wpclean' ); ?></p>
		<?php // Future: List existing 'wpclean_scan' posts here. ?>
	</div>
	<?php
}

/**
 * Display the Settings page content.
 */
function wpclean_settings_page_html() {
	// Check user capabilities.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			// Output security fields for the registered setting "wpclean_options".
			settings_fields( 'wpclean_settings_group' );
			// Output setting sections and fields.
			do_settings_sections( 'wpclean-settings' );
			// Output save settings button.
			submit_button( __( 'Save Settings', 'wpclean' ) );
			?>
		</form>
	</div>
	<?php
}

/**
 * Initialize WPClean settings, sections, and fields.
 */
function wpclean_settings_init() {
	// Register a single option to store all WPClean settings as an array.
	register_setting( 'wpclean_settings_group', 'wpclean_options' /*, 'wpclean_options_sanitize_callback' */ );

	// Add a settings section.
	add_settings_section(
		'wpclean_general_settings_section',
		__( 'General Settings', 'wpclean' ),
		'wpclean_general_settings_section_callback',
		'wpclean-settings' // Page slug where this section will be displayed.
	);

	// Add "Delete data on uninstall" field.
	add_settings_field(
		'wpclean_delete_data_on_uninstall',
		__( 'Delete plugin data on uninstall?', 'wpclean' ),
		'wpclean_delete_data_on_uninstall_render',
		'wpclean-settings',
		'wpclean_general_settings_section'
	);

	// Add "Deep scan" field.
	add_settings_field(
		'wpclean_deep_scan',
		__( 'Deep scan?', 'wpclean' ),
		'wpclean_deep_scan_render',
		'wpclean-settings',
		'wpclean_general_settings_section'
	);
}
add_action( 'admin_init', 'wpclean_settings_init' );

/**
 * Callback for the general settings section.
 */
function wpclean_general_settings_section_callback() {
	echo '<p>' . esc_html__( 'Configure the general settings for WPClean.', 'wpclean' ) . '</p>';
}

/**
 * Render the "Delete data on uninstall" checkbox.
 */
function wpclean_delete_data_on_uninstall_render() {
	$options = get_option( 'wpclean_options' );
	$value   = isset( $options['delete_data_on_uninstall'] ) ? $options['delete_data_on_uninstall'] : 0;
	?>
	<label for="wpclean_options[delete_data_on_uninstall]">
		<input type="checkbox" name="wpclean_options[delete_data_on_uninstall]" id="wpclean_options[delete_data_on_uninstall]" value="1" <?php checked( $value, 1 ); ?> />
		<?php esc_html_e( 'This will delete all of WPClean\'s database data when WPClean is deleted.', 'wpclean' ); ?>
	</label>
	<?php
}

/**
 * Render the "Deep scan" checkbox.
 */
function wpclean_deep_scan_render() {
	$options = get_option( 'wpclean_options' );
	$value   = isset( $options['deep_scan'] ) ? $options['deep_scan'] : 0;
	?>
	<label for="wpclean_options[deep_scan]">
		<input type="checkbox" name="wpclean_options[deep_scan]" id="wpclean_options[deep_scan]" value="1" <?php checked( $value, 1 ); ?> />
		<?php esc_html_e( 'Perform a deeper search.', 'wpclean' ); ?>
	</label>
	<?php
}

// Note: A sanitize callback for 'wpclean_options' (e.g., 'wpclean_options_sanitize_callback')
// should be implemented if you need to validate/sanitize the options before saving.
// For checkboxes, ensuring they are '1' or not set is usually sufficient,
// which the Settings API handles well.