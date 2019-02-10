<?php
class gmap_widgetGmp extends moduleGmp {
	public function init() {
        parent::init();
        add_action('widgets_init', array($this, 'registerWidget'));
    }
    public function registerWidget() {
        return register_widget('gmpMapsWidget');
    }    
}
/**
 * Maps widget class
 */
class gmpMapsWidget extends WP_Widget {
    public function __construct() {
        $widgetOps = array( 
            'classname' => 'gmpMapsWidget', 
            'description' => __('Displays Most Viewed Products', GMP_LANG_CODE)
        );
		parent::__construct( 'gmpMapsWidget', GMP_WP_PLUGIN_NAME, $widgetOps );
    }
    public function widget($args, $instance) {
        frameGmp::_()->getModule('gmap_widget')->getView()->displayWidget($instance);
    }
    public function form($instance) {
        frameGmp::_()->getModule('gmap_widget')->getView()->displayForm($instance, $this);
    }
	public function update($new_instance, $old_instance) {
		//frameGmp::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('map.widget.update');
		return $new_instance;
	}
}