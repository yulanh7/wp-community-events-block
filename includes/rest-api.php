<?php
/**
 * REST endpoint that the registration form posts to.
 *
 * POST /wp-json/community-events/v1/events/{id}/registrations
 *
 * @package CommunityEvents
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// The most places one person can book in one go.
const COMMUNITY_EVENTS_MAX_PLACES = 5;

function community_events_register_rest_routes() {
	register_rest_route(
		'community-events/v1',
		'/events/(?P<id>\d+)/registrations',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'community_events_rest_register',
			'permission_callback' => 'community_events_rest_check_nonce',
			'args'                => array(
				'id'      => array(
					'type'     => 'integer',
					'required' => true,
				),
				'name'    => array(
					'type'              => 'string',
					'required'          => true,
					'minLength'         => 1,
					'maxLength'         => 100,
					// A custom sanitize_callback turns off the schema check, so ask for it back.
					'validate_callback' => 'rest_validate_request_arg',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'email'   => array(
					'type'      => 'string',
					'format'    => 'email',
					'required'  => true,
					'maxLength' => 100,
				),
				'places'  => array(
					'type'    => 'integer',
					'minimum' => 1,
					'maximum' => COMMUNITY_EVENTS_MAX_PLACES,
					'default' => 1,
				),
				// Honeypot: hidden from people, so only bots fill it in.
				'website' => array(
					'type'    => 'string',
					'default' => '',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'community_events_register_rest_routes' );

/**
 * Accepts the request only when it carries a valid nonce from our form.
 *
 * Logged-in users are already checked by WordPress. Visitors are not, so check
 * here for everyone.
 *
 * @param WP_REST_Request $request Request.
 * @return true|WP_Error
 */
function community_events_rest_check_nonce( WP_REST_Request $request ) {
	if ( wp_verify_nonce( $request->get_header( 'X-WP-Nonce' ), 'wp_rest' ) ) {
		return true;
	}

	return community_events_rest_error(
		'expired',
		__( 'This page has expired. Please reload it and try again.', 'wp-community-events-block' ),
		403
	);
}

function community_events_rest_error( $code, $message, $status ) {
	return new WP_Error( 'community_events_' . $code, $message, array( 'status' => $status ) );
}

/**
 * Registers a visitor for an event.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function community_events_rest_register( WP_REST_Request $request ) {
	$event_id = $request['id'];
	$name     = $request['name'];
	$email    = $request['email'];
	$places   = $request['places'];

	// Pretend it worked, so bots learn nothing.
	if ( '' !== $request['website'] ) {
		return new WP_REST_Response( array( 'message' => __( 'Thanks, your place is booked.', 'wp-community-events-block' ) ), 201 );
	}

	if ( 'ce_event' !== get_post_type( $event_id ) || 'publish' !== get_post_status( $event_id ) ) {
		return community_events_rest_error( 'not_found', __( 'Event not found.', 'wp-community-events-block' ), 404 );
	}

	$start = community_events_get_datetime( $event_id, 'ce_event_start' );
	if ( $start && $start->getTimestamp() < time() ) {
		return community_events_rest_error( 'started', __( 'This event has already started.', 'wp-community-events-block' ), 409 );
	}

	$capacity = (int) get_post_meta( $event_id, 'ce_event_capacity', true );
	if ( ! $capacity ) {
		return community_events_rest_error( 'no_registration', __( 'This event is free entry, so there is no need to register.', 'wp-community-events-block' ), 409 );
	}

	if ( '' === $name ) {
		return community_events_rest_error( 'invalid_name', __( 'Please enter your name.', 'wp-community-events-block' ), 400 );
	}

	if ( community_events_is_registered( $event_id, $email ) ) {
		return community_events_rest_error( 'already_registered', __( 'This email address is already registered for this event.', 'wp-community-events-block' ), 409 );
	}

	$left = $capacity - community_events_get_registration_count( $event_id );
	if ( $left < 1 ) {
		return community_events_rest_error( 'full', __( 'Sorry, this event is full.', 'wp-community-events-block' ), 409 );
	}
	if ( $places > $left ) {
		return community_events_rest_error(
			'not_enough_places',
			/* translators: %d: places left. */
			sprintf( _n( 'Sorry, only %d place is left.', 'Sorry, only %d places are left.', $left, 'wp-community-events-block' ), $left ),
			409
		);
	}

	$registration_id = community_events_add_registration( $event_id, $name, $email, $places );
	if ( ! $registration_id ) {
		return community_events_rest_error( 'save_failed', __( 'Something went wrong. Please try again.', 'wp-community-events-block' ), 500 );
	}

	/**
	 * Fires after a visitor registers for an event.
	 *
	 * @param int $registration_id New registration ID.
	 * @param int $event_id        Event ID.
	 */
	do_action( 'community_events_registered', $registration_id, $event_id );

	return new WP_REST_Response(
		array(
			/* translators: %s: attendee name. */
			'message'    => sprintf( __( 'Thanks, %s! Your place is booked.', 'wp-community-events-block' ), $name ),
			'placesLeft' => $left - $places,
		),
		201
	);
}