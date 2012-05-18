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
<table class="datagrid">
    <thead>
        <tr>
            <?php //print_r($obj);
            foreach ($obj->getFields() as $field) :
                if (get_class($field) != 'core\fields\field_formasso') :
                ?>
                <th><?php echo ucfirst(str_replace(' ', '', $field->label)); ?></th>
            <?php endif;
            endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($obj != FALSE) :
            $id = $obj->getId();
	    if(is_object($id)) $id = $id->name;
            $title = $obj->getBehaviorTitle();
	    if(is_object($title)) $title = $title->name;
            foreach ($obj as $key => $line) :
                ?>
                <tr class="line">
                    <?php
                    foreach ($obj->getFields() as $field) :
			$fieldName = $field->name;
			if ($fieldName == $id) {
                            $class = 'datagrid_id';
                        } elseif ($fieldName == $title) {
                            $class = 'datagrid_title';
                        } else {
                            $class = '';
                        }
                        if (get_class($field) != 'core\fields\field_formasso') :
                        ?>
                        <td class="column <?php echo $class; ?>"><?php echo $line->{$field->name}->displayGrid(); ?></td>
                    <?php endif;
                    endforeach; ?>
                </tr>
                <?php
            endforeach;
        endif;
        ?>
    </tbody>
</table>
<?php echo $obj->getPagination(); ?>