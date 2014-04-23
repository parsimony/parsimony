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
 * @package admin
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

$id = $this->entity->getId()->value;
?>
<style>.permissionGroup{float:left;padding: 7px;margin: 7px;}.perm{padding: 5px 0;}.permissionGroup label{padding-left:7px;}</style>
<h3 style="margin-bottom: 0;"><?php echo t('Permissions'); ?></h3>
<div id="<?php echo $this->name . '_' . $id; ?>">
<?php foreach($this->entity->getPermissionGroups() as $groupTitle => $permissionGroup) : ?>
	<div class="permissionGroup">
		<h4><?php echo t($groupTitle); ?></h4>
		<?php foreach($permissionGroup as $key => $right) :
			if($_SESSION['permissions'] & $key) : ?>
				<div class="perm">
					<input type="checkbox" value="<?php echo $key; ?>" name="core_role[permissions][<?php echo $key; ?>]" id="perm_<?php echo $key; ?>"<?php echo ($this->value & $key ? 'checked="checked"' : ''); ?>><label for="perm_<?php echo $key; ?>"><?php echo $right; ?></label>
				</div>
			<?php endif; ?>
	<?php endforeach; ?>
	</div>
<?php endforeach; ?>
</div>