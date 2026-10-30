<?php
/**
 * Plugin Name:       Community Events Hub
 * Description:       Event listings and registrations for community groups.
 * Version:           0.1.0
 * Requires at least: 7.0
 * Requires PHP:      7.4
 * Author:            Yulan Huang
 * Plugin URI:        https://github.com/yulanh7/wp-community-events-block
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-community-events-block
 *
 * @package CommunityEvents
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
require_once __DIR__ . '/includes/post-types.php';
require_once __DIR__ . '/includes/post-meta.php';
require_once __DIR__ . '/includes/acf-fields.php';
require_once __DIR__ . '/includes/template-tags.php';
require_once __DIR__ . '/includes/assets.php';
require_once __DIR__ . '/includes/templates.php';
require_once __DIR__ . '/includes/patterns.php';
/**
 * Registers the block(s) metadata from the `blocks-manifest.php` and registers the block type(s)
 * based on the registered block metadata. Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
 */
function community_events_register_blocks() {
	wp_register_block_types_from_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
}
add_action( 'init', 'community_events_register_blocks' );
