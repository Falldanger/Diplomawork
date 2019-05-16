<?php if (metaslider_user_is_ready_for_notices()) {
    echo $this->notices->do_notice(false, 'header', true);
} ?>
			
<div class="metaslider-header">
	<div>
		<h1 class="wp-heading-inline metaslider-title">
			<img width=50 height=50 src="<?php echo METASLIDER_ADMIN_URL ?>images/metaslider_logo_large.png" alt="MetaSlider">
			MetaSlider
			<?php if (metaslider_pro_is_active()) echo ' Pro'; ?>
		</h1>
		<?php
			$new_slideshow_url = wp_nonce_url(admin_url("admin-post.php?action=metaslider_create_slider"), "metaslider_create_slider");
			$text = __('Add a New Slideshow', 'ml-slider');
			echo "<a href='{$new_slideshow_url}' id='create_new_tab' class='metaslider-add-new'>{$text}</a>";
		?>

	</div>
	<ul class='metaslider-links'>
		<li><a target="_blank" href="#">Documentation</a></li>
		<?php if (!metaslider_pro_is_installed()) { ?>
			<li><a href="<?php echo admin_url("admin.php?page=upgrade-metaslider"); ?>">Available add-ons</a></li>
			<li><a href="<?php echo metaslider_get_upgrade_link(); ?>">Upgrade now</a></li>
		<?php } ?>
	</ul>
	<?php 
		// TODO: find a good place to add a version number
		// echo 'Version ' . metaslider_version(); ?> <?php // if (metaslider_pro_is_active()) echo '/ Pro ' . metaslider_pro_version(); ?>
</div>