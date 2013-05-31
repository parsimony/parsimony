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
 * @authors Julien Gras et BenoÃ®t Lorillot
 * @copyright  Julien Gras et BenoÃ®t Lorillot
 * @version  Release: 1.0
 * @category  Parsimony
 * @package admin
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

app::$request->page->addJSFile('admin/blocks/tree/script.js','footer');
?>
<div id="treelegend">?</div>
<div class="none" id="treelegend2"><fieldset style="text-shadow:none;color:white;">
	<legend><?php echo t('Type of blocks', FALSE); ?></legend>
	<span class="parsicontainer" style="padding-left: 30px;position: relative;left: 5px;"><?php echo t('Block Container', FALSE); ?></span> </br>
	<span class="parsimonyblock" style="padding-left: 39px;position: relative;left: -3px;"><?php echo t('Content Block', FALSE); ?></span></br>
	<span class="parsipage" style="padding-left: 37px;position: relative;left: -1px;"><?php echo t('Dynamic Page', FALSE); ?></span></br>
    </fieldset>
</div>  
<div id="config_tree_selector" class="none">
    <span draggable="true" class="floatleft move_block ui-icon ui-icon-arrow-4"></span>
    <span class="floatleft ui-icon ui-icon-wrench action" rel="getViewConfigBlock" data-action="onConfigure" title="<?php echo t('Configuration', FALSE); ?>"></span>
    <span class="ui-icon ui-icon-pencil cssblock floatleft" data-action="onDesign"></span>
    <span class="ui-icon ui-icon-trash config_destroy floatleft" data-action="onDelete"></span>
</div>
<div id="tree"> 
    <?php
    echo \app::getModule('admin')->structureTree(\theme::get(THEMEMODULE, THEME, THEMETYPE));
    ?>
</div>