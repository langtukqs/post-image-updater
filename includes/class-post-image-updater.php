<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://upwork.com/freelancers/tonyhoang
 * @since      1.0.0
 *
 * @package    Post_Image_Updater
 * @subpackage Post_Image_Updater/includes
 */

/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    Post_Image_Updater
 * @subpackage Post_Image_Updater/includes
 * @author     langtukqs <contact@tuanhoang.me>
 */
class Post_Image_Updater {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Post_Image_Updater_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'POST_IMAGE_UPDATER_VERSION' ) ) {
			$this->version = POST_IMAGE_UPDATER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'post-image-updater';

		$this->load_dependencies();

		add_action('save_post', array($this, 'set_featured_image_if_not_exists'), 999, 2);
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Post_Image_Updater_Loader. Orchestrates the hooks of the plugin.
	 * - Post_Image_Updater_i18n. Defines internationalization functionality.
	 * - Post_Image_Updater_Admin. Defines all hooks for the admin area.
	 * - Post_Image_Updater_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-post-image-updater-loader.php';

		$this->loader = new Post_Image_Updater_Loader();

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Post_Image_Updater_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * @param $post_id
	 * @param WP_Post $post
	 *
	 * @return void
	 */
	static function set_featured_image_if_not_exists($post_id, WP_Post $post) {
		// Check if the post has a featured image
		if (has_post_thumbnail($post_id)) {
			return; // Exit if a featured image already exists
		}

		// Get the post content
		$post_content = $post->post_content;

		// Use regex to find the first image in the content
		preg_match('/<img[^>]+src="([^">]+)"/', $post_content, $matches);

		if (!empty($matches[1])) {
			$image_url = $matches[1];

			// Get the media ID from the image URL
			$attachment_id = attachment_url_to_postid($image_url);
			$set_image = false;
			// Check if a valid attachment ID was found
			if ($attachment_id) {
				// Set the featured image for the post
				set_post_thumbnail($post_id, $attachment_id);
				$set_image = true;
			}else{
				// Remove size specification from the URL
				$new_url = preg_replace('/-\d+x\d+\.(jpg|jpeg|png|gif)$/i', '.$1', $image_url);
				// Get the media ID from the image URL
				$new_id = attachment_url_to_postid($new_url);

				// Check if the attachment is found after removing the size from name;
				if ($new_id) {
					// Set the featured image for the post
					set_post_thumbnail( $post_id, $new_id );
					$set_image = true;
				}
			}

			// If the image is not found in the database, add it to the database first
			if($set_image === false){
				$attachment_id = Post_Image_Updater::add_file_from_link_to_database($image_url);
				if(intval($attachment_id)){
					set_post_thumbnail($post_id, $attachment_id);
				}
			}
		}
	}

	/**
	 * Add file from link to media database
	 * @param $file_url
	 *
	 * @return array|int|WP_Error
	 */
	static function add_file_from_link_to_database($file_url) {
		// Check if the URL is valid
		if (!filter_var($file_url, FILTER_VALIDATE_URL)) {
			return new WP_Error('invalid_url', 'The provided URL is not valid.');
		}

		// Get the file name from the URL
		$file_name = basename($file_url);

		// Check if the file name is too long
		$max_length = 15; // Set maximum length for the file name
		if (strlen($file_name) > $max_length) {
			// Shorten the file name and keep the extension
			$file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
			$base_name = pathinfo($file_name, PATHINFO_FILENAME);

			// Truncate the base name if it's too long
			$base_name = substr($base_name, 0, $max_length - strlen($file_extension) - 1);

			// Create a new file name with a timestamp to ensure uniqueness
			$file_name = $base_name . '-' . time() . '.' . $file_extension;
		}

		// Download the file
		$response = wp_remote_get($file_url);

		// Check for errors in the response
		if (is_wp_error($response)) {
			return $response; // Return error if the download failed
		}

		// Get the file content
		$file_content = wp_remote_retrieve_body($response);

		// Check if file content is empty
		if (empty($file_content)) {
			return new WP_Error('empty_file', 'The file is empty or could not be retrieved.');
		}

		// Define upload directory
		$upload_dir = wp_upload_dir();

		// Create a unique file name to avoid overwriting
		$unique_file_name = wp_unique_filename($upload_dir['path'], $file_name);

		// Set the full path for the file
		$file_path = $upload_dir['path'] . '/' . $unique_file_name;

		// Save the file to the uploads directory
		$file_saved = file_put_contents($file_path, $file_content);

		// Check if the file was saved successfully
		if ($file_saved === false) {
			return new WP_Error('file_save_error', 'Could not save the file.');
		}

		// Prepare the file data for attachment
		$file_type = wp_check_filetype($unique_file_name, null);
		$attachment = array(
			'guid'           => $upload_dir['url'] . '/' . $unique_file_name,
			'post_mime_type' => $file_type['type'],
			'post_title'     => sanitize_file_name($unique_file_name),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		// Insert the attachment into the database
		$attachment_id = wp_insert_attachment($attachment, $file_path);

		// Include the image.php file to handle the attachment properly
		require_once(ABSPATH . 'wp-admin/includes/image.php');

		// Generate attachment metadata and update the database record
		$attach_data = wp_generate_attachment_metadata($attachment_id, $file_path);
		wp_update_attachment_metadata($attachment_id, $attach_data);

		// Return the attachment ID
		return $attachment_id;
	}

}
