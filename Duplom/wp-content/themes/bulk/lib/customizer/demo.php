<?php
/**
 * Bulk Demo Content Information
 *
 * @package lawyer_landing_page
 */

function bulk_customizer_demo_content( $wp_customize ) {
	
    $wp_customize->add_section( 
        'theme_demo_content',
        array(
            'title'    => __( 'One Click Demo Import', 'bulk' ),
            'priority' => 7,
		)
    );
        
    $wp_customize->add_setting(
		'demo_content_instruction',
		array(
			'sanitize_callback' => 'wp_kses_post'
		)
	);
	/* translators: %s: "Click here" string */
	$demo_content_description = sprintf( __( 'You can import the demo content with just one click. For step-by-step video tutorial, see %1$s', 'bulk' ), '<a class="documentation" href="' . esc_url( 'http://demo.themes4wp.com/documentation/importing-elementor-demo-pages/' ) . '" target="_blank">' . esc_html__( 'documentation', 'bulk' ) . '</a>' );

	$wp_customize->add_control(
		new bulk_Info_Text( 
			$wp_customize,
			'demo_content_instruction',
			array(
				'section'	  => 'theme_demo_content',
				'description' => $demo_content_description
			)
		)
	);
    
	$wp_customize->add_setting( 
        'theme_demo_content_info',
        array(
            'default'           => '',
            'sanitize_callback' => 'wp_kses_post',
		)
    );

	// Demo content 
	$wp_customize->add_control( 
        new bulk_Info_Text( 
            $wp_customize,
            'theme_demo_content_info',
            array(
                'section'     => 'theme_demo_content',
    		)
        )
    );

}
add_action( 'customize_register', 'bulk_customizer_demo_content' );