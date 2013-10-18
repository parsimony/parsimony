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
<div class="datagridWrapper">
    <table class="datagrid">
        <thead>
            <tr>
                <?php
                if(method_exists($obj, "prepareFieldsForDisplay")) $obj->prepareFieldsForDisplay();
                foreach ($obj->getFields() as $field) :
                    if (get_class($field) !== 'core\fields\field_formasso') :
                        if ($field->visibility & DISPLAY) :
                            ?>
                        <th><?php echo t(ucfirst(trim($field->label))); ?></th>
                            <?php
                        endif;
                    endif;
                endforeach;
                if (isset($modifModel)):
                    ?>
                <th></th>
                    <?php
                endif;
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($obj !== FALSE) :
                $id = '';
                $title = '';
                if($obj instanceof \entity){
                    $id = $obj->getId()->name;
                    $title = $obj->getBehaviorTitle();
                }
                $obj->setPagination(TRUE);
				if(get_class($obj) === 'core\classes\view') $obj->buildQuery(TRUE);//to force rebuild view
                foreach ($obj as $row) :
                    ?>
                    <tr class="line">
                        <?php
                        foreach ($obj->getFields() as $field) :
                            if ($field->visibility & DISPLAY) :
                                $fieldName = $field->name;
                                $class = '';
                                if ($fieldName === $id) {
                                    $class = ' datagrid_id';
                                }
                                if ($fieldName === $title) {
                                    $class .= ' datagrid_title';
                                }
                                if (get_class($field) !== 'core\fields\field_formasso') :
                                    ?>
                                <td class="column<?php echo $class; ?>">
									<?php
									if(substr($field->views['grid'],-8) === 'grid.php'){ /* to alias fields values */
										echo $row->$fieldName()->displayGrid();
									}else{
										echo $row->$fieldName;
									}
									?>
								</td>
                                    <?php
                                endif;
                            endif;
                        endforeach;
                        if (isset($modifModel)):
                            ?>
                        <td class="updateBTN"><span class="ui-icon ui-icon-pencil"></span></td>
                            <?php
                        endif;
                        ?>
                    </tr>
                    <?php
                endforeach;
            endif;
            ?>
        </tbody>
    </table>
</div>
<?php echo $obj->getPagination(); ?>
