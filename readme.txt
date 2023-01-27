=== PDF Widget ===
Contributors: kdt-solutions
Tags: pdf, elementor, adobe
Requires at least: 5.0
Tested up to: 6.1
Requires PHP: 7.0
Stable tag: 1.4
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html


== Description ==

Use the Adobe Embed API with Elementor or with a Shortcode.

This plugin uses the hosted Aobe Embed API and you need to sign up for a free account on [Adobe Documentcloud](https://documentcloud.adobe.com/dc-integration-creation-app-cdn/main.html?api=pdf-embed-api) and register the domain you use to get an API key. Calls are made to https://documentcloud.adobe.com/view-sdk/viewer.js so please be aware of any data protection regulations. More info about the API can be found [here](https://developer.adobe.com/document-services/docs/overview/pdf-embed-api/)

== Installation ==

= Minimum Requirements =

* WordPress 5.0 or greater
* PHP version 7.0 or greater
* MySQL version 5.0 or greater

= We recommend your host supports: =

* PHP version 7.0 or greater
* MySQL version 5.6 or greater
* WordPress Memory limit of 64 MB or greater (128 MB or higher is preferred)

= Installation =

1. Install using the WordPress built-in Plugin installer, or Extract the zip file and drop the contents in the `wp-content/plugins/` directory of your WordPress installation.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings -> PDF Widget
4. Enter your API Key from Adobe (check that domain is matching) and save
5. Now you can use the elementor Widget or the Shortcode


== Frequently Asked Questions ==

**How do I use the shortcode?**

Simply use [adobepdf link="url_to_pdf_here"]


== Changelog ==

= 1.4 =
* upgraded to Modern PDF viewer integration (Adobe)

= 1.3 =
* changed description and minor code fixes

= 1.2 =
* Changed inclusion of JS and added dynamic file name for download

= 1.1 =
* Changes for Code Approval (Escaping Vars and minor bugfixes)

= 1.0 =
* First Release
