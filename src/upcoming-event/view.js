/**
 * Front-end countdown for the Upcoming Event block.
 *
 * render.php prints the start time with its timezone offset in data-start,
 * so the countdown is correct wherever the visitor is.
 */
const UNITS = {
	days: 86400000,
	hours: 3600000,
	minutes: 60000,
	seconds: 1000,
};

function startCountdown( element ) {
	const start = new Date( element.dataset.start ).getTime();

	const tick = () => {
		let remaining = start - Date.now();

		if ( remaining <= 0 ) {
			element.textContent = element.dataset.started;
			return false;
		}

		Object.entries( UNITS ).forEach( ( [ unit, size ] ) => {
			const value = Math.floor( remaining / size );
			remaining -= value * size;
			element.querySelector( `[data-unit="${ unit }"]` ).textContent =
				unit === 'days' ? value : String( value ).padStart( 2, '0' );
		} );

		return true;
	};

	if ( tick() ) {
		const timer = setInterval( () => {
			if ( ! tick() ) {
				clearInterval( timer );
			}
		}, 1000 );
	}
}

document
	.querySelectorAll( '.ce-countdown[data-start]' )
	.forEach( startCountdown );
