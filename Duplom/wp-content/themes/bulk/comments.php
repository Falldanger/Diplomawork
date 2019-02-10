<?php
/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>
<?php if ( is_single() || is_page() ) : ?>
	<div class="comments-template">
		<?php if ( have_comments() ) : ?>
			<h4 id="comments">
				<?php 
				/* translators: %1$s: comment count number */
				printf( esc_html( _nx( 'One comment', '%1$s comments', get_comments_number(), 'comments title', 'bulk' ) ), number_format_i18n( get_comments_number() ) ); ?>
			</h4>
			<ul class="commentlist list-unstyled">
				<?php
				wp_list_comments();
				paginate_comments_links();

				if ( is_singular() ) {
					wp_enqueue_script( 'comment-reply' );
				}
				?>
			</ul>
			<?php
			comment_form();
		else :
			if ( comments_open() ) :
				comment_form();
			endif;
		endif;
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( !comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
			?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'bulk' ); ?></p>
			<?php
		endif;
		?>
	</div>
<?php endif; ?>
