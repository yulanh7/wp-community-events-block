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
 * @param int    $event_id Event ID.
 * @param string $email    Email address.
 * @return bool
 */
function community_events_is_registered( $event_id, $email ) {
	global $wpdb;

	return (bool) $wpdb->get_var(
		$wpdb->prepare(
			'SELECT id FROM %i WHERE event_id = %d AND email = %s',
			community_events_registrations_table(),
			$event_id,
			$email
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
