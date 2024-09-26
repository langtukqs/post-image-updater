<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://upwork.com/freelancers/tonyhoang
 * @since             1.0.0
 * @package           Post_Image_Updater
 *
 * @wordpress-plugin
 * Plugin Name:       Post Image Updater
 * Plugin URI:        https://github.com/langtukqs/post-image-updater
 * Description:       This plugin checks and updates all the post featured images when it is enabled and when posts are updated
 * Version:           1.0.0
 * Author:            langtukqs
 * Author URI:        https://upwork.com/freelancers/tonyhoang/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       post-image-updater
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'POST_IMAGE_UPDATER_VERSION', '1.0.0' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-post-image-updater.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-post-image-updater-activator.php
 */
function activate_post_image_updater() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-post-image-updater-activator.php';
	Post_Image_Updater_Activator::activate();
}

register_activation_hook( __FILE__, 'activate_post_image_updater' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_post_image_updater() {

	$plugin = new Post_Image_Updater();
	$plugin->run();

}
run_post_image_updater();
