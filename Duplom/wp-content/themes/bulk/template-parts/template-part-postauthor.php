<div class="postauthor-container">			  
	<div class="postauthor-title">
		<h4 class="about">
			<?php esc_html_e( 'About the author', 'bulk' ); ?>
		</h4>
		<div class="">
			<span class="fn">
				<?php the_author_posts_link(); ?>
			</span>
		</div> 				
	</div>        	
	<div class="postauthor-content">	             						           
		<p>
			<?php the_author_meta( 'description' ) ?>
		</p>					
	</div>	 		
</div>
