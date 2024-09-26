=== Plugin Name ===
Contributors: (this should be a list of wordpress.org userid's)
Donate link: https://upwork.com/freelancers/tonyhoang/
Tags: comments, spam
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin checks and updates all the post featured images when it is enabled and when posts are updated

== Description ==

This plugin will scan the post content to find the first image and set it as the featured image for the post.
Upon activation, the plugin will search for all published posts that do not have featured images and assign the first image found in their content as the featured image.

If the images are not yet in the Media Library, the plugin will first add the image to the database and then use it as the featured image.
If the image name is too long and cannot be used as the featured image, the plugin will rename it before adding it to the database.

== Installation ==
1. Clone or download the plugin from Github
1. Upload the zip file or copy the downloaded folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

