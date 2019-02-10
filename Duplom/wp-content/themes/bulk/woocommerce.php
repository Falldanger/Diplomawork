<?php get_header(); ?>
<?php get_template_part( 'template-parts/template-part', 'content' ); ?>
<!-- start content container -->
<div class="row">   
	<article class="col-md-<?php bulk_main_content_width_columns(); ?>">  
        <div class="woocommerce">
			<?php do_action( 'bulk_generate_woo_breadcrumbs' ); ?> 
			<?php woocommerce_content(); ?>
        </div>
	</article>       
	<?php get_sidebar( 'right' ); ?>
</div>
<!-- end content container -->

<?php get_footer(); ?>

