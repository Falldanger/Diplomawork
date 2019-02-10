var g_gmpMap = null
,	g_gmpMapMarkersIdsAdded = []	// Markers, added for map
,	g_gmpMapShapesIdsAdded = []	// Shapes, added for map
,	g_gmpEditMap = false	// Adding or editing map
,	g_gmpMapFormChanged = false
,	g_gmpMarkerTitleColorTimeoutSet = false
,	g_gmpMarkerTitleColorLast = ''
,	g_gmpMarkerBgColorTimeoutSet = false
,	g_gmpMapAuthorizationFailWnd = false
,	g_gmpIsNeedTriggerZoomTypeAdmin = false;
window.onbeforeunload = function(){
	// If there are at lease one unsaved form - show message for confirnation for page leave
	if(_gmpIsMapFormChanged()) {
		return 'You have unsaved changes in Map form. Are you sure want to leave this page?';
	}
	if(_gmpIsMarkerFormChanged()) {
		return 'You have unsaved changes in Marker form. Are you sure want to leave this page?';
	}
	if(GMP_DATA.isPro && _gmpIsShapeFormChanged()) {
		return 'You have unsaved changes in Figure form. Are you sure want to leave this page?';
	}
	if(GMP_DATA.isPro && _gmpIsHeatmapFormChanged()) {
		return 'You have unsaved changes in Heatmap form. Are you sure want to leave this page?';
	}
};
// Right sidebar height re-calc
jQuery(window).bind('resize', _gmpResizeRightSidebar);
jQuery(window).bind('orientationchange', _gmpResizeRightSidebar);

jQuery(document).ready(function(){
	var propTabs = jQuery('#gmpMapPropertiesTabs')
	,	$contactFormsListWnd = jQuery('#gmpInsertToContactFormWnd')
	,	contactFormBtn = jQuery('#gmpInsertToContactForm')
	,	mapMainBtns = jQuery('#gmpMapMainBtns')
	,	markerMainBtns = jQuery('#gmpMarkerMainBtns')
	,	shapeMainBtns = jQuery('#gmpShapeMainBtns')
	,	heatmapMainBtns = jQuery('#gmpHeatmapMainBtns')
	,	markerList = jQuery('#gmpMarkerList')
	,	shapeList = jQuery('#gmpShapeList')
	,	rightStickyBar = jQuery('#gmpMapRightStickyBar');

	propTabs.wpTabs({
		change: function(selector) {
			switch(selector) {
				case '#gmpMarkerTab':
					if(!GMP_DATA.isPro) {
						rightStickyBar.show();
					}
					mapMainBtns.hide();
					markerMainBtns.show();
					shapeMainBtns.hide();
					heatmapMainBtns.hide();
					markerList.show();
					shapeList.hide();
					break;
				case '#gmpShapeTab':
					if(GMP_DATA.isPro) {
						mapMainBtns.hide();
						markerMainBtns.hide();
						shapeMainBtns.show();
						heatmapMainBtns.hide();
						markerList.hide();
						shapeList.show();
					} else {
						rightStickyBar.hide();
					}
					break;
				case '#gmpHeatmapTab':
					if(!GMP_DATA.isPro) {
						rightStickyBar.hide();
					}
					mapMainBtns.hide();
					markerMainBtns.hide();
					shapeMainBtns.hide();
					heatmapMainBtns.show();
					markerList.hide();
					shapeList.hide();
					break;
				case '#gmpMapTab': default:
					if(!GMP_DATA.isPro) {
						rightStickyBar.show();
					}
					mapMainBtns.show();
					markerMainBtns.hide();
					shapeMainBtns.hide();
					heatmapMainBtns.hide();
					markerList.show();
					shapeList.hide();
					break;
			}
		}
	});
	propTabs.show();

	$contactFormsListWnd.dialog({
		modal:    true
	,	autoOpen: false
	,	width: 540
	,	height: 'auto'
	,	buttons:  {
			Cancel: function() {
				$contactFormsListWnd.dialog('close');
			}
		,	Select: function() {
				var formSelect = $contactFormsListWnd.find('select[name="contact_form"]');

				if(formSelect.length && typeof(gmpContactFormEditUrl) != 'undefined') {
					var id = formSelect.val();

					window.open(gmpContactFormEditUrl + '&id=' + id + '&map_id=' + g_gmpMap.getId() + '#cfsFormFields', '_blank');
					$contactFormsListWnd.dialog('close');
				}
			}
		}
	,	open: function() {
			if(!$contactFormsListWnd.find('select[name="contact_form"]').length) {
				$contactFormsListWnd.next('.ui-dialog-buttonpane').find('button:last-child').hide();
			}
		}
	});
	contactFormBtn.click(function(){
		$contactFormsListWnd.dialog('open');
		return false;
	});

	jQuery('#membershipPropEnable').on('ifChanged', function() {
		if(jQuery('#membershipPropEnable:checked').length) {
			jQuery('#membershipHiddenEnable').val('1');
		} else {
			jQuery('#membershipHiddenEnable').val('0');
		}

	});

	// Preview map definition
	gmpMainMap = typeof(gmpMainMap) === 'undefined' ? null : gmpMainMap;
	var previewMapParams = {}
	,	additionalData = {};

	if(gmpMainMap) {
		previewMapParams = gmpMainMap.params;
		additionalData.markerGroups = typeof(gmpMainMap.marker_groups) != 'undefined' ? gmpMainMap.marker_groups : [];
		g_gmpEditMap = true;
	}
	previewMapParams.view_id = jQuery('#gmpViewId').val();
	if(previewMapParams.enable_custom_map_controls == 1) {
		gmpAddCustomControlsOptions();
	}
	g_gmpMap = new gmpGoogleMap('#gmpMapPreview', previewMapParams, additionalData);
	if(!g_gmpEditMap) {
		jQuery('#gmpMapForm input[name="map_opts[map_center][coord_x]"]').val(g_gmpMap.getCenter().lat());
		jQuery('#gmpMapForm input[name="map_opts[map_center][coord_y]"]').val(g_gmpMap.getCenter().lng());
	}
	/*if(gmpMainMap && gmpMainMap.markers) {
		gmpRefreshMapMarkers(g_gmpMap, gmpMainMap.markers);
	}*/
	// Map saving form
	jQuery('#gmpMapForm').submit(function(){
		var currentId = gmpGetCurrentId()
		,	firstTime = currentId ? false : true;

		//jQuery(this).find('input[name="map_opts[map_center][coord_x]"]').val(g_gmpMap.getCenter().lat());
		//jQuery(this).find('input[name="map_opts[map_center][coord_y]"]').val(g_gmpMap.getCenter().lng());
		//jQuery(this).find('[name="map_opts[zoom]"]').val(g_gmpMap.getZoom());
		var $currMapForm = jQuery(this);
		$currMapForm.sendFormGmp({
			btn: '#gmpMapSaveBtn'
		,	appendData: {add_marker_ids: g_gmpMapMarkersIdsAdded, add_shape_ids: g_gmpMapShapesIdsAdded}
		,	onSuccess: function(res) {
				if(!res.error) {
					if(res.data.map_id) {
						jQuery('#gmpMapForm input[name="map_opts[id]"]').val( res.data.map_id );

						// Update Markers table link
						var mrParams = URLToArray(gmpMarkersTblDataUrl)
						,	newMarkersTblUrl = gmpMarkersTblDataUrl.substring(0, gmpMarkersTblDataUrl.indexOf('?') + 1);

						mrParams['map_id'] = res.data.map_id;
						mrParams = ArrayToURL(mrParams);
						newMarkersTblUrl += mrParams;
						jQuery("#gmpMarkersListGrid").jqGrid('setGridParam', { url: newMarkersTblUrl });

						// Update Shapes table link
						if(GMP_DATA.isPro) {
							var shParams = URLToArray(gmpShapesTblDataUrl)
							,	newShapesTblUrl = gmpShapesTblDataUrl.substring(0, gmpShapesTblDataUrl.indexOf('?') + 1);

							shParams['map_id'] = res.data.map_id;
							shParams = ArrayToURL(shParams);
							newShapesTblUrl += shParams;
							jQuery("#gmpShapesListGrid").jqGrid('setGridParam', { url: newShapesTblUrl });
						}
					}
					if(firstTime) {
						gmpCheckShortcode();
						if (res.data.edit_url) {
							setBrowserUrl( res.data.edit_url );
							jQuery('.supsystic-main-navigation-list li').removeClass('active');
							jQuery('.supsystic-main-navigation-list li[data-tab-key="gmap"]').addClass('active');
						}
						g_gmpMapMarkersIdsAdded = [];
						g_gmpMapShapesIdsAdded = [];
						gmpMainMap = res.data.map;
						// #227
                        // window.location.reload();
					}
					if(_gmpIsMarkerFormChanged() && jQuery('#gmpMarkerForm input[name="marker_opts[title]"]').val() != '') {
						jQuery('#gmpMarkerForm').submit();
					}
					// Maybe here should be the saving of shape and heatmap forms
					_gmpUnchangeMapForm();
					$currMapForm.trigger('gmpSaved');
				}
			}
		});
		return false;
	});
	jQuery('#gmpCopyTextCodeExamples').change(function(){
		gmpCheckShortcode();
	});
	jQuery('#gmpMapSaveBtn').click(function(){
		jQuery('#gmpMapForm').submit();
		return false;
	});
	jQuery('#gmpMapDeleteBtn').click(function(){
		var mapId = parseInt( jQuery('#gmpMapForm input[name="map_opts[id]"]').val() );
		if(mapId) {
			if(confirm(toeLangGmp('Are you sure want to delete current map?'))) {
				jQuery.sendFormGmp({
					btn: this
				,	data: {mod: 'gmap', action: 'remove', id: mapId}
				,	onSuccess: function(res) {
						if(!res.error) {
							toeRedirect(gmpMapsListUrl);
						}
					}
				});
			}
		}
		return false;
	});
	// Check - should we show shortcode block or not
	gmpCheckShortcode();
	// Extended options block
	jQuery('#gmpExtendOptsBtn').click(function(){
		jQuery('#gmpExtendOptsBtnShell').slideUp( g_gmpAnimationSpeed );
		jQuery('#gmpExtendOptsShell').slideDown( g_gmpAnimationSpeed );
		return false;
	});
	// Map type control style
	jQuery('#gmpMapForm select[name="map_opts[type_control]"]').change(function(){
		var newType = jQuery(this).val();
		if(typeof(google.maps.MapTypeControlStyle[ newType ]) !== 'undefined') {
			var mapTypeControlOptions = g_gmpMap.get('mapTypeControlOptions') || {};
			mapTypeControlOptions.style = google.maps.MapTypeControlStyle[ newType ];
			g_gmpMap.set('mapTypeControlOptions', mapTypeControlOptions).set('mapTypeControl', true);
		} else {
			g_gmpMap.set('mapTypeControl', false);
		}
	});
	// Map zoom control style
	jQuery('#gmpMapForm select[name="map_opts[zoom_control]"]').change(function(e){
		if(jQuery('#gmpMapForm input[name="map_opts[enable_custom_map_controls]"]').val() == 1) {
			e.stopPropagation();
			var $zoomDisableMsg = jQuery('#gmpDefaultZoomDisable').dialog({
				modal:    true
			,	autoOpen: false
			,	width:	540
			,	height: 150
			});
			$zoomDisableMsg.dialog('open');
			return false;
		}
		var newType = jQuery(this).val();
		if(typeof(google.maps.ZoomControlStyle[ newType ]) !== 'undefined') {
			var zoomControlOptions = g_gmpMap.get('zoomControlOptions') || {};
			zoomControlOptions.style = google.maps.ZoomControlStyle[ newType ];
			g_gmpMap.set('zoomControlOptions', zoomControlOptions).set('zoomControl', true);
		} else {
			g_gmpMap.set('zoomControl', false);
		}
	});
	// Map street view control
	jQuery('#gmpMapForm input[name="map_opts[street_view_control]"]').change(function(){
		// Remember - that this is not actually checkbox, we detect hidden field value here, @see htmlGmp::checkboxHiddenVal()
		if(parseInt(jQuery(this).val())) {
			g_gmpMap.set('streetViewControl', true);
		} else {
			g_gmpMap.set('streetViewControl', false);
		}
	});
	// Map pan view control
	jQuery('#gmpMapForm input[name="map_opts[pan_control]"]').change(function(){
		// Remember - that this is not actually checkbox, we detect hidden field value here, @see htmlGmp::checkboxHiddenVal()
		if(parseInt(jQuery(this).val())) {
			g_gmpMap.set('panControl', true);
		} else {
			g_gmpMap.set('panControl', false);
		}
	});
	// Map overview control style
	jQuery('#gmpMapForm select[name="map_opts[overview_control]"]').change(function(){
		var newType = jQuery(this).val();
		if(newType !== 'none') {
			g_gmpMap.set('overviewMapControlOptions', {
				opened: newType === 'opened' ? true : false
			}).set('overviewMapControl', true);
		} else {
			g_gmpMap.set('overviewMapControl', false);
		}
	});
	// Is map draggable
	jQuery('#gmpMapForm input[name="map_opts[draggable]"]').change(function(){
		// Remember - that this is not actually checkbox, we detect hidden field value here, @see htmlGmp::checkboxHiddenVal()
		if(parseInt(jQuery(this).val())) {
			g_gmpMap.set('draggable', true);
		} else {
			g_gmpMap.set('draggable', false);
		}
	});
	// Enable Double Click to zoom
	jQuery('#gmpMapForm input[name="map_opts[dbl_click_zoom]"]').change(function(){
		// Remember - that this is not actually checkbox, we detect hidden field value here, @see htmlGmp::checkboxHiddenVal()
		if(parseInt(jQuery(this).val())) {
			g_gmpMap.set('disableDoubleClickZoom', false);
		} else {
			g_gmpMap.set('disableDoubleClickZoom', true);
		}
	});
	// Mouse zoom enabling
	jQuery('#gmpMapForm input[name="map_opts[mouse_wheel_zoom]"]').change(function(){
		// Remember - that this is not actually checkbox, we detect hidden field value here, @see htmlGmp::checkboxHiddenVal()
		if(parseInt(jQuery(this).val())) {
			g_gmpMap.set('scrollwheel', true);
		} else {
			g_gmpMap.set('scrollwheel', false);
		}
	});
	// Map center
	jQuery('#gmpMapForm [name="map_opts[map_center][address]"]').mapSearchAutocompleateGmp({
		msgEl: ''
	,	onSelect: function(item, event, ui) {
			if(item) {
				jQuery('#gmpMapForm input[name="map_opts[map_center][coord_x]"]').val(item.lat);
				jQuery('#gmpMapForm input[name="map_opts[map_center][coord_y]"]').val(item.lng);
				g_gmpMap.setCenter(item.lat, item.lng);
			}
		}
	});
	jQuery('#gmpMapForm [name="map_opts[map_center][coord_x]"], #gmpMapForm [name="map_opts[map_center][coord_y]"]').on('change', function() {
		var lat = jQuery.trim(jQuery('#gmpMapForm [name="map_opts[map_center][coord_x]"]').val())
		,	lng = jQuery.trim(jQuery('#gmpMapForm [name="map_opts[map_center][coord_y]"]').val());
		g_gmpMap.setCenter(lat, lng);
	});
	google.maps.event.addListener(g_gmpMap.getRawMapInstance(), 'dragend', jQuery.proxy(function(){
		var center = g_gmpMap.getCenter();
		jQuery('#gmpMapForm input[name="map_opts[map_center][coord_x]"]').val(center.lat());
		jQuery('#gmpMapForm input[name="map_opts[map_center][coord_y]"]').val(center.lng());
	}, this));
	// Map zoom
	jQuery('#gmpMapForm [name="map_opts[zoom_type]"]').change(function(){
		var value = jQuery(this).val()
		,	zoomLevelOpt = jQuery('#gmpMapForm #zoom_type_options .zoom_level');

		switch(value) {
			case 'zoom_level':
				zoomLevelOpt.show(300);
				break;
			case 'fit_bounds':
				zoomLevelOpt.hide(300);
				break;
			default:
				break;
		}
		g_gmpMap.setParam('zoom_type', value);
		if(g_gmpIsNeedTriggerZoomTypeAdmin) {
			g_gmpMap.applyZoomTypeAdmin();
		}
	}).trigger('change');
	g_gmpIsNeedTriggerZoomTypeAdmin = true;	// To prevent trigger applyZoomTypeAdmin by .trigger('change')
	jQuery('#gmpMapForm [name="map_opts[zoom]"]').change(function(){
		g_gmpMap.setZoom(jQuery(this).val());
	});
	google.maps.event.addListener(g_gmpMap.getRawMapInstance(), 'zoom_changed', jQuery.proxy(function(){
		jQuery('#gmpMapForm [name="map_opts[zoom]"]').val(g_gmpMap.getZoom());
	}, this));
	// Map type
	jQuery('#gmpMapForm select[name="map_opts[map_type]"]').change(function(){
		var newType = jQuery(this).val();
		if(typeof(google.maps.MapTypeId[ newType ]) !== 'undefined') {
			g_gmpMap.set('mapTypeId', google.maps.MapTypeId[ newType ]);
		}
	});
	// Map stylization
	jQuery('#gmpMapForm select[name="map_opts[map_stylization]"]').change(function(){
		var newType = jQuery(this).val();

		// Common styles go first
		if(newType !== 'none' && typeof(gmpAllStylizationsList[ newType ]) !== 'undefined') {
			g_gmpMap.set('styles', gmpAllStylizationsList[ newType ]);
		} else {
			g_gmpMap.set('styles', false);
		}
		// Then we apply other styles options
		if(GMP_DATA.isPro) {
			gmpStylesToggle(g_gmpMap, 'hide_poi');
			gmpStylesToggle(g_gmpMap, 'hide_countries');
		}
	});
	// Map Clasterization
	jQuery('#gmpMapForm select[name="map_opts[marker_clasterer]"]').change(function(){
		var newType = jQuery(this).val();
		if(newType !== 'none' && newType) {
			g_gmpMap.enableClasterization( newType );
			gmpSwitchClustererSubOpts(newType);
		} else {
			g_gmpMap.disableClasterization();
			gmpSwitchClustererSubOpts('none');
		}
	});
	gmpSwitchClustererSubOpts(jQuery('#gmpMapForm select[name="map_opts[marker_clasterer]"]').val());
	jQuery('#gmpUploadClastererIconBtn').click(function(e){
		var custom_uploader;
		e.preventDefault();
		//If the uploader object has already been created, reopen the dialog
		if (custom_uploader) {
			custom_uploader.open();
			return;
		}
		//Extend the wp.media object
		custom_uploader = wp.media.frames.file_frame = wp.media({
			title: 'Choose Image'
		,	button: {
				text: 'Choose Image'
			}
		,	multiple: false
		});
		//When a file is selected, grab the URL and set it as the text field's value
		custom_uploader.on('select', function(){
			var attachment = custom_uploader.state().get('selection').first().toJSON()
			,	iconPrevImg = jQuery('#gmpMarkerClastererIconPrevImg')
			,	width  = 53
			,	height = 'auto';

			iconPrevImg.attr('src', attachment.url);
			width = document.getElementById('gmpMarkerClastererIconPrevImg').naturalWidth;
			height = document.getElementById('gmpMarkerClastererIconPrevImg').naturalHeight;
			gmpUpdateClusterIcon(attachment.url, width, height);
		});
		//Open the uploader dialog
		custom_uploader.open();
	});
	jQuery('#gmpDefaultClastererIconBtn').click(function(e) {
		e.preventDefault();
		var defIconUrl = GMP_DATA.modPath + 'gmap/img/m1.png';
		jQuery('#gmpMarkerClastererIconPrevImg').attr('src', defIconUrl);
		gmpUpdateClusterIcon(defIconUrl, 53, 52);
	});
	jQuery('#gmpDefaultClastererGridSizeBtn').click(function(e) {
		e.preventDefault();
		jQuery('#gmpMarkerClastererSubOpts').find('#gmpMarkerClastererGridSize').val('60');
	});

	// Map KML layers
	jQuery('#gmpAddNewShapeBtn').click(function(e){
		if(GMP_DATA.isPro == '') {
			e.stopPropagation();
			var $proOptWnd = gmpGetMainPromoPopup();
			$proOptWnd.dialog('open');
			return false;
		}
	});
	jQuery('#gmpKmlAddFileRowBtn').click(function(e){
		if(GMP_DATA.isPro == '') {
			e.stopPropagation();
			var $proOptWnd = gmpGetMainPromoPopup();
			$proOptWnd.dialog('open');
			return false;
		}
	});
	jQuery('#gmpCurUserPosIconBtn').click(function(e){
		if(GMP_DATA.isPro == '') {
			e.stopPropagation();
			var $proOptWnd = gmpGetMainPromoPopup();
			$proOptWnd.dialog('open');
			return false;
		}
	});
	jQuery('#gmpUploadCurUserPosIconBtn').click(function(e){
		if(GMP_DATA.isPro == '') {
			e.stopPropagation();
			var $proOptWnd = gmpGetMainPromoPopup();
			$proOptWnd.dialog('open');
			return false;
		}
	});
	// Map Marker Info Window width and height units
	jQuery('#gmpMapForm #map_opts_enable_google_kml_api').change(function(){
		if(this.checked) {
			jQuery('#gmpMapForm #map_opts_enable_kml_filter_text').closest('div').addClass('gmpHidden');
		}else{
			jQuery('#gmpMapForm #map_opts_enable_kml_filter_text').closest('div').removeClass('gmpHidden');
		}
	});
	// Map Marker Info Window width and height units
	jQuery('#gmpMapForm select[name="map_opts[marker_infownd_type]"]').change(function(){
		gmpToggleSubOptionsByDataParam(jQuery(this).val());
	});
	gmpToggleSubOptionsByDataParam(g_gmpMap.getParam('marker_infownd_type'));
	jQuery('#gmpMapForm input[name="map_opts[marker_infownd_width_units]"]').change(function(){
		var infoWndWidthInput = jQuery('#gmpMapForm input[name="map_opts[marker_infownd_width]"]')
		,	infoWndWidthLabel = jQuery('#gmpMapForm').find('[for="map_opts_marker_infownd_width_units"]');

		if(jQuery(this).val() == 'px' && jQuery(this).val()) {
			infoWndWidthLabel.css('top', '7px');
			infoWndWidthInput.show();
		} else {
			infoWndWidthLabel.css('top', '0px');
			infoWndWidthInput.hide();
		}
	});
	jQuery('#gmpMapForm input[name="map_opts[marker_infownd_height_units]"]').change(function(){
		var infoWndHeightInput = jQuery('#gmpMapForm input[name="map_opts[marker_infownd_height]"]')
		,	infoWndHeightLabel = jQuery('#gmpMapForm').find('[for="map_opts_marker_infownd_height_units"]');

		if(jQuery(this).val() == 'px' && jQuery(this).val()) {
			infoWndHeightLabel.css('top', '7px');
			infoWndHeightInput.show();
		} else {
			infoWndHeightLabel.css('top', '0px');
			infoWndHeightInput.hide();
		}
	});
	jQuery('#gmpMapForm select[name="map_opts[zoom_min]"]').change(function(){
		var minZoom = jQuery(this).val()
		,	maxZoom = jQuery('#gmpMapForm select[name="map_opts[zoom_max]"]').val();
		g_gmpMap.setParam('zoom_min', minZoom);
		g_gmpMap.setParam('zoom_max', maxZoom);
		g_gmpMap._setMinZoomLevel();
		g_gmpMap._setMaxZoomLevel();
		g_gmpMap._fixZoomLevel();
	});
	jQuery('#gmpMapForm select[name="map_opts[zoom_max]"]').change(function(){
		var minZoom = jQuery('#gmpMapForm select[name="map_opts[zoom_min]"]').val()
		,	maxZoom = jQuery(this).val();
		g_gmpMap.setParam('zoom_min', minZoom);
		g_gmpMap.setParam('zoom_max', maxZoom);
		g_gmpMap._setMinZoomLevel();
		g_gmpMap._setMaxZoomLevel();
		g_gmpMap._fixZoomLevel();
	});
	jQuery('#gmpMapForm input[name="map_opts[adapt_map_to_screen_height]"]').change(function(){
		if(parseInt(jQuery(this).val())) {
			jQuery('.gmpMainHeightOpts').hide(300);
		} else {
			jQuery('.gmpMainHeightOpts').show(300);
		}
	});
	jQuery('#gmpMapForm input[name="map_opts[adapt_map_to_screen_height]"]').trigger('change');
	// Map Markers List selection
	gmpInitMapMarkersListWnd();
	// Ask before leave page without saving
	jQuery('#gmpMapForm').find('input,select,textarea').change(function(){
		_gmpChangeMapForm();
	});
	//Show 'Close description by mouse hover' checkbox only if 'Show description by mouse hover' if checked
	jQuery('#gmpMarkerTab input[name="marker_opts[params][description_mouse_hover]"]').change(function(){
		if(jQuery(this).prop('checked') === true)
			gmpShowCloseDescriptionCheckbox();
		else
			gmpHideCloseDescriptionCheckbox();
	});
	jQuery(".supsystic-panel .tooltipstered").removeAttr("title");
});
function gmpShowCloseDescriptionCheckbox() {
	jQuery('#marker_opts_description_mouse_leave').show();
}
function gmpHideCloseDescriptionCheckbox() {
	//uncheck if checked.
	jQuery('#gmpMarkerTab input[name="marker_opts[params][description_mouse_leave]"]').prop('checked', false).iCheck('update');
	//hide the element
	jQuery('#marker_opts_description_mouse_leave').hide();
}
function gmpCheckShortcode() {
	var currentId = gmpGetCurrentId();
	if(currentId) {
		var codeType = jQuery('#gmpCopyTextCodeExamples').val();
		jQuery('.gmpMapShortCodeShell').val(codeType == 'shortcode' ? '['+ gmpMapShortcode+ ' id="' + currentId+ '"]' : '<?php echo do_shortcode(\'['+ gmpMapShortcode+ ' id="'+ currentId+ '"]\')?>');
		if(GMP_DATA.isPro) {
			jQuery('.gmpMapMarkerFormCodeShell').val('['+ gmpMapShortcode + '_marker_form map_id="'+ currentId+ '"]');
			jQuery('.gmpPlacesToolbarCodeShell').val('['+ gmpMapShortcode + '_places_toolbar map_id="'+ currentId+ '"]');
		}
		gmpResetCopyTextCodeFields('#shortcodeCode');
	}
}
function gmpGetCurrentId() {
	return parseInt( jQuery('#gmpMapForm input[name="map_opts[id]"]').val() );
}
function gmpUpdateClusterIcon(url, width, height) {
	jQuery('input[name="map_opts[marker_clasterer_icon]"]').val(url);
	jQuery('input[name="map_opts[marker_clasterer_icon_width]"]').val(width);
	jQuery('input[name="map_opts[marker_clasterer_icon_height]"]').val(height);
	g_gmpMap
		.setParam('marker_clasterer_icon', url)
		.setParam('marker_clasterer_icon_width', width)
		.setParam('marker_clasterer_icon_height', height)
		.enableClasterization(g_gmpMap.getParam('marker_clasterer'));
}
function gmpInitMapMarkersListWnd() {
	var wndWidth = jQuery(window).width()
	,	wndHeight = jQuery(window).height()
	,	normWidth = 740
	,	normHeight = 540
	,	popupWidth = wndWidth > normWidth ? normWidth : wndWidth - 20
	,	popupHeight = wndHeight < normHeight ? normHeight : wndHeight - 70;

	jQuery('#gmpMarkersListWnd').find('.gmpMmlElement').css('max-width', popupWidth - 20);

	var $markersListWnd = jQuery('#gmpMarkersListWnd').dialog({
		modal:    true
	,	autoOpen: false
	,	width: popupWidth
	,	height: popupHeight
	,	open: function() {
			jQuery('.ui-widget-overlay').bind('click', function() {
				$markersListWnd.dialog('close');
			});
		}
	});
	jQuery('#gmpMapMarkersListBtn').click(function(){
		$markersListWnd.dialog('open');
		return false;
	});
	if(!GMP_DATA.isPro) {
		jQuery('.gmpMmlElement').click(function(){
			var url = jQuery(this).find('.gmpMmlApplyBtn').attr('href');
			window.open( url );
			return false;
		});
	}
}
// Map form check change actions
function _gmpIsMapFormChanged() {
	return g_gmpMapFormChanged;
}
function _gmpChangeMapForm() {
	g_gmpMapFormChanged = true;
}
function _gmpUnchangeMapForm() {
	g_gmpMapFormChanged = false;
}
function _gmpResizeRightSidebar(container) {
	jQuery(window).trigger('scroll');

	var listContainers = container && container instanceof jQuery? container : jQuery('#gmpMarkersListGrid, #gmpShapeListGrid')
	,	rightBar = jQuery('#gmpMapRightStickyBar')
	,	wnd = jQuery(window)
	,	wndWd = wnd.width()
	,	wndHt = wnd.height()
	,	rightBarWd,	newHeight;

	rightBar.width( jQuery('.supsistic-half-side-box').width());
	rightBarWd = rightBar.outerWidth();
	newHeight = 400;

	if(wndWd > 991) {
		newHeight = wndHt
		- jQuery('#wpadminbar').outerHeight()
		- jQuery('#gmpMapPreview').outerHeight()
		- jQuery('.gmpControlBtns:first').outerHeight()
		- jQuery('.ui-jqgrid-htable:first').outerHeight();


	}
	listContainers.each(function() {
		var self = jQuery(this);

		if(self.attr('id') == 'gmpMarkersListGrid') {
			newHeight = newHeight - jQuery('#gmpMarkersSearchInput').outerHeight();
		}
		newHeight = newHeight > 250 ? newHeight : 250;

		self.jqGrid('setGridWidth', rightBarWd);
		self.jqGrid('setGridHeight', newHeight);
	});
}
//function _gmpResizeRightSidebar() {
//	var listContainerId = jQuery('#gmpMarkerList').is(':visible') ? '#gmpMarkerList' : '#gmpShapeList'
//	,	wndWd = jQuery(window).width()
//	,	wndHt = jQuery(window).height()
//	,	preview = jQuery('#gmpMapPreview')
//	,	markerList = jQuery('#gmpMarkersListGrid')
//	,	shapesList = jQuery('#gmpShapesListGrid')
//	,	rightBar = jQuery('#gmpMapRightStickyBar');
//
//	rightBar.width( jQuery('.supsistic-half-side-box').width());
//	jQuery(window).trigger('scroll');
//
//	var rightBarWd = rightBar.outerWidth()
//	,	rightBarHt = rightBar.outerHeight()
//	,	rightBarTop = parseInt(rightBar.css('top'));
//
//	markerList.jqGrid('setGridWidth', rightBarWd);
//	shapesList.jqGrid('setGridWidth', rightBarWd);
//
//	if(!rightBarTop) {
//		var rightBarPos = rightBar.offset();
//		wndHt -= rightBarPos.top + 32;
//	}
//	if(rightBarHt > wndHt && wndWd > 991) {
//		//jQuery('#' + listCintainerId).css('overflow', 'scroll');
//		var minMapHt = 350
//		,	minMarkersTblHt = 300
//		,	d = rightBarHt - wndHt
//		,	mListHt = jQuery(listContainerId).outerHeight()
//		,	mapPreviewHt = preview.outerHeight()
//		,	markersListHeaderHeight = jQuery(listContainerId).find('table thead').height()
//		,	mainButtonsDiv = jQuery('#gmpMapMainBtns').height()
//		,	newHeight = '';
//		if(mListHt - d >= minMarkersTblHt) {	// Try to minimazi it using just markers list
//			newHeight = mListHt - d - markersListHeaderHeight * 2.5;
//			markerList.jqGrid('setGridHeight', newHeight + 'px');
//			shapesList.jqGrid('setGridHeight', newHeight + 'px');
//			//markerList.height( mListHt - d );
//			return;
//		}
//		var canDecreaseMList = mListHt - minMarkersTblHt;
//		if(canDecreaseMList > 0) {
//			newHeight = mListHt - canDecreaseMList - markersListHeaderHeight - mainButtonsDiv;
//			markerList.jqGrid('setGridHeight', newHeight + 'px');
//			shapesList.jqGrid('setGridHeight', newHeight + 'px');
//			//markerList.css('height', mListHt - canDecreaseMList);
//			d -= canDecreaseMList;
//		}
//		if(d <= 0) return;
//
//		if(mapPreviewHt - d >= minMapHt) {
//			preview.height( mapPreviewHt - d - markersListHeaderHeight);
//			return;
//		}
//		var canDecreaseMapPrev = mapPreviewHt - minMapHt;
//		if(canDecreaseMapPrev > 0) {
//			preview.height( mapPreviewHt - canDecreaseMapPrev - markersListHeaderHeight );
//			d -= canDecreaseMapPrev;
//		}
//		if(d <= 0) return;
//	} else {
//		var mListHt = jQuery(listContainerId).outerHeight()
//		,	newHeight = mListHt + wndHt - rightBarHt - jQuery(listContainerId).find('table thead').height() * 2.5;
//		if(newHeight > mListHt) {
//			markerList.jqGrid('setGridHeight', newHeight + 'px');
//			shapesList.jqGrid('setGridHeight', newHeight + 'px');
//		}
//	}
//}
function gmpAddCustomControlsOptions() {
	var customMapControls = jQuery('#map_optsenable_custom_map_controls_check').prop('checked');

	if (customMapControls) {
		jQuery('#custom_controls_options').show(300);
        //#229
        jQuery('select[name="map_opts[zoom_control]"]').closest('tr').hide(300);
        jQuery('[name="map_opts[custom_controls_position]"]').trigger('change');
	} else {
		jQuery('#custom_controls_options').hide(300);
		//#229
        jQuery('select[name="map_opts[zoom_control]"]').closest('tr').show(300);
	}
}
function gmpSwitchClustererSubOpts(clusterType) {
	if (clusterType == 'none') {
		jQuery('#gmpMarkerClastererSubOpts').hide();
	} else {
		jQuery('#gmpMarkerClastererSubOpts').show();
	}
}
function gmpToggleSubOptionsByDataParam(value) {
	var subOpts = jQuery('#gmpMarkerInfoWndTypeSubOpts .gmpSubOpt');

	subOpts.filter('[data-type]').hide();
	subOpts.filter('[data-type="' + value + '"]').show();
}
function gmpWpColorpickerUpdateTitlesColor(color) {
	g_gmpMarkerTitleColorTimeoutSet = false;
	var styleObj = jQuery('#gmpHardcodeMapTitleStl');
	if(!styleObj || !styleObj.size()) {
		styleObj = jQuery('<style type="text/css" id="gmpHardcodeMapTitleStl" />').appendTo('head');
	}
	styleObj.html('.gmpInfoWindowtitle { color: '+ g_gmpMarkerTitleColorLast+ ' !important; }');
}
function wpColorPicker_map_optsmarker_title_color_change(event, ui) {
	g_gmpMarkerTitleColorLast = ui.color.toString();
	if(!g_gmpMarkerTitleColorTimeoutSet) {
		setTimeout(function(){
			gmpWpColorpickerUpdateTitlesColor();
		}, 500);
		g_gmpMarkerTitleColorTimeoutSet = true;
	}
}
function wpColorPicker_map_optscustom_controls_bg_color_change(event, ui) {
	if(!GMP_DATA.isPro) {
		jQuery('#gmpMapForm [name="map_opts[custom_controls_bg_color]"]').trigger('change');
	}
}
function wpColorPicker_map_optscustom_controls_txt_color_change(event, ui) {
	if(!GMP_DATA.isPro) {
		jQuery('#gmpMapForm [name="map_opts[custom_controls_txt_color]"]').trigger('change');
	}
}
function wpColorPicker_map_optsmarker_infownd_bg_color_change(event, ui) {
	var color = ui.color.toString();
	if(!g_gmpMarkerBgColorTimeoutSet) {
		setTimeout(function(){
			//Set param anyway for info window preview, opened before new marker will be saved
			g_gmpMap.setParam('marker_infownd_bg_color', color);
			changeInfoWndBgColor(g_gmpMap);
		}, 500);
		g_gmpMarkerBgColorTimeoutSet = true;
	}
}
// Common function for map PRO tabs
function gmpUnshiftButtons(btns) {
	for(var i in btns) {
		if(jQuery('#' + i).hasClass(btns[i]))
			jQuery('#' + i).trigger('click');
	}
}
// Global Google function for catch Map API Auth Errors
function gm_authFailure() {
	g_gmpMapAuthorizationFailWnd = jQuery('#gmpMapAuthorizationFailWnd');
	g_gmpMapAuthorizationFailWnd.dialog({
		modal:    true
	,	autoOpen: false
	,	width: 540
	,	height: 'auto'
	,	buttons: {
			Cancel: function() {
				g_gmpMapAuthorizationFailWnd.dialog('close');
			}
		}
	});
	g_gmpMapAuthorizationFailWnd.dialog('open');
}