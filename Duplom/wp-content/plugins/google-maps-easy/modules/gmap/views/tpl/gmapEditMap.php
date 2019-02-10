<?php
	//$isPro = frameGmp::_()->getModule('supsystic_promo')->isPro();
	$promoData = frameGmp::_()->getModule('supsystic_promo')->addPromoMapTabs();
	$addProElementAttrs = $this->isPro ? '' : ' title="'. esc_html(__("This option is available in <a target='_blank' href='%s'>PRO version</a> only, you can get it <a target='_blank' href='%s'>here.</a>", GMP_LANG_CODE)). '"';
	$addProElementClass = $this->isPro ? '' : 'supsystic-tooltip gmpProOpt';
	//$addProElementBottomHtml = $this->isPro ? '' : '<span class="gmpProOptMiniLabel"><a target="_blank" href="'. $this->mainLink. '">'. __('PRO option', GMP_LANG_CODE). '</a></span>';
	//$addProElementOptBottomHtml = $this->isPro ? '' : '<br /><span class="gmpProOptMiniLabel" style="padding-left: 0;"><a target="_blank" href="'. $this->mainLink. '">'. __('PRO option', GMP_LANG_CODE). '</a></span>';
	$isCustSearchAndMarkersPeriodAvailable = true;
	if($this->isPro) {	// It's not available for old PRO
		$isCustSearchAndMarkersPeriodAvailable = false;
		if(frameGmp::_()->getModule('custom_controls')
			&& method_exists(frameGmp::_()->getModule('custom_controls'), 'isCustSearchAndMarkersPeriodAvailable')
			&& frameGmp::_()->getModule('custom_controls')->isCustSearchAndMarkersPeriodAvailable()
		) {
			$isCustSearchAndMarkersPeriodAvailable = true;
		}
	}
?>
<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<div class="gmpMapBtns supsistic-half-side-box">
				<button id="gmpInsertToContactForm" class="button"><?php _e('Insert to Contact Form', GMP_LANG_CODE)?></button>
				<?php
				if(property_exists($this, 'membershipPluginError')) {
					echo $this->membershipPluginError;
				} else if(property_exists($this, 'pluginInstallUrl')) {
					echo '<a class="button" target="_blank" href="' . $this->pluginInstallUrl . '">';
					_e('Integrate with Membership', GMP_LANG_CODE);
					echo '</a>';
				} else if(property_exists($this, 'canUseMembershipFeature') && $this->canUseMembershipFeature == 1) {
					echo '<div class="mbs-turn-on-wrapper">';
					echo htmlGmp::checkboxHiddenVal('map_opts[membership-selectbox]', array(
						'value' => isset($this->map['params']['membershipEnable']) ? $this->map['params']['membershipEnable'] : 0,
						'attrs' => 'id="membershipPropEnable"',
					));
					echo '<label for="membershipPropEnable">' . _e('Enable for Membership', GMP_LANG_CODE) . '</label>';
					echo '</div>';
				}
				?>
			</div>
			<div class="supsistic-half-side-box" style="position: relative;">
				<select name="shortcode_example" id="gmpCopyTextCodeExamples" style="width: 35%; height: 32px; float: left; margin: 0; font-size: 16px;">
					<option value="shortcode"><?php _e('Map shortcode', GMP_LANG_CODE)?></option>
					<option value="php_code"><?php _e('PHP code', GMP_LANG_CODE)?></option>
				</select>
				<input type="text" name="gmpCopyTextCode" value="<?php _e('Shortcode will appear after you save map.', GMP_LANG_CODE)?>"
					class="gmpMapShortCodeShell gmpStaticWidth" style="width: 64%; height: 31px; float: right; margin: 0; text-align: center;" readonly="readonly">
			</div>
			<div style="clear: both;"></div>
			<?php do_action('gmp_lang_tabs'); ?>
			<div style="clear: both;"></div>
			<div id="gmpMapPropertiesTabs" style="display: none;">
				<h3 class="nav-tab-wrapper" style="margin-bottom: 12px;">
					<a class="nav-tab nav-tab-active" href="#gmpMapTab">
						<p>
							<i class="fa fa-globe" aria-hidden="true"></i>
							<?php _e('Map', GMP_LANG_CODE)?>
						</p>
					</a>
					<a class="nav-tab" href="#gmpMarkerTab">
						<p>
							<i class="fa fa-map-marker" style="font-size: 18px;"></i>
							<?php _e('Markers', GMP_LANG_CODE)?>
							<button class="button" id="gmpAddNewMarkerBtn">
								<?php _e('New', GMP_LANG_CODE)?>
							</button>
						</p>
					</a>
					<a class="nav-tab" href="#gmpShapeTab">
						<p>
							<i class="fa fa-cubes"></i>
							<?php _e('Figures', GMP_LANG_CODE)?>
							<button class="button gmpProOpt" id="gmpAddNewShapeBtn">
								<?php _e('New', GMP_LANG_CODE)?>
							</button>
						</p>
					</a>
					<a class="nav-tab" href="#gmpHeatmapTab">
						<p style="padding-top: 5px;">
							<i class="fa fa-map"></i>
							<?php _e('Heatmap Layer', GMP_LANG_CODE)?>
						</p>
					</a>
				</h3>
				<div style="clear: both;"></div>
				<div class="supsistic-half-side-box">
					<div id="gmpMapTab" class="gmpTabContent">
						<form id="gmpMapForm">
							<table class="form-table">
								<tr>
									<th scope="row">
										<label class="label-big" for="map_opts_title">
											<?php _e('Map Name', GMP_LANG_CODE)?>:
										</label>
										<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Your map name', GMP_LANG_CODE)?>"></i>
									</th>
									<td>
										<?php echo htmlGmp::text('map_opts[title]', array(
											'value' => $this->editMap ? $this->map['title'] : '',
											'attrs' => 'style="width: 100%;" id="map_opts_title"',
											'required' => true))?>
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label for="map_opts_width">
											<?php _e('Map Width', GMP_LANG_CODE)?>:
										</label>
										<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Your map width', GMP_LANG_CODE)?>"></i>
									</th>
									<td>
										<div class="sup-col sup-w-25">
											<?php echo htmlGmp::input('map_opts[width]', array(
												'value' => $this->editMap ? $this->map['html_options']['width'] : '100',
												'type' => 'number',
												'attrs' => 'style="width: 100%;" id="map_opts_width"'))?>
										</div>
										<div class="sup-col sup-w-75">
											<label class="supsystic-tooltip" title="<?php _e('Pixels', GMP_LANG_CODE)?>" style="margin-right: 15px; position: relative; top: 7px;"><?php echo htmlGmp::radiobutton('map_opts[width_units]', array(
												'value' => 'px',
												'checked' => $this->editMap ? htmlGmp::checkedOpt($this->map['params'], 'width_units', 'px') : false,
											))?>&nbsp;<?php _e('Px', GMP_LANG_CODE)?></label>
											<label style="margin-right: 15px; position: relative; top: 7px;"><?php echo htmlGmp::radiobutton('map_opts[width_units]', array(
												'value' => '%',
												'checked' => $this->editMap ? htmlGmp::checkedOpt($this->map['params'], 'width_units', '%') : true,
											))?>&nbsp;<?php _e('Percent', GMP_LANG_CODE)?></label>
										</div>
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label for="map_opts_height">
											<?php _e('Map Height', GMP_LANG_CODE)?>:
										</label>
										<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Your map height.' .
										'<br /><br />If Adapt map to screen height option is checked - map height will be recalculated on frontend and can be equals to:' .
										'<ul>' .
										'<li>1) your device screen height - height from top of page to top of map (if screen height > height from top of page to top of map)</li>' .
										'<li>2) your device screen height (in other cases)</li>' .
										'</ul>' .
										'Recalculation will be done for maps in page content and widgets except of maps which displaying in Google Maps Easy widget popup (Display as image mode).', GMP_LANG_CODE)?>"></i>
									</th>
									<td>
										<div class="gmpMainHeightOpts sup-col sup-w-50 no-p">
											<div class="sup-col sup-w-50" style="padding-right: 15px;">
												<?php echo htmlGmp::input('map_opts[height]', array(
													'value' => $this->editMap ? $this->map['html_options']['height'] : '250',
													'type' => 'number',
													'attrs' => 'style="width: 100%;" id="map_opts_height"'))?>
											</div>
											<div class="sup-col sup-w-50 no-p">
												<label class="supsystic-tooltip" title="<?php _e('Pixels', GMP_LANG_CODE)?>" style="margin-right: 15px; position: relative; top: 7px;"><?php echo htmlGmp::radiobutton('map_opts_height_units_is_constant', array(
													'value' => 'px',
													'checked' => true,
												))?>&nbsp;<?php _e('Px', GMP_LANG_CODE)?></label>
											</div>
										</div>
										<div class="gmpAdditionalHeightOpts sup-col sup-w-50 no-p" style="margin-top: 8px;">
											<?php echo htmlGmp::checkboxHiddenVal('map_opts[adapt_map_to_screen_height]', array(
													'value' => $this->editMap && isset($this->map['params']['adapt_map_to_screen_height']) ? $this->map['params']['adapt_map_to_screen_height'] : false,
											))?>
											<span style="vertical-align: middle;">
												<?php _e('Adapt map to screen height', GMP_LANG_CODE)?>
											</span>
										</div>
									</td>
								</tr>
							</table>
							<?php /*?><div id="gmpExtendOptsBtnShell" class="row-pad">
								<a href="#" id="gmpExtendOptsBtn" class="button"><?php _e('Extended Options', GMP_LANG_CODE)?></a>
							</div><?php */?>
							<div id="gmpExtendOptsShell" class="row">
								<table class="form-table">
									<tr>
										<th scope="row">
											<label for="map_opts_type_control">
												<?php _e('Map type control', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Control view for map type - you can see it in left upper corner by default', GMP_LANG_CODE)?>"></i>
										</th>
										<td>
											<div class="sup-col sup-w-50">
												<?php echo htmlGmp::selectbox('map_opts[type_control]', array(
													'options' => array('none' => __('None', GMP_LANG_CODE), 'DROPDOWN_MENU' => __('Dropdown Menu', GMP_LANG_CODE), 'HORIZONTAL_BAR' => __('Horizontal Bar', GMP_LANG_CODE)),
													'value' => $this->editMap && isset($this->map['params']['type_control']) ? $this->map['params']['type_control'] : 'HORIZONTAL_BAR',
													'attrs' => 'style="width: 100%;" id="map_opts_type_control"'))?>
											</div>
											<div class="sup-col sup-w-50">
												<?php $proLink = frameGmp::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=type_control_position&utm_campaign=googlemaps'); ?>
												<i class="fa fa-arrows supsystic-tooltip" title="<?php _e('Change type control position on map', GMP_LANG_CODE)?>"></i>
												<?php echo htmlGmp::selectbox('map_opts[type_control_position]', array(
													'options' => $this->positionsList,
													'value' => $this->editMap && isset($this->map['params']['type_control_position']) ? $this->map['params']['type_control_position'] : 'TOP_RIGHT',
													'attrs' => 'data-for="mapTypeControlOptions" class="gmpMapPosChangeSelect '. $addProElementClass. '"'. (empty($addProElementAttrs) ? '' : sprintf($addProElementAttrs, $proLink, $proLink))
												))?>
												<?php if(!$this->isPro) { ?>
													<span class="gmpProOptMiniLabel" style="padding-left: 20px;"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', GMP_LANG_CODE)?></a></span>
												<?php }?>
											</div>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_zoom_control">
												<?php _e('Zoom control', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Zoom control type on your map. Note, to view Zoom control on the map the Custom Map Controls option must be disabled.', GMP_LANG_CODE)?>"></i>
										</th>
										<td>
											<div>
												<div class="sup-col sup-w-50">
													<?php echo htmlGmp::selectbox('map_opts[zoom_control]', array(
														'options' => array('none' => __('None', GMP_LANG_CODE), 'DEFAULT' => __('Default', GMP_LANG_CODE)/*, 'LARGE' => __('Large', GMP_LANG_CODE), 'SMALL' => __('Small', GMP_LANG_CODE)*/),
														'value' => $this->editMap && isset($this->map['params']['zoom_control']) ? $this->map['params']['zoom_control'] : 'DEFAULT',
														'attrs' => 'style="width: 100%;" id="map_opts_zoom_control"'))?>
												</div>
												<div class="sup-col sup-w-50">
													<?php $proLink = frameGmp::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=zoom_control_position&utm_campaign=googlemaps'); ?>
													<i class="fa fa-arrows supsystic-tooltip" title="<?php _e('Change zoom control position on map', GMP_LANG_CODE)?>"></i>
													<?php echo htmlGmp::selectbox('map_opts[zoom_control_position]', array(
														'options' => $this->positionsList,
														'value' => $this->editMap && isset($this->map['params']['zoom_control_position']) ? $this->map['params']['zoom_control_position'] : 'TOP_LEFT',
														'attrs' => 'data-for="zoomControlOptions" class="gmpMapPosChangeSelect '. $addProElementClass. '"'. (empty($addProElementAttrs) ? '' : sprintf($addProElementAttrs, $proLink, $proLink))
													))?>
													<?php if(!$this->isPro) { ?>
														<span class="gmpProOptMiniLabel" style="padding-left: 20px;"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', GMP_LANG_CODE)?></a></span>
													<?php }?>
												</div>
											</div>
											<div id="gmpDefaultZoomDisable" style="display: none;" title="<?php _e('Notice', GMP_LANG_CODE)?>">
												<p>
													<?php printf(__('Standard Zoom control will not displaying for this map, because the Custom Map Controls option enabled now.', GMP_LANG_CODE))?>
												</p>
											</div>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_optsstreet_view_control_check">
												<?php _e('Street view control', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Street view control usually is located on right lower corner of your map', GMP_LANG_CODE)?>"></i>
										</th>
										<td>
											<div class="sup-col sup-w-50">
												<?php echo htmlGmp::checkboxHiddenVal('map_opts[street_view_control]', array(
													'value' => $this->editMap && isset($this->map['params']['street_view_control']) ? $this->map['params']['street_view_control'] : true,
												))?>
											</div>
											<div class="sup-col sup-w-50">
												<?php $proLink = frameGmp::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=street_view_control_position&utm_campaign=googlemaps'); ?>
												<i class="fa fa-arrows supsystic-tooltip" title="<?php _e('Change street view control position on map', GMP_LANG_CODE)?>"></i>
												<?php echo htmlGmp::selectbox('map_opts[street_view_control_position]', array(
													'options' => $this->positionsList,
													'value' => $this->editMap && isset($this->map['params']['street_view_control_position']) ? $this->map['params']['street_view_control_position'] : 'TOP_LEFT',
													'attrs' => 'data-for="streetViewControlOptions" class="gmpMapPosChangeSelect '. $addProElementClass. '"'. (empty($addProElementAttrs) ? '' : sprintf($addProElementAttrs, $proLink, $proLink))
												))?>
												<?php if(!$this->isPro) { ?>
													<span class="gmpProOptMiniLabel" style="padding-left: 20px;"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', GMP_LANG_CODE)?></a></span>
												<?php }?>
											</div>
										</td>
									</tr>
									<?php /* ?>
									<tr>
										<th scope="row">
											<label for="map_optspan_control_check">
												<?php _e('Pan control', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Pan control - allow you to pan over your map using mouse, usually is located on left upper corner of your map', GMP_LANG_CODE)?>"></i>
										</th>
										<td>
											<div class="sup-col sup-w-50">
												<?php echo htmlGmp::checkboxHiddenVal('map_opts[pan_control]', array(
													'value' => $this->editMap && isset($this->map['params']['pan_control']) ? $this->map['params']['pan_control'] : false,
												))?>
											</div>
											<div class="sup-col sup-w-50">
												<?php $proLink =frameGmp::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=pan_control_position&utm_campaign=googlemaps'); ?>
												<i class="fa fa-arrows supsystic-tooltip" title="<?php _e('Change pan control position on map', GMP_LANG_CODE)?>"></i>
												<?php echo htmlGmp::selectbox('map_opts[pan_control_position]', array(
													'options' => $this->positionsList,
													'value' => $this->editMap && isset($this->map['params']['pan_control_position']) ? $this->map['params']['pan_control_position'] : 'TOP_LEFT',
													'attrs' => 'data-for="panControlOptions" class="gmpMapPosChangeSelect '. $addProElementClass. '"'. (empty($addProElementAttrs) ? '' : sprintf($addProElementAttrs, $proLink, $proLink))
												))?>
												<?php if(!$this->isPro) { ?>
													<span class="gmpProOptMiniLabel" style="padding-left: 20px;"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', GMP_LANG_CODE)?></a></span>
												<?php }?>
											</div>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_overview_control">
												<?php _e('Overview control', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Overview control for your map, by default is disabled, and if enabled - is located on the right bottom corner', GMP_LANG_CODE)?>"></i>
										</th>
										<td>
											<div class="sup-col" style="width: 100%;">
												<?php echo htmlGmp::selectbox('map_opts[overview_control]', array(
													'options' => array('none' => __('None', GMP_LANG_CODE), 'opened' => __('Opened', GMP_LANG_CODE), 'collapsed' => __('Collapsed', GMP_LANG_CODE)),
													'value' => $this->editMap && isset($this->map['params']['overview_control']) ? $this->map['params']['overview_control'] : 'none',
													'attrs' => 'style="width: 100%;" id="map_opts_overview_control"'))?>
											</div>
										</td>
									</tr>
									<?php */ ?>
									
									<tr>
										<td colspan="2" style="padding: 0;">
											<table class="form-table">
												<tr>
													<th scope="row">
														<label for="map_optsdraggable_check">
															<?php _e('Draggable', GMP_LANG_CODE)?>:
														</label>
														<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Enable or disable possibility to drag your map using mouse', GMP_LANG_CODE)?>"></i>
													</th>
													<td>
														<?php echo htmlGmp::checkboxHiddenVal('map_opts[draggable]', array(
															'value' => $this->editMap && isset($this->map['params']['draggable']) ? $this->map['params']['draggable'] : true,
														))?>
													</td>
													<th scope="row">
														<label for="map_optsmouse_wheel_zoom_check">
															<?php _e('Mouse wheel to zoom', GMP_LANG_CODE)?>:
														</label>
														<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Sometimes you need to disable possibility to zoom your map using mouse wheel. This can be required for example - if you need to use your wheel for some other action, for example scroll your site even if mouse is over your map.', GMP_LANG_CODE)?>"></i>
													</th>
													<td>
														<?php echo htmlGmp::checkboxHiddenVal('map_opts[mouse_wheel_zoom]', array(
															'value' => $this->editMap && isset($this->map['params']['mouse_wheel_zoom']) ? $this->map['params']['mouse_wheel_zoom'] : true,
														))?>
													</td>
												</tr>
												<tr>
													<th scope="row">
														<label for="map_optsdbl_click_zoom_check">
															<?php _e('Double click to zoom', GMP_LANG_CODE)?>:
														</label>
														<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('By default double left click on map will zoom it in. But you can change this here.', GMP_LANG_CODE)?>"></i>
													</th>
													<td>
														<?php echo htmlGmp::checkboxHiddenVal('map_opts[dbl_click_zoom]', array(
															'value' => $this->editMap && isset($this->map['params']['dbl_click_zoom']) ? $this->map['params']['dbl_click_zoom'] : true,
														))?>
													</td>
													<th scope="row">
														<label for="map_optsis_static_check">
															<?php _e('Set Static', GMP_LANG_CODE)?>:
														</label>
														<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Show map as a Static image. This will allow you to make it cheeper according to new Google Maps API usage Rates. Be aware - not all options will work in this mode!', GMP_LANG_CODE)?>"></i>
													</th>
													<td>
														<?php echo htmlGmp::checkboxHiddenVal('map_opts[is_static]', array(
															'value' => $this->editMap && isset($this->map['params']['is_static']) ? $this->map['params']['is_static'] : false,
															'attrs' => 'class="gmpProOpt"',
														))?>
														<?php $proLink = frameGmp::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=static_map&utm_campaign=googlemaps'); ?>
														<?php if(!$this->isPro) { ?>
															<span class="gmpProOptMiniLabel" style="padding-left: 20px;"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', GMP_LANG_CODE)?></a></span>
														<?php }?>
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_map_center_address" class="sup-medium-label">
												<?php _e('Map Center', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Sets map center. You can set map center in next ways: type address to use its coords, type the coords\' values in appropriate fields or just drag the map on preview.', GMP_LANG_CODE)?>"></i>
										</th>
										<td>
											<div>
												<label for="map_opts_map_center_address">
													<?php _e('Address', GMP_LANG_CODE)?>
												</label>
												<?php echo htmlGmp::text('map_opts[map_center][address]', array(
													'value' => $this->editMap && isset($this->map['params']['map_center']['address']) ? $this->map['params']['map_center']['address'] : '',
													'placeholder' => '603 Park Avenue, Brooklyn, NY 11206, USA',
													'attrs' => 'style="width: 100%;" id="map_opts_map_center_address"'))?>
											</div>
											<div class="sup-col sup-w-50" style="margin-top: 10px;">
												<label for="map_opts_map_center_coord_x">
													<?php _e('Latitude', GMP_LANG_CODE)?>
												</label>
												<?php echo htmlGmp::text('map_opts[map_center][coord_x]', array(
													'value' => $this->editMap ? $this->map['params']['map_center']['coord_x'] : '',
													'attrs' => 'style="width: 100%;" id="map_opts_map_center_coord_x"'))?>
											</div>
											<div class="sup-col sup-w-50" style="margin-top: 10px;">
												<label for="map_opts_map_center_coord_y">
													<?php _e('Longitude', GMP_LANG_CODE)?>
												</label>
												<?php echo htmlGmp::text('map_opts[map_center][coord_y]', array(
													'value' => $this->editMap ? $this->map['params']['map_center']['coord_y'] : '',
													'attrs' => 'style="width: 100%;" id="map_opts_map_center_coord_y"'))?>
											</div>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_zoom_type" class="sup-medium-label">
												<?php _e('Map Zoom', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Sets map zoom.<br /><br />
											<b>Preset Zoom</b> - sets zoom value for map. You can change this value just change zoom on the map preview.<br /><br />
											<b>Fit Bounds</b> - map zoom will be changed on frontend in a way that all markers and figures will be visible.<br /><br />
											<b>Min Zoom Level</b> - sets minimum zoom level (maximum estrangement), which can be applied for map.<br /><br />
											<b>Max Zoom Level</b> - sets maximum zoom level (maximum approximation), which can be applied for map.
											', GMP_LANG_CODE)?>"></i>
										</th>
										<td>
											<?php
												$zoomMin = 1;
												$zoomMax = 21;
												$zoomRange = array_combine(range($zoomMin, $zoomMax), range($zoomMin, $zoomMax));
											?>
											<?php echo htmlGmp::selectbox('map_opts[zoom_type]', array(
												'options' => array('zoom_level' => __('Preset Zoom', GMP_LANG_CODE), 'fit_bounds' => __('Fit Bounds', GMP_LANG_CODE)),
												'value' => $this->editMap && isset($this->map['params']['zoom_type']) ? $this->map['params']['zoom_type'] : 'zoom_level',
												'attrs' => 'style="width: 100%;"'))?>
											<div id="zoom_type_options">
												<div>
													<div class="zoom_level sup-col sup-w-50" style="display: none; margin-top: 10px;">
														<label for="map_opts_zoom">
															<?php _e('Zoom Level', GMP_LANG_CODE)?>
														</label>
														<?php echo htmlGmp::selectbox('map_opts[zoom]', array(
															'options' => $zoomRange,
															'value' => $this->editMap && isset($this->map['params']['zoom']) ? $this->map['params']['zoom'] : 8,
															'attrs' => 'style="width: 100%;"'))?>
														<?php //echo htmlGmp::hidden('map_opts[zoom]', array('value' => $this->editMap ? $this->map['params']['zoom'] : ''))?>
													</div>
													<div class="zoom_level sup-col sup-w-50" style="display: none; margin-top: 10px;">
														<label for="map_opts_zoom_mobile">
															<?php _e('Mobile Zoom Level', GMP_LANG_CODE)?>
														</label>
														<?php echo htmlGmp::selectbox('map_opts[zoom_mobile]', array(
															'options' => $zoomRange,
															'value' => $this->editMap && isset($this->map['params']['zoom_mobile']) ? $this->map['params']['zoom_mobile'] : 8,
															'attrs' => 'style="width: 100%;"'))?>
													</div>
												</div>
												<div>
													<div class="zoom_min_level sup-col sup-w-50" style="margin-top: 10px;">
														<label for="map_opts_zoom_min">
															<?php _e('Min Zoom Level', GMP_LANG_CODE)?>
														</label>
														<?php echo htmlGmp::selectbox('map_opts[zoom_min]', array(
															'options' => $zoomRange,
															'value' => $this->editMap && isset($this->map['params']['zoom_min']) ? $this->map['params']['zoom_min'] : $zoomMin,
															'attrs' => 'style="width: 100%;"'))?>
													</div>
													<div class="zoom_max_level sup-col sup-w-50" style="margin-top: 10px;">
														<label for="map_opts_zoom_max">
															<?php _e('Max Zoom Level', GMP_LANG_CODE)?>
														</label>
														<?php echo htmlGmp::selectbox('map_opts[zoom_max]', array(
															'options' => $zoomRange,
															'value' => $this->editMap && isset($this->map['params']['zoom_max']) ? $this->map['params']['zoom_max'] : $zoomMax,
															'attrs' => 'style="width: 100%;"'))?>
													</div>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_map_type">
												<?php _e('Google Map Theme', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('You can select your Google Map theme - Road Map, Hybrid, Satellite or Terrain - here. By default your map will have Road Map Google maps theme.', GMP_LANG_CODE)?>"></i>
										</th>
										<td>
											<?php echo htmlGmp::selectbox('map_opts[map_type]', array(
												'options' => array('ROADMAP' => __('Road Map', GMP_LANG_CODE), 'HYBRID' => __('Hybrid', GMP_LANG_CODE), 'SATELLITE' => __('Satellite', GMP_LANG_CODE), 'TERRAIN' => __('Terrain', GMP_LANG_CODE)),
												'value' => $this->editMap && isset($this->map['params']['map_type']) ? $this->map['params']['map_type'] : 'ROADMAP',
												'attrs' => 'style="width: 100%;" id="map_opts_map_type"'))?>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_map_stylization">
												<?php _e('Map Stylization', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Make your map unique with our Map Themes, just try to change it here - and you will see results on your Map Preview.', GMP_LANG_CODE)?>"></i>
										</th>
										<td>
											<?php echo htmlGmp::selectbox('map_opts[map_stylization]', array(
												'options' => $this->stylizationsForSelect,
												'value' => $this->editMap && isset($this->map['params']['map_stylization']) ? $this->map['params']['map_stylization'] : 'none',
												'attrs' => 'style="width: '. ($this->isPro ? '100%' : 'calc(100% - 200px)'). ';" id="map_opts_map_stylization"'))?>
											<?php if(!$this->isPro) {?>
												<a target="_blank" href="<?php echo $this->mainLink;?>" class="sup-standard-link">
													<i class="fa fa-plus"></i>
													<?php _e('Get 300+ Themes with PRO', GMP_LANG_CODE)?>
												</a>
											<?php }?>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_marker_clasterer" class="sup-medium-label">
												<?php _e('Markers Clusterization', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('If you have many markers - you can have a problems with viewing them when zoom out for example: they will just cover each-other. Marker clusterization can solve this problem by grouping your markers in groups when they are too close to each-other.', GMP_LANG_CODE)?>"></i>
										</th>
										<td>
											<?php echo htmlGmp::selectbox('map_opts[marker_clasterer]', array(
												'options' => array('none' => __('None', GMP_LANG_CODE), 'MarkerClusterer' => __('Base Clusterization', GMP_LANG_CODE)),
												'value' => $this->editMap && isset($this->map['params']['marker_clasterer']) ? $this->map['params']['marker_clasterer'] : 'none',
												'attrs' => 'style="width: 100%;" id="map_opts_marker_clasterer"'));

											// Prevent to use old default claster icon cdn icon because it is missing
											$oldDefClasterIcon = 'https://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/images/m1.png';
											$curClusterIcon = uriGmp::_(
												$this->editMap
												&& isset($this->map['params']['marker_clasterer_icon'])
												&& $this->map['params']['marker_clasterer_icon']
												&& $this->map['params']['marker_clasterer_icon'] != $oldDefClasterIcon
													? $this->map['params']['marker_clasterer_icon']
													: GMP_MODULES_PATH . '/gmap/img/m1.png');
											$curClusterIconWidth =
												$this->editMap
												&& isset($this->map['params']['marker_clasterer_icon_width'])
												&& $this->map['params']['marker_clasterer_icon_width']
													? $this->map['params']['marker_clasterer_icon_width']
													: 53;
											$curClusterIconHeight =
												$this->editMap
												&& isset($this->map['params']['marker_clasterer_icon_height'])
												&& $this->map['params']['marker_clasterer_icon_height']
													? $this->map['params']['marker_clasterer_icon_height']
													: 52;
											?>
											<div id="gmpMarkerClastererSubOpts" style="display: none;">
												<div class="gmpClastererSubOpts">
													<div class="sup-col" style="max-width: 50%; min-width: 20%; float: right; padding: 0; text-align: center;">
														<a id="gmpUploadClastererIconBtn" href="#" class="button" style="width: 100%; margin-bottom: 5px;"><?php _e('Upload Icon', GMP_LANG_CODE)?></a><br />
														<a id="gmpDefaultClastererIconBtn" href="#" class="button" style="width: 100%; margin-bottom: 5px;"><?php _e('Default Icon', GMP_LANG_CODE)?></a>
														<div class="gmpClastererUplRes"></div>
													</div>
													<label for="map_opts_marker_clasterer_icon">
														<?php _e('Cluster Icon', GMP_LANG_CODE)?>
													</label><br />
													<img id="gmpMarkerClastererIconPrevImg" src="<?php echo $curClusterIcon?>" style="max-width: 53px; height: auto; margin: 5px 0;" />
													<?php echo htmlGmp::hidden('map_opts[marker_clasterer_icon]', array('value' => $curClusterIcon, ))?>
													<?php echo htmlGmp::hidden('map_opts[marker_clasterer_icon_width]', array('value' => $curClusterIconWidth, ))?>
													<?php echo htmlGmp::hidden('map_opts[marker_clasterer_icon_height]', array('value' => $curClusterIconHeight, ))?>
													<div style="clear: both;"></div>
												</div>
												<div class="gmpClastererSubOpts">
													<label for="map_opts_marker_clasterer_grid_size">
														<?php _e('Cluster Area Size', GMP_LANG_CODE)?>
													</label>
													<i class="fa fa-question supsystic-tooltip" title="<?php _e('Sets the grid size of cluster. The higher the size - the more area of capture the markers to the cluster.', GMP_LANG_CODE)?>"></i>
													<br />
													<div class="sup-col sup-w-75">
														<?php echo htmlGmp::text('map_opts[marker_clasterer_grid_size]', array(
															'value' => $this->editMap && isset($this->map['params']['marker_clasterer_grid_size']) ? $this->map['params']['marker_clasterer_grid_size'] : '60',
															'attrs' => 'style="width: 100%;" id="gmpMarkerClastererGridSize" '))?>
													</div>
													<div class="sup-col" style="max-width: 50%; min-width: 20%; float: right; padding: 0; text-align: center;">
														<a id="gmpDefaultClastererGridSizeBtn" href="#" class="button" style="width: 100%; margin-bottom: 5px;"><?php _e('Default', GMP_LANG_CODE)?></a>
													</div>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_markers_list_type">
												<?php _e('Markers List', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Display all map markers - as list below Your map. This will help your users get more info about your markers and find required marker more faster.', GMP_LANG_CODE)?>"></i>
											<?php if(!$this->isPro) { ?>
												<?php $proLink = frameGmp::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=markers_list&utm_campaign=googlemaps'); ?>
												<br /><span class="gmpProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', GMP_LANG_CODE)?></a></span>
											<?php }?>
										</th>
										<td>
											<a id="gmpMapMarkersListBtn" href="#" class="button"><?php _e('Select Markers List type', GMP_LANG_CODE)?></a>
											<?php echo htmlGmp::hidden('map_opts[markers_list_type]', array(
												'value' => $this->editMap && isset($this->map['params']['markers_list_type']) ? $this->map['params']['markers_list_type'] : ''))?>
											<div id="gmpMapMarkersListSettings" style="display: none;">
												<div style="margin-top: 10px;">
													<label for="map_opts_markers_list_color">
														<?php _e('Markers List Color', GMP_LANG_CODE)?>
													</label></br>
													<?php echo htmlGmp::colorpicker('map_opts[markers_list_color]', array(
														'value' => $this->editMap && isset($this->map['params']['markers_list_color']) ? $this->map['params']['markers_list_color'] : '#55BA68'))?>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_enable_trafic_layer">
												<?php _e('Traffic Layer', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Add real-time traffic information to your map.', GMP_LANG_CODE)?>"></i>
											<?php if(!$this->isPro) { ?>
												<?php $proLink = frameGmp::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=trafic_layer&utm_campaign=googlemaps'); ?>
												<br /><span class="gmpProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', GMP_LANG_CODE)?></a></span>
											<?php }?>
										</th>
										<td>
											<?php echo htmlGmp::checkboxHiddenVal('map_opts[enable_trafic_layer]', array(
												'value' => $this->editMap && isset($this->map['params']['enable_trafic_layer']) ? $this->map['params']['enable_trafic_layer'] : false,
												'attrs' => 'class="gmpProOpt"'))?>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_enable_transit_layer">
												<?php _e('Transit Layer', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Display the public transit network of a city on your map. When the Transit Layer is enabled, and the map is centered on a city that supports transit information, the map will display major transit lines as thick, colored lines.', GMP_LANG_CODE)?>"></i>
											<?php if(!$this->isPro) { ?>
												<?php $proLink = frameGmp::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=transit_layer&utm_campaign=googlemaps'); ?>
												<br /><span class="gmpProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', GMP_LANG_CODE)?></a></span>
											<?php }?>
										</th>
										<td>
											<?php echo htmlGmp::checkboxHiddenVal('map_opts[enable_transit_layer]', array(
												'value' => $this->editMap && isset($this->map['params']['enable_transit_layer']) ? $this->map['params']['enable_transit_layer'] : false,
												'attrs' => 'class="gmpProOpt"'))?>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_enable_bicycling_layer">
												<?php _e('Bicycling Layer', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Add a layer of bike paths, suggested bike routes and other overlays specific to bicycling usage on top of the given map.Dark green routes indicated dedicated bicycle routes. Light green routes indicate streets with dedicated bike lanes. Dashed routes indicate streets or paths otherwise recommended for bicycle usage.', GMP_LANG_CODE)?>"></i>
											<?php if(!$this->isPro) { ?>
												<?php $proLink = frameGmp::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=bicycling_layer&utm_campaign=googlemaps'); ?>
												<br /><span class="gmpProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', GMP_LANG_CODE)?></a></span>
											<?php }?>
										</th>
										<td>
											<?php echo htmlGmp::checkboxHiddenVal('map_opts[enable_bicycling_layer]', array(
												'value' => $this->editMap && isset($this->map['params']['enable_bicycling_layer']) ? $this->map['params']['enable_bicycling_layer'] : false,
												'attrs' => 'class="gmpProOpt"'))?>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<?php if(!$this->isPro) { ?>
												<?php $proLink = frameGmp::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=add_kml_layers&utm_campaign=googlemaps'); ?>
											<?php }?>
											<label for="map_opts_add_kml_layers">
												<?php _e('Add KML layers', GMP_LANG_CODE)?>:
											</label>
											<i class="fa fa-question supsystic-tooltip" style="float: right;" title="<?php _e('Add KML files to display custom layers on the map. Additional options:' .
												'<br /><br /><b>Enable KML layers filter</b> - add form to map for dynamically enable / disable KML layers and sublayers. <br /><br />  <b>Load KML faster</b> - Use for large KML files. <b>Warning </b>-  filters will stop working!<br', GMP_LANG_CODE);
												if(!$this->isPro){
													echo esc_html('<a href="'. $proLink. '" target="_blank"><img src="'. $this->promoModPath. 'img/kml/kml.png" /></a>');
												}?>"
											></i>
											<?php if(!$this->isPro) { ?>
												<br /><span class="gmpProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', GMP_LANG_CODE)?></a></span>
											<?php }?>
										</th>
										<td>
											<div style="margin-top: 10px;">
												<label for="map_opts_enable_google_kml_api">
													<?php echo htmlGmp::checkboxHiddenVal('map_opts[enable_google_kml_api]', array(
														'value' => $this->editMap && isset($this->map['params']['enable_google_kml_api']) ? $this->map['params']['enable_google_kml_api'] : false,
														'attrs' => 'class="gmpProOpt" id="map_opts_enable_google_kml_api"'))?>
													<?php _e('Load kml faster', GMP_LANG_CODE)?>
												</label>
											</div>
											<?php
											$hiddenClass = '';
											if(isset($this->map['params']['enable_google_kml_api']) && !empty($this->map['params']['enable_google_kml_api'])){
												$hiddenClass = 'gmpHidden';
											}
											?>
											<div style="margin-top: 10px;" class="<?php echo $hiddenClass;?>" >
												<label for="map_opts_enable_kml_filter">
													<?php echo htmlGmp::checkboxHiddenVal('map_opts[enable_kml_filter]', array(
														'value' => $this->editMap && isset($this->map['params']['enable_kml_filter']) ? $this->map['params']['enable_kml_filter'] : false,
														'attrs' => 'class="gmpProOpt" id="map_opts_enable_kml_filter"'))?>
													<?php _e('Enable KML layers filter', GMP_LANG_CODE)?>
												</label>
											</div>

											<div id="gmpKmlFileRowExample" class="gmpKmlFileRow" style="display: none; margin-top: 10px;">
												<div style="clear: both;"></div>
												<label><?php _e('Enter KML file URL', GMP_LANG_CODE)?></label>
												<label class="gmpShowSublayersLabel" style="float: right;">
													<?php echo htmlGmp::hidden('map_opts[kml_filter][show_sublayers][]', array('value' => '', 'attrs' => 'class="gmpShowSublayersInput gmpProOpt" disabled="disabled"'))?>
													<?php _e('Hide Sublayers at KML filter', GMP_LANG_CODE)?>
												</label>
												<div style="clear: both;"></div>
												<a href="#" title="<?php _e('Remove KML field', GMP_LANG_CODE)?>" class="button gmpProOpt" onclick="gmpKmlRemoveFileRowBtnClick(this); return false;">
													<i class="fa fa-trash-o"></i>
												</a>
												<?php echo htmlGmp::text('map_opts[kml_file_url][]', array('value' => '', 'attrs' => 'class="gmpProOpt" style="width: 86%; float: right;" disabled="disabled"'))?>
												<span class="gmpKmlUploadMsg" style="float: right; width: 100%; text-align: right;" ></span>
												<a 	href="#"
													class="gmpKmlUploadFileBtn button gmpProOpt"
													data-nonce="<?php echo wp_create_nonce('upload-kml-file')?>"
													data-url="<?php echo uriGmp::_(array(
														'baseUrl' => admin_url('admin-ajax.php'),
														'page' => 'kml',
														'action' => 'addFromFile',
														'reqType' => 'ajax',
														'pl' => GMP_CODE))?>"
													id="gmpKmlUploadFileBtn"
													style="margin: 5px 0px; float: right;">
													<?php _e('or Upload KML file', GMP_LANG_CODE)?>
												</a><br />
												<label class="gmpKmlImportToMarkerLbl">
													<span class="gmpKitmLblText"><?php _e('Import markers from layer', GMP_LANG_CODE); ?></span>
												</label>
											</div>
											<?php
												if(!empty($this->map['params']['kml_import_to_marker'])
													&& count($this->map['params']['kml_import_to_marker'])
												) {
													foreach($this->map['params']['kml_import_to_marker'] as $omKey => $oneMarker) {
														$isKmlImpToMarkerVal = 0;
														if($oneMarker == 'on') {
															$isKmlImpToMarkerVal = 1;
														}
														echo htmlGmp::hidden('map_opts[kml_import_to_marker][]', array(
															'value' => $isKmlImpToMarkerVal,
															'attrs' => ' class="gmpProOpt gmpKmlImportToMarkerHid" data-order="' . $omKey . '" ',
														));
													}
												}
											?>
											<div id="gmpKmlFileRowsShell"></div>
											<a href="#" class="button gmpProOpt" id="gmpKmlAddFileRowBtn" style="margin: 5px 5px 5px 0px; float: left;">
												<?php _e('Add more files', GMP_LANG_CODE)?>
											</a>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<?php if(!$this->isPro) { ?>
												<?php $proLink = frameGmp::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=enable_custom_map_controls&utm_campaign=googlemaps'); ?>
											<?php }?>
											<label for="map_opts_enable_custom_map_controls">
												<?php _e('Custom Map Controls', GMP_LANG_CODE)?>:
											</label>
											<i
												style="float: right;"
												class="fa fa-question supsystic-tooltip"
												title="<?php _e('Add custom map controls to the map.', GMP_LANG_CODE);
													if(!$this->isPro){
														echo esc_html('<a href="'. $proLink. '" target="_blank"><img src="'. $this->promoModPath. 'img/custom_controls/custom_map_controls.png" /></a>');
													}?>"
											></i>
											<?php if(!$this->isPro) { ?>
												<br /><span class="gmpProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', GMP_LANG_CODE)?></a></span>
											<?php }?>
										</th>
										<td>
											<?php echo htmlGmp::checkboxHiddenVal('map_opts[enable_custom_map_controls]', array(
												'value' => $this->editMap && isset($this->map['params']['enable_custom_map_controls']) ? $this->map['params']['enable_custom_map_controls'] : false,
												'attrs' => 'class="gmpProOpt" onclick="gmpAddCustomControlsOptions()"'))?>
											<div id="custom_controls_options" style="display: none;">
												<div style="margin-top: 10px;">
												<label for="map_opts_custom_controls_type">
													<?php _e('Controls type', GMP_LANG_CODE)?>
												</label>
												<?php echo htmlGmp::selectbox('map_opts[custom_controls_type]', array(
													'options' => array('gmpSquareControls' => __('Square', GMP_LANG_CODE), 'gmpRoundedEdgesControls' => __('Rounded edges', GMP_LANG_CODE), 'gmpRoundControls' => __('Round', GMP_LANG_CODE)),
													'value' => $this->editMap && isset($this->map['params']['custom_controls_type']) ? $this->map['params']['custom_controls_type'] : 'round',
													'attrs' => 'class="gmpProOpt" style="width: 100%;" id="map_opts_custom_controls_type"'))?>
												</div>
												<div style="margin-top: 10px;">
													<label for="map_opts_custom_controls_bg_color">
														<?php _e('Background color', GMP_LANG_CODE)?>
													</label></br>
													<?php echo htmlGmp::colorpicker('map_opts[custom_controls_bg_color]', array(
														'attrs' => 'class="gmpProOpt"',
														'value' => $this->editMap && isset($this->map['params']['custom_controls_bg_color']) ? $this->map['params']['custom_controls_bg_color'] : '#55BA68'))?>
												</div>
												<div style="margin-top: 10px;">
												<label for="map_opts_custom_controls_txt_color">
													<?php _e('Text color', GMP_LANG_CODE)?>
												</label></br>
												<?php echo htmlGmp::colorpicker('map_opts[custom_controls_txt_color]', array(
													'attrs' => 'class="gmpProOpt"',
													'value' => $this->editMap && isset($this->map['params']['custom_controls_txt_color']) ? $this->map['params']['custom_controls_txt_color'] : '#000000'))?>
												</div>
												<div style="margin-top: 10px;">
													<label for="map_opts_custom_controls_position">
														<?php _e('Controls position', GMP_LANG_CODE)?>
													</label>
													<?php echo htmlGmp::selectbox('map_opts[custom_controls_position]', array(
														'options' => $this->positionsList,
														'value' => $this->editMap && isset($this->map['params']['custom_controls_position']) ? $this->map['params']['custom_controls_position'] : 'TOP_LEFT',
														'attrs' => 'class="gmpProOpt" style="width: 100%;" id="map_opts_custom_controls_position"'
													))?>
												</div>
												<div style="margin-top: 10px;">
													<label for="map_opts_custom_controls_slider_min">
														<?php _e('Min Search Radius (in meters):', GMP_LANG_CODE)?>
													</label></br>
													<?php echo htmlGmp::text('map_opts[custom_controls_slider_min]', array(
														'value' => $this->editMap && isset($this->map['params']['custom_controls_slider_min']) ? $this->map['params']['custom_controls_slider_min'] : '100',
														'attrs' => 'class="gmpProOpt" style="width: 100%;" id="map_opts_custom_controls_slider_min"'))?>
												</div>
												<div style="margin-top: 10px;">
													<label for="map_opts_custom_controls_slider_max">
														<?php _e('Max Search Radius (in meters):', GMP_LANG_CODE)?>
													</label></br>
													<?php echo htmlGmp::text('map_opts[custom_controls_slider_max]', array(
														'value' => $this->editMap && isset($this->map['params']['custom_controls_slider_max']) ? $this->map['params']['custom_controls_slider_max'] : '1000',
														'attrs' => 'class="gmpProOpt" style="width: 100%;" id="map_opts_custom_controls_slider_max"'))?>
												</div>
												<div style="margin-top: 10px;">
													<label for="map_opts_custom_controls_slider_step">
														<?php _e('Search Step (in meters):', GMP_LANG_CODE)?>
													</label></br>
													<?php echo htmlGmp::text('map_opts[custom_controls_slider_step]', array(
														'value' => $this->editMap && isset($this->map['params']['custom_controls_slider_step']) ? $this->map['params']['custom_controls_slider_step'] : '10',
														'attrs' => 'class="gmpProOpt" style="width: 100%;" id="map_opts_custom_controls_slider_step"'))?>
												</div>
												<div style="margin-top: 10px;">
													<label for="map_opts_custom_controls_search_country">
														<?php _e('Search Country', GMP_LANG_CODE)?>
													</label>
													<?php echo htmlGmp::selectbox('map_opts[custom_controls_search_country]', array(
														'options' => array_merge(array('' => 'All Countries'), $this->countries),
														'value' => $this->editMap && isset($this->map['params']['custom_controls_search_country']) ? $this->map['params']['custom_controls_search_country'] : 'round',
														'attrs' => 'class="gmpProOpt" style="width: 100%;" id="map_opts_custom_controls_search_country"'))?>
												</div>
												<?php if($isCustSearchAndMarkersPeriodAvailable) { ?>
												<div style="margin-top: 10px;">
													<label>
														<?php
															$isCustomSearchParamArr = array();
															if(!empty($this->map['params']['custom_controls_improve_search'])
																&& $this->map['params']['custom_controls_improve_search'] == 1
															) {
																$isCustomSearchParamArr = array('checked' => 'checked');
															}
														echo htmlGmp::checkbox('map_opts[custom_controls_improve_search]', $isCustomSearchParamArr)?>
														<?php _e('Use improved markers search', GMP_LANG_CODE);?>
													</label>
													<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('This option allows you to search and show multiple markers for selected date, categories and keywords. NOTE: it removes separate markers categories filter button from custom map controls.', GMP_LANG_CODE); ?>"></i>
												</div>
												<?php }?>
												<div style="margin-top: 10px;">
													<label>
														<?php
														$isFilterEnable = array();
														if(!empty($this->map['params']['button_filter_enable'])
															&& $this->map['params']['button_filter_enable'] == 1
														) {
															$isFilterEnable = array('checked' => 'checked');
														}
														echo htmlGmp::checkbox('map_opts[button_filter_enable]', $isFilterEnable)?>
														<?php _e('Disable filter button', GMP_LANG_CODE);?>
													</label>
													<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Check this option if you want to disable filters button on frontend', GMP_LANG_CODE); ?>"></i>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_enable_full_screen_btn">
												<?php _e('Full Screen Button', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Add a button on map to open it full screen.', GMP_LANG_CODE)?>"></i>
											<?php if(!$this->isPro) { ?>
												<?php $proLink = frameGmp::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=enable_full_screen_btn&utm_campaign=googlemaps'); ?>
												<br /><span class="gmpProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', GMP_LANG_CODE)?></a></span>
											<?php }?>
										</th>
										<td>
											<?php echo htmlGmp::checkboxHiddenVal('map_opts[enable_full_screen_btn]', array(
												'value' => $this->editMap && isset($this->map['params']['enable_full_screen_btn']) ? $this->map['params']['enable_full_screen_btn'] : false,
												'attrs' => 'class="gmpProOpt"'))?>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_hide_poi">
												<?php _e('Hide POI', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Hide the Points Of Interest - landmark or other object, the marked points on the map, for example: hotels, campsites, fuel stations etc.', GMP_LANG_CODE)?>"></i>
											<?php if(!$this->isPro) { ?>
												<?php $proLink = frameGmp::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=hide_poi&utm_campaign=googlemaps'); ?>
												<br /><span class="gmpProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', GMP_LANG_CODE)?></a></span>
											<?php }?>
										</th>
										<td>
											<?php echo htmlGmp::checkboxHiddenVal('map_opts[hide_poi]', array(
												'value' => $this->editMap && isset($this->map['params']['hide_poi']) ? $this->map['params']['hide_poi'] : false,
												'attrs' => 'class="gmpProOpt"'))?>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_hide_countries">
												<?php _e('Hide Countries', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Hide all administrative data about countries: names, borders etc.', GMP_LANG_CODE)?>"></i>
											<?php if(!$this->isPro) { ?>
												<?php $proLink = frameGmp::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=hide_countries&utm_campaign=googlemaps'); ?>
												<br /><span class="gmpProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', GMP_LANG_CODE)?></a></span>
											<?php }?>
										</th>
										<td>
											<?php echo htmlGmp::checkboxHiddenVal('map_opts[hide_countries]', array(
												'value' => $this->editMap && isset($this->map['params']['hide_countries']) ? $this->map['params']['hide_countries'] : false,
												'attrs' => 'class="gmpProOpt"'))?>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_hide_marker icon title">
												<?php _e('Hide Tooltips of Markers', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Hide the tooltips, which displayed by mouse hover on markers\' icons.', GMP_LANG_CODE)?>"></i>
										</th>
										<td>
											<?php echo htmlGmp::checkboxHiddenVal('map_opts[hide_marker_tooltip]', array(
												'value' => $this->editMap && isset($this->map['params']['hide_marker_tooltip']) ? $this->map['params']['hide_marker_tooltip'] : false,
											))?>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_center_on_cur_marker_infownd">
												<?php _e('Center on current opened marker', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('On frontend the map will be centered on current marker with opened info window.', GMP_LANG_CODE)?>"></i>
										</th>
										<td>
											<?php echo htmlGmp::checkboxHiddenVal('map_opts[center_on_cur_marker_infownd]', array(
												'value' => $this->editMap && isset($this->map['params']['center_on_cur_marker_infownd']) ? $this->map['params']['center_on_cur_marker_infownd'] : false,
												'attrs' => ''))?>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_center_on_cur_user_pos">
												<?php _e('Center on current user location', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('On frontend map will be centered on current user location.', GMP_LANG_CODE)?>"></i>
											<?php if(!$this->isPro) { ?>
												<?php $proLink = frameGmp::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=center_on_cur_user_pos&utm_campaign=googlemaps'); ?>
												<br /><span class="gmpProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', GMP_LANG_CODE)?></a></span>
											<?php }?>
										</th>
										<td>
											<?php echo htmlGmp::checkboxHiddenVal('map_opts[center_on_cur_user_pos]', array(
												'value' => $this->editMap && isset($this->map['params']['center_on_cur_user_pos']) ? $this->map['params']['center_on_cur_user_pos'] : false,
												'attrs' => 'class="gmpProOpt"'))?>
											<div id="gmpCurUserPosOptions" style="margin-top: 10px; display: none;">
												<?php echo htmlGmp::hidden('map_opts[center_on_cur_user_pos_icon]', array(
													'value' => $this->editMap && isset($this->map['params']['center_on_cur_user_pos_icon'])
														? $this->map['params']['center_on_cur_user_pos_icon']
														: 1 /*Default Icon ID*/ ))?>
												<img id="gmpCurUserPosIconPrevImg" src="" style="float: left;" />
												<div style="float: right">
													<a href="#" id="gmpCurUserPosIconBtn" class="button gmpProOpt"><?php _e('Choose Icon', GMP_LANG_CODE)?></a>
													<a href="#" id="gmpUploadCurUserPosIconBtn" class="button gmpProOpt"><?php _e('Upload Icon', GMP_LANG_CODE)?></a>
													<div class="gmpCurUserPosUplRes"></div>
													<div class="gmpCurUserPosFileUpRes"></div>
												</div>
												<div style="clear: both;"></div>
											</div>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_frontend_add_markers">
												<?php _e('Add markers on frontend', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e("You can add markers at the current map with the frontend using the form, which can be displayed using the shortcode (it placed below preview map). Additional options that affect the operation of the form:" .
												"<br /><br /><b>Logged In Users Only</b> - form will be displayed only for logged in users." .
												"<br /><br /><b>Disable WP Editor</b> - disable / enable WP Editor for the Marker Description field of the form." .
												"<br /><br /><b>Delete markers</b> - disable / enable interface for deleting markers on frontend. Each user can delete only his own markers." .
												"<br /><br /><b>Use markers categories</b> - disable / enable interface for choose the marker category on frontend." .
												"<br /><br /><b>Use limits for marker's adding</b> - allows you to limit the number of markers, which user can add from one IP address at the current map for a certain amount of time." .
												"<br /><br /><b>Max marker's count</b> - the maximum number of markers, which can be added over certain amount of time." .
												"<br /><br /><b>For allotted time (minutes)</b> - the number of minutes, during which you can add the maximum number of markers." .
												"<br /><br />For example, during three minutes you can add only two markers at the map. If you try to add a third marker - the form will not be saved and you will see the notice with amount of time you must wait. After the right amount of time will pass - you can add next two markers, etc." .
												"<br /><br />You can add markers at the current map with the frontend using the form, which can be displayed using the shortcode. Please place this shortcode at the same page as it's map map. Please note that the page will be overloaded after adding marker.", GMP_LANG_CODE)?>"></i>
											<?php if(!$this->isPro) { ?>
												<?php $proLink = frameGmp::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=frontend_add_markers&utm_campaign=googlemaps'); ?>
												<br /><span class="gmpProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', GMP_LANG_CODE)?></a></span>
											<?php }?>
										</th>
										<td>
											<?php echo htmlGmp::checkboxHiddenVal('map_opts[frontend_add_markers]', array(
												'value' => $this->editMap && isset($this->map['params']['frontend_add_markers']) ? $this->map['params']['frontend_add_markers'] : false,
												'attrs' => 'class="gmpProOpt" id="map_opts_frontend_add_markers"'
											))?>
											<div id="gmpAddMarkersOnFrontendOptions" style="display: none;">
												<div style="margin-top: 10px;">
													<?php echo htmlGmp::text('gmpCopyTextCode', array(
														'value' => '',	// Will be inserted from JS
														'attrs' => 'class="gmpCopyTextCode gmpMapMarkerFormCodeShell gmpStaticWidth" style="width: 100%; text-align: center;"'));?>
												</div>
												<div style="margin-top: 10px;">
													<?php echo htmlGmp::checkboxHiddenVal('map_opts[frontend_add_markers_logged_in_only]', array(
														'value' => $this->editMap && isset($this->map['params']['frontend_add_markers_logged_in_only']) ? $this->map['params']['frontend_add_markers_logged_in_only'] : false,
														'attrs' => 'class="gmpProOpt" id="map_opts_frontend_add_markers_logged_in_only"'
													))?>
													<label for="map_opts_frontend_add_markers_logged_in_only"><?php _e('Logged In Users Only', GMP_LANG_CODE)?></label>
												</div>
												<div style="margin-top: 10px;">
													<?php echo htmlGmp::checkboxHiddenVal('map_opts[frontend_add_markers_disable_wp_editor]', array(
														'value' => $this->editMap && isset($this->map['params']['frontend_add_markers_disable_wp_editor']) ? $this->map['params']['frontend_add_markers_disable_wp_editor'] : false,
														'attrs' => 'class="gmpProOpt" id="map_opts_frontend_add_markers_disable_wp_editor"'
													))?>
													<label for="map_opts_frontend_add_markers_disable_wp_editor"><?php _e('Disable WP Editor', GMP_LANG_CODE)?></label>
												</div>
												<div style="margin-top: 10px;">
													<?php echo htmlGmp::checkboxHiddenVal('map_opts[frontend_add_markers_delete_markers]', array(
														'value' => $this->editMap && isset($this->map['params']['frontend_add_markers_delete_markers']) ? $this->map['params']['frontend_add_markers_delete_markers'] : false,
														'attrs' => 'class="gmpProOpt" id="map_opts_frontend_add_markers_delete_markers"'
													))?>
													<label for="map_opts_frontend_add_markers_delete_markers"><?php _e('Delete markers', GMP_LANG_CODE)?></label>
												</div>
												<div style="margin-top: 10px;">
													<?php echo htmlGmp::checkboxHiddenVal('map_opts[frontend_add_markers_use_markers_categories]', array(
														'value' => $this->editMap && isset($this->map['params']['frontend_add_markers_use_markers_categories']) ? $this->map['params']['frontend_add_markers_use_markers_categories'] : false,
														'attrs' => 'class="gmpProOpt" id="map_opts_frontend_add_markers_use_markers_categories"'
													))?>
													<label for="map_opts_frontend_add_markers_use_markers_categories"><?php _e('Use markers categories', GMP_LANG_CODE)?></label>
												</div>
												<div style="margin-top: 10px;">
													<?php echo htmlGmp::checkboxHiddenVal('map_opts[frontend_add_markers_use_limits]', array(
														'value' => $this->editMap && isset($this->map['params']['frontend_add_markers_use_limits']) ? $this->map['params']['frontend_add_markers_use_limits'] : false,
														'attrs' => 'class="gmpProOpt" id="map_opts_frontend_add_markers_use_limits"'
													))?>
													<label for="map_opts_frontend_add_markers_use_limits"><?php _e('Use limits for marker\'s adding', GMP_LANG_CODE)?></label>
												</div>
												<div id="gmpUseLimitsForMarkerAddingOptions" style="display: none; margin-top: 10px;">
													<div class="sup-col sup-w-50">
														<label for="map_opts_frontend_add_markers_use_count_limits">
															<?php _e('Max marker\'s count', GMP_LANG_CODE)?>
														</label>
														<?php echo htmlGmp::text('map_opts[frontend_add_markers_use_count_limits]', array(
															'value' => $this->editMap && isset($this->map['params']['frontend_add_markers_use_count_limits']) ? $this->map['params']['frontend_add_markers_use_count_limits'] : '10',
															'attrs' => 'style="width: 100%;" id="map_opts_frontend_add_markers_use_count_limits"'))?>
													</div>
													<div class="sup-col sup-w-50">
														<label for="map_opts_frontend_add_markers_use_time_limits">
															<?php _e('For allotted time (minutes)', GMP_LANG_CODE)?>
														</label>
														<?php echo htmlGmp::text('map_opts[frontend_add_markers_use_time_limits]', array(
															'value' => $this->editMap && isset($this->map['params']['frontend_add_markers_use_time_limits']) ? $this->map['params']['frontend_add_markers_use_time_limits'] : '10',
															'attrs' => 'style="width: 100%;" id="map_opts_frontend_add_markers_use_time_limits"'))?>
													</div>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_places_en_toolbar">
												<?php _e('Use Places Toolbar', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e("Activate the toolbar for search Places (restaurants, schools, museums, etc.) on the map. Use the shortcode to display toolbar on wherever you need, but toolbar must be placed on the same page as its map.", GMP_LANG_CODE);
											echo '<br />';
											if(!$this->isPro){
												echo esc_html('<a href="'. $proLink. '" target="_blank"><img src="'. $this->promoModPath. 'img/places/places.png" /></a>');
											}?>"></i>
											<?php if(!$this->isPro) { ?>
												<?php $proLink = frameGmp::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=places_toolbar&utm_campaign=googlemaps'); ?>
												<br /><span class="gmpProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', GMP_LANG_CODE)?></a></span>
											<?php }?>
										</th>
										<td>
											<?php echo htmlGmp::checkboxHiddenVal('map_opts[places][en_toolbar]', array(
												'value' => $this->editMap && isset($this->map['params']['places']['en_toolbar']) ? $this->map['params']['places']['en_toolbar'] : false,
												'attrs' => 'class="gmpProOpt" id="map_opts_places_en_toolbar"'
											))?>
											<div id="gmpPlacesToolbarOptions" style="display: none;">
												<div style="margin-top: 10px;">
													<?php echo htmlGmp::text('gmpCopyTextCode', array(
														'value' => '',	// Will be inserted from JS
														'attrs' => 'class="gmpCopyTextCode gmpPlacesToolbarCodeShell gmpStaticWidth" style="width: 100%; text-align: center;"'));?>
												</div>
												<div style="margin-top: 10px;">
													<label for="map_opts_places_slider_min">
														<?php _e('Min Search Radius (in meters):', GMP_LANG_CODE)?>
													</label></br>
													<?php echo htmlGmp::text('map_opts[places][slider_min]', array(
														'value' => $this->editMap && isset($this->map['params']['places']['slider_min']) ? $this->map['params']['places']['slider_min'] : '100',
														'attrs' => 'class="gmpProOpt" style="width: 100%;" id="map_opts_places_slider_min"'))?>
												</div>
												<div style="margin-top: 10px;">
													<label for="map_opts_places_slider_max">
														<?php _e('Max Search Radius (in meters):', GMP_LANG_CODE)?>
													</label></br>
													<?php echo htmlGmp::text('map_opts[places][slider_max]', array(
														'value' => $this->editMap && isset($this->map['params']['places']['slider_max']) ? $this->map['params']['places']['slider_max'] : '1000',
														'attrs' => 'class="gmpProOpt" style="width: 100%;" id="map_opts_places_slider_max"'))?>
												</div>
												<div style="margin-top: 10px;">
													<label for="map_opts_places_slider_step">
														<?php _e('Search Step (in meters):', GMP_LANG_CODE)?>
													</label></br>
													<?php echo htmlGmp::text('map_opts[places][slider_step]', array(
														'value' => $this->editMap && isset($this->map['params']['places][slider_step']) ? $this->map['params']['places][slider_step'] : '10',
														'attrs' => 'class="gmpProOpt" style="width: 100%;" id="map_opts_places_slider_step"'))?>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_marker_title_color">
												<?php _e('Filter background', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Background color for markers filter. (for 7 markers list type)', GMP_LANG_CODE)?>"></i>
										</th>
										<td>
											<?php echo htmlGmp::colorpicker('map_opts[marker_filter_color]', array(
												'value' => $this->editMap && isset($this->map['params']['marker_filter_color']) ? $this->map['params']['marker_filter_color'] : '#f1f1f1;'))?>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_marker_title_color">
												<?php _e('Filters select all button title', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Filters select all button title. (for 7 markers list type)', GMP_LANG_CODE)?>"></i>
										</th>
										<td>
											<?php echo htmlGmp::text('map_opts[marker_filter_button_title]', array(
												'value' => $this->editMap && isset($this->map['params']['marker_filter_button_title']) ? $this->map['params']['marker_filter_button_title'] : 'Select all'))?>
										</td>
									</tr>
									<tr style="border-bottom: 1px solid #e3dbdb!important;">
										<th scope="row">
											<label class="label-big">
												<?php _e('Info Window', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Parameters of markers / shapes info-window PopUp', GMP_LANG_CODE)?>"></i>
										</th>
										<td></td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_marker_infownd_type">
												<?php _e('Appearance', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Choose the appearance type of infowindow.', GMP_LANG_CODE)?>"></i>
										</th>
										<td>
											<?php echo htmlGmp::selectbox('map_opts[marker_infownd_type]', array(
												'options' => array('' => __('Default', GMP_LANG_CODE), 'rounded_edges' => __('Rounded Edges', GMP_LANG_CODE),),
												'value' => $this->editMap && isset($this->map['params']['marker_infownd_type']) ? $this->map['params']['marker_infownd_type'] : 'default',
												'attrs' => 'style="width: 100%;" id="map_opts_marker_infownd_type"'))?>
											<div id="gmpMarkerInfoWndTypeSubOpts">
												<div class="gmpSubOpt" data-type="rounded_edges" style="display: none; margin-top: 10px;">
													<?php echo htmlGmp::checkboxHiddenVal('map_opts[marker_infownd_hide_close_btn]', array(
														'value' => $this->editMap && isset($this->map['params']['marker_infownd_hide_close_btn']) ? $this->map['params']['marker_infownd_hide_close_btn'] : true,
														'attrs' => 'class="gmpProOpt" id="map_opts_marker_infownd_hide_close_btn"'
													))?>
													<label for="map_opts_marker_infownd_hide_close_btn"><?php _e('Hide Close Button', GMP_LANG_CODE)?></label>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_marker_infownd_width">
												<?php _e('Width', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Width of info window', GMP_LANG_CODE)?>"></i>
										</th>
										<td>
										<?php
											$markersInfoWndWidthUnits = isset($this->map['params']['marker_infownd_width_units']) && $this->map['params']['marker_infownd_width_units'];
											$markersInfoWndWidthInput = isset($this->map['params']['marker_infownd_width']) && $this->map['params']['marker_infownd_width'];
											$markersInfoWndWidthInputViewStyle = $this->editMap && $markersInfoWndWidthUnits && htmlGmp::checkedOpt($this->map['params'], 'marker_infownd_width_units', 'px') ? 'block' : 'none';
											$markersInfoWndWidthUnitsLabelStyle = $this->editMap && $markersInfoWndWidthUnits && htmlGmp::checkedOpt($this->map['params'], 'marker_infownd_width_units', 'px') ? '7px' : '0px';
										?>
											<div class="sup-col" style="padding-right: 0px;">
												<label for="map_opts_marker_infownd_width_units" style="margin-right: 15px; position: relative; top: <?php echo $markersInfoWndWidthUnitsLabelStyle?>;">
													<?php echo htmlGmp::radiobutton('map_opts[marker_infownd_width_units]', array(
														'value' => 'auto',
														'checked' => $this->editMap && $markersInfoWndWidthUnits ? htmlGmp::checkedOpt($this->map['params'], 'marker_infownd_width_units', 'auto') : true,
													))?>&nbsp;<?php _e('Auto', GMP_LANG_CODE)?>
												</label>
												<label
													for="map_opts_marker_infownd_width_units"
													class="supsystic-tooltip"
													title="<?php _e('The value defines maximum width of the description. Window will be drawn according to content size but not wider than the value.', GMP_LANG_CODE)?>"
													style="margin-right: 15px; position: relative; top: <?php echo $markersInfoWndWidthUnitsLabelStyle?>;"
												>
													<?php echo htmlGmp::radiobutton('map_opts[marker_infownd_width_units]', array(
														'value' => 'px',
														'checked' => $this->editMap && $markersInfoWndWidthUnits ? htmlGmp::checkedOpt($this->map['params'], 'marker_infownd_width_units', 'px') : false,
													))?>&nbsp;<?php _e('Px', GMP_LANG_CODE)?>
												</label>
											</div>
											<div class="sup-col sup-w-25">
												<?php echo htmlGmp::text('map_opts[marker_infownd_width]', array(
													'value' => $this->editMap && $markersInfoWndWidthInput ? $this->map['params']['marker_infownd_width'] : '200',
													'attrs' => 'style="width: 100%; display: '. $markersInfoWndWidthInputViewStyle .';"'))?>
											</div>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_marker_infownd_height">
												<?php _e('Height', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Height of info window', GMP_LANG_CODE)?>"></i>
										</th>
										<td>
											<?php
											$markersInfoWndHeightUnits = isset($this->map['params']['marker_infownd_height_units']) && $this->map['params']['marker_infownd_height_units'];
											$markersInfoWndHeightInput = isset($this->map['params']['marker_infownd_height']) && $this->map['params']['marker_infownd_height'];
											$markersInfoWndHeightInputViewStyle = $this->editMap && $markersInfoWndHeightUnits && htmlGmp::checkedOpt($this->map['params'], 'marker_infownd_height_units', 'px') ? 'block' : 'none';
											$markersInfoWndHeightUnitsLabelStyle = $this->editMap && $markersInfoWndHeightUnits && htmlGmp::checkedOpt($this->map['params'], 'marker_infownd_height_units', 'px') ? '7px' : '0px';
											?>
											<div class="sup-col" style="padding-right: 0px;">
												<label for="map_opts_marker_infownd_height_units" style="margin-right: 15px; position: relative; top: <?php echo $markersInfoWndHeightUnitsLabelStyle?>;">
													<?php echo htmlGmp::radiobutton('map_opts[marker_infownd_height_units]', array(
														'value' => 'auto',
														'checked' => $this->editMap && $markersInfoWndHeightUnits ? htmlGmp::checkedOpt($this->map['params'], 'marker_infownd_height_units', 'auto') : true,
													))?>&nbsp;<?php _e('Auto', GMP_LANG_CODE)?>
												</label>
												<label
													for="map_opts_marker_infownd_height_units"
													class="supsystic-tooltip"
													title="<?php _e('Pixels', GMP_LANG_CODE)?>"
													style="margin-right: 15px; position: relative; top: <?php echo $markersInfoWndHeightUnitsLabelStyle?>;"
													>
													<?php echo htmlGmp::radiobutton('map_opts[marker_infownd_height_units]', array(
														'value' => 'px',
														'checked' => $this->editMap && $markersInfoWndHeightUnits ? htmlGmp::checkedOpt($this->map['params'], 'marker_infownd_height_units', 'px') : false,
													))?>&nbsp;<?php _e('Px', GMP_LANG_CODE)?>
												</label>
											</div>
											<div class="sup-col sup-w-25">
												<?php echo htmlGmp::text('map_opts[marker_infownd_height]', array(
													'value' => $this->editMap && $markersInfoWndHeightInput ? $this->map['params']['marker_infownd_height'] : '100',
													'attrs' => 'style="width: 100%; display: '. $markersInfoWndHeightInputViewStyle .';"'))?>
											</div>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_marker_title_color">
												<?php _e('Title Color', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('You can set your info window title color here', GMP_LANG_CODE)?>"></i>
										</th>
										<td>
											<?php echo htmlGmp::colorpicker('map_opts[marker_title_color]', array(
												'value' => $this->editMap && isset($this->map['params']['marker_title_color']) ? $this->map['params']['marker_title_color'] : '#A52A2A'))?>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_marker_infownd_bg_color">
												<?php _e('Background Color', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('You can set your info window background color here', GMP_LANG_CODE)?>"></i>
										</th>
										<td>
											<?php echo htmlGmp::colorpicker('map_opts[marker_infownd_bg_color]', array(
												'value' => $this->editMap && isset($this->map['params']['marker_infownd_bg_color']) ? $this->map['params']['marker_infownd_bg_color'] : '#FFFFFF'))?>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_marker_title_size">
												<?php _e('Title Font Size', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('You can set your info window title font size here', GMP_LANG_CODE)?>"></i>
										</th>
										<td>
											<div class="sup-col sup-w-25">
												<?php echo htmlGmp::text('map_opts[marker_title_size]', array(
													'value' => $this->editMap && isset($this->map['params']['marker_title_size']) ? $this->map['params']['marker_title_size'] : '19',
													'attrs' => 'style="width: 100%;" id="map_opts_marker_title_size"'))?>
											</div>
											<div class="sup-col sup-w-75">
												<label class="supsystic-tooltip" title="<?php _e('Pixels', GMP_LANG_CODE)?>" style="margin-right: 15px; position: relative; top: 7px;">
													<?php echo htmlGmp::radiobutton('map_opts[marker_title_size_units]', array(
														'value' => 'px',
														'checked' => true,
													))?>&nbsp;<?php _e('Px', GMP_LANG_CODE)?></label>
											</div>
										</td>
									</tr>
									<tr style="border-bottom: 1px solid #e3dbdb!important;">
										<th scope="row">
											<label for="map_opts_marker_desc_size">
												<?php _e('Description Font Size', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('You can set your info window description font size here', GMP_LANG_CODE)?>"></i>
										</th>
										<td>
											<div class="sup-col sup-w-25">
												<?php echo htmlGmp::text('map_opts[marker_desc_size]', array(
													'value' => $this->editMap && isset($this->map['params']['marker_desc_size']) ? $this->map['params']['marker_desc_size'] : '13',
													'attrs' => 'style="width: 100%;" id="map_opts_marker_desc_size"'))?>
											</div>
											<div class="sup-col sup-w-75">
												<label class="supsystic-tooltip" title="<?php _e('Pixels', GMP_LANG_CODE)?>" style="margin-right: 15px; position: relative; top: 7px;">
													<?php echo htmlGmp::radiobutton('map_opts[marker_desc_size_units]', array(
														'value' => 'px',
														'checked' => true,
													))?>&nbsp;<?php _e('Px', GMP_LANG_CODE)?></label>
											</div>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<?php if(!$this->isPro) { ?>
												<?php $proLink = frameGmp::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=enable_directions_btn&utm_campaign=googlemaps'); ?>
											<?php }?>
											<label for="map_opts_enable_directions_btn">
												<?php _e('Directions Button', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip"
												title="<?php _e('Add a button at marker info window to get direction from the entered address to the marker. If Show route data option is enabled - the total route time and distance will be shown by click on the route polyline.', GMP_LANG_CODE);
												if(!$this->isPro){
													echo esc_html('<a href="'. $proLink. '" target="_blank"><img src="'. $this->promoModPath. 'img/directions/get_directions.png" /></a>');
												}?>"
												></i>
											<?php if(!$this->isPro) { ?>
												<br /><span class="gmpProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', GMP_LANG_CODE)?></a></span>
											<?php }?>
										</th>
										<td>
											<?php echo htmlGmp::checkboxHiddenVal('map_opts[enable_directions_btn]', array(
												'value' => $this->editMap && isset($this->map['params']['enable_directions_btn']) ? $this->map['params']['enable_directions_btn'] : false,
												'attrs' => 'class="gmpProOpt"'))?>
											<div id="gmpDirectionsOptions" style="margin-top: 10px; display: none;">
												<div style="margin-top: 10px;">
													<?php echo htmlGmp::checkboxHiddenVal('map_opts[directions_alternate_routes]', array(
														'value' => $this->editMap && isset($this->map['params']['directions_alternate_routes']) ? $this->map['params']['directions_alternate_routes'] : false,
														'attrs' => 'class="gmpProOpt"'))?>
													<span>
													<?php _e('Show alternate routes', GMP_LANG_CODE)?>
												</span>
												</div>
												<div style="margin-top: 10px;">
													<?php echo htmlGmp::checkboxHiddenVal('map_opts[directions_data_show]', array(
														'value' => $this->editMap && isset($this->map['params']['directions_data_show']) ? $this->map['params']['directions_data_show'] : false,
														'attrs' => 'class="gmpProOpt"'))?>
													<span>
														<?php _e('Show route data', GMP_LANG_CODE)?>
													</span>
												</div>
												<div style="margin-top: 10px;">
													<?php echo htmlGmp::checkboxHiddenVal('map_opts[directions_steps_show]', array(
														'value' => $this->editMap && isset($this->map['params']['directions_steps_show']) ? $this->map['params']['directions_steps_show'] : false,
														'attrs' => 'class="gmpProOpt"'))?>
													<span>
														<?php _e('Show route steps', GMP_LANG_CODE)?>
													</span>
												</div>
												<div style="margin-top: 10px;">
													<?php echo htmlGmp::checkboxHiddenVal('map_opts[directions_miles]', array(
														'value' => $this->editMap && isset($this->map['params']['directions_miles']) ? $this->map['params']['directions_miles'] : false,
														'attrs' => 'class="gmpProOpt"'))?>
													<span>
														<?php _e('Use miles', GMP_LANG_CODE)?>
													</span>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="map_opts_enable_infownd_print_btn">
												<?php _e('Print Button', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Add Print button to markers info window', GMP_LANG_CODE)?>"></i>
											<?php if(!$this->isPro) { ?>
												<?php $proLink = frameGmp::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=enable_infownd_print_btn&utm_campaign=googlemaps'); ?>
												<br /><span class="gmpProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', GMP_LANG_CODE)?></a></span>
											<?php }?>
										</th>
										<td>
											<?php echo htmlGmp::checkboxHiddenVal('map_opts[enable_infownd_print_btn]', array(
												'value' => $this->editMap && isset($this->map['params']['enable_infownd_print_btn']) ? $this->map['params']['enable_infownd_print_btn'] : false,
												'attrs' => 'class="gmpProOpt"'
											))?>
										</td>
									</tr>
								</table>
							</div>
							<?php echo htmlGmp::hidden('mod', array('value' => 'gmap'))?>
							<?php echo htmlGmp::hidden('action', array('value' => 'save'))?>
							<?php echo htmlGmp::hidden('map_opts[id]', array('value' => $this->editMap ? $this->map['id'] : ''))?>
							<?php echo htmlGmp::hidden('map_opts[membershipEnable]', array('value' => isset($this->map['params']['membershipEnable']) ? $this->map['params']['membershipEnable'] : 0, 'attrs' => 'id="membershipHiddenEnable"'))?>
						</form>
					</div>
					<div id="gmpMarkerTab" class="gmpTabContent">
						<form id="gmpMarkerForm">
							<table class="form-table">
								<tr>
									<th scope="row">
										<label class="label-big" for="marker_opts_title">
											<?php _e('Marker Name', GMP_LANG_CODE)?>:
										</label>
										<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Your marker title', GMP_LANG_CODE)?>"></i>
									</th>
									<td>
										<?php echo htmlGmp::text('marker_opts[title]', array(
											'value' => '',
											'attrs' => 'style="width: 100%;" id="marker_opts_title"'))?>
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label>
											<?php _e('Marker Description', GMP_LANG_CODE)?>:
										</label>
										<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Write here all text, that you want to appear in marker info-window PopUp', GMP_LANG_CODE)?>"></i>
									</th>
									<td></td>
								</tr>
								<tr>
									<th colspan="2">
										<?php wp_editor('', 'markerDescription', array(
											//'textarea_name' => 'marker_opts[description]',
											'textarea_rows' => 10
										));?>
										<?php echo htmlGmp::hidden('marker_opts[description]', array('value' => ''))?>
									</th>
								</tr>
								<tr>
									<th scope="row">
										<label class="label-big" for="gmpMarkerIconBtn">
											<?php _e('Icon', GMP_LANG_CODE)?>:
										</label>
										<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Your marker Icon, that will appear on your map for this marker', GMP_LANG_CODE)?>"></i>
									</th>
									<td>
										<?php echo htmlGmp::hidden('marker_opts[icon]', array(
											'value' => 1 /*Default Icon ID*/ ))?>
										<img id="gmpMarkerIconPrevImg" src="" style="float: left;" />
										<div style="float: right">
											<a id="gmpMarkerIconBtn" href="#" class="button"><?php _e('Choose Icon', GMP_LANG_CODE)?></a>
											<a id="gmpUploadIconBtn" href="#" class="button"><?php _e('Upload Icon', GMP_LANG_CODE)?></a>
											<div class="gmpUplRes"></div>
											<div class="gmpFileUpRes"></div>
										</div>
										<div style="clear: both;"></div>
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label for="marker_opts_address">
											<?php _e('Address', GMP_LANG_CODE)?>:
										</label>
										<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Search your location by address, just start typing here', GMP_LANG_CODE)?>"></i>
									</th>
									<td>
										<?php echo htmlGmp::text('marker_opts[address]', array(
											'value' => '',
											'placeholder' => '603 Park Avenue, Brooklyn, NY 11206, USA',
											'attrs' => 'style="width: 100%;" id="marker_opts_address"'))?>
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label for="marker_opts_coord_x">
											<?php _e('Latitude', GMP_LANG_CODE)?>:
										</label>
										<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Latitude for your marker', GMP_LANG_CODE)?>"></i>
									</th>
									<td>
										<?php echo htmlGmp::text('marker_opts[coord_x]', array(
											'value' => '',
											'placeholder' => '40.69827799999999',
											'attrs' => 'style="width: 100%;" id="marker_opts_coord_x"'))?>
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label for="marker_opts_coord_y">
											<?php _e('Longitude', GMP_LANG_CODE)?>:
										</label>
										<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Longitude for your marker', GMP_LANG_CODE)?>"></i>
									</th>
									<td>
										<?php echo htmlGmp::text('marker_opts[coord_y]', array(
											'value' => '',
											'placeholder' => '-73.95141139999998',
											'attrs' => 'style="width: 100%;" id="marker_opts_coord_y"'))?>
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label for="marker_opts_marker_group_id">
											<?php _e('Marker Category', GMP_LANG_CODE)?>:
										</label>
										<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Choose marker category', GMP_LANG_CODE)?>"></i>
									</th>
									<td>
										<div style="width: 100%;">
											<?php echo htmlGmp::selectlist('marker_opts[marker_group_id]', array(
												'options' => $this->markerGroupsForSelect,
												'value' => '',
												'attrs' => 'style="width: 100%;" id="marker_opts_marker_group_id" class="chosen-select"'))?>
										</div>
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label for="marker_opts_marker_link">
											<?php _e('Marker Link', GMP_LANG_CODE)?>:
										</label>
										<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Link for opening by click on the marker', GMP_LANG_CODE)?>"></i>
									</th>
									<td>
										<?php echo htmlGmp::checkbox('marker_opts[params][marker_link]', array(
											'checked' => '',
											'attrs' => 'id="marker_link" onclick="gmpAddLinkOptions()"',
										))?>
										<div id="link_options" style="display: none;">
											<?php echo htmlGmp::text('marker_opts[params][marker_link_src]', array(
												'value' => '',
												'attrs' => 'style="width: 90%; float: right; margin: 0px 0px 10px 0px;"',
											))?>
											<div style="clear: both;"></div>
											<?php echo htmlGmp::checkbox('marker_opts[params][marker_link_new_wnd]', array(
												'checked' => ''))?>
											<span>
												<?php _e('Open in new window', GMP_LANG_CODE)?>
											</span>
										</div>
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label for="marker_opts_show_description">
											<?php _e('Show description by default', GMP_LANG_CODE)?>:
										</label>
										<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Open marker description when map load', GMP_LANG_CODE)?>"></i>
									</th>
									<td>
										<?php echo htmlGmp::checkbox('marker_opts[params][show_description]', array(
											'checked' => ''))?>
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label for="marker_opts_description_mouse_hover">
											<?php _e('Show description by mouse hover', GMP_LANG_CODE)?>:
										</label>
										<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Open marker description by mouse hover', GMP_LANG_CODE)?>"></i>
									</th>
									<td>
										<?php echo htmlGmp::checkbox('marker_opts[params][description_mouse_hover]', array(
											'checked' => ''))?>
									</td>
								</tr>
								<tr id="marker_opts_description_mouse_leave">
									<th scope="row">
										<label for="marker_opts_description_mouse_leave">
											<?php _e('Hide description on mouse leave', GMP_LANG_CODE)?>:
										</label>
										<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Hide description when mouse leaves the marker area', GMP_LANG_CODE)?>"></i>
									</th>
									<td>
										<?php echo htmlGmp::checkbox('marker_opts[params][description_mouse_leave]', array(
											'checked' => ''))?>
									</td>
								</tr>
								<tr style="display: none;">
									<th scope="row">
										<label for="marker_opts_marker_list_def_img">
											<?php _e('Marker List Default Image', GMP_LANG_CODE)?>:
										</label>
										<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('If there is no image tag in the marker description - this image will be used for displaying in the map\'s markers list', GMP_LANG_CODE)?>"></i>
									</th>
									<td>
										<?php echo htmlGmp::checkbox('marker_opts[params][marker_list_def_img]', array(
											'checked' => ''))?>
										<div id="gmpMarkerListDefImgOptions" style="display: none;">
											<a 	href="#"
												id="gmpMarkerListDefImgUploadFileBtn"
												class="button gmpProOpt"
												title="<?php _e('Upload', GMP_LANG_CODE)?>"
												data-nonce="<?php echo wp_create_nonce('upload-marker-list-def-img-file')?>"
												data-url="<?php echo uriGmp::_(array(
													'baseUrl' => admin_url('admin-ajax.php'),
													'page' => 'add_map_options',
													'action' => 'addFromFile',
													'reqType' => 'ajax',
													'pl' => GMP_CODE))?>"
												  style="float: right;">
												<i class="fa fa-upload"></i>
											</a>
											<?php echo htmlGmp::text('marker_opts[params][marker_list_def_img_url]', array(
												'value' => '',
												'attrs' => 'id="gmpMarkerListDefImgUrl" style="width: 78%; margin-right: 5px; float: right;"',
											))?>
											<span class="gmpMarkerListDefImgUploadMsg" style="	float: right; width: 100%; text-align: right;" ></span>
										</div>
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label for="marker_opts_clasterer_exclude">
											<?php _e('Exclude from Cluster', GMP_LANG_CODE)?>:
										</label>
										<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Exclude marker from cluster if Markers Clusterization option is enabled.', GMP_LANG_CODE)?>"></i>
									</th>
									<td>
										<?php echo htmlGmp::checkbox('marker_opts[params][clasterer_exclude]', array(
											'checked' => ''))?>
									</td>
								</tr>
								<?php if($isCustSearchAndMarkersPeriodAvailable) { ?>
								<tr>
									<th>
										<label>
											<?php _e('Period From', GMP_LANG_CODE);?>
										</label>
<!--										<i style="float: right;" class="fa fa-question supsystic-tooltip" title="--><?php //_e('', GMP_LANG_CODE); ?><!--"></i>-->
										<?php if(!$this->isPro) { ?>
											<?php $proLink = frameGmp::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=marker_period_from&utm_campaign=googlemaps'); ?>
											<br /><span class="gmpProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', GMP_LANG_CODE)?></a></span>
										<?php }?>
									</th>
									<td>
										<?php
										if($this->isPro) {
											echo htmlGmp::text('marker_opts[period_date_from]', array(
												'value' => '',
												'attrs' => 'id="markerPeriodDateFrom"',
											));
										}
										?>
									</td>
								</tr>
								<tr>
									<th>
										<label>
											<?php _e('Period To', GMP_LANG_CODE);?>
										</label>
<!--										<i style="float: right;" class="fa fa-question supsystic-tooltip" title="--><?php //_e('', GMP_LANG_CODE); ?><!--"></i>-->
										<?php if(!$this->isPro) { ?>
											<?php $proLink = frameGmp::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=marker_period_to&utm_campaign=googlemaps'); ?>
											<br /><span class="gmpProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', GMP_LANG_CODE)?></a></span>
										<?php }?>
									</th>
									<td>
										<?php
										if($this->isPro) {
											echo htmlGmp::text('marker_opts[period_date_to]', array(
												'value' => '',
												'attrs' => 'id="markerPeriodDateTo"',
											));
										}?>
									</td>
								</tr>
								<?php }?>
							</table>
							<?php echo htmlGmp::hidden('mod', array('value' => 'marker'))?>
							<?php echo htmlGmp::hidden('action', array('value' => 'save'))?>
							<?php echo htmlGmp::hidden('marker_opts[id]', array('value' => ''))?>
							<?php echo htmlGmp::hidden('marker_opts[map_id]', array('value' => $this->editMap ? $this->map['id'] : ''))?>
							<?php echo htmlGmp::hidden('marker_opts[path]', array('value' => ''))?>
						</form>
					</div>
					<div id="gmpShapeTab" class="gmpTabContent">
						<?php if($this->isPro) {?>
							<form id="gmpShapeForm">
								<table class="form-table">
									<tr>
										<th scope="row">
											<label class="label-big" for="shape_opts_title">
												<?php _e('Figure Name', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Your figure title', GMP_LANG_CODE)?>"></i>
										</th>
										<td>
											<?php echo htmlGmp::text('shape_opts[title]', array(
												'value' => '',
												'attrs' => 'style="width: 100%;"'))?>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label class="label-big" for="shape_opts_type">
												<?php _e('Figure Type', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Type of your figure:' .
												'<br /><br /><b>Polyline</b> - a series of straight segments on the map.' .
												'<br /><br /><b>Polygon</b> - area enclosed by a closed path (or loop), which is defined by a series of coordinates.' .
												'<br /><br /><b>Circle</b> - circle shape,defined by center coordinates and radius.', GMP_LANG_CODE)?>"></i>
										</th>
										<td>
											<?php echo htmlGmp::selectbox('shape_opts[type]', array(
												'options' => array(
													'polyline' => __('Polyline', GMP_LANG_CODE),
													'polygon' => __('Polygon', GMP_LANG_CODE),
													'circle' => __('Circle', GMP_LANG_CODE),),
												'value' => 'polyline',
												'attrs' => 'style="width: 100%;"'))?>
										</td>
									</tr>
									<tr>
										<td colspan="2" style="padding: 0 10px 0 0;">
											<div class="gmpCommonShapeParam gmpParamLeft">
												<label class="label" for="shape_opts_line_color">
													<?php _e('Line Color', GMP_LANG_CODE)?>
												</label></br>
												<?php echo htmlGmp::colorpicker('shape_opts[params][strokeColor]', array(
													'value' => ''))?>
											</div>
											<div class="gmpCommonShapeParam">
												<label class="label" for="shape_opts_line_opacity">
													<?php _e('Line Opacity', GMP_LANG_CODE)?>
												</label></br>
												<?php echo htmlGmp::selectbox('shape_opts[params][strokeOpacity]', array(
													'options' => array(
														'0' => 0, '0.1' => 0.1, '0.2' => 0.2, '0.3' => 0.3
													,	'0.4' => 0.4, '0.5' => 0.5, '0.6' => 0.6
													,	'0.7' => 0.7, '0.8' => 0.8, '0.9' => 0.9, '1' => 1),
													'value' => ''))?>
											</div>
											<div class="gmpCommonShapeParam  gmpParamRight">
												<label class="label" for="shape_opts_line_weight">
													<?php _e('Line Weight', GMP_LANG_CODE)?>
												</label></br>
												<?php echo htmlGmp::text('shape_opts[params][strokeWeight]', array(
													'value' => '',
													'attrs' => 'style="width: 100%;"'))?>
											</div>
											<div class="gmpPolygonShapeParam gmpParamLeft">
												<label class="label" for="shape_opts_fill_color">
													<?php _e('Fill Color', GMP_LANG_CODE)?>
												</label></br>
												<?php echo htmlGmp::colorpicker('shape_opts[params][fillColor]', array(
													'value' => ''))?>
											</div>
											<div class="gmpPolygonShapeParam">
												<label class="label" for="shape_opts_fill_opacity">
													<?php _e('Fill Opacity', GMP_LANG_CODE)?>
												</label></br>
												<?php echo htmlGmp::selectbox('shape_opts[params][fillOpacity]', array(
													'options' => array(
														'0' => 0, '0.1' => 0.1, '0.2' => 0.2, '0.3' => 0.3
													,	'0.4' => 0.4, '0.5' => 0.5, '0.6' => 0.6
													,	'0.7' => 0.7, '0.8' => 0.8, '0.9' => 0.9, '1' => 1),
													'value' => ''))?>
											</div>
											<div style="clear: both;"></div>
											<div class="gmpPolygonShapeParam gmpPolygonShapeDesc">
												<div>
													<label>
														<?php _e('Figure Description', GMP_LANG_CODE)?>:
													</label>
													<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Write here all text, that you want to appear in shape info-window PopUp', GMP_LANG_CODE)?>"></i>
												</div>
												<?php wp_editor('', 'shapeDescription', array(
													'textarea_rows' => 10
												));?>
												<?php echo htmlGmp::hidden('shape_opts[description]', array('value' => ''))?>
											</div>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="shape_opts_coords">
												<?php _e('Points', GMP_LANG_CODE)?>:
											</label>
											<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Figure\'s points list: you can search the point by address (just start typing in Address field), type the Latitude and Longitude of point in appropriate fields or activate Add by Click button, and then draw figure on the map by clicking on it. Important! You must deactivate Add by Click button after ending of the draw.', GMP_LANG_CODE)?>"></i>
										</th>
										<td>
											<a href="#" class="button" id="gmpShapeAddPointByClickBtn" style="float: left;">
												<?php _e('Add by Click', GMP_LANG_CODE)?>
											</a>
											<a href="#" class="button" id="gmpShapeAddPointRowBtn" style="float: right;">
												<?php _e('Add New Point', GMP_LANG_CODE)?>
											</a>
										</td>
									</tr>
									<tr>
										<td colspan="2" style="padding-top: 10px; padding-left: 0;">
											<div class="gmpShapePointRowExample" style="display: none;">
												<div style="clear: both;">
													<div style="display: inline-block; width: 50%;">
														<label for="shape_opts_address">
															<?php _e('Address', GMP_LANG_CODE)?>
															<?php echo htmlGmp::text('shape_opts[coords][0][address]', array(
																'value' => '',
																'placeholder' => '603 Park Avenue, Brooklyn, NY 11206, USA',
																'attrs' => 'class="gmpShapeAddress" data-type="address" style="width: 100%;" disabled="disabled"'))?>
														</label>
													</div>
													<div style="display: inline-block; width: 20%;">
														<label for="shape_opts_coord_x">
															<?php _e('Latitude', GMP_LANG_CODE)?>
															<?php echo htmlGmp::text('shape_opts[coords][0][coord_x]', array(
																'value' => '',
																'placeholder' => '40.69827799999999',
																'attrs' => 'class="gmpShapeCoordX" data-type="coord_x" style="width: 100%;" disabled="disabled"'))?>
														</label>
													</div>
													<div style="display: inline-block; width: 20%;">
														<label for="shape_opts_coord_y">
															<?php _e('Longitude', GMP_LANG_CODE)?>
															<?php echo htmlGmp::text('shape_opts[coords][0][coord_y]', array(
																'value' => '',
																'placeholder' => '-73.95141139999998',
																'attrs' => 'class="gmpShapeCoordY" data-type="coord_y" style="width: 100%;" disabled="disabled"'))?>
														</label>
													</div>
													<div style="display: none; width: 10%;">
														<label for="shape_opts_radius">
															<?php _e('Radius', GMP_LANG_CODE)?>
															<?php echo htmlGmp::text('shape_opts[coords][0][radius]', array(
																'value' => '',
																'placeholder' => '10000',
																'attrs' => 'class="gmpShapeRadius" data-type="radius" data-def="100000" style="width: 100%;" disabled="disabled"'))?>
														</label>
													</div>
													<a href="#" title="<?php _e('Remove Point', GMP_LANG_CODE)?>" class="button" id="gmpShapeRemovePointRowBtn">
														<i class="fa fa-trash-o"></i>
													</a>
												</div>
											</div>
											<div id="gmpShapePointRowsShell"></div>
										</td>
									</tr>
								</table>
								<?php echo htmlGmp::hidden('mod', array('value' => 'shape'))?>
								<?php echo htmlGmp::hidden('action', array('value' => 'save'))?>
								<?php echo htmlGmp::hidden('shape_opts[id]', array('value' => ''))?>
								<?php echo htmlGmp::hidden('shape_opts[map_id]', array('value' => $this->editMap ? $this->map['id'] : ''))?>
							</form>
						<?php } else {
							echo $promoData['gmpShapeTab']['content'];
						}?>
					</div>
					<div id="gmpHeatmapTab" class="gmpTabContent">
					<?php if($this->isPro) {?>
						<form id="gmpHeatmapForm">
							<table class="form-table">
								<tr>
									<th scope="row">
										<label class="label-big">
											<?php _e('Points', GMP_LANG_CODE)?>:
										</label>
										<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('To add Heatmap Layer points you need to activate Add Points button and draw each point by click on map. To remove points you need to activate Remove Points button and delete necessary point by click on it or just click on Delete Heatmap Layer button to remove all Heatmap Layer points. Important! You must to deactivate Add by Click and Remove by Click buttons after ending of the add / remove points.', GMP_LANG_CODE)?>"></i>
									</th>
									<td>
										<div class="gmpHeatmapPointsBtns">
											<a href="#" class="button" id="gmpHeatmapAddPointBtn">
												<?php _e('Add Point', GMP_LANG_CODE)?>
											</a>
											<a href="#" class="button" id="gmpHeatmapRemovePointBtn">
												<?php _e('Remove Point', GMP_LANG_CODE)?>
											</a>
										</div>
										<div class="gmpHeatmapPointsCount">
											<label>
												<?php _e('Points Count', GMP_LANG_CODE)?>:
											</label>
											<div id="gmpHeatmapPointsNumber"></div>
										</div>
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label>
											<?php _e('Radius', GMP_LANG_CODE)?>:
										</label>
										<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Heatmap Layer points radius in pixels', GMP_LANG_CODE)?>"></i>
									</th>
									<td>
										<?php echo htmlGmp::text('heatmap_opts[params][radius]', array(
											'value' => '',
											'attrs' => 'style="width: 100%;"'))?>
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label>
											<?php _e('Opacity', GMP_LANG_CODE)?>:
										</label>
										<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Heatmap Layer points opacity', GMP_LANG_CODE)?>"></i>
									</th>
									<td>
										<?php echo htmlGmp::selectbox('heatmap_opts[params][opacity]', array(
											'options' => array(
												'0' => 0, '0.1' => 0.1, '0.2' => 0.2, '0.3' => 0.3
											,	'0.4' => 0.4, '0.5' => 0.5, '0.6' => 0.6
											,	'0.7' => 0.7, '0.8' => 0.8, '0.9' => 0.9, '1' => 1),
											'value' => '',
											'attrs' => 'style="width: 100%;"'))?>
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label>
											<?php _e('Gradient', GMP_LANG_CODE)?>:
										</label>
										<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Heatmap Layer points color gradient.', GMP_LANG_CODE)?>"></i>
									</th>
									<td>
										<a href="#" class="button" id="gmpHeatmapAddColorBtn">
											<?php _e('Add Color', GMP_LANG_CODE)?>
										</a>
										<a href="#" class="button" id="gmpHeatmapClearColorsBtn" style="float: right;">
											<?php _e('Clear', GMP_LANG_CODE)?>
										</a>
										<div class="gmpHeatmapGradientExample gmpHeatmapGradient" style="display: none; margin-top: 10px;">
											<input type="text" name="heatmap_opts[params][gradient][]" value="#5ED836" disabled="disabled" />
											<a href="#" class="button gmpHeatmapRemoveColorBtn" title="<?php _e('Remove Color', GMP_LANG_CODE)?>" onclick="gmpHeatmapRemoveColorBtnClick(this); return false;">
												<i class="fa fa-trash-o"></i>
											</a>
										</div>
										<div id="gmpHeatmapGradientFirstColorContainer">

										</div>
										<?php echo htmlGmp::hidden('heatmap_opts[params][gradient][]', array('value' => '', 'attrs' => 'class="firstHeatmapColor"'))?>
										<div id="gmpHeatmapGradientContainer"></div>
									</td>
								</tr>
							</table>
							<?php echo htmlGmp::hidden('mod', array('value' => 'heatmap'))?>
							<?php echo htmlGmp::hidden('action', array('value' => 'save'))?>
							<?php echo htmlGmp::hidden('heatmap_opts[id]', array('value' => ''))?>
							<?php echo htmlGmp::hidden('heatmap_opts[map_id]', array('value' => $this->editMap ? $this->map['id'] : ''))?>
						</form>
					<?php } else {
						echo $promoData['gmpHeatmapTab']['content'];
					}?>
				</div>
				</div>
				<div class="supsistic-half-side-box" style="position: relative;">
				<div id="gmpMapRightStickyBar" class="supsystic-sticky">
					<div id="gmpMapPreview" style="width: 100%; height: 350px;"></div>
					<div class="gmpMapProControlsCon" id="gmpMapProControlsCon_<?php echo $this->viewId;?>">
						<?php dispatcherGmp::doAction('addAdminMapBottomControls', $this->editMap ? $this->map : array()); ?>
					</div>
					<?php echo htmlGmp::hidden('rand_view_id', array('value' => $this->viewId, 'attrs' => 'id="gmpViewId"'))?>
					<div id="gmpMapMainBtns" class="gmpControlBtns row" style="display: none;">
						<div class="sup-col sup-w-50">
							<button id="gmpMapSaveBtn" class="button button-primary" style="width: 100%;">
								<i class="fa dashicons-before dashicons-admin-site"></i>
								<?php _e('Save Map', GMP_LANG_CODE)?>
							</button>
						</div>
						<div class="sup-col sup-w-50" style="padding-right: 0;">
							<button id="gmpMapDeleteBtn" class="button button-primary" style="width: 100%;">
								<i class="fa dashicons-before dashicons-trash"></i>
								<?php _e('Delete Map', GMP_LANG_CODE)?>
							</button>
						</div>
						<div style="clear: both;"></div>
					</div>
					<div id="gmpMarkerMainBtns" class="gmpControlBtns row" style="display: none;">
						<div class="sup-col sup-w-50">
							<button id="gmpSaveMarkerBtn" class="button button-primary" style="width: 100%;">
								<i class="fa fa-map-marker"></i>
								<?php _e('Save Marker', GMP_LANG_CODE)?>
							</button>
						</div>
						<div class="sup-col sup-w-50" style="padding-right: 0;">
							<button id="gmpMarkerDeleteBtn" class="button button-primary" style="width: 100%;">
								<i class="fa dashicons-before dashicons-trash"></i>
								<?php _e('Delete Marker', GMP_LANG_CODE)?>
							</button>
						</div>
						<div style="clear: both;"></div>
					</div>
					<div id="gmpShapeMainBtns" class="gmpControlBtns row" style="display: none;">
						<div class="sup-col sup-w-50">
							<button id="gmpSaveShapeBtn" class="button button-primary" style="width: 100%;">
								<i class="fa fa-cubes"></i>
								<?php _e('Save Figure', GMP_LANG_CODE)?>
							</button>
						</div>
						<div class="sup-col sup-w-50" style="padding-right: 0;">
							<button id="gmpShapeDeleteBtn" class="button button-primary" style="width: 100%;">
								<i class="fa dashicons-before dashicons-trash"></i>
								<?php _e('Delete Figure', GMP_LANG_CODE)?>
							</button>
						</div>
						<div style="clear: both;"></div>
					</div>
					<div id="gmpHeatmapMainBtns" class="gmpControlBtns row" style="display: none;">
						<div class="sup-col sup-w-50">
							<button id="gmpSaveHeatmapBtn" class="button button-primary" style="width: 100%;">
								<i class="fa fa-map"></i>
								<?php _e('Save Heatmap Layer', GMP_LANG_CODE)?>
							</button>
						</div>
						<div class="sup-col sup-w-50" style="padding-right: 0;">
							<button id="gmpHeatmapDeleteBtn" class="button button-primary" style="width: 100%;">
								<i class="fa dashicons-before dashicons-trash"></i>
								<?php _e('Delete Heatmap Layer', GMP_LANG_CODE)?>
							</button>
						</div>
						<div style="clear: both;"></div>
					</div>
					<div id="gmpMarkerList">
						<input id="gmpMarkersSearchInput" type="text" placeholder="<?php _e('Search by name', GMP_LANG_CODE)?>" style="display: none; width: 100%; margin: 0;" >
						<table id="gmpMarkersListGrid" class="supsystic-tbl-pagination-shell"></table>
					</div>
					<div id="gmpShapeList">
						<table id="gmpShapesListGrid" class="supsystic-tbl-pagination-shell"></table>
					</div>
					<?php /*?>
					<div class="row">
						<div id="gmpMarkerList">
							<div style="display: none;" id="markerRowTemplate" class="row gmpMapMarkerRow">
								<div class="col-xs-12 egm-marker">
									<div class="row">
										<div class="col-xs-2 egm-marker-icon">
											<img alt="" src="">
										</div>
										<div class="col-xs-4 egm-marker-title">
										</div>
										<div class="col-xs-3 egm-marker-latlng">
										</div>
										<div class="col-xs-3 egm-marker-actions">
											<button title="<?php _e('Edit', GMP_LANG_CODE)?>" type="button" class="button button-small egm-marker-edit">
												<i class="fa fa-fw fa-pencil"></i>
											</button>
											<button title="<?php _e('Delete', GMP_LANG_CODE)?>" type="button" class="button button-small egm-marker-remove">
												<i class="fa fa-fw fa-trash-o"></i>
											</button>
										</div>
									</div>
								</div>
								<div style="clear: both;"></div>
							</div>
						</div>
					</div>
				<?php */?>
				</div>
			</div>
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
</section>
<!--Icons Wnd-->
<div id="gmpIconsWnd" style="display: none;">
	<ul class="iconsList">
		<?php foreach($this->icons as $icon) { ?>
			<?php if( intval($icon['id']) > 47):?>
				<li class="previewIcon" data-id="<?php echo $icon['id']?>" title="<?php echo $icon['title']?>">
					<img src="<?php echo $icon['path']?>" ><i class="fa fa-times" aria-hidden="true"></i>
				</li>
			<?php else: ?>
				<li class="previewIcon" data-id="<?php echo $icon['id']?>" title="<?php echo $icon['title']?>">
					<img src="<?php echo $icon['path']?>" >
				</li>
			<?php endif; ?>
		<?php }?>
	</ul>
</div>
<!--Map Markers List Wnd-->
<div id="gmpMarkersListWnd" style="display: none;" title="<?php _e('Show markers list with your map on frontend', GMP_LANG_CODE)?>">
	<!--Mml == Map Markers List-->
	<ul id="gmpMml">
		<?php foreach($this->markerLists as $lKey => $lData) { ?>
		<li class="gmpMmlElement gmpMmlElement-<?php echo $lKey?>" data-key="<?php echo $lKey?>">
			<img src="<?php echo $this->promoModPath?>img/markers_list/<?php echo $lData['prev_img']?>" /><br />
			<div class="gmpMmlElementBtnShell">
				<a href="<?php echo frameGmp::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=marker_list_' . $lKey . '&utm_campaign=googlemaps');?>" target="_blank" class="button button-primary gmpMmlApplyBtn" data-apply-label="<?php _e('Apply', GMP_LANG_CODE)?>" data-active-label="<?php _e('Selected', GMP_LANG_CODE)?>">
					<?php $this->isPro ? _e('Apply', GMP_LANG_CODE) : _e('Available in PRO', GMP_LANG_CODE)?>
				</a>
			</div>
		</li>
		<?php }?>
	</ul>
</div>
<!--Insert To Contact Form Wnd-->
<div id="gmpInsertToContactFormWnd" style="display: none;" title="<?php _e('Select Contact Form', GMP_LANG_CODE)?>">
	<?php if($this->isContactFormsInstalled) {?>
		<?php if($this->contactFormsForSelect) {?>
			<select name="contact_form" style="width: 100%; margin: 20px 0 0 0;">
				<?php foreach($this->contactFormsForSelect as $k => $v) { ?>
					<option value="<?php echo $k; ?>"><?php echo $v; ?></option>
				<?php }?>
			</select>
		<?php } else {?>
			<span style="font-size: 14px; line-height: 25px;"><?php echo sprintf(
					'You have no Contact Forms for now. <a target="_blank" href="%s">Create your first contact form</a> then just reload page with your Map settings, and you will see list with available Contact Forms for your Map.',
					frameCfs::_()->getModule('options')->getTabUrl('forms_add_new')); ?>
			</span>
		<?php }?>
	<?php } else {?>
		<span style="font-size: 14px; line-height: 25px;"><?php echo sprintf(
				'You need to install Contact Forms by Supsystic to use this feature. <a target="_blank" href="%s">Install plugin</a> from your admin area, or visit it\'s official page on Wordpress.org <a target="_blank" href="%s">here.</a>',
				admin_url('plugin-install.php?tab=search&type=term&s=Contact+Forms+by+Supsystic'),
				'https://wordpress.org/plugins/contact-form-by-supsystic/'); ?>
		</span>
	<?php }?>
</div>
<!--Map Authorization Fail Wnd-->
<div id="gmpMapAuthorizationFailWnd" style="display: none;" title="<?php _e('Oops! Something went wrong...', GMP_LANG_CODE)?>">
	<span style="font-size: 14px; line-height: 25px;">
		<?php echo sprintf(__(
			'Map can not be loaded completely. Probably, you are using our base Google Map API key.<br /><br />
This key is used by default for all our users in accordance with <a target="_blank" href="%s">Google Maps APIs Standard Plan</a>.
But each API key has fixed limits on count of maps loads per day.<br /><br />
You can create <a target="_blank" href="%s">your own Google Maps API key</a> and type it on <a target="_blank" href="%s">Settings tab</a>.
It\'s free, takes 10-20 minutes of your time and lets to apply your own API key only for your sites.
If you already use own Google Maps API key - you should open <a target="_blank" href="%s">Google Developer console</a> and check:
<ul style="padding-left: 20px; list-style: decimal;">
<li>Have you set correct settings to use your Google Map API key?</li>
<li>Have you paste correct Google Map API key on the <a target="_blank" href="%s">Settings tab</a>?</li>
<li>Open <a target="_blank" href="%s">browser console</a>, find Google Map API error and read its description in <a target="_blank" href="%s">Map API Errors table</a>.</li>
</ul>', GMP_LANG_CODE),
			'//developers.google.com/maps/pricing-and-plans/standard-plan-2016-update',
			'//supsystic.com/google-maps-api-key/',
			frameGmp::_()->getModule('options')->getTabUrl('settings'),
			'//console.developers.google.com/apis/credentials',
			frameGmp::_()->getModule('options')->getTabUrl('settings'),
			'//developers.google.com/maps/documentation/javascript/error-messages#checking-errors',
			'//developers.google.com/maps/documentation/javascript/error-messages#deverrorcodes'
		); ?>
	</span>
</div>
