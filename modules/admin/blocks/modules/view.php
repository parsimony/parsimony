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
 * to contact@parsimony.mobi so we can send you a copy immediately.
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
?>
<div id="modulespages">
    <?php
    $activeModule = \app::$activeModules;
    unset($activeModule[MODULE]);
    $activeModule = array_merge(array(MODULE => '1'), \app::$activeModules);
    foreach ($activeModule as $module => $type) {
        $moduleobj = \app::getModule($module);
        if (is_file('modules/' . $module . '/icon.png'))
            $icon = 'background:url(' . BASE_PATH . $module . '/icon.png)';
        else
            $icon = 'background:url(admin/img/module.png) no-repeat';
        $adminHTML = $moduleobj->displayAdmin();
        if ($adminHTML == FALSE)
            $htmlConfig = '';
        else
            $htmlConfig = '<div class="action floatright" style="margin:3px; line-height:0;" rel="getViewModuleAdmin" params="module=' . $moduleobj->getName() . '" title="' . t('Administration Module', FALSE) . ' ' . ucfirst($moduleobj->getTitle()) . '"><img src="' . BASE_PATH . 'admin/img/config.png"/></div>';
        if ($module != 'admin')
            echo '<div class="titleTab ellipsis"><span style="margin: 7px 7px 0px 7px;' . $icon . '" class="ui-icon floatleft"></span> ' . ucfirst($moduleobj->getTitle()) . $htmlConfig . '</div>';
        $display = '';
        if ($module != MODULE)
            $display = 'none';
        ?>  
        <div id="page_<?php echo $moduleobj->getName(); ?>" class="<?php echo $display; ?>">
            <div id="datatopages">
                <div id="switchtodata" rel="pages" class="ssTab ellipsis active" title="<?php echo t('Pages in', FALSE) . ' ' . ucfirst($moduleobj->getName()); ?>" target="_blank"><?php echo t('Pages', FALSE); ?>                  
                </div>
                <div id="switchtopages" rel="models" class="ssTab db ellipsis" target="_blank" title="<?php echo t('Database', FALSE) . ' ' . ucfirst($moduleobj->getName()); ?>"><?php echo ' ' . t('Data', FALSE); ?>
                </div> 
            </div>
            <ul class="none models">
                <?php
                $models = $moduleobj->getModel();
                if (count($models) > 0) {
                    foreach ($moduleobj->getModel() as $entity) {
                        $entityName = $entity->getName();
                        if ($module != 'core' || ($entityName != 'role' && $entityName != 'user')) {
                            ?>
                            <li class="sublist modelSubList"><a href="#" class="modeleajout ellipsis" rel="<?php echo $module . ' - ' . $entityName; ?>" title="<?php ucfirst($entityName); ?>"><?php echo str_replace('_', ' ', $entityName); ?></a></li>
                            <?php
                        }
                    }
                }
                ?>
                <li class="sublist" style="padding-left: 25px;">
                    <span class="dbdesigner ui-icon ui-icon-extlink"></span>
                    <a href="#" title="<?php echo t('Database Designer', FALSE) . ' ' . ucfirst($moduleobj->getName()); ?>" onclick="$(this).next('form').trigger('submit');return false;"><?php echo t('Database Designer', FALSE) ?></a>
                    <form method="POST" class="none" action="<?php echo BASE_PATH; ?>admin/dbDesigner" target="_blank">
                        <input type="hidden" name="module" value="<?php echo $module; ?>">
                    </form>
                </li>
            </ul>
            <ul class="pages" data-module="<?php echo $moduleobj->getName(); ?>">
                <?php
                if (file_exists(PROFILE_PATH . $moduleobj->getName() . '/module' . '.' . \app::$config['dev']['serialization'])) {
                    foreach ($moduleobj->getPages() as $id_page => $page) {
                        if ($id_page == \app::$request->page->getId() && empty($display))
                            $selected = ' selected';
                        else
                            $selected = '';

                        if ($moduleobj->getName() == 'core')
                            $pageURL = BASE_PATH . $page->getURL();
                        else
                            $pageURL = BASE_PATH . $moduleobj->getName() . '/' . $page->getURL();
                        ?>
                        <li class="sublist ellipsis <?php echo $selected ?>" id="page_<?php echo $id_page ?>" data-url="<?php echo $pageURL ?>"><span class="ui-icon ui-icon-document floatleft"></span>
                            <a class="ellipsis" onclick="ParsimonyAdmin.goToPage('<?php echo str_replace("'", "\\'", $page->getTitle()); ?>', '<?php echo utf8_encode($pageURL); ?>');return false;"
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
    ?>		
    <div class="titleTab ellipsis" style="padding-left: 31px;"><span class="ui-icon floatleft panelmodules" style="top: 5px;  left: 6px;  position: absolute;"></span></span><a href="#" style="color: white;text-decoration: none" title="<?php echo t('Add a Module', FALSE); ?>" id="add-module" class="action" rel="getViewAddModule"><?php echo t('Add a Module', FALSE); ?></a></div>
</div>
<script>
    $( ".pages" ).sortable({
        update: function(event, ui) {
            $.post(BASE_PATH + "admin/reorderPages", {module: $(this).data("module"), order : $( this ).sortable( "toArray")}, function(data) {
                ParsimonyAdmin.notify(t("The changes have been saved"),"positive");
            });
        }
    });
</script>