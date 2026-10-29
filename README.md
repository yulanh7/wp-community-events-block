# Community Events Hub

A WordPress plugin that helps churches, community groups and nonprofits publish events and take registrations. Volunteers manage everything from the WordPress admin, with no coding needed.

Built with Gutenberg blocks (React), PHP and the WordPress REST API.

## Features

### Phase 1: Event display

- [x] Upcoming Event block
- [ ] Event custom post type with date, time, location and capacity
- [ ] Event categories
- [ ] Event List block with category filtering

### Phase 2: Registration

- [ ] Registration form block (name, email, number of attendees)
- [ ] AJAX submission to a custom REST API endpoint, with nonce checks, validation and escaping
- [ ] Registrations stored in a custom database table, with a "full" notice when capacity is reached
- [ ] Admin registration list with search and CSV export
- [ ] Confirmation emails to attendees and site admins

### Phase 3: Real-world use

- [ ] English and Chinese translations (WordPress i18n)
- [ ] Add to calendar (`.ics` download, no third-party services)
- [ ] Monthly calendar view block
- [ ] Privacy tools for exporting and deleting registration data (Australian Privacy Act basics)

### Phase 4: Quality and release

- [ ] Accessibility review (keyboard navigation, contrast, form labels)
- [ ] GitHub Actions for linting and building a release zip
- [ ] Live demo on WordPress Playground

## Requirements

- WordPress 6.8 or later
- PHP 7.4 or later
- Node.js 20.19+ or 22.13+ (for building the blocks)
- A local WordPress environment, such as [Local](https://localwp.com/)

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

### Available scripts

| Command | Description |
| --- | --- |
| `npm run start` | Build in watch mode for development |
| `npm run build` | Build for production |
| `npm run lint:js` | Check JavaScript code style |
| `npm run lint:css` | Check SCSS code style |
| `npm run format` | Format code |
| `npm run plugin-zip` | Create an installable plugin zip |

## Project structure

```
wp-community-events-block/
├── wp-community-events-block.php   # Main plugin file: plugin header and block registration
├── src/                            # Block source code (edit these files)
│   └── wp-community-events-block/
│       ├── block.json              # Block metadata: name, title, icon, assets
│       ├── index.js                # Registers the block in the editor
│       ├── edit.js                 # Editor view (React)
│       ├── save.js                 # Saved front-end markup
│       ├── style.scss              # Styles for the editor and front end
│       └── editor.scss             # Styles for the editor only
├── build/                          # Compiled output (generated, not committed)
├── package.json                    # npm scripts and dependencies
└── readme.txt                      # WordPress.org plugin readme
```

## License

[GPL-2.0-or-later](https://www.gnu.org/licenses/gpl-2.0.html)
