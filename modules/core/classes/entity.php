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
 * @copyright Julien Gras et Benoît Lorillot
 * 
 * @category Parsimony
 * @package core\classes
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace core\classes;

/**
 * @abstract Entity Class 
 * Provides the mapping of a SQL table in a PHP object
 * Iterator interface allows you to:
 * $users = app::getModule('core')->getEntity('user');
 * foreach($users as $user){
 *      echo $user->pseudo;
 * }
 */
abstract class entity extends queryBuilder implements \Iterator {

	/** @var string _module name */
	protected $_module;

	/** @var string table name for example $_tableName = $module_$entityName */
	protected $_tableName;

	/** @var string title */
	protected $_entityTitle;

	/** @var string entity name */
	protected $_entityName;

	/** @var string title in metadata */
	public $behaviorTitle;

	/** @var string description in metadata */
	public $behaviorDescription;

	/** @var string keywords in metadata */
	public $behaviorKeywords;

	/** @var string image in metadata */
	public $behaviorImage;
	
	/** @var string author in metadata */
	public $behaviorAuthor;

	/** @var array of extends */
	protected $_extends = array();

	/** @var array of rights */
	protected $_rights = array();
	
	/**
	 * Constructor: init entity vars
	 */
	public function __construct() {
		list( $this->_module, $entity, $this->_entityName) = explode('\\', get_class($this));
		$this->_tableName = $this->_module . '_' . $this->_entityName;
	}

	/**
	 * Get the value of a given field name
	 * @param string $name
	 * @return field object | false
	 */
	public function __get($name) {
		return $this->fields[$name]->value;
	}

	/**
	 * Set name of entity
	 * @param string $name
	 * @param string $value
	 */
	public function __set($name, $value) {
		if (isset($this->fields[$name])) { /* usefull for fields multicolumns */
			$this->fields[$name]->setValue($value);
		} else {
			$this->$name = $value;
		}
	}

	/**
	 * Get the name of a given entity
	 * @param string $name
	 * @param string $args
	 * @return field object | false
	 */
	public function __call($name, $args) {
		return $this->fields[$name];
	}
	
	/**
	 * Update Rights
	 * @param string $role
	 * @param integer $rights
	 */
	public function setRights($role, $rights) {
		/* We remove role entry if the role has the maximum of rights ( 15 = DISPLAY:1 + INSERT:2 + UPDATE:4 + DELETE:8 ) #performance */
		if($rights === 15){
			if(isset($this->_rights[$role])){
				unset($this->_rights[$role]);
			}
		}else{
			$this->_rights[$role] = $rights;
		}
	}

	/**
	 * Get Rights
	 * @param string $role
	 * @return integer
	 */
	public function getRights($role) {
		if (isset($this->_rights[$role]))
			return $this->_rights[$role];
		return 15;
	}

	/**
	 * Get all Rights
	 * @param string $role
	 * @return string
	 */
	public function getAllRights() {
			return $this->_rights;
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
	
	/**
	 * Get the module name
	 * @return string
	 */
	public function getModule() {
		return $this->_module;
	}

	/**
	 * Get entity name
	 * @return string
	 */
	public function getName() {
		return $this->_entityName;
	}
	
	/**
	 * Get Sql table name
	 * @return string
	 */
	public function getTableName() {
		return $this->_tableName;
	}

	/**
	 * Get Title
	 * @return string
	 */
	public function getTitle() {
		return $this->_entityTitle;
	}

	/**
	 * Set Title
	 * @return string
	 */
	public function setTitle($title) {
		$this->_entityTitle = $title;
	}

	/**
	 * Create a SQL table
	 * @return bool 
	 */
	public function createTable() {
		$sql = 'CREATE TABLE IF NOT EXISTS ' . PREFIX . $this->_tableName . ' (';
		foreach ($this->fields as $field) {
			$sqlField = $field->sqlModel();
			if ($sqlField !== FALSE)
				$sql .= $sqlField . ',';
		}
		$sql = substr($sql, 0, -1) . ') ENGINE=InnoDB DEFAULT CHARSET=utf8;'; /* InnoDB to support transactions */
		return (bool) PDOconnection::getDB()->exec($sql);
	}

	/**
	 * Delete a SQL table
	 * @return bool 
	 */
	public function deleteTable() {
		$sql = 'DROP TABLE ' . PREFIX . $this->_tableName;
		$path = 'modules/' . $this->_module . '/model/' . $this->_entityName;
		if(is_file($path . '.php')) 
			rename( $path . '.php', $path. '.php.back');
		if(is_file($path . \app::$config['dev']['serialization'])) 
			rename($path . '.' . \app::$config['dev']['serialization'], $path . '.' . \app::$config['dev']['serialization'] . '.back');
		return (bool) PDOconnection::getDB()->exec($sql);
	}

	/**
	 * Insert Into DB 
	 * @param array $vars
	 * @return bool|int
	 */
	public function insertInto(array $vars, $mainEntity = TRUE) {
		if($this->getRights($_SESSION['id_role']) & INSERT){
			if($mainEntity === TRUE){
				\PDOconnection::getDB()->beginTransaction();
			}
			if (isset($vars[$this->_tableName])) {
				$varsEntity = $this->beforeInsert($vars[$this->_tableName]);
			} else {
				$varsEntity = $this->beforeInsert($vars);
			}
			if($varsEntity === FALSE) return FALSE;
			$query = 'INSERT INTO ' . PREFIX . $this->_tableName . '(';
			$params = '';
			foreach ($this->fields as $name => $field) {
				if (isset($this->$name) && $field->type !== '') { /* to exclude fields from extended entities */ /* field_formasso */
					foreach ($field->getColumns() AS $column){
						$query .= $column . ',';
						$params .= ':' . $column . ',';
					}
				 }
			}
			$query = substr($query, 0, -1) . ') VALUES(' . substr($params, 0, -1) . ');';
			$sth = PDOconnection::getDB()->prepare($query);
			$values = $this->prepareValues($varsEntity);
			if (!is_array($values)){
				\PDOconnection::getDB()->rollBack();
				return $values; // FALSE : error message
			}
			$res = $sth->execute($values);
			if($res !== FALSE) {
				$lastId = $values[':' . $this->getId()->name] = \PDOconnection::getDB()->lastInsertId(); // should be before afterInsert
				$this->afterInsert($values);
				\app::dispatchEvent('afterInsert', array($values));
				if (!empty($this->_extends)) {	
					foreach ($this->_extends as $entity) {
						$id = $entity->getId()->name;
						$vars[$entity->getTableName()][$id] = $lastId;
						$resExtend = $entity->insertInto($vars, FALSE);
						if (!is_numeric($resExtend)) {
							return $resExtend;
						}
					}
				}
				if($mainEntity === TRUE){
					\PDOconnection::getDB()->commit();
				}
				return $lastId;
			}
			\PDOconnection::getDB()->rollBack();
			return FALSE;
		}else{
			throw new \Exception(t('Insert forbidden on ' . $this->_tableName, FALSE));
		}
	}

	/**
	 * Update in DB 
	 * @param array $vars
	 * @return bool
	 */
	public function update(array $vars, $mainEntity = TRUE) {
		if($this->getRights($_SESSION['id_role']) & UPDATE){
			if($mainEntity === TRUE){
				\PDOconnection::getDB()->beginTransaction();
			}
			if (isset($vars[$this->_tableName])) {
				$varsEntity = $this->beforeUpdate($vars[$this->_tableName]);
			} else {
				$varsEntity = $this->beforeUpdate($vars);
			}
			if($varsEntity === FALSE) return FALSE;
			$query = 'UPDATE ' . PREFIX . $this->_tableName . ' SET ';
			foreach ($this->fields as $name => $field) {
				if (isset($this->$name) && $field->type !== '' && isset($varsEntity[$name])) { /* to exclude fields from extended entities */  /* field_formasso */
					foreach ($field->getColumns() AS $column)
						$query .= $column . ' = :' . $column . ',';
				}
			}
			$query = substr($query, 0, -1);
			if (isset($this->_SQL['wheres'])) {
				$query .= ' WHERE ' . implode(' AND ', $this->_SQL['wheres']);
			} else {
				$query .= ' WHERE ' . $this->getId()->name . ' = :' . $this->getId()->name . ';';
			}
			$sth = PDOconnection::getDB()->prepare($query);
			$values = $this->prepareValues($varsEntity, 'UPDATE');
			if (!is_array($values)) {
				\PDOconnection::getDB()->rollBack();
				return $values; // FALSE : error message
			}
			$res = $sth->execute($values);
			unset($this->_SQL['wheres']);
			if($res !== FALSE) {
				$this->afterUpdate($values);
				\app::dispatchEvent('afterUpdate', array($values));
				if (!empty($this->_extends)) {
					foreach ($this->_extends as $entity) {
						if (empty($vars[$entity->getTableName()][$entity->getId()->name])) { /* To manage with extended entities without matched rows */
							$vars[$entity->getTableName()][$entity->getId()->name] = $vars[$this->getTableName()][$this->getId()->name];
							$resExtend = $entity->insertInto($vars, FALSE);
						} else {
							$resExtend = $entity->update($vars, FALSE);
						}
						if ($resExtend === FALSE) {
							return $resExtend;
						}
					}
				}
				if($mainEntity === TRUE){
					\PDOconnection::getDB()->commit();
				}
				return $res;
			}
			\PDOconnection::getDB()->rollBack();
			return FALSE;
		}else{
			throw new \Exception(t('Update forbidden on ' . $this->_tableName, FALSE));
		}
	}

	/**
	 * Delete in DB 
	 * @param int $id
	 * @return bool
	 */
	public function delete($id, $mainEntity = TRUE) {
		if ($this->getRights($_SESSION['id_role']) & DELETE) {
			if($mainEntity === TRUE){
				\PDOconnection::getDB()->beginTransaction();
			}
			if($this->beforeDelete($id) === FALSE) return FALSE;
			$query = 'DELETE FROM ' . PREFIX . $this->_tableName . ' WHERE ' . $this->getId()->name . ' = :id';
			$sth = PDOconnection::getDB()->prepare($query);
			$res = $sth->execute(array(':id' => $id));
			if($res !== FALSE) {
				$this->afterDelete($id);
				\app::dispatchEvent('afterDelete', array(&$this));
				if (!empty($this->_extends)) {
					foreach ($this->_extends as $entity) {
						$resExtend = $entity->delete($id, FALSE);
						if ($resExtend !== TRUE) {
							return $resExtend;
						}
					}
				}
				if($mainEntity === TRUE){
					\PDOconnection::getDB()->commit();
				}
				return $res;
			}
			\PDOconnection::getDB()->rollBack();
			return FALSE;
		} else {
			throw new \Exception(t('Delete forbidden on ' . $this->_tableName, FALSE));
		}
	}

	/**
	 * Prepare an array in order to execute insert and update prepared query
	 * @param array $vars
	 * @return array
	 */
	protected function prepareValues(array $vars, $type = 'INSERT') {
		$values = array();
		$val = 'insert';
		if ($type === 'UPDATE') {
			$idName = $this->getId()->name;
			if (isset($vars[$idName])) {
				$val = $vars[$idName];
			} elseif (is_numeric($this->$idName->value)) {
				$val = $this->$idName->value;
			} else {
				throw new \Exception(t('ID must be filled to update', FALSE));
			}
		}

		foreach ($this->fields as $name => $field) {
			if (isset($this->$name)) { /* to exclude fields from extended entities */
				if ($type === 'INSERT' || isset($vars[$name])) { // to allow only the update of some properties
					$columns = $field->getColumns();
					if (count($columns) === 1) {
						/* If the field has one column */
						$value = isset($vars[$name]) && $field->getRights($_SESSION['id_role']) & constant($type) ? $vars[$name] : '';
						$value = $field->validate($value, $val, $vars);
					} else {
						/* If the field has severals columns */
						$columnsValues = array_intersect_key($vars, array_flip($columns));
						$value = $field->validate($columnsValues, $val, $vars);
					}
				if ($value === FALSE)
					return $this->getTitle() . ' ' . $field->label . ', ' . $field->msg_error; // return error message
				else
					$field->setValue($value);

					/* If field is a field_formasso */
					if (get_class($field) !== \app::$aliasClasses['field_formasso']) {
						foreach ($columns AS $column){
							if (count($columns) === 1)
								$values[':' . $column] = $value;
							else {
								foreach ($value AS $key => $val)
									$values[':' . $key] = $val;
							}
						}
					}
				}
			}
		}
		return $values;
	}

	/**
	 * Return dataset
	 * @return array
	 */
	public function display($format = 'json') {
		if($this->getRights($_SESSION['id_role']) & DISPLAY){
			$id = $this->getId()->name;
			$displayedField = array();
			foreach ($this->fields as $name => $field) {
				if ($field->getRights($_SESSION['id_role']) & DISPLAY ) {
					$displayedField[] = $name;
				}
			}

			$dataset = array();
			if($this->buildQuery()){ 
				foreach ($this as $row) {
					$line = array();
					foreach ($displayedField as $name) {
						if($format === 'lightjson'){
							$line[] = $row->$name->value;
						}else{
							$line[$name] = $row->$name->value;
						}
					}
					$dataset[$row->$id->value] = $line;
				}
			}
			defined('JSON_PRETTY_PRINT') or define('JSON_PRETTY_PRINT', null); //compatibility
			return json_encode($dataset, JSON_PRETTY_PRINT);
			
		}else{
			throw new \Exception(t('Display forbidden on ' . $this->_tableName, FALSE));
		}
	}

	/**
	 * Get the view of adding form
	 * @param string $ajax by default False
	 * @return string
	 */
	public function getViewAddForm() {
		if($this->getRights($_SESSION['id_role']) & INSERT){
			$html = '<form method="post" action="">
			<input type="hidden" name="TOKEN" value="' . TOKEN . '" />
			<input type="hidden" name="action" value="addNewEntry">
			<input type="hidden" name="entity" value="' . $this->_module . ' - ' . $this->_entityName . '">';
			$col1 = '';
			$col2 = '';
			foreach ($this->fields as $field) {
				if ($field->visibility & INSERT) {
					$className = get_class($field);
					$field->setValue((isset($_POST[$field->name]) ? $_POST[$field->name] : FALSE));
					if ($className === \app::$aliasClasses['field_formasso'] || $className === \app::$aliasClasses['field_publication'] || $className === \app::$aliasClasses['field_boolean'] || $className === \app::$aliasClasses['field_state'] || $className === \app::$aliasClasses['field_foreignkey'] || $className === \app::$aliasClasses['field_date'] || $className === \app::$aliasClasses['field_user'])
						$col2 .= $field->form();
					else
						$col1 .= $field->form();
				} 
			}
			$html .= '<h2 style="position:relative">' . t('Add in') . ' ' . $this->_entityName . '<input style="position:absolute;right:3px;top:3px;" type="submit" value="' . t('Save') . '" name="add"></h2><div class="cols">';
			$html .= '<div class="col col1">' . $col1 . '</div>';
			if (!empty($col2))
				$html .= '<div class="col col2">' . $col2 . '</div>';
			$html .= '</div><div class="clearboth"></div></form>';
			return $html;
		}else{
			throw new \Exception(t('Insert forbidden on ' . $this->_tableName, FALSE));
		}
	}

	/**
	 * Get the view of updating form
	 * @param string $ajax by default False
	 * @return string
	 */
	public function getViewUpdateForm() {
		if($this->getRights($_SESSION['id_role']) & UPDATE){
			$html = '<form method="post" class="form" action="">
			<input type="hidden" name="TOKEN" value="' . TOKEN . '" />
			<input type="hidden" name="action" value="updateEntry">
			<input type="hidden" name="entity" value="' . $this->_module . ' - ' . $this->_entityName . '">';
			$col1 = '';
			$col2 = '';
			if($this->buildQuery()){
				foreach ($this->fields as $field) {
					if ($field->visibility & UPDATE) {
						$className = get_class($field);
						if ($className == \app::$aliasClasses['field_publication'] || $className == \app::$aliasClasses['field_formasso'] || $className == \app::$aliasClasses['field_state'] || $className === \app::$aliasClasses['field_boolean'] || $className == \app::$aliasClasses['field_foreignkey'] || $className == \app::$aliasClasses['field_date'] || $className == \app::$aliasClasses['field_user'])
							$col2 .= $field->form();
						else
							$col1 .= $field->form();
					}
				}
				$html .= '<h2 style="position:relative">' . t('Record') . ' N°' . $this->getId()->value;
				$html .= '<div style="position:absolute;right:3px;top:3px;"><input type="submit" name="update" value="' . t('Update') . '">';
				if ($this->getRights($_SESSION['id_role']) & DELETE)
					$html .= '<input type="submit" name="delete" value="' . t('Delete') . '" onclick="if(!confirm(\'' . t('Are you sure you want to delete ?') . '\')) {event.preventDefault();return FALSE;}">';

				$html .= '</div></h2><div class="cols">';
				$html .= '<div class="col col1">' . $col1 . '</div>';
				if (!empty($col2))
					$html .= '<div class="col col2">' . $col2 . '</div>';
				$html .= '</div><div class="clearboth"></div></form>';
			}
			return $html;
		 }else{
			 throw new \Exception(t('Update forbidden on ' . $this->_tableName, FALSE));
		 }
	 }
	 
	 /*
	  * Extend this entity with another entity from any module
	  * @param entity $entity
	  */
	 public function extend($entity) {
		$this->_extends[] = $entity;
		$foreignFields = $entity->getFields();
		foreach($foreignFields AS $name => &$field) {
			if(isset($this->fields[$name])){
				$tableName = $field->getTableName();
				$aliasName = $name . '_' . $tableName;
				$this->fields[$aliasName] = $field;
			}else{
				$this->fields[$name] = $field;
			}
		}
	 }

	 
	 /** *************************************************************
	  * ************************* EVENTS *************
	  * *************************************************** */

	 /**
	  * Event before select
	  */
	 public function beforeSelect() {

	 }

	 /**
	  * Event before update
	  * @param array $values
	  * @return array
	  */
	 public function beforeUpdate($values) {
		 return $values;
	 }

	 /**
	  * Event before insert
	  * @param array $values
	  * @return array
	  */
	 public function beforeInsert($values) {
		 return $values;
	 }

	 /**
	  * Event before delete
	  */
	 public function beforeDelete($id) {

	 }

	 /**
	  * Event after select
	  */
	 public function afterSelect() {

	 }

	 /**
	  * Event after update
	  * @param array $values
	  */
	 public function afterUpdate($values) {

	 }

	 /**
	  * Event after insert
	  * @param array $values
	  */
	 public function afterInsert($values) {

	 }

	 /**
	  * Event after delete
	  */
	 public function afterDelete() {

	 }

	 /**
	  * Get the entry with the given id
	  * @param integer $id
	  * @return entity object
	  */
	 public function getById($id) {
		 if (is_numeric($id)) {
			 $this->_SQL = array();
			 $this->where($this->getId()->name . ' = ' . $id);
			 return $this;
		 }else
			 throw new \Exception(t('ID isn\'t numeric'));
	 }

	 /**
	  * Get the ID
	  * @return field obejct
	  */
	 public function getId() {
		return $this->fields['id_' . $this->_entityName];
	 }

	 /**
	  * Returns the field that contains the title of an entity row (METADATA)
	  * @param integer $id
	  * @return string
	  */
	 public function getBehaviorTitle() {
		 if (!empty($this->behaviorTitle)) {
			 return $this->behaviorTitle;
		 } else {
			 foreach ($this->fields as $name => $property) {
				 if (get_class($property) === \app::$aliasClasses['field_string']) {
					 $this->behaviorTitle = $name;
					 return $name;
				 }
			 }
			 reset($this->fields);
			 return key($this->fields);
		 }
	 }

	 /**
	  * Returns the field that contains the description of an entity row (METADATA)
	  * @return string|false
	  */
	 public function getBehaviorDescription() {
		 if (!empty($this->behaviorDescription)) {
			 return $this->behaviorDescription;
		 } else {
			 foreach ($this->fields as $name => $property) {
				 if (get_class($property) === \app::$aliasClasses['field_textarea'] ||get_class($property) === \app::$aliasClasses['field_wysiwyg']) {
					 $this->behaviorDescription = $name;
					 return $name;
				 }
			 }
		 }
		 return FALSE;
	 }

	 /**
	  * Returns the field that contains the Keywords of an entity row  (METADATA)
	  * @return string|false
	  */
	 public function getBehaviorKeywords() {
		 if (!empty($this->behaviorKeywords)) {
			 return $this->behaviorKeywords;
		 } else {
			 $this->behaviorKeywords = $this->getBehaviorDescription();
			 return $this->behaviorKeywords;
		 }
	 }

	 /**
	  * Returns the field that contains the images of an entity row  (METADATA)
	  * @return string|false
	  */
	 public function getBehaviorImage() {
		 if (!empty($this->behaviorImage)) {
			 return $this->behaviorImage;
		 } else {
			 foreach ($this->fields as $name => $property) {
				 if (get_class($property) === \app::$aliasClasses['field_image']) {
					 $this->behaviorImage = $name;
					 return $name;
				 }
			 }
		 }
		 return FALSE;
	 }

	 /**
	  * Return the field that describes the author of an entity row  (METADATA)
	  * @return string|false
	  */
	 public function getBehaviorAuthor() {
		 if (!empty($this->behaviorAuthor)) {
			 return $this->behaviorAuthor;
		 } else {
			 foreach ($this->fields as $name => $property) {
				 if (get_class($property) === \app::$aliasClasses['field_user']) {
					 $this->behaviorAuthor = $name;
					 return $name;
				 }
			 }
		 }
		 return FALSE;
	 }

	 /**
	  * Get field
	  * @param string $name
	  * @return field|false
	  */
	 public function getField($name) {
		return $this->$name;
	 }

	 /**
	  * Put content in file.obj
	  * @return string
	  */
	 public function save() {
		 return \tools::serialize('modules/' . $this->_module . '/model/' . $this->_entityName, $this);
	 }

	 /**
	  * Clean entity for storing
	  */
	 public function __sleep() {
		$properties = get_object_vars($this);
		unset($properties['_SQL']);
		unset($properties['fields']);
		unset($properties['_extends']);
		unset($properties['_tableTitle']); /* Todo remove */
		if(empty($this->behaviorTitle)){
			unset($properties['behaviorTitle']);
		}
		if(empty($this->behaviorDescription)){
			unset($properties['behaviorDescription']);
		}
		if(empty($this->behaviorKeywords)){
			unset($properties['behaviorKeywords']);
		}
		if(empty($this->behaviorImage)){
			unset($properties['behaviorImage']);
		}
		unset($this->extends);
		$fields = array_keys($properties);
		return $fields;
	 }

	 /**
	  * Select in DB 
	  * @param $clauses
	  * @return entity object
	  */
	 public function select($clause = '', $hidden = false) {
		 $this->_SQL = array(); /* begin new request */
		 $selects = explode(',', $clause);
		 if (!empty($clause)) {
			 foreach ($selects AS $select) {
				 $select = trim($select);
				 $this->_SQL['selects'][$select] = $select;
			 }
		 }
		 return $this;
	 }
	 
	 
	 public function __wakeup() {
		 
		 /* Get fields and separate them from other props */
		$fields = get_object_vars($this);
		 foreach ($fields as $name => &$property) {
			 if ($property instanceof \field) {
				/* Insert an entity reference in each field */
				$property->setEntity($this);
				/* Save field in fields array - usefull to extend(), getFields(), getId() methods, also idem to class view structure   */
				$this->fields[$name] = $property;
			 }
		 }
		 
		 \app::dispatchEvent('wakeupEntity', array($this->_tableName, &$this)); /* mainly if antoher module wants to extend this entity */
	 }

}
