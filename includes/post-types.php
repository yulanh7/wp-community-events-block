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
		'name'               => __( 'Events', 'wp-community-events-block' ),
		'singular_name'      => __( 'Event', 'wp-community-events-block' ),
		'add_new_item'       => __( 'Add New Event', 'wp-community-events-block' ),
		'edit_item'          => __( 'Edit Event', 'wp-community-events-block' ),
		'new_item'           => __( 'New Event', 'wp-community-events-block' ),
		'view_item'          => __( 'View Event', 'wp-community-events-block' ),
		'view_items'         => __( 'View Events', 'wp-community-events-block' ),
		'all_items'          => __( 'All Events', 'wp-community-events-block' ),
		'search_items'       => __( 'Search Events', 'wp-community-events-block' ),
		'not_found'          => __( 'No events found.', 'wp-community-events-block' ),
		'not_found_in_trash' => __( 'No events found in Trash.', 'wp-community-events-block' ),
		'archives'           => __( 'Event Archives', 'wp-community-events-block' ),
		'item_published'     => __( 'Event published.', 'wp-community-events-block' ),
		'item_updated'       => __( 'Event updated.', 'wp-community-events-block' ),
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

function community_events_register_taxonomy() {
	$labels = array(
		'name'          => __( 'Event Categories', 'wp-community-events-block' ),
		'singular_name' => __( 'Event Category', 'wp-community-events-block' ),
		'search_items'  => __( 'Search Event Categories', 'wp-community-events-block' ),
		'all_items'     => __( 'All Event Categories', 'wp-community-events-block' ),
		'parent_item'   => __( 'Parent Event Category', 'wp-community-events-block' ),
		'edit_item'     => __( 'Edit Event Category', 'wp-community-events-block' ),
		'update_item'   => __( 'Update Event Category', 'wp-community-events-block' ),
		'add_new_item'  => __( 'Add New Event Category', 'wp-community-events-block' ),
		'new_item_name' => __( 'New Event Category Name', 'wp-community-events-block' ),
		'menu_name'     => __( 'Categories', 'wp-community-events-block' ),
	);

	register_taxonomy(
		'ce_event_category',
		'ce_event',
		array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'event-category' ),
		)
	);
}
add_action( 'init', 'community_events_register_taxonomy' );
