<?php
// This file is generated. Do not modify it manually.
return array(
	'event-details' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'community-events/event-details',
		'version' => '0.2.0',
		'title' => 'Event Details',
		'category' => 'widgets',
		'icon' => 'info-outline',
		'description' => 'Shows the current event\'s date, time, location and places, or its accessibility and organiser details.',
		'keywords' => array(
			'event',
			'date',
			'location',
			'organiser'
		),
		'attributes' => array(
			'variant' => array(
				'type' => 'string',
				'enum' => array(
					'summary',
					'info'
				),
				'default' => 'summary'
			)
		),
		'usesContext' => array(
			'postId',
			'postType'
		),
		'supports' => array(
			'html' => false
		),
		'textdomain' => 'wp-community-events-block',
		'editorScript' => 'file:./index.js',
		'style' => 'community-events',
		'render' => 'file:./render.php'
	),
	'event-list' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'community-events/event-list',
		'version' => '0.2.0',
		'title' => 'Event List',
		'category' => 'widgets',
		'icon' => 'grid-view',
		'description' => 'Shows upcoming events as cards, with optional category filters.',
		'keywords' => array(
			'events',
			'cards',
			'grid'
		),
		'attributes' => array(
			'heading' => array(
				'type' => 'string',
				'default' => ''
			),
			'numberOfEvents' => array(
				'type' => 'integer',
				'default' => 6
			),
			'columns' => array(
				'type' => 'integer',
				'default' => 3
			),
			'category' => array(
				'type' => 'string',
				'default' => ''
			),
			'showFilters' => array(
				'type' => 'boolean',
				'default' => false
			),
			'groupByMonth' => array(
				'type' => 'boolean',
				'default' => false
			),
			'showPast' => array(
				'type' => 'boolean',
				'default' => false
			),
			'related' => array(
				'type' => 'boolean',
				'default' => false
			)
		),
		'usesContext' => array(
			'postId',
			'postType'
		),
		'supports' => array(
			'html' => false,
			'align' => array(
				'wide',
				'full'
			)
		),
		'textdomain' => 'wp-community-events-block',
		'editorScript' => 'file:./index.js',
		'style' => 'community-events',
		'render' => 'file:./render.php'
	),
	'registration-form' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'community-events/registration-form',
		'version' => '0.1.0',
		'title' => 'Event Registration',
		'category' => 'widgets',
		'icon' => 'groups',
		'description' => 'Lets visitors book places for the current event.',
		'keywords' => array(
			'event',
			'register',
			'booking',
			'form'
		),
		'usesContext' => array(
			'postId',
			'postType'
		),
		'supports' => array(
			'html' => false,
			'multiple' => false
		),
		'textdomain' => 'wp-community-events-block',
		'editorScript' => 'file:./index.js',
		'viewScript' => 'file:./view.js',
		'style' => 'community-events',
		'render' => 'file:./render.php'
	),
	'upcoming-event' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'community-events/upcoming-event',
		'version' => '0.2.0',
		'title' => 'Upcoming Event',
		'category' => 'widgets',
		'icon' => 'calendar-alt',
		'description' => 'Shows the next upcoming event with its date, time and location.',
		'keywords' => array(
			'event',
			'next',
			'countdown'
		),
		'attributes' => array(
			'showCountdown' => array(
				'type' => 'boolean',
				'default' => true
			)
		),
		'supports' => array(
			'html' => false,
			'align' => array(
				'wide'
			)
		),
		'textdomain' => 'wp-community-events-block',
		'editorScript' => 'file:./index.js',
		'style' => 'community-events',
		'viewScript' => 'file:./view.js',
		'render' => 'file:./render.php'
	)
);
