<?php
/**
 * Shared helpers for querying and rendering events.
 *
 * @package CommunityEvents
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns an event date meta value as a DateTimeImmutable in the site timezone.
 *
 * @param int    $post_id Event ID.
 * @param string $key     Meta key, e.g. ce_event_start.
 * @return DateTimeImmutable|null
 */
function community_events_get_datetime( $post_id, $key ) {
	$value = get_post_meta( $post_id, $key, true );
	if ( ! $value ) {
		return null;
	}

	$date = DateTimeImmutable::createFromFormat( 'Y-m-d\TH:i', $value, wp_timezone() );

	return $date ? $date : null;
}

/**
 * Builds WP_Query arguments for events that have not started yet, soonest first.
 *
 * @param array $args Extra or overriding query arguments.
 * @return array
 */
function community_events_upcoming_query_args( $args = array() ) {
	return wp_parse_args(
		$args,
		array(
			'post_type'      => 'ce_event',
			'post_status'    => 'publish',
			'posts_per_page' => 6,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_key'       => 'ce_event_start', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => 'ce_event_start',
					'value'   => current_time( 'Y-m-d\TH:i' ),
					'compare' => '>=',
				),
			),
		)
	);
}

/**
 * Builds WP_Query arguments for events that have already started, most recent first.
 *
 * @param array $args Extra or overriding query arguments.
 * @return array
 */
function community_events_past_query_args( $args = array() ) {
	$args = community_events_upcoming_query_args( $args );

	$args['order']                    = 'DESC';
	$args['meta_query'][0]['compare'] = '<';

	return $args;
}

/**
 * Returns the slug of the top-level category, used to colour category pills.
 *
 * @param WP_Term $term Event category.
 * @return string
 */
function community_events_get_top_level_slug( $term ) {
	while ( $term->parent ) {
		$parent = get_term( $term->parent, 'ce_event_category' );
		if ( ! $parent || is_wp_error( $parent ) ) {
			break;
		}
		$term = $parent;
	}

	return $term->slug;
}

/**
 * Renders a category pill for the event's first category.
 *
 * @param int $post_id Event ID.
 * @return string
 */
function community_events_render_category_pill( $post_id ) {
	$terms = get_the_terms( $post_id, 'ce_event_category' );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return '';
	}

	return sprintf(
		'<span class="ce-pill" data-category="%1$s">%2$s</span>',
		esc_attr( community_events_get_top_level_slug( $terms[0] ) ),
		esc_html( $terms[0]->name )
	);
}

/**
 * Renders the tear-off calendar date tab.
 *
 * @param DateTimeImmutable|null $start Event start.
 * @return string
 */
function community_events_render_date_tab( $start ) {
	if ( ! $start ) {
		return '';
	}

	$timestamp = $start->getTimestamp();

	return sprintf(
		'<span class="ce-datetab" aria-hidden="true"><span class="ce-datetab__month">%1$s</span><span class="ce-datetab__day">%2$s</span><span class="ce-datetab__weekday">%3$s</span></span>',
		esc_html( wp_date( 'M', $timestamp ) ),
		esc_html( wp_date( 'j', $timestamp ) ),
		esc_html( wp_date( 'D', $timestamp ) )
	);
}

/**
 * Renders the date, time and location list.
 *
 * @param int $post_id Event ID.
 * @return string
 */
function community_events_render_meta( $post_id ) {
	$start    = community_events_get_datetime( $post_id, 'ce_event_start' );
	$end      = community_events_get_datetime( $post_id, 'ce_event_end' );
	$location = get_post_meta( $post_id, 'ce_event_location', true );
	$items    = '';

	if ( $start ) {
		$when = wp_date( 'l j F · g:i a', $start->getTimestamp() );
		if ( $end ) {
			$when .= ' – ' . wp_date( 'g:i a', $end->getTimestamp() );
		}
		$items .= '<li class="ce-meta__time">' . esc_html( $when ) . '</li>';
	}

	if ( $location ) {
		$items .= '<li class="ce-meta__location">' . esc_html( $location ) . '</li>';
	}

	return $items ? '<ul class="ce-meta">' . $items . '</ul>' : '';
}

/**
 * Returns how many places are taken. Phase 2 hooks real registrations into this filter.
 *
 * @param int $post_id Event ID.
 * @return int
 */
function community_events_get_registration_count( $post_id ) {
	return (int) apply_filters( 'community_events_registration_count', 0, $post_id );
}

/**
 * Renders the "places left" status line.
 *
 * @param int $post_id Event ID.
 * @return string
 */
function community_events_render_places( $post_id ) {
	$start = community_events_get_datetime( $post_id, 'ce_event_start' );

	if ( $start && $start->getTimestamp() < time() ) {
		return '<p class="ce-places is-muted">' . esc_html__( 'Finished', 'wp-community-events-block' ) . '</p>';
	}

	$capacity = (int) get_post_meta( $post_id, 'ce_event_capacity', true );
	if ( ! $capacity ) {
		return '<p class="ce-places is-muted">' . esc_html__( 'Free entry, no registration limit', 'wp-community-events-block' ) . '</p>';
	}

	$taken = min( $capacity, community_events_get_registration_count( $post_id ) );
	$left  = $capacity - $taken;

	if ( ! $left ) {
		return '<p class="ce-places is-full">' . esc_html__( 'Full', 'wp-community-events-block' ) . '</p>';
	}

	$label = sprintf(
		/* translators: 1: places left, 2: total places. */
		_n( '%1$d of %2$d place left', '%1$d of %2$d places left', $left, 'wp-community-events-block' ),
		$left,
		$capacity
	);

	return sprintf(
		'<p class="ce-places %1$s"><span>%2$s</span><span class="ce-places__bar" aria-hidden="true"><span style="width:%3$d%%"></span></span></p>',
		$left / $capacity <= 0.25 ? 'is-low' : 'is-open',
		esc_html( $label ),
		(int) round( $taken / $capacity * 100 )
	);
}

/**
 * Renders an event card.
 *
 * @param int $post_id Event ID.
 * @return string
 */
function community_events_render_card( $post_id ) {
	$start   = community_events_get_datetime( $post_id, 'ce_event_start' );
	$is_past = $start && $start->getTimestamp() < time();

	return sprintf(
		'<article class="ce-card%1$s"><div class="ce-card__media">%2$s%3$s</div><div class="ce-card__body">%4$s<h3 class="ce-card__title"><a href="%5$s">%6$s</a></h3>%7$s%8$s</div></article>',
		$is_past ? ' is-past' : '',
		get_the_post_thumbnail( $post_id, 'medium_large', array( 'alt' => '' ) ),
		community_events_render_date_tab( $start ),
		community_events_render_category_pill( $post_id ),
		esc_url( get_permalink( $post_id ) ),
		esc_html( get_the_title( $post_id ) ),
		community_events_render_meta( $post_id ),
		community_events_render_places( $post_id )
	);
}
