<?php
$isStatic = isset($this->currentMap['params']['is_static']) ? (int) $this->currentMap['params']['is_static'] : false;
//$popup = $this->currentMap['params']['map_display_mode'] == 'popup' ? true : false;

$viewId = $this->currentMap['view_id'];
$mapHtmlId = $this->currentMap['view_html_id'];
$mapPreviewClassname = @$this->currentMap['html_options']['classname'];
//$mapOptsClassname = $popup ? 'display_as_popup' : '';

if($this->markersDisplayType === 'slider_checkbox_table') {
	$mapsWrapperStart = "<div class='gmpLeft'>";
	$mapsWrapperEnd = "</div>";
	$filtersWrapperStart = "<div class='filterRight'>";
	$filtersWrapperEnd = "</div>";
} else {
	$mapsWrapperStart = "";
	$mapsWrapperEnd = "";
	$filtersWrapperStart = "";
	$filtersWrapperEnd = "";
}?>
<?php if($isStatic) { ?>
	<?php $canDrawStaticMap = (bool)(frameGmp::_()->getModule('supsystic_promo')->isPro() 
			&& frameGmp::_()->getModule('add_map_options') 
			&& method_exists(frameGmp::_()->getModule('add_map_options'), 'connectStaticMapCore'));
		/*	? frameGmp::_()->getModule('add_map_options')->generateStaticImgUrl($this->currentMap)
			: false;*/
		$title = $this->currentMap['title'];
		$error = '';
		if(!$canDrawStaticMap) {
			// Detailed error message
			if(!frameGmp::_()->getModule('supsystic_promo')->isPro()) {
				$error = __('This feature available in PRO version. You can get it <a href="" target="_blank">here</a>.', GMP_LANG_CODE);
			} else {
				// PRO version exists - but there are no such functionality there - need to update
				$error = __('You need to upgrade PRO plugin to latest version to use this feature', GMP_LANG_CODE);
			}
			$title = $error;
		}
	?>
	<?php if(is_user_logged_in() && !empty($error)) { ?>
		<b><?php echo $error;?></b>
	<?php }?>
	<img id="<?php echo $mapHtmlId; ?>" class="gmpMapImg gmpMapImg_<?php echo $viewId; ?>" 
		src="<?php echo GMP_IMG_PATH . 'gmap_preview.png'; ?>" 
		data-id="<?php echo $this->currentMap['id']; ?>" data-view-id="<?php echo $viewId; ?>" 
		title="<?php echo $title; ?>" alt="<?php echo $title; ?>" 
	/>
<?php } else  { ?>
	<div class="gmp_map_opts" id="mapConElem_<?php echo $viewId;?>"
		data-id="<?php echo $this->currentMap['id']; ?>" data-view-id="<?php echo $viewId;?>"
		<?php if(!empty($this->mbsIntegrating)) {
			echo 'data-mbs-gme-map="' . $this->currentMap['id'] . '" style="display:none;"';
		} else if(!empty($this->mbsMapId) && !empty($this->mbsMapInfo)) {
			echo "data-mbs-gme-map-id='" . $this->mbsMapId . "' data-mbs-gme-map-info='" . $this->mbsMapInfo . "'";
		}
		?>
	>
		<?php echo $mapsWrapperStart; ?>
		<div class="gmpMapDetailsContainer" id="gmpMapDetailsContainer_<?php echo $viewId ;?>">
			<i class="gmpKMLLayersPreloader fa fa-spinner fa-spin" aria-hidden="true" style="display: none;"></i>
			<div class="gmp_MapPreview <?php echo $mapPreviewClassname;?>" id="<?php echo $mapHtmlId ;?>"></div>
		</div>
		<?php echo $mapsWrapperEnd; ?>

		<?php echo $filtersWrapperStart; ?>
		<div class="gmpMapMarkerFilters" id="gmpMapMarkerFilters_<?php echo $viewId;?>">
			<?php dispatcherGmp::doAction('addMapFilters', $this->currentMap); ?>
		</div>
		<?php echo $filtersWrapperEnd; ?>

		<div class="gmpMapProControlsCon" id="gmpMapProControlsCon_<?php echo $viewId;?>">
			<?php dispatcherGmp::doAction('addMapBottomControls', $this->currentMap); ?>
		</div>
		<div class="gmpMapProDirectionsCon" id="gmpMapProDirectionsCon_<?php echo $viewId;?>" >
			<?php dispatcherGmp::doAction('addMapDirectionsData', $this->currentMap); ?>
		</div>
		<div class="gmpMapProKmlFilterCon" id="gmpMapProKmlFilterCon_<?php echo $viewId;?>" >
			<?php dispatcherGmp::doAction('addMapKmlFilterData', $this->currentMap); ?>
		</div>
		<div class="gmpSocialSharingShell gmpSocialSharingShell_<?php echo $viewId ;?>">
			<?php echo $this->currentMap['params']['ss_html'];?>
		</div>
		<div style="clear: both;"></div>
	</div>
<?php } ?>