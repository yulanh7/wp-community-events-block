/**
 * Adds the editor sidebar script to the default @wordpress/scripts build.
 *
 * Blocks are found through their block.json files. The sidebar is not a block,
 * so it needs its own entry point.
 */
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

module.exports = {
	...defaultConfig,
	entry: {
		...defaultConfig.entry(),
		'event-settings/index': './src/event-settings/index.js',
	},
};
