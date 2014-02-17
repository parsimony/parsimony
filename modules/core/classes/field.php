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
 * @authors Julien Gras et BenoÃ®t Lorillot
 * @copyright  Julien Gras et BenoÃ®t Lorillot
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

	/** @var string field value */
	protected $value;
	
	// SQL Features
	
	/** @var string SQL name */
	protected $name;

	/** @var string SQL type */
	protected $type = 'VARCHAR';

	/** @var integer SQL Max characters */
	protected $characters_max = 255;

	/** @var integer SQL Min characters */
	protected $characters_min = 0;

	/** @var string SQL Default */
	protected $default = '';

	/** @var bool SQL Requirement */
	protected $required = TRUE;
	
	// Interface
	
	/** @var string label */
	protected $label = '';

	/** @var string text help */
	protected $text_help = '';

	/** @var string msg error */
	protected $msg_error = 'Invalid';
	
	// Verif
	/** @var string regex */
	protected $regex = '^.*$';

	/** @var string visibility */
	protected $visibility = 7;
	
	//others

	/** @var string visibility */
	protected $rights;

	/** @var object of the entity container */
	protected $entity = '';
	
	/** @var object of the entity container */
	protected $editMode = 'default';

	/**
	 * Build field
	 * @param string $module
	 * @param string $entity 
	 * @param string $name 
	 * @param array $properties
	 */
	public function __construct($name, $properties = array()) {
		$this->name = $name;
		foreach($properties AS $property => $value){
			$this->$property = $value;
		}
	}

	/**
	 * Get field name
	 * @param string $name
	 * @return mixed 
	 */
	public function __get($name) {
		if (isset($this->$name)) {
			return $this->$name;
		} elseif($name === 'getEditOptions') {
			return $this->getEditOptions = $this->getEditOptions();
		} else { /* allow us to defines these vars just once a time */
			$fieldPath = 'modules/' . str_replace('\\', '/', get_class($this)); /* __get can't recall himself */
			$this->currentRights = $this->getRights($_SESSION['id_role']);
			
			/* Determine if current user has the right to editinline */
			if ($this->currentRights & DISPLAY) {
				$this->views = array('display' => $fieldPath . '/display.php', 'grid' => $fieldPath . '/grid.php', 'form' => $fieldPath . '/form.php');
			} else {
				$this->views = array('display' => 'php://temp', 'grid' => 'php://temp', 'form' => 'php://temp'); // display nothing, to avoid a "if" in each field->display() call
			}
			return $this->$name;
		}
	}

	/**
	 * Set field value
	 * @param string $value
	 * @return field
	 */
	public function setValue($value) {
		$this->value = $value;
		return $this;
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
	 * @return field
	 */
	public function setLabel($label) {
		$this->label = $label;
		return $this;
	}

	/**
	 * Set parent entity
	 * @param string $name
	 * @return field
	 */
	public function setEntity(\entity $entity) {
		$this->entity = $entity;
		return $this;
	}

	/**
	 * Set field visibility
	 * @param int $visibility
	 * @return field
	 */
	public function setVisibility($visibility) {
		$this->visibility = $visibility;
		return $this;
	}

	/**
	 * Convert into String, and choose if we display for read or editinline
	 * @return string
	 */
	public function __toString() {
		return $this->editInline();
	}

	public function __sleep() {
		$reflect = new \ReflectionClass($this);
		$defaultValues = $reflect->getDefaultProperties();
		$properties = get_object_vars($this);
		unset($properties['views']);
		unset($properties['value']);
		unset($properties['fieldPath']);
		unset($properties['editMode']);
		unset($properties['getEditOptions']);
		unset($properties['entity']);
		unset($properties['module']); /* todo remove */
		foreach ($properties AS $name => $value) { /* unset unchanged values */
			if(isset($defaultValues[$name]) && $properties[$name] == $defaultValues[$name]) {
				unset($properties[$name]);
			}
		}
		return array_keys($properties);
	}

	/**
	 * Display view
	 * @param object &$entity
	 * @return string
	 */
	public function display() {
		ob_start();
		include($this->views['display']);
		return ob_get_clean();
	}
	
	/**
	 * Display edit Inline view
	 * @return string
	 */
	public function editInline() {
		if($this->currentRights & UPDATE) {
			return '<div data-id="' . $this->entity->getId()->value.'" ' . $this->getEditOptions . '>'
						. $this->display()
						. '</div>';
		} else {
			return $this->display();
		}
	}
	
	public function getEditOptions() {
		if(is_object(\app::$response->page)){ /* for ajax requests */
			\app::$response->addJSFile('core/js/editinline.js');
			\app::$response->addCSSFile('core/css/editinline.css');
		}
		return  'class="parsieditinline fieldeditinline"  data-mode="' . $this->editMode . '" data-module="' . $this->entity->getModule() . '" data-entity="' . $this->entity->getName() . '" data-property="' . $this->name . '" data-label="' . $this->label . '"';
	}
	
	public function editInlineForAuthor($authorID) {
		if($authorID === $_SESSION['id_user'] || $_SESSION['behavior'] >= 1){
			$this->editInline();
		} else {
			return $this->display();
		}
	}

	public function saveEditInlineAction($id, $data = FALSE) {
		if($data === FALSE){ /* if it's not an ajax request ( forms ) */
			$data = \app::$request->getParam($this->name);
		}
		if($data !== FALSE){
			$data = $this->validate($data);
			if ($data !== FALSE) {
				$entityObj = \app::getModule($this->entity->getModule())->getEntity($this->entity->getName());
				$res = \PDOconnection::getDB()->prepare('UPDATE ' .PREFIX . $this->entity->getModule() . '_' . $this->entity->getName() . ' SET ' . $this->name . ' = :data WHERE ' . $entityObj->getId()->name . '=:id');
				$res->execute(array(':data' => $data, ':id' => $id));
				if ($res !== FALSE) {
					$this->value = $data;
					return $this->display();
				}
			}
		}
		return FALSE;
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
	 * Display Updating Form
	 * @return string
	 */
	public function form() {
		ob_start();
		$row = $this->entity;
		$tableName = $row->getModule() . '_' . $row->getName();
		$fieldName = $tableName . '_' . $this->name;
		$value = $this->value;
		$id = $row->getId()->value;
		if ($id !== null) {
			$fieldName .= '_' . $id;
		}
		?>
		<div class="field placeholder">
		<?php
			include($this->views['form']);
		?>
		</div>
		<?php
		return ob_get_clean();
	}
	
	public function editInlineFormAction($id) {
		if (is_numeric($id)) {
			$this->entity->where($this->entity->getModule() . '_' . $this->entity->getName() . '.' . $this->entity->getId()->name . ' = ' . $id);
			include('modules/core/views/editInlineForm.php');
		}
		return FALSE;
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
			<span class="tooltip ui-icon ui-icon-info" data-tooltip="<?php echo t($this->text_help, FALSE) ?>"></span>
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
		if(empty($value) && $this->required) {
			return FALSE;
		} else {
			$length = strlen($value);
			if ($length >= $this->characters_min && $length <= $this->characters_max) {
				return filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '#' .  str_replace('#','\#',$this->regex) . '#')));
			}
			return FALSE;
		}
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
			$pos = ' AFTER `' . $fieldBefore . '`';
		$sql = 'ALTER TABLE ' . PREFIX . $this->entity->getModule() . '_' . $this->entity->getName() . ' ADD ' . $this->sqlModel() . $pos;
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
			$pos = ' AFTER `' . $fieldBefore . '`';
		if ($oldName)
			$name = $oldName;
		else
			$name = $this->name;
		$sql = 'ALTER TABLE ' . PREFIX . $this->entity->getModule() . '_' . $this->entity->getName() . ' CHANGE `' . $name . '` ' . str_replace(' PRIMARY KEY', '', $this->sqlModel() . $pos);
		return \PDOconnection::getDB()->exec($sql);
	}

	/**
	 * Delete a column 
	 * @return bool|int
	 */
	public function deleteColumn() {
		$sql = 'ALTER TABLE ' . PREFIX . $this->entity->getModule() . '_' . $this->entity->getName() . ' DROP `' . $this->name . '`';
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
			$required = 'NOT NULL';
		else
			$required = 'NULL';
		if (!empty($this->characters_max) || $this->characters_max != 0)
			$characters_max = '(' . $this->characters_max . ')';
		if (!empty($this->default))
			$default = ' DEFAULT \'' . $this->default . '\'';
		return '`' . $this->name . '` ' . $this->type . $characters_max . ' ' . $required . $default . $auto_increment . $primary_key;
	}

	/**
	 * Returns SQL to filter the field ( overridable for multiple colums)
	 * @param string $filter
	 * @return string
	 */
	public function sqlFilter($filter) {
		$fieldName = $this->getTableName() . '_' . $this->name;
		if(isset($this->calculation)){
			$name = $this->calculation;
		}else{
			$name = $this->getFullName();
		}
		if (is_array($filter)) {
			if (isset($filter[0])) {
				foreach ($filter as $key => &$value) {
					$paramName = $fieldName . '_in' . $key;
					\app::$request->setParam($paramName, $value);
					$value = $paramName;
				}
				return $name . ' IN (:' . implode(',:', $filter) . ')';
			} else {
				$start = isset($filter['start']) && !empty($filter['start']);
				$end = isset($filter['end']) && !empty($filter['end']);
				if ($start === TRUE && $end === TRUE) {
					\app::$request->setParam($fieldName . '_start', $filter['start']);
					\app::$request->setParam($fieldName . '_end', $filter['end']);
					return $name . ' BETWEEN :' . $fieldName . '_start' . ' AND :' . $fieldName . '_end';
				} elseif ($start === TRUE) {
					\app::$request->setParam($fieldName . '_start', $filter['start']);
					return $name . ' >= :' . $fieldName . '_start';
				} elseif ($end === TRUE) {
					\app::$request->setParam($fieldName . '_end', $filter['end']);
					return $name . ' <= :' . $fieldName . '_end';
				}
				return '';
			}
		} else {
			\app::$request->setParam($fieldName, '%' . $filter . '%');
			return $name . ' like :' . $fieldName;
		}
	}
	
	public function sqlGroup($group) {
		return $this->getFullName();
	}
	
	public function getAllValues() {
		$table = $this->getTableName();
		$result = \PDOconnection::getDB()->query('select ' . PREFIX . $table . '.' . $this->name . ' from ' . PREFIX . $table . ' group by ' . PREFIX . $table . '.' . $this->name);
		if (is_object($result)) {
			$values = $result->fetchAll(\PDO::FETCH_COLUMN);
			if (is_array($values)) {
				$values = array_combine($values, $values);
				return $values;
			}
		}
		return array();
	}
	
	public function getTableName() {
		return $this->entity->getModule() . '_' . $this->entity->getName();
	}
	
	public function getFullName() {
		return $this->entity->getModule() . '_' . $this->entity->getName() . '.' . $this->name;
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
