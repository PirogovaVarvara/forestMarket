# Changelog

All notable changes to XtremeSlider are documented here.

## [1.4.5] - 2026-05-23

### Changed
- Verified compatibility with WordPress 7.0. No code changes required — bumped `Tested up to` header in `readme.txt` from 6.9 to 7.0 and version constants/headers to 1.4.5. Lint clean under PHP 8.3; no use of APIs deprecated or removed in WP 7.0.

## [1.4.4] - 2026-05-13

### Changed
- WordPress.org Plugin Check compliance pass. Added `translators:` comments for `__()` calls containing placeholders (`%d titles loaded` in `class-admin.php`, `Slide %d` fallback in the Options renderer). Wrapped `$_POST['image_ratio']` in `sanitize_text_field()` before passing to the custom `xs_sanitize_image_ratio()` validator so the static-analysis sniff recognises the sanitization step. Replaced the inline `phpcs:ignore` on the multi-line `wp_unslash( $_POST['slides']['html_content'] )` expression with a `phpcs:disable/enable` block; per-item `wp_kses_post()` still runs inside the loop. Gated `error_log()` in the save-rollback path behind `WP_DEBUG && WP_DEBUG_LOG` so it only fires in dev.
- Description text in `readme.txt` updated from "three layout modes" to "four layout modes" — the Options layout shipped in 1.4.1 but the description was never refreshed.

## [1.4.3] - 2026-05-09

### Fixed
- Cool layout fixed-height sliders lost their navigation arrows when more than one slider was on the page. In fixed-height mode each slide's width is derived from the natural width of its (lazy-loaded) image, but `getMaxPage` was measuring `slide.offsetWidth` at `DOMContentLoaded` — before images had loaded — so the total measured width was 0, max-page was 0, and `updateArrowVisibility()` hid both arrows. The first slider on the page typically used non-fixed ratio (computed slot widths) and so was unaffected, which made the bug look like a "second slider" issue. Layout + arrow visibility now re-run as each slide image fires `load`, plus once on `window.load` as a fallback.
- Extracted the resize/load recalculation into a single `recalcLayout()` method so `update*` and `updateArrowVisibility()` can't drift out of sync.

## [1.4.2] - 2026-05-09

### Fixed
- Cool layout with `Fixed Height` image ratio was cropping wider images because all slides were forced to a uniform slot width (`viewport / visible`). Slides now keep each image's natural width derived from its aspect ratio at the configured fixed height, so doors of different widths (e.g. 204×450 vs 294×450) display in their proper proportions instead of being clipped by `object-fit: cover`
- Cool layout navigation in fixed-height mode now advances by the per-slide width (cumulative offset) instead of a uniform page step, and `getMaxPage` accounts for variable slide widths so the last slides remain reachable

## [1.3.4] - 2026-04-11

### Added
- `Black arrows` display option to switch slider navigation buttons to solid black circles with white icons

### Fixed
- Existing installs now auto-add the new `black_arrows` slider column during upgrade so the option saves cleanly without manual database work

## [1.3.3] - 2026-04-11

### Changed
- Free mode is now limited to 2 total sliders, while premium keeps unlimited slider creation
- Slider list header now disables `Add New Slider` once the free-tier slider cap is reached and shows current usage in the admin

### Fixed
- New slider creation is now blocked at both the admin UI layer and the save handler, preventing cached forms or direct links from bypassing the free-tier slider cap

## [1.3.2] - 2026-04-11

### Added
- Premium license activation with site-bound validation, unlocking up to 50 slides per slider and up to 15 visible slides
- Premium-only `Default (Original ratio)` image ratio option to preserve each image's natural aspect ratio
- `Load Titles` editor action to fill slide titles from image filenames before saving
- `Square corners` display option to switch slide images from rounded to sharp corners

### Changed
- Save feedback on the editor now uses a compact inline status pill beside the save button
- Image ratio selectors in the editor and settings now adapt to the active license tier while preserving legacy premium values during edits

### Fixed
- Slider creation could fail when older installs were missing newer columns such as `title_shadow`; storage repair now self-heals schema drift before save
- Slider saves are now transactional, preventing orphaned or partially-saved slide rows when a database write fails
- Upgrade routines now add the new `square_corners` column on existing installs automatically

## [1.3.1] - 2026-04-05

### Fixed
- Gradient color fields now display the actual saved value after reload — removed `?: default` fallback from the input `value=` attribute that was masking the real DB value with the default color even when a different color (including white) was saved
- Arrows no longer shown when slide count equals visible count (nothing to scroll)

## [1.3.0] - 2026-04-05

### Added
- Fixed Height image ratio option — user sets a pixel height; width scales automatically to preserve natural image proportions
- DB migration: `fixed_height` column added to `wp_xs_sliders` with live ALTER TABLE support for existing installs

### Changed
- Sidebar sections compacted (reduced padding/gaps) so Save button is visible without scrolling
- Fixed Height input now appears inline next to the Image Ratio select instead of occupying a separate row
- Color pickers now initialized per-field with correct `defaultColor` so Clear button restores the field's own default (red for link hover, pink/purple for gradient) instead of resetting to white

### Fixed
- Colors saving as `#ffffff` when Clear was clicked — Iris wp-color-picker was defaulting to white with no `defaultColor` set
- Hex color sanitizer now correctly accepts 3-digit shorthand (`#fff`) and expands to 6-digit
- Color field defaults (`link_hover_color`, `gradient_start`, `gradient_end`) applied after sanitization instead of before, preventing wrong fallback on invalid input

## [1.2.0] - 2026-03-25

### Added
- Image ratio option: choose between 16:10 (landscape) and 1:1 (square) per slider
- Link hover color picker — configurable per slider
- Shortcode attribute overrides: `layout`, `visible`, `autoplay`, `fullscreen`
- Mouse drag support for Default layout (click-and-drag to navigate)
- Click-through prevention after drag gesture to avoid accidental link navigation

### Changed
- Improved responsive breakpoints — smoother transitions between 4 screen sizes
- Default layout arrows repositioned closer to peek slides for better UX
- Refined 3D layout depth: adjusted rotation angles and scale factors for adjacent slides

### Fixed
- Clone-based infinite loop occasionally flickering on fast consecutive clicks
- 3D layout slides not recalculating positions on window resize
- Autoplay timer not resetting after manual navigation

## [1.1.0] - 2026-02-14

### Added
- Cool layout — card-based slider with customizable two-color gradient background
- Pagination dots for Cool and 3D layouts
- Fullscreen mode (100vw edge-to-edge display)
- Gradient background picker with live preview in admin
- Touch/swipe support for all layouts on mobile devices
- Autoplay with configurable speed (2–10 seconds) and hover pause

### Changed
- Admin design overhaul — migrated to neumorphic design system
- Slide cards now use drag-to-reorder with jQuery UI Sortable
- Replaced text-based layout selector with visual radio cards

### Fixed
- Shortcode rendering empty div when slider had no slides
- Admin color picker popup overlapping sidebar on smaller screens
- Status toggle not saving correctly when switching from active to draft

## [1.0.0] - 2026-01-08

### Added
- Initial release
- Default layout — editorial full-bleed slider with peek effect and infinite seamless looping
- 3D layout — CSS perspective slider with depth transforms and glassmorphism arrows
- Up to 10 slides per slider with 1–6 visible at once
- Per-slide fields: title (vertical overlay), caption, description, link URL
- Relative and absolute link support (internal links same tab, external new tab)
- Image hover zoom effect
- Conditional CSS/JS loading — assets only enqueued on pages containing a slider
- Admin panel with slider list, edit screen, and global settings page
- Copy-to-clipboard shortcode button
- Save button with loading spinner feedback
- Clean uninstall — drops custom tables and removes options

## [0.9.0] - 2025-12-18

### Added
- Beta release for internal testing
- Default and 3D layouts
- Basic admin UI with slide upload and ordering
- Shortcode registration and frontend rendering

### Known Issues
- No touch/swipe support yet
- Infinite loop has occasional visual glitch on fast clicks
- Admin not fully responsive below 960px
