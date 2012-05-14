<?php
app::$request->page->addCSSFile(BASE_PATH . 'admin/style.css');
app::$request->page->addCSSFile(BASE_PATH . 'lib/colorpicker/colorpicker.css');
app::$request->page->addCSSFile(BASE_PATH . 'lib/tooltip/parsimonyTooltip.css');
app::$request->page->addJSFile('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.js');
app::$request->page->addJSFile(BASE_PATH . 'lib/tinymce/jquery.tinymce.js');
app::$request->page->addJSFile(BASE_PATH . 'lib/tinymce/plugins/tinybrowser/tb_tinymce.js.php');
app::$request->page->addJSFile(BASE_PATH . 'lib/colorpicker/colorpicker.js');
app::$request->page->addJSFile(BASE_PATH . 'lib/tooltip/parsimonyTooltip.js');
app::$request->page->addJSFile(BASE_PATH . 'lib/dnd/parsimonyDND.js');
app::$request->page->addJSFile(BASE_PATH . 'admin/script.js');
app::$request->page->addJSFile(BASE_PATH . 'lib/CodeMirror/lib/codemirror.js');
app::$request->page->addCSSFile(BASE_PATH . 'lib/CodeMirror/theme/default.css');
app::$request->page->addCSSFile(BASE_PATH . 'lib/CodeMirror/lib/codemirror.css');
app::$request->page->addJSFile(BASE_PATH . 'lib/CodeMirror/mode/xml/xml.js');
app::$request->page->addJSFile(BASE_PATH . 'lib/CodeMirror/mode/css/css.js');
app::$request->page->addJSFile(BASE_PATH . 'cache/' . app::$request->getLocale() . '-lang.js');
?>
<script type="text/javascript">
    
    var CSSTHEMEPATH = "<?php echo THEMEMODULE ?>/themes/<?php echo THEME ?>/<?php echo THEMETYPE ?>.css";
    var CSSPAGEPATH = "<?php echo MODULE . '/' . THEMETYPE ?>.css";
		
    $(document).ready(function() {
        ParsimonyAdmin.initBefore();
        $('#parsiframe').load(function() {
            if(!ParsimonyAdmin.isInit) ParsimonyAdmin.init();   
        });
    });
</script>  

<div id="admin">
    <?php
    $menutop = new \admin\blocks\menu("toolbar");
    echo $menutop->display();
    ?>
    <?php
    if (ID_ROLE == 1):
        $style = '';
        if (isset($_COOKIE['rightToolbarCoordX']) && $_COOKIE['rightToolbarCoordX'] != 0)
            $style .= 'left:' . $_COOKIE['rightToolbarCoordX'] . ';top:' . $_COOKIE['rightToolbarCoordY'] . ';';
        if (isset($_COOKIE['rightToolbarX']))
            $style .= 'width:' . $_COOKIE['rightToolbarX'] . ';';
        ?>
        <div id="right_sidebar" class="sidebar creation<?php if (isset($_COOKIE['rightToolbarOpen']) && $_COOKIE['rightToolbarOpen'] == 0)
                 echo ' close';
        ?>"<?php echo ' style="' . $style . '"'; ?>>
                 <?php
                 /* Container Right */
                 $contaireright = new \core\blocks\container("contaireright");
                 /* Tree */
                 $block = new \admin\blocks\tree("paneltree");
                 $contaireright->addBlock($block);
                 /* CSS */
                 $block = new \admin\blocks\css("panelcss");
                 $contaireright->addBlock($block);
                 ?>
            <div class="subSidebar">         
                <div class="subSidebarOnglet handle" style="cursor: move; display: block; " title="<?php echo t('Move', FALSE); ?>"><span class="ui-icon ui-icon-arrow-4"></span></div>
                <div class="subSidebarOnglet revert" style="cursor: default; display: block; " title="<?php echo t('Return', FALSE); ?>"><span class="ui-icon ui-icon-seek-next"></span></div>
                <div class="subSidebarOnglet" id="openrightslide" title="<?php echo t('Slide', FALSE); ?>"><span class="ui-icon ui-icon-circle-arrow-e"></span></div>
                <div class="subSidebarOnglet" id="resizerightslide" title="<?php echo t('Resize', FALSE); ?>"><span class="ui-icon ui-icon-arrowthick-2-e-w  ui-resizable-handle ui-resizable-w"></span></div>
    <?php foreach ($contaireright->getBlocks() AS $block): ?>
                    <div class="subSidebarOnglet <?php echo t($block->getId(), FALSE); ?>" rel="<?php echo t($block->getId(), FALSE); ?>" title="<?php echo t($block->getId(), FALSE); ?>"></div>
                    <?php endforeach; ?>
                <div class="subSidebarOnglet" id="csspicker"><img src="<?php echo BASE_PATH . 'admin/img/picker.png'; ?>" title="<?php echo t('CSSPicker', FALSE); ?>"/></div>
            </div>
            <div class="contenttab cs">
                <div>
                    <?php foreach ($contaireright->getBlocks() AS $block): ?>
                        <div class="mainTab <?php echo $block->getId(); ?> ellipsis" rel="<?php echo $block->getId(); ?>">
                    <?php echo t($block->getName(), FALSE); ?>
                        </div>
                <?php endforeach; ?>
                </div>
        <?php
        echo $contaireright->display();
        ?>
            </div>
        </div>
    <?php
    endif;
    $style = '';
    if (isset($_COOKIE['leftToolbarCoordX']) && $_COOKIE['leftToolbarCoordX'] != 0)
        $style .= 'left:' . $_COOKIE['leftToolbarCoordX'] . ';top:' . $_COOKIE['leftToolbarCoordY'] . ';';
    if (isset($_COOKIE['leftToolbarX']))
        $style .= 'width:' . $_COOKIE['leftToolbarX'] . ';';
    ?>
    <div id="left_sidebar" class="sidebar<?php if (isset($_COOKIE['leftToolbarOpen']) && $_COOKIE['leftToolbarOpen'] == 0)
                 echo ' close';
    ?>"<?php echo ' style="' . $style . '"'; ?>>
             <?php
             /* Container Left */
             $contaireleft = new \core\blocks\container("modulespages");
             /* Modules */
             $block = new \admin\blocks\modules("panelmodules");
             $contaireleft->addBlock($block);
             if (ID_ROLE == 1):
                 /* Blocks */
                 $block = new \admin\blocks\blocks("panelblocks");
                 $contaireleft->addBlock($block);
             endif;
             ?>
        <div class="subSidebar">
            <div class="subSidebarOnglet handle" style="cursor: move; display: block;" title="<?php echo t('Move', FALSE); ?>"><span class="ui-icon ui-icon-arrow-4"></span></div>
            <div class="subSidebarOnglet revert" style="cursor: default; display: block;" title="<?php echo t('Return', FALSE); ?>"><span class="ui-icon ui-icon-seek-prev"></span></div>
            <div class="subSidebarOnglet" id="openleftslide"><span class="ui-icon ui-icon-circle-arrow-w" title="<?php echo t('Slide', FALSE); ?>"></span></div>
            <div class="subSidebarOnglet" id="resizeleftslide" title="<?php echo t('Resize', FALSE); ?>"><span class="ui-icon ui-icon-arrowthick-2-e-w ui-resizable-handle ui-resizable-e"></span></div>
                    <?php foreach ($contaireleft->getBlocks() AS $block): ?>
                <div class="subSidebarOnglet <?php echo t($block->getId(), FALSE); ?>" rel="<?php echo t($block->getId(), FALSE); ?>" title="<?php echo t($block->getId(), FALSE); ?>"></div>
                    <?php endforeach; ?>
        </div>

        <div class="contenttab cs">
            <div class="creation">
            <?php foreach ($contaireleft->getBlocks() AS $block): ?>
                    <div class="mainTab <?php echo t($block->getId(), FALSE); ?> ellipsis" rel="<?php echo t($block->getId(), FALSE); ?>"><span class="ui-icon floatleft <?php echo t($block->getId(), FALSE); ?>"></span>
    <?php echo t($block->getName(), FALSE);
    if ($block->getId() == 'panelmodules')
        echo '<a href="#" title="' . t('Add a Module', FALSE) . '" id="add-module" class="action parsiplusone" rel="getViewAddModule"></a>';
    ?>
                    </div>
    <?php endforeach; ?>
            </div>
<?php
echo $contaireleft->display();
?>
        </div>

    </div>
<?php
$themes = new \admin\blocks\themes("themes");
echo $themes->display();
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
    </div>
</div>
    <?php
    echo '<style>';
    if (isset($_COOKIE['leftToolbarPanel']) && $_COOKIE['leftToolbarPanel'] == 'panelblocks') {
        echo '#panelmodules{display:none;}';
    } else {
        echo '#panelblocks{display:none;}';
    }
    if (isset($_COOKIE['rightToolbarPanel']) && $_COOKIE['rightToolbarPanel'] == 'panelcss') {
        echo '#paneltree{display:none;}';
    } else {
        echo '#panelcss{display:none;}';
    }
    echo '</style>';
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
