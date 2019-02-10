<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<ul class="supsystic-bar-controls">
				<li title="<?php _e('Add Category', GMP_LANG_CODE)?>">
					<a class="button button-table-action" id="addMarkerGroup" href="<?php echo $this->addNewLink?>">
						<?php _e('Add Category', GMP_LANG_CODE)?>
					</a>
				</li>
				<li title="<?php _e('Delete selected', GMP_LANG_CODE)?>">
					<button class="button" id="gmpMgrRemoveGroupBtn" disabled data-toolbar-button>
						<?php _e('Delete Selected', GMP_LANG_CODE)?>
					</button>
				</li>
				<li title="<?php _e('Clear All', GMP_LANG_CODE)?>">
					<button class="button" id="gmpMgrClearBtn" data-toolbar-button>
						<?php _e('Clear All', GMP_LANG_CODE)?>
					</button>
				</li>
				<li title="<?php _e('Search', GMP_LANG_CODE)?>">
					<input id="gmpMgrTblSearchTxt" type="text" name="tbl_search" placeholder="<?php _e('Search', GMP_LANG_CODE)?>">
				</li>
			</ul>
			<div id="gmpMgrTblNavShell" class="supsystic-tbl-pagination-shell"></div>
			<div style="clear: both;"></div>
			<hr />
			<div id="gmpMgrTbl"></div>
			<div id="gmpMgrTblNav"></div>
			<div id="gmpMgrTblEmptyMsg" style="display: none;">
				<h3><?php printf(__("You have no Marker Categories for now. <a href='%s' style='font-style: italic;'>Create</a> your first Marker Category!", GMP_LANG_CODE), $this->addNewLink)?></h3>
			</div>
		</div>
		<div style="clear: both;"></div>
	</div>
</section>