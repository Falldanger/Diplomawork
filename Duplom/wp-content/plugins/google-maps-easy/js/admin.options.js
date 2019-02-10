var gmpAdminFormChanged = [];
/*window.onbeforeunload = function(){
	// If there are at lease one unsaved form - show message for confirnation for page leave
	if(gmpAdminFormChanged.length)
		return 'Some changes were not-saved. Are you sure you want to leave?';
};*/
jQuery(document).ready(function(){
	gmpInitMainPromoPopup();
	if(typeof(gmpActiveTab) != 'undefined' && gmpActiveTab != 'main_page' && jQuery('#toplevel_page_'+ gmpMainSlug).hasClass('wp-has-current-submenu')) {
		var subMenus = jQuery('#toplevel_page_'+ gmpMainSlug).find('.wp-submenu li');
		subMenus.removeClass('current').each(function(){
			if(jQuery(this).find('a[href$="&tab='+ gmpActiveTab+ '"]').size()) {
				jQuery(this).addClass('current');
			}
		});
	}
	
	// Timeout - is to count only user changes, because some changes can be done auto when form is loaded
	setTimeout(function() {
		// If some changes was made in those forms and they were not saved - show message for confirnation before page reload
		var formsPreventLeave = [];
		if(formsPreventLeave && formsPreventLeave.length) {
			jQuery('#'+ formsPreventLeave.join(', #')).find('input,select').change(function(){
				var formId = jQuery(this).parents('form:first').attr('id');
				changeAdminFormGmp(formId);
			});
			jQuery('#'+ formsPreventLeave.join(', #')).find('input[type=text],textarea').keyup(function(){
				var formId = jQuery(this).parents('form:first').attr('id');
				changeAdminFormGmp(formId);
			});
			jQuery('#'+ formsPreventLeave.join(', #')).submit(function(){
				adminFormSavedGmp( jQuery(this).attr('id') );
			});
		}
	}, 1000);

	if(jQuery('.gmpInputsWithDescrForm').size()) {
		jQuery('.gmpInputsWithDescrForm').find('input[type=checkbox][data-optkey]').change(function(){
			var optKey = jQuery(this).data('optkey')
			,	descShell = jQuery('#gmpFormOptDetails_'+ optKey);
			if(descShell.size()) {
				if(jQuery(this).attr('checked')) {
					descShell.slideDown( 300 );
				} else {
					descShell.slideUp( 300 );
				}
			}
		}).trigger('change');
	}
	gmpInitCustomCheckRadio();
	//gmpInitCustomSelect();
	
	jQuery('.gmpFieldsetToggled').each(function(){
		var self = this;
		jQuery(self).find('.gmpFieldsetContent').hide();
		jQuery(self).find('.gmpFieldsetToggleBtn').click(function(){
			var icon = jQuery(this).find('i')
			,	show = icon.hasClass('fa-plus');
			show ? icon.removeClass('fa-plus').addClass('fa-minus') : icon.removeClass('fa-minus').addClass('fa-plus');
			jQuery(self).find('.gmpFieldsetContent').slideToggle( 300, function(){
				if(show) {
					jQuery(this).find('textarea').each(function(i, el){
						if(typeof(this.CodeMirrorEditor) !== 'undefined') {
							this.CodeMirrorEditor.refresh();
						}
					});
				}
			} );
			return false;
		});
	});
	// Go to Top button init
	if(jQuery('#gmpPopupGoToTopBtn').size()) {
		jQuery('#gmpPopupGoToTopBtn').click(function(){
			jQuery('html, body').animate({
				scrollTop: 0
			}, 1000);
			jQuery(this).parents('#gmpPopupGoToTop:first').hide();
			return false;
		});
	}
	// Tooltipster initialization
	tooltipsterize();

    // Check for showing review notice after a week usage
    gmpInitPlugNotices();
});
jQuery(window).load(function(){
	setTimeout(function(){	// setTimeout to make sure that all required show/hide were triggered
		gmpResetCopyTextCodeFields();
	}, 10);
});
/**
 * Make shortcodes display normal width
 */
function gmpResetCopyTextCodeFields(selector) {
	var area = selector ? jQuery(selector) : jQuery(document);
	if(area.find('.gmpCopyTextCode').size()) {
		var cloneWidthElement =  jQuery('<span class="sup-shortcode" />').appendTo('.supsystic-plugin');
		area.find('.gmpCopyTextCode').attr('readonly', 'readonly').click(function(){
			this.setSelectionRange(0, this.value.length);
		}).focus(function(){
			this.setSelectionRange(0, this.value.length);
		});
		area.find('input.gmpCopyTextCode:not(.gmpStaticWidth)').each(function(){
			cloneWidthElement.html( str_replace(jQuery(this).val(), '<', 'P') );
			jQuery(this).width( cloneWidthElement.width() );
		});
		cloneWidthElement.remove();
	}
	if(area.find('.gmpMapShortCodeShell').size()) {
		area.find('.gmpMapShortCodeShell').attr('readonly', 'readonly').click(function(){
			this.setSelectionRange(0, this.value.length);
		}).focus(function(){
			this.setSelectionRange(0, this.value.length);
		});
	}
}
function tooltipsterize(shell) {
	var tooltipsterSettings = {
		contentAsHTML: true
	,	interactive: true
	,	speed: 250
	,	delay: 0
	,	animation: 'swing'
	,	maxWidth: 450
	,	functionInit: function(origin, content) {
			// Check if there are image in tooltip
			if(content && typeof(content) === 'string' && content.indexOf('<img') !== -1) {
				// Preload all images from tooltip - to make it calc position correctly
				var $tmpDiv = jQuery('<div style="display: none;" />').appendTo('body').html(content);
				$tmpDiv.load(function(){
					$tmpDiv.remove();
				});
			}
		}
	}
	,	classToPos = {
		'.supsystic-tooltip': 'top-left'
	,	'.supsystic-tooltip-bottom': 'bottom-left'
	,	'.supsystic-tooltip-left': 'left'
	,	'.supsystic-tooltip-right': 'right'
	};
	for(var className in classToPos) {
		if(shell) {
			if(jQuery(shell).find( className ).size()) {
				tooltipsterSettings.position = classToPos[ className ];
				jQuery(shell).find( className ).tooltipster( tooltipsterSettings );
			}
		} else {
			if(jQuery( className ).size()) {
				tooltipsterSettings.position = classToPos[ className ];
				jQuery( className ).tooltipster( tooltipsterSettings );
			}
		}
	}
}
function changeAdminFormGmp(formId) {
	if(jQuery.inArray(formId, gmpAdminFormChanged) == -1)
		gmpAdminFormChanged.push(formId);
}
function adminFormSavedGmp(formId) {
	if(gmpAdminFormChanged.length) {
		for(var i in gmpAdminFormChanged) {
			if(gmpAdminFormChanged[i] == formId) {
				gmpAdminFormChanged.pop(i);
			}
		}
	}
}
function checkAdminFormSaved() {
	if(gmpAdminFormChanged.length) {
		if(!confirm(toeLangGmp('Some changes were not-saved. Are you sure you want to leave?'))) {
			return false;
		}
		gmpAdminFormChanged = [];	// Clear unsaved forms array - if user wanted to do this
	}
	return true;
}
function isAdminFormChanged(formId) {
	if(gmpAdminFormChanged.length) {
		for(var i in gmpAdminFormChanged) {
			if(gmpAdminFormChanged[i] == formId) {
				return true;
			}
		}
	}
	return false;
}
jQuery(window).load(function(){
	gmpInitStickyItem();
});
/*Some items should be always on users screen*/
function gmpInitStickyItem() {
	jQuery(window).scroll(function(){
		var stickiItemsSelectors = [/*'.ui-jqgrid-hdiv', */'.supsystic-sticky']
		,	elementsUsePaddingNext = [/*'.ui-jqgrid-hdiv', */'.supsystic-bar']	// For example - if we stick row - then all other should not offest to top after we will place element as fixed
		,	wpTollbarHeight = 32
		,	wndScrollTop = jQuery(window).scrollTop() + wpTollbarHeight
		,	footer = jQuery('.gmpAdminFooterShell')
		,	footerHeight = footer && footer.size() ? footer.height() : 0
		,	docHeight = jQuery(document).height()
		,	wasSticking = false
		,	wasUnSticking = false;
		/*if(jQuery('#wpbody-content .update-nag').size()) {	// Not used for now
			wpTollbarHeight += parseInt(jQuery('#wpbody-content .update-nag').outerHeight());
		}*/
		for(var i = 0; i < stickiItemsSelectors.length; i++) {
			jQuery(stickiItemsSelectors[ i ]).each(function(){
				var element = jQuery(this);
				if(element.attr('id') == 'gmpMapRightStickyBar') {
					// #gmpMapRightStickyBar - map preview container, let be here for normal map preview container scrolling
					if(jQuery(window).width() <= 991) {
						element.removeClass('supsystic-sticky-active');
						element.addClass('sticky-ignore');
					} else {
						element.removeClass('sticky-ignore')
					}
				}
				if(element && element.size() && !element.hasClass('sticky-ignore')) {
					var scrollMinPos = element.offset().top
					,	prevScrollMinPos = parseInt(element.data('scrollMinPos'))
					,	useNextElementPadding = toeInArray(stickiItemsSelectors[ i ], elementsUsePaddingNext) !== -1 || element.hasClass('sticky-padd-next')
					,	currentScrollTop = wndScrollTop
					,	calcPrevHeight = element.data('prev-height')
					,	currentBorderHeight = wpTollbarHeight
					,	usePrevHeight = 0
					,	nextElement;
					if(calcPrevHeight) {
						usePrevHeight = jQuery(calcPrevHeight).outerHeight();
						currentBorderHeight += usePrevHeight;
					}
					if(element.is(':visible') && currentScrollTop > scrollMinPos && !element.hasClass('supsystic-sticky-active')) {	// Start sticking
						element.addClass('supsystic-sticky-active').data('scrollMinPos', scrollMinPos).css({
							'top': currentBorderHeight
						});
						if(element.hasClass('sticky-save-width')) {
							element.addClass('sticky-full-width');
						}
						if(useNextElementPadding) {
							//element.addClass('supsystic-sticky-active-bordered');
							nextElement = element.next();
							if(nextElement && nextElement.size()) {
								nextElement.data('prevPaddingTop', nextElement.css('padding-top'));
								var addToNextPadding = parseInt(element.data('next-padding-add'));
								addToNextPadding = addToNextPadding ? addToNextPadding : 0;
								nextElement.css({
									'padding-top': element.height() + usePrevHeight  + addToNextPadding
								});
							}
						}
						wasSticking = true;
						element.trigger('startSticky');
					} else if(!isNaN(prevScrollMinPos) && currentScrollTop <= prevScrollMinPos) {	// Stop sticking
						// because of this action some map tabs (shapes and heatmap) are jump up during scroll.
						element.removeClass('supsystic-sticky-active').data('scrollMinPos', 0).css({
							'top': 0
						});
						if(element.hasClass('sticky-save-width')) {
							element.removeClass('sticky-full-width');
						}
						if(useNextElementPadding) {
							//element.removeClass('supsystic-sticky-active-bordered');
							nextElement = element.next();
							if(nextElement && nextElement.size()) {
								var nextPrevPaddingTop = parseInt(nextElement.data('prevPaddingTop'));
								if(isNaN(nextPrevPaddingTop))
									nextPrevPaddingTop = 0;
								nextElement.css({
									'padding-top': nextPrevPaddingTop
								});
							}
						}
						element.trigger('stopSticky');
						wasUnSticking = true;
					} else {	// Check new stick position
						if(element.hasClass('supsystic-sticky-active')) {
							if(footerHeight) {
								var elementHeight = element.height()
								,	heightCorrection = 32
								,	topDiff = docHeight - footerHeight - (currentScrollTop + elementHeight + heightCorrection);
								if(topDiff < 0) {
									element.css({
										'top': currentBorderHeight + topDiff
									});
								} else {
									element.css({
										'top': currentBorderHeight
									});
								}
							}
							// If at least on element is still sticking - count it as all is working
							wasSticking = wasUnSticking = false;
						}
					}
				}
			});
		}
		if(wasSticking) {
			if(jQuery('#gmpPopupGoToTop').size())
				jQuery('#gmpPopupGoToTop').show();
		} else if(wasUnSticking) {
			if(jQuery('#gmpPopupGoToTop').size())
				jQuery('#gmpPopupGoToTop').hide();
		}
	});
}
function gmpInitCustomCheckRadio(selector) {
	if(!selector)
		selector = document;
	jQuery(selector).find('input').iCheck('destroy').iCheck({
		checkboxClass: 'icheckbox_minimal'
	,	radioClass: 'iradio_minimal'
	}).on('ifChanged', function(e){
		// for checkboxHiddenVal type, see class htmlGmp
		jQuery(this).trigger('change');
		if(jQuery(this).hasClass('cbox')) {
			var parentRow = jQuery(this).parents('.jqgrow:first');
			if(parentRow && parentRow.size()) {
				jQuery(this).parents('td:first').trigger('click');
			} else {
				var checkId = jQuery(this).attr('id');
				if(checkId && checkId != '' && strpos(checkId, 'cb_') === 0) {
					var parentTblId = str_replace(checkId, 'cb_', '');
					if(parentTblId && parentTblId != '' && jQuery('#'+ parentTblId).size()) {
						jQuery('#'+ parentTblId).find('input[type=checkbox]').iCheck('update');
					}
				}
			}
		}
	}).on('ifClicked', function(e){
		jQuery(this).trigger('click');
	});
}
function gmpCheckUpdate(checkbox) {
	jQuery(checkbox).iCheck('update');
}
function gmpCheckUpdateArea(selector) {
	jQuery(selector).find('input[type=checkbox]').iCheck('update');
}
/**
 * Add data to jqGrid object post params search
 * @param {object} param Search params to set
 * @param {string} gridSelectorId ID of grid table html element
 */
function gmpGridSetListSearch(param, gridSelectorId) {
	jQuery('#'+ gridSelectorId).setGridParam({
		postData: {
			search: param
		}
	});
}
/**
 * Set data to jqGrid object post params search and trigger search
 * @param {object} param Search params to set
 * @param {string} gridSelectorId ID of grid table html element
 */
function gmpGridDoListSearch(param, gridSelectorId) {
	gmpGridSetListSearch(param, gridSelectorId);
	jQuery('#'+ gridSelectorId).trigger( 'reloadGrid' );
}
/**
 * Get row data from jqGrid
 * @param {number} id Item ID (from database for example)
 * @param {string} gridSelectorId ID of grid table html element
 * @return {object} Row data
 */
function gmpGetGridDataById(id, gridSelectorId) {
	var rowId = getGridRowId(id, gridSelectorId);
	if(rowId) {
		return jQuery('#'+ gridSelectorId).jqGrid ('getRowData', rowId);
	}
	return false;
}
/**
 * Get cell data from jqGrid
 * @param {number} id Item ID (from database for example)
 * @param {string} column Column name
 * @param {string} gridSelectorId ID of grid table html element
 * @return {string} Cell data
 */
function gmpGetGridColDataById(id, column, gridSelectorId) {
	var rowId = getGridRowId(id, gridSelectorId);
	if(rowId) {
		return jQuery('#'+ gridSelectorId).jqGrid ('getCell', rowId, column);
	}
	return false;
}
/**
 * Get grid row ID (ID of table row) from item ID (from database ID for example)
 * @param {number} id Item ID (from database for example)
 * @param {string} gridSelectorId ID of grid table html element
 * @return {number} Table row ID
 */
function getGridRowId(id, gridSelectorId) {
	var rowId = parseInt(jQuery('#'+ gridSelectorId).find('[aria-describedby='+ gridSelectorId+ '_id][title='+ id+ ']').parent('tr:first').index());
	if(!rowId) {
		console.log('CAN NOT FIND ITEM WITH ID  '+ id);
		return false;
	}
	return rowId;
}
function prepareToPlotDate(data) {
	if(typeof(data) === 'string') {
		if(data) {
			
			data = str_replace(data, '/', '-');
			console.log(data, new Date(data));
			return (new Date(data)).getTime();
		}
	}
	return data;
}
/**
 * Main promo popup will show each time user will try to modify PRO option with free version only
 */
function gmpInitMainPromoPopup() {
	if(!GMP_DATA.isPro) {
		var $proOptWnd = gmpGetMainPromoPopup();

		jQuery('.gmpProOpt').change(function(e){
			e.stopPropagation();
			var needShow = true
			,	isRadio = jQuery(this).attr('type') == 'radio'
			,	isCheck = jQuery(this).attr('type') == 'checkbox';
			if(isRadio && !jQuery(this).attr('checked')) {
				needShow = false;
			}
			if(!needShow) {
				return;
			}
			if(isRadio) {
				jQuery('input[name="'+ jQuery(this).attr('name')+ '"]:first').parents('label:first').click();
				if(jQuery(this).parents('.iradio_minimal:first').size()) {
					var self = this;
					setTimeout(function(){
						jQuery(self).parents('.iradio_minimal:first').removeClass('checked');
					}, 10);
				}
			}
			var parent = null;
			if(jQuery(this).parents('#gmpPopupMainOpts').size()) {
				parent = jQuery(this).parents('label:first');
			} else if(jQuery(this).parents('.gmpPopupOptRow:first').size()) {
				parent = jQuery(this).parents('.gmpPopupOptRow:first');
			} else {
				parent = jQuery(this).parents('tr:first');
			}
			if(!parent.size()) return;
			var promoLink = parent.find('.gmpProOptMiniLabel a').attr('href');
			if(promoLink && promoLink != '') {
				jQuery('#gmpOptInProWnd a').attr('href', promoLink);
			}
			$proOptWnd.dialog('open');
			return false;
		});
	}
}
function gmpInitPlugNotices() {
	var $notices = jQuery('.supsystic-admin-notice');
	if($notices && $notices.size()) {
		$notices.each(function(){
			jQuery(this).find('.notice-dismiss').click(function(){
				var $notice = jQuery(this).parents('.supsystic-admin-notice');
				if(!$notice.data('stats-sent')) {
					// User closed this message - that is his choise, let's respect this and save it's saved status
					jQuery.sendFormGmp({
						data: {mod: 'supsystic_promo', action: 'addNoticeAction', code: $notice.data('code'), choice: 'hide'}
					});
				}
			});
			jQuery(this).find('[data-statistic-code]').click(function(){
				var href = jQuery(this).attr('href')
				,	$notice = jQuery(this).parents('.supsystic-admin-notice');
				jQuery.sendFormGmp({
					data: {mod: 'supsystic_promo', action: 'addNoticeAction', code: $notice.data('code'), choice: jQuery(this).data('statistic-code')}
				});
				$notice.data('stats-sent', 1).find('.notice-dismiss').trigger('click');
				if(!href || href === '' || href === '#')
					return false;
			});
		});
	}
}
function gmpGetMainPromoPopup() {
	if(jQuery('#gmpOptInProWnd').hasClass('ui-dialog-content')) {
		return jQuery('#gmpOptInProWnd');
	}
	return jQuery('#gmpOptInProWnd').dialog({
		modal:    true
	,	autoOpen: false
	,	width:	540
	,	height: 200
	});
}