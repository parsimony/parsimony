<?php
app::$request->page->addCSSFile(BASE_PATH . 'admin/style.css');
app::$request->page->addCSSFile(BASE_PATH . 'lib/tooltip/parsimonyTooltip.css','footer');
app::$request->page->addCSSFile(BASE_PATH . 'lib/HTML5editor/HTML5editor.css','footer');
app::$request->page->addJSFile(BASE_PATH . 'lib/HTML5editor/HTML5editor.js','footer');
app::$request->page->addJSFile(BASE_PATH . 'lib/tooltip/parsimonyTooltip.js','footer');
app::$request->page->addJSFile(BASE_PATH . 'admin/script.js');
app::$request->page->addJSFile(BASE_PATH . 'lib/HTML5sortable/jquery.sortable.js','footer');
app::$request->page->addJSFile(BASE_PATH . 'admin/blocks/toolbar/script.js','footer');
?>
<script type="text/javascript">
    
    var CSSTHEMEPATH = "<?php echo THEMEMODULE ?>/themes/<?php echo THEME ?>/<?php echo THEMETYPE ?>.css";
    var CSSPAGEPATH = "<?php echo MODULE . '/' . THEMETYPE ?>.css";
		
    $(document).ready(function() {
        ParsimonyAdmin.initBefore();
        $(ParsimonyAdmin.currentDocument).ready(function() {
            if(!ParsimonyAdmin.isInit) ParsimonyAdmin.init();   
        });
    });
</script>  


<?php
$admin = new \core\blocks\container("admin");

/* Menu  */
$menutop = new \admin\blocks\menu("toolbar");
$admin->addBlock($menutop);

/* Define active panels */
$leftPan = 'panelmodules';
if (isset($_COOKIE['leftToolbarPanel']) && $_COOKIE['leftToolbarPanel'] == 'panelblocks') {
    $leftPan = 'panelblocks';
}
$rightPan = 'paneltree';
if (isset($_COOKIE['rightToolbarPanel']) && $_COOKIE['rightToolbarPanel'] == 'panelcss') {
    $rightPan = 'panelcss';
}

/* Sidebar Left */
$leftSidebar = new \admin\blocks\adminsidebar("left_sidebar");

/* Modules */
$block = new \admin\blocks\modules("panelmodules");
if($leftPan != 'panelmodules') $block->setConfig('cssClasses','none');
$leftSidebar->addBlock($block);

if (BEHAVIOR == 2):
    /* Blocks */
    $block = new \admin\blocks\blocks("panelblocks");
    if($leftPan != 'panelblocks') $block->setConfig('cssClasses','none');
    $leftSidebar->addBlock($block);

    $admin->addBlock($leftSidebar);

    /* Sidebar Right */
    $rightSidebar = new \admin\blocks\adminsidebar("right_sidebar");
    $rightSidebar->setSide('right');

    /* Tree */
    $block = new \admin\blocks\tree("paneltree");
    if($rightPan != 'paneltree') $block->setConfig('cssClasses','none');
    $rightSidebar->addBlock($block);

    /* CSS */
    $block = new \admin\blocks\css("panelcss");
    if($rightPan != 'panelcss') $block->setConfig('cssClasses','none');
    $rightSidebar->addBlock($block);
    $admin->addBlock($rightSidebar);
endif;
/* Themes */
$themes = new \admin\blocks\themes("themes");
$admin->addBlock($themes);

echo $admin->display();
?>
<div id="toolbarEditMode">
    <div style="float:left"><input type="image" src="<?php echo BASE_PATH; ?>admin/img/undo.png" id="toolbarEditModeUndo" class="tooltip toolbarEditModeCommands" data-tooltip="<?php echo t('Undo'); ?>" data-command="undo" /><input type="image" src="<?php echo BASE_PATH; ?>admin/img/redo.png" value="<?php echo t('Redo'); ?>" id="toolbarEditModeRedo" class="tooltip toolbarEditModeCommands" data-tooltip="<?php echo t('Undo'); ?>"  data-command="redo" /></div><div style="float:right"><input type="button" style="width: 140px;" value="<?php echo t('Save modifications'); ?>" id="toolbarEditModeSave" /></div>
</div>
<div id="admin_core">
    <div id="conf_box_overlay">
	<div id="conf_box_load"></div>
	<div id="conf_box" class="none">
	    <span id="conf_box_close" onclick="ParsimonyAdmin.closeConfBox()" class="floatright ui-icon ui-icon-closethick"></span>
	    <span id="conf_box_wpopup" class="floatright ui-icon ui-icon-extlink"></span>
	    <div id="conf_box_title"></div>
	    <div id="conf_box_content">
		<iframe name="conf_box_content_iframe" id="conf_box_content_iframe" src="" style="max-height:630px;overflow: hidden"></iframe>
		<div id="conf_box_content_inline"></div>
	    </div>
	    <form method="POST" target="conf_box_content_iframe" id="conf_box_form" action="<?php echo BASE_PATH . 'admin/action' ?>" class="none">
		<input type="hidden" name="vars" value="" id="conf_box_form_vars" />
	    </form>
	</div>
    </div>
    <div id="dialog" style="display:none;width: 450px;">
	<div style="text-align: center;padding-top: 20px;"><input type="text" id="dialog-id" /><input type="hidden" id="dialog-id-options" /></div>
	<div style="text-align: center;padding: 10px 0px;background: #E2E2E2;margin-top: 10px;border-top: 1px solid #BBB;"><input type="button" id="dialog-ok" value="<?php echo t("Add", FALSE) ?>" /><input type="button" onclick="ParsimonyAdmin.closeConfBox();ParsimonyAdmin.returnToShelter();" value="<?php echo t("Cancel", FALSE) ?>" /></div>
    </div>
    <iframe name="ajaxhack" id="ajaxhack" src="" class="none"></iframe>
</div>
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
<div class="align_center" style="min-height: 600px;">
    <?php
    if (strstr($_SERVER['REQUEST_URI'], '?') != FALSE)
	$frameUrl = $_SERVER['REQUEST_URI'];
    else
	$frameUrl = $_SERVER['REQUEST_URI'] . '?parsiframe=ok';
    $style = 'width: 100%; height: 100%;';
    if(isset($_COOKIE['screenX']) && isset($_COOKIE['screenY']) && is_numeric($_COOKIE['screenX']) && is_numeric($_COOKIE['screenY'])){
	if(isset($_COOKIE['landscape']) && $_COOKIE['landscape'] == 'landscape'){
	    $style = 'width: '.$_COOKIE['screenY'].'px; height: '.$_COOKIE['screenX'].'px;';
	}else{
	    $style = 'width: '.$_COOKIE['screenX'].'px; height: '.$_COOKIE['screenY'].'px;';
	}
    }
    ?>
    <iframe id="parsiframe" src="<?php echo $frameUrl; ?>" align="middle" style="<?php echo $style; ?>"></iframe>
</div>

<div id="overlays">
    <div id="blockOverlay"></div>
</div>