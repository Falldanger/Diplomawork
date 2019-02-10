<?php get_header(); ?>
<?php get_template_part( 'template-parts/template-part', 'content' ); ?>
<!-- start content container -->
<div class="row">
	<div class="col-md-<?php bulk_main_content_width_columns(); ?>">
		<div class="main-content-page">
			<div class="error-template text-center">
				<h1><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'bulk' ); ?></h1>
				<p class="error-details">
					<?php esc_html_e( 'Looks like nothing was found at this location. Want to try a search?', 'bulk' ); ?>
				</p>
				<div class="error-actions">
					<?php get_search_form(); ?>    
				</div>
			</div>
		</div>
	</div>

	<?php get_sidebar( 'right' ); ?>

</div>
<!-- end content container -->

<?php get_footer(); ?>
