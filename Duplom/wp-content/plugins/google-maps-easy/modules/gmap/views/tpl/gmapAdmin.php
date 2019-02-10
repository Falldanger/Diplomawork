<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<ul id="gmpGmapTblNavBtnsShell" class="supsystic-bar-controls">
				<li title="<?php _e('Search', GMP_LANG_CODE)?>">
					<input id="gmpGmapTblSearchTxt" type="text" name="tbl_search" placeholder="<?php _e('Search', GMP_LANG_CODE)?>">
				</li>
				<li title="<?php _e('Clone selected', GMP_LANG_CODE)?>">
					<button class="button" id="gmpGmapCloneGroupBtn" disabled data-toolbar-button>
						<i class="fa fa-fw fa-clone"></i>
						<?php _e('Clone selected', GMP_LANG_CODE)?>
					</button>
				</li>
				<li title="<?php _e('Delete selected', GMP_LANG_CODE)?>">
					<button class="button" id="gmpGmapRemoveGroupBtn" disabled data-toolbar-button>
						<i class="fa fa-fw fa-trash-o"></i>
						<?php _e('Delete selected', GMP_LANG_CODE)?>
					</button>
				</li>
				<?php /*We don't need this feature for now*/ ?>
				<?php /*?><li title="<?php _e('Clear All')?>">
					<button class="button" id="gmpGmapClearBtn" disabled data-toolbar-button>
						<?php _e('Clear', GMP_LANG_CODE)?>
					</button>
				</li><?php */?>
			</ul>
			<div id="gmpGmapTblNavShell" class="supsystic-tbl-pagination-shell"></div>
			<div style="clear: both;"></div>
			<hr />
			<table id="gmpGmapTbl"></table>
			<div id="gmpGmapTblNav"></div>
			<div id="gmpGmapTblEmptyMsg" style="display: none;">
				<h3><?php printf(__("You have no Maps for now. <a href='%s' style='font-style: italic;'>Create</a> your first Map!", GMP_LANG_CODE), $this->addNewLink)?></h3>
			</div>
		</div>
		<div style="clear: both;"></div>
	</div>
</section>