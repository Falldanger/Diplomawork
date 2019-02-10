<style type="text/css">
	.gmpAdminMainLeftSide {
		width: 56%;
		float: left;
	}
	.gmpAdminMainRightSide {
		width: <?php echo (empty($this->optsDisplayOnMainPage) ? 100 : 40)?>%;
		float: left;
		text-align: center;
	}
	#gmpMainOccupancy {
		box-shadow: none !important;
	}
</style>
<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<?php _e('Main page Go here!!!!', GMP_LANG_CODE)?>
		</div>
		<div style="clear: both;"></div>
	</div>
</section>