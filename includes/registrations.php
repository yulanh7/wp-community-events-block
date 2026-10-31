<?php
/**
 * Registrations: the database table and the functions that read and write it.
 *
 * @package CommunityEvents
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Bump this when the table structure changes, so existing sites upgrade.
const COMMUNITY_EVENTS_DB_VERSION = '1.0';

function community_events_registrations_table() {
	global $wpdb;
	return $wpdb->prefix . 'ce_registrations';
}

/**
 * Creates or updates the registrations table.
 */
function community_events_install() {
	global $wpdb;

	$table           = community_events_registrations_table();
	$charset_collate = $wpdb->get_charset_collate();

	// dbDelta is picky: one column per line, two spaces after PRIMARY KEY, KEY not INDEX.
	$sql = "CREATE TABLE {$table} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		event_id bigint(20) unsigned NOT NULL,
		name varchar(100) NOT NULL,
		email varchar(100) NOT NULL,
		places tinyint(3) unsigned NOT NULL DEFAULT 1,
		created_at datetime NOT NULL,
		PRIMARY KEY  (id),
		KEY event_id (event_id),
		UNIQUE KEY event_email (event_id,email)
	) {$charset_collate};";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );

	update_option( 'community_events_db_version', COMMUNITY_EVENTS_DB_VERSION );
}

/**
 * Runs the install again when the stored version is older than the code.
 *
 * The activation hook only runs when the plugin is switched on, not when it is
 * updated, so this check covers updates.
 */
function community_events_maybe_upgrade() {
	if ( get_option( 'community_events_db_version' ) !== COMMUNITY_EVENTS_DB_VERSION ) {
		community_events_install();
	}
}
add_action( 'plugins_loaded', 'community_events_maybe_upgrade' );

/**
 * Saves a registration.
 *
 * @param int    $event_id Event ID.
 * @param string $name     Attendee name, already sanitised.
 * @param string $email    Attendee email, already sanitised.
 * @param int    $places   Number of places.
 * @return int New registration ID, or 0 on failure.
 */
function community_events_add_registration( $event_id, $name, $email, $places ) {
	global $wpdb;

	$inserted = $wpdb->insert(
		community_events_registrations_table(),
		array(
			'event_id'   => $event_id,
			'name'       => $name,
			'email'      => $email,
			'places'     => $places,
			'created_at' => current_time( 'mysql', true ),
		),
		array( '%d', '%s', '%s', '%d', '%s' )
	);

	return $inserted ? (int) $wpdb->insert_id : 0;
}

/**
 * Checks whether an email address has already registered for an event.
 *
 * @param int    $event_id   Event ID.
 * @param string $email      Email address.
 * @param int    $exclude_id Registration to ignore, so an edit does not clash with itself.
 * @return bool
 */
function community_events_is_registered( $event_id, $email, $exclude_id = 0 ) {
	global $wpdb;

	return (bool) $wpdb->get_var(
		$wpdb->prepare(
			'SELECT id FROM %i WHERE event_id = %d AND email = %s AND id != %d',
			community_events_registrations_table(),
			$event_id,
			$email,
			$exclude_id
		)
	);
}

/**
 * Counts the places taken for an event.
 *
 * @param int $event_id Event ID.
 * @return int
 */
function community_events_count_registered_places( $event_id ) {
	global $wpdb;

	return (int) $wpdb->get_var(
		$wpdb->prepare(
			'SELECT COALESCE( SUM( places ), 0 ) FROM %i WHERE event_id = %d',
			community_events_registrations_table(),
			$event_id
		)
	);
}

/**
 * Feeds real registrations into the "places left" status on cards and pages.
 *
 * @param int $count   Places taken so far.
 * @param int $post_id Event ID.
 * @return int
 */
function community_events_filter_registration_count( $count, $post_id ) {
	return $count + community_events_count_registered_places( $post_id );
}
add_filter( 'community_events_registration_count', 'community_events_filter_registration_count', 10, 2 );

/**
 * Gets registrations, newest first.
 *
 * @param int $event_id Event ID, or 0 for all events.
 * @param int $limit    How many to return.
 * @param int $offset   How many to skip, for paging.
 * @return object[] Rows from the registrations table.
 */
function community_events_get_registrations( $event_id = 0, $limit = 20, $offset = 0 ) {
	global $wpdb;

	$table = community_events_registrations_table();

	if ( $event_id ) {
		return $wpdb->get_results(
			$wpdb->prepare(
				'SELECT * FROM %i WHERE event_id = %d ORDER BY created_at DESC, id DESC LIMIT %d OFFSET %d',
				$table,
				$event_id,
				$limit,
				$offset
			)
		);
	}

	return $wpdb->get_results(
		$wpdb->prepare( 'SELECT * FROM %i ORDER BY created_at DESC, id DESC LIMIT %d OFFSET %d', $table, $limit, $offset )
	);
}

/**
 * Counts registrations (rows, not places).
 *
 * @param int $event_id Event ID, or 0 for all events.
 * @return int
 */
function community_events_count_registrations( $event_id = 0 ) {
	global $wpdb;

	$table = community_events_registrations_table();

	if ( $event_id ) {
		return (int) $wpdb->get_var(
			$wpdb->prepare( 'SELECT COUNT(*) FROM %i WHERE event_id = %d', $table, $event_id )
		);
	}

	return (int) $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM %i', $table ) );
}

/**
 * Gets one registration.
 *
 * @param int $registration_id Registration ID.
 * @return object|null Row from the registrations table.
 */
function community_events_get_registration( $registration_id ) {
	global $wpdb;

	return $wpdb->get_row(
		$wpdb->prepare( 'SELECT * FROM %i WHERE id = %d', community_events_registrations_table(), $registration_id )
	);
}

/**
 * Updates a registration's details. The event cannot change.
 *
 * @param int    $registration_id Registration ID.
 * @param string $name            Attendee name, already sanitised.
 * @param string $email           Attendee email, already sanitised.
 * @param int    $places          Number of places.
 * @return bool Whether the row was saved.
 */
function community_events_update_registration( $registration_id, $name, $email, $places ) {
	global $wpdb;

	$updated = $wpdb->update(
		community_events_registrations_table(),
		array(
			'name'   => $name,
			'email'  => $email,
			'places' => $places,
		),
		array( 'id' => $registration_id ),
		array( '%s', '%s', '%d' ),
		array( '%d' )
	);

	return false !== $updated;
}

/**
 * Deletes registrations.
 *
 * @param int[] $registration_ids Registration IDs.
 * @return int How many were deleted.
 */
function community_events_delete_registrations( $registration_ids ) {
	global $wpdb;

	$registration_ids = array_filter( array_map( 'absint', (array) $registration_ids ) );
	if ( ! $registration_ids ) {
		return 0;
	}

	// One %d per ID, so every value still goes through prepare().
	$placeholders = implode( ', ', array_fill( 0, count( $registration_ids ), '%d' ) );

	return (int) $wpdb->query(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Only %d placeholders are added.
			"DELETE FROM %i WHERE id IN ( {$placeholders} )",
			community_events_registrations_table(),
			...$registration_ids
		)
	);
}

/**
 * The most places a registration can have when an admin edits it: the places
 * still free plus the ones it already holds.
 *
 * @param object $registration Row from the registrations table.
 * @return int
 */
function community_events_max_places_for( $registration ) {
	$capacity = (int) get_post_meta( $registration->event_id, 'ce_event_capacity', true );
	$free     = $capacity - community_events_get_registration_count( $registration->event_id );

	// The places column is a tinyint, so 255 is the hard limit.
	return min( 255, max( (int) $registration->places, $free + (int) $registration->places ) );
}
