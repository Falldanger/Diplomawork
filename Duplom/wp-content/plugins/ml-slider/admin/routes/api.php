<?php

if (!defined('ABSPATH')) die('No direct access.');

/** 
 * Class to handle ajax endpoints, specifically used by vue components
 * If possible, keep logic here to a minimum.
 */
class MetaSLider_Api {
	
	/**
	 * Theme instance
	 * 
	 * @var object
	 * @see get_instance()
	 */
	protected static $instance = null;

	/**
	 * Constructor
	 */
	public function __construct() {}

	/**
	 * Setup 
	 */
	public function setup() {
		$this->slideshows = new MetaSlider_Slideshows();
		$this->themes = MetaSlider_Themes::get_instance();
	}

	/**
	 * Used to access the instance
	 */
	public static function get_instance() {
		if (null === self::$instance) self::$instance = new self();
		return self::$instance;
	}

	/**
	 * Register routes for admin ajax. Even if not used these can still be available.
	 */
	public function register_admin_ajax_hooks() {

		// Slideshows
		add_action('wp_ajax_ms_get_all_slideshows', array(self::$instance, 'get_all_slideshows'));
		add_action('wp_ajax_ms_get_preview', array(self::$instance, 'get_preview'));
		add_action('wp_ajax_ms_delete_slideshow', array(self::$instance, 'delete_slideshow'));

		// Themes
		add_action('wp_ajax_ms_get_all_free_themes', array(self::$instance, 'get_all_free_themes'));
		add_action('wp_ajax_ms_get_custom_themes', array(self::$instance, 'get_custom_themes'));
		add_action('wp_ajax_ms_set_theme', array(self::$instance, 'set_theme'));

		// Slides
		add_action('wp_ajax_ms_import_images', array(self::$instance, 'import_images'));
	}

	/**
	 * Returns all slideshows
	 * 
	 * @return array|WP_Error
	 */
    public function get_all_slideshows() {

		$capability = apply_filters('metaslider_capability', 'edit_others_posts');
		if (!current_user_can($capability)) {
			return wp_send_json_error(array(
				'message' => __('You do not have access to this resource.', 'ml-slider')
			), 401);
		}

		$slideshows = $this->slideshows->get_all_slideshows();

		if (is_wp_error($slideshows)) {
			return wp_send_json_error(array(
				'message' => $slideshows->get_error_message()
			), 401);
		}
		
		return wp_send_json_success($slideshows, 200);
    }

	/**
	 * Returns all custom themes
	 * 
	 * @return array|WP_Error
	 */
    public function get_custom_themes() {

		$capability = apply_filters('metaslider_capability', 'edit_others_posts');
		if (!current_user_can($capability)) {
			return wp_send_json_error(array(
				'message' => __('You do not have access to this resource.', 'ml-slider')
			), 401);
		}
		
		$themes = $this->themes->get_custom_themes();

		if (is_wp_error($themes)) {
			return wp_send_json_error(array(
				'message' => $themes->get_error_message()
			), 400);
		}

		return wp_send_json_success($themes, 200);
	}

	/**
	 * Returns all themes
	 * 
	 * @return array|WP_Error
	 */
    public function get_all_free_themes() {

		$user = wp_get_current_user();
		$capability = apply_filters('metaslider_capability', 'edit_others_posts');

		if (!current_user_can($capability)) {
			return wp_send_json_error(array(
				'message' => __('You do not have access to this resource.', 'ml-slider')
			), 401);
		}
		
		$themes = $this->themes->get_all_free_themes();

		if (is_wp_error($themes)) {
			return wp_send_json_error(array(
				'message' => $themes->get_error_message()
			), 400);
		}

		return wp_send_json_success($themes, 200);
	}
	
	/**
	 * Sets a specific theme
	 * 
	 * @param object $request The request
	 * @return array|WP_Error
	 */
    public function set_theme($request) {
		if (method_exists($request, 'get_param')) {
			$slideshow_id = $request->get_param('slideshow_id');
			$theme = $request->get_param('theme');
			$theme = is_array($theme) ? $theme : array();
		} else {

			// Support for admin-ajax
			$slideshow_id = $_POST['slideshow_id'];
			$theme = isset($_POST['theme']) ? $_POST['theme'] : array();
		}

		$user = wp_get_current_user();
		$capability = apply_filters('metaslider_capability', 'edit_others_posts');

		if (!current_user_can($capability)) {
			return wp_send_json_error(array(
				'message' => __('You do not have access to this resource.', 'ml-slider')
			), 401);
		}

		if (!is_array($theme)) {
			return wp_send_json_error(array(
				'message' => __('The request format was not valid.', 'ml-slider')
			), 415);
		}
		
		$response = $this->themes->set(absint($slideshow_id), $theme);
		
		if (!$response) {
			return wp_send_json_error(array(
				'message' => 'There was an issue while attempting to save the theme. Please refresh and try again.'
			), 400);
		}

		// If we made it this far, return the theme data
		return wp_send_json_success($theme, 200);
    }
	
	/**
	 * Returns the preview HTML
	 * 
	 * @param object $request The request
	 * @return array|WP_Error
	 */
    public function get_preview($request) {
		if (method_exists($request, 'get_param')) {
			$slideshow_id = $request->get_param('slideshow_id');
			$theme_id = $request->get_param('theme_id');
		} else {
			// Support for admin-ajax
			$slideshow_id = $_GET['slideshow_id'];
			$theme_id = isset($_GET['theme_id']) ? $_GET['theme_id'] : array();
		}

		$user = wp_get_current_user();
		$capability = apply_filters('metaslider_capability', 'edit_others_posts');

		if (!current_user_can($capability)) {
			return wp_send_json_error(array(
				'message' => __('You do not have access to this resource.', 'ml-slider')
			), 401);
		}

		// The theme id can be either a string or null, exit if it's something else
		if (!is_null($theme_id) && !is_string($theme_id)) {
			return wp_send_json_error(array(
				'message' => __('The request format was not valid.', 'ml-slider')
			), 415);
		}

		// If the slideshow was deleted
		$slideshow = get_post($slideshow_id);
		if ('publish' !== $slideshow->post_status) {
			return wp_send_json_error(array(
				'message' => __('This slideshow is no longer available.', 'ml-slider')
			), 410);
		}

		$html = $this->slideshows->preview(
			absint($slideshow_id), $theme_id
		);

		if (is_wp_error($html)) {
			return wp_send_json_error(array(
				'message' => $html->get_error_message()
			), 400);
		}

		return wp_send_json_success($html, 200);
	}
	
	/**
	 * Delete a slideshow
	 * 
	 * @param object $request The request
	 * @return array|WP_Error
	 */
    public function delete_slideshow($request) {
		if (method_exists($request, 'get_param')) {
			$slideshow_id = $request->get_param('slideshow_id');
		} else {
			// Support for admin-ajax
			$slideshow_id = isset($_POST['slideshow_id']) ? $_POST['slideshow_id'] :
				(isset($_GET['slider_id']) ? $_GET['slider_id'] : null); // bw compatability
		}

		$user = wp_get_current_user();
		$capability = apply_filters('metaslider_capability', 'edit_others_posts');

		if (!current_user_can($capability)) {
			return wp_send_json_error(array(
				'message' => __('You do not have access to this resource.', 'ml-slider')
			), 401);
		}

		// If the slideshow was deleted
		$slideshow = get_post($slideshow_id);
		if ('publish' !== $slideshow->post_status) {
			return wp_send_json_error(array(
				'message' => __('This slideshow is no longer available.', 'ml-slider')
			), 410);
		}

		// Confirm it's one of ours
		if ('ml-slider' !== get_post_type($slideshow_id)) {
			return wp_send_json_error(array(
				'message' => __('This was not a slideshow, so we cannot delete it.', 'ml-slider')
			), 409);
		}

		$next_slideshow = $this->slideshows->delete(absint($slideshow_id));
		
		if (is_wp_error($next_slideshow)) {
			return wp_send_json_error(array(
				'message' => 'There was an issue while attempting delete the slideshow. Please refresh and try again.'
			), 400);
		}

		return wp_send_json_success($next_slideshow, 200);
	}
	
	/**
	 * Import theme images
	 * 
	 * @param object $request The request
	 * @return array|WP_Error
	 */
    public function import_images($request) {
		if (method_exists($request, 'get_param')) {
			$slideshow_id = $request->get_param('slideshow_id');
			$theme_id = $request->get_param('theme_id');
			$slide_id = $request->get_param('slide_id');
			$image_data = $request->get_param('image_data');
		} else {

			// Support for admin-ajax
			$slideshow_id = isset($_POST['slideshow_id']) ? $_POST['slideshow_id'] : null;
			$theme_id = isset($_POST['theme_id']) ? $_POST['theme_id'] : 'none';
			$slide_id = isset($_POST['slide_id']) ? $_POST['slide_id'] : null;
			$image_data = isset($_POST['image_data']) ? $_POST['image_data'] : null;
		}

		$user = wp_get_current_user();
		$capability = apply_filters('metaslider_capability', 'edit_others_posts');

		if (!current_user_can($capability)) {
			return wp_send_json_error(array(
				'message' => __('You do not have access to this resource.', 'ml-slider')
			), 401);
		}

		// Create a slideshow if one doesn't exist
        if (is_null($slideshow_id) || !absint($slideshow_id)) {
            $slideshow_id = $this->slideshows->create();

            if (is_wp_error($slideshow_id)) {
                return wp_send_json_error(array(
                    'message' => $slideshow_id->get_error_message()
                ), 400);
            }
		}

		// If there are files here, then we need to prepare them
		// Dont use get_file_params() as it's WP4.4
		$images = isset($_FILES['files']) ? $this->process_uploads($_FILES['files'], $image_data) : array();

		// $images should be an array of image data at this point
		// Capture the slide markup that is typically echoed from legacy code
		ob_start();

		$image_ids = MetaSlider_Image::instance()->import($images, $theme_id);
		if (is_wp_error($image_ids)) {
            return wp_send_json_error(array(
                'message' => $image_ids->get_error_message()
            ), 400);
        }
		
		$errors = array();
		$method = is_null($slide_id) ? 'create_slide' : 'update';
		foreach ($image_ids as $image_id) {
			$slide = new MetaSlider_Slide(absint($slideshow_id), $slide_id);
			$slide->add_image($image_id)->$method();
			if (is_wp_error($slide->error)) array_push($errors, $slide->error);
		}

		// Disregard the output. It's not needed for imports
		ob_get_clean();

        // Send back the first error, if any
        if (isset($errors[0])) {
            return wp_send_json_error(array(
                'message' => $errors[0]->get_error_message()
            ), 400);
        }

		return wp_send_json_success(wp_get_attachment_thumb_url($slide->slide_data['id']), 200);
	}


	/**
	 * Verify uploads are useful and return an array with metadata
	 * For now only handles images.
	 * 
	 * @param array $files An array of the images
	 * @param array $data  Data for the image, keys should match
	 *
	 * @return array An array with image data
	 */
	public function process_uploads($files, $data = null) {
		$images = array();
		foreach($files['tmp_name'] as $index => $tmp_name) {

			// If there was an error, skip this file
			// TODO: consider reporting an error back to the user, but skipping might be best
			if (!empty($files['error'][$index])) continue;

			// If the name is empty or isn't an uploaded file, skip it
			if (empty($tmp_name) || !is_uploaded_file($tmp_name)) continue;

			// For now there's no reason to import anything but images
			if (!strstr(mime_content_type($tmp_name), "image/")) continue;
				
			// Ignore images too large for the server (According to WP)
			// The server probably handles this already
			// TODO: possibly provide user feedback, but skipping moves forward
			$max_upload_size = wp_max_upload_size();
			if (!$max_upload_size) $max_upload_size = 0;
			$file_size = $files['size'][$index];
			if ($file_size > $max_upload_size) continue;

			// Tests were passed, so move forward with this image
			$filename = $files['name'][$index];
			$images[$filename] = array(
				'source' => $tmp_name,
				'caption' => isset($data[$filename]['caption']) ? $data[$filename]['caption'] : '',
				'title' => isset($data[$filename]['title']) ? $data[$filename]['title'] : '',
				'description' => isset($data[$filename]['description']) ? $data[$filename]['description'] : '',
				'alt' => isset($data[$filename]['alt']) ? $data[$filename]['alt'] : ''
			);
		}
		return $images;
	}
}

if (class_exists('WP_REST_Controller')) :
	/**
	 * Class to handle REST route api endpoints.
	 */
	class MetaSlider_REST_Controller extends WP_REST_Controller {

		/**
		 * Namespace and version for the API
		 * 
		 * @var string
		 */
		protected $namespace = 'metaslider/v1';

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action('rest_api_init', array($this, 'register_routes'));
			$this->api = MetaSLider_Api::get_instance();
			$this->api->setup();
		}

		/**
		 * Register routes
		 */
		public function register_routes() {

			register_rest_route($this->namespace, '/slideshow/all', array(
				array(
					'methods' => 'GET',
					'callback' => array($this->api, 'get_all_slideshows')
				)
			));
			register_rest_route($this->namespace, '/slideshow/preview', array(
				array(
					'methods' => 'GET',
					'callback' => array($this->api, 'get_preview')
				)
			));
			register_rest_route($this->namespace, '/slideshow/delete', array(
				array(
					'methods' => 'POST',
					'callback' => array($this->api, 'delete_slideshow')
				)
			));
			
			register_rest_route($this->namespace, '/themes/all', array(
				array(
					'methods' => 'GET',
					'callback' => array($this->api, 'get_all_free_themes')
				)
			));
			register_rest_route($this->namespace, '/themes/custom', array(
				array(
					'methods' => 'GET',
					'callback' => array($this->api, 'get_custom_themes')
				)
			));
			register_rest_route($this->namespace, '/themes/set', array(
				array(
					'methods' => 'POST',
					'callback' => array($this->api, 'set_theme')
				)
			));
			
			register_rest_route($this->namespace, '/import/images', array(
				array(
					'methods' => 'POST',
					'callback' => array($this->api, 'import_images')
				)
			));
		}
	}
endif;
