/**
 * Registers the Upcoming Event block.
 *
 * It is a dynamic block: render.php builds the HTML, so there is no save().
 */
import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit: Edit,
} );
