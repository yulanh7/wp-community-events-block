import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, Placeholder, SelectControl } from '@wordpress/components';
import { ServerSideRender } from '@wordpress/server-side-render';
import metadata from './block.json';

/**
 * Editor view for the Event Details block.
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Updates attributes.
 * @param {Object}   props.context       Block context: postId and postType.
 * @return {Element} Element to render.
 */
export default function Edit( { attributes, setAttributes, context } ) {
	// In the template editor there is no real event yet, so there is nothing to preview.
	const hasEvent = context.postId && context.postType === 'ce_event';

	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody
					title={ __( 'Settings', 'wp-community-events-block' ) }
				>
					<SelectControl
						label={ __( 'Show', 'wp-community-events-block' ) }
						value={ attributes.variant }
						options={ [
							{
								label: __(
									'Summary: date, time, location, places',
									'wp-community-events-block'
								),
								value: 'summary',
							},
							{
								label: __(
									'Info: accessibility and organiser',
									'wp-community-events-block'
								),
								value: 'info',
							},
						] }
						onChange={ ( variant ) => setAttributes( { variant } ) }
					/>
				</PanelBody>
			</InspectorControls>
			{ hasEvent ? (
				<ServerSideRender
					block={ metadata.name }
					attributes={ attributes }
					urlQueryArgs={ { post_id: context.postId } }
				/>
			) : (
				<Placeholder
					icon="info-outline"
					label={ metadata.title }
					instructions={ __(
						'Shows details of the current event. Add it to the single event template.',
						'wp-community-events-block'
					) }
				/>
			) }
		</div>
	);
}
