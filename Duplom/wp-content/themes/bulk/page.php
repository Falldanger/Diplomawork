<?php get_header(); ?>

<div class="top-header text-center">
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="single-image">
			<?php the_post_thumbnail( 'full' ); ?>
		</div>
	<?php endif; ?>
	<header class="header-title container">
		<h1 class="page-header">                                
			<?php the_title(); ?>                          
		</h1>
		<?php do_action( 'bulk_after_page_title' ); ?>
	</header>
</div>

<?php get_template_part( 'template-parts/template-part', 'content' ); ?>
<!-- start content container -->
<?php get_template_part( 'content', 'page' ); ?>
<!-- end content container -->

<?php get_footer(); ?>
