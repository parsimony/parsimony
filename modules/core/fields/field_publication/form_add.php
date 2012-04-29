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
<div><label for="<?php echo $this->name ?>">
	<?php echo $this->label ?>
	<?php if (!empty($this->text_help)): ?>
    	<span class="tooltip ui-icon ui-icon-info" data-tooltip="<?php echo $this->text_help ?>"></span>
	<?php endif; ?>
    </label>
<input type="button" style="width:35%" value="<?php echo t('Save Draft',FALSE); ?>" name="add">
<input type="button" style="width:35%"" value="<?php echo t('Publish',FALSE); ?>" name="add">
<img src="<?php echo BASE_PATH ?>admin/img/calendar.gif" onclick="$(this).next().slideToggle()" style="cursor:pointer" />
<div class="none" style="font-size: 16px;color: #333;text-shadow: 0px 1px 0px white;padding-top: 8px;">
  <?php echo t('Publish', false)?> : <input type="text" class="date datepicker" name="<?php echo $this->name ?>" style="width: 80px;" id="<?php echo $this->name ?>" value="<?php echo date('Y/m/d') ?>" />
 @ <input type="text" id="hh" name="hh" style="width: 30px;" maxlength="2" value="<?php echo date('H') ?>"> : <input type="text" id="mn" name="mn" style="width: 30px;" maxlength="2" value="<?php echo date('s') ?>">
 </div>
</div>