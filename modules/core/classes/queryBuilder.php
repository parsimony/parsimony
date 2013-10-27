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
 *  @authors Julien Gras et Benoît Lorillot
 *  @copyright  Julien Gras et Benoît Lorillot
 *  @version  Release: 1.0
 * @category  Parsimony
 * @package core\classes
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
	 * Get SQL settings of the query
	 * @return array of SQL settings of the query
	 */
	public function getSQL() {
		return $this->_SQL;
	}

	 /**
	  * Set a WHERE clause
	  * @param string $property
	  * @param string $condition
	  * @return view object
	  */
	 public function where($condition) {
		 $this->_SQL['wheres'][] = $condition;
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
	 * @return view
	 */
	public function groupBy($property) {
		$this->_SQL['groupBys'][] = $property;
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
		if ($function === 'groupby') {
			$this->groupBy($property);
		} else {
			list($tableName, $propertyName) = explode('.', $property);
			$alias = $propertyName . '_nb';
			$this->_SQL['selects'][$alias] = $function . '(' . $property . ') AS ' . $alias; 
			list($module, $entity) = explode('_', $tableName, 2);
			$this->fields[$alias] = new \field_ident ($module, $entity, $propertyName); /* $propertyName for name to keep his origin sql name  */
			$this->fields[$alias]->setLabel($alias);
			if(!isset($this->fields[$propertyName])) $this->fields[$alias]->setVisibility(0); /* no display in datagrid */
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
		 if($this->buildQuery()){
			 return $this->_SQL['stmt']->fetchAll($fetchStyle);
		 }else{
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
	 * Build the query and his PDO statement with SQL infos already set to this object
	 * @return bool
	 */
	public function buildQuery($forceRebuild = FALSE) {
		
		if (!isset($this->_SQL['stmt']) || $forceRebuild) { /* exec query once a page load */
			
			\app::dispatchEvent('beforeBuildQuery', array());

			/* SELECT */
			$query = 'SELECT ';
			if(isset($this->_tableName)){ /* only for entity, to define selects */
				$this->_SQL['selects']['*'] = '*';
				foreach ($this->getFields() as $field) {
					$id = app::getModule($field->module)->getEntity($field->entity)->getId()->name;
					if (get_class($field) === \app::$aliasClasses['field_formasso']) {
						$currentEntity = \app::getModule($field->module)->getEntity($field->entity);
						$foreignEntity = \app::getModule($field->module)->getEntity($field->entity_foreign);
						$idNameForeignEntity = $foreignEntity->getId()->name;
						$this->_SQL['selects'][$field->name] = 'GROUP_CONCAT(CAST(CONCAT(' . $field->module . '_' . $field->entity_foreign . '.' . $idNameForeignEntity . ',\'||\',' . $field->module . '_' . $field->entity_foreign . '.' . $foreignEntity->getBehaviorTitle() . ') AS CHAR)) AS ' . $field->name;
						$this->groupBy($field->module . '_' . $field->entity . '.' . $currentEntity->getId()->name);
						$this->join($field->module . '_' . $field->entity . '.' . $currentEntity->getId()->name, $field->module . '_' . $field->entity_asso . '.' . $currentEntity->getId()->name, 'inner join');
						$this->join($field->module . '_' . $field->entity_asso . '.' . $idNameForeignEntity, $field->module . '_' . $field->entity_foreign . '.' . $idNameForeignEntity, 'inner join');
					} elseif ($this->getField($id) === FALSE) {
						$this->select($field->module . '_' . $field->entity . '.' . $id, TRUE);
					}
				}
				$this->_SQL['froms'][$this->_tableName] = $this->_tableName; /* FROM for entity */
			}
			$query .= implode(',', $this->_SQL['selects']);


			/* FROM */
			if (count($this->_SQL['froms']) === 1 && empty($this->_SQL['joins'])) {
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
			$vars = array(); // init here for pagination
			if (isset($this->_SQL['wheres'])) {
				$wheres = array();
				foreach ($this->_SQL['wheres'] AS $where) {
					if(strstr($where, ':') !== FALSE){
						preg_match_all("/\:([^\s%,\)]*)/", $where, $matches);
						foreach($matches[1] AS $param){
							$value = \app::$request->getParam($param);
							if($value !== FALSE){
								if(is_array($value)){
									$nb = count($value);
									$str = array();
									for ($i = 0; $i < $nb; $i++) {
										$str[] = ':'.$param.$i;
										$vars[':'.$param.$i] = $value[$i];
									}
									$where = str_replace(':'.$param, implode(',',$str), $where);
								}else{
									$vars[':'.$param] = strlen($value) > 0 ? $value : '';
								}
							}
						}
					}
					// Frame the "where" if several sql conditions
					if(strstr($where,'&&') || strstr($where,'||') || stristr($where,' or ') || stristr($where,' and ')) $wheres[] = '(' . $where .')';
					else $wheres[] = $where;
				}
				if(!empty($wheres)) $query .= ' WHERE ' . implode(' AND ', $wheres);
			}

			/* GROUP BY */
			if (isset($this->_SQL['groupBys'])) {
				$query .= ' GROUP BY ' . implode(' ,', $this->_SQL['groupBys']);
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
				$query .= ' '; /* tip to replace from table */
				foreach ($this->_SQL['froms'] AS $table) {
					$query = str_replace($table . ' ', PREFIX . $table . ' ', $query);
					$query = str_replace($table . '.', PREFIX . $table . '.', $query);
				}
			}

			/* LIMIT */
			if (isset($this->_SQL['limit'])) {
				$limit = ' LIMIT 0,' . $this->_SQL['limit'];
				if (isset($this->_SQL['pagination']) && $this->_SQL['pagination'] !== FALSE) {
					$this->_SQL['pagination'] = new \pagination($query, $this->_SQL['limit'], $vars);
					$start = $this->_SQL['pagination']->getCurrentPage() * $this->_SQL['limit'] - $this->_SQL['limit'];
					$limit = ' LIMIT ' . $start . ',' . $this->_SQL['limit'];
				}
				$query .= $limit;
			}
			$this->_SQL['query'] = $query;

			/* EXEC query */
			if(!empty($vars)){
				$this->_SQL['stmt'] = \PDOconnection::getDB()->prepare($query);
				$this->_SQL['stmt']->setFetchMode(\PDO::FETCH_INTO, $this);
				$this->_SQL['stmt']->execute($vars);
			}else{
				$this->_SQL['stmt'] = \PDOconnection::getDB()->query($query, \PDO::FETCH_INTO, $this);
			}
		}
		return is_object($this->_SQL['stmt']);
	}
	
	/* Iterator interface */
	
	/**
	  * Rewind the cursor to the first row
	  */
	 public function rewind() {
		 if ($this->buildQuery()) {
			if (!isset($this->_SQL['firstFetch'])) { /* first fetch could be exec by a rewind or isEmpty */
				$this->_SQL['firstFetch'] = $this->_SQL['stmt']->fetch();
			}
			if ($this->_SQL['firstFetch'] !== FALSE) {
				return $this->_SQL['position'] = 0;
			}
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
		 if($this->_SQL['position'] !== FALSE){
			 return TRUE;
		 }else{
			 if(method_exists($this, 'afterSelect')){
				 $this->afterSelect();
			 }
			 return FALSE;
		 }
	 }
	 
	 public function isEmpty() {
		if ($this->buildQuery()) {
			if (!isset($this->_SQL['firstFetch'])) {
				$this->_SQL['firstFetch'] = $this->_SQL['stmt']->fetch();
			}
			if (is_object($this->_SQL['stmt']))
				return !(bool) $this->_SQL['firstFetch'];
		}
		return TRUE;
	}

}

?>