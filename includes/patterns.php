<?php
/**
 * Registers the block patterns that ship with the plugin.
 *
 * @package CommunityEvents
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the "Community Events" pattern category and the home page pattern.
 */
function community_events_register_patterns() {
	register_block_pattern_category(
		'community-events',
		array( 'label' => __( 'Community Events', 'wp-community-events-block' ) )
	);

	register_block_pattern(
		'community-events/home',
		array(
			'title'       => __( 'Community home page', 'wp-community-events-block' ),
			'description' => __( 'Intro, the next event with a countdown, upcoming events, categories and a volunteer call to action.', 'wp-community-events-block' ),
			'categories'  => array( 'community-events' ),
			'postTypes'   => array( 'page' ),
			'filePath'    => dirname( __DIR__ ) . '/patterns/home.php',
		)
	);
}
add_action( 'init', 'community_events_register_patterns' );
