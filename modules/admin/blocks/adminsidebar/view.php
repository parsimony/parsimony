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
    <div class="subSidebarOnglet handle" style="cursor: move;" title="<?php echo t('Move', FALSE); ?>"><span class="ui-icon ui-icon-arrow-4"></span></div>
    <?php if ($this->side == 'left'): ?>
        <div class="subSidebarOnglet revert" title="<?php echo t('Return', FALSE); ?>"><span class="ui-icon ui-icon-seek-prev"></span></div>
        <div class="subSidebarOnglet" id="openleftslide"><span class="ui-icon ui-icon-circle-arrow-w" title="<?php echo t('Slide', FALSE); ?>"></span></div>
        <div class="subSidebarOnglet" id="resizeleftslide" title="<?php echo t('Resize', FALSE); ?>"><span class="ui-icon ui-icon-arrowthick-2-e-w ui-resizable-handle ui-resizable-e"></span></div>
    <?php else: ?>
        <div class="subSidebarOnglet revert" title="<?php echo t('Return', FALSE); ?>"><span class="ui-icon ui-icon-seek-next"></span></div>
        <div class="subSidebarOnglet" id="openrightslide" title="<?php echo t('Slide', FALSE); ?>"><span class="ui-icon ui-icon-circle-arrow-e"></span></div>
        <div class="subSidebarOnglet" id="resizerightslide" title="<?php echo t('Resize', FALSE); ?>"><span class="ui-icon ui-icon-arrowthick-2-e-w  ui-resizable-handle ui-resizable-w"></span></div>
    <?php endif; ?>
</div>
<div class="contenttab cs">
    <div <?php if ($this->side == 'left') echo 'class="creation"'; ?>>
	<?php foreach ($this->getBlocks() AS $block): ?>
    	<div class="mainTab <?php echo $block->getId(); ?> ellipsis" rel="<?php echo $block->getId(); ?>">
		<?php echo t($block->getName(), FALSE); ?>
    	</div>
	<?php endforeach; ?>
    </div>
    <?php
    if (!empty($this->blocks)) {
	foreach ($this->blocks as $selected_block) {
	    echo $selected_block->display() . PHP_EOL;
	}
    }
    ?>
</div>