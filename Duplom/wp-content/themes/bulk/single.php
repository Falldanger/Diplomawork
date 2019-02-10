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
		<div class="post-meta">
			<?php bulk_time_link(); ?>
			<?php bulk_posted_on(); ?>
			<?php bulk_entry_footer(); ?>
		</div>
		<?php do_action( 'bulk_after_post_meta' ); ?>
	</header>
</div>
<?php get_template_part( 'template-parts/template-part', 'content' ); ?>

<?php get_template_part( 'content', 'single' ); ?>

<?php get_footer(); ?>
