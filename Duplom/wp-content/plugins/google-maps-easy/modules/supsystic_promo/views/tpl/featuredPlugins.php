<section id="supsystic-featured-plugins" class="supsystic-item supsystic-panel">
	<div class="supsysticPageBundleContainer container-fluid">
		<div class="bundle-text col-md-7 col-xs-12"><?php _e('Get plugins bundle today and save over 80%', GMP_LANG_CODE)?></div>
		<div class="bundle-btn col-md-5 col-xs-12">
			<a href="<?php echo $this->bundleUrl;?>" class="btn btn-full btn-revert hvr-shutter-out-horizontal" target="_blank">
				<?php _e('Check It out', GMP_LANG_CODE)?>
			</a>
		</div>
	</div>
	<hr />
	<?php foreach($this->pluginsList as $p) { ?>
		<div class="catitem col-md-4 col-sm-6 col-xs-12">
			<div class="download-product-item">
				<div class="dp-thumb text-center">
					<a href="<?php echo $p['url']?>" target="_blank">
						<img src="<?php echo $p['img']?>" class="img-responsive wp-post-image" alt="<?php echo $p['label']?>" />					
					</a>
				</div>
				<div class="dp-title">
					<a href="<?php echo $p['url']?>" target="_blank">
						<?php echo $p['label']?>
					</a>
				</div>
				<div class="dp-excerpt">
					<div class="dp-excerpt-wrapper">
						<?php echo $p['desc']?>
					</div>
				</div>
				<div class="dp-buttons">
					<a href="<?php echo $p['url']?>" target="_blank" class="btn btn-full hvr-shutter-out-horizontal">
						<?php _e('More info', GMP_LANG_CODE)?>
					</a>
					<a href="<?php echo $p['download']?>" target="_blank" class="btn btn-full btn-info hvr-shutter-out-horizontal">
						<?php _e('Download', GMP_LANG_CODE)?>
					</a>
				</div>
			</div>
		</div>
	<?php }?>
	<div style="clear: both;"></div>
</section>
