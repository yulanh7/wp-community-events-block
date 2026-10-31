<?php
/**
 * Renders the Event Registration block.
 *
 * @var array    $attributes Block attributes (none).
 * @var string   $content    Inner content (unused).
 * @var WP_Block $block      Block instance.
 *
 * @package CommunityEvents
 */

$community_events_id = isset( $block->context['postId'] ) ? (int) $block->context['postId'] : 0;
if ( ! $community_events_id || 'ce_event' !== get_post_type( $community_events_id ) ) {
	return;
}

$community_events_start    = community_events_get_datetime( $community_events_id, 'ce_event_start' );
$community_events_capacity = (int) get_post_meta( $community_events_id, 'ce_event_capacity', true );

// Free and finished events have nothing to book; Event Details already says so.
if ( ! $community_events_capacity || ( $community_events_start && $community_events_start->getTimestamp() < time() ) ) {
	return;
}

$community_events_left = $community_events_capacity - community_events_get_registration_count( $community_events_id );
$community_events_max  = min( COMMUNITY_EVENTS_MAX_PLACES, $community_events_left );
$community_events_uid  = wp_unique_id( 'ce-register-' );
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'ce-register' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="<?php echo esc_attr( $community_events_uid ); ?>-heading">
	<h2 class="ce-register__heading" id="<?php echo esc_attr( $community_events_uid ); ?>-heading"><?php esc_html_e( 'Book your place', 'wp-community-events-block' ); ?></h2>

	<?php if ( $community_events_left < 1 ) : ?>
		<p class="ce-register__full"><?php esc_html_e( 'Sorry, this event is full.', 'wp-community-events-block' ); ?></p>
	<?php else : ?>
		<form
			class="ce-form"
			data-endpoint="<?php echo esc_url( rest_url( 'community-events/v1/events/' . $community_events_id . '/registrations' ) ); ?>"
			data-nonce="<?php echo esc_attr( wp_create_nonce( 'wp_rest' ) ); ?>"
		>
			<p class="ce-form__field">
				<label for="<?php echo esc_attr( $community_events_uid ); ?>-name"><?php esc_html_e( 'Your name', 'wp-community-events-block' ); ?></label>
				<input id="<?php echo esc_attr( $community_events_uid ); ?>-name" name="name" type="text" autocomplete="name" maxlength="100" required>
			</p>
			<p class="ce-form__field">
				<label for="<?php echo esc_attr( $community_events_uid ); ?>-email"><?php esc_html_e( 'Email', 'wp-community-events-block' ); ?></label>
				<input id="<?php echo esc_attr( $community_events_uid ); ?>-email" name="email" type="email" autocomplete="email" maxlength="100" required>
			</p>
			<p class="ce-form__field">
				<label for="<?php echo esc_attr( $community_events_uid ); ?>-places"><?php esc_html_e( 'How many people?', 'wp-community-events-block' ); ?></label>
				<select id="<?php echo esc_attr( $community_events_uid ); ?>-places" name="places">
					<?php for ( $community_events_i = 1; $community_events_i <= $community_events_max; $community_events_i++ ) : ?>
						<option value="<?php echo esc_attr( $community_events_i ); ?>"><?php echo esc_html( $community_events_i ); ?></option>
					<?php endfor; ?>
				</select>
			</p>
			<?php // Honeypot: hidden from people and screen readers, so only bots fill it in. ?>
			<p class="ce-form__trap" aria-hidden="true">
				<label for="<?php echo esc_attr( $community_events_uid ); ?>-website"><?php esc_html_e( 'Leave this empty', 'wp-community-events-block' ); ?></label>
				<input id="<?php echo esc_attr( $community_events_uid ); ?>-website" name="website" type="text" tabindex="-1" autocomplete="off">
			</p>
			<button class="ce-button" type="submit"><?php esc_html_e( 'Book my place', 'wp-community-events-block' ); ?></button>
		</form>
		<p class="ce-register__message" role="status"></p>
	<?php endif; ?>
</section>
