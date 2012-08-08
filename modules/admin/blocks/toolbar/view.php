<?php
app::$request->page->addJSFile('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.js');
app::$request->page->addCSSFile(BASE_PATH . 'admin/style.css');
app::$request->page->addCSSFile(BASE_PATH . 'lib/tooltip/parsimonyTooltip.css');
app::$request->page->addCSSFile(BASE_PATH . 'lib/HTML5editor/HTML5editor.css');
app::$request->page->addJSFile(BASE_PATH . 'lib/HTML5editor/HTML5editor.js');
app::$request->page->addJSFile(BASE_PATH . 'lib/tooltip/parsimonyTooltip.js');
app::$request->page->addJSFile(BASE_PATH . 'admin/script.js');
app::$request->page->addJSFile(BASE_PATH . 'cache/' . app::$request->getLocale() . '-lang.js');
app::$request->page->addJSFile(BASE_PATH . 'admin/blocks/toolbar/script.js');
?>
<script>typeof jQuery.ui != 'undefined' || document.write('<script src="' + BASE_PATH + 'lib/jquery-ui-1.8.18.min.js"><\/script>')</script>
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

/* Sidebar Left */
$leftSidebar = new \admin\blocks\adminsidebar("left_sidebar");

/* Modules */
$block = new \admin\blocks\modules("panelmodules");
$leftSidebar->addBlock($block);

if (BEHAVIOR == 2):
    /* Blocks */
    $block = new \admin\blocks\blocks("panelblocks");
    $leftSidebar->addBlock($block);

    $admin->addBlock($leftSidebar);

    /* Sidebar Right */
    $rightSidebar = new \admin\blocks\adminsidebar("right_sidebar");
    $rightSidebar->setSide('right');

    /* Tree */
    $block = new \admin\blocks\tree("paneltree");
    $rightSidebar->addBlock($block);

    /* CSS */
    $block = new \admin\blocks\css("panelcss");
    $rightSidebar->addBlock($block);
    $admin->addBlock($rightSidebar);
endif;
/* Themes */
$themes = new \admin\blocks\themes("themes");
$admin->addBlock($themes);

echo $admin->display();
?>
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
	<div style="text-align: center;padding-top: 20px;"><input type="text" id="dialog-id" /></div>
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

<?php
echo '<script> $(document).ready(function() {';
if (isset($_COOKIE['leftToolbarPanel']) && $_COOKIE['leftToolbarPanel'] == 'panelmodules') {
    echo '$(".panelmodules").trigger("click");';
} else {
    echo '$(".panelblocks").trigger("click");';
}
if (isset($_COOKIE['rightToolbarPanel']) && $_COOKIE['rightToolbarPanel'] == 'paneltree') {
    echo '$(".paneltree").trigger("click");';
} else {
    echo '$(".panelcss").trigger("click");';
}
echo '}); </script>';
?>
<div class="align_center" style="min-height: 600px;">
    <?php
    if (strstr($_SERVER['REQUEST_URI'], '?') != FALSE)
	$frameUrl = $_SERVER['REQUEST_URI'];
    else
	$frameUrl = $_SERVER['REQUEST_URI'] . '?parsiframe=ok';
    ?>
    <iframe id="parsiframe" src="<?php echo $frameUrl; ?>" align="middle"></iframe>
</div>

<div id="overlays">
    <div id="blockOverlay"></div>
</div>