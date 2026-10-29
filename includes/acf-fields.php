<?php
/**
 * Registers optional ACF fields for events.
 *
 * Only runs when Advanced Custom Fields is active.
 *
 * @package CommunityEvents
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function community_events_register_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'          => 'group_ce_event_details',
			'title'        => __( 'Event Details', 'wp-community-events-block' ),
			'fields'       => array(
				array(
					'key'   => 'field_ce_organizer_name',
					'label' => __( 'Organizer name', 'wp-community-events-block' ),
					'name'  => 'ce_organizer_name',
					'type'  => 'text',
				),
				array(
					'key'          => 'field_ce_organizer_email',
					'label'        => __( 'Organizer email', 'wp-community-events-block' ),
					'name'         => 'ce_organizer_email',
					'type'         => 'email',
					'instructions' => __( 'Shown to attendees who have questions about the event.', 'wp-community-events-block' ),
				),
				array(
					'key'          => 'field_ce_accessibility_notes',
					'label'        => __( 'Accessibility notes', 'wp-community-events-block' ),
					'name'         => 'ce_accessibility_notes',
					'type'         => 'textarea',
					'instructions' => __( 'For example: wheelchair ramp, hearing loop, accessible toilets.', 'wp-community-events-block' ),
					'rows'         => 3,
				),
				array(
					'key'           => 'field_ce_related_events',
					'label'         => __( 'Related events', 'wp-community-events-block' ),
					'name'          => 'ce_related_events',
					'type'          => 'relationship',
					'post_type'     => array( 'ce_event' ),
					'filters'       => array( 'search', 'taxonomy' ),
					'return_format' => 'id',
					'max'           => 3,
				),
			),
			'location'     => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'ce_event',
					),
				),
			),
			'show_in_rest' => true,
		)
	);
}
add_action( 'acf/include_fields', 'community_events_register_acf_fields' );
