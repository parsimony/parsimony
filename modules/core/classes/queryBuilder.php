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
 * @copyright Julien Gras et Benoît Lorillot
 * 
 * @category Parsimony
 * @package core\classes
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */


namespace core\classes;

/**
 * PDOconnection Class 
 * Provides an access for databases building and storing a PHP Data Object.
 */
class queryBuilder {
	
	/**
	 * @var array of SQL fields in order to build SQL query
	 */
	protected $_SQL = array();
	
	/**
	 * @var array of fields 
	 */
	protected $fields = array();

	/**
	 * Get SQL settings of the query
	 * @return array of SQL settings of the query
	 */
	public function getSQL() {
		return $this->_SQL;
	}
	
	/**
	 * Set SQL settings of the query
	 * @param array $SQL
	 */
	public function setSQL($SQL) {
		$this->_SQL = $SQL;
	}

	 /**
	  * Set a WHERE clause
	  * @param string $condition
	  * @return view object
	  */
	 public function where($condition) {
		 $this->_SQL['wheres'][] = $condition;
		 return $this;
	 }
	 
	 /**
	  * Set a HAVING clause
	  * @param string $condition
	  * @return view object
	  */
	 public function having($condition) {
		 $this->_SQL['havings'][] = $condition;
		 return $this;
	 }

	 /**
	  * Limit the results of Query 
	  * @param string $limit
	  * @return entity object 
	  */
	 public function limit($limit) {
		 $this->_SQL['limit'] = $limit;
		 return $this;
	 }

	 /**
	  * Set Order of the query
	  * @param string $property
	  * @param string $order
	  * @return view object
	  */
	 public function order($property, $order) {
		 $this->_SQL['orders'][$property] = $order;
		 return $this;
	 }
	 
	 
	/**
	 * Group the query by a property
	 * @param string $property name
	 * @param string $clause optional group by with functions like month(..)
	 * @param string $function function
	 * @return view
	 */
	public function groupBy($property, $clause = FALSE, $function = 'count') {
		if($clause === FALSE) {
			$clause = $property;
		}
		$this->_SQL['groupBys'][$property] = $clause;
		$this->aggregate($property, $function); /* to add property in select and count */
		return $this;
	}
	
	/**
	 * Set properties to select array
	 * @param string $clause list of properties
	 * @param string $hidden optional
	 * @return view object
	 */
	public function select($clause, $hidden = FALSE) {
		$selects = explode(',', $clause);
		foreach ($selects AS $select) {
			$select = trim($select);
			if(strstr($select, '.') === FALSE) return FALSE; // must have tablename->.<-property
			list($tableName, $propertyName) = explode('.',$select);// extract property name
			if(isset($this->fields[$propertyName])){ // if property name is already use, set an alias
				$alias = $propertyName . '_' . $tableName;
				$this->_SQL['selects'][$alias] = $select . ' AS ' . $alias;
			}else {
				$alias = $propertyName;
				$this->_SQL['selects'][$propertyName] = $select;
			}
			$obj = \app::getModule(strstr($tableName, '_', true))->getEntity(substr(strstr($tableName, '_'), 1))->getField($propertyName);
			if ($hidden) {
				$obj->setVisibility(0); // keep this field invisible
			}
			
			$columns = $obj->getColumns();
			if(count($columns) > 1){
				unset($columns[0]);
				foreach ($columns as $name) {
					$this->_SQL['selects'][$name] = $name;
				}
			}
			
			$this->setField($alias, $obj);
		}
		return $this;
	}
	
	/**
	 * Set the FROM clause
	 * @param string $table
	 * @return view object
	 */
	public function from($table) {
		$this->_SQL['froms'][$table] = $table;
		return $this;
	}

	/**
	 * SQL Aggregate Functions
	 * @param string $property 
	 * @param string $function
	 * @return view object
	 */
	public function aggregate($property, $function) {
		$explProp = explode('.', $property);
		if(isset($explProp[1])) $aliasProperty = $explProp[1];
		else $aliasProperty = $explProp[0];
		$alias = $aliasProperty . '_nb';
		$this->_SQL['selects'][$alias] = $function . '(' . $property . ') AS ' . $alias; 
		
		if (isset($this->fields)) { /* Only for views */

			$this->fields[$alias] = new \core\fields\alias ($alias, array('label' => $alias , 'calculation' =>  $function . '(' . $property . ')' ));
			
		}
		return $this;
	}

	 /**
	 * Set a table to Join
	 * @param string $propertyLeft ID of left table
	 * @param string $propertyRight ID of right table
	 * @param string $type of join
	 * @return view object
	 */
	public function join($propertyLeft, $propertyRight, $type = 'left outer join') {
		$this->_SQL['joins'][$propertyLeft.'_'.$propertyRight] = array('propertyLeft' => $propertyLeft, 'propertyRight' => $propertyRight, 'type' => $type);
		return $this;
	}
	
    /**
	 * Enable or disable pagination
	 * @param bool $limit
	 * @return view object
	 */
	public function setPagination($state) {
		$this->_SQL['pagination'] = $state;
		return $this;
	}

	 /**
	  * Get pagination
	  * @return pagination object
	  */
	public function getPagination() {
	   if (isset($this->_SQL['pagination']))
		   return $this->_SQL['pagination'];
	   else
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
	  * Return first data row
	  * @return entity object
	  */
	 public function fetch() {
		 foreach ($this as $obj) {
			 return $obj;
		 }
	 }
	 
	 /**
	  * Returns an array containing all of the remaining rows in the result set
	  * @param int $fetchStyle
	  * @return array
	  */
	 public function fetchAll($fetchStyle = \PDO::FETCH_INTO) {
		if ($this->buildQuery(true)) {
			$fetchAll = $this->_SQL['stmt']->fetchAll($fetchStyle);
			$this->clearQuery();
			return $fetchAll;
		} else {
			unset($this->_SQL['firstFetch']); /* allow to re-exec query */
			return FALSE;
		}
	}

	/**
	  * Return the PDOStatement object
	  * @return PDOStatement
	  */
	 public function getStatement() {
		 $this->buildQuery();
		 return $this->_SQL['stmt'];
	 }
	 
	 /**
	  * Evaluate conditions before concatenation in where or having
	  * @param string $condition
	  * Return the condition before SQL concatenation
	  * @return string $condition
	  */
	 public function evaluateConditions($condition) {
		if (strstr($condition, ':') !== FALSE) {
			preg_match_all("/\:([^\s%,\)]*)/", $condition, $matches);
			foreach ($matches[1] AS $param) {
				$value = \app::$request->getParam($param);
				if ($value !== FALSE) {
					if (is_array($value)) {
						$nb = count($value);
						$str = array();
						for ($i = 0; $i < $nb; $i++) {
							$str[] = ':' . $param . $i;
							$this->_SQL['vars'][':' . $param . $i] = $value[$i];
						}
						$condition = str_replace(':' . $param, implode(',', $str), $condition);
					} else {
						$this->_SQL['vars'][':' . $param] = strlen($value) > 0 ? $value : '';
					}
				} else {
					$this->_SQL['vars'][':' . $param] = '';
				}
			}
		}
		return $condition;
	}

	/**
	 * Build the query and his PDO statement with SQL infos already set to this object
	 * @return bool
	 */
	public function buildQuery($forceRebuild = FALSE) {
		
		if (!isset($this->_SQL['stmt']) || $forceRebuild) { /* exec query once a page load */

			\app::dispatchEvent('beforeBuildQuery', array());

			/* SELECT */
			$query = 'SELECT ';
			if ($this instanceof \entity) { /* only for entity, to define defaults selects && from */
				$this->beforeSelect();
				if(empty($this->_SQL['selects'])){
					$this->_SQL['selects'][$this->_tableName . '.*'] = $this->_tableName . '.*';
				}
				$this->_SQL['froms'][$this->_tableName] = $this->_tableName; /* FROM for entity */
				/*  extends */
				if(!empty($this->_extends)){
					foreach($this->_extends AS $entity) {
						$foreignFields = $entity->getFields();
						foreach($foreignFields AS $name => &$field) {
							if($name !== $field->name){ /* detect alias */
								$tableName = $field->getTableName();
								$aliasName = $name . '_' . $tableName;
								$this->_SQL['selects'][$aliasName] = $tableName . '.' . $name . ' AS ' . $aliasName;
							}else{
								$this->_SQL['selects'][$name] = $field->getTableName() . '.' . $name; /* best than "table.*" which bug when using alias ( duplicate) */
							}
						}
						$foreignTableName = str_replace('\\model\\', '_', get_class($entity));
						$this->join($this->_tableName . '.' . $this->getId()->name, $foreignTableName . '.' . $entity->getId()->name, 'left outer join');
					}
				}
			}
			foreach ($this->getFields() as $field) {
				/* TODO IMPROVE */
				if(!$field instanceof \core\fields\alias){
					$module = $field->entity->getModule();
					$entity = $field->entity->getName();
					$id = $field->entity->getId()->name;
					if ($field instanceof \field_formasso) {
						$cutForeign = explode('_', $field->entity_foreign, 2);
						$foreignEntity = \app::getModule($cutForeign[0])->getEntity($cutForeign[1]);
						$idNameForeignEntity = $foreignEntity->getId()->name;
						$this->_SQL['selects'][$field->name] = ' CONCAT(  \'{\', GROUP_CONCAT(CONCAT(\'"\', ' . $field->entity_foreign . '.' . $idNameForeignEntity . ' , \'"\',\':"\',' . $field->entity_foreign . '.' . $foreignEntity->getBehaviorTitle() . ', \'"\')), \'}\') AS ' . $field->name;
						$this->groupBy($module . '_' . $entity . '.' . $id);
						$this->join($module . '_' . $entity . '.' . $id, $field->entity_asso . '.' . $field->entity->getId()->name, 'left outer join');
						$this->join($field->entity_asso . '.' . $idNameForeignEntity, $field->entity_foreign . '.' . $idNameForeignEntity, 'left outer join');
					} elseif ($this->getField($id) === FALSE) {
						$this->select($module . '_' . $entity . '.' . $id, TRUE);
					}
				}
			}
			$query .= implode(',', $this->_SQL['selects']);


			/* FROM */
			if (empty($this->_SQL['joins'])) {
				$query .= ' FROM ' . reset($this->_SQL['froms']);
			} else {
				$firstTable = reset($this->_SQL['joins']);
				$tableLeft = strstr($firstTable['propertyLeft'], '.', true);
				$query .= ' FROM ' . $tableLeft;
				$this->_SQL['froms'][$tableLeft] = $tableLeft; //to prefix $tableLeft
				foreach ($this->_SQL['joins'] AS $join) {
					$tableRight = strstr($join['propertyRight'], '.', true);
					$this->_SQL['froms'][$tableRight] = $tableRight; //to prefix $tableRight
					$query .= ' ' . $join['type'] . ' ' . $tableRight . ' ON ' . $join['propertyLeft'] . ' = ' . $join['propertyRight'];
				}
			}

			/* WHERE */
			$this->_SQL['vars'] = array(); // init here for pagination
			if (isset($this->_SQL['wheres'])) {
				$wheres = array();
				foreach ($this->_SQL['wheres'] AS $where) {
					
					// Frame the "where" if several sql conditions
					/* For the record if(strstr($where,'&&') || strstr($where,'||') || stristr($where,' or ') || stristr($where,' and ')) $wheres[] = */
					 $wheres[] = '(' .  $this->evaluateConditions($where) .')';
					
				}
				if(!empty($wheres)) $query .= ' WHERE ' . implode(' AND ', $wheres);
			}

			/* GROUP BY */
			if (isset($this->_SQL['groupBys'])) {
				$query .= ' GROUP BY ' . implode(' ,', $this->_SQL['groupBys']);
			}
			
			/* HAVING */
			if (isset($this->_SQL['havings'])) {
				$havings = array();
				foreach ($this->_SQL['havings'] AS $having) {
					
					// Frame the "having" if several sql conditions
					 $havings[] = '(' .  $this->evaluateConditions($having) .')';
					
				}
				if(!empty($havings)) $query .= ' HAVING ' . implode(' AND ', $havings);
			}
			
			/* ORDER */
			if (isset($this->_SQL['orders'])) {
				$orders = array();
				foreach ($this->_SQL['orders'] AS $property => $order) {
					$orders[] = $property . ' ' . $order;
				}
				$query .= ' ORDER BY ' . implode(',', $orders);
			}

			/* DB PREFIX */
			if (PREFIX !== '') {/* must be before pagination */
				$query .= ' '; /* tip to replace table name */
				foreach ($this->_SQL['froms'] AS $table) {
					$query = preg_replace('/([,\s\(])' . $table . '([\.\s])/', '$1' . PREFIX . $table . '$2', $query);
				}
			}

			/* LIMIT */
			if (isset($this->_SQL['limit'])) {
				$limit = ' LIMIT 0,' . $this->_SQL['limit'];
				if (isset($this->_SQL['pagination']) && $this->_SQL['pagination'] !== FALSE) {
					$this->_SQL['pagination'] = new \pagination($query, $this->_SQL['limit'], $this->_SQL['vars']);
					$start = $this->_SQL['pagination']->getCurrentPage() * $this->_SQL['limit'] - $this->_SQL['limit'];
					$limit = ' LIMIT ' . $start . ',' . $this->_SQL['limit'];
				}
				$query .= $limit;
			}
			$this->_SQL['query'] = $query;
			
		}
		
		/* EXEC query */
		if(!empty($this->_SQL['vars'])){
			$this->_SQL['stmt'] = \PDOconnection::getDB()->prepare($this->_SQL['query']);
			$this->_SQL['stmt']->setFetchMode(\PDO::FETCH_INTO, $this);
			$this->_SQL['stmt']->execute($this->_SQL['vars']);
		}else{
			$this->_SQL['stmt'] = \PDOconnection::getDB()->query($this->_SQL['query'], \PDO::FETCH_INTO, $this);
		}
		
		return $this->_SQL['stmt'];
	}
	
	/**
	 * Clear values of fields, for future queries with this object ; allow to re-exec query
	 */
	public function clearQuery() {
		foreach ($this->getFields() as $field) {
			$field->setValue(NULL);
		}
		if($this instanceof \entity) {
			if (isset($this->_SQL['pagination'])) { /* keep pagination to allow to display pagination after the query */
				$pagination = $this->_SQL['pagination'];
				$this->_SQL = array('pagination' => $pagination);
			} else {
				$this->_SQL = array();
			}
		}
	}
	
	/** 
	  * ************************* Iterator interface  *************
	  *  */
	
	/**
	  * Rewind the cursor to the first row
	  */
	 public function rewind() {
		if (!isset($this->_SQL['firstFetch'])) {
			if ($this->buildQuery() !== FALSE) {
				$this->_SQL['firstFetch'] = $this->_SQL['stmt']->fetch(); /* first fetch could be used by a rewind or isEmpty */
			} else {
				$this->_SQL['firstFetch'] = FALSE;
			}
		}
		if ($this->_SQL['firstFetch'] !== FALSE) {
			return $this->_SQL['position'] = 0;
		}
		$this->_SQL['position'] = FALSE;
	}

	/**
	  * Get the current row
	  * @return entity object
	  */
	 public function current() {
		 return $this;
	 }

	 /**
	  * Get the position of cursor
	  * @return integer
	  */
	 public function key() {
		 return $this->_SQL['position'];
	 }

	 /**
	  * Move forward to the next row 
	  */
	 public function next() {
		 if($this->_SQL['stmt']->fetch() !== FALSE){
			 $this->_SQL['position']++;
		 }else{
			 $this->_SQL['position'] = FALSE;
		 }
	 }
	 
	/**
	  * Check if current position is valid
	  * @return object|false
	  */
	 public function valid() {
		 if ($this->_SQL['position'] !== FALSE) {
			return TRUE;
		} else {
			$this->afterSelect();
			return FALSE;
		}
	}
	 
	 public function isEmpty() {
		 if(!isset($this->_SQL['firstFetch'])){
			if ($this->buildQuery() !== FALSE) {
				$this->_SQL['firstFetch'] = $this->_SQL['stmt']->fetch(); /* first fetch could be used by a rewind or isEmpty */
			} else {
				$this->_SQL['firstFetch'] = FALSE;
			}
		}
		return !(bool) $this->_SQL['firstFetch'];
	}

	/** 
	  * ************************* EVENTS *************
	  *  */
	
	/**
	  * Event before select
	  */
	 public function beforeSelect() {

	 }
	
	/**
	  * Event after select
	  */
	 public function afterSelect() {
		 $this->clearQuery();
	 }

}
