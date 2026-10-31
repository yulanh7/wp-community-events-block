<?php
/**
 * Renders the Event Details block.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner content (unused).
 * @var WP_Block $block      Block instance.
 *
 * @package CommunityEvents
 */

// Only makes sense inside an event: render nothing anywhere else.
$community_events_id = isset( $block->context['postId'] ) ? (int) $block->context['postId'] : 0;
if ( ! $community_events_id || 'ce_event' !== get_post_type( $community_events_id ) ) {
	return;
}

if ( 'info' === $attributes['variant'] ) :
	// ACF fields read as plain post meta, so this works without ACF too.
	$community_events_notes = get_post_meta( $community_events_id, 'ce_accessibility_notes', true );
	$community_events_name  = get_post_meta( $community_events_id, 'ce_organizer_name', true );
	$community_events_email = get_post_meta( $community_events_id, 'ce_organizer_email', true );

	if ( ! $community_events_notes && ! $community_events_name && ! $community_events_email ) {
		return;
	}
	?>
	<div <?php echo get_block_wrapper_attributes(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php if ( $community_events_notes ) : ?>
			<section class="ce-info">
				<h2 class="ce-info__heading"><?php esc_html_e( 'Accessibility', 'wp-community-events-block' ); ?></h2>
				<p><?php echo nl2br( esc_html( $community_events_notes ) ); ?></p>
			</section>
		<?php endif; ?>

		<?php if ( $community_events_name || $community_events_email ) : ?>
			<section class="ce-info">
				<h2 class="ce-info__heading"><?php esc_html_e( 'Organiser', 'wp-community-events-block' ); ?></h2>
				<p>
					<?php if ( $community_events_name ) : ?>
						<strong><?php echo esc_html( $community_events_name ); ?></strong>
					<?php endif; ?>
					<?php if ( $community_events_name && $community_events_email ) : ?>
						&middot;
					<?php endif; ?>
					<?php if ( $community_events_email ) : ?>
						<a href="<?php echo esc_url( 'mailto:' . $community_events_email ); ?>"><?php echo esc_html( $community_events_email ); ?></a>
					<?php endif; ?>
				</p>
			</section>
		<?php endif; ?>
	</div>
	<?php
	return;
endif;

$community_events_start    = community_events_get_datetime( $community_events_id, 'ce_event_start' );
$community_events_end      = community_events_get_datetime( $community_events_id, 'ce_event_end' );
$community_events_location = get_post_meta( $community_events_id, 'ce_event_location', true );

$community_events_time = '';
if ( $community_events_start ) {
	$community_events_time = wp_date( 'g:i a', $community_events_start->getTimestamp() );
	if ( $community_events_end ) {
		$community_events_time .= ' – ' . wp_date( 'g:i a', $community_events_end->getTimestamp() );
	}
}
?>
<aside <?php echo get_block_wrapper_attributes( array( 'class' => 'ce-details' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-label="<?php esc_attr_e( 'Event details', 'wp-community-events-block' ); ?>">
	<h2 class="ce-details__heading"><?php esc_html_e( 'Event details', 'wp-community-events-block' ); ?></h2>

	<dl class="ce-facts">
		<?php if ( $community_events_start ) : ?>
			<div>
				<dt><?php esc_html_e( 'Date', 'wp-community-events-block' ); ?></dt>
				<dd><?php echo esc_html( wp_date( 'l j F Y', $community_events_start->getTimestamp() ) ); ?></dd>
			</div>
			<div>
				<dt><?php esc_html_e( 'Time', 'wp-community-events-block' ); ?></dt>
				<dd><?php echo esc_html( $community_events_time ); ?></dd>
			</div>
		<?php endif; ?>

		<?php if ( $community_events_location ) : ?>
			<div>
				<dt><?php esc_html_e( 'Location', 'wp-community-events-block' ); ?></dt>
				<dd><?php echo esc_html( $community_events_location ); ?></dd>
			</div>
		<?php endif; ?>

		<div>
			<dt><?php esc_html_e( 'Places', 'wp-community-events-block' ); ?></dt>
			<dd><?php echo community_events_render_places( $community_events_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></dd>
		</div>
	</dl>
</aside>
