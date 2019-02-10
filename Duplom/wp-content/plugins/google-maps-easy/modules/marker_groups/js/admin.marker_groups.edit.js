var g_gmpMarkerGroupFormChanged = false;

window.onbeforeunload = function(){
	// If there are at lease one unsaved form - show message for confirnation for page leave
	if(_gmpIsMarkerGroupFormChanged()) {
		return 'You have unsaved changes in Marker Category form. Are you sure want to leave this page?';
	}
};
jQuery(document).ready(function() {
	function gmpGetCurrentId() {
		return parseInt( jQuery('#gmpMgrForm input[name="marker_group[id]"]').val() );
	}
	// Map saving form
	jQuery('#gmpMgrForm').submit(function () {
		var currentId = gmpGetCurrentId()
		,	firstTime = currentId ? false : true;

		jQuery(this).sendFormGmp({
			btn: '#gmpMgrSaveBtn'
		,	onSuccess: function (res) {
				if (!res.error) {
					_gmpUnchangeMarkerGroupForm();
					if(firstTime) {
						if(res.data.edit_url) {
							window.location = res.data.edit_url;
						}
					}
				}
			}
		});
		return false;
	});
	jQuery('#gmpMgrSaveBtn').click(function () {
		jQuery('#gmpMgrForm').submit();
		return false;
	});
	jQuery('#gmpMgrForm').find('input').change(function(){
		_gmpChangeMarkerGroupForm();
	});
	jQuery('#gmpUploadMarkerGroupClastererIconBtn').click(function(e){
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
				,	iconPrevImg = jQuery('#gmpMarkerGroupClastererIconPrevImg')
				,	width  = 53
				,	height = 'auto';

			iconPrevImg.attr('src', attachment.url);
			width = document.getElementById('gmpMarkerGroupClastererIconPrevImg').naturalWidth;
			height = document.getElementById('gmpMarkerGroupClastererIconPrevImg').naturalHeight;
			gmpUpdateMarkerGroupClusterIcon(attachment.url, width, height);
		});
		//Open the uploader dialog
		custom_uploader.open();
	});
	jQuery('#gmpDefaultMarkerGroupClastererIconBtn').click(function(e) {
		e.preventDefault();
		var defIconUrl = GMP_DATA.modPath + 'gmap/img/m1.png';
		jQuery('#gmpMarkerGroupClastererIconPrevImg').attr('src', defIconUrl);
		gmpUpdateMarkerGroupClusterIcon(defIconUrl, 53, 52);
	});
});
// Marker Group form check change actions
function _gmpIsMarkerGroupFormChanged() {
	return g_gmpMarkerGroupFormChanged;
}
function _gmpChangeMarkerGroupForm() {
	g_gmpMarkerGroupFormChanged = true;
}
function _gmpUnchangeMarkerGroupForm() {
	g_gmpMarkerGroupFormChanged = false;
}
function gmpUpdateMarkerGroupClusterIcon(url, width, height) {
	jQuery('input[name="marker_group[claster_icon]"]').val(url);
	jQuery('input[name="marker_group[claster_icon_width]"]').val(width);
	jQuery('input[name="marker_group[claster_icon_height]"]').val(height);
}