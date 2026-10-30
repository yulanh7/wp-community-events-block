<?php
/**
 * The table on the Events → Registrations screen.
 *
 * @package CommunityEvents
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Community_Events_Registrations_Table extends WP_List_Table {

	const PER_PAGE = 20;

	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'registration',
				'plural'   => 'registrations',
				'ajax'     => false,
			)
		);
	}

	/**
	 * The event picked in the filter, or 0 for all events.
	 *
	 * @return int
	 */
	public function get_event_filter() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- A read-only filter.
		return isset( $_GET['event_id'] ) ? absint( $_GET['event_id'] ) : 0;
	}

	public function get_columns() {
		return array(
			'name'       => __( 'Name', 'wp-community-events-block' ),
			'email'      => __( 'Email', 'wp-community-events-block' ),
			'event'      => __( 'Event', 'wp-community-events-block' ),
			'places'     => __( 'Places', 'wp-community-events-block' ),
			'created_at' => __( 'Registered', 'wp-community-events-block' ),
		);
	}

	public function prepare_items() {
		$event_id = $this->get_event_filter();
		$offset   = ( $this->get_pagenum() - 1 ) * self::PER_PAGE;

		$this->_column_headers = array( $this->get_columns(), array(), array() );
		$this->items           = community_events_get_registrations( $event_id, self::PER_PAGE, $offset );

		$this->set_pagination_args(
			array(
				'total_items' => community_events_count_registrations( $event_id ),
				'per_page'    => self::PER_PAGE,
			)
		);
	}

	protected function column_default( $item, $column_name ) {
		return esc_html( $item->$column_name );
	}

	protected function column_email( $item ) {
		return sprintf( '<a href="%s">%s</a>', esc_url( 'mailto:' . $item->email ), esc_html( $item->email ) );
	}

	protected function column_event( $item ) {
		$title = get_the_title( $item->event_id );
		$link  = get_edit_post_link( $item->event_id );

		return $link ? sprintf( '<a href="%s">%s</a>', esc_url( $link ), esc_html( $title ) ) : esc_html( $title );
	}

	protected function column_created_at( $item ) {
		// Stored in UTC; show it in the site's time zone.
		return esc_html( get_date_from_gmt( $item->created_at, 'j M Y, g:i a' ) );
	}

	protected function extra_tablenav( $which ) {
		if ( 'top' !== $which ) {
			return;
		}

		$events = get_posts(
			array(
				'post_type'   => 'ce_event',
				'numberposts' => -1,
				'orderby'     => 'title',
				'order'       => 'ASC',
			)
		);
		?>
		<div class="alignleft actions">
			<label class="screen-reader-text" for="ce-event-filter"><?php esc_html_e( 'Filter by event', 'wp-community-events-block' ); ?></label>
			<select name="event_id" id="ce-event-filter">
				<option value="0"><?php esc_html_e( 'All events', 'wp-community-events-block' ); ?></option>
				<?php foreach ( $events as $event ) : ?>
					<option value="<?php echo esc_attr( $event->ID ); ?>" <?php selected( $this->get_event_filter(), $event->ID ); ?>><?php echo esc_html( get_the_title( $event ) ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php submit_button( __( 'Filter', 'wp-community-events-block' ), '', 'filter_action', false ); ?>
		</div>
		<?php
	}

	public function no_items() {
		esc_html_e( 'No registrations yet.', 'wp-community-events-block' );
	}
}
