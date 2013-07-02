<?php
/**
 * Parsimony
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@parsimony-cms.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Parsimony to newer
 * versions in the future. If you wish to customize Parsimony for your
 * needs please refer to http://www.parsimony.mobi for more information.
 *
 * @authors Julien Gras et Benoît Lorillot
 * @copyright  Julien Gras et Benoît Lorillot
 * @version  Release: 1.0
 * @category  Parsimony
 * @package admin
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

app::$request->page->addJSFile('admin/blocks/modules/script.js','footer');
?>
<div id="modulespages">
    <?php
    $activeModule = \app::$config['modules']['active'];
    unset($activeModule[MODULE]);
    $activeModule = array_merge(array(MODULE => '1'), \app::$config['modules']['active']);
    foreach ($activeModule as $module => $type) {
        $moduleobj = \app::getModule($module);
        $moduleInfos = \tools::getClassInfos($moduleobj);
        if(!isset($moduleInfos['displayAdmin']) || $moduleInfos['displayAdmin'] == 4 ){
            $icon = '';
            if (is_file('modules/' . $module . '/icon.png'))
                $icon = 'background:url(' . BASE_PATH . $module . '/icon.png)';
            $adminHTML = $moduleobj->displayAdmin();
            if ($adminHTML == FALSE)
                $htmlConfig = '';
            else
                $htmlConfig = '<div class="action floatright" style="margin:3px; line-height:0;" rel="getViewModuleAdmin" params="module=' . $moduleobj->getName() . '" title="' . t('Administration Module', FALSE) . ' ' . ucfirst(s($moduleInfos['title'])) . '"><img src="' . BASE_PATH . 'admin/img/config.png"/></div>';
            if ($module != 'core' && $module != 'admin')
                echo '<div class="titleTab ellipsis"><span style="margin: 5px 7px 0px 7px;' . $icon . '" class="sprite sprite-module floatleft"></span> ' . ucfirst(s($moduleInfos['title'])) . $htmlConfig . '</div>';
            $display = '';
            if ($module != MODULE)
                $display = 'none';
            ?>  
            <div id="page_<?php echo $moduleobj->getName(); ?>" class="<?php echo $display; ?>" style="border-bottom: 1px solid rgb(233, 233, 233);border-top: 1px solid white;">
                <div class="datatopages subTabsContainer">
                    <div rel="pages" class="ssTab ellipsis switchtodata active" title="<?php echo t('Pages in', FALSE) . ' ' . ucfirst($moduleobj->getName()); ?>"><?php echo t('Pages', FALSE); ?>                  
                    </div>
                    <div rel="models" class="ssTab db ellipsis switchtopages" title="<?php echo t('Content', FALSE) . ' ' . ucfirst($moduleobj->getName()); ?>"><?php echo ' ' . t('Content', FALSE); ?>
                    </div> 
                </div>
                <ul class="none models tabPanel">
                    <?php
                    $models = $moduleobj->getModel();
                    if (count($models) > 0) {
                        foreach ($moduleobj->getModel() as $entity) {
                            $entityName = $entity->getName();
                            $entityTitle = s(ucfirst($entity->getTitle()));
                            if ($module != 'core' || !empty($entityTitle)) {
                                ?>
                                <li class="sublist modelSubList"><a href="#" class="modeleajout ellipsis" rel="<?php echo $module . ' - ' . $entityName; ?>" title="<?php echo $entityTitle; ?>"><?php echo $entityTitle; ?></a></li>
                                <?php
                            }
                        }
                    }
                    if ($_SESSION['behavior'] == 2 ):
                    ?>
                    <li class="sublist" style="padding-left: 25px;">
                        <span class="dbdesigner ui-icon ui-icon-extlink"></span>
                        <a href="#" title="<?php echo t('Database Designer', FALSE) . ' ' . ucfirst($moduleobj->getName()); ?>" onclick="$(this).next('form').trigger('submit');return false;"><?php echo t('Database Designer', FALSE) ?></a>
                        <form method="POST" class="none" action="<?php echo BASE_PATH; ?>admin/dbDesigner" target="_blank">
                            <input type="hidden" name="module" value="<?php echo $module; ?>">
                        </form>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="pages tabPanel" data-module="<?php echo $moduleobj->getName(); ?>" style="display:block;max-height: 300px;overflow-y: auto">
                    <?php
                    if (stream_resolve_include_path($moduleobj->getName() . '/module' . '.' . \app::$config['dev']['serialization'])) {
                        foreach ($moduleobj->getPages() as $id_page => $page) {
                            if ($id_page == \app::$request->page->getId() && empty($display))
                                $selected = ' selected';
                            else
                                $selected = '';

                            if ($moduleobj->getName() == \app::$config['modules']['default'])
                                $pageURL = BASE_PATH . $page->getURL();
                            else
                                $pageURL = BASE_PATH . $moduleobj->getName() . '/' . $page->getURL();
                            ?>
                            <li class="sublist ellipsis <?php echo $selected ?>" draggable="true" id="page_<?php echo $id_page ?>" data-url="<?php echo $pageURL ?>"><span class="ui-icon ui-icon-document floatleft"></span>
                                <a class="ellipsis" onclick="ParsimonyAdmin.goToPage('<?php echo str_replace("'", "\\'", $page->getTitle()); ?>', '<?php echo $pageURL; ?>');return false;"
                                   href="#" ><?php echo ucfirst(s($page->getTitle())); ?></a>
                                <span class="action ui-icon ui-icon-pencil" style="right: 5px;top: 2px;position: absolute;border: #666 solid 1px;border-radius: 5px;cursor: pointer;" rel="getViewUpdatePage" title="<?php echo t('Manage this page', FALSE); ?>" params="module=<?php echo $moduleobj->getName(); ?>&page=<?php echo $id_page; ?>"></span>

                            </li>
                            <?php
                        }
                    }
                    ?>
                    <li class="sublist ellipsis">
                        <a href="#" style="text-decoration: none;display:block" class="action ellipsis" params="module=<?php echo $moduleobj->getName() ?>&amp;page=new"  rel="getViewUpdatePage" title="<?php echo t('Add A Page in', FALSE) . ' ' . ucfirst($moduleobj->getName()); ?>">
                            <span class="ui-icon ui-icon-plus" style="position: relative;top: 2px;float: left;"></span>
                            <?php echo t('Add A Page', FALSE); ?></a>
                    </li>
                </ul>
            </div>

            <?php
            }
    }
    if ($_SESSION['behavior'] == 2 ):
    ?>		
    <div class="titleTab ellipsis" style="padding-left: 31px;"><span class="ui-icon ui-icon-plus" style="top: 5px;  left: 6px;  position: absolute;"></span></span><a href="#" style="color: #444;text-decoration: none" title="<?php echo t('Add a Module', FALSE); ?>" id="add-module" class="action" rel="getViewAddModule"><?php echo t('Add a Module', FALSE); ?></a></div>
    <?php endif; ?>
</div>
<script>
    $(document).ready(function(){
	$('.pages').on('dragstart',"li",function(e){
	    $("#conf_box_overlay").css({"opacity":0,"z-index":0, "display":"block"});
	    var evt = e.originalEvent;
	    evt.dataTransfer.effectAllowed = 'move';
	    evt.dataTransfer.setData("Parsimony/dragSidebar", "drag page"); /* Firefox fix */
	    var dragInfos = {elmt : this, list:this.parentNode};
	    dragInfos.width = isNaN(parseFloat(dragInfos.elmt.style.width)) ? 0 : dragInfos.elmt.style.width;
	    $('li',dragInfos.list).on('dragover.sortPages',dragInfos,function(e){
		if(dragInfos.elmt != this) this.parentNode.insertBefore(dragInfos.elmt, this);
		return false;
	    })
	    $(document).on('dragend.sortPages',dragInfos,function(){
		$("#conf_box_overlay").css({"z-index":999, "display":"none"});
		$(document).add('li',dragInfos.list).off('.sortPages');
		var list = Array();
		$('li',dragInfos.list).each(function(){
		    if(this.id) list.push(this.id);
		});
		$.post(BASE_PATH + "admin/reorderPages", {module: $(dragInfos.list).data("module"), order : list}, function(data) {
		    ParsimonyAdmin.notify(t("The changes have been saved"),"positive");
		});
	    });
	});
    });
</script>