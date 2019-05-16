<?php

if (!defined('ABSPATH')) die('No direct access.');

/**
 * Class to handle themes
 */
class MetaSlider_Themes {

	/**
	 * Theme instance
	 * 
	 * @var object
	 * @see get_instance()
	 */
	protected static $instance = null;

	/**
	 * Theme name
	 * 
	 * @var string
	 */
	public $theme_id;

	/**
	 * List of supported slide types
	 * 
	 * @var array
	 */
	public $supported_slideshow_libraries = array('flex', 'responsive', 'nivo', 'coin');

	/**
	 * Constructor
	 */
	public function __construct() {}

	/**
	 * Used to access the instance
	 */
	public static function get_instance() {
		if (null === self::$instance) self::$instance = new self();
		return self::$instance;
	}

	/**
	 * Method to get all the free themes from the theme directory
	 * 
	 * @return array|WP_Error whether the file was included, or error class
	 */
	public function get_all_free_themes() {
		if (!file_exists(METASLIDER_THEMES_PATH . 'manifest.php')) {
			return new WP_Error('manifest_not_found', __('No themes found.', 'ml-slider'), array('status' => 404));
		}
		return (include METASLIDER_THEMES_PATH . 'manifest.php');
	}

	/**
	 * Method to get all custom themes from the database.
	 * 
	 * @return array Returns an array of custom themes or an empty array if none found
	 */
	public function get_custom_themes() {
		$custom_themes = array();
		if ((bool) $themes = get_option('metaslider-themes')) {
			foreach($themes as $id => $theme) {
				$custom_themes[$id] = array(
					'folder' => $id,
					'title' => $theme['title'],
					'type' => 'custom'
				);
			}
		}
		return $custom_themes;
	}

	/**
     * Method to get details about a theme
     *
	 * @param string $id - Id of the slideshow
     * @return void
     */
	public function details($id) {

	}

	/**
	 * Method to get the object by theme name/id
	 *
	 * @param string $slideshow_id - Id of the slideshow
	 * @param string $theme_id 	   - Id of the theme
	 * 
	 * @return bool|array - The theme object or false if no theme
	 */
	public function get_theme_object($slideshow_id, $theme_id) {
		if (is_wp_error($free_themes = $this->get_all_free_themes())) {
			$free_themes = array();
		}
		$themes = array_merge($free_themes, $this->get_custom_themes());
		foreach($themes as $one_theme) {
			if ($one_theme['folder'] === $theme_id) {
				$theme = $one_theme;
			}
		}

		// If the folder isn't set then something went wrong or no theme is set.
		if (!isset($theme['folder']) || '' == $theme['folder']) {
			return false;
		}

		// If the version isn't set, grab the latest
		if (!isset($theme['version']) || '' == $theme['version']) {
			$theme['version'] = $this->get_latest_version($theme['folder']);
		}

		return $theme;
    }


	/**
	 * Method to get a random theme
	 *
	 * @param bool $all - Whether to include themes that arent fully supported
	 * 
	 * @return bool|array - The theme object or false if no theme
	 */
	public function random($all = false) {
		if (is_wp_error($free_themes = $this->get_all_free_themes())) {
			return false;
		}
		if ($all) return array_rand($free_themes);

		$themes = array();
		foreach($free_themes as $id => $theme) {

			// Be sure the theme supports all slider libraries
			if (count($this->supported_slideshow_libraries) === count($theme['supports'])) {
				$themes[$id] = $theme;
			}
		}

		return array_rand($themes);
	}

	/**
	 * Method to get the current set theme for a slideshow
	 *
	 * @param string $slideshow_id - Id of the slideshow
	 * 
	 * @return bool|array - The theme object or false if no theme
	 */
	public function get_current_theme($slideshow_id) {

		$theme = get_post_meta($slideshow_id, 'metaslider_slideshow_theme', true);

		// If the theme is none, it means no theme is set (happens if they remove a theme)
		// $theme may be WP_Error due to a bug in 3.9.1
		if ('none' === $theme || is_wp_error($theme)) return false;

		$is_a_custom_theme = (isset($theme['folder']) && ('_theme' === substr($theme['folder'], 0, 6)));

		// Check here for a legacy theme OR a custom theme
		if (!isset($theme['folder']) || $is_a_custom_theme) {
			$settings = get_post_meta($slideshow_id, 'ml-slider_settings', true);
			
			// * This might be a nivo theme or a custom theme (pro)
			if (isset($settings['theme'])) {
				$settings['theme'] = in_array($settings['theme'], array('light', 'dark', 'bar')) ? 'nivo-' . $settings['theme'] : $settings['theme'];
				$theme = $this->get_theme_object($slideshow_id, $settings['theme']);

				// Update the theme to the new system
				update_post_meta($slideshow_id, 'metaslider_slideshow_theme', $theme);
			}
		}

		// If the folder isn't set then something went wrong or no theme is set.
		if (!isset($theme['folder']) || '' == $theme['folder']) {
			return false;
		}

		// If the version isn't set, grab the latest
		if (!isset($theme['version']) || '' == $theme['version']) {
			$theme['version'] = $this->get_latest_version($theme['folder']);
		}

        // At this point, if it's a custom theme we are okay
        if ($is_a_custom_theme) return $theme;

        // If the folder physically doesn't exist then we need to send an error
        if (!file_exists(METASLIDER_THEMES_PATH . trailingslashit($theme['folder']) . $theme['version'])) {

			// TODO in the future if we add versioning this could switch to an older version and message them about it.
            return new WP_Error('theme_not_found', __('Your selected theme is no longer available. Was the folder deleted?', 'ml-slider'));
		}
		
		return $theme;
	}

	/**
     * Method to get the version of the latest theme
     *
	 * @param string $folder - Folder name in /themes/
     * @return string the version number
     */
	public function get_latest_version($folder) {

		// If the changelog isn't there for some reason just assume it's v1.0.0
		if (!file_exists(METASLIDER_THEMES_PATH . trailingslashit($folder) . 'changelog.php')) {
			return 'v1.0.0';
		}
		$changelog = (include METASLIDER_THEMES_PATH . trailingslashit($folder) . 'changelog.php');
		return current(array_keys($changelog));
	}

	/**
	 * Method to set the theme
	 *
	 * @param int|string $slideshow_id The id of the current slideshow
	 * @param array 	 $theme 	   The selected theme object
	 * @return bool true on successful update, false on failure.
	 */
	public function set($slideshow_id, $theme) {

		// If the theme isn't set, then they attempted to remove the theme
		if (!isset($theme['folder']) || is_wp_error($theme)) {
			return update_post_meta($slideshow_id, 'metaslider_slideshow_theme', 'none');
		}

		// For legacy reasons we have to query the settings
		$settings = get_post_meta($slideshow_id, 'ml-slider_settings', true);

		// For custom themes, it's easier to use the legacy setting because the pro plugin
		// already hooks into it.
		if ('_theme' === substr($theme['folder'], 0, 6)) {
			$settings['theme'] = $theme['folder'];
			update_post_meta($slideshow_id, 'ml-slider_settings', $settings);
		} else if (isset($settings['theme'])) {
			
			// If the theme isn't a custom theme, we should unset the legacy setting
			// unset($settings['theme']); // ! Pro requires this to be set
			$settings['theme'] = '';
			update_post_meta($slideshow_id, 'ml-slider_settings', $settings);
		}

		// This will return false if the data is the same, unfortunately
		return (bool) update_post_meta($slideshow_id, 'metaslider_slideshow_theme', $theme);
	}

	/**
	 * Load in the selected theme assets.
	 * 
	 * @param int|string $slideshow_id The id of the current slideshow
	 * @param string 	 $theme_id 	   The folder name of a theme
	 * 
	 * @return bool|WP_Error whether the file was included, or error class
	 */
	public function load_theme($slideshow_id, $theme_id = null) {

		// Don't load a theme on the editor page.
		if (is_admin() && function_exists('get_current_screen') && $screen = get_current_screen()) {
			if ('metaslider-pro_page_metaslider-theme-editor' === $screen->id) return false;
		}

		$theme = (is_null($theme_id)) ? $this->get_current_theme($slideshow_id) : $this->get_theme_object($slideshow_id, $theme_id);

		// 'none' is the default theme to load no theme. 
		if ('none' == $theme_id) return false;
		if (is_wp_error($theme) || false === $theme) return $theme;

		// We have a theme, so lets add the class to the body
		$this->theme_id = $theme['folder'];
		add_filter('metaslider_css_classes', array($this, 'add_theme_class'), 10, 3);
		
		// Custom themes don't need any special file loading.
		if (!file_exists(METASLIDER_THEMES_PATH . trailingslashit($theme['folder']))) {
			return $theme;
		}

		// Load in the base theme class
		require_once(METASLIDER_THEMES_PATH . 'ms-theme-base.php');
		
		return include_once METASLIDER_THEMES_PATH . trailingslashit($theme['folder']) . trailingslashit($theme['version']) . 'theme.php';
	}

	/**
	 * Add the theme to the class so styles will apply. The theme can be
	 * overridden, for example from the preview functionality
	 * 
	 * @param string $class		   The slideshow classlist
	 * @param string $slideshow_id The id of the slideshow
	 * @param string $settings	   The settings for the slideshow
	 */
	public function add_theme_class($class, $slideshow_id, $settings) {
		$class .= ' ms-theme-' . $this->theme_id;
		return $class;
	}
}
