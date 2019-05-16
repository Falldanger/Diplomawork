<?php

if (!defined('ABSPATH')) die('No direct access.');

/**
 *  Class to handle slideshows
 */
class MetaSlider_Slideshows {

	/**
	 * Themes class
	 * 
	 * @var object
	 */
	private $themes;

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
	public function __construct() {
		if (!class_exists('MetaSlider_Themes')) {
			require_once plugin_dir_path(__FILE__) . 'Themes.php';
		}
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
	 * Method to add a slideshow
	 * 
	 * @return int
	 */
	public function create() {

		// Check if there's an existing slideshow
		$ms_settings = new MetaSlider_Slideshow_Settings();
		$recent_slideshow =  $this->get_recent_slideshow();
		$defaults = !empty($recent_slideshow) ? get_post_meta($recent_slideshow['id'], 'ml-slider_settings', true) : $ms_settings->defaults();
		
		// Insert the slideshow
		// TODO: Maybe have a list of 100 random words that could be slideshow titles
        $slideshow_id = wp_insert_post(array(
			'post_title' => __("New Slideshow", "ml-slider"),
			'post_status' => 'publish',
			'post_type' => 'ml-slider'
		));
		
		if (is_wp_error($slideshow_id)) {
			// No translation as this wont show to the user (but will in the payload)
			return new WP_Error('post_create_failed', 'A new, blank slideshow could not be created', array('status' => 409));
		}

		// insert the settings
		// TODO: Perhaps we create a settings page and let the user select defaults
        add_post_meta($slideshow_id, 'ml-slider_settings', $defaults, true);

		// Create the taxonomy term, the term is the ID of the slideshow itself
		// I'm not sure this is needed but it's in the original code so I'm leaving it in
		// I believe it might be here for backwards compatibility
		// I'm not handling the error because as stated I'm not sure it's required in modern WP
		wp_insert_term($slideshow_id, 'ml-slider');
		
		return $slideshow_id;
	}

	/**
	 * Method to delete a slideshow
	 * 
	 * @param int|string $slideshow_id - The id of the slideshow to delete
	 * 
	 * @return int|boolean - id of the next slideshow to show, or false
	 */
	public function delete($slideshow_id) {

        // Send the post to trash
		$id = wp_update_post(array(
			'ID' => $slideshow_id,
			'post_status' => 'trash'
		));

		$this->delete_all_slides($slideshow_id);

		$recent_slideshow = $this->get_recent_slideshow();
		return !empty($recent_slideshow) ? $recent_slideshow['id'] : false;
	}


	/**
     * Method to disassociate slides from a slideshow
     *
     * @param int $slideshow_id - the id of the slideshow
	 * 
	 * @return int
     */
	public function delete_all_slides($slideshow_id) {
		$args = array(
			'force_no_custom_order' => true,
			'orderby' => 'menu_order',
			'order' => 'ASC',
			'post_type' => array('ml-slide'),
			'post_status' => array('publish'),
			'lang' => '', // polylang, ingore language filter
			'suppress_filters' => 1, // wpml, ignore language filter
			'posts_per_page' => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'ml-slider',
					'field' => 'slug',
					'terms' => $slideshow_id
				)
			)
		);

		// I believe if this fails there's no real harm done
		// because slides don't really need to be linked to their parent slideshow
		$query = new WP_Query($args);
		while ($query->have_posts()) {
			$query->next_post();
			wp_trash_post($query->post->ID);
		}

		return $slideshow_id;
	}


	/**
	 * Method to get the most recently modified slideshow
	 * 
	 * @return int The id of the slideshow
	 */
	public function get_recent_slideshow() {

        $args = array(
            'force_no_custom_order' => true,
            'post_type' => 'ml-slider',
            'num_posts' => 1,
            'post_status' => 'publish',
            'suppress_filters' => 1, // wpml, ignore language filter
            'orderby' => 'modified',
            'order' => 'DESC'
        );

		$slideshow = get_posts(apply_filters('metaslider_all_meta_sliders_args', $args));

        return isset($slideshow[0]) ? $this->build_slideshow_object($slideshow[0]) : array();
	}

	/**
	 * Method to get all slideshows from the database
	 * 
	 * @return array 
	 */
	public function get_all_slideshows() {

        $args = array(
            'post_type' => 'ml-slider',
            'post_status' => array('inherit', 'publish'),
            'orderby' => 'date',
            'suppress_filters' => 1, // wpml, ignore language filter
            'order' => 'ASC',
            'posts_per_page' => -1
		);

		$slideshows = get_posts(apply_filters('metaslider_all_meta_sliders_args', $args));

        return array_map(array($this, 'build_slideshow_object'), $slideshows);
	}

	/**
     * Method to build out the slideshow object
	 * For now this wont include slides. They will be handled separately.
     *
	 * @param object $slideshow - The slideshow object
     * @return array
     */
	public function build_slideshow_object($slideshow) {

		if (empty($slideshow)) return array();

		return array(
			'id' => $slideshow->ID,
			'title' => $slideshow->post_title,
			'created_at' => $slideshow->post_date,
			'modified_at' => $slideshow->post_modified,
			'slides' => $this->active_slide_ids($slideshow->ID)
		);
	}

	/**
     * Method to get the slide ids
	 * 
	 * @param int|string $id - The id of the slideshow
	 * @return array - Returns an array of just the slide IDs
     */
	public function active_slide_ids($id) {
		$slides = get_posts(array(
			'force_no_custom_order' => true,
			'orderby' => 'menu_order',
			'order' => 'ASC',
			'post_type' => array('attachment', 'ml-slide'),
			'post_status' => array('inherit', 'publish'),
			'lang' => '',
			'posts_per_page' => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'ml-slider',
					'field' => 'slug',
					'terms' => $id
				)
			)
		));

		$slide_ids = array();
		foreach ($slides as $slide) {
			$type = get_post_meta($slide->ID, 'ml-slider_type', true);
            $type = $type ? $type : 'image'; // Default ot image

			// If this filter exists, that means the slide type is available (i.e. pro slides)
			if (has_filter("metaslider_get_{$type}_slide")) {
				array_push($slide_ids, $slide->ID);
			}
		}
		return $slide_ids;
	}

	/**
     * Method to get the latest slideshow
     */
	public function recently_modified() {}

	/**
     * Method to get a single slideshow from the database
	 * 
	 * @param string $id - The id of a slideshow
     */
	public function single($id) {}
	
	/**
     * Returns the shortcode of the slideshow
	 * 
	 * @param string|int  $id 		   - The id of a slideshow
	 * @param string|int  $restrict_to - page to limit the slideshow to
	 * @param string|null $theme_id    - load a theme, defaults to the current theme
     */
	public function shortcode($id = null, $restrict_to = null, $theme_id = null) {

		// if no id is given, try to find the first available slideshow
		if (is_null($id)) {
			$the_query = get_posts(array('orderby' => 'rand', 'posts_per_page' => '1'));
			$id = isset($the_query[0]) ? $the_query[0]->ID : $id;
		}

		return "[metaslider id='{$id}' restrict_to='{$restrict_to}' theme='{$theme_id}']";
	}

	/**
	 * Return the preview
	 * 
	 * @param int|string $slideshow_id The id of the current slideshow
	 * @param string 	 $theme_id 	   The folder name of the theme
	 * 
	 * @return string|WP_Error whether the file was included, or error class
	 */
	public function preview($slideshow_id, $theme_id = null) {
		if (!class_exists('MetaSlider_Slideshow_Settings')) {
			require_once plugin_dir_path(__FILE__) . 'Settings.php';
		}
		$settings = new MetaSlider_Slideshow_Settings($slideshow_id);

        try {
            ob_start();

            // Remove the admin bar
            remove_action('wp_footer', 'wp_admin_bar_render', 1000);
            
            // Load in theme if set. Note that the shortcode below is set to 'none'
            $this->themes->load_theme($slideshow_id, $theme_id); ?>

<!DOCTYPE html>
<html>
	<head>
		<style type='text/css'>
			<?php ob_start(); ?>
			body, html {
				overflow: auto;
				height:100%;
				margin:0;
				padding:0;
				box-sizing: border-box;
				font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; 
		        font-size: 14px; 
			}
			body {
				padding: 60px 40px 40px;
			}
			#preview-container {
				min-height: 100%;
				max-width: <?php echo $settings->get_single('width'); ?>px;
				margin: 0 auto;
				display: -webkit-box;
				display: -ms-flexbox;
				display: flex;
				-webkit-box-align: center;
				   -ms-flex-align: center;
				      align-items: center;
				-webkit-box-pack: center;
				   -ms-flex-pack: center;
				 justify-content: center;
			}
			#preview-inner {
				width: 100%;
				height: 100%;
			}
			.metaslider {
				margin: 0 auto;
			}
			<?php echo apply_filters('metaslider_preview_styles', ob_get_clean()); ?>
		</style>
		<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Expires" content="0">
	</head>
	<body>
		<div id="preview-container">
			<div id="preview-inner">
				<?php echo do_shortcode($this->shortcode(absint($slideshow_id), null, 'none')); ?>
			</div>
		</div>
		<?php wp_footer(); ?>
	</body>
</html>
			<?php return preg_replace('/\s+/S', " ", ob_get_clean());
		} catch (Exception $e) {
			ob_clean();
			return new WP_Error('preview_failed', $e->getMessage());
		}
	}
}
