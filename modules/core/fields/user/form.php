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
 * @package core/fields
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

echo $this->displayLabel($fieldName);
 ?>
<select name="<?php echo $tableName ?>[<?php echo $this->name ?>]" id="<?php echo $fieldName ?>">
	<?php
	$users = \app::getModule('core')->getEntity('user')->select();
	foreach ($users as $row) :?>
	<option value="<?php echo $row->id_user; ?>"<?php if($value == $row->id_user) echo ' selected="selected"' ?>><?php echo $row->pseudo; ?></option>
	<?php endforeach; ?>
</select>