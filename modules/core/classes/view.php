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
 * @package core\classes
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace core\classes;

/**
 * View Class 
 * Manage views of SQL request 
 */

class view extends queryBuilder implements \Iterator {

	/**
	 * @var array $entities
	 */
	public $entities = array();

	/**
	 * @var array of fields 
	 */
	protected $fields = array();

	/**
	 * !!TODO REMOVE!!
	 * @var array of SQL fields in order to build SQL query 
	 */
	protected $SQL = array();

	/**
	 * @var array $displayView
	 */
	public $displayView = array();

	public function __wakeup() {
		/* !!TODO REMOVE!! */
		if(isset($this->SQL) && !empty($this->SQL)){
			$this->_SQL = $this->SQL;
			unset($this->SQL);
		}
		/* !!TODO REMOVE!! */
		
		/* 
		 * Load fields objects and inject a reference to its entity parent 
		 * Create a public property for each field that links to the field value
		 */
		foreach ($this->fields as $key => &$field) {
			extract($field);
			$name = $module.'_'.$entity;
			if(!isset($this->entities[$name])){
				$this->entities[$name] = app::getModule($module)->getEntity($entity);
				$this->entities[$name]->prepareFieldsForDisplay();
			}
			$field = $this->entities[$name]->getField($key);
			$this->{$key} = &$field->getValue();
			$field->row = $this->entities[$name];
		}
	}

	/**
	 * Get a field
	 * @param string $name
	 * @param field $args
	 * @return string
	 */
	public function __call($name, $args) {
		return $this->fields[$name];
	}

	/**
	 * Set a field
	 * @param string $name the property to update
	 * @param field $fieldValue 
	 */
	public function setField($name, $field) { 
		$this->fields[$name] = $field;
	}

	/**
	 * Get a field
	 * @param string $name of the field
	 * @return field
	 */
	public function getField($name) {
		 if (isset($this->fields[$name])) {
			 return $this->fields[$name];
		 }
		 return FALSE;
	}

	/**
	 * Get all fields
	 * @return array of fields
	 */
	public function getFields() {
		return $this->fields;
	}

	/**
	 * Init view from an array
	 * @param array $properties
	 * @param array $joins
	 * @return view
	 */
	public function initFromArray(array $properties, array $joins = array()) {
		if (!empty($joins)) {
			foreach ($joins AS $p) {
				$this->join($p['propertyLeft'], $p['propertyRight'], $p['type']);
			}
		}

		foreach ($properties AS $p) {
			/* select */
			$this->select($p['table'].'.'.$p['property']);
			
			/* From */
			$this->from($p['table']);
			
			/* where */
			if (isset($p['where']) && !empty($p['where'])) {
				$this->where($p['table'] . '.' . $p['property'] .' '. $p['where']);
			}
			/* or */
			if (isset($p['or']) && !empty($p['or'])) {
				$this->where($p['table'] . '.' . $p['property'] .' '. $p['or']);
			}
			/* aggregate */
			if (isset($p['aggregate']) && !empty($p['aggregate'])) {
				$this->aggregate($p['table'] . '.' . $p['property'], $p['aggregate']);
			}
			/* order */
			if (isset($p['order']) && !empty($p['order'])) {
				$this->order($p['table'] . '.' . $p['property'], $p['order']);
			}
		}

		/* Simulate a part of __sleep() */
		foreach ($this->fields as $key => $field) {
			if(is_object($field)) $this->fields[$key] = array('module' => $field->module, 'entity' => $field->entity, 'fieldName' => $field->name);
		}
		$this->__wakeup();
		return $this;
	}

	/**
	 * Return properties in order to serialize it
	 * @return array of SQL properties
	 */
	public function __sleep() {
		foreach ($this->fields as $key => $field) {
			if(is_object($field)) $this->fields[$key] = array('module' => $field->module, 'entity' => $field->entity, 'fieldName' => $field->name);
		}
		unset($this->_SQL['entities']);
		unset($this->_SQL['displayView']);
		unset($this->_SQL['valid']);
		unset($this->_SQL['stmt']);
		unset($this->_SQL['position']);
		unset($this->_SQL['firstFetch']);
		return array('fields', '_SQL');
	}

}

?>
