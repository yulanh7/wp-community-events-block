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
 * Registers the single event template from templates/single-ce_event.html.
 */
function community_events_register_templates() {
	register_block_template(
		'wp-community-events-block//single-ce_event',
		array(
			'title'       => __( 'Single Event', 'wp-community-events-block' ),
			'description' => __( 'Displays a single event with its details and related events.', 'wp-community-events-block' ),
			'content'     => file_get_contents( dirname( __DIR__ ) . '/templates/single-ce_event.html' ), // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		)
	);
}
add_action( 'init', 'community_events_register_templates' );
