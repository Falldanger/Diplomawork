<!-- start content container -->
<div class="row">      
	<article class="col-md-<?php bulk_main_content_width_columns(); ?>">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>                         
				<div <?php post_class(); ?>>
					<div class="single-content"> 
						<div class="single-entry-summary">
							<?php the_content(); ?> 
						</div><!-- .single-entry-summary -->
						<?php wp_link_pages(); ?>                                                           
					</div>
					<div class="single-footer row">
						<div class="col-md-4">
							<?php get_template_part( 'template-parts/template-part', 'postauthor' ); ?>
						</div>
						<div class="col-md-8">
							<?php comments_template(); ?> 
						</div>
					</div>
				</div>        
			<?php endwhile; ?>        
		<?php else : ?>            
			<?php get_template_part( 'content', 'none' ); ?>        
		<?php endif; ?>    
	</article> 

	<?php get_sidebar( 'right' ); ?>
</div>
<!-- end content container -->
