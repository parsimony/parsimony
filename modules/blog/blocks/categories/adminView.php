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
 * @copyright Julien Gras et Benoît Lorillot
 * 
 * @category Parsimony
 * @package blog/blocks
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
?>
<div class="placeholder">
    <label><?php echo t('Category to display') ?></label>
    <select name="display">
	<option value="no"></option>
	<?php echo $this->drawTreeAdmin($this->getCategories(TRUE), 'display'); ?>
    </select>
</div>
<div class="placeholder">
    <label><?php echo t('Categories to exclude') ?></label>
    <select name="exclude[]" multiple="multiple" style="height:250px">
	<option value="no"></option>
	<?php echo $this->drawTreeAdmin($this->getCategories(TRUE), 'exclude'); ?>
    </select>
</div>
<div class="placeholder">
    <label><?php echo t('URL Pattern') ?></label>
    <input type="text" name="URLpattern" value="<?php if($this->getConfig('URLpattern')) echo s($this->getConfig('URLpattern')); else echo s('category/%url%'); ?>" />
</div>