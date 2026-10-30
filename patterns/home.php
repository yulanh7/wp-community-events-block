<?php
/**
 * Home page pattern: intro, next event, coming up, categories and a volunteer band.
 *
 * Loaded through the `filePath` of register_block_pattern(), so it only runs
 * when the pattern is actually used.
 *
 * @package CommunityEvents
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$community_events_categories = get_terms(
	array(
		'taxonomy'   => 'ce_event_category',
		'parent'     => 0,
		'hide_empty' => false,
	)
);
?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:columns {"verticalAlignment":"bottom"} -->
	<div class="wp-block-columns are-vertically-aligned-bottom">
		<!-- wp:column {"verticalAlignment":"bottom","width":"60%"} -->
		<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:60%">
			<!-- wp:paragraph {"style":{"typography":{"fontWeight":"700","letterSpacing":"0.1em","textTransform":"uppercase"}},"fontSize":"small"} -->
			<p class="has-small-font-size" style="font-weight:700;letter-spacing:0.1em;text-transform:uppercase"><?php esc_html_e( 'Church · Community · All welcome', 'wp-community-events-block' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:heading {"level":1,"fontSize":"xx-large"} -->
			<h1 class="wp-block-heading has-xx-large-font-size"><?php esc_html_e( 'Everyone has a place here.', 'wp-community-events-block' ); ?></h1>
			<!-- /wp:heading -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"bottom","width":"40%"} -->
		<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:40%">
			<!-- wp:paragraph {"fontSize":"medium"} -->
			<p class="has-medium-font-size"><?php esc_html_e( 'Services, classes, festivals and volunteering for every age and every background.', 'wp-community-events-block' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( get_post_type_archive_link( 'ce_event' ) ); ?>"><?php esc_html_e( 'See what’s on', 'wp-community-events-block' ); ?></a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide">
	<!-- wp:column {"width":"62%"} -->
	<div class="wp-block-column" style="flex-basis:62%">
		<!-- wp:community-events/upcoming-event /-->
	</div>
	<!-- /wp:column -->

	<!-- wp:column {"width":"38%"} -->
	<div class="wp-block-column" style="flex-basis:38%">
		<!-- wp:group {"style":{"border":{"radius":"18px","width":"1px","color":"#dfe3dc"},"spacing":{"padding":{"top":"var:preset|spacing|40","right":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40"}}},"backgroundColor":"base","layout":{"type":"default"}} -->
		<div class="wp-block-group has-border-color has-base-background-color has-background" style="border-color:#dfe3dc;border-width:1px;border-radius:18px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">
			<!-- wp:heading {"fontSize":"large"} -->
			<h2 class="wp-block-heading has-large-font-size"><?php esc_html_e( 'Reserve your place', 'wp-community-events-block' ); ?></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><?php esc_html_e( 'Online registration is coming soon. Until then, every event page lists the time, place and who to contact.', 'wp-community-events-block' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( get_post_type_archive_link( 'ce_event' ) ); ?>"><?php esc_html_e( 'Browse events', 'wp-community-events-block' ); ?></a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:group {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|70"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide" style="margin-top:var(--wp--preset--spacing--70)">
	<!-- wp:community-events/event-list <?php echo serialize_block_attributes( array( 'heading' => __( 'Coming up', 'wp-community-events-block' ), 'numberOfEvents' => 3 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> /-->

	<!-- wp:paragraph -->
	<p><a href="<?php echo esc_url( get_post_type_archive_link( 'ce_event' ) ); ?>"><?php esc_html_e( 'See all events →', 'wp-community-events-block' ); ?></a></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<?php if ( $community_events_categories && ! is_wp_error( $community_events_categories ) ) : ?>
<!-- wp:group {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|70"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide" style="margin-top:var(--wp--preset--spacing--70)">
	<!-- wp:heading -->
	<h2 class="wp-block-heading"><?php esc_html_e( 'Find your place', 'wp-community-events-block' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p><?php esc_html_e( 'Browse by what you’re interested in.', 'wp-community-events-block' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons -->
	<div class="wp-block-buttons">
		<?php foreach ( $community_events_categories as $community_events_term ) : ?>
		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( get_term_link( $community_events_term ) ); ?>"><?php echo esc_html( $community_events_term->name ); ?></a></div>
		<!-- /wp:button -->
		<?php endforeach; ?>
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
<?php endif; ?>

<!-- wp:group {"align":"wide","style":{"border":{"radius":"18px"},"spacing":{"margin":{"top":"var:preset|spacing|70"},"padding":{"top":"var:preset|spacing|50","right":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50"}},"color":{"background":"#17302a","text":"#ffffff"}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide has-text-color has-background" style="border-radius:18px;color:#ffffff;background-color:#17302a;margin-top:var(--wp--preset--spacing--70);padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)">
	<!-- wp:heading {"textColor":"base"} -->
	<h2 class="wp-block-heading has-base-color has-text-color"><?php esc_html_e( 'Give a few hours, make a big difference.', 'wp-community-events-block' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p><?php esc_html_e( 'Our lunches, classes and festivals are run by volunteers. Training is free and you choose when you help.', 'wp-community-events-block' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons -->
	<div class="wp-block-buttons">
		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/event-category/volunteering/' ) ); ?>"><?php esc_html_e( 'Volunteer with us', 'wp-community-events-block' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
