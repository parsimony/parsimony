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
 * @package core\fields
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace core\fields;

/**
 * @title Vote
 * @description Vote
 * @version 1
 * @browsers all
 * @php_version_min 5.3
 * @modules_dependencies core:1
 */

class field_vote extends \field {

    /**
     * Build a field_numeric field
     * @param string $module
     * @param string $entity 
     * @param string $name 
     * @param string $type by default 'FLOAT'
     * @param integer $characters_max by default '20'
     * @param integer $characters_min by default 0
     * @param string $label by default ''
     * @param string $text_help by default ''
     * @param string $msg_error by default invalid
     * @param string $default by default ''
     * @param bool $required by default true
     * @param string $regex by default '[0-9\.,]*'
     * @param integer $visibility by default '7'
     */
    public function __construct($module, $entity, $name, $type = 'FLOAT', $characters_max = '20', $characters_min = 0, $label = '', $text_help = '', $msg_error = 'invalid', $default = '', $required = TRUE, $regex = '[0-9\.,]*', $visibility = 7) {
        $this->constructor(func_get_args());
    }

    /**
     * Validate field
     * @param string $value
     * @return string
     */
    public function validate($value) {
        if (is_array($value)) {
            return $value;
        } else {
            return FALSE;
        }
    }

    /**
     * Add a column after the last existing field 
     * @param string $fieldBefore
     * @return bool
     */
    public function addColumn($fieldBefore) {
        return \PDOconnection::getDB()->exec('ALTER TABLE ' . $this->module . '_' . $this->entity . ' ADD  ' . $this->name . ' FLOAT NOT NULL,
            ADD  ' . $this->name . '_nb_votes INT NOT NULL AFTER ' . $this->name);
    }

    /**
     * Alter the order of columns
     * @param string $fieldBefore
     * @param string $oldName optional
     * @return bool
     */
    public function alterColumn($fieldBefore, $oldName = FALSE) {
        return TRUE;
    }

    /**
     * Delete a column 
     * @return bool
     */
    public function deleteColumn() {
        return \PDOconnection::getDB()->exec('ALTER TABLE ' . $this->module . '_' . $this->entity . ' DROP ' . $this->name . ';
            ALTER TABLE ' . $this->module . '_' . $this->entity . ' DROP ' . $this->name . '_nb_votes');
    }

    /**
     * Fill SQL Features
     * @return string
     */
    public function sqlModel() {
        return $this->name . ' ' . $this->type . ' NOT NULL ,' . $this->name . '_nb_votes INT NOT NULL';
    }
    
    /**
     * List SQLcolumns
     * @return array
     */
    public function getColumns(){
        return array($this->name,$this->name . '_nb_votes');
    }

}

?>
