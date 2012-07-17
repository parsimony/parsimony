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
 * @package core\classes
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace core\classes;

/**
 * @abstract Field Class 
 * Provides the mapping of a SQL property in a PHP object
 */
class field {
    
    /** @var string field name */
    protected $fieldName;

    /** @var string module name */
    protected $module;

    /** @var string entity name */
    protected $entity;

    /** @var string field value */
    protected $value;
    
    // SQL Features
    /** @var string SQL name */
    protected $name;

    /** @var string SQL type */
    protected $type;

    /** @var integer SQL Max characters */
    protected $characters_max;

    /** @var integer SQL Min characters */
    protected $characters_min;

    /** @var string SQL Default */
    protected $default;

    /** @var bool SQL Requirement */
    protected $required;
    // Interface
    /** @var string label */
    protected $label;

    /** @var string text help */
    protected $text_help;

    /** @var string msg error */
    protected $msg_error;
    
    // Verif
    /** @var string regex */
    protected $regex;
    
    /** @var string visibility */
    protected $visibility;

    /**
     * Build field
     * @param string $module
     * @param string $entity 
     * @param string $name 
     * @param string $type by default 'VARCHAR'
     * @param bool $unique by default false
     * @param integer $characters_max by default 255
     * @param integer $characters_min by default 0
     * @param string $label by default ''
     * @param string $text_help by default ''
     * @param string $msg_error by default invalid
     * @param string $default by default ''
     * @param bool $required by default true
     * @param string $regex by default '.*'
     * * @param string $visibility by default '.*'
     */
    public function __construct($module, $entity, $name, $type = 'VARCHAR', $characters_max = 255, $characters_min = 0, $label = '', $text_help = '', $msg_error = 'Invalid', $default = '', $required = TRUE, $regex = '.*', $visibility = 7) {
        $this->constructor(func_get_args());
	$this->msg_error = 'Invalid '.$name;
    }

    /**
     * Build field arguments
     * @param string $args
     */
    protected function constructor($args) {
        $method = new \ReflectionMethod($this, '__construct');
        $params = $method->getParameters();
        foreach ($params as $key => $param) {
            $namevar = $param->getName();
            if ($param->isOptional() && (!isset($args[$key]) || $args[$key] == null))
                $this->$namevar = $param->getDefaultValue();
            else
                $this->$namevar = $args[$key];
        }
    }

    /**
     * Get field name
     * @param string $name
     * @return mixed 
     */
    public function __get($name) {
        if(isset($this->$name)) return $this->$name;
        else return FALSE;
    }

    /**
     * Set field value
     * @param string $value
     */
    public function setValue($value) {
        $this->value = $value;
    }

    /**
     * Get field value
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Get field title
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }
    
    /**
     * Get field title
     * @return string
     */
    public function getFieldPath() {
        if(!isset($this->fieldPath)){
            $this->fieldPath = 'modules/' . str_replace('\\', '/', get_class($this));
        }
        return $this->fieldPath;
    }
    
    /**
     * Convert into String
     * @return string
     */
    public function __toString() {
        return (string) $this->value;
    }

    /**
     * Display view
     * @param string &$view
     * @return string
     */
    public function display(&$row = '', $authorEdit = FALSE) {
        ob_start();
        $idName = $row->getId()->name;
        include($this->getFieldPath() . '/display.php');
        $html = ob_get_clean();
        /* todo check rights model in module for his ROLE */
        if(BEHAVIOR > 1 || (isset($_SESSION['id_user']) && $_SESSION['id_user'] == $row->getBehaviorAuthor())) $html = '<div class="editinline " spellcheck="false" data-module="' . $this->module . '" data-model="' . $this->entity . '" data-property="' . $this->name . '" data-id="' . $row->{$idName} . '">'.$html.'</div>';
        return $html;
    }
    
    /**
     * Display view
     * @param string &$view
     * @return string
     */
    public function displayEditInline(&$row = '') {
        ob_start();
        $idName = $row->getId()->name;
        $authorName = $row->getBehaviorAuthor()->name;
        include($this->getFieldPath() . '/display.php');
        $html = ob_get_clean();
        if(BEHAVIOR > 1 || $_SESSION['id_user'] == $row->$authorName) $html = '<div class="editinline " spellcheck="false" data-module="' . $this->module . '" data-model="' . $this->entity . '" data-property="' . $this->name . '" data-id="' . $row->{$idName} . '">'.$html.'</div>';
        return $html;
    }

    /**
     * Display grid
     * @return string
     */
    public function displayGrid() {
        ob_start();
        include($this->getFieldPath() . '/grid.php');
        $html = ob_get_clean();
        return $html;
    }
    
    /**
     * Display filter form
     * @return string
     */
    public function displayFilter() {
        ob_start();
        include($this->getFieldPath() . '/form_filter.php');
        $html = ob_get_clean();
        return $html;
    }

    /**
     * Display Updating Form
     * @param string $value
     * @return string
     */
    public function formUpdate($value,&$row = '') {
        ob_start();
        include($this->getFieldPath() . '/form_update.php');
        $html = ob_get_clean();
        return $html;
    }

    /**
     * Display Adding Form
     * @return string
     */
    public function formAdd() {
        ob_start();
        include($this->getFieldPath() . '/form_add.php');
        $html = ob_get_clean();
        return $html;
    }

    /**
     * Validate the value of Field
     * @param string $value
     * @return string|false
     */
    public function validate($value) {
        return filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '#' . $this->regex . '#')));
    }

    /**
     * Add a column after the last existing field 
     * @param string $fieldBefore
     * @return bool
     */
    public function addColumn($fieldBefore) {
        if (empty($fieldBefore))
            $pos = ' FIRST ';
        else
            $pos = ' AFTER ' . $fieldBefore;
        $sql = 'ALTER TABLE ' . $this->module . '_' . $this->entity . ' ADD ' . $this->sqlModel() . $pos;
        return (bool) PDOconnection::getDB()->exec($sql);
    }

    /**
     * Alter the order of columns
     * @param string $fieldBefore
     * @param string $oldName optional
     * @return bool
     */
    public function alterColumn($fieldBefore,$oldName = FALSE) {
        if (empty($fieldBefore))
            $pos = ' FIRST ';
        else
            $pos = ' AFTER ' . $fieldBefore;
	if($oldName) $name = $oldName;
	else $name = $this->name;
        $sql = 'ALTER TABLE ' . $this->module . '_' . $this->entity . ' CHANGE ' . $name. ' ' . str_replace(' PRIMARY KEY', '', $this->sqlModel() . $pos);
        return (bool) PDOconnection::getDB()->exec($sql);
    }

    /**
     * Delete a column 
     * @return bool
     */
    public function deleteColumn() {
        $sql = 'ALTER TABLE ' . $this->module . '_' . $this->entity . ' DROP ' . $this->name;
        return (bool) PDOconnection::getDB()->exec($sql);
    }

    /**
     * Fill SQL Features
     * @return string
     */
    public function sqlModel() {
        $primary_key = $auto_increment = '';
        if (get_class($this) == \app::$aliasClasses['field_ident']) {
            $primary_key = ' PRIMARY KEY';
            $auto_increment = ' AUTO_INCREMENT';
        }
        if ($this->required)
            $required = ' NOT NULL';
        else
            $required = 'NULL';
        if (!empty($this->characters_max) || $this->characters_max != 0)
            $characters_max = '(' . $this->characters_max . ')';
        else
            $characters_max = '';
        if (!empty($this->default))
            $default = ' DEFAULT \'' . $this->default . '\'';
        else
            $default = '';
        return $this->name . ' ' . $this->type . $characters_max . ' ' . $required . $default . $auto_increment . $primary_key;
    }
    
    /**
     * Fill SQL Features
     * @return string
     */
    public function sqlFilter($filter) {
        return 'like \'%' . $filter . '%\'';
    }
    
    /**
     * List SQLcolumns
     * @return array
     */
    public function getColumns(){
        return array($this->name);
    }

}

?>