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
?>
<div class="contenttab">
    <?php foreach ($this->blocks AS $block): ?>
        <div class="mainTab <?php echo $block->getId(); ?> <?php if($block->getConfig('cssClasses') != 'none') echo 'active'; ?>" rel="<?php echo $block->getId(); ?>">
	    <div class="icons sprite"></div>
	    <div class="content">
		<h2><?php echo t($block->getName(), FALSE); ?></h2>
		 <?php 
		    echo $block->display() . PHP_EOL;
		    ?>
	    </div>
        </div>
    <?php endforeach; ?>
	<div style="height:0;margin:10px 5px;border-top: 1px solid #0c0c0c;border-bottom: 1px solid #3c3c3c;"></div>
    <?php if($this->side == 'left'): ?>
		<div onclick="ParsimonyAdmin.displayExplorer();" data-title="<?php echo t('Files Explorer', FALSE); ?>" class="roundBTN creation tooltip sprite sprite-dir" data-tooltip="<?php echo t('Files Explorer', FALSE); ?>" data-pos="e"></div>
		<div onclick="$(this).next('form').trigger('submit');" data-title="<?php echo t('Db Modeling', FALSE); ?>" class="roundBTN creation tooltip sprite sprite-bdd" data-tooltip="<?php echo t('Db Modeling', FALSE); ?>" data-pos="e"></div>        
	        <form method="POST" class="none" action="<?php echo BASE_PATH; ?>admin/dbDesigner" target="_blank"></form>
   <?php endif; ?>
   <?php if($this->side == 'right'): ?>
        <div data-title="<?php echo t('CSS Picker', FALSE); ?>" class="roundBTN cssPickerBTN tooltip sprite sprite-csspicker" data-tooltip="<?php echo t('CSS Picker', FALSE); ?>" data-pos="w">
        </div>        
   <?php endif; ?>
</div>
<div onclick="$(this).parent().toggleClass('pin')" class="pinner"></div>
