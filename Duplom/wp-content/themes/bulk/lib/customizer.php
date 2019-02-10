<?php
/**
 * Bulk Theme Customizer
 *
 * @package Bulk
 */

$bulk_sections = array( 'info', 'demo' );

foreach( $bulk_sections as $s ){
    require get_template_directory() . '/lib/customizer/' . $s . '.php';
}

function bulk_customizer_scripts() {
    wp_enqueue_style( 'bulk-customize',get_template_directory_uri().'/lib/customizer/css/customize.css', '', 'screen' );
    wp_enqueue_script( 'bulk-customize', get_template_directory_uri() . '/lib/customizer/js/customize.js', array( 'jquery' ), '20170404', true );
}
add_action( 'customize_controls_enqueue_scripts', 'bulk_customizer_scripts' );

/*
 * Notifications in customizer
 */
require get_template_directory() . '/lib/customizer-plugin-recommend/customizer-notice/class-customizer-notice.php';

require get_template_directory() . '/lib/customizer-plugin-recommend/plugin-install/class-plugin-install-helper.php';

$config_customizer = array(
	'recommended_plugins' => array( 
		'elementor' => array(
			'recommended' => true,
			/* translators: %s: "Elementor Page Builder" string */
			'description' => sprintf( esc_html__( 'To take full advantage of all the features this theme has to offer, please install and activate the %s plugin.', 'bulk' ), '<strong>Elementor Page Builder</strong>' ),
		),
	),
	'recommended_plugins_title' => esc_html__( 'Recommended plugin', 'bulk' ),
	'install_button_label'      => esc_html__( 'Install and Activate', 'bulk' ),
	'activate_button_label'     => esc_html__( 'Activate', 'bulk' ),
	'deactivate_button_label'   => esc_html__( 'Deactivate', 'bulk' ),
);
bulk_Customizer_Notice::init( apply_filters( 'bulk_customizer_notice_array', $config_customizer ) );
