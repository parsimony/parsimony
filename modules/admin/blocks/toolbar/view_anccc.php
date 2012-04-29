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
app::$request->page->addJSFile(BASE_PATH . 'cache/' . app::$request->getLocale() . '-lang.js');
?>
<script type="text/javascript">
    
    var CSSTHEMEPATH = "<?php echo 'http://' . $_SERVER['HTTP_HOST'] . BASE_PATH ?><?php echo THEMEMODULE ?>/themes/<?php echo THEME ?>/<?php echo THEMETYPE ?>.css";
    var CSSPAGEPATH = "<?php echo 'http://' . $_SERVER['HTTP_HOST'] . BASE_PATH ?><?php echo MODULE . '/' . THEMETYPE ?>.css";
		
    $(document).ready(function() {
        ParsimonyAdmin.initBefore();
        $('#parsiframe').load(function() {
            if(!ParsimonyAdmin.isInit) ParsimonyAdmin.init();   
            $(document).on('click',".cssblock",function(e){ 
                e.preventDefault();
                var filePath = "<?php echo 'http://' . $_SERVER['HTTP_HOST'] . BASE_PATH ?><?php echo THEMEMODULE ?>/themes/<?php echo THEME ?>/<?php echo THEMETYPE ?>.css";
                if(ParsimonyAdmin.whereIAm(ParsimonyAdmin.inProgress)=='page') filePath = "<?php echo 'http://' . $_SERVER['HTTP_HOST'] . BASE_PATH ?><?php echo MODULE . '/' . THEMETYPE ?>.css";
                ParsimonyAdmin.displayCSSConf(filePath, "#" + ParsimonyAdmin.inProgress.attr("id"));
            });
            $(".cssblock").on('click',function(e){// fix #config_tree_selector
                e.preventDefault();
                var filePath = "<?php echo 'http://' . $_SERVER['HTTP_HOST'] . BASE_PATH ?><?php echo THEMEMODULE ?>/themes/<?php echo THEME ?>/<?php echo THEMETYPE ?>.css";
                if(ParsimonyAdmin.whereIAm(ParsimonyAdmin.inProgress)=='page') filePath = "<?php echo 'http://' . $_SERVER['HTTP_HOST'] . BASE_PATH ?><?php MODULE . '/' . THEMETYPE ?>.css";
                ParsimonyAdmin.displayCSSConf(filePath, "#" + ParsimonyAdmin.inProgress.attr("id"));
            });
        });
        $(".tooltip").parsimonyTooltip({triangleWidth:5});
    });
</script>  

<div id="admin">
    <div id="toolbar">
        <div class="rightpart floatright">
            <?php if (ID_ROLE == 1): ?>
                <a class="floatleft" href="#"><span class="ui-icon ui-icon-clock floatleft"></span><span id="timer"></span></a>
            <?php endif; ?>
            <a href="#" class="floatleft action" rel="getViewAdminLanguage" title="<?php echo t('Current Language', FALSE); ?>">
                <span class="ui-icon ui-icon-flag floatleft"></span> <?php echo \app::$config['locales'][\app::$request->getLocale()] ?>
            </a>
            <a href="#" class="floatleft action" rel="getViewUserProfile" title="My Profile">
                <span class="ui-icon ui-icon-locked floatleft"></span><?php echo ucfirst(htmlentities($_SESSION['login'], ENT_QUOTES, "UTF-8")); ?>        
            </a>
            <a href="<?php echo BASE_PATH; ?>logout" class="floatleft">
                <span class="ui-icon ui-icon-circle-close floatleft"></span>&nbsp;<?php echo t('Logout', FALSE); ?>
            </a>
        </div>
        <ul>
            <li style="border:0;width:208px"><a href="http://parsimony.mobi" target="_blank" style="padding:0;display: block;height:28px;">
                    <img src="<?php echo BASE_PATH; ?>admin/img/parsimony.png">
                </a>
            </li>
            <?php if (ID_ROLE == 1): ?>
                <li style="border-left:0;"><a href="#"  class="action" rel="getViewModuleAdmin" params="module=admin" title="<?php echo t('Settings', FALSE); ?>"><?php echo t('Settings', FALSE); ?></a></li>
                <li class="subMenu" >
                    <a href="#" style="background: none" title="<?php echo t('Accounts', FALSE); ?>"><?php echo t('Accounts', FALSE); ?></a>
                    <ul>
                        <li>
                            <a href="#" class="action" rel="getViewAdminRights" title="Gestion des Droits"><?php echo t('Permissions', FALSE); ?></a>
                        </li>
                        <li>
                            <a href="#" class="modeleajout ellipsis" rel="core - role" title=""><?php echo t('Manage Rights', FALSE); ?></a>
                        </li>
                        <li>
                            <a href="#" class="modeleajout ellipsis" rel="core - user" title=""><?php echo t('Manage Roles', FALSE); ?></a>
                        </li>
                    </ul>
                </li>              
                <li><a href="#" onclick="$(this).next('form').trigger('submit');return false;" title="<?php echo t('Manage Themes', FALSE); ?>"><?php echo t('Themes', FALSE); ?></a>
                    <form method="POST" class="none" action="<?php echo BASE_PATH; ?>admin/action" target="parsithemes_iframe">
                        <input name="action" value="getViewConfigThemes" type="hidden">
                        <input name="popup" value="yes" type="hidden">
                    </form>
                </li>   
                <li><a href="#" onclick="$(this).next('form').trigger('submit');return false;" title="<?php echo t('Db Modeling', FALSE); ?>"><?php echo t('DB', FALSE); ?></a>        
                    <form method="POST" class="none" action="<?php echo BASE_PATH; ?>admin/plump" target="_blank"></form>
                </li>

            <?php endif; ?>
            <li class="subMenu"><a href="#" id="info_themetype" title="<?php echo t('Version', FALSE); ?>" style="text-transform: capitalize"><?php echo t('Version', FALSE); ?> <?php echo str_replace('theme', '', THEMETYPE); ?></a>
                <ul>
                    <?php foreach (\app::$config['devices'] AS $device => $regex): ?>
                        <li>
                            <a href="#" onclick="ParsimonyAdmin.changeDevice('<?php echo $device; ?>'); return false;">
                                <?php echo ucfirst($device); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>  
        </ul>
        
        <div class="toolbarbonus floatleft">
            <img src="<?php echo BASE_PATH . 'admin/img/resolution.png'; ?>"/> 
            <select id="changeres" style="width: 70px;">
            </select> 
            <script>
                var resultions = new Array();
<?php
foreach (\app::$config['devices'] AS $deviceName => $device) {
    echo 'resultions["' . $deviceName . '"] = \'' . json_encode($device['resolution']) . '\';' . PHP_EOL;
}
?>
            </script>
            <img src="<?php echo BASE_PATH . 'admin/img/landscape-portrait.png'; ?>"/>
            <select id="changeorientation" style="width: 70px;">
                <option value="portrait"><?php echo t('Portrait', FALSE); ?></option><option value="landscape"><?php echo t('Landscape', FALSE); ?></option>
            </select>
             <?php if (ID_ROLE == 1): ?>
            <span style="padding: 0 5px;"><?php echo t('Mode'); ?></span>
                <select id="mode">
                    <option value="development"><?php echo t('Development',false); ?></option>
                    <option value="webmaster"><?php echo t('Webmaster',false); ?></option>
                </select>
            <?php endif; ?>
            
        </div>      
    </div>
    <?php if (ID_ROLE == 1): ?>
    <div id="right_sidebar" class="sidebar<?php if (isset($_COOKIE['rightToolbarOpen']) && $_COOKIE['rightToolbarOpen'] == 0)
    echo ' close'; ?>"<?php if (isset($_COOKIE['rightToolbarCoordX']) && $_COOKIE['rightToolbarCoordX'] != 0)
        echo ' style="left:' . $_COOKIE['rightToolbarCoordX'] . ';top:' . $_COOKIE['rightToolbarCoordY'] . ';"'; ?>>
        <div class="subSidebar">         
            <div class="subSidebarOnglet handle" style="cursor: move; display: block; " title="<?php echo t('Move', FALSE); ?>"><span class="ui-icon ui-icon-arrow-4"></span></div>
            <div class="subSidebarOnglet revert" style="cursor: default; display: block; " title="<?php echo t('Return', FALSE); ?>"><span class="ui-icon ui-icon-seek-next"></span></div>
            <div class="subSidebarOnglet" id="openrightslide" title="<?php echo t('Slide', FALSE); ?>"><span class="ui-icon ui-icon-circle-arrow-e"></span></div>
            <div class="subSidebarOnglet admdesign" title="<?php echo t('Tree', FALSE); ?>"></div>
            <div class="subSidebarOnglet switchtab" title="<?php echo t('CSS', FALSE); ?>"></div>
            <div class="subSidebarOnglet" id="csspicker"><img src="<?php echo BASE_PATH . 'admin/img/picker.png'; ?>" title="<?php echo t('CSSPicker', FALSE); ?>"/></div>
        </div>
        <div class="contenttab">
            <div>
                <div class="mainTab switchtab ellipsis<?php
         if (isset($_COOKIE['rightToolbarPanel']) && $_COOKIE['rightToolbarPanel'] != 'paneltree') {
             echo ' active';
         }
?>"><?php echo t('CSS', FALSE); ?></div>
                <div class="mainTab admdesign ellipsis<?php
                     if (isset($_COOKIE['rightToolbarPanel']) && $_COOKIE['rightToolbarPanel'] == 'paneltree') {
                         echo ' active';
                     }
?>"><?php echo t('Tree', FALSE); ?></div>
            </div>  

            <div id="paneltree"<?php if (isset($_COOKIE['rightToolbarPanel']) && $_COOKIE['rightToolbarPanel'] != 'paneltree')
                         echo 'class="none"'; ?>>
                <div class="titleTab ellipsis"><span style="letter-spacing: 1.1px;"><?php echo t('Tree', FALSE); ?></span><span id="treelegend" style="position: absolute;right: 6px;top: 1px;color: #444;font-weight: bold;padding-right: 10px;">?</span></div>
                <div class="none" id="treelegend2"><fieldset style="text-shadow:none;color:white;">
                        <legend><?php echo t('Type of blocks', FALSE); ?></legend>
                        <span class="parsicontainer" style="padding-left: 30px;position: relative;left: 5px;"><?php echo t('Block Container', FALSE); ?></span> </br>
                        <span class="parsiblock" style="padding-left: 39px;position: relative;left: -3px;"><?php echo t('Content Block', FALSE); ?></span></br>
                        <span class="parsipage" style="padding-left: 37px;position: relative;left: -1px;"><?php echo t('Dynamic Page', FALSE); ?></span></br>
                    </fieldset>
                </div>  
                <div id="config_tree_selector" class="none">
                    <span draggable="true" class="floatleft move_block ui-icon ui-icon-arrow-4"></span>
                    <span class="floatleft ui-icon ui-icon-wrench action" rel="getViewConfigBlock" title="<?php echo t('Configuration', FALSE); ?>"></span>
                    <span class="ui-icon ui-icon-pencil cssblock floatleft"></span>
                    <span class="ui-icon ui-icon-closethick config_destroy floatleft"></span>
                </div>
                <div id="tree"></div>
            </div> 
            <div id="panelcss" class="<?php if (!isset($_COOKIE['rightToolbarPanel']) || $_COOKIE['rightToolbarPanel'] != 'panelcss')
                     echo 'none '; ?>">
                     <?php
                     include('modules/admin/views/web/manageCSS.php');
                     /*
                       ?>
                       <a href="#" onclick="ParsimonyAdmin.currentWindow.attr('draggable','true');ParsimonyAdmin.currentWindow.prepend('<style>' + $('#test').html() + '</style>');return false;" style="font-size:2px">Test</a>
                       <div class="none" id="test">
                       body{-webkit-transform-origin: center center;}
                       html *{
                       -webkit-transform-style: preserve-3d;
                       -webkit-transform: translateZ(20px);
                       outline:1px solid #bbb;
                       }</div>
                       <div class="none">
                       Rotate : <input type="range" class="ch" id="rotate" min=0 max=360>
                       RotateX : <input type="range" class="ch" id="rotatex" min=0 max=360>
                       RotateY : <input type="range" class="ch" id="rotatey" min=0 max=360>
                       perspective : <input type="range" class="ch" id="perspective" min=-2000 max=2000>
                       TranslateZ : <input type="range" class="ch" id="translatez" min=-2000 max=2000>
                       </div>
                       <?php
                      */
                     ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div id="left_sidebar" class="sidebar<?php if (isset($_COOKIE['leftToolbarOpen']) && $_COOKIE['leftToolbarOpen'] == 0)
                         echo ' close'; ?>"<?php if (isset($_COOKIE['leftToolbarCoordX']) && $_COOKIE['leftToolbarCoordX'] != 0)
        echo ' style="left:' . $_COOKIE['leftToolbarCoordX'] . ';top:' . $_COOKIE['leftToolbarCoordY'] . ';"'; ?>>
        <div class="subSidebar">
            <div class="subSidebarOnglet handle" style="cursor: move; display: block;" title="<?php echo t('Move', FALSE); ?>"><span class="ui-icon ui-icon-arrow-4"></span></div>
            <div class="subSidebarOnglet revert" style="cursor: default; display: block;" title="<?php echo t('Return', FALSE); ?>"><span class="ui-icon ui-icon-seek-prev"></span></div>
            <div class="subSidebarOnglet" id="openleftslide"><span class="ui-icon ui-icon-circle-arrow-w" title="<?php echo t('Slide', FALSE); ?>"></span></div>
            <div class="subSidebarOnglet admmodules imgmodule" title="<?php echo t('Module', FALSE); ?>"></div>
            <div class="subSidebarOnglet admtabblocks imgblock" title="<?php echo t('Block', FALSE); ?>"></div>
        </div>
        <div class="contenttab cs">
            <?php if (ID_ROLE == 1): ?>
            <div>
                <div class="mainTab admtabblocks ellipsis<?php
         if (isset($_COOKIE['leftToolbarPanel']) && $_COOKIE['leftToolbarPanel'] == 'modulesblocks') {
             echo ' active';
         }
                     ?>"><span style="margin: 0px -16px 0px 0px;background: url(/admin/img/bloc.png) no-repeat;" class="ui-icon floatleft"></span><?php echo t('Blocks', FALSE); ?>
                   <?php /* <a href="#" title="<?php echo t('Download a new block', FALSE); ?>" id="add-block" class="action parsiplusone" rel="getViewAddBlock"></a> */ ?>
                </div>
                <div class="mainTab admmodules ellipsis<?php
                     if (isset($_COOKIE['leftToolbarPanel']) && $_COOKIE['leftToolbarPanel'] != 'modulesblocks') {
                         echo ' active';
                     }
                     ?>"><span style="margin: 0px -16px 0px 0px;background: url(/admin/img/module.png) no-repeat 2px;" class="ui-icon floatleft"></span><?php echo t('Modules', FALSE); ?><a href="#" title="<?php echo t('Add a Module', FALSE); ?>" id="add-module" class="action parsiplusone" rel="getViewAddModule"></a></div>
            </div>
            <?php endif; ?>
            <div id="modulespages">
                <?php
                $activeModules = \app::$activeModules;
                $activeModules = array_flip($activeModules);
                unset($activeModules[MODULE]);
                $activeModules = array_flip($activeModules);
                array_unshift($activeModules, MODULE);
                foreach ($activeModules as $id => $module) {
                    $moduleobj = app::getModule($module);
                    if (is_file('modules/' . $module . '/icon.png'))
                        $icon = 'background:url(' . BASE_PATH . $module . '/icon.png)';
                    else
                        $icon = 'background:url(admin/img/module.png) no-repeat';
                    $adminHTML = $moduleobj->displayAdmin();
                    if ($adminHTML == FALSE)
                        $htmlConfig = '';
                    else
                        $htmlConfig = '<div class="action" style="margin:3px; line-height:0;" rel="getViewModuleAdmin" params="module=' . $moduleobj->getName() . '" title="' . t('Administration Module', FALSE) . ' ' . ucfirst($moduleobj->getName()) . '"><img style="position: absolute;right: 8px;top: 4px;" src="' . BASE_PATH . 'admin/img/config.png"/></div>';
                    if ($module != 'admin')
                        echo '<div class="titleTab ellipsis"><span style="margin: 7px 7px 0px 7px;' . $icon . '" class="ui-icon floatleft"></span> ' . ucfirst($module) . $htmlConfig . '</div>';
                    $display = '';
                    if ($module != MODULE)
                        $display = 'none';
                    ?>  
                    <div id="page_<?php echo $moduleobj->getName(); ?>" class="<?php echo $display; ?>">
                        <div class="modules<?php
                if (isset($_COOKIE['leftToolbarPanel']) && $_COOKIE['leftToolbarPanel'] == 'modulesblocks') {
                    echo ' none';
                }
                    ?>">
                            <div rel="pages" class="ssTab ellipsis active" title="<?php echo t('Pages in', FALSE) . ' ' . ucfirst($moduleobj->getName()); ?>" target="_blank"><?php echo t('Page', FALSE); ?><a href="#" class="action" params="module=<?php echo $moduleobj->getName() ?>&amp;page=new"  rel="getViewUpdatePage" title="<?php echo t('Add A Page in', FALSE) . ' ' . ucfirst($moduleobj->getName()); ?>"><span style="width: 16px;height: 16px;position: absolute;right: 3px;top: 6px;border: #666 solid 1px;border-radius: 5px;float: right;cursor: alias;line-height: 14px;">+</span></a></div>
                            <div rel="models" class="ssTab db ellipsis" target="_blank" title="<?php echo t('Database', FALSE) . ' ' . ucfirst($moduleobj->getName()); ?>"><?php echo ' ' . t('Data', FALSE); ?>
                                <?php if (ID_ROLE == 1): ?>
                                <span class="dbdesigner ui-icon ui-icon-extlink" title="<?php echo t('Database Designer', FALSE) . ' ' . ucfirst($moduleobj->getName()); ?>" onclick="$(this).next('form').trigger('submit');return false;"></span>
                                <form method="POST" class="none" action="<?php echo BASE_PATH; ?>admin/plump" target="_blank">
                                    <input type="hidden" name="module" value="<?php echo $module; ?>">
                                </form>
                                <?php endif; ?>
                            </div>        
                            <ul class="none models">
                                <?php
                                $models = $moduleobj->getModel();
                                if (count($models) > 0) {
                                    foreach ($moduleobj->getModel() as $entity) {
                                        $entityName = $entity->getName();
                                        if ($module != 'core' && ($entityName != 'role' || $entityName != 'user')) {
                                            ?>
                                            <li class="sublist" style="background: url(/admin/img/db_small.png) no-repeat 3px 3px;padding-left: 25px;"><a href="#" class="modeleajout ellipsis" rel="<?php echo $module . ' - ' . $entityName; ?>" title="<?php ucfirst($entityName); ?>"><?php echo ucfirst($entityName); ?></a></li>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </ul>
                            <ul class="pages">
                                <?php
                                if (file_exists(PROFILE_PATH . $moduleobj->getName() . '/module' . '.' . \app::$config['dev']['serialization'])) {
                                    foreach ($moduleobj->getPages() as $id_page => $page) {
                                        if ($id_page == \app::$request->page->getId() && empty($display))
                                            $selected = ' selected';
                                        else
                                            $selected = '';
                                        ?>
                                        <li class="sublist ellipsis <?php echo $selected ?>"><span class="ui-icon ui-icon-document floatleft"></span>
                                            <?php
                                            if ($moduleobj->getName() == 'core')
                                                $pageURL = BASE_PATH . $page->getURL();
                                            else
                                                $pageURL = BASE_PATH . $moduleobj->getName() . '/' . $page->getURL();
                                            ?>
                                            <a class="ellipsis" onclick="ParsimonyAdmin.goToPage('<?php echo $page->getTitle(); ?>', '<?php echo $pageURL; ?>');"
                                                href="#" ><?php echo ucfirst(htmlentities($page->getTitle())); ?></a>
                                            <span class="action ui-icon ui-icon-pencil" style="right: 5px;top: 2px;position: absolute;border: #666 solid 1px;border-radius: 5px;cursor: pointer;" rel="getViewUpdatePage" title="<?php echo t('Manage this page', FALSE); ?>" params="module=<?php echo $moduleobj->getName(); ?>&page=<?php echo $id_page; ?>"></span>
                                            
                                        </li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                        <ul class="blocks <?php
                            if (!isset($_COOKIE['leftToolbarPanel']) || (isset($_COOKIE['leftToolbarPanel']) && $_COOKIE['leftToolbarPanel'] == 'modulespages')) {
                                echo ' none';
                            }
                                ?>">
                                <?php
                                if (file_exists('modules/' . $moduleobj->getName() . '/blocks')) {
                                    if ($moduleobj->getName() != 'admin') {
                                        $blocklist = glob('modules/' . $moduleobj->getName() . '/blocks/*/block.php');
                                        if (count($blocklist) > 1)
                                            echo'<div id="blocks_' . $moduleobj->getName() . '" style="padding:0px;">';
                                        foreach ($blocklist as $path) {
                                            $blockName = substr(strrchr(substr($path, 0, -10), '/block.php'), 1);
                                            if ($blockName !== 'error404' && $blockName !== 'page') {
                                                $blockClassName = $moduleobj->getName() . '\blocks\\' . $blockName;
                                                $obj = new ReflectionClass('\\' . $blockClassName);
                                                $props = $obj->getDefaultProperties();
                                                if (!$obj->hasProperty('allowedTypes') || ($obj->hasProperty('allowedTypes') && in_array(THEMETYPE, $props['allowedTypes']))) {
                                                    echo '<div class="admin_core_block tooltip" data-tooltip="' . ucfirst($blockName) . '" draggable="true" id="' . $blockClassName . '" style="float:left;position:relative;background:url(' . BASE_PATH . 'modules/' . $moduleobj->getName() . '/blocks/' . $blockName . '/img.gif) center center;" title="' . ucfirst($blockName) . '"></div>';
                                                }
                                            }
                                        }
                                    }
                                }
                                ?>
                        </ul>						
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    <div id="footer_sidebar" class="none" style="bottom: 0px;position:fixed;width: 100%;z-index: 999999;height: 180px; ">
        <iframe name="parsithemes_iframe" id="parsithemes_iframe" src="" style="width: 100%;height: 180px;background: #EEE url(/admin/img/concrete_wall_3.png) "></iframe>
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
        <div id="dialog" style="display:none">
            <div style="text-align: center;padding-top: 20px;"><input type="text" id="dialog-id" /></div>
            <div style="text-align: center;padding: 15px 0px;"><input type="button" id="dialog-ok" value="<?php echo t("Add", FALSE) ?>" /><input type="button" onclick="ParsimonyAdmin.closeConfBox()" value="<?php echo t("Cancel", FALSE) ?>" /></div>
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
<div id="parsimonyTooltip">
    <div class="tri"></div>
    <div class="parsimonyTooltipContent"></div>
</div>
<div class="align_center" style="min-height: 1000px;">
    <?php
    if (strstr($_SERVER['REQUEST_URI'], '?') != FALSE)
        $frameUrl = $_SERVER['REQUEST_URI'];
    else
        $frameUrl = $_SERVER['REQUEST_URI'] . '?parsiframe=ok';
    ?>
    <iframe id="parsiframe" src="<?php echo $frameUrl; ?>" align="middle" style="text-align:center;width:100%;height:9000px;background: #FFF;margin-top: 28px !important;"></iframe>
</div>
