<?php get_header(); ?> 

<?php if ( have_posts() ) : ?>
	<header class="archive-page-header container-fluid text-center">
		<div class="container">
			<?php
			the_archive_title( '<h1 class="page-title">', '</h1>' );
			the_archive_description( '<div class="taxonomy-description">', '</div>' );
			?>
		</div>
	</header><!-- .page-header -->
<?php endif; ?>

<?php get_template_part( 'template-parts/template-part', 'content' ); ?>
<!-- start content container -->
<div class="row">

	<div class="col-md-<?php bulk_main_content_width_columns(); ?>">
		<?php
		if ( have_posts() ) :

			while ( have_posts() ) : the_post();

				get_template_part( 'content', get_post_format() );

			endwhile;

			the_posts_pagination();

		else :

			get_template_part( 'content', 'none' );

		endif;
		?>

	</div>

	<?php get_sidebar( 'right' ); ?>

</div>
<!-- end content container -->

<?php get_footer(); ?>
