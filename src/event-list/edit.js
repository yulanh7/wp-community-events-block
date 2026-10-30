import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	SelectControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { ServerSideRender } from '@wordpress/server-side-render';
import metadata from './block.json';

/**
 * Editor view for the Event List block.
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Updates attributes.
 * @return {Element} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	// Fetches /wp-json/wp/v2/ce_event_category. It is null until the response arrives,
	// then the component renders again with the terms.
	const categories = useSelect(
		( select ) =>
			select( coreStore ).getEntityRecords(
				'taxonomy',
				'ce_event_category',
				{ per_page: -1, orderby: 'name' }
			),
		[]
	);

	const categoryOptions = [
		{
			label: __( 'All categories', 'wp-community-events-block' ),
			value: '',
		},
		...( categories || [] ).map( ( term ) => ( {
			label: term.name,
			value: term.slug,
		} ) ),
	];

	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody
					title={ __( 'Layout', 'wp-community-events-block' ) }
				>
					<TextControl
						label={ __( 'Heading', 'wp-community-events-block' ) }
						value={ attributes.heading }
						onChange={ ( heading ) => setAttributes( { heading } ) }
					/>
					<RangeControl
						label={ __(
							'Number of events',
							'wp-community-events-block'
						) }
						min={ 1 }
						max={ 24 }
						value={ attributes.numberOfEvents }
						onChange={ ( numberOfEvents ) =>
							setAttributes( { numberOfEvents } )
						}
					/>
					<RangeControl
						label={ __( 'Columns', 'wp-community-events-block' ) }
						min={ 1 }
						max={ 4 }
						value={ attributes.columns }
						onChange={ ( columns ) => setAttributes( { columns } ) }
					/>
					<ToggleControl
						label={ __(
							'Group by month',
							'wp-community-events-block'
						) }
						checked={ attributes.groupByMonth }
						onChange={ ( groupByMonth ) =>
							setAttributes( { groupByMonth } )
						}
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'Filter', 'wp-community-events-block' ) }
				>
					<SelectControl
						label={ __( 'Category', 'wp-community-events-block' ) }
						value={ attributes.category }
						options={ categoryOptions }
						onChange={ ( category ) =>
							setAttributes( { category } )
						}
					/>
					<ToggleControl
						label={ __(
							'Show category filters',
							'wp-community-events-block'
						) }
						checked={ attributes.showFilters }
						onChange={ ( showFilters ) =>
							setAttributes( { showFilters } )
						}
					/>
					<ToggleControl
						label={ __(
							'Show past events',
							'wp-community-events-block'
						) }
						checked={ attributes.showPast }
						onChange={ ( showPast ) =>
							setAttributes( { showPast } )
						}
					/>
					<ToggleControl
						label={ __(
							'Related to current event',
							'wp-community-events-block'
						) }
						help={ __(
							'For the single event template. Uses the related events chosen in ACF, or events in the same category.',
							'wp-community-events-block'
						) }
						checked={ attributes.related }
						onChange={ ( related ) => setAttributes( { related } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<ServerSideRender
				block={ metadata.name }
				attributes={ attributes }
			/>
		</div>
	);
}
