<?php
/**
 * Events → Registrations screen and its CSV export.
 *
 * @package CommunityEvents
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Registrations hold personal details, so only editors and administrators see them.
const COMMUNITY_EVENTS_MANAGE_CAP = 'edit_others_posts';

function community_events_add_registrations_page() {
	add_submenu_page(
		'edit.php?post_type=ce_event',
		__( 'Registrations', 'wp-community-events-block' ),
		__( 'Registrations', 'wp-community-events-block' ),
		COMMUNITY_EVENTS_MANAGE_CAP,
		'ce-registrations',
		'community_events_render_registrations_page'
	);
}
add_action( 'admin_menu', 'community_events_add_registrations_page' );

function community_events_render_registrations_page() {
	require_once __DIR__ . '/class-registrations-table.php';

	$table = new Community_Events_Registrations_Table();
	$table->prepare_items();

	$export_url = wp_nonce_url(
		add_query_arg(
			array(
				'action'   => 'community_events_export',
				'event_id' => $table->get_event_filter(),
			),
			admin_url( 'admin-post.php' )
		),
		'community_events_export'
	);
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Registrations', 'wp-community-events-block' ); ?></h1>
		<a href="<?php echo esc_url( $export_url ); ?>" class="page-title-action"><?php esc_html_e( 'Export CSV', 'wp-community-events-block' ); ?></a>
		<hr class="wp-header-end">

		<form method="get">
			<input type="hidden" name="post_type" value="ce_event">
			<input type="hidden" name="page" value="ce-registrations">
			<?php $table->display(); ?>
		</form>
	</div>
	<?php
}

/**
 * Sends the registrations as a CSV download.
 */
function community_events_export_registrations() {
	if ( ! current_user_can( COMMUNITY_EVENTS_MANAGE_CAP ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to export registrations.', 'wp-community-events-block' ), 403 );
	}
	check_admin_referer( 'community_events_export' );

	$event_id = isset( $_GET['event_id'] ) ? absint( $_GET['event_id'] ) : 0;
	$rows     = community_events_get_registrations( $event_id, community_events_count_registrations( $event_id ) );

	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=registrations-' . gmdate( 'Y-m-d' ) . '.csv' );

	$output = fopen( 'php://output', 'w' );

	// A byte order mark tells Excel the file is UTF-8, so accents and emoji show correctly.
	fwrite( $output, "\xEF\xBB\xBF" );
	fputcsv( $output, array( 'Name', 'Email', 'Event', 'Places', 'Registered' ), ',', '"', '' );

	foreach ( $rows as $row ) {
		$fields = array(
			$row->name,
			$row->email,
			get_post_field( 'post_title', $row->event_id ),
			$row->places,
			get_date_from_gmt( $row->created_at, 'Y-m-d H:i' ),
		);
		fputcsv( $output, array_map( 'community_events_csv_cell', $fields ), ',', '"', '' );
	}

	fclose( $output );
	exit;
}
add_action( 'admin_post_community_events_export', 'community_events_export_registrations' );

/**
 * Stops spreadsheet apps from running a cell as a formula (CSV injection).
 *
 * @param mixed $value Cell value.
 * @return string
 */
function community_events_csv_cell( $value ) {
	$value = (string) $value;

	if ( in_array( substr( $value, 0, 1 ), array( '=', '+', '-', '@', "\t", "\r" ), true ) ) {
		return "'" . $value;
	}

	return $value;
}
