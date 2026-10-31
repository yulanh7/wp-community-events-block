<?php
/**
 * Renders the Event List block.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner content (unused).
 * @var WP_Block $block      Block instance.
 *
 * @package CommunityEvents
 */

$community_events_args = array( 'posts_per_page' => $attributes['numberOfEvents'] );

// On a category archive, list that category unless the block picks one itself.
$community_events_category = $attributes['category'];
if ( ! $community_events_category && is_tax( 'ce_event_category' ) ) {
	$community_events_category = get_queried_object()->slug;
}

if ( $community_events_category ) {
	$community_events_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		array(
			'taxonomy' => 'ce_event_category',
			'field'    => 'slug',
			'terms'    => $community_events_category,
		),
	);
}

// Related events: the ACF choice if there is one, otherwise events in the same category.
if ( $attributes['related'] ) {
	$community_events_current = isset( $block->context['postId'] ) ? (int) $block->context['postId'] : 0;
	if ( ! $community_events_current ) {
		return;
	}

	$community_events_args['post__not_in'] = array( $community_events_current );

	// Read the ACF relationship as plain post meta, so this works without ACF too.
	$community_events_chosen = get_post_meta( $community_events_current, 'ce_related_events', true );
	if ( $community_events_chosen ) {
		$community_events_args['post__in'] = array_map( 'intval', (array) $community_events_chosen );
	} else {
		$community_events_terms = wp_get_post_terms( $community_events_current, 'ce_event_category', array( 'fields' => 'ids' ) );
		if ( ! $community_events_terms ) {
			return;
		}
		$community_events_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'ce_event_category',
				'terms'    => $community_events_terms,
			),
		);
	}
}

$community_events_upcoming = new WP_Query( community_events_upcoming_query_args( $community_events_args ) );
$community_events_past     = $attributes['showPast'] ? new WP_Query( community_events_past_query_args( $community_events_args ) ) : null;

// Related events with nothing to show: render nothing at all, not even the heading.
if ( $attributes['related'] && ! $community_events_upcoming->posts ) {
	return;
}

$community_events_columns = max( 1, min( 4, (int) $attributes['columns'] ) );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'ce-list' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $attributes['heading'] ) : ?>
		<h2 class="ce-list__heading"><?php echo esc_html( $attributes['heading'] ); ?></h2>
	<?php endif; ?>

	<?php
	if ( $attributes['showFilters'] ) :
		$community_events_filters = get_terms(
			array(
				'taxonomy'   => 'ce_event_category',
				'parent'     => 0,
				'hide_empty' => true,
			)
		);
		?>
		<nav class="ce-chips" aria-label="<?php esc_attr_e( 'Filter events by category', 'wp-community-events-block' ); ?>">
			<a class="ce-chip" href="<?php echo esc_url( get_post_type_archive_link( 'ce_event' ) ); ?>" <?php echo $community_events_category ? '' : 'aria-current="page"'; ?>><?php esc_html_e( 'All', 'wp-community-events-block' ); ?></a>
			<?php foreach ( $community_events_filters as $community_events_term ) : ?>
				<a class="ce-chip" href="<?php echo esc_url( get_term_link( $community_events_term ) ); ?>" <?php echo $community_events_term->slug === $community_events_category ? 'aria-current="page"' : ''; ?>><?php echo esc_html( $community_events_term->name ); ?></a>
			<?php endforeach; ?>
		</nav>
	<?php endif; ?>

	<?php if ( ! $community_events_upcoming->posts ) : ?>
		<p class="ce-empty"><?php esc_html_e( 'No upcoming events here yet. Check back soon!', 'wp-community-events-block' ); ?></p>
	<?php elseif ( $attributes['groupByMonth'] ) : ?>
		<?php
		$community_events_months = array();
		foreach ( $community_events_upcoming->posts as $community_events_id ) {
			$community_events_start = community_events_get_datetime( $community_events_id, 'ce_event_start' );
			$community_events_month = $community_events_start ? wp_date( 'F Y', $community_events_start->getTimestamp() ) : '';

			$community_events_months[ $community_events_month ][] = $community_events_id;
		}
		foreach ( $community_events_months as $community_events_month => $community_events_ids ) :
			?>
			<h3 class="ce-month"><?php echo esc_html( $community_events_month ); ?></h3>
			<div class="ce-grid" style="--ce-columns: <?php echo (int) $community_events_columns; ?>">
				<?php echo implode( '', array_map( 'community_events_render_card', $community_events_ids ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		<?php endforeach; ?>
	<?php else : ?>
		<div class="ce-grid" style="--ce-columns: <?php echo (int) $community_events_columns; ?>">
			<?php echo implode( '', array_map( 'community_events_render_card', $community_events_upcoming->posts ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	<?php endif; ?>

	<?php if ( $community_events_past && $community_events_past->posts ) : ?>
		<h3 class="ce-month"><?php esc_html_e( 'Past events', 'wp-community-events-block' ); ?></h3>
		<div class="ce-grid" style="--ce-columns: <?php echo (int) $community_events_columns; ?>">
			<?php echo implode( '', array_map( 'community_events_render_card', $community_events_past->posts ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	<?php endif; ?>
</div>
