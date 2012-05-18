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
 * @package core/fields
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>
<div> 
    <?php if ($this->use == 'normal') : ?>
        <label for="<?php echo $this->name ?>">
	    <?php echo $this->label ?>
	    <?php if (!empty($this->text_help)): ?>
		<span class="tooltip ui-icon ui-icon-info" data-tooltip="<?php echo t($this->text_help) ?>"></span>
	    <?php endif; ?>
        </label>
        <input type="date" class="date" name="<?php echo $this->name ?>" id="<?php echo $this->name ?>" value="<?php echo s($value) ?>" />
        <?php echo t('at') ?>  <input type="text" id="hh" name="hh" size="2" maxlength="2">
        : <input type="text" id="mn" name="mn" size="2" maxlength="2">

    <?php elseif ($this->use == 'creation') : ?>
        <input type="hidden" name="<?php echo $this->name ?>" value="<?php echo s($value) ?>">
    <?php elseif ($this->use == 'update') : ?>
    <?php endif; ?>
</div>