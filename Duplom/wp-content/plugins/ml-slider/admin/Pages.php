<?php

if (!defined('ABSPATH')) die('No direct access.');

/**
 * Entry point for building the wordpress admin pages.
 * Temporarily extends the MetaSlider Class until more refactoring can be done.
 */
Class MetaSlider_Admin_Pages extends MetaSliderPlugin {

    /**
     * The MetaSlider plugin class
     *
     * @var object $plugin
     */
    private $plugin;
    
    /**
     * The current admin page
     *
     * @var string $current_page
     */
    private $current_page;

    /**
     * Sets up the notices, security and loads assets for the admin page
     *
     * @param array $plugin Plugin details
     */
    public function __construct($plugin) {
        $this->plugin = $plugin;
        $this->notices = new MetaSlider_Notices($plugin);
        $this->tour = new MetaSlider_Tour($plugin, 'toplevel_page_metaslider');
        add_action('admin_enqueue_scripts', array($this, 'load_icon_css'));
        add_action('admin_enqueue_scripts', array($this, 'load_upgrade_page_assets'));
    }
    
    /**
     * Loads the icon on the top levelmenu page name
     */
    public function load_icon_css() {
        wp_enqueue_style('metaslider-global', METASLIDER_ADMIN_URL . 'assets/css/icon-' . sanitize_title(METASLIDER_VERSION) . '.css', array(), METASLIDER_VERSION);
    }

    /**
     * Loads in the overlay assets
     */
    public function load_overlay() {
        wp_enqueue_style('metaslider-colorbox-styles', METASLIDER_ADMIN_URL . 'assets/vendor/colorbox/colorbox.css', false, METASLIDER_VERSION);
        wp_enqueue_script('metaslider-colorbox', METASLIDER_ADMIN_URL . 'assets/vendor/colorbox/jquery.colorbox-min.js', array('jquery'), METASLIDER_VERSION, true);
    }
    
    /**
     * Loads in tooltips
     */
    public function load_tooltips() {
        wp_enqueue_style('metaslider-tipsy-styles', METASLIDER_ADMIN_URL . 'assets/vendor/tipsy/tipsy.css', false, METASLIDER_VERSION);
        wp_enqueue_script('metaslider-tipsy', METASLIDER_ADMIN_URL . 'assets/vendor/tipsy/jquery.tipsy.js', array('jquery'), METASLIDER_VERSION, true);
    }

    /**
     * Loads in custom javascript
     */    
    public function load_javascript() {
         
        wp_enqueue_media();
        wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-sortable');
     
        wp_register_script('metaslider-admin-script', METASLIDER_ADMIN_URL . 'assets/js/admin-' . sanitize_title(METASLIDER_VERSION) . '.js', array('jquery'), METASLIDER_VERSION, true);
        wp_localize_script('metaslider-admin-script', 'metaslider', array(
            'url' => __("URL", "ml-slider"),
            'caption' => __("Caption", "ml-slider"),
            'new_window' => __("New Window", "ml-slider"),
            'confirm' => __("Please confirm that you would like to delete this slideshow.", "ml-slider"),
            'restore_language' => __("Undo", "ml-slider"),
            'restored_language' => __("Slide restored", "ml-slider"),
            'deleted_language' => __("Slide deleted", "ml-slider"),
            'success_language' => __("Success", "ml-slider"),
            'copied_language' => __("Item was copied to your clipboard", "ml-slider"),
            'click_to_undo_language' => __("Press to undo", "ml-slider"),
            'ajaxurl' => admin_url('admin-ajax.php'),
            'update_image' => __("Select replacement image", "ml-slider"),
            'resize_nonce' => wp_create_nonce('metaslider_resize'),
            'create_slide_nonce' => wp_create_nonce('metaslider_create_slide'),
            'delete_slide_nonce' => wp_create_nonce('metaslider_delete_slide'),
            'undelete_slide_nonce' => wp_create_nonce('metaslider_undelete_slide'),
            'update_slide_image_nonce' => wp_create_nonce('metaslider_update_slide_image'),
            'handle_notices_nonce' => wp_create_nonce('metaslider_handle_notices_nonce'),
            'useWithCaution' => __("Caution: This setting is for advanced developers only. If you're unsure, leave it checked.", "ml-slider")
        ));
        wp_enqueue_script('metaslider-admin-script');
		do_action('metaslider_register_admin_scripts');
		
		// Register components and add support for the REST API / Admin AJAX
		wp_register_script('metaslider-admin-components', METASLIDER_ADMIN_URL . 'assets/js/app-' . sanitize_title(METASLIDER_VERSION) . '.js', array(), METASLIDER_VERSION, true);

		// Check if rest is available
		$can_use_rest = class_exists('WP_REST_Controller');

		// Further check, probably doing more harm than help
		// $can_use_rest = class_exists('WP_REST_Controller') ? (200 === wp_remote_retrieve_response_code(wp_remote_get(rest_url()))) : false;
		// Add extra data
		wp_localize_script('metaslider-admin-components', 'metaslider_api', array(
			'root' => $can_use_rest ? esc_url_raw(rest_url("metaslider/v1/")) : false,
			'nonce' => wp_create_nonce('wp_rest'),
			'ajaxurl' => admin_url('admin-ajax.php'),
			'proUser' => metaslider_pro_is_active(),
			'hoplink' => metaslider_get_upgrade_link(),
			'metaslider_page' => admin_url('admin.php?page=metaslider'),
			'theme_editor_link' => admin_url('admin.php?page=metaslider-theme-editor'),
			'supports_rest' => $can_use_rest,
			'locale' => $this->gutenberg_get_jed_locale_data('ml-slider'),
			'default_locale' => $this->gutenberg_get_jed_locale_data('default')
		));
		wp_enqueue_script('metaslider-admin-components');
    }

    /**
     * Loads in custom styling for upgrade page
     */    
    public function load_upgrade_page_assets() {
        if ('upgrade-metaslider' == $this->current_page) {
            wp_enqueue_style('metaslider-upgrade-styles', METASLIDER_ADMIN_URL . 'assets/css/upgrade-' . sanitize_title(METASLIDER_VERSION) . '.css', false, METASLIDER_VERSION);
        }
    }
    
    /**
     * Loads in custom styling
     */    
    public function load_styles() {
        wp_enqueue_style('metaslider-admin-styles', METASLIDER_ADMIN_URL . 'assets/css/admin-' . sanitize_title(METASLIDER_VERSION) . '.css', false, METASLIDER_VERSION);

        // Hook to load more styles and scripts (from pro)
        do_action('metaslider_register_admin_styles');
    }

    /**
     * Area used to handle any third-party plugin conflicts
     */    
    public function fix_conflicts() {

        // WP Posts Filter Fix (Advanced Settings not toggling)
        wp_dequeue_script('link'); 

        // All In One Events Calendar Fix (Advanced Settings not toggling)
        wp_dequeue_script('ai1ec_requirejs'); 
    }
    
    /**
     * Method to add pages
     *
     * @param string $title  - The title of the page
     * @param string $slug   - The slug used for the page
     * @param string $parent - Setting a parent will make this page a submenu item
     */
    public function add_page($title, $slug = '', $parent = '') {
        $slug = ('' == $slug) ? sanitize_title($title) : $slug;
        $method = 'render_'.str_replace("-", "_", $slug).'_page';
        if (!method_exists($this, $method)) {
            return false;
        }
        $this->current_page = $slug;
        $capability = apply_filters('metaslider_capability', 'edit_others_posts');

        $page = ('' == $parent) ? add_menu_page($title, $title, $capability, $slug, array($this, $method)) : add_submenu_page($parent, $title, $title, $capability, $slug, array($this, $method));
        
        // Load assets on all pages
        add_action('load-' . $page, array($this, 'fix_conflicts'));
        add_action('load-' . $page, array($this, 'load_overlay'));
        add_action('load-' . $page, array($this, 'load_tooltips'));
        add_action('load-' . $page, array($this, 'load_javascript'));
        add_action('load-' . $page, array($this, 'load_styles'));
    }

    /**
     * Sets up any logic needed for the main page
     * TODO continue refactoring from here
     */
    public function render_metaslider_page() {
        parent::render_admin_page(); 
    }

    /**
     * Sets up any logic needed for the upgrade page
     */
    public function render_upgrade_metaslider_page() {
        include METASLIDER_PATH."admin/views/pages/upgrade.php";
	}
	
	/**
	 * Backup function for Gutenberg's gutenberg_get_jed_locale_data
	 * 
	 * @param string $domain - The text domain for the strings
	 */
	private function gutenberg_get_jed_locale_data($domain) {

		if (function_exists('gutenberg_get_jed_locale_data')) {
			return gutenberg_get_jed_locale_data($domain);
		}
		
		$translations = get_translations_for_domain($domain);
		$locale = array(
			'' => array(
				'domain' => $domain,
				'lang' => is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale(),
			),
		);

		if (!empty($translations->headers['Plural-Forms'])) {
			$locale['']['plural_forms'] = $translations->headers['Plural-Forms'];
		}

		foreach ($translations->entries as $msgid => $entry) {
			$locale[$msgid] = $entry->translations;
		}
		return $locale;
	}
}
