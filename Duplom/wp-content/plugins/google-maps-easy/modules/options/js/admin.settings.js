jQuery(document).ready(function(){
	jQuery('#gmpSettingsSaveBtn').click(function(){
		_gmpSaveMainOpts();
		return false;
	});
	jQuery('#gmpSettingsForm').submit(function(){
		jQuery(this).sendFormGmp({
			btn: jQuery('#gmpSettingsSaveBtn')
		});
		return false;
	});
});
function _gmpSaveMainOpts() {
	jQuery('#gmpSettingsForm').submit();
}