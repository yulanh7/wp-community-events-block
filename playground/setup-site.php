<?php
/**
 * Turns a fresh WordPress install into the Lakeside Community demo site.
 *
 * Used by playground/blueprint.json after bin/seed-demo-content.php has made
 * the events. Run it on a fresh site only, it does not check for earlier runs:
 *
 *     wp eval-file wp-content/plugins/wp-community-events-block/playground/setup-site.php
 *
 * @package CommunityEvents
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit;
}

// WP-CLI runs without a user. Act as the admin, so block markup is saved
// unfiltered and terms can be set on the template parts.
wp_set_current_user( 1 );
kses_remove_filters();

$community_events_dir      = __DIR__;
$community_events_home_url = untrailingslashit( home_url() );

/*
 * Site settings.
 */
update_option( 'blogname', 'Lakeside Community' );
update_option( 'blogdescription', 'Church · community · all welcome' );
update_option( 'timezone_string', 'Australia/Sydney' );

global $wp_rewrite;
$wp_rewrite->set_permalink_structure( '/%postname%/' );
flush_rewrite_rules();

// The default "Hello world!" post and "Sample Page".
wp_delete_post( 1, true );
wp_delete_post( 2, true );

/*
 * Home page, built from the plugin's pattern, and shown on the front page.
 */
$community_events_pattern = WP_Block_Patterns_Registry::get_instance()->get_registered( 'community-events/home' );

$community_events_home_id = wp_insert_post(
	array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => 'Home',
		'post_name'    => 'home',
		'post_content' => $community_events_pattern['content'],
	)
);

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $community_events_home_id );

/*
 * Menu used by the header and footer: Home and Events.
 */
$community_events_nav_id = wp_insert_post(
	array(
		'post_type'    => 'wp_navigation',
		'post_status'  => 'publish',
		'post_title'   => 'Main menu',
		'post_content' => serialize_blocks(
			array(
				array(
					'blockName'    => 'core/navigation-link',
					'attrs'        => array(
						'label' => 'Home',
						'type'  => 'page',
						'id'    => $community_events_home_id,
						'url'   => home_url( '/' ),
						'kind'  => 'post-type',
					),
					'innerBlocks'  => array(),
					'innerHTML'    => '',
					'innerContent' => array(),
				),
				array(
					'blockName'    => 'core/navigation-link',
					'attrs'        => array(
						'label' => 'Events',
						'url'   => get_post_type_archive_link( 'ce_event' ),
						'kind'  => 'custom',
					),
					'innerBlocks'  => array(),
					'innerHTML'    => '',
					'innerContent' => array(),
				),
			)
		),
	)
);

/*
 * Header and footer template parts, saved the way the Site Editor saves them.
 */
foreach ( array( 'header', 'footer' ) as $community_events_area ) {
	$community_events_part = str_replace(
		array( '{{nav_id}}', '{{home_url}}' ),
		array( $community_events_nav_id, $community_events_home_url ),
		file_get_contents( "{$community_events_dir}/parts/{$community_events_area}.html" ) // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local file.
	);

	$community_events_part_id = wp_insert_post(
		array(
			'post_type'    => 'wp_template_part',
			'post_status'  => 'publish',
			'post_title'   => ucfirst( $community_events_area ),
			'post_name'    => $community_events_area,
			'post_content' => $community_events_part,
		)
	);

	wp_set_post_terms( $community_events_part_id, get_stylesheet(), 'wp_theme' );
	wp_set_post_terms( $community_events_part_id, $community_events_area, 'wp_template_part_area' );
}

/*
 * Fonts, colours and Additional CSS (Appearance → Editor → Styles).
 */
$community_events_styles = json_decode( file_get_contents( "{$community_events_dir}/global-styles.json" ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local file.

foreach ( $community_events_styles['settings']['typography']['fontFamilies']['custom'] as &$community_events_family ) {
	foreach ( $community_events_family['fontFace'] as &$community_events_face ) {
		$community_events_face['src'] = plugins_url( $community_events_face['src'], __FILE__ );
	}
}
unset( $community_events_family, $community_events_face );

$community_events_styles['styles']['css'] = file_get_contents( "{$community_events_dir}/site.css" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local file.

// Finds the theme's global styles post, creating it if needed.
$community_events_user_styles = WP_Theme_JSON_Resolver::get_user_data_from_wp_global_styles( wp_get_theme(), true );

wp_update_post(
	array(
		'ID'           => $community_events_user_styles['ID'],
		'post_content' => wp_slash( wp_json_encode( $community_events_styles ) ),
	)
);

/*
 * A few registrations, so Events → Registrations has something to show.
 * Added directly, so no emails are sent.
 */
$community_events_registrations = array(
	array( 'community-lunch-welcome-morning', 'Margaret Chen', 'margaret.chen@example.com', 2 ),
	array( 'community-lunch-welcome-morning', 'David Nguyen', 'david.nguyen@example.com', 1 ),
	array( 'community-lunch-welcome-morning', 'Aisha Rahman', 'aisha.rahman@example.com', 4 ),
	array( 'bible-study-gospel-of-luke', 'Priya Sharma', 'priya.sharma@example.com', 1 ),
	array( 'chinese-english-language-exchange', 'Tom O’Brien', 'tom.obrien@example.com', 1 ),
);

foreach ( $community_events_registrations as $community_events_registration ) {
	list( $community_events_slug, $community_events_name, $community_events_email, $community_events_places ) = $community_events_registration;

	$community_events_event = get_page_by_path( $community_events_slug, OBJECT, 'ce_event' );
	if ( $community_events_event ) {
		community_events_add_registration( $community_events_event->ID, $community_events_name, $community_events_email, $community_events_places );
	}
}

WP_CLI::success( 'The Lakeside Community demo site is ready.' );
