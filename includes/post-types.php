<?php
/**
 * Registers the Event post type and its taxonomy.
 *
 * @package CommunityEvents
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function community_events_register_post_type() {
	$labels = array(
		'name'          => __( 'Events', 'wp-community-events-block' ),
		'singular_name' => __( 'Event', 'wp-community-events-block' ),
		// TODO: add_new_item, edit_item, view_item, search_items, not_found ...
	);

	register_post_type(
		'ce_event',
		array(
			'labels'       => $labels,
			'public'       => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-calendar-alt',
			'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'events' ),
		)
	);
}
add_action( 'init', 'community_events_register_post_type' );
