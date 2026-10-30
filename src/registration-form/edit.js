import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { Disabled, Placeholder } from '@wordpress/components';
import { ServerSideRender } from '@wordpress/server-side-render';
import metadata from './block.json';

/**
 * Editor view for the Event Registration block.
 *
 * @param {Object} props         Block props.
 * @param {Object} props.context Block context: postId and postType.
 * @return {Element} Element to render.
 */
export default function Edit( { context } ) {
	const hasEvent = context.postId && context.postType === 'ce_event';

	return (
		<div { ...useBlockProps() }>
			{ hasEvent ? (
				// A preview only: stop people typing into or submitting the form here.
				<Disabled>
					<ServerSideRender
						block={ metadata.name }
						urlQueryArgs={ { post_id: context.postId } }
					/>
				</Disabled>
			) : (
				<Placeholder
					icon="groups"
					label={ metadata.title }
					instructions={ __(
						'Shows a registration form for the current event. Add it to the single event template.',
						'wp-community-events-block'
					) }
				/>
			) }
		</div>
	);
}
