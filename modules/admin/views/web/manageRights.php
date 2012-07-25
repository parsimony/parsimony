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
    #enablemodule{width: 300px; position: relative;top: -22px;left: 300px;font-size: 13px;color: #777;letter-spacing: 2px;}
    .firsttd{font-size: 18px;letter-spacing: 2px;vertical-align: middle}
    .secondtd{text-transform: capitalize;color: #444;font-size: 12px;text-align:left !important}
</style>
<div class="adminzone" id="admin_rights">
    <div class="adminzonemenu">
        <div class="save"><a href="#" onclick="$('form').trigger('submit');return false;" class="ellipsis"><?php echo t('Save', FALSE); ?></a></div>
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
                        <h2><?php echo $line->name; ?></h2>
                        <label for="type">Anonymous </label><input type="radio" name="type[<?php echo $line->id_role; ?>]" value="0" <?php if($line->state == "0") echo 'checked="checked"';  ?> />
                        <label for="type">Edit </label><input type="radio" name="type[<?php echo $line->id_role; ?>]" value="1" <?php if($line->state == "1") echo 'checked="checked"';  ?> />
                        <label for="type">Creation </label><input type="radio" name="type[<?php echo $line->id_role; ?>]" value="2" <?php if($line->state == "2") echo 'checked="checked"';  ?> />
                        <br><br>
                        <div style="clear:both"></div>
                        <label class="modulename"><?php echo t('Module', FALSE); ?> :</label>
                        <select name="module" onchange="$(this).closest('.admintabs').find('.rightbox').hide();$('#rights-<?php echo $line->name ?>-' + this.value).show()">
                            <?php
                            $modules = \app::$activeModules;
                            unset($modules['admin']);
                            foreach ($modules as $moduleName => $type) {
                                echo '<option value="' . $moduleName . '">' . $moduleName . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <?php
                    foreach (\app::$activeModules as $moduleName => $type) {
                        echo '<div id="rights-' . $line->name . '-' . $moduleName . '" class="rightbox';
                        if ($moduleName != 'core')
                            echo ' none';
                        echo '">';
                        ?>
                        <div id="enablemodule"><?php echo '<label>' . t('Enable', FALSE) . ' ' . ucfirst($moduleName); ?> ?</label><input type="hidden" name="modulerights[<?php echo $line->id_role; ?>][<?php echo $moduleName; ?>]" value="0">
                            <input type="checkbox" name="modulerights[<?php echo $line->id_role; ?>][<?php echo $moduleName; ?>]" <?php if (\app::getModule($moduleName)->getRights($line->id_role)) echo 'checked'; ?>></div>
                        <table>
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
        </div>
        <?php
    endforeach;
    ?>
    <br>
    <input type="hidden" name="action" value="saveRights">
    </form>
</div>
</div>
