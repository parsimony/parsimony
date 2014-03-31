<?php
app::$response->addCSSFile('admin/css/main.css');
app::$response->addCSSFile('admin/css/ui.css');
app::$response->addCSSFile('lib/tooltip/parsimonyTooltip.css', 'footer');
app::$response->addJSFile('lib/tooltip/parsimonyTooltip.js');
app::$response->addJSFile('admin/script.js');
app::$response->addJSFile('lib/HTML5sortable/jquery.sortable.js', 'footer');
app::$response->addJSFile('admin/blocks/toolbar/block.js', 'footer');
?>
<script type="text/javascript">

	$(document).ready(function() {
		ParsimonyAdmin.initBefore();
		<?php
		/* Define active panels */
		if (isset($_COOKIE['rightToolbarPanel'])) {
			echo '$(\'a[href="#' . $_COOKIE['rightToolbarPanel'] . '"]\')[0].click();';
		}
		?>
		
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

/* Sidebar Left:  Modules */
$leftSidebar = new \admin\blocks\modules("left_sidebar");
$leftSidebar->setConfig('cssClasses', 'sidebar');
$admin->addBlock($leftSidebar);

/* Sidebar Right */
if ($_SESSION['behavior'] == 2):
	
	$rightSidebar = new \core\blocks\tabs("right_sidebar");
	$rightSidebar->setConfig('cssClasses', 'sidebar');
	
		/* CSS */
	$block = new \admin\blocks\css("panelcss");
	$block->setConfig('headerTitle', 'CSS');
	$rightSidebar->addBlock($block);
	$admin->addBlock($rightSidebar);
	
	/* Blocks */
	$block = new \admin\blocks\blocks("panelblocks");
	$block->setConfig('headerTitle', 'Blocks');
	$rightSidebar->addBlock($block);
	
	/* Tree */
	$block = new \admin\blocks\tree("paneltree");
	$block->setConfig('headerTitle', 'Tree');
	$rightSidebar->addBlock($block);

	/* Theme */
	$block = new \admin\blocks\themes("themes");
	$block->setConfig('headerTitle', 'Themes');
	$rightSidebar->addBlock($block);
	$admin->addBlock($rightSidebar);
endif;

echo $admin->display();
?>
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
	<div id="conf_box_title"><?php echo t('Enter an ID for the new block') ?></div>
	<div id="dialog-input"><input type="text" id="dialog-id" /><input type="hidden" id="dialog-id-options" /></div>
	<div id="dialog-ac">
		<input type="button" id="dialog-ok" value="<?php echo t("Add", FALSE) ?>"/>
		<input type="button" id="dialog-cancel" onclick="ParsimonyAdmin.closeConfBox();ParsimonyAdmin.returnToShelter();" value="<?php echo t("Cancel", FALSE) ?>" />
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
		<span id="closemenu" onclick="ParsimonyAdmin.closeParsiadminMenu()" class="floatright ui-icon ui-icon-closethick"></span>
		<div class="options"></div>
	</div>
	<datalist id="parsidatalist"></datalist>
</div>

<?php
if (strstr($_SERVER['REQUEST_URI'], '?') != FALSE)
	$frameUrl = $_SERVER['REQUEST_URI'];
else
	$frameUrl = $_SERVER['REQUEST_URI'] . '?preview=ok';
$style = 'width: 100%; height: 100%;';
$sized = '';
if (isset($_COOKIE['screenX']) && isset($_COOKIE['screenY']) && is_numeric($_COOKIE['screenX']) && is_numeric($_COOKIE['screenY'])) {
	$sized = ' class="sized"';
	if (isset($_COOKIE['landscape']) && $_COOKIE['landscape'] == 'landscape') {
		$style = 'width: ' . $_COOKIE['screenY'] . 'px; height: ' . $_COOKIE['screenX'] . 'px;';
	} else {
		$style = 'width: ' . $_COOKIE['screenX'] . 'px; height: ' . $_COOKIE['screenY'] . 'px;';
	}
}
?>
<div id="previewContainer" style="<?php echo $style; ?>"<?php echo $sized; ?>>
	<iframe id="preview" src="<?php echo $frameUrl; ?>"></iframe>

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