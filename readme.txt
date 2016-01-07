=== Featured Image Sharpen Up ===
Contributors: mabujo
Tags: plugin, images, page speed, lazy load, svg
Requires at least: 3.9
Tested up to: 4.4
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin helps increase the page loading speed of your site by replacing featured post images with a small inline image, and lazy loading the full image.

== Description ==

Featured Image Sharpen Up makes a tiny version of your featured post image and places it in the head of the page.
As you scroll down the page, some JavaScript lazy loads the featured images in the background and replaces the tiny images as the full images are loaded.

You may have seen a similar effect used for images on medium.com.

Created on WordPress 4.4, but should work on earlier versions.
Tested working on all Twenty Sixteen, Twenty Fifteen, Twenty Fourteen, Twenty Thirteen, Twenty Twelve and Twenty Eleven themes. Should work on other themes, but may need CSS adjustments, depending on theme handling of featured images.

Uses jQuery.Lazy(); (http://jquery.eisbehr.de/lazy/) and Hugh Lashbrooke's plugin template (https://github.com/hlashbrooke/WordPress-Plugin-Template)

Plugin page : [Featured Image Sharpen Up plugin @ mabujo](https://mabujo.com/blog/featured-image-sharpen-up-wordpress-plugin/)

== Installation ==

Installing "Featured Image Sharpen Up" can be done either by searching for "Featured Image Sharpen Up" via the "Plugins > Add New" screen in your WordPress dashboard, or by using the following steps:

1. Download the plugin via WordPress.org
2. Upload the ZIP file through the 'Plugins > Add New > Upload' screen in your WordPress dashboard
3. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. The stretched and blurred placeholder image
2. When the image is loaded
3. Showing network requests as user scrolls down the page and images are lazy loaded.

== Frequently Asked Questions ==

= Does this plugin work with my theme? =
It should do! We've tested the plugin with all the default WordPress themes and some of the most popular third party themes from the Wordpress Theme Directory.

= What if the plugin doesn't work with my theme? =
The plugin uses standard WordPress hooks so it should work with nearly all well built themes. If you have problems with the plugin and your theme and contact us, please make sure to include your theme.

== Changelog ==

= 1.0.1 =
* 2016-01-07
* Fix frontend js address bug.

= 1.0 =
* 2015-12-17
* Initial release

== Upgrade Notice ==

= 1.0 =
* 2015-12-17
* Initial release
