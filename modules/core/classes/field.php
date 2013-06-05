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
    
    /** @var string visibility */
    protected $rights;
    
    /** @var object of the container */
    public $row = "";
    
    /** @var object of the container */
    public $views = array();

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
     * @param string $visibility by default '.*'
     */
    public function __construct($module, $entity, $name, $type = 'VARCHAR', $characters_max = 255, $characters_min = 0, $label = '', $text_help = '', $msg_error = 'Invalid', $default = '', $required = TRUE, $regex = '.*', $visibility = 7) {
        $this->constructor(func_get_args());
        $this->msg_error = 'Invalid ' . $name;
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
        if (isset($this->$name))
            return $this->$name;
        else
            return FALSE;
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
    public function &getValue() {
        return $this->value;
    }
    
    /**
     * Set field label
     * @param string $label
     */
    public function setLabel($label) {
        $this->label = $label;
        return $this;
    }
    
    /**
     * Set field entity name
     * @param string $name
     */
    public function setEntity($name) {
        $this->entity = $name;
        return $this;
    }
    
    /**
     * Set field visibility
     * @param int $visibility
     */
    public function setVisibility($visibility) {
        $this->visibility = $visibility;
        return $this;
    }

    /**
     * Convert into String
     * @return string
     */
    public function __toString() {
        return $this->display();
    }

    
    public function __sleep() {
        $fields = get_object_vars($this);
        unset($fields['views']);
        unset($fields['row']);
        unset($fields['hasUpdateRight']);
        if(isset($fields['editInline'])) unset($fields['editInline']);
        return array_keys($fields);
    }
    
    /**
     * Display view
     * Returns by default the display view of field. With editinline rights returns the editing view.
     * if behavior = 0 check if he has the right and if he is the autor of the content
     * if behavior > 0 check if id_role has the right
     * @param object &$row
     * @return string
     */
    public function display() {
        $row = $this->row;
        ob_start();
        include($this->views['display']);
        return ob_get_clean();
    }
    
    /**
     * Display view
     * @param string $data
     * @param int $id
     * @return string
     */
    public function displayEditInline(&$row = '', $authorID = FALSE) {
        /*if($this->row->isAuthor === TRUE && $row->getRights($_SESSION['id_role']) & UPDATE ){
            \app::$request->page->addJSFile('lib/editinline.js');
            $this->displayView = 'editinline.php';
        }*/
        ob_start();
        $idName = $row->getId()->name;
        /*$authorName = $row->getBehaviorAuthor()->name;*/
	if (empty($this->displayView)) {
	    $this->displayView = 'display.php';
            if ((isset($_SESSION['id_user']) && $authorID == $_SESSION['id_user'] || $_SESSION['behavior'] >= 1) && app::getModule($this->module)->getEntity($this->entity)->getRights($_SESSION['id_role']) & UPDATE ) {
                \app::$request->page->addJSFile('lib/editinline.js');
                $this->displayView = 'editinline.php';
            }
        }
	
        include($this->views['fieldPath'] . '/' . $this->displayView);
        return ob_get_clean();
    }
    
    public function saveEditInline($data, $id) {
        $data = $this->validate($data);
        if ($data !== FALSE) {
            $entityObj = \app::getModule($this->module)->getEntity($this->entity);
            $res = \PDOconnection::getDB()->exec('UPDATE ' .PREFIX . $this->module . '_' . $this->entity . ' SET ' . $this->name . ' = \'' . str_replace("'", "\'", $data) . '\' WHERE ' . $entityObj->getId()->name . '=' . $id);
            if ($res !== FALSE) {
                return TRUE;
            }
        }
        return FALSE;
    }
    
    /**
     * Display view
     * @param string $data
     * @param int $id
     * @return string
     */
    public function saveEditInlineAction($data, $id) {
	if ($this->saveEditInline($data, $id)) {
	    $return = array('eval' => '', 'notification' => t('The data have been saved', FALSE), 'notificationType' => 'positive');
	}else{
            $return = array('eval' => '', 'notification' => t('The data has not been saved', FALSE), 'notificationType' => 'negative');
        }
        \app::$response->setHeader('X-XSS-Protection', '0');
        \app::$response->setHeader('Content-type', 'application/json');
        return json_encode($return);
    }

    /**
     * Display grid
     * @return string
     */
    public function displayGrid() {
        ob_start();
        include($this->views['grid']);
        return ob_get_clean();
    }

    /**
     * Display filter form
     * @return string
     */
    public function displayFilter() {
        ob_start();
        include($this->views['fieldPath'] . '/form_filter.php');
        return ob_get_clean();
    }
    
    /**
     * Display Updating Form
     * @param string $value
     * @param string &$row optional
     * @return string
     */
    public function form($value = '', &$row = FALSE) {
        ob_start();
	$fieldName = $this->name;
	if(is_object($row)){
	    $fieldName .= '_'.$row->getId()->value;
	}
	?>
	<div class="field placeholder">
	<?php
        include($this->views['fieldPath'] . '/form.php');
	?>
	</div>
	<?php
        return ob_get_clean();
    }
    
    /**
     * Display Label
     * @param string $fieldName
     * @return string
     */
    public function displayLabel($fieldName) {
        ob_start();
	?>
	<label for="<?php echo $fieldName ?>">
	    <?php echo t($this->label) ?>
	    <?php if (!empty($this->text_help)): ?>
	    <span class="tooltip ui-icon ui-icon-info" data-tooltip="<?php echo t($this->text_help) ?>"></span>
	    <?php endif; ?>
	</label>
	<?php
        return ob_get_clean();
    }

    /**
     * Validate the value of Field
     * @param string $value
     * @return string|false
     */
    public function validate($value) {
        return filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '#' .  str_replace('#','\#',$this->regex) . '#')));
    }

    /**
     * Add a column after the last existing field 
     * @param string $fieldBefore optional
     * @return bool|int
     */
    public function addColumn($fieldBefore = '') {
        if (empty($fieldBefore))
            $pos = ' FIRST ';
        else
            $pos = ' AFTER ' . $fieldBefore;
        $sql = 'ALTER TABLE ' . PREFIX . $this->module . '_' . $this->entity . ' ADD ' . $this->sqlModel() . $pos;
        return \PDOconnection::getDB()->exec($sql);
    }

    /**
     * Alter the order of columns
     * @param string $fieldBefore optional
     * @param string $oldName optional
     * @return bool|int
     */
    public function alterColumn($fieldBefore = '', $oldName = FALSE) {
        if (empty($fieldBefore))
            $pos = ' FIRST ';
        else
            $pos = ' AFTER ' . $fieldBefore;
        if ($oldName)
            $name = $oldName;
        else
            $name = $this->name;
        $sql = 'ALTER TABLE ' . PREFIX . $this->module . '_' . $this->entity . ' CHANGE ' . $name . ' ' . str_replace(' PRIMARY KEY', '', $this->sqlModel() . $pos);
        return \PDOconnection::getDB()->exec($sql);
    }

    /**
     * Delete a column 
     * @return bool|int
     */
    public function deleteColumn() {
        $sql = 'ALTER TABLE ' . PREFIX . $this->module . '_' . $this->entity . ' DROP ' . $this->name;
        return \PDOconnection::getDB()->exec($sql);
    }

    /**
     * Fill SQL Features
     * @return string
     */
    public function sqlModel() {
        $primary_key = $auto_increment = $characters_max = $default = '';
        if (get_class($this) === \app::$aliasClasses['field_ident']) {
            $primary_key = ' PRIMARY KEY';
            $auto_increment = ' AUTO_INCREMENT';
        }
        if ($this->required)
            $required = ' NOT NULL';
        else
            $required = 'NULL';
        if (!empty($this->characters_max) || $this->characters_max != 0)
            $characters_max = '(' . $this->characters_max . ')';
        if (!empty($this->default))
            $default = ' DEFAULT \'' . $this->default . '\'';
        return $this->name . ' ' . $this->type . $characters_max . ' ' . $required . $default . $auto_increment . $primary_key;
    }

    /**
     * Returns SQL to filter the field ( overridable for multiple colums)
     * @param string $filter
     * @return string
     */
    public function sqlFilter($filter) {
        return 'like \'%' . $filter . '%\'';
    }

    /**
     * List SQLcolumns
     * @return array
     */
    public function getColumns() {
        return array($this->name);
    }
    
    
    /**
     * Update Rights
     * @param string $role
     * @param integer $rights
     */
    public function setRights($role, $rights) {
        /* We remove role entry if the role has the maximum of rights ( 7 = DISPLAY:1 + INSERT:2 + UPDATE:4 ) #performance */
        if($rights === 7){
            if(isset($this->rights[$role])){
                unset($this->rights[$role]);
            }
        }else{
            $this->rights[$role] = $rights;
        }
    }

    /**
     * Get Rights
     * @param string $role
     * @return integer
     */
    public function getRights($role) {
        if (isset($this->rights[$role]))
            return $this->rights[$role];
        return 7;
    }
    
    /**
     * Get all Rights
     * @param string $role
     * @return string
     */
    public function getAllRights() {
            return $this->rights;
    }
    
    /**
     * Set all Rights
     * @param array $rights
     */
    public function setAllRights(array $rights) {
        foreach ($rights as $id_role => $right) {
            $this->setRights($id_role, $right);
        }
    }

}

?>