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
	$hook = add_submenu_page(
		'edit.php?post_type=ce_event',
		__( 'Registrations', 'wp-community-events-block' ),
		__( 'Registrations', 'wp-community-events-block' ),
		COMMUNITY_EVENTS_MANAGE_CAP,
		'ce-registrations',
		'community_events_render_registrations_page'
	);

	// Runs before any output, so the handler can redirect after a change.
	add_action( 'load-' . $hook, 'community_events_handle_registration_actions' );
}
add_action( 'admin_menu', 'community_events_add_registrations_page' );

/**
 * URL of the Registrations screen.
 *
 * @param array $args Extra query arguments.
 * @return string
 */
function community_events_registrations_url( $args = array() ) {
	return add_query_arg(
		array_filter( $args ),
		admin_url( 'edit.php?post_type=ce_event&page=ce-registrations' )
	);
}

/**
 * Deletes or updates registrations, then redirects back to the list.
 *
 * Changes are redirected (Post/Redirect/Get), so reloading the page does not
 * repeat them.
 */
function community_events_handle_registration_actions() {
	require_once __DIR__ . '/class-registrations-table.php';

	$table  = new Community_Events_Registrations_Table();
	$action = $table->current_action();

	if ( 'delete' === $action ) {
		check_admin_referer( 'bulk-registrations' );

		$ids     = isset( $_REQUEST['registration'] ) ? wp_unslash( (array) $_REQUEST['registration'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Cast to integers below.
		$deleted = community_events_delete_registrations( array_map( 'absint', $ids ) );

		wp_safe_redirect(
			community_events_registrations_url(
				array(
					'event_id' => $table->get_event_filter(),
					'deleted'  => $deleted,
				)
			)
		);
		exit;
	}

	if ( 'update' === $action ) {
		$id = isset( $_POST['registration'] ) ? absint( $_POST['registration'] ) : 0;
		check_admin_referer( 'community_events_update_registration_' . $id );

		$registration = community_events_get_registration( $id );
		if ( ! $registration ) {
			wp_die( esc_html__( 'Registration not found.', 'wp-community-events-block' ), 404 );
		}

		$name   = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$email  = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$places = isset( $_POST['places'] ) ? absint( $_POST['places'] ) : 0;

		// The same rules as the public form, checked again on the server.
		$error = '';
		if ( '' === $name ) {
			$error = 'invalid_name';
		} elseif ( ! is_email( $email ) ) {
			$error = 'invalid_email';
		} elseif ( $places < 1 ) {
			$error = 'invalid_places';
		} elseif ( $places > community_events_max_places_for( $registration ) ) {
			$error = 'not_enough_places';
		} elseif ( community_events_is_registered( $registration->event_id, $email, $id ) ) {
			$error = 'already_registered';
		} elseif ( ! community_events_update_registration( $id, $name, $email, $places ) ) {
			$error = 'save_failed';
		}

		if ( $error ) {
			wp_safe_redirect(
				community_events_registrations_url(
					array(
						'action'       => 'edit',
						'registration' => $id,
						'error'        => $error,
					)
				)
			);
			exit;
		}

		wp_safe_redirect( community_events_registrations_url( array( 'updated' => 1 ) ) );
		exit;
	}
}

/**
 * Shows the result of the last change as an admin notice.
 */
function community_events_registration_notices() {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only messages after a redirect.
	if ( isset( $_GET['deleted'] ) ) {
		$count = absint( $_GET['deleted'] );
		wp_admin_notice(
			/* translators: %d: number of registrations deleted. */
			esc_html( sprintf( _n( '%d registration deleted.', '%d registrations deleted.', $count, 'wp-community-events-block' ), $count ) ),
			array(
				'type'        => 'success',
				'dismissible' => true,
			)
		);
	}

	if ( isset( $_GET['updated'] ) ) {
		wp_admin_notice(
			esc_html__( 'Registration updated.', 'wp-community-events-block' ),
			array(
				'type'        => 'success',
				'dismissible' => true,
			)
		);
	}

	$errors = array(
		'invalid_name'       => __( 'Please enter a name.', 'wp-community-events-block' ),
		'invalid_email'      => __( 'Please enter a valid email address.', 'wp-community-events-block' ),
		'invalid_places'     => __( 'Please book at least one place.', 'wp-community-events-block' ),
		'not_enough_places'  => __( 'There are not enough places left for that number.', 'wp-community-events-block' ),
		'already_registered' => __( 'This email address is already registered for this event.', 'wp-community-events-block' ),
		'save_failed'        => __( 'The registration could not be saved. Please try again.', 'wp-community-events-block' ),
	);
	$error  = isset( $_GET['error'] ) ? sanitize_key( $_GET['error'] ) : '';
	// phpcs:enable

	if ( isset( $errors[ $error ] ) ) {
		wp_admin_notice( esc_html( $errors[ $error ] ), array( 'type' => 'error' ) );
	}
}

function community_events_render_registrations_page() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only picks which screen to show.
	if ( isset( $_GET['action'], $_GET['registration'] ) && 'edit' === $_GET['action'] ) {
		community_events_render_edit_registration( absint( $_GET['registration'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

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

		<?php community_events_registration_notices(); ?>

		<form method="get">
			<input type="hidden" name="post_type" value="ce_event">
			<input type="hidden" name="page" value="ce-registrations">
			<?php $table->display(); ?>
		</form>
	</div>
	<?php
}

/**
 * The form for editing one registration.
 *
 * @param int $registration_id Registration ID.
 */
function community_events_render_edit_registration( $registration_id ) {
	$registration = community_events_get_registration( $registration_id );
	if ( ! $registration ) {
		wp_die( esc_html__( 'Registration not found.', 'wp-community-events-block' ), 404 );
	}

	$max_places = community_events_max_places_for( $registration );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Edit registration', 'wp-community-events-block' ); ?></h1>

		<?php community_events_registration_notices(); ?>

		<p>
			<?php
			printf(
				/* translators: 1: event title, 2: date and time of registration. */
				esc_html__( 'For %1$s, registered %2$s.', 'wp-community-events-block' ),
				'<strong>' . esc_html( get_the_title( $registration->event_id ) ) . '</strong>',
				esc_html( get_date_from_gmt( $registration->created_at, 'j M Y, g:i a' ) )
			);
			?>
		</p>

		<form method="post" action="<?php echo esc_url( community_events_registrations_url() ); ?>">
			<input type="hidden" name="action" value="update">
			<input type="hidden" name="registration" value="<?php echo esc_attr( $registration->id ); ?>">
			<?php wp_nonce_field( 'community_events_update_registration_' . $registration->id ); ?>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="ce-name"><?php esc_html_e( 'Name', 'wp-community-events-block' ); ?></label></th>
					<td><input id="ce-name" name="name" type="text" class="regular-text" maxlength="100" required value="<?php echo esc_attr( $registration->name ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="ce-email"><?php esc_html_e( 'Email', 'wp-community-events-block' ); ?></label></th>
					<td><input id="ce-email" name="email" type="email" class="regular-text" maxlength="100" required value="<?php echo esc_attr( $registration->email ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="ce-places"><?php esc_html_e( 'Places', 'wp-community-events-block' ); ?></label></th>
					<td>
						<input id="ce-places" name="places" type="number" class="small-text" min="1" max="<?php echo esc_attr( $max_places ); ?>" required value="<?php echo esc_attr( $registration->places ); ?>" aria-describedby="ce-places-help">
						<p class="description" id="ce-places-help">
							<?php
							/* translators: %d: most places this registration can have. */
							echo esc_html( sprintf( _n( 'Up to %d place for this event.', 'Up to %d places for this event.', $max_places, 'wp-community-events-block' ), $max_places ) );
							?>
						</p>
					</td>
				</tr>
			</table>

			<p class="submit">
				<?php submit_button( __( 'Update registration', 'wp-community-events-block' ), 'primary', 'submit', false ); ?>
				<a class="button" href="<?php echo esc_url( community_events_registrations_url() ); ?>"><?php esc_html_e( 'Cancel', 'wp-community-events-block' ); ?></a>
			</p>
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
