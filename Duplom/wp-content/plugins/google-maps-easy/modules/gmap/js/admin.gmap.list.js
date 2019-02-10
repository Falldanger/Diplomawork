jQuery(document).ready(function(){
	var tblId = 'gmpGmapTbl';
	jQuery('#'+ tblId).jqGrid({ 
		url: gmpTblDataUrl
	,	datatype: 'json'
	,	autowidth: true
	,	shrinkToFit: true
	,	colNames:[toeLangGmp('ID'), toeLangGmp('Title'), toeLangGmp('Create Date'), toeLangGmp('Markers'), toeLangGmp('Actions')]
	,	colModel:[
			{name: 'id', index: 'id', searchoptions: {sopt: ['eq']}, width: '50', align: 'center'}
		,	{name: 'title', index: 'title', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'create_date', index: 'create_date', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'markers', index: 'markers', searchoptions: {sopt: ['eq']}, align: 'center', sortable: false}
		,	{name: 'actions', index: 'actions', searchoptions: {sopt: ['eq']}, align: 'center', sortable: false}
		]
	,	postData: {
			search: {
				text_like: jQuery('#'+ tblId+ 'SearchTxt').val()
			}
		}
	,	rowNum: 10
	,	rowList: [10, 20, 30, 1000]
	,	pager: '#'+ tblId+ 'Nav'
	,	sortname: 'id'
	,	viewrecords: true
	,	sortorder: 'desc'
	,	jsonReader: { repeatitems : false, id: '0' }
	,	caption: toeLangGmp('Current Map')
	,	height: '100%' 
	,	emptyrecords: toeLangGmp('You have no Map for now.')
	,	multiselect: true
	,	onSelectRow: function(rowid, e) {
			var tblId = jQuery(this).attr('id')
			,	selectedRowIds = jQuery('#'+ tblId).jqGrid ('getGridParam', 'selarrrow')
			,	totalRows = jQuery('#'+ tblId).getGridParam('reccount')
			,	totalRowsSelected = selectedRowIds.length;
			if(totalRowsSelected) {
				jQuery('#gmpGmapRemoveGroupBtn').removeAttr('disabled');
				jQuery('#gmpGmapCloneGroupBtn').removeAttr('disabled');
				if(totalRowsSelected == totalRows) {
					jQuery('#cb_'+ tblId).prop('indeterminate', false);
					jQuery('#cb_'+ tblId).attr('checked', 'checked');
				} else {
					jQuery('#cb_'+ tblId).prop('indeterminate', true);
				}
			} else {
				jQuery('#gmpGmapRemoveGroupBtn').attr('disabled', 'disabled');
				jQuery('#gmpGmapCloneGroupBtn').attr('disabled', 'disabled');
				jQuery('#cb_'+ tblId).prop('indeterminate', false);
				jQuery('#cb_'+ tblId).removeAttr('checked');
			}
			gmpCheckUpdate(jQuery(this).find('tr:eq('+rowid+')').find('input[type=checkbox].cbox'));
			gmpCheckUpdate('#cb_'+ tblId);
		}
	,	gridComplete: function(a, b, c) {
			var tblId = jQuery(this).attr('id');
			jQuery('#gmpGmapRemoveGroupBtn').attr('disabled', 'disabled');
			jQuery('#gmpGmapCloneGroupBtn').attr('disabled', 'disabled');
			jQuery('#cb_'+ tblId).prop('indeterminate', false);
			jQuery('#cb_'+ tblId).removeAttr('checked');
			/*if(jQuery('#'+ tblId).jqGrid('getGridParam', 'records'))	// If we have at least one row - allow to clear whole list
				jQuery('#gmpGmapClearBtn').removeAttr('disabled');
			else
				jQuery('#gmpGmapClearBtn').attr('disabled', 'disabled');*/
			// Custom checkbox manipulation
			gmpInitCustomCheckRadio('#'+ jQuery(this).attr('id') );
			gmpCheckUpdate('#cb_'+ jQuery(this).attr('id'));
			tooltipsterize( jQuery('#'+ tblId) );
		}
	,	loadComplete: function() {
			var tblId = jQuery(this).attr('id');
			if (this.p.reccount === 0) {
				jQuery(this).hide();
				jQuery('#'+ tblId+ 'EmptyMsg').show();
			} else {
				jQuery(this).show();
				jQuery('#'+ tblId+ 'EmptyMsg').hide();
			}
		}
	});
	jQuery('#'+ tblId+ 'NavShell').append( jQuery('#'+ tblId+ 'Nav') );
	jQuery('#'+ tblId+ 'Nav').find('.ui-pg-selbox').insertAfter( jQuery('#'+ tblId+ 'Nav').find('.ui-paging-info') );
	jQuery('#'+ tblId+ 'Nav').find('.ui-pg-table td:first').remove();
	// Make navigation tabs to be with our additional buttons - in one row
	jQuery('#'+ tblId+ 'Nav_center').prepend( jQuery('#'+ tblId+ 'NavBtnsShell') ).css({
		'width': '80%'
	,	'white-space': 'normal'
	,	'padding-top': '8px'
	});
	jQuery('#'+ tblId+ 'SearchTxt').keyup(function(){
		var searchVal = jQuery.trim( jQuery(this).val() );
		if(searchVal && searchVal != '') {
			gmpGridDoListSearch({
				text_like: searchVal
			}, tblId);
		}
	});
	
	jQuery('#'+ tblId+ 'EmptyMsg').insertAfter(jQuery('#'+ tblId+ '').parent());
	jQuery('#'+ tblId+ '').jqGrid('navGrid', '#'+ tblId+ 'Nav', {edit: false, add: false, del: false});
	jQuery('#cb_'+ tblId+ '').change(function(){
		if(jQuery(this).attr('checked')) {
			jQuery('#gmpGmapRemoveGroupBtn').removeAttr('disabled');
			jQuery('#gmpGmapCloneGroupBtn').removeAttr('disabled');
		} else {
			jQuery('#gmpGmapRemoveGroupBtn').attr('disabled', 'disabled');
			jQuery('#gmpGmapCloneGroupBtn').attr('disabled', 'disabled');
		}
	});
	jQuery('#gmpGmapRemoveGroupBtn').click(function(){
		var selectedRowIds = jQuery('#gmpGmapTbl').jqGrid ('getGridParam', 'selarrrow')
		,	listIds = [];
		for(var i in selectedRowIds) {
			var rowData = jQuery('#gmpGmapTbl').jqGrid('getRowData', selectedRowIds[ i ]);
			listIds.push( rowData.id );
		}
		var mapLabel = '';
		if(listIds.length == 1) {	// In table label cell there can be some additional links
			var labelCellData = gmpGetGridColDataById(listIds[0], 'title', 'gmpGmapTbl');
			mapLabel = labelCellData;
		}
		var confirmMsg = listIds.length > 1
			? toeLangGmp('Are you sur want to remove '+ listIds.length+ ' Maps?')
			: toeLangGmp('Are you sure want to remove "'+ mapLabel+ '" Map?');
		if(confirm(confirmMsg)) {
			jQuery.sendFormGmp({
				btn: this
			,	data: {mod: 'gmap', action: 'removeGroup', listIds: listIds}
			,	onSuccess: function(res) {
					if(!res.error) {
						jQuery('#gmpGmapTbl').trigger( 'reloadGrid' );
					}
				}
			});
		}
		return false;
	});
	jQuery('#gmpGmapCloneGroupBtn').click(function(){
		var selectedRowIds = jQuery('#gmpGmapTbl').jqGrid ('getGridParam', 'selarrrow')
		,	mapLabel = ''
		,	listIds = [];

		for(var i in selectedRowIds) {
			var rowData = jQuery('#gmpGmapTbl').jqGrid('getRowData', selectedRowIds[ i ]);
			listIds.push( rowData.id );
		}
		if(listIds.length == 1) {	// In table label cell there can be some additional links
			var labelCellData = gmpGetGridColDataById(listIds[0], 'title', 'gmpGmapTbl');
			mapLabel = labelCellData;
		}
		var confirmMsg = listIds.length > 1
			? toeLangGmp('Are you sur want to clone '+ listIds.length+ ' Maps?')
			: toeLangGmp('Are you sure want to clone "'+ mapLabel+ '" Map?');
		if(confirm(confirmMsg)) {
			jQuery.sendFormGmp({
				btn: this
			,	data: {mod: 'gmap', action: 'cloneMapGroup', listIds: listIds}
			,	onSuccess: function(res) {
					if(!res.error) {
						jQuery('#gmpGmapTbl').trigger( 'reloadGrid' );
					}
				}
			});
		}
		return false;
	});
	/*jQuery('#gmpGmapClearBtn').click(function(){
		if(confirm(toeLangGmp('Clear whole maps list?'))) {
			jQuery.sendFormGmp({
				btn: this
			,	data: {mod: 'gmap', action: 'clear'}
			,	onSuccess: function(res) {
					if(!res.error) {
						jQuery('#gmpGmapTbl').trigger( 'reloadGrid' );
					}
				}
			});
		}
		return false;
	});*/
	
	gmpInitCustomCheckRadio('#'+ tblId+ '_cb');
});
function gmpRemoveMapFromTblClick(mapId){
	if(!confirm(toeLangGmp('Remove Map?'))) {
		return false;
	}
	if(mapId == ''){
		return false;
	}
	var msgEl = jQuery('#gmpRemoveElemLoader__'+ mapId);
	
	jQuery.sendFormGmp({
		msgElID: msgEl
	,	data: {action: 'remove', mod: 'gmap', id: mapId}
	,	onSuccess: function(res) {
			if(!res.error){
				jQuery('#gmpGmapTbl').trigger( 'reloadGrid' );
				setTimeout(function(){
					msgEl.hide('500', function(){
						jQuery(this).parents('tr:first').remove();
					});
				}, 500);
			}
		}
	});
}
