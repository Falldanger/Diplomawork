<?php if (!defined('ABSPATH')) die('No direct access.'); ?>
<div class="schedule_placeholder">
	<?php if (metaslider_pro_is_installed()) : ?>
		<h4 style="margin:0"><?php _e('Update or activate your pro add-on pack now to add a start/end date option to your slides', 'ml-slider'); ?></h4>
		<p><?php printf(_x('Have an expired license? Use coupon code %s for a %s discount', 'A coupon code and a discount amount', 'ml-slider'), '<strong>unlockscheduling</strong>', '20%'); ?></p>
		<a href="https://www.metaslider.com/documentation/i-have-the-pro-add-on-pack-why-isnt-this-feature-working" class="button button-primary" target="_blank"><?php _e('Why am I seeing this?', 'ml-slider'); ?></a>
	<?php else : ?>
		<h4><?php _e('Get the Pro add-on pack now to add a start/end date option to your slides', 'ml-slider'); ?></h4>
		<a href="<?php echo metaslider_get_upgrade_link(); ?>" class="button button-primary" target="_blank"><?php _e('Get it now!', 'ml-slider'); ?></a>
	<?php endif; ?>
</div>
