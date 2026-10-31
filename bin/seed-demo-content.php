<?php
/**
 * Creates demo event categories and events for local development.
 *
 * Usage (from the WordPress root, e.g. in Local's "Open site shell"):
 *
 *     wp eval-file wp-content/plugins/wp-community-events-block/bin/seed-demo-content.php
 *
 * Running it again updates the demo content instead of duplicating it.
 * Featured images are attached from demo-images/<event-slug>.jpg when present.
 *
 * @package CommunityEvents
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit;
}

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$community_events_categories = array(
	'worship'      => array( 'Worship & Faith', '' ),
	'sunday'       => array( 'Sunday Service', 'worship' ),
	'bible-study'  => array( 'Bible Study', 'worship' ),
	'community'    => array( 'Community', '' ),
	'family-kids'  => array( 'Family & Kids', 'community' ),
	'seniors'      => array( 'Seniors', 'community' ),
	'volunteering' => array( 'Volunteering', 'community' ),
	'learning'     => array( 'Learning', '' ),
	'workshops'    => array( 'Workshops', 'learning' ),
	'language'     => array( 'Language Classes', 'learning' ),
	'festivals'    => array( 'Festivals & Celebrations', '' ),
);

$community_events_events = array(
	array(
		'slug'          => 'sunday-family-service',
		'title'         => 'Sunday Family Service',
		'start'         => '2026-11-01T10:00',
		'end'           => '2026-11-01T11:30',
		'location'      => 'Main Hall, 12 Church Street',
		'capacity'      => 0,
		'categories'    => array( 'sunday' ),
		'excerpt'       => 'A relaxed, bilingual service for all ages, with a kids program and morning tea afterwards.',
		'content'       => array(
			'Join us for a relaxed service in English and Mandarin. Families are welcome, and there is a kids program running at the same time in Room 2.',
			'Stay for morning tea afterwards and meet the community. No registration is needed.',
		),
		'organizer'     => 'Pastor Daniel Wong',
		'email'         => 'office@example.org',
		'accessibility' => "Step-free entrance from the car park.\nHearing loop in the Main Hall.",
		'related'       => array( 'bible-study-gospel-of-luke', 'christmas-carols-night' ),
	),
	array(
		'slug'          => 'bible-study-gospel-of-luke',
		'title'         => 'Bible Study: The Gospel of Luke',
		'start'         => '2026-11-05T19:30',
		'end'           => '2026-11-05T21:00',
		'location'      => 'Room 1, 12 Church Street',
		'capacity'      => 12,
		'categories'    => array( 'bible-study' ),
		'excerpt'       => 'A small-group study working through Luke, chapter by chapter. Newcomers welcome.',
		'content'       => array(
			'This term we are reading the Gospel of Luke together. Each week we look at one chapter, share questions and pray for each other.',
			'Bibles are provided in English and Chinese. Tea and biscuits from 7:15 pm.',
		),
		'organizer'     => 'Grace Lee',
		'email'         => 'biblestudy@example.org',
		'accessibility' => 'Room 1 is on the ground floor, next to the accessible toilet.',
		'related'       => array( 'sunday-family-service' ),
	),
	array(
		'slug'          => 'community-lunch-welcome-morning',
		'title'         => 'Community Lunch & Welcome Morning',
		'start'         => '2026-11-08T11:30',
		'end'           => '2026-11-08T14:00',
		'location'      => 'Community Hall, 45 Park Road',
		'capacity'      => 60,
		'categories'    => array( 'community' ),
		'excerpt'       => 'New to the area? Share a free home-cooked lunch and find out what is happening in the neighbourhood.',
		'content'       => array(
			'Our monthly welcome lunch is a chance for newcomers and long-time locals to meet. Volunteers cook a shared lunch with vegetarian and halal options.',
			'Local groups will have information tables about English classes, sport clubs and support services.',
		),
		'organizer'     => 'Michelle Chen',
		'email'         => 'welcome@example.org',
		'accessibility' => "Wheelchair accessible hall and toilets.\nPlease tell us about dietary needs when you register.",
		'related'       => array( 'volunteer-training-day', 'chinese-english-language-exchange' ),
	),
	array(
		'slug'          => 'chinese-english-language-exchange',
		'title'         => 'Chinese–English Language Exchange',
		'start'         => '2026-11-12T19:00',
		'end'           => '2026-11-12T20:30',
		'location'      => 'Library Meeting Room, 3 Station Street',
		'capacity'      => 20,
		'categories'    => array( 'language' ),
		'excerpt'       => 'Practise conversation in pairs: half the evening in English, half in Mandarin.',
		'content'       => array(
			'Whether you are learning English or Mandarin, come and practise with a friendly partner. We switch languages halfway through the evening.',
			'All levels are welcome. Conversation cards are provided for beginners.',
		),
		'organizer'     => 'Kevin Zhang',
		'email'         => 'language@example.org',
		'accessibility' => 'The library has a lift and accessible parking at the rear.',
		'related'       => array( 'community-lunch-welcome-morning' ),
	),
	array(
		'slug'          => 'seniors-tai-chi-in-the-park',
		'title'         => "Seniors' Tai Chi in the Park",
		'start'         => '2026-11-14T08:00',
		'end'           => '2026-11-14T09:00',
		'location'      => 'Memorial Park, near the rotunda',
		'capacity'      => 0,
		'categories'    => array( 'seniors' ),
		'excerpt'       => 'Gentle morning tai chi for over-60s. No experience needed, just comfortable clothes.',
		'content'       => array(
			'A gentle, low-impact session led by an experienced instructor. Movements can be done standing or seated.',
			'If it rains, we move to the Community Hall at 45 Park Road.',
		),
		'organizer'     => 'Mrs. Lin Hua',
		'email'         => 'seniors@example.org',
		'accessibility' => "Seated options for every movement.\nFlat, paved path from the park entrance.",
		'related'       => array( 'smartphone-basics-for-seniors' ),
	),
	array(
		'slug'          => 'smartphone-basics-for-seniors',
		'title'         => 'Smartphone Basics for Seniors',
		'start'         => '2026-11-18T14:00',
		'end'           => '2026-11-18T15:30',
		'location'      => 'Library Meeting Room, 3 Station Street',
		'capacity'      => 12,
		'categories'    => array( 'workshops', 'seniors' ),
		'excerpt'       => 'Learn to make video calls, send photos and stay safe from scams, with one-on-one help.',
		'content'       => array(
			'Bring your phone and learn the basics at your own pace: video calls with family, sharing photos and spotting scam messages.',
			'Volunteers help in English, Mandarin and Cantonese. Places are limited so everyone gets one-on-one help.',
		),
		'organizer'     => 'Kevin Zhang',
		'email'         => 'workshops@example.org',
		'accessibility' => 'Large-print handouts available.',
		'related'       => array( 'seniors-tai-chi-in-the-park' ),
	),
	array(
		'slug'          => 'kids-craft-workshop-paper-lanterns',
		'title'         => 'Kids Craft Workshop: Paper Lanterns',
		'start'         => '2026-11-21T10:00',
		'end'           => '2026-11-21T12:00',
		'location'      => 'Room 2, 12 Church Street',
		'capacity'      => 15,
		'categories'    => array( 'family-kids', 'workshops' ),
		'excerpt'       => 'Children aged 5–12 make and decorate their own paper lantern to take home.',
		'content'       => array(
			'Kids will fold, cut and decorate a traditional paper lantern, and hear the story behind lantern festivals.',
			'All materials are provided. A parent or carer must stay for the session.',
		),
		'organizer'     => 'Sarah Ho',
		'email'         => 'kids@example.org',
		'accessibility' => 'Pram parking and a baby change table are available.',
		'related'       => array( 'lunar-new-year-festival', 'christmas-carols-night' ),
	),
	array(
		'slug'          => 'volunteer-training-day',
		'title'         => 'Volunteer Training Day',
		'start'         => '2026-11-28T09:30',
		'end'           => '2026-11-28T15:00',
		'location'      => 'Community Hall, 45 Park Road',
		'capacity'      => 30,
		'categories'    => array( 'volunteering' ),
		'excerpt'       => 'Everything new volunteers need to know: safety, first aid basics and how our programs run.',
		'content'       => array(
			'This training is required for new volunteers and a refresher for existing ones. Sessions cover child safety, food handling and first aid basics.',
			'Lunch is provided. You will receive a certificate of attendance.',
		),
		'organizer'     => 'Michelle Chen',
		'email'         => 'volunteer@example.org',
		'accessibility' => 'Wheelchair accessible hall. Training materials available in large print.',
		'related'       => array( 'community-lunch-welcome-morning', 'spring-garden-working-bee' ),
	),
	array(
		'slug'          => 'christmas-carols-night',
		'title'         => 'Christmas Carols Night',
		'start'         => '2026-12-19T18:30',
		'end'           => '2026-12-19T20:30',
		'location'      => 'Church Lawn, 12 Church Street',
		'capacity'      => 150,
		'categories'    => array( 'festivals' ),
		'excerpt'       => 'Bring a picnic rug for carols by candlelight, a children’s nativity and a sausage sizzle.',
		'content'       => array(
			'Our annual carols night features the community choir, a children’s nativity play and carols in English and Chinese.',
			'Bring a picnic rug or chair. Candles and song sheets are provided. Sausage sizzle from 6 pm.',
		),
		'organizer'     => 'Pastor Daniel Wong',
		'email'         => 'events@example.org',
		'accessibility' => "Reserved seating for people with limited mobility.\nAccessible toilets inside the hall.",
		'related'       => array( 'sunday-family-service', 'kids-craft-workshop-paper-lanterns' ),
	),
	array(
		'slug'          => 'lunar-new-year-festival',
		'title'         => 'Lunar New Year Festival',
		'start'         => '2027-02-06T11:00',
		'end'           => '2027-02-06T16:00',
		'location'      => 'Town Square',
		'capacity'      => 300,
		'categories'    => array( 'festivals', 'family-kids' ),
		'excerpt'       => 'Celebrate the Year of the Goat with lion dancing, food stalls, calligraphy and kids activities.',
		'content'       => array(
			'Welcome the Year of the Goat with a lion dance at 11 am, food stalls, Chinese calligraphy, lantern making and performances from local groups.',
			'Entry is free. Registration helps us plan seating and catering.',
		),
		'organizer'     => 'Community Events Team',
		'email'         => 'events@example.org',
		'accessibility' => "Step-free access across the square.\nQuiet space available in the library for anyone who needs a break from the noise.",
		'related'       => array( 'kids-craft-workshop-paper-lanterns', 'christmas-carols-night' ),
	),
	array(
		'slug'          => 'mid-autumn-mooncake-evening',
		'title'         => 'Mid-Autumn Mooncake Evening',
		'start'         => '2026-09-25T18:00',
		'end'           => '2026-09-25T20:30',
		'location'      => 'Community Hall, 45 Park Road',
		'capacity'      => 80,
		'categories'    => array( 'festivals' ),
		'excerpt'       => 'Mooncakes, tea and lanterns to celebrate the Mid-Autumn Festival together.',
		'content'       => array(
			'Share mooncakes and tea, hear the story of Chang’e and the moon, and join the lantern walk around the park.',
		),
		'organizer'     => 'Community Events Team',
		'email'         => 'events@example.org',
		'accessibility' => 'Wheelchair accessible hall. The lantern walk follows a flat, paved path.',
		'related'       => array( 'lunar-new-year-festival' ),
	),
	array(
		'slug'          => 'spring-garden-working-bee',
		'title'         => 'Spring Garden Working Bee',
		'start'         => '2026-10-18T09:00',
		'end'           => '2026-10-18T12:00',
		'location'      => 'Community Garden, 45 Park Road',
		'capacity'      => 25,
		'categories'    => array( 'volunteering' ),
		'excerpt'       => 'Help plant the spring vegetable beds. Gloves, tools and morning tea provided.',
		'content'       => array(
			'Spend a morning planting tomatoes, herbs and beans in the community garden. No gardening experience needed.',
		),
		'organizer'     => 'Michelle Chen',
		'email'         => 'volunteer@example.org',
		'accessibility' => 'Raised garden beds for seated gardening.',
		'related'       => array( 'volunteer-training-day' ),
	),
);

// The first event above is on 1 November 2026. Later runs (such as a fresh
// Playground demo) move every event forward by whole weeks, so the first one is
// still upcoming and each one stays on the same day of the week.
$community_events_weeks = max( 0, (int) ceil( ( time() - strtotime( '2026-11-01 10:00' ) ) / WEEK_IN_SECONDS ) );
$community_events_shift = function ( $datetime ) use ( $community_events_weeks ) {
	return ( new DateTimeImmutable( $datetime ) )->modify( "+{$community_events_weeks} weeks" )->format( 'Y-m-d\TH:i' );
};

$community_events_term_ids = array();

foreach ( $community_events_categories as $community_events_slug => $community_events_category ) {
	list( $community_events_name, $community_events_parent ) = $community_events_category;

	$community_events_existing = get_term_by( 'slug', $community_events_slug, 'ce_event_category' );
	if ( $community_events_existing ) {
		$community_events_term_ids[ $community_events_slug ] = $community_events_existing->term_id;
		continue;
	}

	$community_events_result = wp_insert_term(
		$community_events_name,
		'ce_event_category',
		array(
			'slug'   => $community_events_slug,
			'parent' => $community_events_parent ? $community_events_term_ids[ $community_events_parent ] : 0,
		)
	);

	if ( is_wp_error( $community_events_result ) ) {
		WP_CLI::error( $community_events_result->get_error_message() );
	}

	$community_events_term_ids[ $community_events_slug ] = $community_events_result['term_id'];
	WP_CLI::log( "Created category: {$community_events_name}" );
}

$community_events_post_ids = array();
$community_events_image_dir = dirname( __DIR__ ) . '/demo-images';

foreach ( $community_events_events as $community_events_event ) {
	$community_events_content = '';
	foreach ( $community_events_event['content'] as $community_events_paragraph ) {
		$community_events_content .= "<!-- wp:paragraph -->\n<p>" . esc_html( $community_events_paragraph ) . "</p>\n<!-- /wp:paragraph -->\n\n";
	}

	$community_events_existing = get_page_by_path( $community_events_event['slug'], OBJECT, 'ce_event' );

	$community_events_post_id = wp_insert_post(
		array(
			'ID'           => $community_events_existing ? $community_events_existing->ID : 0,
			'post_type'    => 'ce_event',
			'post_status'  => 'publish',
			'post_name'    => $community_events_event['slug'],
			'post_title'   => $community_events_event['title'],
			'post_excerpt' => $community_events_event['excerpt'],
			'post_content' => trim( $community_events_content ),
			'meta_input'   => array(
				'ce_event_start'    => $community_events_shift( $community_events_event['start'] ),
				'ce_event_end'      => $community_events_shift( $community_events_event['end'] ),
				'ce_event_location' => $community_events_event['location'],
				'ce_event_capacity' => $community_events_event['capacity'],
			),
		),
		true
	);

	if ( is_wp_error( $community_events_post_id ) ) {
		WP_CLI::error( $community_events_post_id->get_error_message() );
	}

	$community_events_post_ids[ $community_events_event['slug'] ] = $community_events_post_id;

	wp_set_object_terms(
		$community_events_post_id,
		array_map(
			function ( $slug ) use ( $community_events_term_ids ) {
				return $community_events_term_ids[ $slug ];
			},
			$community_events_event['categories']
		),
		'ce_event_category'
	);

	if ( function_exists( 'update_field' ) ) {
		update_field( 'field_ce_organizer_name', $community_events_event['organizer'], $community_events_post_id );
		update_field( 'field_ce_organizer_email', $community_events_event['email'], $community_events_post_id );
		update_field( 'field_ce_accessibility_notes', $community_events_event['accessibility'], $community_events_post_id );
	}

	$community_events_image = $community_events_image_dir . '/' . $community_events_event['slug'] . '.jpg';
	if ( ! has_post_thumbnail( $community_events_post_id ) && file_exists( $community_events_image ) ) {
		$community_events_tmp = wp_tempnam( $community_events_image );
		copy( $community_events_image, $community_events_tmp );

		$community_events_attachment_id = media_handle_sideload(
			array(
				'name'     => basename( $community_events_image ),
				'tmp_name' => $community_events_tmp,
			),
			$community_events_post_id,
			$community_events_event['title']
		);

		if ( is_wp_error( $community_events_attachment_id ) ) {
			WP_CLI::warning( "Image for {$community_events_event['slug']}: " . $community_events_attachment_id->get_error_message() );
		} else {
			set_post_thumbnail( $community_events_post_id, $community_events_attachment_id );
			update_post_meta( $community_events_attachment_id, '_wp_attachment_image_alt', $community_events_event['title'] );
		}
	}

	$community_events_action = $community_events_existing ? 'Updated' : 'Created';
	WP_CLI::log( "{$community_events_action} event: {$community_events_event['title']}" );
}

if ( function_exists( 'update_field' ) ) {
	foreach ( $community_events_events as $community_events_event ) {
		$community_events_related = array();
		foreach ( $community_events_event['related'] as $community_events_slug ) {
			$community_events_related[] = $community_events_post_ids[ $community_events_slug ];
		}
		update_field( 'field_ce_related_events', $community_events_related, $community_events_post_ids[ $community_events_event['slug'] ] );
	}
} else {
	WP_CLI::warning( 'ACF is not active, so organizer, accessibility and related event fields were skipped.' );
}

WP_CLI::success( count( $community_events_events ) . ' demo events are ready.' );
