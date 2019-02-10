var	g_gmpCurrentEditMarker = null
,	g_gmpMarkerFormChanged = false
,	g_gmpTinyMceMarkerEditorUpdateBinded = false
,   g_gmpGrid = jQuery('#gmpMarkersListGrid')
,	g_gmpGridData = null;
jQuery(document).ready(function(){
	// Build initial markers list
    g_gmpGrid.jqGrid({
		url: gmpMarkersTblDataUrl
	,	mtype: 'GET'
	,	datatype: 'json'
	,	colNames:[toeLangGmp('ID'), toeLangGmp('Icon'), toeLangGmp('Title'), toeLangGmp('Coords'), toeLangGmp('Actions')]
	,	colModel: [
			{ name: 'id', index: 'id', key: true, sortable: true, width: '90', align: 'center' }
		,	{ name: 'icon_img', index: 'icon_img', sortable: false, width: '70', align: 'center' }
		,	{ name: 'title', index: 'title', sortable: true, align: 'center' }
		,	{ name: 'coords', index: 'coords', sortable: false, width: '90', align: 'center' }
		,	{ name: 'actions', index: 'actions', sortable: false, width: '100', align: 'center' }
	]
	,	width: jQuery('#gmpMapRightStickyBar').width()
	,	height: 200
	//,	autowidth: true
	,	shrinkToFit: false
	,	sortname: 'sort_order'
	,	rowNum: 1000000000000
	,	viewrecords: true
	,	emptyrecords: toeLangGmp('You have no markers for now.')
	,	loadComplete: function(res) {
			if(g_gmpGridData === null)
				g_gmpGridData = res.rows;
			gmpRefreshMapMarkersList(res.rows);
			if(res.rows.length) {
				g_gmpMap.applyZoomTypeAdmin();	// Apply zoom type fit_bounds after all markers load in admin area
			}
			_gmpResizeRightSidebar(jQuery('#gmpMarkersListGrid'));
			jQuery('#gmpMarkersSearchInput').show();
		}
	}).jqGrid('sortableRows', {
		update: function (e, ui) {
			var markersList = jQuery('#gmpMarkersListGrid').jqGrid('getDataIDs');
			jQuery.sendFormGmp({
				data: { mod: 'gmap', action: 'resortMarkers', markers_list: markersList }
			,	onSuccess: function(res) {
					if(!res.error) {
						var sortOrder = jQuery('#gmpMarkersListGrid').jqGrid('getGridParam', 'sortorder');

						jQuery('#gmpMarkersListGrid').jqGrid('setGridParam', {
							sortname: 'sort_order',
							sortorder: sortOrder
						});
						jQuery('#gmpMarkersListGrid').trigger('reloadGrid');
					}
				}
			});
		}
	});

    // Search by markers name functionality
    jQuery("#gmpMarkersSearchInput").on('keyup', function (e) {

        var result = []
			, value = e.target.value
			, valueLength = value.length;

			if(valueLength === 0) {
				console.log('Value length == 0');
				result = g_gmpGridData;
			} else {
				result = g_gmpGridData.filter(function (item) {
					var result = item.title.substr(0, valueLength) == value;
					return result;
				});
			}

		g_gmpGrid.jqGrid('clearGridData');
		g_gmpGrid.jqGrid("setGridParam", { datatype: 'local', data: result });
		g_gmpGrid.trigger('reloadGrid');
    });
	// Markers form functionality
	jQuery('#gmpAddNewMarkerBtn').click(function(){
		var currentEditId = parseInt( jQuery('#gmpMarkerForm input[name="marker_opts[id]"]').val() );
		if(!currentEditId) {	// This was new marker
			var title = jQuery.trim( jQuery('#gmpMarkerForm input[name="marker_opts[title]"]').val() );
			if(title && title != '') {	// Save it if there was some required changes
				jQuery('#gmpMarkerForm').data('only-save', 1).submit();
			} else {
				var currentMarker = gmpGetCurrentMarker();
				if(currentMarker) {
					currentMarker.removeFromMap();
				}
			}
		}
		gmpOpenMarkerForm();
		// Add new marker - right after click on "Add new"
		_gmpCreateNewMapMarker();
		return false;
	});
	jQuery('#gmpSaveMarkerBtn').click(function(){
		jQuery('#gmpMarkerForm').submit();
		return false;
	});
	jQuery('#gmpMarkerDeleteBtn').click(function(){
		var markerTitle = jQuery('#gmpMarkerForm [name="marker_opts[title]"]').val();
		if(markerTitle && markerTitle != '') {
			markerTitle = '"'+ markerTitle+ '"';
		} else {
			markerTitle = 'current';
		}
		if(confirm('Remove '+ markerTitle+ ' marker?')) {
			var currentMarkerIdInForm = g_gmpCurrentEditMarker ? g_gmpCurrentEditMarker.getId() : 0;
			var removeFinalClb = function() {
				if(currentMarkerIdInForm) {
					g_gmpMap.removeMarker( currentMarkerIdInForm );
					jQuery('#gmpMarkersListGrid').trigger('reloadGrid');
				}
				if(g_gmpCurrentEditMarker) {
					g_gmpCurrentEditMarker.removeFromMap();
				}
				gmpResetMarkerForm();
			};
			if(currentMarkerIdInForm) {
				jQuery.sendFormGmp({
					btn: this
					,	data: {action: 'removeMarker', mod: 'marker', id: currentMarkerIdInForm}
					,	onSuccess: function(res) {
						if(!res.error) {
							removeFinalClb();
						}
					}
				});
			} else {
				removeFinalClb();
			}
		}
		return false;
	});
	// Marker saving
	jQuery('#gmpMarkerForm').submit(function(){
		var currentMapId = gmpGetCurrentId()
		,	currentMarkerMapId = parseInt( jQuery('#gmpMarkerForm input[name="marker_opts[map_id]"]').val() )
		,	coordX = jQuery('#gmpMarkerForm input[name="marker_opts[coord_x]"]').val()
		,	coordY = jQuery('#gmpMarkerForm input[name="marker_opts[coord_y]"]').val()
		,	onlySave = parseInt(jQuery(this).data('only-save'));

		if(currentMapId && !currentMarkerMapId) {
			jQuery('#gmpMarkerForm input[name="marker_opts[map_id]"]').val( currentMapId );
		}
		jQuery('#gmpMarkerForm input[name="marker_opts[description]"]').val( gmpGetTxtEditorVal('markerDescription') );
		if(coordX == '' && coordY == '') {
			_gmpCreateNewMapMarker();
		}
		if(onlySave) {
			jQuery(this).data('only-save', 0);
		}
		jQuery(this).sendFormGmp({
			btn: jQuery('#gmpSaveMarkerBtn')
		,	onSuccess: function(res) {
				if(!res.error) {
					if(!onlySave) {
						if(!res.data.update) {
							jQuery('#gmpMarkerForm input[name="marker_opts[id]"]').val( res.data.marker.id );
							var marker = gmpGetCurrentMarker();
							if(marker) {
								marker.setId(res.data.marker.id);
							}
						}
					}

					if(!currentMarkerMapId) {
						g_gmpMapMarkersIdsAdded.push( res.data.marker.id );
					}
					if(!onlySave) {
						jQuery('#gmpMarkersListGrid').trigger('reloadGrid');
					}
					_gmpUnchangeMarkerForm();
					jQuery(document).trigger('gmpAfterMarkerSave', gmpGetCurrentMarker());
				}
			}
		});
		return false;
	});
	// Init window to choose marker
	gmpInitIconsWnd();
	// Set base icon img
	gmpSetIconImg();
	// Bind change marker description - with it's description in map preview
	setTimeout(function(){
		gmpBindMarkerTinyMceUpdate();
		if(!g_gmpTinyMceMarkerEditorUpdateBinded) {
			jQuery('#markerDescription-tmce.wp-switch-editor.switch-tmce').click(function(){
				setTimeout(gmpBindMarkerTinyMceUpdate, 500);
			});
		}
	}, 500);
	jQuery('#markerDescription').keyup(function(){
		var marker = gmpGetCurrentMarker();
		if(!marker) {
			_gmpCreateNewMapMarker();
			marker = gmpGetCurrentMarker();
		}
		if(marker) {
			marker.setDescription( gmpGetTxtEditorVal('markerDescription') );
			marker.showInfoWnd();
		}
	});
	jQuery('#gmpMarkerForm [name="marker_opts[title]"]').keyup(function(){
		var marker = gmpGetCurrentMarker();
		if(!marker) {
			_gmpCreateNewMapMarker();
			marker = gmpGetCurrentMarker();
		}
		if(marker) {
			marker.setTitle( jQuery(this).val() );
			marker.showInfoWnd();
		}
	});
	jQuery('#gmpMarkerForm [name="marker_opts[address]"]').mapSearchAutocompleateGmp({
		msgEl: ''
		,	onSelect: function(item, event, ui) {
			if(item) {
				jQuery('#gmpMarkerForm [name="marker_opts[coord_x]"]').val(item.lat);
				jQuery('#gmpMarkerForm [name="marker_opts[coord_y]"]').val(item.lng).trigger('change');
			}
		}
	});
	jQuery('#gmpMarkerForm').find('input[name="marker_opts[coord_x]"],input[name="marker_opts[coord_y]"]').change(function(){
		var newX = jQuery('#gmpMarkerForm [name="marker_opts[coord_x]"]').val()
		,	newY = jQuery('#gmpMarkerForm [name="marker_opts[coord_y]"]').val();
		var marker = gmpGetCurrentMarker();
		if(marker) {
			marker.setPosition(newX, newY);
		} else {	// If there are no marker on map - set it and re-position it right into new position
			_gmpCreateNewMapMarker({coord_x: newX, coord_y: newY});
		}
	});
	jQuery('#gmpMarkerForm').find('input,textarea,select').change(function(){
		_gmpChangeMarkerForm();
	});
	// Make old markers table - sortable
	/*jQuery('#gmpMarkerList').sortable({
		revert: true
	,	items: '.gmpMapMarkerRow'
	,	placeholder: 'ui-sortable-placeholder'
	,	update: function(event, ui) {
			var mapId = gmpGetCurrentId();
			var msgEl = jQuery('#gmpMarkersSortMsg').size() ? jQuery('#gmpMarkersSortMsg') : jQuery('<div id="gmpMarkersSortMsg" />')
	 	,	markersList = [];
			jQuery('#gmpMarkerList').find('.gmpMapMarkerRow:not(#markerRowTemplate)').each(function(){
				markersList.push( jQuery(this).data('id') );
			});
			ui.item.find('.egm-marker-icon').append( msgEl );
			jQuery.sendFormGmp({
				msgElID: 'gmpMarkersSortMsg'
			,	data: {mod: 'gmap', action: 'resortMarkers', markers_list: markersList, map_id: mapId}
			,	onSuccess: function(res) {	}
			});
		}
	});*/
});
function gmpSetCurrentMarker(marker) {
	g_gmpCurrentEditMarker = marker;
}
function gmpGetCurrentMarker() {
	return g_gmpCurrentEditMarker;
}
// Markers form check change actions
function _gmpIsMarkerFormChanged() {
	return g_gmpMarkerFormChanged;
}
function _gmpChangeMarkerForm() {
	g_gmpMarkerFormChanged = true;
}
function _gmpUnchangeMarkerForm() {
	g_gmpMarkerFormChanged = false;
}
function gmpOpenMarkerForm() {
	gmpShowMarkerForm();
	gmpResetMarkerForm();
}
function gmpShowMarkerForm() {
	var markerFormIsVisible = jQuery('#gmpMarkerForm').is(':visible');
	if(!markerFormIsVisible) {
		jQuery('#gmpMapPropertiesTabs').wpTabs('activate', '#gmpMarkerTab');
	}
}
function gmpHideMarkerForm() {
	var markerFormIsVisible = jQuery('#gmpMarkerForm').is(':visible');
	if(markerFormIsVisible) {
		jQuery('#gmpSaveMarkerBtn').hide( g_gmpAnimationSpeed );
		jQuery('#gmpAddNewMarkerBtn').animate({
			width: '100%'
		}, g_gmpAnimationSpeed);
		jQuery('#gmpMarkerForm').slideUp( g_gmpAnimationSpeed );
	}
}
function gmpResetMarkerForm() {
	jQuery('#gmpMarkerForm')[0].reset();
	jQuery('#gmpMarkerForm input[name="marker_opts[id]"]').val('');
	jQuery('#gmpMarkerForm input[name="marker_opts[icon]"]').val( 1 );

	jQuery('#gmpMarkerForm input[name="marker_opts[params][show_description]"]').prop('checked', false);
	gmpCheckUpdate( jQuery('#gmpMarkerForm input[name="marker_opts[params][show_description]"]') );

	jQuery('#gmpMarkerForm input[name="marker_opts[params][marker_link]"]').prop('checked', false);
	gmpCheckUpdate( jQuery('#gmpMarkerForm input[name="marker_opts[params][marker_link]"]') );

	jQuery('#gmpMarkerForm input[name="marker_opts[params][marker_link_new_wnd]"]').prop('checked', false);
	gmpCheckUpdate( jQuery('#gmpMarkerForm input[name="marker_opts[params][marker_link_new_wnd]"]') );

	jQuery('#gmpMarkerForm input[name="marker_opts[params][description_mouse_hover]"]').prop('checked', false);
	gmpCheckUpdate( jQuery('#gmpMarkerForm input[name="marker_opts[params][description_mouse_hover]"]') );

	if(jQuery('#gmpMarkerForm input[name="marker_opts[params][description_mouse_hover]"]').prop('checked') === false)
		gmpHideCloseDescriptionCheckbox();
	jQuery('#gmpMarkerForm input[name="marker_opts[params][description_mouse_leave]"]').prop('checked', false);
	jQuery('#gmpMarkerForm input[name="marker_opts[params][clasterer_exclude]"]').prop('checked', false);

	gmpCheckUpdate( jQuery('#gmpMarkerForm input[name="marker_opts[params][description_mouse_leave]"]') );
	gmpCheckUpdate( jQuery('#gmpMarkerForm input[name="marker_opts[params][clasterer_exclude]"]') );

	jQuery('#gmpMarkerForm input[name="marker_opts[params][marker_list_def_img]"]').prop('checked', false);
	gmpCheckUpdate( jQuery('#gmpMarkerForm input[name="marker_opts[params][marker_list_def_img]"]') );
	jQuery('#gmpMarkerForm input[name="marker_opts[params][marker_list_def_img]"]').trigger('change');

	gmpSetIconImg();
	gmpAddLinkOptions();

	jQuery('#gmpMarkerForm select[name="marker_opts[marker_group_id][]"] option:selected').prop("selected", false);
	jQuery('#gmpMarkerForm select[name="marker_opts[marker_group_id][]"] option[value="0"]').prop("selected", true);
	jQuery('#marker_opts_marker_group_id').trigger("chosen:updated");
	
	jQuery(document).trigger('gmpAfterResetMarkerForm');
}
function _gmpCreateNewMapMarker(params) {
	params = params || {};
	var newMarkerData = {
		icon: gmpGetIconPath()
	,	draggable: true
	,	dragend: _gmpMarkerDragEndClb
	};
	var lat = 0
	,	lng = 0;
	if(params.coord_x && params.coord_y) {
		newMarkerData.coord_x = lat = parseFloat( params.coord_x );
		newMarkerData.coord_y = lng = parseFloat( params.coord_y );
	} else {
		var mapCenter = g_gmpMap.getCenter();
		newMarkerData.position = mapCenter;
		lat = mapCenter.lat();
		lng = mapCenter.lng();
	}
	gmpSetCurrentMarker( g_gmpMap.addMarker( newMarkerData ) );
	jQuery('#gmpMarkerForm [name="marker_opts[coord_x]"]').val( lat );
	jQuery('#gmpMarkerForm [name="marker_opts[coord_y]"]').val( lng );
}
function gmpMarkerEditBtnClick(btn){
	var markerId = jQuery(btn).data('marker_id');
	gmpOpenMarkerEdit( markerId );
}
function gmpMarkerDelBtnClick(btn){
	var markerId = jQuery(btn).data('marker_id')
	,	markerRow = jQuery(btn).parents('tr:first');
	gmpRemoveMarkerFromMapTblClick(markerId, {row: markerRow});
}
function gmpOpenMarkerEdit(id) {
	gmpOpenMarkerForm();
	var marker = g_gmpMap.getMarkerById( id );
	if(marker) {
		var markerParams = marker.getRawMarkerParams();
		jQuery('#gmpMarkerForm input[name="marker_opts[title]"]').val( markerParams.title );
		gmpSetTxtEditorVal('markerDescription', markerParams.description);

		jQuery('#gmpMarkerForm input[name="marker_opts[icon]"]').val( markerParams.icon_data.id );
		jQuery('#gmpMarkerForm input[name="marker_opts[address]"]').val( markerParams.address );

		jQuery('#gmpMarkerForm input[name="marker_opts[coord_x]"]').val( markerParams.coord_x );
		jQuery('#gmpMarkerForm input[name="marker_opts[coord_y]"]').val( markerParams.coord_y );

		jQuery('#gmpMarkerForm input[name="marker_opts[id]"]').val( markerParams.id );
		var markerGroupsIds = markerParams.marker_group_ids;
		jQuery('#gmpMarkerForm select[name="marker_opts[marker_group_id][]"] option:selected').prop("selected", false);
		for(var i = 0; i < markerGroupsIds.length; i++ ){
			jQuery('#gmpMarkerForm select[name="marker_opts[marker_group_id][]"] option[value="'+markerGroupsIds[i]+'"]').prop("selected", true);
		}
		jQuery('#marker_opts_marker_group_id').trigger("chosen:updated");

		if(markerParams.period_from) {
			jQuery('#gmpMarkerForm input[name="marker_opts[period_date_from]"]').val(markerParams.period_from);
		}
		if(markerParams.period_to) {
			jQuery('#gmpMarkerForm input[name="marker_opts[period_date_to]"]').val(markerParams.period_to);
		}

		if(parseInt(markerParams.params.show_description)){
			jQuery('#gmpMarkerForm input[name="marker_opts[params][show_description]"]').prop('checked', true);
			gmpCheckUpdate( jQuery('#gmpMarkerForm input[name="marker_opts[params][show_description]"]') );
		}
		if(parseInt(markerParams.params.marker_link)){
			jQuery('#gmpMarkerForm input[name="marker_opts[params][marker_link]"]').prop('checked', true);
			gmpCheckUpdate( jQuery('#gmpMarkerForm input[name="marker_opts[params][marker_link]"]') );
			jQuery('#gmpMarkerForm input[name="marker_opts[params][marker_link_src]"]').val( markerParams.params.marker_link_src );
			if(parseInt(markerParams.params.marker_link_new_wnd)){
				jQuery('#gmpMarkerForm input[name="marker_opts[params][marker_link_new_wnd]"]').prop('checked', true);
				gmpCheckUpdate( jQuery('#gmpMarkerForm input[name="marker_opts[params][marker_link_new_wnd]"]') );
			}
		}
		if(parseInt(markerParams.params.description_mouse_hover)) {
			jQuery('#gmpMarkerForm input[name="marker_opts[params][description_mouse_hover]"]').prop('checked', true);
			gmpCheckUpdate( jQuery('#gmpMarkerForm input[name="marker_opts[params][description_mouse_hover]"]') );
			gmpShowCloseDescriptionCheckbox();
		} else {
			gmpHideCloseDescriptionCheckbox();
		}
		if(parseInt(markerParams.params.description_mouse_leave)) {
			jQuery('#gmpMarkerForm input[name="marker_opts[params][description_mouse_leave]"]').prop('checked', true);
			gmpCheckUpdate( jQuery('#gmpMarkerForm input[name="marker_opts[params][description_mouse_leave]"]') );
		}
		if(parseInt(markerParams.params.clasterer_exclude)) {
			jQuery('#gmpMarkerForm input[name="marker_opts[params][clasterer_exclude]"]').prop('checked', true);
			gmpCheckUpdate( jQuery('#gmpMarkerForm input[name="marker_opts[params][clasterer_exclude]"]') );
		}
		if(parseInt(markerParams.params.marker_list_def_img)) {
			var markerListDefImg = jQuery('#gmpMarkerForm input[name="marker_opts[params][marker_list_def_img]"]')
			,	markerListDefImgUrl = jQuery('#gmpMarkerForm input[name="marker_opts[params][marker_list_def_img_url]"]');

			markerListDefImgUrl.val(markerParams.params.marker_list_def_img_url);
			markerListDefImg.prop('checked', true);
			gmpCheckUpdate( markerListDefImg );
			markerListDefImg.trigger('change');
		}
		gmpAddLinkOptions();
		gmpSetIconImg();
		gmpSetCurrentMarker( marker );
		marker.showInfoWnd();
		jQuery(document).trigger('gmpMarkerFormEdit', marker);
	}
}
function gmpRemoveMarkerFromMapTblClick(markerId, params) {
	params = params || {};
	var markerTitle = params.row ? params.row.find('td[aria-describedby="gmpMarkersListGrid_title"]').text() : ''
	,	btn = params.row ? params.row : params.btn;
	if(!confirm('Remove "'+ markerTitle+ '" marker?')) {
		return false;
	}
	if(markerId == ''){
		return false;
	}
	jQuery.sendFormGmp({
		btn: btn
		,	data: {action: 'removeMarker', mod: 'marker', id: markerId}
		,	onSuccess: function(res) {
			if(!res.error){
				g_gmpMap.removeMarker( markerId );
				jQuery('#gmpMarkersListGrid').trigger('reloadGrid');
				var currentEditMarkerId = parseInt( jQuery('#gmpMarkerForm input[name="marker_opts[id]"]').val() );
				if(currentEditMarkerId && currentEditMarkerId == markerId) {
					gmpResetMarkerForm();
					//gmpHideMarkerForm();
				}
			}
		}
	});
}
function _gmpMarkerDragEndClb() {
	var currentMarkerIdInForm = g_gmpCurrentEditMarker ? g_gmpCurrentEditMarker.getId() : 0
		,	draggedId =  this.getId()
		,	self = this;
	if((currentMarkerIdInForm && currentMarkerIdInForm == draggedId) || (!currentMarkerIdInForm && !draggedId)) {
		jQuery('#gmpMarkerForm input[name="marker_opts[coord_x]"]').val( this.lat() );
		jQuery('#gmpMarkerForm input[name="marker_opts[coord_y]"]').val( this.lng() );
	}
	if(draggedId) {	// Just save it in database
		jQuery.sendFormGmp({
			data: {mod: 'marker', action: 'updatePos', id: draggedId, lat: this.lat(), lng: this.lng()}
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#gmpMarkersListGrid').trigger('reloadGrid');
				}
			}
		});
	}
}
function drawNewIcon(icon){
	if(typeof(icon.data) == undefined){
		return;
	}
	jQuery('#gmpMarkerForm input[name="marker_opts[icon]"]').val(icon.id);
	var newIcon = '<li class="previewIcon" data-id="'+ icon.id+ '" title="'+ icon.title+ '"><img src="'+ icon.url+ '"><i class="fa fa-times" aria-hidden="true"></i></li>';
	jQuery('ul.iconsList').append(newIcon);
	gmpSetIconImg();
}
function gmpSetIconImg() {
	var id = parseInt( jQuery('#gmpMarkerForm input[name="marker_opts[icon]"]').val() );
	jQuery('#gmpMarkerIconPrevImg').attr('src', jQuery('.previewIcon[data-id="'+ id+ '"] img').attr('src'));
}
function gmpGetIconPath() {
	return jQuery('#gmpMarkerIconPrevImg').attr('src');
}
function gmpGetDialogClasses() {
	return {
		markerIcon: 'gmpMarkerIconWnd'
	,	curUserPosIcon: 'gmpCurUserPosIconWnd'
	};
}
function gmpInitMarkerIconsDialogWnd() {
	if(jQuery('#gmpIconsWnd').hasClass('ui-dialog-content')) {
		return jQuery('#gmpIconsWnd');
	}
	var dialodClasses =  gmpGetDialogClasses();

	return jQuery('#gmpIconsWnd').dialog({
		modal:    true
	,	autoOpen: false
	,	width: 540
	,	height: 600
	,	beforeClose: function(e, ui) {
			for(var i in dialodClasses) {
				if(jQuery(this).hasClass(dialodClasses[i]))
					jQuery(this).removeClass(dialodClasses[i]);
			}
		}
	});
}
function gmpInitIconsWnd() {
	var $container = gmpInitMarkerIconsDialogWnd()
	,	dialodClasses =  gmpGetDialogClasses();

	jQuery('#gmpMarkerIconBtn').click(function(){
		$container.addClass(dialodClasses.markerIcon);
		$container.dialog('open');
		return false;
	});
	jQuery('#gmpIconsWnd').on('click', '.previewIcon img', function(e){
		if($container.hasClass(dialodClasses.markerIcon)) {
			var newId = jQuery(this).parent().data('id');
			jQuery('#gmpMarkerForm input[name="marker_opts[icon]"]').val( newId );
			gmpSetIconImg();
			var marker = gmpGetCurrentMarker();
			if(!marker) {
				_gmpCreateNewMapMarker();
				marker = gmpGetCurrentMarker();
			}
			if(marker) {
				marker.setIcon( gmpGetIconPath() );
			}
			$container.dialog('close');
			return false;
		}
	});
	
	jQuery('#gmpIconsWnd').on('click', '.previewIcon i', function(e){
		e.preventDefault();
		var icon = jQuery(this)
		,   iconWrapper = icon.closest('.previewIcon')
		,   iconId = iconWrapper.attr('data-id');
		
		jQuery.sendFormGmp({
			data: {action: 'remove', mod: 'icons', id: iconId}
			,	onSuccess: function(res) {
				iconWrapper.remove();
			}
		});

	});
	/*
	 * wp media upload
	 *
	 */
	jQuery('#gmpUploadIconBtn').click(function(e){
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
		var currentForm = jQuery(this).parents('form');
		custom_uploader.on('select', function(){
			var attachment = custom_uploader.state().get('selection').first().toJSON()
			,	respElem = jQuery('.gmpUplRes')
			,	sendData = {
					page: 'icons'
				,	action: 'saveNewIcon'
				,	reqType: 'ajax'
				,	icon: {
						url: attachment.url
					}
				};
			if(attachment.title != undefined){
				sendData.icon.title = attachment.title;
			}
			if(attachment.description != undefined){
				sendData.icon.description = attachment.description;
			}
			jQuery.sendFormGmp({
				msgElID: respElem
			,	data: sendData
			,	onSuccess: function(res){
					if(!res.error) {
						drawNewIcon(res.data);
					} else {
						respElem.html(data.error.join(','));
					}
				}
			});
		});
		//Open the uploader dialog
		custom_uploader.open();
	});
}
function gmpAddLinkOptions() {
	var markerLink = jQuery('#marker_link').prop('checked');
	if (markerLink) {
		jQuery('#link_options').css('display', 'inline');
	} else {
		jQuery('#link_options').css('display', 'none');
	}
}
function gmpRefreshMapMarkers(map, markers) {
	map.clearMarkers();
	markers = _gmpPrepareMarkersListAdmin( markers );
	for(var i in markers) {
		var newMarker = map.addMarker( markers[i] );
		newMarker.setTitle( markers[i].title, true );
		newMarker.setDescription( markers[i].description );
	}
	map.markersRefresh();
}
function _gmpPrepareMarkersListAdmin(markers) {
	return _gmpPrepareMarkersList(markers, {
		dragend: _gmpMarkerDragEndClb
	});
}
// New markers list version method
function gmpRefreshMapMarkersList(markersList) {
	gmpRefreshMapMarkers(g_gmpMap, markersList);
	var currentFormMarker = parseInt( jQuery('#gmpMarkerForm input[name="marker_opts[id]"]').val() );
	if(currentFormMarker) {
		var editMapMarker = g_gmpMap.getMarkerById(currentFormMarker);
		if(editMapMarker) {
			gmpSetCurrentMarker( editMapMarker );
			editMapMarker.showInfoWnd();
		}
	}
}
function gmpBindMarkerTinyMceUpdate() {
	if(!g_gmpTinyMceMarkerEditorUpdateBinded && typeof(tinyMCE) !== 'undefined' && tinyMCE.editors) {
		if(tinyMCE.editors.markerDescription) {
			tinyMCE.editors.markerDescription.onKeyUp.add(function(){
				var marker = gmpGetCurrentMarker();

				if(!marker) {
					_gmpCreateNewMapMarker();
					marker = gmpGetCurrentMarker();
				}
				if(marker) {
					marker.setDescription( gmpGetTxtEditorVal('markerDescription') );
					marker.showInfoWnd();
				}
			});
			g_gmpTinyMceMarkerEditorUpdateBinded = true;
		}
	}
}
// Old markers list version method
/*function gmpRefreshMapMarkersList(fromServer, justTable) {
	var shell = jQuery('#gmpMarkerList');
	var buildListClb = function(markersList) {
		if(gmpMainMap)
			gmpMainMap.markers = markersList;
		if(!justTable) {
			gmpRefreshMapMarkers(g_gmpMap, markersList);
			var currentFormMarker = parseInt( jQuery('#gmpMarkerForm input[name="marker_opts[id]"]').val() );
			if(currentFormMarker) {
				var editMapMarker = g_gmpMap.getMarkerById(currentFormMarker);
				if(editMapMarker) {
					gmpSetCurrentMarker( editMapMarker );
					editMapMarker.showInfoWnd();
				}
			}
		}
		//g_gmpMap.setMarkersParams( markersList );
		shell.find('.gmpMapMarkerRow:not(#markerRowTemplate)').remove();
		if(markersList && markersList.length) {
			for(var i = 0; i < markersList.length; i++) {
				var newRow = jQuery('#markerRowTemplate').clone();
				newRow.find('.egm-marker-icon img').attr('src', markersList[i].icon_data.path);
				newRow.find('.egm-marker-title').html(markersList[i].title);
				newRow.find('.egm-marker-latlng').html(parseFloat(markersList[i].coord_x).toFixed(2)+ '"N '+ parseFloat(markersList[i].coord_y).toFixed(2)+ '"E');
				newRow.data('id', markersList[i].id);
				newRow.find('.egm-marker-edit').click(function(){
					var markerRow = jQuery(this).parents('.gmpMapMarkerRow:first');
					gmpOpenMarkerEdit( markerRow.data('id') );
					return false;
				});
				newRow.find('.egm-marker-remove').click(function(){
					var markerRow = jQuery(this).parents('.gmpMapMarkerRow:first');
					gmpRemoveMarkerFromMapTblClick(markerRow.data('id'), {row: markerRow});
					return false;
				});
				newRow.removeAttr('id').show();
				shell.append( newRow );
			}
		}
		_gmpResizeRightSidebar();
	};
	if(fromServer) {
		shell.find('.egm-marker').css('opacity', '0.5');
		shell.addClass('supsystic-inline-loader');
		var currentMapId = gmpGetCurrentId();
		jQuery.sendFormGmp({
			data: {mod: 'marker', action: 'getMapMarkers', map_id: (currentMapId ? currentMapId : 0), 'added_marker_ids': g_gmpMapMarkersIdsAdded}
			,	onSuccess: function(res) {
				if(!res.error) {
					shell.find('.egm-marker').css('opacity', '1');
					shell.removeClass('supsystic-inline-loader');
					buildListClb( res.data.markers );
				}
			}
		});
	} else {
		if(gmpMainMap)
			buildListClb( gmpMainMap.markers );
	}
}*/



