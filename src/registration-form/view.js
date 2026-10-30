/**
 * Sends the registration form with fetch, so the page does not reload.
 *
 * render.php puts the endpoint URL and a nonce on the form as data attributes.
 */
import { __ } from '@wordpress/i18n';

async function sendRegistration( form ) {
	const data = Object.fromEntries( new FormData( form ) );

	try {
		const response = await fetch( form.dataset.endpoint, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': form.dataset.nonce,
			},
			body: JSON.stringify( { ...data, places: Number( data.places ) } ),
		} );
		const result = await response.json();

		return {
			ok: response.ok,
			text: result.message,
			placesHtml: result.placesHtml,
		};
	} catch {
		// No connection, or the server sent back something that is not JSON.
		return { ok: false, text: '' };
	}
}

/**
 * Shows the new "places left" in the Event Details box without a reload.
 *
 * @param {string} html Markup from community_events_render_places().
 */
function updatePlaces( html ) {
	if ( ! html ) {
		return;
	}
	document
		.querySelectorAll( '.ce-details .ce-places' )
		.forEach( ( places ) => ( places.outerHTML = html ) );
}

async function onSubmit( event ) {
	event.preventDefault();

	const form = event.currentTarget;
	const button = form.querySelector( 'button[type="submit"]' );
	const message = form
		.closest( '.ce-register' )
		.querySelector( '.ce-register__message' );

	button.disabled = true;
	message.textContent = '';
	message.classList.remove( 'is-success', 'is-error' );

	const { ok, text, placesHtml } = await sendRegistration( form );

	if ( ok ) {
		form.hidden = true;
		message.textContent = text;
		message.classList.add( 'is-success' );
		updatePlaces( placesHtml );
		return;
	}

	button.disabled = false;
	message.textContent =
		text ||
		__(
			'Something went wrong. Please check your connection and try again.',
			'wp-community-events-block'
		);
	message.classList.add( 'is-error' );
}

document
	.querySelectorAll( '.ce-register form' )
	.forEach( ( form ) => form.addEventListener( 'submit', onSubmit ) );
