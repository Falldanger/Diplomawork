<?php
/**
 *
 * Template name: Elementor WooCommerce
 * 
 */

get_header(); ?>
<div class="container-fluid homepage-row row woocommerce">
<!-- start content container -->       
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>                          
				<div <?php post_class(); ?>>
					<div class="composer-main-content-page">                                                         
							<?php the_content(); ?>                                                          
					</div>
				</div>        
			<?php endwhile; ?>        
		<?php else : ?>            
			<?php get_template_part( 'content', 'none' ); ?>        
		<?php endif; ?>    
<!-- end content container -->
<?php 
get_footer();

