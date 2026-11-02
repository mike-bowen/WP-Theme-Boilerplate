# Frost theme

Block theme (full-site editing) based on [Frost](https://frostwp.com/) 1.0.9 by WP Engine, with site-specific PHP in `inc/site-settings/`. Text domain is `frost`. Theme prefix for original setup is `frost_`. Site-specific functions use `sai_`.

Generate new work by copying an existing file of the same kind. Do not invent a parallel system (custom CSS frameworks, React, ACF, a pattern registry in PHP). Patterns are PHP files of block markup. Templates and parts are HTML files of block markup. Design tokens live in `theme.json`.

## Folder map

```
style.css              Theme header + small CSS that theme.json cannot express
theme.json             Design tokens, element styles, block styles, template parts
functions.php          Setup, stylesheet enqueue, requires inc/ files
readme.txt             WordPress.org readme (do not treat as the style guide)

inc/site-settings/
  blocks.php           Block styles, custom pattern categories, block allowlist
  comments.php         Comments and trackbacks disabled site-wide

patterns/*.php         Every inserter pattern. One pattern per file.
templates/*.html       Page templates. They pull in parts and patterns by slug.
parts/*.html           header and footer. Each is a single wp:pattern reference.
styles/*.json          Color-only style variations. Slugs stay the same.
assets/fonts/          Outfit variable font, registered from theme.json
assets/images/         Sample images referenced by patterns
```

Templates:

| File | Role |
| --- | --- |
| `templates/index.html` | Blog index. `frost/posts` inside `main.site-content`. |
| `templates/home.html` | Front page. `frost/page-home`, no extra content padding. |
| `templates/page.html` | Page. Post title in `.entry-header`, then post content. |
| `templates/single.html` | Single post. Title, meta, content, terms, `frost/comments`. |
| `templates/archive.html` | Archive. Query title, then `frost/posts`. |
| `templates/search.html` | Search. Query title, then `frost/posts`. |
| `templates/404.html` | 404. `frost/404` inside a constrained group. |
| `templates/blank.html` | Custom template "Blank". Post content only, no header or footer. |
| `templates/no-title.html` | Custom template "No Title". Header, content, footer, no post title. |

`parts/header.html` is `frost/header-default`. `parts/footer.html` is `frost/footer-default`. Swap the slug to use another header or footer pattern.

## Design tokens

Use preset slugs. Do not hardcode hex colors in new patterns. A raw pixel value is acceptable only where existing patterns already do it (header padding `30px`, button padding, a `14px` notification button).

Colors (`theme.json` palette). Style variations in `styles/` replace the hex values and keep these slugs:

| Slug | Default | Use |
| --- | --- | --- |
| `base` | `#ffffff` | Page background, text on contrast |
| `contrast` | `#000000` | Text, borders, dark fills |
| `primary` | `#0000ff` | Buttons, highlights |
| `secondary` | `#000099` | Secondary accent |
| `neutral` | `#eeeeee` | Code background, input focus |

Spacing presets: `x-small` 20px, `small` clamp(30px, 4vw, 40px), `medium` clamp(40px, 6vw, 60px), `large` clamp(50px, 8vw, 80px), `x-large` clamp(60px, 10vw, 100px). Custom gap is `30px` (`--wp--custom--spacing--gap`).

Layout: content `640px`, wide `1200px`. Root padding is the 30px gap. `useRootPaddingAwareAlignments` is on, so `alignfull` sections still inset their inner wide content.

Type: Outfit (`primary`), fluid. Body weight is light (300), headings regular (400), prices and strong labels medium (500). Line height body `1.75`, heading `1.1`, medium `1.5`.

Font-size slugs: `x-small` 16, `small` 18, `medium` 20, `large` 24, `x-large` 30, `max-36`, `max-48`, `max-60`, `max-72`. Section headings use `x-large` or `max-36`. Page heroes use `max-48` or `max-72`. Card titles are heading level 3 and inherit `large`.

Shadows: `light` (soft), `solid` (5px offset). Registered as block styles `is-style-shadow-light` and `is-style-shadow-solid` on Group and Quote.

In block JSON write `var:preset|spacing|small`, `var:preset|color|base`, `var:preset|font-size|x-large`. In the HTML `style` attribute write the expanded custom property: `var(--wp--preset--spacing--small)`. Both must be present and must match.

## PHP

`functions.php` loads behavior. New site behavior goes in `inc/site-settings/` and is pulled in with `require_once get_template_directory() . '/inc/site-settings/{file}.php';`.

- Tabs, not spaces.
- `frost_` for theme setup that ships with Frost. `sai_` for this site's restrictions.
- Text domain `'frost'` on every `__()` and `esc_html__()`.
- Docblock with a one-line summary. Newer `sai_` functions also include `@since`, `@param`, and `@return`.
- `add_action` / `add_filter` sits directly under the function.
- Compare with Yoda conditions: `'edit-comments.php' === $pagenow`.

`inc/site-settings/blocks.php` does three things:

1. `frost_register_block_styles()` registers `columns-reverse` (Columns), `shadow-light` and `shadow-solid` (Group, Quote), `no-disc` (List), `outline` (Social Links). CSS for those classes is in `style.css` or `theme.json` `styles.blocks`.
2. `frost_register_block_pattern_categories()` registers `frost-page` and `frost-pricing`. Core categories (`featured`, `header`, `footer`, `call-to-action`, `testimonials`, `posts`) are not registered here.
3. `sai_allowed_block_types()` returns a whitelist. A pattern that uses a commented-out block cannot be inserted. Stick to uncommented blocks when generating new patterns. Query, post title, excerpt, featured image, and read-more are allowed. Comments, post date, post author, avatar, and post terms are currently commented out, even though a few original Frost files still reference them.

`inc/site-settings/comments.php` closes comments and trackbacks, hides existing comments, and removes the admin UI. Do not add comment patterns for new templates.

## Pattern file

One file, `patterns/{slug-without-prefix}.php`. WordPress auto-registers every PHP file in `patterns/`. Do not register patterns in PHP.

```php
<?php
/**
 * Title: Boxes with heading, text, button.
 * Slug: frost/boxes-three
 * Categories: featured
 */
?>
<!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide">
<!-- inner blocks -->
</div>
<!-- /wp:group -->
```

Header rules:

- `Title` is a sentence fragment ending in a period. It names the visible pieces, not the marketing name: `Boxes with heading, text, button.` `Call-to-action with text, button.` `Footer with text, links.`
- `Slug` is `frost/` plus the filename without `.php`. The one exception already in the theme is `patterns/pricing-two-columns.php` → `frost/pricing-two-columns-main`. New files should match filename and slug.
- `Categories` is one category. Pick from the list below.
- `Block Types` only when the pattern is a header, footer, or query. Headers: `Block Types: core/template-part/header`. Footers: `Block Types: core/template-part/footer`. Queries: `Block Types: core/query`.
- `Inserter: no` hides the pattern from the inserter. Use it for patterns that only a template should reference (`404`, `comments`). Those files omit `Categories`.

| Category | What goes here | Extra header |
| --- | --- | --- |
| `featured` | Heroes, feature boxes, portfolio, team, progress | none |
| `call-to-action` | CTA bands, product box, newsletter, author box | none |
| `testimonials` | Quote cards | none |
| `frost-pricing` | Pricing tables | none |
| `frost-page` | A full page made of stacked sections | none |
| `header` | Site header or notification bar | `Block Types: core/template-part/header` |
| `footer` | Site footer | `Block Types: core/template-part/footer` |
| `posts` | Query loops | `Block Types: core/query` |

A new category is a `register_block_pattern_category()` call in `frost_register_block_pattern_categories()`, slug `frost-{name}`, with `label` and `description`.

### Markup rules

Patterns are block grammar, not free HTML. Every block is a `<!-- wp:name {json} -->` comment, the matching HTML, and a closing `<!-- /wp:name -->`. Self-closing blocks (`site-title`, `navigation`, `post-title`, `separator` when empty of children) use `<!-- wp:name {...} /-->` and, when they render a tag, the tag with no closer comment.

The JSON and the HTML must describe the same block. If the JSON sets `backgroundColor`, `fontSize`, `className`, `align`, or `style`, the HTML carries the generated classes and the expanded inline style. Mismatched markup becomes an invalid block in the editor.

- User-facing strings: `<?php echo esc_html__( 'Text', 'frost' ); ?>`.
- Image alt text: `<?php echo esc_attr__( 'Sample Image', 'frost' ); ?>`.
- Image src: `<?php echo esc_url( get_theme_file_uri() ) . '/assets/images/sample_1200x1200.jpg'; ?>`.
- Year: `<?php echo esc_html( gmdate( 'Y' ) ); ?>`.
- Placeholder links use `href="#"`.
- Sample images: `sample_1920x1200.jpg`, `sample_1200x1200.jpg`, `sample_800x800.jpg`, `sample_black_1200x1200.jpg`, `sample_white_1200x1200.jpg`, `sample_white_800x800.jpg`, `sample-logo.png`.
- Keep the `id` and `class="wp-image-{id}"` that the image block expects. Reuse an existing id from a similar pattern. Do not invent attachment IDs as if they were real media-library records that must resolve; the `src` is what renders.
- Headings that are anchors include `anchor` in JSON and `id` on the tag, plus `className":"wp-block-heading"`.
- Buttons: wrapper `<!-- wp:buttons -->`, then `<!-- wp:button -->` whose HTML is `<div class="wp-block-button"><a class="wp-block-button__link wp-element-button">`. Outline buttons add `className":"is-style-outline"` and class `is-style-outline`. Fill is the default. Primary action first, outline second (`Get Started` / `Learn More`).
- Card buttons usually override padding to vertical `x-small` and horizontal `medium` (sometimes `large`).
- Lists of links use middots in a paragraph (`<a href="#">Facebook</a> · <a href="#">Twitter</a>`) or a real `core/list` in mega footers.
- Quote marks in testimonials are a centered paragraph, `fontSize` `max-72`, `lineHeight` `1`, containing `“`. The attribution is a centered `small` paragraph wrapped in `<strong>`, starting with an em dash.

Section patterns are mostly compact: the first inner block comment sits on the same line as the parent `<div>`. Query patterns and templates use indented multiline markup. Match the file you are copying.

### Shells

In-content section (boxes, testimonials, heroes, portfolio, team). Wide, not full-bleed, so it sits in the post content width:

```html
<!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide">...</div>
<!-- /wp:group -->
```

Narrow centered copy (hero one column, two-column pricing). `layout.type` `constrained` and a `wideSize` of `800px` or `600px`. Text column beside an image uses `constrained` with `wideSize` `480px` and `justifyContent` `left`.

Full-bleed band (page-home sections, headers, footers). `align` `full`, `layout.type` `constrained`, horizontal padding `30px`, `margin.top` `0px` so it does not stack on the theme gap:

```html
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|x-large","bottom":"var:preset|spacing|x-large","left":"30px","right":"30px"},"margin":{"top":"0px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="margin-top:0px;padding-top:var(--wp--preset--spacing--x-large);padding-right:30px;padding-bottom:var(--wp--preset--spacing--x-large);padding-left:30px">
```

A dark full-bleed band adds `"backgroundColor":"contrast","textColor":"base"` and classes `has-base-color has-contrast-background-color has-text-color has-background`.

Columns hold repeated cards. Equal columns omit widths. Asymmetric columns set `width` (`"55%"`, `"35%"`, `"15%"`) and `flex-basis` on the column div. Image-and-text rows set `verticalAlignment` `center` and `className` `is-style-columns-reverse` when the image is the second column, so the image moves above the text under 782px.

### Light and dark pairs

Most section patterns exist twice. The dark file is the light file plus `-dark` on the filename and the slug. Title and category stay the same.

Light card: 1px contrast border, preset padding, no fill.

```html
<!-- wp:column {"style":{"spacing":{"padding":{"top":"var:preset|spacing|small","right":"var:preset|spacing|small","bottom":"var:preset|spacing|small","left":"var:preset|spacing|small"}},"border":{"width":"1px"}},"borderColor":"contrast"} -->
<div class="wp-block-column has-border-color has-contrast-border-color" style="border-width:1px;padding-top:var(--wp--preset--spacing--small);padding-right:var(--wp--preset--spacing--small);padding-bottom:var(--wp--preset--spacing--small);padding-left:var(--wp--preset--spacing--small)">
```

Dark card: same padding, drop the border, fill with contrast and base text.

```html
<!-- wp:column {"style":{"spacing":{"padding":{"top":"var:preset|spacing|small","right":"var:preset|spacing|small","bottom":"var:preset|spacing|small","left":"var:preset|spacing|small"}}},"backgroundColor":"contrast","textColor":"base"} -->
<div class="wp-block-column has-base-color has-contrast-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--small);padding-right:var(--wp--preset--spacing--small);padding-bottom:var(--wp--preset--spacing--small);padding-left:var(--wp--preset--spacing--small)">
```

When a pattern has a light and a dark version, ship both. Do not encode dark mode with a custom class.

### Recipes

Copy the closest file rather than assembling these from scratch.

- **Hero, two columns** (`hero-two-columns.php`). Wide group, reversed columns, text constrained to 480px (heading `x-large`, paragraph, fill + outline buttons), image `sample_1200x1200.jpg`.
- **Hero, one column** (`hero-one-column.php`). Constrained to 800px. Image `sample_1920x1200.jpg`, centered heading, paragraph constrained to 600px, centered buttons.
- **Feature boxes** (`boxes-two.php`, `boxes-three.php`). Wide group, one column per box, light border card, `h3`, paragraph, one button.
- **Testimonials** (`testimonials.php`, `testimonials-image.php`). Three bordered columns. Decorative quote, centered quote, strong attribution. Image variant adds an image.
- **Pricing** (`pricing-three-columns.php` and the two- and four-column files). Bordered column: plan name (`small`, centered), price (`max-36`, weight 500), feature lines (`small`) separated by wide contrast separators, then a centered button. Two-column variant is constrained to 800px.
- **Stacked CTA** (`cta-stacked.php`). Constrained group, inner bordered group padded with `medium`, centered `x-large` heading, centered paragraph at line-height 1.5, centered button.
- **Inline CTA** (`cta-button.php`). Wide flex group, `space-between`, bordered, padded with `large`. Paragraph is `large` with `flexSize` 720px. Button is `small` and aligned right.
- **Product box** (`product-box.php`). Bordered group, class `stacked`. Columns: image at 35% (`sample_800x800.jpg`), then heading `large`, paragraph, button.
- **Newsletter** (`newsletter-signup.php`). Same bordered centered stack as the CTA. The form is a placeholder paragraph: `[INSERT SIGNUP FORM CODE HERE]`.
- **Portfolio** (`portfolio.php`). Reversed columns. Copy plus button on the left, gallery of four `sample_800x800.jpg` with `x-small` block gap on the right.
- **Team** (`team-four-columns.php`). Four columns, centered 200×200 image, `h3` at `medium`, centered bio, centered "View Profile" button.
- **Progress bars** (`progress-bars.php`). Label and percent in a space-between flex row (`small`). Bar is a contrast group whose width is the column `width`, with a 20px spacer inside. Columns are `isStackedOnMobile` false.
- **Header** (`header-default.php`). Full group, padding 30px, margin-top 0, constrained. Inner wide flex group, `space-between`: `site-title` and `navigation` with `setCascadingProperties` true.
- **Notification bar** (`header-notification-bar.php`). Full group, 15px vertical padding, 30px horizontal, 1px contrast border top and bottom. Centered flex row, `x-small` text, compact button (14px type, 5px/15px padding).
- **Footer, default** (`footer-default.php`). Full group, `small` font, vertical padding `small`, horizontal 30px. Wide flex, `space-between`: copyright paragraph, social links separated by middots.
- **Footer, three columns** (`footer-three-columns.php`). Same shell. Copyright, policy links, social icons with contrast background.
- **Footer, mega** (`footer-mega.php`). Vertical padding `x-large`. Wide columns: 55% intro with `h4` and button, then 15% link lists under `h4` headings.
- **Footer, stacked** (`footer-stacked.php`). Centered `max-48` heading, paragraph, button, then the default footer row.
- **Query, single column** (`posts.php`). `core/query` with `inherit` true, constrained. `post-template` is an article: linked title, flex meta row (date, `·`, author), excerpt with more text `Read More`. Pagination is previous and next only.
- **Query, grid** (`posts-grid.php`). `inherit` true, `alignwide`. Post template layout `grid`, `columnCount` 3. Featured image 16/9 and linked, title `large`, meta at `x-small`, excerpt `small` with `Read More →` and `excerptLength` 20.
- **Query, list** (`posts-list.php`). `inherit` false (a page can place it without taking over the main query). Wide separator, then each post is columns: 25% date, 50% linked title, 25% terms.
- **Full page** (`page-home.php`, `page-about.php`, `page-pricing.php`, `page-link.php`). Category `frost-page`. Stack full-bleed groups in one file. Do not reference other patterns with `wp:pattern`; paste the section markup. `page-link.php` is a centered 600px column with `minHeight` 100vh.

## Templates and parts

Templates are HTML, not PHP. No escaping, no `esc_html__`. Strings in templates are plain text inside block markup.

Shared skeleton:

```html
<!-- wp:template-part {"slug":"header","tagName":"header","className":"site-header"} /-->
<!-- wp:group {"tagName":"main","style":{"spacing":{"margin":{"top":"0"}}},"className":"site-content"} -->
<main class="wp-block-group" style="margin-top:0">
</main>
<!-- /wp:group -->
<!-- wp:template-part {"slug":"footer","tagName":"footer","className":"site-footer"} /-->
```

Pull a pattern in with `<!-- wp:pattern {"slug":"frost/posts"} /-->`. Content padding uses spacing presets (`small` top, `large` bottom on pages and singles). `margin.top` on `main` is `0`.

Entry header class is `entry-header`. Post meta is a flex group, class `post-meta`, gap 5px, font size 18px, with a paragraph containing `·` between date and author. Single posts add `entry-footer` for terms.

A new custom template needs a file in `templates/` and an entry in `theme.json` `customTemplates` (`name`, `postTypes`, `title`).

## Style variations

`styles/{name}.json` replaces `settings.color.palette` and `settings.color.duotone` only. Keep every slug (`base`, `contrast`, `primary`, `secondary`, `neutral`, and the three duotone slugs). Change hex values. `title` and `description` are the picker label. Existing variations: teal, magenta, green, orange, graphite, red, purple.

## CSS

Prefer `theme.json` for color, type, spacing, and block defaults. `style.css` is for behavior theme.json does not cover: box sizing, focus transitions, form controls, navigation overlay, submenu colors, `mark` highlight, and the utility classes `is-style-hidden-mobile` (max-width 600px) and `is-style-columns-reverse` (max-width 782px). New CSS goes in `style.css` under the existing section comment style (`/* Section` then `/* -- Subsection -- */`), uses preset custom properties, and stays global. Do not add a per-pattern stylesheet.

## Checklist for a new pattern

1. Copy the closest file in `patterns/`.
2. New filename, matching `Slug: frost/{filename}`.
3. `Title` lists the pieces and ends with a period.
4. One category. Add `Block Types` only for header, footer, or query.
5. Strings through `esc_html__` / `esc_attr__` with domain `frost`. Images through `esc_url( get_theme_file_uri() )`.
6. Spacing, color, and font size come from presets. Light cards use a contrast border. Dark cards use contrast background and base text, in a `-dark` file.
7. JSON attributes and HTML classes/styles match.
8. Every block is uncommented in `sai_allowed_block_types()`.
9. If it is a header or footer meant to be the site default, point `parts/header.html` or `parts/footer.html` at the new slug. Otherwise leave the parts alone.
