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
<div class="subSidebar">
    <div class="subSidebarOnglet handle" style="cursor: move;" draggable="true"><span class="ui-icon ui-icon-arrow-4"></span></div>
    <div class="subSidebarOnglet revert tooltip" data-tooltip="<?php echo t('Pin', FALSE); ?>"><span class="ui-icon ui-icon-seek-<?php if ($this->side == 'left') echo 'prev'; else echo 'next'; ?>"></span></div>
    <div class="subSidebarOnglet openclose tooltip" data-tooltip="<?php echo t('Show / Hide', FALSE); ?>"><span class="ui-icon ui-icon-circle-arrow-<?php if ($this->side == 'left') echo 'w'; else echo 'e'; ?>"></span></div>
    <div class="subSidebarOnglet tooltip resizeHandle" style="cursor: w-resize;" draggable="true" data-tooltip="<?php echo t('Resize', FALSE); ?>"><span class="ui-icon ui-icon-arrowthick-2-e-w ui-resizable-handle ui-resizable-<?php if ($this->side == 'left') echo 'e'; else echo 'w'; ?>"></span></div>
</div>
<div class="contenttab">
    <?php foreach ($this->getBlocks() AS $block): ?>
        <div class="mainTab <?php echo $block->getId(); ?> ellipsis <?php if($block->getConfig('cssClasses') != 'none') echo 'active'; ?>" rel="<?php echo $block->getId(); ?>">
            <?php echo t($block->getName(), FALSE); ?>
        </div>
    <?php endforeach; ?>
    <?php
    if (!empty($this->blocks)) {
        foreach ($this->blocks as $selected_block) {
            echo $selected_block->display() . PHP_EOL;
        }
    }
    ?>
</div>