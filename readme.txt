=== PDF Widget ===
Contributors: kdt-solutions
Tags: pdf, elementor, adobe, pdf viewer, embed
Requires at least: 5.0
Tested up to: 7.1
Requires PHP: 7.0
Stable tag: 1.4.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Embed PDF files with the Adobe PDF Embed API, as Elementor widget or with the [adobepdf] shortcode.

== Description ==

PDF Widget embeds PDF files with the Adobe PDF Embed API. You can use it as an Elementor widget (including dynamic tags, e.g. an ACF file field) or with the [adobepdf] shortcode on any page.

Features:

* Elementor widget and shortcode
* All embed modes of the Adobe PDF Embed API: In-Line, Sized Container, Full Window and Lightbox
* Download and print buttons can be switched off
* Several PDFs on the same page
* The Adobe viewer script is only loaded on pages that contain a PDF

You need a free client ID (API key) from Adobe. The domain of your website must be registered for that client ID.

== External services ==

This plugin uses the Adobe PDF Embed API, a service provided by Adobe Inc., to display PDF files.

On every page that contains a PDF, the visitor's browser loads the viewer script from https://acrobatservices.adobe.com/view-sdk/viewer.js. The URL and file name of the PDF and your client ID are passed to the viewer, and the browser then loads the PDF and further viewer resources from Adobe. Please mention this in your privacy policy.

* Adobe PDF Embed API: https://developer.adobe.com/document-services/docs/overview/pdf-embed-api/
* Adobe General Terms of Use: https://www.adobe.com/legal/terms.html
* Adobe Privacy Policy: https://www.adobe.com/privacy/policy.html

== Installation ==

1. Install the plugin through the 'Plugins' menu in WordPress, or extract the zip file into the `wp-content/plugins/` directory.
2. Activate the plugin.
3. Get a free client ID from Adobe: https://acrobatservices.adobe.com/dc-integration-creation-app-cdn/main.html?api=pdf-embed-api
4. Go to Settings -> PDF Widget, enter the client ID and save.
5. Use the Elementor widget "PDF Widget" or the [adobepdf] shortcode.

== Frequently Asked Questions ==

= How do I use the shortcode? =

`[adobepdf link="https://example.com/file.pdf"]`

Optional attributes:

* `embedmode` - IN_LINE (default), SIZED_CONTAINER, FULL_WINDOW or LIGHT_BOX
* `height` - height for SIZED_CONTAINER and FULL_WINDOW, e.g. `600` or `80vh` (default 600px)
* `download` - `no` hides the download button
* `print` - `no` hides the print button

Example: `[adobepdf link="https://example.com/file.pdf" embedmode="SIZED_CONTAINER" height="700" download="no"]`

= Do I need Elementor? =

Only for the widget. The shortcode works without Elementor.

= The PDF is not shown =

Check that the client ID is entered under Settings -> PDF Widget and that the domain of your website is registered for this client ID at Adobe. The PDF must be reachable by URL.

== Changelog ==

= 1.4.1 =
* Compatibility with WordPress 5.0 to 5.0.2 restored (no longer uses wp_unique_id())

= 1.4 =
* Viewer script loaded from the current Adobe address (acrobatservices.adobe.com) and only on pages with a PDF
* Compatible with Elementor 3.5+ and 4.x (new widget registration API)
* Embed mode, height, download and print button selectable in the widget and the shortcode
* Several PDFs on one page are now possible
* Fixed unclosed HTML container and a JavaScript error on every page
* Shortcode works without Elementor
* Settings are removed when the plugin is deleted
* Tested up to WordPress 7.1

= 1.3 =
* Changed description and minor code fixes

= 1.2 =
* Changed inclusion of JS and added dynamic file name for download

= 1.1 =
* Changes for Code Approval (Escaping Vars and minor bugfixes)

= 1.0 =
* First Release
