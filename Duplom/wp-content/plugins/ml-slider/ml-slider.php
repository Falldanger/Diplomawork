<?php
// @codingStandardsIgnoreLine
/*
 * MetaSlider. Slideshow plugin for WordPress.
 *
 * Plugin Name: MetaSlider
 * Plugin URI:  https://www.metaslider.com
 * Description: Easy to use slideshow plugin. Create SEO optimised responsive slideshows with Nivo Slider, Flex Slider, Coin Slider and Responsive Slides.
 * Version:     3.12.1
 * Author:      Team Updraft
 * Author URI:  https://www.metaslider.com
 * License:     GPL-2.0+
 * Copyright:   2017- Simba Hosting Ltd
 *
 * Text Domain: ml-slider
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) die('No direct access.');

if (!class_exists('MetaSliderPlugin')) :

/**
 * Register the plugin.
 *
 * Display the administration panel, insert JavaScript etc.
 */
class MetaSliderPlugin {

    /**
     * MetaSlider version number
     *
     * @var string
     */
    public $version = '3.12.1';

	/**
     * Pro installed version number
     *
     * @var string
     */
    public $installed_pro_version = '';

    /**
     * Specific SLider
     *
     * @var MetaSlider
     */
    public $slider = null;
    
	/**
	 * Instance object
	 *
	 * @var object
	 * @see get_instance()
	 */
	protected static $instance = NULL;

    /**
     * Constructor
     */
    public function __construct() {}

	/**
	 * Used to access the instance
     *
     * @return object - class instance
	 */
	public static function get_instance() {
		if (NULL === self::$instance) self::$instance = new self();
		return self::$instance;
	}

    /**
     * Setup
     */
    public function setup() {
		define('METASLIDER_PATH', plugin_dir_path(__FILE__));

        $this->includes();
        $this->define_constants();
        $this->setup_actions();
        $this->setup_filters();
		$this->setup_shortcode();
		$this->check_dependencies();
		
		// Load in slideshow admin relates classes.
        $this->register_slide_types();
		$this->admin = new MetaSlider_Admin_Pages($this);
		
		// Load in slideshow related classes
		require_once(METASLIDER_PATH . 'admin/slideshows/bootstrap.php');
		$this->themes = MetaSlider_Themes::get_instance();
		$this->slideshows = MetaSlider_Slideshows::get_instance();
		
		// Default to WP (4.4) REST API but backup with admin ajax
		require_once(METASLIDER_PATH . 'admin/routes/api.php');
		$this->api = MetaSlider_Api::get_instance();
		$this->api->setup();
		$this->api->register_admin_ajax_hooks();
		if (class_exists('WP_REST_Controller')) new MetaSlider_REST_Controller();

        if (function_exists('register_block_type')) {
            $this->gutenberg = new MetaSlider_Gutenberg($this);
		}
	}

    /**
     * Define MetaSlider constants
     */
	private function define_constants() {
		if (!defined('METASLIDER_VERSION')) {
			define('METASLIDER_VERSION', $this->version);
			define('METASLIDER_BASE_URL', plugin_dir_url(metaslider_plugin_is_installed('ml-slider')));
			define('METASLIDER_ASSETS_URL', METASLIDER_BASE_URL . 'assets/');
			define('METASLIDER_ADMIN_URL', METASLIDER_BASE_URL . 'admin/');
			
			// Use the themes in the plugin dir if it's there (useful for developing)
			$has_themes_repo = file_exists(trailingslashit(WP_PLUGIN_DIR) . 'ml-slider-themes/manifest.php');
			$themes_path = $has_themes_repo ? trailingslashit(WP_PLUGIN_DIR) . 'ml-slider-themes/' : METASLIDER_PATH . 'themes/';
			$themes_url = $has_themes_repo ? trailingslashit(plugins_url('ml-slider-themes/')) : METASLIDER_BASE_URL . 'themes/';

			define('METASLIDER_THEMES_PATH', $themes_path);
			define('METASLIDER_THEMES_URL', $themes_url);
		}
	}

    /**
     * All MetaSlider classes
     */
    private function plugin_classes() {
        return array(
            'metaslider'             => METASLIDER_PATH . 'inc/slider/metaslider.class.php',
            'metacoinslider'         => METASLIDER_PATH . 'inc/slider/metaslider.coin.class.php',
            'metaflexslider'         => METASLIDER_PATH . 'inc/slider/metaslider.flex.class.php',
            'metanivoslider'         => METASLIDER_PATH . 'inc/slider/metaslider.nivo.class.php',
            'metaresponsiveslider'   => METASLIDER_PATH . 'inc/slider/metaslider.responsive.class.php',
            'metaslide'              => METASLIDER_PATH . 'inc/slide/metaslide.class.php',
            'metaimageslide'         => METASLIDER_PATH . 'inc/slide/metaslide.image.class.php',
            'metasliderimagehelper'  => METASLIDER_PATH . 'inc/metaslider.imagehelper.class.php',
            'metaslidersystemcheck'  => METASLIDER_PATH . 'inc/metaslider.systemcheck.class.php',
            'metaslider_widget'      => METASLIDER_PATH . 'inc/metaslider.widget.class.php',
            'simple_html_dom'        => METASLIDER_PATH . 'inc/simple_html_dom.php',
            'metaslider_notices'     => METASLIDER_PATH . 'admin/Notices.php',
            'metaslider_admin_pages' => METASLIDER_PATH . 'admin/Pages.php',
			'metaslider_slideshows'  => METASLIDER_PATH . 'admin/Slideshows/Slideshows.php',
			'metaslider_slide'  	 => METASLIDER_PATH . 'admin/Slideshows/slides/Slide.php',
			'metaslider_themes'  	 => METASLIDER_PATH . 'admin/Slideshows/Themes.php',
			'metaslider_image'  	 => METASLIDER_PATH . 'admin/Slideshows/Image.php',
            'metaslider_tour'        => METASLIDER_PATH . 'admin/Tour.php',
            'metaslider_gutenberg'   => METASLIDER_PATH . 'admin/Gutenberg.php'
        );
    }

    /**
     * Load required classes
     */
    private function includes() {
		require_once(METASLIDER_PATH . 'admin/lib/helpers.php');
		// require_once(METASLIDER_PATH . 'admin/lib/temporary.php');
        $autoload_is_disabled = defined( 'METASLIDER_AUTOLOAD_CLASSES' ) && METASLIDER_AUTOLOAD_CLASSES === false;
        if ( function_exists( "spl_autoload_register" ) && ! ( $autoload_is_disabled ) ) {

            // >= PHP 5.2 - Use auto loading
            if ( function_exists( "__autoload" ) ) {
                spl_autoload_register( "__autoload" );
            }

            spl_autoload_register( array( $this, 'autoload' ) );

        } else {

            // < PHP5.2 - Require all classes
            foreach ( $this->plugin_classes() as $id => $path ) {
                if ( is_readable( $path ) && ! class_exists( $id ) ) {
                    require_once( $path );
                }
            }

        }

    }
	
	/**
	 * Display a warning on the plugins page if a dependancy
	 * is missing or a conflict might exist.
	 *
	 * @return void
	 */
	public function check_dependencies() {
		// MetaSlider pro is active but pre 2.13.0 (2.13.0 includes its own notice system)
		$slug = metaslider_plugin_is_installed('ml-slider-pro');
		if (is_plugin_active($slug)) {
			$pro_data = get_file_data(trailingslashit(WP_PLUGIN_DIR) . $slug, array('Version' => 'Version'));
			$this->installed_pro_version = $pro_data['Version'];
			if ($this->installed_pro_version && version_compare($this->installed_pro_version, '2.13.0', '<')) {
				add_action('admin_notices', array($this, 'show_pro_is_outdated'), 10, 3);
			}
		}		
	}

	/**
	 * The warning message that is displayed
	 * 
	 * @return void
	 */
	public function show_pro_is_outdated() { 
		global $pagenow;
		$page = isset($_GET['page']) ? $_GET['page'] : '';
		if ('update-core.php' !== $pagenow && 'plugins.php' !== $pagenow && 'metaslider' !== $page) return;
		?>
		<div class='notice notice-error metaslider-pro-outdated-notice'>
			<p><?php printf(__('MetaSlider Pro is installed but is out of date. You may update it <a target="_blank" href="%s">here</a>. Learn more about this notice <a href="%s">here</a>', 'ml-slider'), self_admin_url('plugins.php'), 'https://www.metaslider.com/pro-is-installed-but-out-of-date')?></p>
		</div>
		<?php
	}

    /**
     * Autoload MetaSlider classes to reduce memory consumption
     *
     * @param  string $class Class name
     */
    public function autoload( $class ) {

        $classes = $this->plugin_classes();

        $class_name = strtolower( $class );

        if ( isset( $classes[$class_name] ) && is_readable( $classes[$class_name] ) ) {
            require_once( $classes[$class_name] );
        }

    }


    /**
     * Register the [metaslider] shortcode.
     */
    private function setup_shortcode() {

        add_shortcode( 'metaslider', array( $this, 'register_shortcode' ) );
        add_shortcode( 'ml-slider', array( $this, 'register_shortcode' ) ); // backwards compatibility

    }


    /**
     * Hook MetaSlider into WordPress
     */
    private function setup_actions() {
        add_action('admin_menu', array($this, 'register_admin_pages'), 9553);
        add_action('init', array($this, 'register_post_types'));
        add_action('init', array($this, 'register_taxonomy'));
        add_action('init', array($this, 'load_plugin_textdomain'));
        add_action('admin_footer', array($this, 'admin_footer'), 11);
        add_action('widgets_init', array($this, 'register_metaslider_widget'));

        add_action('admin_post_metaslider_switch_view', array($this, 'switch_view'));
        add_action('admin_post_metaslider_delete_slide', array($this, 'delete_slide'));
        add_action('admin_post_metaslider_delete_slider', array($this, 'delete_slider'));
        add_action('admin_post_metaslider_create_slider', array($this, 'create_slider'));
        add_action('admin_post_metaslider_update_slider', array($this, 'update_slider'));

        add_action('media_upload_vimeo', array($this, 'upgrade_to_pro_tab_vimeo'));
        add_action('media_upload_youtube', array($this, 'upgrade_to_pro_tab_youtube'));
        add_action('media_upload_post_feed', array($this, 'upgrade_to_pro_tab_post_feed'));
        add_action('media_upload_layer', array($this, 'upgrade_to_pro_tab_layer'));

        // TODO: Refactor to Slide class object
        add_action('wp_ajax_delete_slide', array($this, 'ajax_delete_slide'));
        add_action('wp_ajax_undelete_slide', array($this, 'ajax_undelete_slide'));

        // TODO: Make this work
        // register_activation_hook(plugin_basename(__FILE__), array($this, 'after_activation'));
    }


    /**
     * Hook MetaSlider into WordPress
     */
    private function setup_filters() {
        add_filter('media_upload_tabs', array($this, 'custom_media_upload_tab_name'), 998);
        add_filter('media_view_strings', array($this, 'custom_media_uploader_tabs'), 5);
        add_filter('media_buttons_context', array($this, 'insert_metaslider_button'));
        add_filter("plugin_row_meta", array($this, 'get_extra_meta_links'), 10, 4);
        add_action('admin_head', array($this, 'add_star_styles'));
        add_action('admin_head', array($this, 'add_tour_nonce_to_activation_page'));

        // html5 compatibility for stylesheets enqueued within <body>
        add_filter('style_loader_tag', array($this, 'add_property_attribute_to_stylesheet_links'), 11, 2);
    }


    /**
     * Register MetaSlider widget
     */
    public function register_metaslider_widget() {
        register_widget('MetaSlider_Widget');
    }


    /**
     * Register ML Slider post type
     */
    public function register_post_types() {

        $show_ui = false;

        $capability = apply_filters( 'metaslider_capability', 'edit_others_posts' );

        if ( is_admin() && current_user_can( $capability ) && ( isset($_GET['show_ui']) || defined("METASLIDER_DEBUG") && METASLIDER_DEBUG ) ) {
            $show_ui = true;
        }

        register_post_type( 'ml-slider', array(
                'query_var' => false,
                'rewrite' => false,
                'public' => false,
                'exclude_from_search' => true,
                'publicly_queryable' => false,
                'show_in_nav_menus' => false,
                'show_ui' => $show_ui,
                'labels' => array(
                    'name' => 'MetaSlider'
                )
            )
        );

        register_post_type( 'ml-slide', array(
                'query_var' => false,
                'rewrite' => false,
                'public' => false,
                'exclude_from_search' => true,
                'publicly_queryable' => false,
                'show_in_nav_menus' => false,
                'show_ui' => $show_ui,
                'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt'),
                'labels' => array(
                    'name' => 'Meta Slides'
                )
            )
        );

    }


    /**
     * Register taxonomy to store slider => slides relationship
     */
    public function register_taxonomy() {

        $show_ui = false;

        $capability = apply_filters( 'metaslider_capability', 'edit_others_posts' );

        if (is_admin() && current_user_can( $capability ) && ( isset($_GET['show_ui']) || defined("METASLIDER_DEBUG") && METASLIDER_DEBUG ) ) {
            $show_ui = true;
        }

        register_taxonomy( 'ml-slider', array('attachment', 'ml-slide'), array(
                'hierarchical' => true,
                'public' => false,
                'query_var' => false,
                'rewrite' => false,
                'show_ui' => $show_ui,
                'label' => "Slider"
            )
        );

    }


    /**
     * Register our slide types
     */
    private function register_slide_types() {

        $image = new MetaImageSlide();

    }

   /**
    * Add the menu pages
    */
    public function register_admin_pages() {
        if (metaslider_pro_is_active()) {
            $this->admin->add_page('MetaSlider Pro', 'metaslider');
        } else {
            $this->admin->add_page('MetaSlider');
        }

        if (metaslider_user_sees_upgrade_page()) {
            $this->admin->add_page(__('Add-ons', 'ml-slider'), 'upgrade-metaslider', 'metaslider');
        }   
    }
	
    /**
     * Shortcode used to display slideshow
     *
     * @param  string $atts attributes for short code
     * @return string HTML output of the shortcode
     */
    public function register_shortcode($atts) {
        extract(shortcode_atts(array(
			'id' => false,
			'title' => false,
			'restrict_to' => false,
			'theme' => null
		), $atts, 'metaslider'));
		
		// If no id and no title, exit here
		if (!$id && !$title) return false;

		// If there is a title, get the id from the title
        if ($title) {
			global $wpdb;

			// Run a custom query because get_page_by_title() includes "trash" posts
			// Also, be sure just to get 1 post, in case they have multiple
			$sql = $wpdb->prepare("
				SELECT ID
				FROM $wpdb->posts
				WHERE post_title = %s
				AND post_type = 'ml-slider'
				AND post_status = 'publish'
				LIMIT 1
			", $title);
			$id = $wpdb->get_var($sql);

			// If no posts were returned, $id will be NULL
			if (is_null($id) || !(bool) $title) return false;
		}

        // handle [metaslider id=123 restrict_to=home]
        if ($restrict_to && ('home' === $restrict_to) && !is_front_page()) return false;
        if ($restrict_to && ('home' !== $restrict_to) && !is_page($restrict_to)) return false;

		// Attempt to get the ml-slider post object
        $slider = get_post($id);

        // check the slideshow is published and the ID is correct
        if (!$slider || 'publish' !== $slider->post_status || 'ml-slider' !== $slider->post_type) {
            return "<!-- MetaSlider {$atts['id']} not found -->";
		}

		// Set up the slideshow and load the slideshow theme
        $this->set_slider($id, $atts);
		MetaSlider_Themes::get_instance()->load_theme($id, $theme);
		$this->slider->enqueue_scripts();
        return $this->slider->render_public_slides();
	}

    /**
     * Set first activation option to database
     */
    public function after_activation() {

        // Set date showing the first activation and redirect
        if (!get_option('ms_was_installed_on')) {
            update_option('ms_was_installed_on', time());
        }
    }

    /**
     * Initialise translations
     */
    public function load_plugin_textdomain() {

        load_plugin_textdomain( 'ml-slider', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    }

    /**
     * Check our WordPress installation is compatible with MetaSlider
     */
    public function do_system_check() {

        $systemCheck = new MetaSliderSystemCheck();
        $systemCheck->check();

    }


    /**
     * Update the tab options in the media manager
     *
     * @param  array $strings Array of settings for custom media tabs
     * @return array
     */
    public function custom_media_uploader_tabs( $strings ) {

        // update strings
        if ( ( isset( $_GET['page'] ) && $_GET['page'] == 'metaslider' ) ) {
            $strings['insertMediaTitle'] = __( "Image", "ml-slider" );
            $strings['insertIntoPost'] = __( "Add to slideshow", "ml-slider" );

            // remove options
            $strings_to_remove = array(
                'createVideoPlaylistTitle',
                'createGalleryTitle',
                'insertFromUrlTitle',
                'createPlaylistTitle'
            );

            foreach ($strings_to_remove as $string) {
                if (isset($strings[$string])) {
                    unset($strings[$string]);
                }
            }
        }

        return $strings;

    }


    /**
     * Add extra tabs to the default wordpress Media Manager iframe
     *
     * @param  array $tabs existing media manager tabs]
     * @return array
     */
    public function custom_media_upload_tab_name( $tabs ) {

        $metaslider_tabs = array( 'post_feed', 'layer', 'youtube', 'vimeo' );

        // restrict our tab changes to the MetaSlider plugin page
        if ( ( isset( $_GET['page'] ) && $_GET['page'] == 'metaslider' ) || ( isset( $_GET['tab'] ) && in_array( $_GET['tab'], $metaslider_tabs ) ) ) {
            $newtabs = array();

            if ( function_exists( 'is_plugin_active' ) && ! is_plugin_active( 'ml-slider-pro/ml-slider-pro.php' ) ) {
                $newtabs = array(
                    'post_feed' => __( "Post Feed", "metaslider" ),
                    'vimeo' => __( "Vimeo", "metaslider" ),
                    'youtube' => __( "YouTube", "metaslider" ),
                    'layer' => __( "Layer Slide", "metaslider" )
                );
            }

            if ( isset( $tabs['nextgen'] ) ) 
                unset( $tabs['nextgen'] );


            if ( is_array( $tabs ) ) {
                return array_merge( $tabs, $newtabs );
            } else {
                return $newtabs;
            }
            
        }

        return $tabs;


    }

    /**
     * Set the current slider
     *
     * @param int   $id                 ID for slider
     * @param array $shortcode_settings Settings for slider
     */
    public function set_slider( $id, $shortcode_settings = array() ) {

        $type = 'flex';

        if ( isset( $shortcode_settings['type'] ) ) {
            $type = $shortcode_settings['type'];
        } else if ( $settings = get_post_meta( $id, 'ml-slider_settings', true ) ) {
            if ( is_array( $settings ) && isset( $settings['type'] ) ) {
                $type = $settings['type'];
            }
        }

        if ( ! in_array( $type, array( 'flex', 'coin', 'nivo', 'responsive' ) ) ) {
            $type = 'flex';
        }

        $this->slider = $this->load_slider( $type, $id, $shortcode_settings );

    }


    /**
     * Create a new slider based on the sliders type setting
     *
     * @param  string $type               Type of slide
     * @param  int    $id                 ID of slide
     * @param  string $shortcode_settings Shortcode settings
     * @return array
     */
    private function load_slider( $type, $id, $shortcode_settings ) {

        switch ( $type ) {
            case( 'coin' ):
                return new MetaCoinSlider( $id, $shortcode_settings );
            case( 'flex' ):
                return new MetaFlexSlider( $id, $shortcode_settings );
            case( 'nivo' ):
                return new MetaNivoSlider( $id, $shortcode_settings );
            case( 'responsive' ):
                return new MetaResponsiveSlider( $id, $shortcode_settings );
            default:
                return new MetaFlexSlider( $id, $shortcode_settings );

        }
    }

	/**
	 * Update the slider
	 *
	 * @return string a JSON string with success or failure (and errors)
	 */
	public function update_slider() {

		// Wordpress will just die() if this fails
		check_admin_referer("metaslider_update_slider");

		if (!current_user_can(apply_filters('metaslider_capability', 'edit_others_posts'))) {
			return wp_send_json_error(array(
				'message' => __('The security check failed. Please refresh the page and try again.', 'ml-slider')
			), 401);
		}

		$slider_id = absint($_POST['slider_id']);
		if (!$slider_id) {
			return wp_send_json_error(array(
				'message' => __("The slideshow you're trying to update was not found.", 'ml-slider')
			), 401);
		}
		
		$errors = new WP_Error();

		// update settings
		if (isset($_POST['settings'])) {
			$new_settings = $_POST['settings'];
			$old_settings = get_post_meta($slider_id, 'ml-slider_settings', true);

            // convert submitted checkbox values from 'on' or 'off' to boolean values
            $checkboxes = apply_filters("metaslider_checkbox_settings", array('noConflict', 'fullWidth', 'hoverPause', 'links', 'reverse', 'random', 'printCss', 'printJs', 'smoothHeight', 'center', 'carouselMode', 'autoPlay', 'firstSlideFadeIn', 'responsive_thumbs'));

			foreach ($checkboxes as $checkbox) {
				$new_settings[$checkbox] = (isset($new_settings[$checkbox]) && 'on' == $new_settings[$checkbox]) ? 'true' : 'false';
			}

			$settings = array_merge((array) $old_settings, $new_settings);

			// update the slider settings
			// TODO this will return false if the data is unchanged, so we should consider checking the data prior to updating it.
			$result = update_post_meta($slider_id, 'ml-slider_settings', $settings);
			// if (!$result) $errors->add('update_failed', __('There was an error while updating the post meta. Most likely this means nothing has changed.', 'ml-slider'));
		}

		// update slideshow title
		if (isset($_POST['title'])) {
			$result = wp_update_post(array(
				'ID' => $slider_id,
				'post_title' => esc_html($_POST['title'])
			));
			if (!$result) $errors->add('update_failed', __('There was an error while updating the title.', 'ml-slider'));
		}

		// update individual slides
		// TODO Refactor this to return better error handling.
        if (isset($_POST['attachment'])) {
            foreach ($_POST['attachment'] as $slide_id => $fields) {
                do_action("metaslider_save_{$fields['type']}_slide", $slide_id, $slider_id, $fields);
            }
		}
		
		if (count($errors->get_error_messages())) {
			return wp_send_json_error($errors, 409);
		}

		return wp_send_json_success(array(
			'message' => __('The slideshow was successfully updated.', 'ml-slider'),
		), 200);
	}

    /**
     * Delete a slide via ajax.
     *
     * @return string Returns the status of the request
     */
    public function ajax_undelete_slide() {
        if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'metaslider_undelete_slide')) {
			return wp_send_json_error(array(
					'message' => __('The security check failed. Please refresh the page and try again.', 'ml-slider')
			), 401);
        }
        
        $result = $this->undelete_slide(absint($_POST['slide_id']), absint($_POST['slider_id']));
        
		if (is_wp_error($result)) {
			return wp_send_json_error(array(
					'message' => $result->get_error_message()
			), 409);
		}
		
		return wp_send_json_success(array(
            'message' => __('The slide was successfully restored', 'ml-slider'),
        ), 200);
    }

    /**
     * Undeletes a slide.
     *
     * @param int $slide_id  The ID of the slide
     * @param int $slider_id The ID of the slider (for legacy purposes)
     * @return mixed 
     */
    public function undelete_slide($slide_id, $slider_id) {
        if ('ml-slide' === get_post_type($slide_id)) {
            return wp_update_post(array(
                'ID' => $slide_id,
                'post_status' => 'publish'
            ), new WP_Error('update_failed', __('The attempt to restore the slide failed.', 'ml-slider'), array('status' => 409)));
        }
        
        /*
         * Legacy: This removes the relationship between the slider and slide
         * This restores the relationship between a slide and slider.
         * If using a newer version, this relationship is never lost on delete.
         */

        // Get the slider's term and apply it to the slide.
        $term = get_term_by('name', $slider_id, 'ml-slider');
        return wp_set_object_terms($slide_id, $term->term_id, 'ml-slider');
    }
    
    /**
     * Delete a slide via ajax.
     */
    public function ajax_delete_slide() {
        if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'metaslider_delete_slide')) {
			return wp_send_json_error(array(
					'message' => __('The security check failed. Please refresh the page and try again.', 'ml-slider')
			), 401);
        }
        
        $result = $this->delete_slide(absint($_POST['slide_id']), absint($_POST['slider_id']));
        
		if (is_wp_error($result)) {
			return wp_send_json_error(array(
					'message' => $result->get_error_message()
			), 409);
		}
		
		return wp_send_json_success(array(
            'message' => __('The slide was successfully trashed', 'ml-slider'),
        ), 200);
    }

    /**
     * Delete a slide by either trashing it or for 
     * legacy reasons removing the taxonomy relationship.
     *
     * @param int $slide_id  The ID of the slide
     * @param int $slider_id The ID of the slider
     * @return mixed Will return the terms or WP_Error
     */
    public function delete_slide($slide_id, $slider_id) {
        if ('ml-slide' === get_post_type($slide_id)) {
            return wp_update_post(array(
                'ID' => $slide_id,
                'post_status' => 'trash'
            ), new WP_Error('update_failed', 'The attempt to delete the slide failed.', array('status' => 409)));
        }
        
        /*
         * Legacy: This removes the relationship between the slider and slide
         * A slider with ID 216 might have a term_id of 7
         * A slide with ID 217 could have a term_taxonomy_id of 7
         * Multiple slides would have this term_taxonomy_id of 7
         */

        // This returns the term_taxonomy_id (7 from example)
        $current_terms = wp_get_object_terms($slide_id, 'ml-slider', array('fields' => 'ids'));
        
        // This returns the term object, named after the slider ID
        // The $term->term_id would be 7 in the example above
        // It also includes the count of slides attached to the slider
        $term = get_term_by('name', $slider_id, 'ml-slider');
        
        // I'm not sure why this is here. It seems this is only useful if
        // a slide was attached to multiple sliders. A slide should only
        // have one $current_terms (7 above)
        $new_terms = array();
        foreach ($current_terms as $current_term) {
            if ($current_term != $term->term_id) {
                $new_terms[] = absint($current_term);
            }
        }

        // This only works becasue $new_terms is an empty array, 
        // which deletes the relationship. I'm leaving the loop above
        // in case it's here for some legacy reason I'm unaware of.
        return wp_set_object_terms($slide_id, $new_terms, 'ml-slider');
    }


    /**
     * Delete a slider (send it to trash)
	 * 
	 * @deprecated 3.11.0 use the API
	 * 
	 * @return string - the json response from the API
     */
    public function delete_slider() {
		return $this->api->delete_slideshow($_REQUEST);
    }

    /**
     * Switch view
     *
     * @return null
     */
    public function switch_view() {
        global $user_ID;

        $view = $_GET['view'];

        $allowed_views = array('tabs', 'dropdown');

        if ( ! in_array( $view, $allowed_views ) ) {
            return;
        }

        delete_user_meta( $user_ID, "metaslider_view" );

        if ( $view == 'dropdown' ) {
            add_user_meta( $user_ID, "metaslider_view", "dropdown");
        }

        wp_redirect( admin_url( "admin.php?page=metaslider" ) );

    }


    /**
     * Create a new slider
     */
    public function create_slider() {

        // check nonce
        check_admin_referer( "metaslider_create_slider" );

        $capability = apply_filters( 'metaslider_capability', 'edit_others_posts' );

        if ( ! current_user_can( $capability ) ) {
            return;
        }

        $defaults = array();

        // if possible, take a copy of the last edited slider settings in place of default settings
        if ( $last_modified = $this->find_slider( 'modified', 'DESC' ) ) {
            $defaults = get_post_meta( $last_modified, 'ml-slider_settings', true );
        }

        // insert the post
        $id = wp_insert_post( array(
                'post_title' => __("New Slideshow", "ml-slider"),
                'post_status' => 'publish',
                'post_type' => 'ml-slider'
            )
        );

        // use the default settings if we can't find anything more suitable.
        if ( empty( $defaults ) ) {
            $slider = new MetaSlider( $id, array() );
            $defaults = $slider->get_default_parameters();
		}
		
		// Set a default slideshow theme
		// If there is a theme on the most recent
		if ($theme = get_post_meta($last_modified, 'metaslider_slideshow_theme', true));
		if (!isset($theme['folder'])) {
			$theme = $this->themes->get_theme_object(null, $this->themes->random());
		}
		
		if (isset($theme['folder'])) update_post_meta($id, 'metaslider_slideshow_theme', $theme);

        // insert the post meta
        add_post_meta( $id, 'ml-slider_settings', $defaults, true );

        // create the taxonomy term, the term is the ID of the slider itself
        wp_insert_term( $id, 'ml-slider' );

        wp_redirect( admin_url( "admin.php?page=metaslider&id={$id}" ) );

    }



    /**
     * Find a single slider ID. For example, last edited, or first published.
     *
     * @param string $orderby field to order.
     * @param string $order   direction (ASC or DESC).
     * @return int slider ID.
     */
    private function find_slider( $orderby, $order ) {

        $args = array(
            'force_no_custom_order' => true,
            'post_type' => 'ml-slider',
            'num_posts' => 1,
            'post_status' => 'publish',
            'suppress_filters' => 1, // wpml, ignore language filter
            'orderby' => $orderby,
            'order' => $order
        );

        $the_query = new WP_Query( $args );

        while ( $the_query->have_posts() ) {
            $the_query->the_post();
            return $the_query->post->ID;
        }

        wp_reset_query();

        return false;

    }


    /**
     * Get sliders. Returns a nicely formatted array of currently
     * published sliders.
     *
     * @param string $sort_key Specified sort key
     * @return array all published sliders
     */
    public function all_meta_sliders( $sort_key = 'date' ) {

        $sliders = array();

        // list the tabs
        $args = array(
            'post_type' => 'ml-slider',
            'post_status' => 'publish',
            'orderby' => $sort_key,
            'suppress_filters' => 1, // wpml, ignore language filter
            'order' => 'ASC',
            'posts_per_page' => -1
        );

        $args = apply_filters( 'metaslider_all_meta_sliders_args', $args );

        // WP_Query causes issues with other plugins using admin_footer to insert scripts
        // use get_posts instead
        $all_sliders = get_posts( $args );

        foreach( $all_sliders as $slideshow ) {

            $active = $this->slider && ( $this->slider->id == $slideshow->ID ) ? true : false;

            $sliders[] = array(
                'active' => $active,
                'title' => $slideshow->post_title,
                'id' => $slideshow->ID
            );

        }

        return $sliders;

    }


    /**
     * Compare array values
     *
     * @param array $elem1 The first element to comapre
     * @param array $elem2 The second element to comapr
     * @return bool
     */
    private function compare_elems( $elem1, $elem2 ) {

        return $elem1['priority'] > $elem2['priority'];

    }


    /**
     * Building setting rows
     *
     * @param  array $settings array of fields to render
     * @return string
     */
    public function build_settings_rows($settings) {

        // order the fields by priority
        uasort($settings, array($this, "compare_elems"));
        $output = "";

        // loop through the array and build the settings HTML
        foreach ($settings as $id => $row) {
            $helptext = isset($row['helptext']) ? htmlentities2($row['helptext']) : '';

            switch ($row['type']) {

                // checkbox input type
                case 'checkbox':
                    $output .= "<tr><td class='tipsy-tooltip' title='{$helptext}'>{$row['label']}</td><td><input class='option {$row['class']} {$id}' type='checkbox' name='settings[{$id}]' {$row['checked']} />";
                    if (isset($row['after'])) {
                        $output .= "<span class='after'>{$row['after']}</span>";
                    }
                    $output .= "</td></tr>";
                    break;

                // navigation row
                case 'navigation':
                    $navigation_row = "<tr class='{$row['type']}'><td class='tipsy-tooltip' title='{$helptext}'>{$row['label']}</td><td><ul>";
                    foreach ($row['options'] as $option_name => $option_value) {
                        $tease = isset($option_value['addon_required']) ? 'disabled' : '';
                        if (true === $row['value'] && 'true' === $option_name) {
                            $checked = checked(true, true, false);
                        } else if (false === $row['value'] && 'false' === $option_name) {
                            $checked = checked(true, true, false);
                        } else {
                            $checked = checked($option_name, $row['value'], false);
                        }
                        $disabled = $option_name == 'thumbnails' ? 'disabled' : '';
                        $navigation_row .= "<li><label class='{$tease}'><input {$tease} type='radio' name='settings[{$id}]' value='{$option_name}' {$checked} {$disabled}/>{$option_value['label']}</label>";
                        if (isset($option_value['addon_required']) && $option_value['addon_required']) {
                            $navigation_row .= sprintf(" <a target='_blank' class='get-addon' href='%s' title='%s'>%s</a>", metaslider_get_upgrade_link(), __('Get the Add-on Pack today!', 'ml-slider'), __('Learn More', 'ml-slider'));
                        }
                        $navigation_row .= "</li>";
                    }
                    $navigation_row .= "</ul></td></tr>";
                    $output .= apply_filters('metaslider_navigation_options', $navigation_row, $this->slider);
                    break;

                // navigation row
                case 'radio':
                    $navigation_row = "<tr class='{$row['type']}'><td class='tipsy-tooltip' title='{$helptext}'>{$row['label']}</td><td><ul>";
                    foreach ($row['options'] as $option_name => $option_value) {
                        $checked = checked($option_name, $row['value'], false);
                        $class = isset($option_value['class']) ? $option_value['class'] : "";
                        $navigation_row .= "<li><label><input type='radio' name='settings[{$id}]' value='{$option_name}' {$checked} class='radio {$class}'/>{$option_value['label']}</label></li>";
                    }
                    $navigation_row .= "</ul></td></tr>";
                    $output .= apply_filters('metaslider_navigation_options', $navigation_row, $this->slider);
                    break;

                // header/divider row
                case 'divider':
                    $output .= "<tr class='{$row['type']}'><td colspan='2' class='divider'><b>{$row['value']}</b></td></tr>";
                    break;

                // slideshow select row
                case 'slider-lib':
                    $output .= "<tr class='{$row['type']}'><td colspan='2' class='slider-lib-row'>";
                    foreach ($row['options'] as $option_name => $option_value) {
                        $checked = checked($option_name, $row['value'], false);
                        $output .= "<input class='select-slider' id='{$option_name}' rel='{$option_name}' type='radio' name='settings[type]' value='{$option_name}' {$checked} />
                        <label tabindex='0' for='{$option_name}'>{$option_value['label']}</label>";
                    }
                    $output .= "</td></tr>";
                    break;

                // number input type
                case 'number':
                    $output .= "<tr class='{$row['type']}'><td class='tipsy-tooltip' title='{$helptext}'>{$row['label']}</td><td><input class='option {$row['class']} {$id}' type='number' min='{$row['min']}' max='{$row['max']}' step='{$row['step']}' name='settings[{$id}]' value='" . absint($row['value']) . "' /><span class='after'>{$row['after']}</span></td></tr>";
                    break;

                // select drop down
                case 'select':
                    $output .= "<tr class='{$row['type']}'><td class='tipsy-tooltip' title='{$helptext}'>{$row['label']}</td><td><select class='option {$row['class']} {$id}' name='settings[{$id}]'>";
                    foreach ($row['options'] as $option_name => $option_value) {
                        $selected = selected($option_name, $row['value'], false);
                        $output .= "<option class='{$option_value['class']}' value='{$option_name}' {$selected}>{$option_value['label']}</option>";
                    }
                    $output .= "</select></td></tr>";
                    break;

                // theme drop down
                case 'theme':
                    $output .= "<tr class='{$row['type']}'><td class='tipsy-tooltip' title='{$helptext}'>{$row['label']}</td><td><select class='option {$row['class']} {$id}' name='settings[{$id}]'>";
                    $themes = "";
                    foreach ($row['options'] as $option_name => $option_value) {
                        $selected = selected($option_name, $row['value'], false);
                        $themes .= "<option class='{$option_value['class']}' value='{$option_name}' {$selected}>{$option_value['label']}</option>";
                    }
                    $output .= apply_filters('metaslider_get_available_themes', $themes, $this->slider->get_setting('theme'));
                    $output .= "</select></td></tr>";
                    break;

                // text input type
                case 'text':
                    $output .= "<tr class='{$row['type']}'><td class='tipsy-tooltip' title='{$helptext}'>{$row['label']}</td><td><input class='option {$row['class']} {$id}' type='text' name='settings[{$id}]' value='" . esc_attr($row['value']) . "' /></td></tr>";
                    break;

                // text input type
                case 'textarea':
                    $output .= "<tr class='{$row['type']}'><td class='tipsy-tooltip' title='{$helptext}' colspan='2'>{$row['label']}</td></tr><tr><td colspan='2'><textarea class='option {$row['class']} {$id}' name='settings[{$id}]' />{$row['value']}</textarea></td></tr>";
                    break;

                // text input type
                case 'title':
                    $output .= "<tr class='{$row['type']}'><td class='tipsy-tooltip' title='{$helptext}'>{$row['label']}</td><td><input class='option {$row['class']} {$id}' type='text' name='{$id}' value='" . esc_attr( $row['value']) . "' /></td></tr>";
                    break;
            }
        }
        return $output;
    }


    /**
     * Return an indexed array of all easing options
     *
     * @return array
     */
    private function get_easing_options() {

        $options = array(
            'linear', 'swing', 'jswing', 'easeInQuad', 'easeOutQuad', 'easeInOutQuad',
            'easeInCubic', 'easeOutCubic', 'easeInOutCubic', 'easeInQuart',
            'easeOutQuart', 'easeInOutQuart', 'easeInQuint', 'easeOutQuint',
            'easeInOutQuint', 'easeInSine', 'easeOutSine', 'easeInOutSine',
            'easeInExpo', 'easeOutExpo', 'easeInOutExpo', 'easeInCirc', 'easeOutCirc',
            'easeInOutCirc', 'easeInElastic', 'easeOutElastic', 'easeInOutElastic',
            'easeInBack', 'easeOutBack', 'easeInOutBack', 'easeInBounce', 'easeOutBounce',
            'easeInOutBounce'
        );
        $output = array();

        foreach ( $options as $option) {
            $output[$option] = array(
                'label' => ucfirst( preg_replace( '/(\w+)([A-Z])/U', '\\1 \\2', $option)),
                'class' => ''
            );
        }

        return $output;

    }

    /**
     * Output the slideshow selector.
     *
     * Show tabs or a dropdown list depending on the users saved preference.
     */
    public function print_slideshow_selector() {
        global $user_ID;

        // First check if we have any slideshows yet
        if ($tabs = $this->all_meta_sliders()) {

            // Next check if they have the tabs view selected
            if ('tabs' == $this->get_view()) {

                // Render the tabs
				echo "<div class='nav-tab-wrapper'>";
					echo "<div class='nav-tabs'>";
						foreach ($tabs as $tab) {
							if (!isset($tab['title'])) continue;
							$active = isset($tab['active']) && filter_var($tab['active'], FILTER_VALIDATE_BOOLEAN);
							$title = esc_attr($tab['title']);
							$title_with_html_allowed = esc_html($tab['title']);
							if ($active) {
								echo "<div class='nav-tab nav-tab-active'><input class='no_last_pass' type='text' name='title'  value='{$title}'></div>";
							} else {
								echo "<a href='?page=metaslider&amp;id={$tab['id']}' title= '{$title}' class='nav-tab'>{$title_with_html_allowed}</a>";
							}
						}
					echo "</div>";

					// TODO: Update this button and the entire nav system to a vuejs component
					if ($button = $this->toggle_layout_button()) echo $button;
				echo "</div>";

            // This will render the select dropdown view
            // TODO make this resemble the WP Nav menu UI perhaps
            } else {
				echo "<div class='manage-menus'>";
					echo "<label for='select-slideshow' class='selected-menu'>" . __("Select Slideshow", "ml-slider") . ": </label>";
					echo "<select name='select-slideshow' onchange='if (this.value) window.location.href=this.value'>";

					$tabs = $this->all_meta_sliders('title');
					foreach ($tabs as $tab) {
						if (!isset($tab['title'])) continue;
						$active = isset($tab['active']) && filter_var($tab['active'], FILTER_VALIDATE_BOOLEAN);
						$selected = $active ? " selected" : "";
						$title = esc_attr($tab['title']);
						echo "<option value='?page=metaslider&amp;id={$tab['id']}'{$selected}>{$title}</option>";
					}
					echo "</select>";
					
					// TODO: Update this button and the entire nav system to a vuejs component
					if ($button = $this->toggle_layout_button()) echo $button;
				echo "</div>";
            }

        // This section is shown when there are no slideshows
        } else {
            include METASLIDER_PATH."admin/views/pages/start.php";
        }
    }

    /**
     * Return the users saved view preference.
     */
    public function get_view() {
        global $user_ID;

        if ( get_user_meta( $user_ID, "metaslider_view", true ) ) {
            return get_user_meta( $user_ID, "metaslider_view", true );
        }

        return 'tabs';
    }


    /**
     * Render the admin page (tabs, slides, settings)
     */
    public function render_admin_page() {

        // Default to the most recently modified slider
        $slider_id = $this->find_slider('modified', 'DESC');

        // If the id parameter exists, verify and use that. 
        if (isset($_REQUEST['id']) && $id = $_REQUEST['id']) {
            if (in_array(get_post_status(absint($id)), array('publish', 'inherit'))) {
                $slider_id = (int)$id;
            }
        }

        // "Set the slider"
        // TODO figure out what this does and if it can be better stated
        // Perhaps maybe "apply_settings()" or something.
        if ($slider_id) {
            $this->set_slider($slider_id);
        }
        
        $this->do_system_check();

        $slider_id = $this->slider ? $this->slider->id : 0;

        ?>

        <script>
            var metaslider_slider_id = <?php echo $slider_id; ?>;
        </script>

        <div id="metaslider-ui" class="metaslider metaslider-ui">
		<?php $slider_settings = get_post_meta($slider_id, 'ml-slider_settings', true); ?>
		<metaslider	:id='<?php echo $slider_id; ?>' v-bind:settings='<?php echo json_encode($slider_settings);?>' inline-template>
			<div>
				
				<?php include METASLIDER_PATH."admin/views/pages/parts/header.php"; ?>

            <form accept-charset="UTF-8" action="<?php echo admin_url( 'admin-post.php'); ?>" method="post">
                <input type="hidden" name="action" value="metaslider_update_slider">
                <input type="hidden" name="slider_id" value="<?php echo $slider_id; ?>">
                <?php wp_nonce_field( 'metaslider_update_slider' ); ?>

                <?php $this->print_slideshow_selector(); ?>

                <?php // If there is no slideshow we don't need to show the rest
                    if (!$this->slider) { echo '</form></div></metaslider>'; return false; } ?>
				
				<div id='poststuff' class="metaslider-inner wp-clearfix">
                    <div id='post-body' class='metabox-holder columns-2'>

                        <div id='post-body-content'>
                            <div class="left">

                                <?php do_action( "metaslider_admin_table_before", $this->slider->id ); ?>

                                <table id="metaslider-slides-list" class="widefat sortable metaslider-slides-container">
                                    <thead>
                                        <tr>
                                            <?php if (metaslider_viewing_trashed_slides($this->slider->id)) { 
                                                
                                                // If they are on the trash page, show them?>
                                                <th class="trashed-header">
                                                    <h3><i><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></i> <?php _e('Trashed Slides', 'ml-slider'); ?></h3>
                                                    <small> <?php printf(__('<a href="%s">view active</a>', 'ml-slider'), admin_url("?page=metaslider&id={$this->slider->id}")); ?></small>
                                                </th>
                                            <?php } else { ?>
                                                <th class="slider-title" colspan="2">
                                                <h3 class="alignleft"><?php echo get_the_title($this->slider->id) ?></h3>
                                                <?php if (!metaslider_viewing_trashed_slides($this->slider->id)) { 
                                                    
                                                    // Remove the actions on trashed view?>
                                                    <button class='ml-button ml-has-icon ml-skinless-button alignright add-slide' data-editor='content' title='<?php _e( "Add a New Slide", "ml-slider" ) ?>'>
                                                        <i style="top:0;"><svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg></i>
                                                        <span><?php _e("Add Slide", "ml-slider") ?></span>
                                                    </button>
                                                <?php } ?>
                                                    <?php do_action( "metaslider_admin_table_header_right", $this->slider->id ); ?>
                                                </th>
                                            <?php } ?>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                            $this->slider->render_admin_slides();
                                        ?>
                                    </tbody>
                                </table>

                                <?php do_action( "metaslider_admin_table_after", $this->slider->id ); ?>

                            </div>
                        </div>

                        <div id="postbox-container-1" class="postbox-container ml-sidebar metaslider-settings-area">
                            <div class='right'>
                            <?php if (metaslider_viewing_trashed_slides($this->slider->id)) { 
                                
                                // Show a notice explaining the trash?>
                                <div class="ms-postbox trashed-notice">
                                    <div class="notice-info"><?php printf(__('You are viewing slides that have been trashed, which will be automatically deleted in %s days. Click <a href="%s">here</a> to view active slides.', 'ml-slider'), EMPTY_TRASH_DAYS, admin_url("?page=metaslider&id={$this->slider->id}")); ?></div>

                                    <?php 
                                        // TODO this is a temp fix to avoid a compatability check in pro
                                        echo "<input type='checkbox' style='display:none;' checked class='select-slider' rel='flex'></inpu>";
                                    ?>
                                </div>
							<?php } else {?>
                                <div class="ms-postbox" id="metaslider_configuration">
									<div class='configuration metaslider-actions'>
									<?php $theme = metaslider_themes::get_instance()->get_current_theme($this->slider->id); ?>
										<metaslider-preview
											slideshow-id="<?php echo $this->slider->id; ?>"
											:keyboard-control="[18, 80]"
											theme-identifier="<?php echo $theme['folder']; ?>"
										></metaslider-preview>
                                        <button class='alignright button button-primary' type='submit' name='save' id='ms-save'>
                                            <?php _e("Save", "ml-slider"); ?>
                                        </button>
                                        <span class="spinner"></span>
                                    </div>
                                    <div class="inside wp-clearfix">
                                        <table class="settings">
                                            <tbody>
                                                <?php
                                                    $aFields = array(
                                                        'type' => array(
                                                            'priority' => 0,
                                                            'type' => 'slider-lib',
                                                            'value' => $this->slider->get_setting('type'),
                                                            'options' => array(
                                                                'flex' => array('label' => "FlexSlider"),
                                                                'responsive' => array('label' => "R. Slides"),
                                                                'nivo' => array('label' => "Nivo Slider"),
                                                                'coin' => array('label' => "Coin Slider")
                                                            )
                                                        ),
                                                        'width' => array(
                                                            'priority' => 10,
                                                            'type' => 'number',
                                                            'size' => 3,
                                                            'min' => 0,
                                                            'max' => 9999,
                                                            'step' => 1,
                                                            'value' => $this->slider->get_setting( 'width' ),
                                                            'label' => __( "Width", "ml-slider" ),
                                                            'class' => 'coin flex responsive nivo',
                                                            'helptext' => __( "Slideshow width", "ml-slider" ),
                                                            'after' => __( "px", "ml-slider" )
                                                        ),
                                                        'height' => array(
                                                            'priority' => 20,
                                                            'type' => 'number',
                                                            'size' => 3,
                                                            'min' => 0,
                                                            'max' => 9999,
                                                            'step' => 1,
                                                            'value' => $this->slider->get_setting( 'height' ),
                                                            'label' => __( "Height", "ml-slider" ),
                                                            'class' => 'coin flex responsive nivo',
                                                            'helptext' => __( "Slideshow height", "ml-slider" ),
                                                            'after' => __( "px", "ml-slider" )
                                                        ),
                                                        'effect' => array(
                                                            'priority' => 30,
                                                            'type' => 'select',
                                                            'value' => $this->slider->get_setting( 'effect' ),
                                                            'label' => __( "Effect", "ml-slider" ),
                                                            'class' => 'effect coin flex responsive nivo',
                                                            'helptext' => __( "Slide transition effect", "ml-slider" ),
                                                            'options' => array(
                                                                'random'             => array( 'class' => 'option coin nivo' , 'label' => __( "Random", "ml-slider" ) ),
                                                                'swirl'              => array( 'class' => 'option coin', 'label' => __( "Swirl", "ml-slider" ) ),
                                                                'rain'               => array( 'class' => 'option coin', 'label' => __( "Rain", "ml-slider" ) ),
                                                                'straight'           => array( 'class' => 'option coin', 'label' => __( "Straight", "ml-slider" ) ),
                                                                'sliceDown'          => array( 'class' => 'option nivo', 'label' => __( "Slide Down", "ml-slider" ) ),
                                                                'sliceUp'            => array( 'class' => 'option nivo', 'label' => __( "Slice Up", "ml-slider" ) ),
                                                                'sliceUpLeft'        => array( 'class' => 'option nivo', 'label' => __( "Slide Up Left", "ml-slider" ) ),
                                                                'sliceUpDown'        => array( 'class' => 'option nivo', 'label' => __( "Slice Up Down", "ml-slider" ) ),
                                                                'slideUpDownLeft'    => array( 'class' => 'option nivo', 'label' => __( "Slide Up Down Left", "ml-slider" ) ),
                                                                'fold'               => array( 'class' => 'option nivo', 'label' => __( "Fold", "ml-slider" ) ),
                                                                'fade'               => array( 'class' => 'option nivo flex responsive', 'label' => __( "Fade", "ml-slider" ) ),
                                                                'slideInRight'       => array( 'class' => 'option nivo', 'label' => __( "Slide In Right", "ml-slider" ) ),
                                                                'slideInLeft'        => array( 'class' => 'option nivo', 'label' => __( "Slide In Left", "ml-slider" ) ),
                                                                'boxRandom'          => array( 'class' => 'option nivo', 'label' => __( "Box Random", "ml-slider" ) ),
                                                                'boxRain'            => array( 'class' => 'option nivo', 'label' => __( "Box Rain", "ml-slider" ) ),
                                                                'boxRainReverse'     => array( 'class' => 'option nivo', 'label' => __( "Box Rain Reverse", "ml-slider" ) ),
                                                                'boxRainGrowReverse' => array( 'class' => 'option nivo', 'label' => __( "Box Rain Grow Reverse", "ml-slider" ) ),
                                                                'slide'              => array( 'class' => 'option flex', 'label' => __( "Slide", "ml-slider" ) )
                                                            ),
                                                        ),
                                                        'links' => array(
                                                            'priority' => 50,
                                                            'type' => 'checkbox',
                                                            'label' => __( "Arrows", "ml-slider" ),
                                                            'class' => 'option coin flex nivo responsive',
                                                            'checked' => $this->slider->get_setting( 'links' ) == 'true' ? 'checked' : '',
                                                            'helptext' => __( "Show the previous/next arrows", "ml-slider" )
                                                        ),
                                                        'navigation' => array(
                                                            'priority' => 60,
                                                            'type' => 'navigation',
                                                            'label' => __("Navigation", "ml-slider"),
                                                            'class' => 'option coin flex nivo responsive',
                                                            'value' => $this->slider->get_setting('navigation'),
                                                            'helptext' => __("Show the slide navigation bullets", "ml-slider"),
                                                            'options' => array(
                                                                'false' => array('label' => __("Hidden", "ml-slider")),
                                                                'true' => array('label' => __("Dots", "ml-slider")),
                                                                'thumbs' => array(
                                                                    'label' => __("Thumbnail", "ml-slider"),
                                                                    'addon_required' => true
                                                                ),
                                                                'filmstrip' => array(
                                                                    'label' => __("Filmstrip", "ml-slider"),
                                                                    'addon_required' => true
                                                                ),
                                                            )
                                                        ),
                                                    );
                        
                                                    if ( $this->get_view() == 'dropdown' ) {
                                                        $aFields['title'] = array(
                                                            'type' => 'title',
                                                            'priority' => 5,
                                                            'class' => 'option flex nivo responsive coin',
                                                            'value' => get_the_title($this->slider->id),
                                                            'label' => __( "Title", "ml-slider" ),
                                                            'helptext' => __( "Slideshow title", "ml-slider" )
                                                        );
                                                    }

                                                    $aFields = apply_filters( 'metaslider_basic_settings', $aFields, $this->slider );

                                                    echo $this->build_settings_rows( $aFields );
                                                ?>
                                            </tbody>
                                        </table>

                                        
                                        <?php 
                                        // Show the restore button if there are trashed posts
                                        // Also, render but hide the link in case we want to show
                                        // it when the user deletes their first slide
                                        $count = count(metaslider_has_trashed_slides($this->slider->id));
                                        if (!metaslider_viewing_trashed_slides($this->slider->id)) { ?>
                                            <a <?php echo $count ? '' : "style='display:none;'" ?> class="restore-slide-link" title="<?php _e('View trashed slides', 'ml-slider'); ?>" href="<?php echo admin_url("?page=metaslider&id={$this->slider->id}&show_trashed=true"); ?>">
                                                <i><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></i><?php echo __('Trash', 'ml-slider'); if ($count) echo " <span>({$count})</span>";?>
                                            </a>
                                        <?php } ?>
                                    </div>
								</div><?php
                                $theme = metaslider_themes::get_instance()->get_current_theme($this->slider->id);
								if (is_array($theme)) unset($theme['images']);
								$theme_error = is_wp_error($theme) ? $theme->get_error_message() : '';
								if (is_wp_error($theme)) $theme = false;
                                ?>
								<metaslider-theme-viewer
									theme-directory-url="<?php echo METASLIDER_THEMES_URL; ?>"
									incoming-error-message="<?php echo $theme_error; ?>"
									v-bind:initial-theme='<?php echo json_encode($theme); ?>'>
								</metaslider-theme-viewer>
                                <div class="ms-postbox">
									<?php echo $this->shortcode_tip(); ?>
                                </div>
                                <div class="ms-postbox ms-toggle closed" id="metaslider_advanced_settings">
                                    <div class="handlediv" title="<?php esc_attr_e('Click to toggle', 'ml-slider'); ?>"></div><h3 class="hndle"><span><?php _e("Advanced Settings", "ml-slider") ?></span></h3>
                                    <div class="inside">
                                        <table>
                                            <tbody>
                                                <?php
                                                    $aFields = array(
                                                        'fullWidth' => array(
                                                            'priority' => 5,
                                                            'type' => 'checkbox',
                                                            'label' => __( "Stretch", "ml-slider" ),
                                                            'class' => 'option flex nivo responsive',
                                                            'after' => __( "100% wide output", "ml-slider" ),
                                                            'checked' => $this->slider->get_setting( 'fullWidth' ) == 'true' ? 'checked' : '',
                                                            'helptext' => __( "Stretch the slideshow output to fill it's parent container", "ml-slider" )
                                                        ),
                                                        'center' => array(
                                                            'priority' => 10,
                                                            'type' => 'checkbox',
                                                            'label' => __( "Center align", "ml-slider" ),
                                                            'class' => 'option coin flex nivo responsive',
                                                            'checked' => $this->slider->get_setting( 'center' ) == 'true' ? 'checked' : '',
                                                            'helptext' => __( "Center align the slideshow", "ml-slider" )
                                                        ),
														'autoPlay' => array(
															'priority' => 20,
															'type' => 'checkbox',
															'label' => __("Auto play", "ml-slider"),
															'class' => 'option flex nivo responsive coin',
															'checked' => 'true' == $this->slider->get_setting('autoPlay') ? 'checked' : '',
															'helptext' => __("Transition between slides automatically", "ml-slider")
														),
                                                        'smartCrop' => array(
                                                            'priority' => 30,
                                                            'type' => 'select',
                                                            'label' => __( "Image Crop", "ml-slider" ),
                                                            'class' => 'option coin flex nivo responsive',
                                                            'value' => $this->slider->get_setting( 'smartCrop' ),
                                                            'options' => array(
                                                                'true' => array( 'label' => __( "Smart Crop", "ml-slider" ), 'class' => '' ),
                                                                'false' => array( 'label' => __( "Standard", "ml-slider" ), 'class' => '' ),
                                                                'disabled' => array( 'label' => __( "Disabled", "ml-slider" ), 'class' => '' ),
                                                                'disabled_pad' => array( 'label' => __( "Disabled (Smart Pad)", "ml-slider" ), 'class' => 'option flex' ),
                                                            ),
                                                            'helptext' => __( "Smart Crop ensures your responsive slides are cropped to a ratio that results in a consistent slideshow size", "ml-slider" )
                                                        ),
                                                        'carouselMode' => array(
                                                            'priority' => 40,
                                                            'type' => 'checkbox',
                                                            'label' => __( "Carousel mode", "ml-slider" ),
                                                            'class' => 'option flex showNextWhenChecked',
                                                            'checked' => $this->slider->get_setting( 'carouselMode' ) == 'true' ? 'checked' : '',
                                                            'helptext' => __( "Display multiple slides at once. Slideshow output will be 100% wide.", "ml-slider" )
                                                        ),
                                                        'carouselMargin' => array(
                                                            'priority' => 45,
                                                            'min' => 0,
                                                            'max' => 9999,
                                                            'step' => 1,
                                                            'type' => 'number',
                                                            'label' => __( "Carousel margin", "ml-slider" ),
                                                            'class' => 'option flex',
                                                            'value' => $this->slider->get_setting( 'carouselMargin' ),
                                                            'helptext' => __( "Pixel margin between slides in carousel.", "ml-slider" ),
                                                            'after' => __( "px", "ml-slider" )
                                                        ),
                                                        'firstSlideFadeIn' => array(
                                                            'priority' => 47,
                                                            'type' => 'checkbox',
                                                            'label' => __("Fade in", "ml-slider"),
                                                            'class' => 'option flex',
                                                            'checked' => 'true' == $this->slider->get_setting('firstSlideFadeIn') ? 'checked' : '',
                                                            'helptext' => __("Fade in the first slide", "ml-slider"),
                                                        ),
                                                        'random' => array(
                                                            'priority' => 50,
                                                            'type' => 'checkbox',
                                                            'label' => __( "Random", "ml-slider" ),
                                                            'class' => 'option coin flex nivo responsive',
                                                            'checked' => $this->slider->get_setting( 'random' ) == 'true' ? 'checked' : '',
                                                            'helptext' => __( "Randomise the order of the slides", "ml-slider" )
                                                        ),
                                                        'hoverPause' => array(
                                                            'priority' => 60,
                                                            'type' => 'checkbox',
                                                            'label' => __( "Hover pause", "ml-slider" ),
                                                            'class' => 'option coin flex nivo responsive',
                                                            'checked' => $this->slider->get_setting( 'hoverPause' ) == 'true' ? 'checked' : '',
                                                            'helptext' => __( "Pause the slideshow when hovering over slider, then resume when no longer hovering.", "ml-slider" )
                                                        ),
                                                        'reverse' => array(
                                                            'priority' => 70,
                                                            'type' => 'checkbox',
                                                            'label' => __( "Reverse", "ml-slider" ),
                                                            'class' => 'option flex',
                                                            'checked' => $this->slider->get_setting( 'reverse' ) == 'true' ? 'checked' : '',
                                                            'helptext' => __( "Reverse the animation direction", "ml-slider" )
                                                        ),
                                                        'delay' => array(
                                                            'priority' => 80,
                                                            'type' => 'number',
                                                            'size' => 3,
                                                            'min' => 500,
                                                            'max' => 10000,
                                                            'step' => 100,
                                                            'value' => $this->slider->get_setting( 'delay' ),
                                                            'label' => __( "Slide delay", "ml-slider" ),
                                                            'class' => 'option coin flex responsive nivo',
                                                            'helptext' => __( "How long to display each slide, in milliseconds", "ml-slider" ),
                                                            'after' => _x("ms", "Short for milliseconds", "ml-slider")
                                                        ),
                                                        'animationSpeed' => array(
                                                            'priority' => 90,
                                                            'type' => 'number',
                                                            'size' => 3,
                                                            'min' => 0,
                                                            'max' => 2000,
                                                            'step' => 100,
                                                            'value' => $this->slider->get_setting( 'animationSpeed' ),
                                                            'label' => __( "Animation speed", "ml-slider" ),
                                                            'class' => 'option flex responsive nivo',
                                                            'helptext' => __( "Set the speed of animations, in milliseconds", "ml-slider" ),
                                                            'after' => _x("ms", "Short for milliseconds", "ml-slider")
                                                        ),
                                                        'slices' => array(
                                                            'priority' => 100,
                                                            'type' => 'number',
                                                            'size' => 3,
                                                            'min' => 0,
                                                            'max' => 20,
                                                            'step' => 1,
                                                            'value' => $this->slider->get_setting( 'slices' ),
                                                            'label' => __( "Number of slices", "ml-slider" ),
                                                            'class' => 'option nivo',
                                                            'helptext' => __( "Number of slices", "ml-slider" ),
                                                            'after' => _x("ms", "Short for milliseconds", "ml-slider")
                                                        ),
                                                        'spw' => array(
                                                            'priority' => 110,
                                                            'type' => 'number',
                                                            'size' => 3,
                                                            'min' => 0,
                                                            'max' => 20,
                                                            'step' => 1,
                                                            'value' => $this->slider->get_setting( 'spw' ),
                                                            'label' => __( "Number of squares", "ml-slider" ) . " (" . __( "Width", "ml-slider" ) . ")",
                                                            'class' => 'option nivo',
                                                            'helptext' => __( "Number of squares", "ml-slider" ),
                                                            'after' => ''
                                                        ),
                                                        'sph' => array(
                                                            'priority' => 120,
                                                            'type' => 'number',
                                                            'size' => 3,
                                                            'min' => 0,
                                                            'max' => 20,
                                                            'step' => 1,
                                                            'value' => $this->slider->get_setting( 'sph' ),
                                                            'label' => __( "Number of squares", "ml-slider" ) . " (" . __( "Height", "ml-slider" ) . ")",
                                                            'class' => 'option nivo',
                                                            'helptext' => __( "Number of squares", "ml-slider" ),
                                                            'after' => ''
                                                        ),
                                                        'direction' => array(
                                                            'priority' => 130,
                                                            'type' => 'select',
                                                            'label' => __( "Slide direction", "ml-slider" ),
                                                            'class' => 'option flex',
                                                            'helptext' => __( "Select the sliding direction", "ml-slider" ),
                                                            'value' => $this->slider->get_setting( 'direction' ),
                                                            'options' => array(
                                                                'horizontal' => array( 'label' => __( "Horizontal", "ml-slider" ), 'class' => '' ),
                                                                'vertical' => array( 'label' => __( "Vertical", "ml-slider" ), 'class' => '' ),
                                                            )
                                                        ),
                                                        'easing' => array(
                                                            'priority' => 140,
                                                            'type' => 'select',
                                                            'label' => __( "Easing", "ml-slider" ),
                                                            'class' => 'option flex',
                                                            'helptext' => __( "Animation easing effect", "ml-slider" ),
                                                            'value' => $this->slider->get_setting( 'easing' ),
                                                            'options' => $this->get_easing_options()
                                                        ),
                                                        'prevText' => array(
                                                            'priority' => 150,
                                                            'type' => 'text',
                                                            'label' => __( "Previous text", "ml-slider" ),
                                                            'class' => 'option coin flex responsive nivo',
                                                            'helptext' => __( "Set the text for the 'previous' direction item", "ml-slider" ),
                                                            'value' => $this->slider->get_setting( 'prevText' ) == 'false' ? '' : $this->slider->get_setting( 'prevText' )
                                                        ),
                                                        'nextText' => array(
                                                            'priority' => 160,
                                                            'type' => 'text',
                                                            'label' => __( "Next text", "ml-slider" ),
                                                            'class' => 'option coin flex responsive nivo',
                                                            'helptext' => __( "Set the text for the 'next' direction item", "ml-slider" ),
                                                            'value' => $this->slider->get_setting( 'nextText' ) == 'false' ? '' : $this->slider->get_setting( 'nextText' )
                                                        ),
                                                        'sDelay' => array(
                                                            'priority' => 170,
                                                            'type' => 'number',
                                                            'size' => 3,
                                                            'min' => 0,
                                                            'max' => 500,
                                                            'step' => 10,
                                                            'value' => $this->slider->get_setting( 'sDelay' ),
                                                            'label' => __( "Square delay", "ml-slider" ),
                                                            'class' => 'option coin',
                                                            'helptext' => __( "Delay between squares in ms", "ml-slider" ),
                                                            'after' => _x("ms", "Short for milliseconds", "ml-slider")
                                                        ),
                                                        'opacity' => array(
                                                            'priority' => 180,
                                                            'type' => 'number',
                                                            'size' => 3,
                                                            'min' => 0,
                                                            'max' => 1,
                                                            'step' => 0.1,
                                                            'value' => $this->slider->get_setting( 'opacity' ),
                                                            'label' => __( "Opacity", "ml-slider" ),
                                                            'class' => 'option coin',
                                                            'helptext' => __( "Opacity of title and navigation", "ml-slider" ),
                                                            'after' => ''
                                                        ),
                                                        'titleSpeed' => array(
                                                            'priority' => 190,
                                                            'type' => 'number',
                                                            'size' => 3,
                                                            'min' => 0,
                                                            'max' => 10000,
                                                            'step' => 100,
                                                            'value' => $this->slider->get_setting( 'titleSpeed' ),
                                                            'label' => __( "Caption speed", "ml-slider" ),
                                                            'class' => 'option coin',
                                                            'helptext' => __( "Set the fade in speed of the caption", "ml-slider" ),
                                                            'after' => _x("ms", "Short for milliseconds", "ml-slider")
                                                        ),
                                                        'developerOptions' => array(
                                                            'priority' => 195,
                                                            'type' => 'divider',
                                                            'class' => 'option coin flex responsive nivo',
                                                            'value' => __( "Developer options", "ml-slider" )
                                                        ),
                                                        'cssClass' => array(
                                                            'priority' => 200,
                                                            'type' => 'text',
                                                            'label' => __( "CSS classes", "ml-slider" ),
                                                            'class' => 'option coin flex responsive nivo',
                                                            'helptext' => __( "Specify any custom CSS Classes you would like to be added to the slider wrapper", "ml-slider" ),
                                                            'value' => $this->slider->get_setting( 'cssClass' ) == 'false' ? '' : $this->slider->get_setting( 'cssClass' )
                                                        ),
                                                        'printCss' => array(
                                                            'priority' => 210,
                                                            'type' => 'checkbox',
                                                            'label' => __( "Print CSS", "ml-slider" ),
                                                            'class' => 'option coin flex responsive nivo useWithCaution',
                                                            'checked' => $this->slider->get_setting( 'printCss' ) == 'true' ? 'checked' : '',
                                                            'helptext' => __( "Uncheck this is you would like to include your own CSS", "ml-slider" )
                                                        ),
                                                        'printJs' => array(
                                                            'priority' => 220,
                                                            'type' => 'checkbox',
                                                            'label' => __( "Print JS", "ml-slider" ),
                                                            'class' => 'option coin flex responsive nivo useWithCaution',
                                                            'checked' => $this->slider->get_setting( 'printJs' ) == 'true' ? 'checked' : '',
                                                            'helptext' => __( "Uncheck this is you would like to include your own Javascript", "ml-slider" )
                                                        ),
                                                        'noConflict' => array(
                                                            'priority' => 230,
                                                            'type' => 'checkbox',
                                                            'label' => __( "No conflict mode", "ml-slider" ),
                                                            'class' => 'option flex',
                                                            'checked' => $this->slider->get_setting( 'noConflict' ) == 'true' ? 'checked' : '',
                                                            'helptext' => __( "Delay adding the flexslider class to the slideshow", "ml-slider" )
                                                        ),
                                                    );

                                                    $aFields = apply_filters( 'metaslider_advanced_settings', $aFields, $this->slider );

                                                    echo $this->build_settings_rows( $aFields );
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <?php $url = wp_nonce_url(admin_url("admin-post.php?action=metaslider_delete_slider&amp;slider_id={$this->slider->id}"), "metaslider_delete_slider"); ?>
								<div class="ms-delete-save">
									<a @click="deleteSlideshow" class='ms-delete-slideshow' href='<?php echo $url ?>'>Delete</a>
									<button class='button button-primary' type='submit' name='save'>
										<?php _e("Save slideshow", "ml-slider"); ?>
									</button>
									<span class="spinner" style="display: none;"></span>
								</div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
			</form>
			<metaslider-import-module></metaslider-import-module>
			</div>
		</metaslider>
        </div>
        <?php
    }

    /**
     * Append the 'Add Slider' button to selected admin pages
     *
     * @param  string $context HTML being passed to amend HTML button
     * @return string          HTML button
     */
    public function insert_metaslider_button($context) {

        $capability = apply_filters( 'metaslider_capability', 'edit_others_posts' );

        if ( ! current_user_can( $capability ) ) {
            return $context;
        }

        global $pagenow;

        if ( in_array( $pagenow, array( 'post.php', 'page.php', 'post-new.php', 'post-edit.php' ) ) ) {
            $context .= '<a href="#TB_inline?&inlineId=choose-meta-slider" class="thickbox button" title="' .
                __( "Select slideshow to insert into post", "ml-slider" ) .
                '"><span class="wp-media-buttons-icon" style="background: url(' . METASLIDER_ASSETS_URL .
                '/metaslider/matchalabs.png); background-repeat: no-repeat; background-position: left bottom;"></span> ' .
                __( "Add slider", "ml-slider" ) . '</a>';
        }

        return $context;

    }


    /**
     * Append the 'Choose MetaSlider' thickbox content to the bottom of selected admin pages
     */
    public function admin_footer() {

        global $pagenow;

        // Only run in post/page creation and edit screens
        if ( in_array( $pagenow, array( 'post.php', 'page.php', 'post-new.php', 'post-edit.php' ) ) ) {
            $sliders = $this->all_meta_sliders( 'title' );
            ?>

            <script>
                jQuery(document).ready(function() {
                  jQuery('#insertMetaSlider').on('click', function() {
					var id = jQuery('#metaslider-select option:selected').val();
                    window.send_to_editor('[metaslider id=' + id + ']');
                    tb_remove();
                  })
                });
            </script>

            <div id="choose-meta-slider" style="display: none;">
                <div class="wrap">
                    <?php
                        if ( count( $sliders ) ) {
                            echo "<h3 style='margin-bottom: 20px;'>" . _x("Insert MetaSlider", 'Keep the plugin name "MetaSlider" when possible', "ml-slider") . "</h3>";
                            echo "<select id='metaslider-select'>";
                            echo "<option disabled=disabled>" . __( "Choose slideshow", "ml-slider" ) . "</option>";						
                            foreach ( $sliders as $slider ) {
                                echo "<option value='{$slider['id']}'>{$slider['title']}</option>";

                            }
                            echo "</select>";
                            echo "<button class='button primary' id='insertMetaSlider'>" . __( "Insert slideshow", "ml-slider" ) . "</button>";
                        } else {
                            _e( "No slideshows found", "ml-slider" );
                        }
                    ?>
                </div>
            </div>

            <?php
        }
    }


    /**
     * Return the MetaSlider pro upgrade iFrame
     */
    public function upgrade_to_pro_tab_layer() {
        if (function_exists('is_plugin_active') && ! is_plugin_active('ml-slider-pro/ml-slider-pro.php')) {
            return wp_iframe(array($this, 'upgrade_to_pro_iframe_layer'));
        }
    }

    /**
     * Media Manager iframe HTML - vimeo
     */
    public function upgrade_to_pro_iframe_layer() {
        $link = apply_filters('metaslider_hoplink', 'https://www.metaslider.com/upgrade/');
        $link .= '?utm_source=lite&amp;utm_medium=more-slide-types-layer&amp;utm_campaign=pro';
        $this->upgrade_to_pro_iframe(
            array(
                "<img src='" . METASLIDER_ADMIN_URL . "images/upgrade/layers.png' alt='' />",
                "<p>" . sprintf(_x("Ideal for easily creating eye-catching presentations and slideshows using stunning effects with %s 30 animation options %s and a beautiful, easy to use interface.", 'Translators: %s opens and closes a strong tag', 'ml-slider'), '<strong>', '</strong>') . " " . sprintf(_x('Layers can include %1$s text, HTML, images, videos %2$s and even %1$s shortcodes%2$s!', 'Translators: %1$s opens and %2$s closes a strong tag', 'ml-slider'), '<strong>', '</strong>') . "</p>",
                "<p>" . __("Additional optimization and customization options including using a video as the slide background, and adjusting the padding and background color of each layer.", 'ml-slider') . "</p>",
                "<a class='probutton button button-primary button-hero' href='{$link}' target='_blank'>" . __('Find out more about all the features of the Add-on Pack here', 'ml-slider') . "</a>",
                "<span class='subtext'>" . __('Opens in a new window', 'ml-slider') . "</span>"
            )
        );
    }

    /**
     * Return the MetaSlider pro upgrade iFrame for Vimeo
     */
    public function upgrade_to_pro_tab_vimeo() {
        if (function_exists('is_plugin_active') && ! is_plugin_active('ml-slider-pro/ml-slider-pro.php')) {
            return wp_iframe(array($this, 'upgrade_to_pro_iframe_vimeo'));
        }
    }

    /**
     * Media Manager iframe HTML - vimeo
     */
    public function upgrade_to_pro_iframe_vimeo() {
        $link = apply_filters('metaslider_hoplink', 'https://www.metaslider.com/upgrade/');
        $link .= '?utm_source=lite&amp;utm_medium=more-slide-types-vimeo&amp;utm_campaign=pro';
        $this->upgrade_to_pro_iframe(
            array(
                "<img src='" . METASLIDER_ADMIN_URL . "images/upgrade/vimeo.png' alt='' />",
                "<h3>" . __('Create a slideshow full of your favorite videos easily and quickly by simply adding a URL to a Vimeo slide.', 'ml-slider') . "</h3>",
                "<p>" . __('Features include an automatic play/pause function, in which your slideshow will detect when you set a video to play and continue once the video has finished.', 'ml-slider') . "</p>",
                "<p>" . __('You could even have the slider autoplay a video, giving you a smooth, completely automatic presentation!', 'ml-slider') . "</p>",
                "<a class='probutton button button-primary button-hero' href='{$link}' target='_blank'>" . __('Find out more about all the features of the Add-on Pack here', 'ml-slider') . "</a>",
                "<span class='subtext'>" . __('Opens in a new window', 'ml-slider') . "</span>"
            )
        );
    }

    /**
     * Return the MetaSlider pro upgrade iFrame
     */
    public function upgrade_to_pro_tab_youtube() {
        if (function_exists('is_plugin_active') && ! is_plugin_active('ml-slider-pro/ml-slider-pro.php')) {
            return wp_iframe(array($this, 'upgrade_to_pro_iframe_youtube'));
        }
    }
    /**
     * Media Manager iframe HTML - youtube
     */
    public function upgrade_to_pro_iframe_youtube() {
        $link = apply_filters('metaslider_hoplink', 'https://www.metaslider.com/upgrade/');
        $link .= '?utm_source=lite&amp;utm_medium=more-slide-types-youtube&amp;utm_campaign=pro';
        $this->upgrade_to_pro_iframe(
            array(
                "<img src='" . METASLIDER_ADMIN_URL . "images/upgrade/youtube.png' alt='' />",
                "<h3>" . __('Create a slideshow full of your favorite videos easily and quickly by simply adding a URL to a YouTube slide.', 'ml-slider') . "</h3>",
                "<p>" . __('Features include an automatic play/pause function, in which your slideshow will detect when you set a video to play and continue once the video has finished.', 'ml-slider') . "</p>",
                "<p>" . __('You could even have the slider autoplay a video, giving you a smooth, completely automatic presentation!', 'ml-slider') . "</p>",
                "<a class='probutton button button-primary button-hero' href='{$link}' target='_blank'>" . __('Find out more about all the features of the Add-on Pack here', 'ml-slider') . "</a>",
                "<span class='subtext'>" . __('Opens in a new window', 'ml-slider') . "</span>"
            )
        );
    }
    /**
     * Return the MetaSlider pro upgrade iFrame
     */
    public function upgrade_to_pro_tab_post_feed() {
        if (function_exists('is_plugin_active') && ! is_plugin_active('ml-slider-pro/ml-slider-pro.php')) {
            return wp_iframe(array($this, 'upgrade_to_pro_iframe_post_feed'));
        }
    }
    /**
     * Media Manager iframe HTML - post_feed
     */
    public function upgrade_to_pro_iframe_post_feed() {
        $link = apply_filters('metaslider_hoplink', 'https://www.metaslider.com/upgrade/');
        $link .= '?utm_source=lite&amp;utm_medium=more-slide-types-feed&amp;utm_campaign=pro';
        $this->upgrade_to_pro_iframe(
            array(
                "<img src='" . METASLIDER_ADMIN_URL . "images/upgrade/post-feed.png' alt='' />",
				"<p>" . sprintf(_x('Show off your %1$sblog posts%2$s, %1$sevents%2$s, %1$sWooCommerce products%2$s and other content with %1$sPost Feed%2$s.', 'Translators: %1$s opens and %2$s closes a strong tag', 'ml-slider'), '<strong>', '</strong>') . "</p>",
                "<p>" . __('Customise and control which post types you want to display, their order and how to restrict posts to certain tags or categories.', 'ml-slider') . "</p>",
                "<p>" . __('Post Feed slides can also be used with other slide types to either show one post at a time or in a carousel mode, allowing you to show off a large number of your latest posts in a small amount of space.', 'ml-slider') . "</p>",
                "<a class='probutton button button-primary button-hero' href='{$link}' target='_blank'>" . __('Find out more about all the features of the Add-on Pack here', 'ml-slider') . "</a>",
                "<span class='subtext'>" . __('Opens in a new window', 'ml-slider') . "</span>"
            )
        );
    }

    /**
     * Upgrade to pro Iframe - Render
     *
     * @param array $content The HTML to render
     */
    public function upgrade_to_pro_iframe($content) {
        wp_enqueue_style('metaslider-admin-styles', METASLIDER_ADMIN_URL . 'assets/css/admin.css', false, METASLIDER_VERSION);
        wp_enqueue_script('google-font-api', 'https://fonts.googleapis.com/css?family=PT+Sans:400,700' , false, METASLIDER_VERSION);
        
        echo "<div class='metaslider_pro'>";
        echo implode("", $content);
        echo "</div>";
    }

    /**
     * Provide a tip so the user can add the slideshow to thier site
     * 
     * @return string the tip
     */
    public function shortcode_tip() {
		// Title
		return '<h3 class="hndle metaslider-shortcode-title"><span>' . __("How to Use", "ml-slider") . '</span>' .

		// Switch to title button
		'<div class="ms-action-buttons wp-clearfix"><button data-type="id" id="ms-copy-type" class="ml-button ml-skinless-button tipsy-tooltip-top" title="' . __("Show title", "ml-slider") . '"><i><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shuffle"><polyline points="16 3 21 3 21 8"></polyline><line x1="4" y1="20" x2="21" y2="3"></line><polyline points="21 16 21 21 16 21"></polyline><line x1="15" y1="15" x2="21" y2="21"></line><line x1="4" y1="4" x2="9" y2="9"></line></svg></i></button>' .

		// Copy all button
		'<button id="ms-copy-all" class="ml-button ml-skinless-button tipsy-tooltip-top" title="' . __("Copy all code", "ml-slider") . '"><i><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-copy"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg></i></button></div>' .

		'</h3>' .

		// Container
		'<div class="inside wp-clearfix metaslider-shortcode">' .

		// Description
		'<p>' . __('To display your slideshow using id or title, add the following shortcodes (in orange) to your page. If adding the slideshow to your theme files, additionally include the surrounding PHP function (in gray).', 'ml-slider') . '</p>' .
		
		// Shortcode
		'<pre class="ms-entire" id="ms-entire-code">&lt;?php echo do_shortcode(\'<br>&emsp;&emsp;<div class="ms-shortcode">[metaslider <span id="ms-shortcode-id">id="' . $this->slider->id . '"</span><span style="display:none" id="ms-shortcode-title">title="' . get_the_title($this->slider->id) . '"</span>]</div><br>\'); ?&gt;</pre>' .

        '</div>';    
    }

    /**
     * Adds extra links to the plugin activation page
     *
     * @param  array  $meta   Extra meta links
     * @param  string $file   Specific file to compare against the base plugin
     * @param  string $data   Data for the meat links
     * @param  string $status Staus of the meta links
     * @return array          Return the meta links array
     */
    public function get_extra_meta_links($meta, $file, $data, $status) {

        if (plugin_basename(__FILE__) == $file) {
            $plugin_page = admin_url('admin.php?page=metaslider');
            $meta[] = "<a href='{$plugin_page}' onclick=\"event.preventDefault();var link = jQuery(this);jQuery.post(ajaxurl, {action: 'reset_tour_status', _wpnonce: metaslider_tour_nonce }, function(data) {window.location = link.attr('href');});\">" . __('Take a tour', 'ml-slider') . "</a>";
            if (metaslider_pro_is_installed()) {
                $meta[] = "<a href='https://www.metaslider.com/support/' target='_blank'>" . __('Premium Support', 'ml-slider') . "</a>";
            } else {
                $upgrade_link = apply_filters('metaslider_hoplink', 'https://www.metaslider.com/upgrade/');
                $meta[] = "<a href='{$upgrade_link}' target='_blank'>" . __('Add-ons', 'ml-slider') . "</a>";
                $meta[] = "<a href='https://wordpress.org/support/plugin/ml-slider/' target='_blank'>" . __('Support', 'ml-slider') . "</a>";
            }
            $meta[] = "<a href='https://www.metaslider.com/documentation/' target='_blank'>" . __('Documentation', 'ml-slider') . "</a>";
            $meta[] = "<a href='https://wordpress.org/support/plugin/ml-slider/reviews#new-post' target='_blank' title='" . __('Leave a review', 'ml-slider') . "'><i class='ml-stars'><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg></i></a>";
        }
        return $meta;
    }

   /**
    * Adds styles to admin head to allow for stars animation and coloring
    */
    public function add_star_styles() {
        if (metaslider_user_is_on_admin_page('plugins.php')) {?>
            <style>
                .ml-stars{display:inline-block;color:#ffb900;position:relative;top:3px}
                .ml-stars svg{fill:#ffb900}
                .ml-stars svg:hover{fill:#ffb900}
                .ml-stars svg:hover ~ svg{fill:none}
            </style>
    <?php }
    }

   /**
    * Add nonce to activation pa
    */
    public function add_tour_nonce_to_activation_page() {
        if (metaslider_user_is_on_admin_page('plugins.php')) {?>
            <script>
                var metaslider_tour_nonce = "<?php echo wp_create_nonce('metaslider_tour_nonce'); ?>";
            </script>
    <?php }
    }

    /**
     * Toggle Layout Buttons.
     *
     * @return string returns html button
     */
    public function toggle_layout_button() {
        
        // Don't show this if there are no slideshows
        if (!count($this->all_meta_sliders())) {
            return '';
        }

        $view = ('tabs' == $this->get_view()) ? 'tabs' : 'dropdown';
        $view_opposite = ('dropdown' == $this->get_view()) ? 'tabs' : 'dropdown';
		$instructions = ($this->get_view() == 'tabs') ? __("Switch to dropdown view", "ml-slider") : __("Switch to tabs view", "ml-slider");
		$url = admin_url("admin-post.php?action=metaslider_switch_view&view=" . $view_opposite);
        return "<a class='toggle-nav-view tipsy-tooltip' title='{$instructions}' href='{$url}'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"17\" height=\"17\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-shuffle\"><polyline points=\"16 3 21 3 21 8\"/><line x1=\"4\" y1=\"20\" x2=\"21\" y2=\"3\"/><polyline points=\"21 16 21 21 16 21\"/><line x1=\"15\" y1=\"15\" x2=\"21\" y2=\"21\"/><line x1=\"4\" y1=\"4\" x2=\"9\" y2=\"9\"/></svg></a>";
    }

    /**
     * Add a 'property=stylesheet' attribute to the MetaSlider CSS links for HTML5 validation
     *
     * @since 3.3.4
     * @param string $tag    Specifies tag
     * @param string $handle Checks for the handle to add property to
     * @return string
     */
    public function add_property_attribute_to_stylesheet_links( $tag, $handle ) {

        if ( strpos( $handle, 'metaslider' ) !== FALSE && strpos( $tag, "property='" ) === FALSE ) {
            // we're only filtering tags with metaslider in the handle, and links which don't already have a property attribute
            $tag = str_replace( "/>", "property='stylesheet' />", $tag );
        }

        return $tag;

    }

}

endif;

add_action('plugins_loaded', array(MetaSliderPlugin::get_instance(), 'setup'), 10);
