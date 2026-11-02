<?php
/**
 * Drop comment and trackback support from every post type.
 *
 * @since 1.4.27
 *
 * @return void
 */
function sai_disable_comments_support() {
	foreach ( get_post_types() as $post_type ) {
		remove_post_type_support( $post_type, 'comments' );
		remove_post_type_support( $post_type, 'trackbacks' );
	}
}
add_action( 'init', 'sai_disable_comments_support', 100 );

/**
 * Force comments and pingbacks closed.
 *
 * @since 1.4.27
 *
 * @return false
 */
function sai_comments_closed() {
	return false;
}
add_filter( 'comments_open', 'sai_comments_closed', 20, 2 );
add_filter( 'pings_open', 'sai_comments_closed', 20, 2 );

/**
 * Hide comments stored before comments were disabled.
 *
 * @since 1.4.27
 *
 * @return array<int, WP_Comment>
 */
function sai_hide_existing_comments() {
	return array();
}
add_filter( 'comments_array', 'sai_hide_existing_comments', 10, 2 );

/**
 * Remove Comments from the admin sidebar and from post edit screens.
 *
 * @since 1.4.27
 *
 * @return void
 */
function sai_remove_comments_admin_menu() {
	remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'sai_remove_comments_admin_menu' );

/**
 * Remove comment meta boxes after core registers them.
 *
 * @since 1.4.27
 *
 * @return void
 */
function sai_remove_comments_meta_boxes() {
	foreach ( get_post_types() as $post_type ) {
		remove_meta_box( 'commentstatusdiv', $post_type, 'normal' );
		remove_meta_box( 'commentsdiv', $post_type, 'normal' );
		remove_meta_box( 'trackbacksdiv', $post_type, 'normal' );
	}
}
add_action( 'add_meta_boxes', 'sai_remove_comments_meta_boxes', 99 );

/**
 * Send direct visits to the comments screen back to the dashboard.
 *
 * @since 1.4.27
 *
 * @return void
 */
function sai_block_comments_admin_screen() {
	global $pagenow;

	if ( 'edit-comments.php' === $pagenow ) {
		wp_safe_redirect( admin_url() );
		exit;
	}
}
add_action( 'admin_init', 'sai_block_comments_admin_screen' );

/**
 * Remove the comments node from the admin bar.
 *
 * @since 1.4.27
 *
 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance.
 * @return void
 */
function sai_remove_comments_admin_bar( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'comments' );
}
add_action( 'admin_bar_menu', 'sai_remove_comments_admin_bar', 999 );

/**
 * Remove the recent comments dashboard widget.
 *
 * @since 1.4.27
 *
 * @return void
 */
function sai_remove_comments_dashboard_widget() {
	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
}
add_action( 'wp_dashboard_setup', 'sai_remove_comments_dashboard_widget' );