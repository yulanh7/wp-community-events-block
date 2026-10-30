<?php
/**
 * Removes the registrations table when the plugin is deleted.
 *
 * Events are normal posts and stay, so nothing the site owner wrote is lost.
 *
 * @package CommunityEvents
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $wpdb->prefix . 'ce_registrations' ) );
delete_option( 'community_events_db_version' );
