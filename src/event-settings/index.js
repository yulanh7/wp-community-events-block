/**
 * "Event settings" panel in the editor sidebar for the Event post type.
 *
 * Reads and writes the meta registered in includes/post-meta.php. The values
 * are saved with the post, through the REST API, when the user clicks Save.
 */
import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';
import {
	PluginDocumentSettingPanel,
	store as editorStore,
} from '@wordpress/editor';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { Notice, TextControl } from '@wordpress/components';

function EventSettingsPanel() {
	const postType = useSelect(
		( select ) => select( editorStore ).getCurrentPostType(),
		[]
	);
	const [ meta, setMeta ] = useEntityProp( 'postType', 'ce_event', 'meta' );

	if ( postType !== 'ce_event' || ! meta ) {
		return null;
	}

	const update = ( key ) => ( value ) =>
		setMeta( { ...meta, [ key ]: value } );

	// Both values use the same "YYYY-MM-DDTHH:MM" format, so they compare as strings.
	const endsBeforeStart =
		meta.ce_event_start &&
		meta.ce_event_end &&
		meta.ce_event_end < meta.ce_event_start;

	return (
		<PluginDocumentSettingPanel
			name="community-events-settings"
			title={ __( 'Event settings', 'wp-community-events-block' ) }
		>
			<TextControl
				label={ __( 'Starts', 'wp-community-events-block' ) }
				type="datetime-local"
				value={ meta.ce_event_start }
				onChange={ update( 'ce_event_start' ) }
			/>
			<TextControl
				label={ __( 'Ends', 'wp-community-events-block' ) }
				type="datetime-local"
				value={ meta.ce_event_end }
				onChange={ update( 'ce_event_end' ) }
			/>
			{ endsBeforeStart && (
				<Notice status="warning" isDismissible={ false }>
					{ __(
						'The event ends before it starts.',
						'wp-community-events-block'
					) }
				</Notice>
			) }
			<TextControl
				label={ __( 'Location', 'wp-community-events-block' ) }
				value={ meta.ce_event_location }
				onChange={ update( 'ce_event_location' ) }
			/>
			<TextControl
				label={ __( 'Places', 'wp-community-events-block' ) }
				help={ __(
					'Leave at 0 for free entry with no registration.',
					'wp-community-events-block'
				) }
				type="number"
				min={ 0 }
				value={ meta.ce_event_capacity }
				onChange={ ( value ) =>
					update( 'ce_event_capacity' )( parseInt( value, 10 ) || 0 )
				}
			/>
		</PluginDocumentSettingPanel>
	);
}

registerPlugin( 'community-events-settings', {
	render: EventSettingsPanel,
} );
