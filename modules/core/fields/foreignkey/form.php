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
echo $this->displayLabel($fieldName);

$foreignID = $this->value;
$sth = PDOconnection::getDB()->query('SELECT * FROM ' . PREFIX . $this->moduleLink . '_' . $this->link); // used ->getEntity() but there was interference because of cache
if (is_object($sth)) {
	$sth->setFetchMode(PDO::FETCH_OBJ);
	echo '<select name="' . $tableName . '[' . $this->name . ']"><option></option>';
	$properties = app::getModule($this->moduleLink)->getEntity($this->link)->getFields();
	foreach ($sth as $key => $row) {
		$text = $this->templatelink;
		foreach ($properties as $key => $field) {
			if (get_class($field) == \app::$aliasClasses['field_ident'])
				$id = $key;
			if (isset($row->$key))
				$text = str_replace('%' . $key . '%', $row->$key, $text);
		}
		if ($row->$id == $foreignID)
			$selected = ' selected="selected"';
		else
			$selected = '';
		echo '<option value="' . $row->$id . '"' . $selected . '>' . $text . '</option>';
	}
	echo '</select>';
}
?>