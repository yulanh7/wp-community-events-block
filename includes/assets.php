<?php
/**
 * Registers the shared stylesheet used by all event blocks.
 *
 * @package CommunityEvents
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function community_events_register_assets() {
	wp_register_style(
		'community-events',
		plugins_url( 'assets/css/community-events.css', __DIR__ ),
		array(),
		filemtime( dirname( __DIR__ ) . '/assets/css/community-events.css' )
	);
}
add_action( 'init', 'community_events_register_assets', 5 );
