<?php
/**
 * Renders the Upcoming Event block.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner content (unused).
 * @var WP_Block $block      Block instance.
 *
 * @package CommunityEvents
 */

$community_events_query = new WP_Query( community_events_upcoming_query_args( array( 'posts_per_page' => 1 ) ) );

if ( ! $community_events_query->posts ) : ?>
	<div <?php echo get_block_wrapper_attributes(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<p class="ce-empty"><?php esc_html_e( 'No upcoming events right now. Check back soon!', 'wp-community-events-block' ); ?></p>
	</div>
	<?php
	return;
endif;

$community_events_id    = $community_events_query->posts[0];
$community_events_start = community_events_get_datetime( $community_events_id, 'ce_event_start' );
?>
<div <?php echo get_block_wrapper_attributes(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<article class="ce-featured">
		<div class="ce-featured__media">
			<?php echo get_the_post_thumbnail( $community_events_id, 'large' ); ?>
			<span class="ce-featured__flag"><?php esc_html_e( 'Next event', 'wp-community-events-block' ); ?></span>
		</div>
		<div class="ce-featured__body">
			<?php echo community_events_render_date_tab( $community_events_start ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div>
				<?php echo community_events_render_category_pill( $community_events_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<h2 class="ce-featured__title">
					<a href="<?php echo esc_url( get_permalink( $community_events_id ) ); ?>"><?php echo esc_html( get_the_title( $community_events_id ) ); ?></a>
				</h2>
				<?php echo community_events_render_meta( $community_events_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

				<?php if ( $attributes['showCountdown'] && $community_events_start ) : ?>
					<p class="ce-countdown__label"><?php esc_html_e( 'Starts in', 'wp-community-events-block' ); ?></p>
					<div class="ce-countdown" aria-hidden="true" data-start="<?php echo esc_attr( $community_events_start->format( DATE_ATOM ) ); ?>" data-started="<?php esc_attr_e( 'Happening now', 'wp-community-events-block' ); ?>">
						<span><b data-unit="days">0</b><?php esc_html_e( 'Days', 'wp-community-events-block' ); ?></span>
						<span><b data-unit="hours">00</b><?php esc_html_e( 'Hours', 'wp-community-events-block' ); ?></span>
						<span><b data-unit="minutes">00</b><?php esc_html_e( 'Mins', 'wp-community-events-block' ); ?></span>
						<span><b data-unit="seconds">00</b><?php esc_html_e( 'Secs', 'wp-community-events-block' ); ?></span>
					</div>
				<?php endif; ?>

				<div class="ce-featured__footer">
					<a class="ce-button is-outline" href="<?php echo esc_url( get_permalink( $community_events_id ) ); ?>"><?php esc_html_e( 'Event details', 'wp-community-events-block' ); ?></a>
					<?php echo community_events_render_places( $community_events_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>
		</div>
	</article>
</div>
