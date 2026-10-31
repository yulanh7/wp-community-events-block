<?php
/**
 * Registers meta fields for the Event post type.
 * 
 * @package CommunityEvents
 */ 

if ( ! defined( 'ABSPATH' ) ) {
    exit;           
}

function community_events_register_post_meta() {
    register_post_meta(
        'ce_event',
        'ce_event_location',
        array(
            'type'              => 'string',
            'single'            => true,
            'show_in_rest'      => true,
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        )
    );
    register_post_meta(
        'ce_event',
        'ce_event_start',
        array(
            'type'              => 'string',
            'single'            => true,
            'show_in_rest'      => true,
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        )
    );
    register_post_meta(
        'ce_event',
        'ce_event_end',
        array(
            'type'              => 'string',
            'single'            => true,
            'show_in_rest'      => true,
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        )
    );
    register_post_meta(
        'ce_event',
        'ce_event_capacity',
        array(
            'type'              => 'integer',
            'single'            => true,
            'show_in_rest'      => true,
            'sanitize_callback' => 'absint',
            'default'           => 0,
        )
    );

}
add_action( 'init', 'community_events_register_post_meta' );

