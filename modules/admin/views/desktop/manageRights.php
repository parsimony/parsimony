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
$role = app::getModule('core')->getEntity('role');
?>
<style>    
    th,td{height: 23px;width: 87px;}
    td{padding:4px 2px 7px 7px;text-align: center !important}
    .active{background:#AAA}  
    .modulename{font-size: 13px;color: #777;letter-spacing: 2px;}
    #enablemodule{margin-bottom: 10px;color: #464646;padding: 3px 7px;font-size: 14px;position: relative;letter-spacing: 2px;}
    .firsttd{font-size: 18px;letter-spacing: 2px;vertical-align: middle}
    .secondtd{text-transform: capitalize;color: #444;font-size: 12px;text-align:left !important}
</style>
<div class="adminzone" id="admin_rights">
    <div class="adminzonemenu">
        <?php
        $class = ' firstpanel';
        foreach ($role->select() as $key => $line) {
            echo '<div class="adminzonetab' . $class . '"><a href="#tabs-' . $line->name . '" class="ellipsis">' . ucfirst($line->name) . '</a></div>';
            $class = '';
        }
        ?>
    </div>
    <div class="adminzonecontent">
        <form action="" method="POST" target="ajaxhack">
            <input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />
            <?php foreach ($role->select() as $key => $line) : ?>
                <div id="tabs-<?php echo $line->name; ?>" class="admintabs">
                    <div>
                        <h2 style="letter-spacing: 1.5px;color: #555;text-transform: capitalize;margin-left: 10px;"><?php echo t('%s role', array($line->name)); ?></h2>
                        <table style="margin-left: 10px">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th style="width: 120px;">Anonymous<span class="tooltip ui-icon ui-icon-info floatright" data-tooltip="<?php echo t('The only right of reading content and in some offline cases<br> to add, delete or modify his own content') ;?>"></span></th>
                                    <th style="width: 120px;">Editor<span class="tooltip ui-icon ui-icon-info floatright" data-tooltip="<?php echo t('The right of editing content & pages') ;?>"></span></th>
                                    <th style="width: 120px;">Developer<span class="tooltip ui-icon ui-icon-info floatright" data-tooltip="<?php echo t('All web development rights : Design, Module, blocks, database & so on') ;?>"></span></th></tr>
                            </thead>
                            <tbody>
                                <tr>  
                                    <td style="width: 160px;">Status of <span style="text-transform: capitalize"><?php echo $line->name; ?></span></td>
                                    <td style="height: 40px;"><input type="radio" name="type[<?php echo $line->id_role; ?>]" value="0" <?php if($line->state == "0") echo 'checked="checked"';  ?> /></td>
                                    <td style="height: 40px;"><input type="radio" name="type[<?php echo $line->id_role; ?>]" value="1" <?php if($line->state == "1") echo 'checked="checked"';  ?> /></td>
                                    <td style="height: 40px;"><input type="radio" name="type[<?php echo $line->id_role; ?>]" value="2" <?php if($line->state == "2") echo 'checked="checked"';  ?> /></td>
                                </tr>
                            </tbody>
                        </table>
                       <br>
                       </div>
                   
                       <div style="clear:both"></div> 
                       <fieldset style="background: #F9F9F9;border: 1px solid #CCC;margin: 10px;border-radius: 8px;padding-bottom: 10px;">
                           <legend style="display: block;text-transform: capitalize;margin: 4px 7px 0px 5px;color: #464646;padding: 3px 7px;font-size: 14px;border: 1px solid #DFDFDF;border-radius: 5px;background-color: #F1F1F1;
background-image: -ms-linear-gradient(top,#F9F9F9,#ECECEC);background-image: -moz-linear-gradient(top,#F9F9F9,#ECECEC);background-image: -o-linear-gradient(top,#F9F9F9,#ECECEC);background-image: -webkit-gradient(linear,left top,left bottom,from(#F9F9F9),to(#ECECEC));background-image: -webkit-linear-gradient(top,#F9F9F9,#ECECEC);
background-image: linear-gradient(top,#F9F9F9,#ECECEC);"><label class="modulename"><?php echo t('Module', FALSE); ?> :</label>
                        <select name="module" onchange="$(this).closest('.admintabs').find('.rightbox').hide();$('#rights-<?php echo $line->name ?>-' + this.value).show()">
                            <?php
                            $modules = \app::$activeModules;
                            unset($modules['admin']);
                            foreach ($modules as $moduleName => $type) {
                                echo '<option value="' . $moduleName . '">' . $moduleName . '</option>';
                            }
                            ?>
                        </select>
                    </legend>
                    <?php
                    foreach (\app::$activeModules as $moduleName => $type) {
                        echo '<div id="rights-' . $line->name . '-' . $moduleName . '" class="rightbox';
                        if ($moduleName != 'core')
                            echo ' none';
                        echo '">';
                        ?>
                           <div id="enablemodule"><label><?php echo t('Enable the %s module for %s role', array(ucfirst($moduleName), $line->name)) ;?> ?</label><input type="hidden" name="modulerights[<?php echo $line->id_role; ?>][<?php echo $moduleName; ?>]" value="0">
                            <input type="checkbox" name="modulerights[<?php echo $line->id_role; ?>][<?php echo $moduleName; ?>]" <?php if (\app::getModule($moduleName)->getRights($line->id_role)) echo 'checked'; ?>></div>
                        
                    <table style="margin: 0 auto">
                            <thead>
                                <tr><th></th><th><?php echo t('Name', FALSE); ?></th><th><?php echo t('Display', FALSE); ?></th><th><?php echo t('Insert', FALSE); ?></th><th><?php echo t('Update', FALSE); ?></th><th><?php echo t('Delete', FALSE); ?></th></tr>
                            </thead>
                            <tbody>
                                <?php
                                $module = app::getModule($moduleName);
                                $count = 0;
                                $models = $module->getModel();
                                $nbmodels = count($models);
                                foreach ($models as $modelName => $model) {
                                    $count++;
                                    $myModel = $module->getEntity($modelName);
                                    if ($myModel->getRights($line->id_role) & DISPLAY)
                                        $displayChecked = 'checked="checked"';
                                    else
                                        $displayChecked = '';
                                    if ($myModel->getRights($line->id_role) & INSERT)
                                        $insertChecked = 'checked="checked"';
                                    else
                                        $insertChecked = '';
                                    if ($myModel->getRights($line->id_role) & UPDATE)
                                        $updateChecked = 'checked="checked"';
                                    else
                                        $updateChecked = '';
                                    if ($myModel->getRights($line->id_role) & DELETE)
                                        $deleteChecked = 'checked="checked"';
                                    else
                                        $deleteChecked = '';
                                    ?>
                                    <tr class="line">
                                        <?php
                                        if ($count == 1)
                                            echo '<td rowspan="' . $nbmodels . '" class="firsttd" valign="middle">Models</td>';
                                        echo '<td class="secondtd">' . $modelName . '</td>
                                    <td><div><input type="hidden" name="modelsrights[' . $line->id_role . '][' . $moduleName . '][' . $modelName . '][display]" value="0"><input type="checkbox" name="modelsrights[' . $line->id_role . '][' . $moduleName . '][' . $modelName . '][display]" class="display" ' . $displayChecked . '></div></td>
                                        <td><div><input type="hidden" name="modelsrights[' . $line->id_role . '][' . $moduleName . '][' . $modelName . '][insert]" value="0"><input type="checkbox" name="modelsrights[' . $line->id_role . '][' . $moduleName . '][' . $modelName . '][insert]" class="insert" ' . $insertChecked . '></div></td>
                                            <td><div><input type="hidden" name="modelsrights[' . $line->id_role . '][' . $moduleName . '][' . $modelName . '][update]" value="0"><input type="checkbox" name="modelsrights[' . $line->id_role . '][' . $moduleName . '][' . $modelName . '][update]" class="update" ' . $updateChecked . '></div></td>
                                                <td><div><input type="hidden" name="modelsrights[' . $line->id_role . '][' . $moduleName . '][' . $modelName . '][delete]" value="0"><input type="checkbox" name="modelsrights[' . $line->id_role . '][' . $moduleName . '][' . $modelName . '][delete]" class="delete" ' . $deleteChecked . '></div></td>';
                                    }
                                    ?>
                                </tr>
                                <?php
                                $count = 0;
                                foreach ($module->getPages() as $id_page => $page) {
                                    $displayChecked = '';
                                    if ($page->getRights($line->id_role) & DISPLAY)
                                        $displayChecked = 'checked="checked"';
                                    $count++;
                                    echo '<tr class="line">';
                                    if ($count == 1)
                                        echo '<td rowspan="30" class="firsttd" valign="middle">Pages</td>';
                                    echo '<td class="secondtd" style="width:200px;">' . s($page->getTitle()) . '</td>';
                                    echo '<td><div><input type="hidden" name="pagesrights[' . $line->id_role . '][' . $moduleName . '][' . $page->getId() . '][display]" value="0"><input type="checkbox" name="pagesrights[' . $line->id_role . '][' . $moduleName . '][' . $page->getId() . '][display]" class="display" ' . $displayChecked . '></div></td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                        <br>
                        <div class="clearboth">
                        
                        <?php /*
                        <table style="width:100%">
                            <thead>
                                <tr><th></th><th><?php echo t('Name', FALSE); ?></th><th><?php echo t('Configuration', FALSE); ?></th></tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 0;
                                $blocklist = glob('modules/' . $module->getName() . '/blocks/*//*block.php');
                                foreach ($blocklist as $path) {
                                    $count++;
                                    $blockName = substr(strrchr(substr($path, 0, -10), '/block.php'), 1);
                                    echo '<tr class="line">';
                                    if ($count == 1)
                                        echo '<td rowspan="30" class="firsttd" valign="middle">Block</td>';
                                    echo '<td class="secondtd" style="width:200px;">' . s($blockName) . '</td>';
                                    echo '<td><div><input type="hidden" name="blockrights[' . $line->id_role . '][' . $moduleName . '][' . $page->getId() . '][display]" value="0"><input type="checkbox" name="blockrights[' . $line->id_role . '][' . $moduleName . '][' . $page->getId() . '][display]" class="display" ' . $displayChecked . '></div></td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>*/ ?>
                    </div>
                    <?php
                }
                ?>
        </fieldset><br><br></div>
        <?php
    endforeach;
    ?>
    <br>
    <input type="hidden" name="action" value="saveRights">
    </form>
</div>
    <div class="adminzonefooter">
	<div id="save_page" class="save ellipsis" onclick="$('form').trigger('submit');return false;"><?php echo t('Save', FALSE); ?></div>
    </div>
</div>
