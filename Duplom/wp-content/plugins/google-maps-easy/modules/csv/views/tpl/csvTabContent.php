<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<table class="form-table">
				<tr>
					<th scope="row">
						<label>
							<?php _e('Maps', GMP_LANG_CODE); ?>
						</label>
					</th>
					<td>
						<button id="gmpCsvExportMapsBtn" class="button">
							<?php _e('Export', GMP_LANG_CODE); ?>
						</button>
						<?php echo htmlGmp::ajaxfile('csv_import_file_maps', array(
							'url' => uriGmp::_(array('baseUrl' => admin_url('admin-ajax.php'), 'page' => 'csv', 'action' => 'import', 'type' => 'maps', 'reqType' => 'ajax')),
							'data' => 'gmpCsvImportData',
							'buttonName' => __('Import', GMP_LANG_CODE),
							'responseType' => 'json',
							'onSubmit' => 'gmpCsvImportOnSubmit',
							'onComplete' => 'gmpCsvImportOnComplete',
							'btn_class' => 'button',
						))?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="gmpCsvExportMarkersBtn">
							<?php _e('Markers', GMP_LANG_CODE); ?>
						</label>
					</th>
					<td>
						<button id="gmpCsvExportMarkersBtn" class="button">
							<?php _e('Export', GMP_LANG_CODE); ?>
						</button>
						<?php echo htmlGmp::ajaxfile('csv_import_file_markers', array(
							'url' => uriGmp::_(array('baseUrl' => admin_url('admin-ajax.php'), 'page' => 'csv', 'action' => 'import', 'type' => 'markers', 'reqType' => 'ajax')),
							'data' => 'gmpCsvImportData',
							'buttonName' => __('Import', GMP_LANG_CODE),
							'responseType' => 'json',
							'onSubmit' => 'gmpCsvImportOnSubmit',
							'onComplete' => 'gmpCsvImportOnComplete',
							'btn_class' => 'button',
						))?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="gmpCsvExportFiguresBtn">
							<?php _e('Figures', GMP_LANG_CODE); ?>
						</label>
					</th>
					<td>
						<button id="gmpCsvExportFiguresBtn" class="button">
							<?php _e('Export', GMP_LANG_CODE); ?>
						</button>
						<?php echo htmlGmp::ajaxfile('csv_import_file_figures', array(
							'url' => uriGmp::_(array('baseUrl' => admin_url('admin-ajax.php'), 'page' => 'csv', 'action' => 'import', 'type' => 'figures', 'reqType' => 'ajax')),
							'data' => 'gmpCsvImportData',
							'buttonName' => __('Import', GMP_LANG_CODE),
							'responseType' => 'json',
							'onSubmit' => 'gmpCsvImportOnSubmit',
							'onComplete' => 'gmpCsvImportOnComplete',
							'btn_class' => 'button',
						))?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="gmpCsvExportHeatMapBtn">
							<?php _e('HeatMap Layer', GMP_LANG_CODE); ?>
						</label>
					</th>
					<td>
						<button id="gmpCsvExportHeatmapBtn" class="button">
							<?php _e('Export', GMP_LANG_CODE); ?>
						</button>
						<?php echo htmlGmp::ajaxfile('csv_import_file_heatmap', array(
							'url' => uriGmp::_(array('baseUrl' => admin_url('admin-ajax.php'), 'page' => 'csv', 'action' => 'import', 'type' => 'heatmap', 'reqType' => 'ajax')),
							'data' => 'gmpCsvImportData',
							'buttonName' => __('Import', GMP_LANG_CODE),
							'responseType' => 'json',
							'onSubmit' => 'gmpCsvImportOnSubmit',
							'onComplete' => 'gmpCsvImportOnComplete',
							'btn_class' => 'button',
						))?>
					</td>
				</tr>
				<tr>
					<td colspan="2"><div id="gmpCsvImportMsg"></div></td>
				</tr>
			</table>
			<h3><?php _e('CSV Options', GMP_LANG_CODE)?></h3>
			<form id="gmpCsvForm">
				<table class="form-table no-border">
					<tr>
						<th scope="row">
							<label for="gmpCsvExportDelimiter">
								<?php _e('Delimiter', GMP_LANG_CODE); ?>
							</label>
						</th>
						<td>
							<?php echo htmlGmp::selectbox('opt_values[csv_options][delimiter]', array(
								'options' => $this->delimiters,
								'value' => !empty($this->options['delimiter']) ? $this->options['delimiter'] : ';',
								'attrs' => 'style="min-width: 150px;" id="gmpCsvExportDelimiter"'))?>
						</td>
					</tr>
				</table>
				<?php echo htmlGmp::hidden('page', array('value' => 'csv'))?>
				<?php echo htmlGmp::hidden('action', array('value' => 'saveCsvOptions'))?>
			</form>
			<button id="gmpCsvSaveBtn" class="button">
				<i class="fa fa-save"></i>
				<?php _e('Save', GMP_LANG_CODE)?>
			</button>
		</div>
	</div>
</section>