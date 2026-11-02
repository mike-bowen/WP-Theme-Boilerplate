<?php
/**
 * Register block styles.
 *
 * @since 0.9.2
 */
function frost_register_block_styles() {

	$block_styles = array(
		'core/columns' => array(
			'columns-reverse' => __( 'Reverse', 'frost' ),
		),
		'core/group' => array(
			'shadow-light' => __( 'Shadow', 'frost' ),
			'shadow-solid' => __( 'Solid', 'frost' ),
		),
		'core/list' => array(
			'no-disc' => __( 'No Disc', 'frost' ),
		),
		'core/quote' => array(
			'shadow-light' => __( 'Shadow', 'frost' ),
			'shadow-solid' => __( 'Solid', 'frost' ),
		),
		'core/social-links' => array(
			'outline' => __( 'Outline', 'frost' ),
		),
	);

	foreach ( $block_styles as $block => $styles ) {
		foreach ( $styles as $style_name => $style_label ) {
			register_block_style(
				$block,
				array(
					'name'  => $style_name,
					'label' => $style_label,
				)
			);
		}
	}
}
add_action( 'init', 'frost_register_block_styles' );

/**
 * Register block pattern categories.
 *
 * @since 1.0.4
 */
function frost_register_block_pattern_categories() {

	register_block_pattern_category(
		'frost-page',
		array(
			'label'       => __( 'Page', 'frost' ),
			'description' => __( 'Create a full page with multiple patterns that are grouped together.', 'frost' ),
		)
	);
	register_block_pattern_category(
		'frost-pricing',
		array(
			'label'       => __( 'Pricing', 'frost' ),
			'description' => __( 'Compare features for your digital products or service plans.', 'frost' ),
		)
	);

}

add_action( 'init', 'frost_register_block_pattern_categories' );


/**
 * Restrict certain blocks for client sites.
 *
 * @since 1.0.0
 */
add_filter( 'allowed_block_types_all', 'sai_allowed_block_types', 10, 2 );
function sai_allowed_block_types( $allowed_blocks, $editor_context ) {
	// Full list of core blocks from
	// https://developer.wordpress.org/block-editor/reference-guides/core-blocks/
	// Remove any block below to disallow it from the editor.
	$whitelist = array(

		// Accordion.
		'core/accordion',
		'core/accordion-heading',
		'core/accordion-item',
		'core/accordion-panel',

		// Text.
		'core/paragraph',
		'core/heading',
		'core/list',
		'core/list-item',
		'core/quote',
		'core/pullquote',
		// 'core/code',
		// 'core/preformatted',
		// 'core/verse',          // Poetry.
		// 'core/details',
		// 'core/footnotes',
		'core/table',
		// 'core/math',
		// 'core/freeform',       // Classic.
		// 'core/missing',        // Unsupported (fallback).

		// Media.
		'core/image',
		'core/gallery',
		// 'core/audio',
		'core/video',
		'core/file',
		'core/cover',
		'core/media-text',
		'core/icon',
		// 'core/playlist',       // Experimental.
		// 'core/playlist-track', // Experimental.

		// Design / Layout.
		'core/columns',
		'core/column',
		'core/group',
		'core/buttons',
		'core/button',
		'core/separator',
		'core/spacer',
		// 'core/more',
		// 'core/nextpage',       // Page Break.
		// 'core/text-columns',   // Deprecated.

		// Tabs (experimental).
		// 'core/tabs',
		// 'core/tab',
		// 'core/tab-list',
		// 'core/tab-panel',
		// 'core/tab-panels',
		// 'core/table-of-contents', // Experimental.

		// Widgets.
		// 'core/archives',
		// 'core/calendar',
		// 'core/categories',     // Terms List.
		// 'core/latest-comments',
		// 'core/latest-posts',
		// 'core/page-list',
		// 'core/page-list-item',
		// 'core/rss',
		'core/search',
		'core/shortcode',
		'core/social-links',
		'core/social-link',
		// 'core/tag-cloud',
		'core/html',           // Custom HTML.

		// Forms (experimental).
		// 'core/form',
		// 'core/form-input',
		// 'core/form-submission-notification',
		// 'core/form-submit-button',

		// Embed.
		'core/embed',

		// Reusable / Pattern.
		'core/block',          // Pattern (synced).
		'core/pattern',        // Pattern Placeholder.

		// Theme / Site.
		'core/site-title',
		'core/site-tagline',
		'core/site-logo',
		'core/template-part',
		'core/breadcrumbs',
		// 'core/loginout',
		// 'core/avatar',
		'core/read-more',           // Enabled for sai/news-post-list (OQ#12).

		// Navigation.
		'core/navigation',
		'core/navigation-link',
		'core/navigation-submenu',
		'core/navigation-overlay-close',
		'core/home-link',

		// Post (single post bindings).
		'core/post-title',          // Enabled for sai/news-post-list (OQ#12).
		// 'core/post-content',
		// 'core/post-date',
		'core/post-excerpt',        // Enabled for sai/news-post-list (OQ#12).
		'core/post-featured-image', // Enabled for sai/news-post-list (OQ#12).
		// 'core/post-terms',
		// 'core/post-navigation-link',
		// 'core/post-time-to-read',
		// 'core/post-author',           // Deprecated.
		// 'core/post-author-name',
		// 'core/post-author-biography',

		// Comments.
		// 'core/comments',
		// 'core/comments-title',
		// 'core/comment-template',
		// 'core/comment-author-name',
		// 'core/comment-author-avatar', // Deprecated.
		// 'core/comment-content',
		// 'core/comment-date',
		// 'core/comment-edit-link',
		// 'core/comment-reply-link',
		// 'core/comments-pagination',
		// 'core/comments-pagination-next',
		// 'core/comments-pagination-numbers',
		// 'core/comments-pagination-previous',
		// 'core/post-comment',           // Deprecated.
		// 'core/post-comments-count',
		// 'core/post-comments-form',
		// 'core/post-comments-link',

		// Query Loop.
		// Query Loop blocks enabled for sai/news-post-list — Mike, Jul 2/Jul 6: news is
		// standard WP posts rendered natively, numbered pagination (OQ#12 RESOLVED).
		'core/query',
		'core/post-template',
		'core/query-no-results',
		'core/query-pagination',
		'core/query-pagination-next',
		'core/query-pagination-numbers',
		'core/query-pagination-previous',
		// 'core/query-title',
		// 'core/query-total',

		// Terms Query.
		// 'core/terms-query',
		// 'core/term-template',
		// 'core/term-name',
		// 'core/term-description',
		// 'core/term-count',
	);

	return $whitelist;
}