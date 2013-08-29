<?php
app::$request->page->addCSSFile('admin/style.css');
app::$request->page->addCSSFile('lib/tooltip/parsimonyTooltip.css', 'footer');
app::$request->page->addCSSFile('lib/HTML5editor/HTML5editor.css', 'footer');
app::$request->page->addJSFile('lib/HTML5editor/HTML5editor.js', 'footer');
app::$request->page->addJSFile('lib/tooltip/parsimonyTooltip.js');
app::$request->page->addJSFile('admin/script.js');
app::$request->page->addJSFile('lib/HTML5sortable/jquery.sortable.js', 'footer');
app::$request->page->addJSFile('admin/blocks/toolbar/block.js', 'footer');
?>
<script type="text/javascript">

	var CSSTHEMEPATH = "<?php echo THEMEMODULE ?>/themes/<?php echo THEME ?>/<?php echo THEMETYPE ?>.css";
	var CSSPAGEPATH = "<?php echo MODULE . '/css/' . THEMETYPE ?>.css";

	$(document).ready(function() {
		ParsimonyAdmin.initBefore();
		$(ParsimonyAdmin.currentDocument).ready(function() {
			if (!ParsimonyAdmin.isInit)
				ParsimonyAdmin.init();
		});
	});
</script>

<?php
$admin = new \core\blocks\container("admin");

/* Menu */
$menutop = new \admin\blocks\menu("toolbar");
$admin->addBlock($menutop);

/* Define active panels */
$leftPan = 'modules';
if (isset($_COOKIE['leftToolbarPanel'])) {
	$leftPan = $_COOKIE['leftToolbarPanel'];
}
$rightPan = 'paneltree';
if (isset($_COOKIE['rightToolbarPanel'])) {
	$rightPan = $_COOKIE['rightToolbarPanel'];
}

/* Sidebar Left */
$leftSidebar = new \admin\blocks\adminsidebar("left_sidebar");

/* Modules */
$block = new \admin\blocks\modules("modules");
if ($leftPan != 'modules') $block->setConfig('cssClasses', 'none');
$leftSidebar->addBlock($block);

if ($_SESSION['behavior'] == 2):
	/* Blocks */
	$block = new \admin\blocks\blocks("panelblocks");
	if ($leftPan != 'panelblocks') $block->setConfig('cssClasses', 'none');
	$leftSidebar->addBlock($block);

	$admin->addBlock($leftSidebar);

	/* Sidebar Right */
	$rightSidebar = new \admin\blocks\adminsidebar("right_sidebar");
	$rightSidebar->setSide('right');

	/* Tree */
	$block = new \admin\blocks\tree("paneltree");
	if ($rightPan != 'paneltree') $block->setConfig('cssClasses', 'none');
	$rightSidebar->addBlock($block);

	/* CSS */
	$block = new \admin\blocks\css("panelcss");
	if ($rightPan != 'panelcss') $block->setConfig('cssClasses', 'none');
	$rightSidebar->addBlock($block);
	$admin->addBlock($rightSidebar);

	/* Theme */
	$block = new \admin\blocks\themes("themes");
	if ($rightPan != 'themes') $block->setConfig('cssClasses', 'none');
	$rightSidebar->addBlock($block);
	$admin->addBlock($rightSidebar);
endif;

echo $admin->display();
?>
<div id="toolbarEditMode">
	<div style="float:left"><input type="image" src="<?php echo BASE_PATH; ?>admin/img/undo.png" id="toolbarEditModeUndo" class="tooltip toolbarEditModeCommands" data-tooltip="<?php echo t('Undo'); ?>" data-command="undo" /><input type="image" src="<?php echo BASE_PATH; ?>admin/img/redo.png" value="<?php echo t('Redo'); ?>" id="toolbarEditModeRedo" class="tooltip toolbarEditModeCommands" data-tooltip="<?php echo t('Undo'); ?>"  data-command="redo" /></div><div style="float:right"><input type="button" style="width: 140px;" value="<?php echo t('Save modifications'); ?>" id="toolbarEditModeSave" /></div>
</div>
<div id="conf_box_overlay" class="none">
	<div id="conf_box_load">
		<div id="followingBalls_1" class="followingBalls"></div>
		<div id="followingBalls_2" class="followingBalls"></div>
		<div id="followingBalls_3" class="followingBalls"></div>
		<div id="followingBalls_4" class="followingBalls"></div>
	</div>
	<iframe name="conf_box_content_iframe" id="conf_box_content_iframe" src="" class="conf_box"></iframe>
	<div id="conf_box_content_inline" class="conf_box"></div>
</div>
<div id="dialog" style="display:none;width: 450px;">
	<span id="conf_box_close" onclick="top.ParsimonyAdmin.closeConfBox()" class="floatright ui-icon ui-icon-closethick"></span>
	<div id="conf_box_title"><?php echo t('Entrez un identifiant pour ce nouveau bloc') ?></div>
	<div style="text-align: center;padding: 20px 0;background:#fbfbfb"><input type="text" id="dialog-id" /><input type="hidden" id="dialog-id-options" /></div>
	<div style="text-align: right;padding: 10px;background: #E2E2E2;border-top: 1px solid #BBB;">
		<input type="button" id="dialog-ok" value="<?php echo t("Add", FALSE) ?>" style="margin-right: 10px;" />
		<input type="button" onclick="ParsimonyAdmin.closeConfBox();ParsimonyAdmin.returnToShelter();" value="<?php echo t("Cancel", FALSE) ?>" />
	</div>
</div>
<iframe name="formResult" id="formResult" src="" class="none"></iframe>
<div id="shelter">
	<div id="dropInPage" class="marqueurdragndrop">
		<div id="dropInPageChild"></div>
	</div>
	<div id="dropInTree" class="marqueurdragndrop"></div>
	<div id="notify"></div>
	<div id="menu">
		<span id="closemenu" style="position: absolute;top: -15px;right: -15px;" onclick="ParsimonyAdmin.closeParsiadminMenu()" class="floatright ui-icon ui-icon-closethick"></span>
		<div class="options"></div>
	</div>
	<datalist id="parsidatalist"></datalist>
</div>

<?php
if (strstr($_SERVER['REQUEST_URI'], '?') != FALSE)
	$frameUrl = $_SERVER['REQUEST_URI'];
else
	$frameUrl = $_SERVER['REQUEST_URI'] . '?parsiframe=ok';
$style = 'width: 100%; height: 100%;';
if (isset($_COOKIE['screenX']) && isset($_COOKIE['screenY']) && is_numeric($_COOKIE['screenX']) && is_numeric($_COOKIE['screenY'])) {
	if (isset($_COOKIE['landscape']) && $_COOKIE['landscape'] == 'landscape') {
		$style = 'width: ' . $_COOKIE['screenY'] . 'px; height: ' . $_COOKIE['screenX'] . 'px;';
	} else {
		$style = 'width: ' . $_COOKIE['screenX'] . 'px; height: ' . $_COOKIE['screenY'] . 'px;';
	}
}
?>
<iframe id="parsiframe" src="<?php echo $frameUrl; ?>" style="<?php echo $style; ?>"></iframe>


<div id="overlays">
	<div id="blockOverlay"></div>
	<div id="parsimonyDND">
		<div class="parsimonyResizeInfo">
			<span class="parsimonyResizeClose" id="idName"></span>
			<a href="#" style="border-left:0" class="toolbarButton configure_block" rel="getViewConfigBlock" data-action="onConfigure" title="Configuration">
				<span class="spanDND ui-icon-wrench"></span>
			</a>
			<div href="#" id="stylableElements" class="toolbarButton">
				<a href="#" style="display:block;width: 100%;height: 100%" class="cssblock" data-action="onDesign">
					<span class="spanDND sprite sprite-csspickerlittle"></span>
				</a>
				<div id="CSSProps" class="none"></div>
			</div>
			<a href="#" draggable="true" class="toolbarButton move_block" style="cursor:move">
				<span class="spanDND ui-icon-arrow-4"></span>
			</a>
			<a href="#" class="toolbarButton config_destroy" data-action="onDelete">
				<span class="spanDND ui-icon-trash"></span>
			</a>
			<a href="#" style="border-right:0;border-radius: 0 3px 3px 0;" class="toolbarButton" onclick="ParsimonyAdmin.unSelectBlock();return false;">
				<span class="spanDND ui-icon-closethick"></span>
			</a>
			<div class="arrow" style="left: 20px; border-color: #f9f9f9 transparent transparent;bottom: -14px;margin-left: -7px;width: 0;height: 0;position: absolute;border-width: 7px;border-style: solid;"></div>
		</div>
		<div class="parsimonyResize se"></div>
		<div class="parsimonyResize nw"></div>
		<div class="parsimonyResize ne"></div>
		<div class="parsimonyResize sw"></div>
		<div class="parsimonyMove"></div>
	</div>
</div>