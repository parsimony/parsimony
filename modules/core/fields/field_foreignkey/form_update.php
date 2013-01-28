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
 * @package core/fields
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>

<div>
    <label for="<?php echo $this->name ?>">
	<?php echo $this->link ?>
	<?php if (!empty($this->text_help)): ?>
    	<span class="tooltip ui-icon ui-icon-info" data-tooltip="<?php echo t($this->text_help) ?>"></span>
	<?php endif; ?>
    </label>
<?php
$foreignID = $this->value;
$sth = PDOconnection::getDB()->query('SELECT * FROM ' . PREFIX . $this->moduleLink.'_'.$this->link); // used ->getEntity() but there was interference because of cache
$sth->setFetchMode(PDO::FETCH_OBJ);
if(is_object($sth)){
    echo '<select name="'.$this->name.'"><option></option>';
    $properties = app::getModule($this->moduleLink)->getEntity($this->link)->getFields();
    foreach ($sth as $key => $line) {
        $text = $this->templatelink;
        foreach ($properties as $key => $field) {
            if(get_class($field)==\app::$aliasClasses['field_ident']) $id = $key;
            $text = str_replace('%'.$key.'%',$line->$key, $text);
        } echo $line->$id .' - '. $foreignID;
        if($line->$id == $foreignID) $selected = ' selected="selected"';
        else $selected = '';
        echo  '<option value="'.$line->$id.'"'.$selected.'>'.$text.'</option>';
    }
    echo '</select>';
}
?></div>