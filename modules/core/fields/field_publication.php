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
 * @package core\fields
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace core\fields;

/**
 * @title Publication
 * @description Publication
 * @version 1
 * @browsers all
 * @php_version_min 5.3
 * @modules_dependencies core:1
 */

class field_publication extends \field {

	protected $type = 'DATETIME';
	protected $characters_max = '';

	/**
	 * Validate field
	 * @param string $value
	 * @return string
	 */
	public function validate($value) {
		if(is_array($value) && isset($value[$this->name]) && isset($value[$this->name . '_status']) && isset($value[$this->name . '_visibility'])
				&& is_numeric($value[$this->name . '_status']) && $value[$this->name . '_status'] >= 0 && $value[$this->name . '_status'] <= 2
				&& is_numeric($value[$this->name . '_visibility']) && $value[$this->name . '_visibility'] >= 0 && $value[$this->name . '_visibility'] <= 2){
			if (preg_match('#^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$#', $value[$this->name], $date)) {
				if (checkdate($date[2], $date[3], $date[1])) {
					return $value;
				}
			}
		}
		return FALSE;
	}

	/**
	 * Add a column after the last existing field 
	 * @param string $fieldBefore
	 * @return bool
	 */
	public function addColumn($fieldBefore = '') {
		if (empty($fieldBefore))
			$pos = ' FIRST ';
		else
			$pos = ' AFTER ' . $fieldBefore;
		return \PDOconnection::getDB()->exec('ALTER TABLE ' . $this->module . '_' . $this->entity . ' ADD ' . $this->name . ' DATETIME NOT NULL '.$pos.';
			ALTER TABLE ' . $this->module . '_' . $this->entity . ' ADD ' . $this->name . '_status INT(1) NOT NULL '.$pos.';
			ALTER TABLE ' . $this->module . '_' . $this->entity . ' ADD ' . $this->name . '_visibility VARCHAR(25) NOT NULL '.$pos);
	}

	/**
	 * Alter the order of columns
	 * @param string $fieldBefore
	 * @param string $oldName optional
	 * @return bool
	 */
	public function alterColumn($fieldBefore = '', $oldName = FALSE) {
		return TRUE;
	}

	/**
	 * Delete a column 
	 * @return bool
	 */
	public function deleteColumn() {
		return \PDOconnection::getDB()->exec('ALTER TABLE ' . $this->module . '_' . $this->entity . ' DROP ' . $this->name . ';
			ALTER TABLE ' . $this->module . '_' . $this->entity . ' DROP ' . $this->name . '_status' . ';
			ALTER TABLE ' . $this->module . '_' . $this->entity . ' DROP ' . $this->name . '_visibility');

	}

	/**
	 * Fill SQL Features
	 * @return string
	 */
	public function sqlModel() {
		return $this->name . ' ' . $this->type . ' NOT NULL ,' . $this->name . '_status INT(1) NOT NULL,' . $this->name . '_visibility VARCHAR(25) NOT NULL';
	}

	/**
	 * List SQLcolumns
	 * @return array
	 */
	public function getColumns() {
		return array($this->name, $this->name . '_visibility', $this->name . '_status');
	}

}

?>
