<?php

/**
 * Fired during plugin activation
 *
 * @link       https://upwork.com/freelancers/tonyhoang
 * @since      1.0.0
 *
 * @package    Post_Image_Updater
 * @subpackage Post_Image_Updater/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Post_Image_Updater
 * @subpackage Post_Image_Updater/includes
 * @author     langtukqs <contact@tuanhoang.me>
 */
class Post_Image_Updater_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Get all the published post without featured image
		// Define the query arguments
		$args = array(
			'post_type'      => 'post', // Specify the post type (e.g., 'post', 'page', etc.)
			'post_status'    => 'publish', // Only get published posts
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => '_thumbnail_id', // The meta key for featured images
					'compare' => 'NOT EXISTS', // Check if the key does not exist
				),
				array(
					'key'     => '_thumbnail_id', // The meta key for featured images
					'compare' => '=', // Check if the key does not exist
					'value'   => 'raw'
				),
			),
			'posts_per_page' => - 1, // Get all posts
		);

		// Create a new query
		$query = new WP_Query( $args );

		// Check if there are any posts
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post = get_post();

				Post_Image_Updater::set_featured_image_if_not_exists(get_the_ID(), $post);
			}
		}
		// Reset post data
		wp_reset_postdata();
	}

}
