<?php
/**
 * Emails sent after someone registers: a confirmation to them and a notice to the organiser.
 *
 * @package CommunityEvents
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sends both emails for a new registration.
 *
 * @param int $registration_id Registration ID.
 * @param int $event_id        Event ID.
 */
function community_events_send_registration_emails( $registration_id, $event_id ) {
	$registration = community_events_get_registration( $registration_id );
	if ( ! $registration ) {
		return;
	}

	$title     = get_post_field( 'post_title', $event_id );
	$start     = community_events_get_datetime( $event_id, 'ce_event_start' );
	$location  = get_post_meta( $event_id, 'ce_event_location', true );
	$organiser = get_post_meta( $event_id, 'ce_organizer_email', true );
	$organiser = is_email( $organiser ) ? $organiser : get_option( 'admin_email' );

	$details = implode(
		"\n",
		array(
			/* translators: %s: event title. */
			sprintf( __( 'Event: %s', 'wp-community-events-block' ), $title ),
			/* translators: %s: event date and time. */
			sprintf( __( 'When: %s', 'wp-community-events-block' ), $start ? wp_date( 'l j F Y, g:i a', $start->getTimestamp() ) : '' ),
			/* translators: %s: event location. */
			sprintf( __( 'Where: %s', 'wp-community-events-block' ), $location ),
			/* translators: %d: number of places booked. */
			sprintf( __( 'Places: %d', 'wp-community-events-block' ), $registration->places ),
		)
	);

	$confirmation = array(
		'to'      => $registration->email,
		/* translators: %s: event title. */
		'subject' => sprintf( __( 'You are registered: %s', 'wp-community-events-block' ), $title ),
		'message' => sprintf(
			/* translators: 1: attendee name, 2: event details, 3: event link. */
			__( "Hi %1\$s,\n\nThanks for registering. Here are the details:\n\n%2\$s\n\nQuestions? Reply to this email to reach the organiser.\n\n%3\$s", 'wp-community-events-block' ),
			$registration->name,
			$details,
			get_permalink( $event_id )
		),
		'headers' => array( 'Reply-To: ' . $organiser ),
	);

	$capacity = (int) get_post_meta( $event_id, 'ce_event_capacity', true );
	$left     = $capacity - community_events_get_registration_count( $event_id );

	$notice = array(
		'to'      => $organiser,
		/* translators: %s: event title. */
		'subject' => sprintf( __( 'New registration: %s', 'wp-community-events-block' ), $title ),
		'message' => sprintf(
			/* translators: 1: attendee name, 2: attendee email, 3: event details, 4: places left, 5: total places, 6: link to the registrations screen. */
			__( "%1\$s (%2\$s) has registered.\n\n%3\$s\n\n%4\$d of %5\$d places left.\n\nAll registrations: %6\$s", 'wp-community-events-block' ),
			$registration->name,
			$registration->email,
			$details,
			max( 0, $left ),
			$capacity,
			admin_url( 'edit.php?post_type=ce_event&page=ce-registrations&event_id=' . $event_id )
		),
		'headers' => array( 'Reply-To: ' . $registration->email ),
	);

	/**
	 * Filters the confirmation email before it is sent.
	 *
	 * @param array  $confirmation to, subject, message and headers for wp_mail().
	 * @param object $registration The registration.
	 */
	$confirmation = apply_filters( 'community_events_confirmation_email', $confirmation, $registration );

	/**
	 * Filters the organiser notice before it is sent.
	 *
	 * @param array  $notice       to, subject, message and headers for wp_mail().
	 * @param object $registration The registration.
	 */
	$notice = apply_filters( 'community_events_organiser_email', $notice, $registration );

	wp_mail( $confirmation['to'], $confirmation['subject'], $confirmation['message'], $confirmation['headers'] );
	wp_mail( $notice['to'], $notice['subject'], $notice['message'], $notice['headers'] );
}
add_action( 'community_events_registered', 'community_events_send_registration_emails', 10, 2 );
