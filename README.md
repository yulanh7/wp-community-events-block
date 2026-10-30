# Community Events Hub

A WordPress plugin that helps churches, community groups and nonprofits publish events and take registrations. Volunteers manage everything from the WordPress admin, with no coding needed.

Built with Gutenberg blocks (React), PHP and the WordPress REST API.

## Features

### Phase 1: Event display

- [x] Event custom post type with date, time, location and capacity
- [x] Event categories
- [x] Optional ACF integration: organizer, contact, accessibility notes and related events
- [x] Upcoming Event block: the next event, with a live countdown
- [x] Event List block: card grid with category filters, month grouping, past events and related events
- [x] Event Details block: date, time, location and places, or accessibility and organiser details
- [x] Block templates for single events, the events archive and event categories
- [x] Home page block pattern

### Phase 2: Registration

- [ ] Registration form block (name, email, number of attendees)
- [ ] AJAX submission to a custom REST API endpoint, with nonce checks, validation and escaping
- [ ] Registrations stored in a custom database table, with a "full" notice when capacity is reached
- [ ] Admin registration list with search and CSV export
- [ ] Confirmation emails to attendees and site admins

### Phase 3: Real-world use

- [ ] Translation-ready (WordPress i18n)
- [ ] Add to calendar (`.ics` download, no third-party services)
- [ ] Monthly calendar view block
- [ ] Privacy tools for exporting and deleting registration data (Australian Privacy Act basics)

### Phase 4: Quality and release

- [ ] Accessibility review (keyboard navigation, contrast, form labels)
- [ ] GitHub Actions for linting and building a release zip
- [ ] Live demo on WordPress Playground

## Blocks, templates and patterns

All three blocks are dynamic: `render.php` builds the HTML on every page load, so dates and places are always current. The editor shows a live preview through `ServerSideRender`.

| Block | What it shows | Main settings |
| --- | --- | --- |
| Upcoming Event | The next event as a featured card | Show countdown |
| Event List | Upcoming events as cards | Number, columns, category, filters, group by month, past events, related to current event |
| Event Details | Facts about the current event | Summary (date, time, location, places) or info (accessibility, organiser) |

The plugin also registers block templates, so an event site works as soon as the plugin is active:

| Template | Used for |
| --- | --- |
| `single-ce_event` | A single event: image, details, and related events |
| `archive-ce_event` | `/events/`: all upcoming events with category filters, grouped by month |
| `taxonomy-ce_event_category` | `/event-category/<slug>/`: events in one category |

A **Community home page** pattern (in the inserter under **Patterns → Community Events**) adds an intro, the next event, upcoming events, category links and a volunteer call to action.

## Requirements

- WordPress 7.0 or later
- PHP 7.4 or later
- Node.js 20.19+ or 22.13+ (for building the blocks)
- A local WordPress environment, such as [Local](https://localwp.com/)
- [Advanced Custom Fields](https://wordpress.org/plugins/advanced-custom-fields/) (free, optional) for extra event fields

## Data model

Core event data uses native WordPress APIs, so the plugin works without any other plugins:

| Data | Storage | Registered with |
| --- | --- | --- |
| Events | `ce_event` post type | `register_post_type()` |
| Categories | `ce_event_category` taxonomy | `register_taxonomy()` |
| Start, end, location, capacity | Post meta, exposed in the REST API | `register_post_meta()` |
| Organizer, email, accessibility notes, related events | ACF fields, only when ACF is active | `acf_add_local_field_group()` |

Core fields use native meta because registrations will read and write capacity through the REST API with strict types. Display-only fields use ACF for a faster, friendlier admin UI. The ACF field group is defined in PHP, so it is version-controlled and ships with the plugin.

## Local development

1. Clone the repository into your site's plugins folder:

   ```bash
   cd wp-content/plugins
   git clone https://github.com/yulanh7/wp-community-events-block.git
   cd wp-community-events-block
   ```

2. Install dependencies:

   ```bash
   npm install
   ```

3. Build the blocks. The `build/` folder is not committed, so this step is required:

   ```bash
   npm run build
   ```

   While developing, use `npm run start` instead. It rebuilds automatically when files in `src/` change.

4. Activate **Community Events Hub** on the **Plugins** screen in WordPress.

### Demo content

To fill a local site with sample events and categories, run this from the WordPress root (in Local, use **Open site shell**):

```bash
wp eval-file wp-content/plugins/wp-community-events-block/bin/seed-demo-content.php
```

It creates 11 categories and 12 events (10 upcoming, 2 past), including ACF fields when ACF is active. Running it again updates the demo content instead of duplicating it.

Featured images are attached from `demo-images/<event-slug>.jpg` (1600px, free-licence stock photos). They are only used for demo content and are not needed by the plugin itself.

### Available scripts

| Command | Description |
| --- | --- |
| `npm run start` | Build in watch mode for development |
| `npm run build` | Build for production |
| `npm run lint:js` | Check JavaScript code style |
| `npm run lint:css` | Check CSS code style |
| `npm run format` | Format code |
| `npm run plugin-zip` | Create an installable plugin zip |

## Project structure

```
wp-community-events-block/
├── wp-community-events-block.php   # Main plugin file: plugin header and block registration
├── includes/                       # PHP modules loaded by the main plugin file
│   ├── post-types.php              # Event post type and category taxonomy
│   ├── post-meta.php               # Event meta fields (date, location, capacity)
│   ├── acf-fields.php              # Optional ACF field group (runs only when ACF is active)
│   ├── template-tags.php           # Shared helpers: event queries, cards, dates, places
│   ├── assets.php                  # Registers the shared stylesheet
│   ├── templates.php               # Registers the block templates
│   └── patterns.php                # Registers the home page pattern
├── src/                            # Block source code (edit these files)
│   ├── upcoming-event/             # Each block: block.json, index.js, edit.js (React), render.php
│   ├── event-list/
│   └── event-details/
├── assets/css/community-events.css # Shared front-end styles for all blocks
├── templates/                      # Block templates (single event, archive, category)
├── patterns/home.php               # Home page pattern
├── bin/seed-demo-content.php       # WP-CLI script that creates demo events
├── demo-images/                    # Featured images for the demo events
├── build/                          # Compiled output (generated, not committed)
├── package.json                    # npm scripts and dependencies
└── readme.txt                      # WordPress.org plugin readme
```

## License

[GPL-2.0-or-later](https://www.gnu.org/licenses/gpl-2.0.html)
