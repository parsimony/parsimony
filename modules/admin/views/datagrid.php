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

$id = '';
$title = '';
if($obj instanceof \entity){
	$id = $obj->getId()->name;
	$title = $obj->getBehaviorTitle();
}
$obj->setPagination(TRUE);
$obj->buildQuery(TRUE);
$fields = $obj->getFields();
$aliasClasses = array_flip(\app::$aliasClasses);
?>
<style>.field_formasso{display:none}</style>
<div class="datagridWrapper">
    <table class="datagrid">
        <thead>
            <tr>
                <?php
                foreach ($fields as $field) :
					if ($field->visibility & DISPLAY) : ?>
						<th class="<?php echo $aliasClasses[get_class($field)]; ?>"><?php echo t(ucfirst($field->label), FALSE); ?></th>
						<?php
					endif;
                endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php
			if (!$obj->isEmpty()) :
				foreach ($obj as $row) : ?>
					<tr class="line">
						<?php 
						foreach ($fields as $fieldName => $field) : /* use fieldName to reach value of fields because of alias */
							if ($field->visibility & DISPLAY) :
								$class = $aliasClasses[get_class($field)];
								if ($fieldName === $id) {
									$class .= ' datagrid_id';
								}
								if ($fieldName === $title) {
									$class .= ' datagrid_title';
								}
								?>
								<td class="column <?php echo $class; ?>">
									<?php echo $row->$fieldName()->displayGrid(); ?>
								</td>
								<?php
							endif;
						endforeach;
						if (isset($modifModel)): ?>
							<td class="updateBTN"><span class="ui-icon ui-icon-pencil"></span></td>
							<?php
						endif; ?>
					</tr>
					<?php
				endforeach;
			else: ?>
				<tr class="line noResults"><td colspan="20"><?php echo t('No results'); ?></td></tr>
			<?php
			endif;
            ?>
        </tbody>
    </table>
</div>
<?php echo $obj->getPagination(); ?>
