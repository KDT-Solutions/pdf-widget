# PDF Widget

WordPress plugin to embed PDF files with the [Adobe PDF Embed API](https://developer.adobe.com/document-services/docs/overview/pdf-embed-api/), as Elementor widget or with the `[adobepdf]` shortcode.

- WordPress.org: https://wordpress.org/plugins/pdf-widget/
- Current version: 1.4 (tested up to WordPress 7.1, Elementor 3.8+ / 4.x)

## Features

- Elementor widget (supports dynamic tags, e.g. an ACF file field) and shortcode
- All Adobe embed modes: In-Line, Sized Container, Full Window, Lightbox
- Download and print buttons can be switched off
- Several PDFs on the same page
- The Adobe viewer script is only loaded on pages that contain a PDF

## Setup

1. Install and activate the plugin.
2. Get a free client ID from Adobe: https://acrobatservices.adobe.com/dc-integration-creation-app-cdn/main.html?api=pdf-embed-api (register the domain of your site).
3. Enter the client ID under Settings -> PDF Widget.

## Shortcode

```
[adobepdf link="https://example.com/file.pdf"]
[adobepdf link="https://example.com/file.pdf" embedmode="SIZED_CONTAINER" height="700" download="no" print="no"]
```

| Attribute   | Values                                                    | Default   |
|-------------|-----------------------------------------------------------|-----------|
| `link`      | URL of the PDF                                            | -         |
| `embedmode` | `IN_LINE`, `SIZED_CONTAINER`, `FULL_WINDOW`, `LIGHT_BOX`  | `IN_LINE` |
| `height`    | e.g. `600`, `80vh` (Sized Container / Full Window only)   | `600px`   |
| `download`  | `yes` / `no`                                              | `yes`     |
| `print`     | `yes` / `no`                                              | `yes`     |

## Release to WordPress.org

The plugin is published via SVN (`https://plugins.svn.wordpress.org/pdf-widget`).
The workflow `.github/workflows/deploy.yml` deploys automatically when a tag is pushed:

1. Raise the version in `pdf-widget.php` (header and `PDF_WIDGET_VERSION`) and `Stable tag` in `readme.txt`, add a changelog entry.
2. Commit, then push a tag with the same version, e.g. `git tag 1.5 && git push origin 1.5`.

Required repository secret: `SVN_PASSWORD` (SVN password from the WordPress.org profile, "Account & Security").

## License

GPLv2 or later
