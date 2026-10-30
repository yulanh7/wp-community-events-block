<?php
/**
 * Registers the block templates that ship with the plugin.
 *
 * @package CommunityEvents
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers each template in the templates/ folder.
 *
 * The array key is the template slug, which is also the file name.
 * WordPress picks the template from the slug (single-ce_event, archive-ce_event, ...).
 */
function community_events_register_templates() {
	$templates = array(
		'single-ce_event'            => array(
			'title'       => __( 'Single Event', 'wp-community-events-block' ),
			'description' => __( 'Displays a single event with its details and related events.', 'wp-community-events-block' ),
		),
		'archive-ce_event'           => array(
			'title'       => __( 'Events Archive', 'wp-community-events-block' ),
			'description' => __( 'Lists upcoming events with category filters, grouped by month.', 'wp-community-events-block' ),
		),
		'taxonomy-ce_event_category' => array(
			'title'       => __( 'Event Category', 'wp-community-events-block' ),
			'description' => __( 'Lists upcoming events in one event category.', 'wp-community-events-block' ),
		),
	);

	foreach ( $templates as $slug => $template ) {
		$template['content'] = file_get_contents( dirname( __DIR__ ) . '/templates/' . $slug . '.html' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		register_block_template( 'wp-community-events-block//' . $slug, $template );
	}
}
add_action( 'init', 'community_events_register_templates' );
