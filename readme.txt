=== WP Responsive Images ===
Contributors: stuartbates
Donate link: http://www.stuartbates.com/donate/
Tags: responsive, images, mobile, rwd
Requires at least: 2.1
Tested up to: 3.4.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WP Responsive Images detects mobile devices and dynamically serves up smaller image sizes to improve page load times and user experience

== Description ==

WP Responsive Images once activated automatically detects mobile devices viewing your website and serves up images of appropriate size
by dynamically resizing images and caching them for improved performance.

== Installation ==

1. Upload `WPResponsiveImages` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Tweet me @stuartbates telling me which domains you're using it on

== Frequently Asked Questions ==

= Do you detect high pixel density devices? =

No not yet.  This is the first version - I'll be adding high resolution detection very soon

== Screenshots ==

== Changelog ==

= 1.0 =
* Screen size detection via JS
* Backup detection via UA sniffing to protect against race conditions
* Image caching for improved performance
* Solution to allow it to work without mod_rewrite enabled