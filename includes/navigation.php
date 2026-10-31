<?php
/**
 * Marks the Events menu item as the current section on event pages.
 *
 * @package CommunityEvents
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds `current-menu-ancestor` to a Navigation Link that points to the events
 * archive while a single event or an event category is shown.
 *
 * WordPress marks the link itself only on /events/, so without this the menu
 * shows no current item on an event page.
 *
 * @param string $block_content Rendered link.
 * @param array  $block         Parsed block.
 * @return string
 */
function community_events_mark_events_menu_item( $block_content, $block ) {
	if ( ! is_singular( 'ce_event' ) && ! is_tax( 'ce_event_category' ) ) {
		return $block_content;
	}

	$url     = isset( $block['attrs']['url'] ) ? $block['attrs']['url'] : '';
	$archive = get_post_type_archive_link( 'ce_event' );
	if ( ! $url || ! $archive || untrailingslashit( $url ) !== untrailingslashit( $archive ) ) {
		return $block_content;
	}

	$processor = new WP_HTML_Tag_Processor( $block_content );
	if ( $processor->next_tag( 'li' ) ) {
		$processor->add_class( 'current-menu-ancestor' );
	}

	return $processor->get_updated_html();
}
add_filter( 'render_block_core/navigation-link', 'community_events_mark_events_menu_item', 10, 2 );
