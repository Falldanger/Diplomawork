<div class="error-template text-center">
	<h1><?php esc_html_e( 'Nothing found', 'bulk' ); ?></h1>
	<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

		<p>
			<?php 
			/* translators: %1$s link */
			printf( wp_kses( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'bulk' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'post-new.php' ) ) ); 
			?>
		</p>

	<?php elseif ( is_search() ) : ?>

		<p>
			<?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with different keywords.', 'bulk' ); ?>
		</p>
		<?php get_search_form(); ?>

	<?php else : ?>

		<p>
			<?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'bulk' ); ?>
		</p>
		<?php get_search_form(); ?>

	<?php endif; ?>
</div>
