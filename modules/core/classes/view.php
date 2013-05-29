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

class view implements \Iterator {
    
    /**
     * @var array $entities
     */
    public $entities = array();

    /**
     * @var array of fields 
     */
    protected $fields = array();

    /**
     * @var array of SQL fields in order to build SQL query
     */
    protected $SQL = array();
    
    /**
     * @var array $displayView
     */
    public $displayView = array();

    public function __wakeup() {
        foreach ($this->fields as $key => &$field) {
            extract($field);
            $name = $module.'_'.$entity;
            if(!isset($this->entities[$name])){
                $this->entities[$name] = app::getModule($module)->getEntity($entity);
            }
            $field = $this->entities[$name]->getField($fieldName);
            $this->{$key} = &$field->getValue(); // 
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
        return $this->fields[$name];
    }

    /**
     * Get all fields
     * @return array of fields
     */
    public function getFields() {
        return $this->fields;
    }
    
    /**
     * Get SQL settings of the query
     * @return array of SQL settings of the query
     */
    public function getSQL() {
        return $this->SQL;
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
		$alias = $propertyName . '_'.$tableName;
		$this->SQL['selects'][$alias] = $select.' AS '.$alias;
	    }else {
		$alias = $propertyName;
		$this->SQL['selects'][$propertyName] = $select;
	    }
            $obj = \app::getModule(strstr($tableName, '_', true))->getEntity(substr(strstr($tableName, '_'), 1))->__get($propertyName);
            if($hidden) {
                $obj->setVisibility(0); // keep this field invisible
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
        $this->SQL['froms'][$table] = $table;
        return $this;
    }

    /**
     * Set a table to Join
     * @param string $propertyLeft ID of left table
     * @param string $propertyRight ID of right table
     * @param string $type of join
     * @return view object
     */
    public function join($propertyLeft, $propertyRight, $type) {
        $this->SQL['joins'][$propertyLeft.'_'.$propertyRight] = array('propertyLeft' => $propertyLeft, 'propertyRight' => $propertyRight, 'type' => $type);
        return $this;
    }

    /**
     * Set a WHERE clause
     * @param string $property
     * @param string $condition
     * @return view object
     */
    public function where($condition) {
        $this->SQL['wheres'][] = $condition;
        return $this;
    }

    /**
     * Set Order of the query
     * @param string $property
     * @param string $order
     * @return view object
     */
    public function order($property, $order) {
        $this->SQL['orders'][$property] = $order;
        return $this;
    }

    /**
     * Group the query by a property
     * @param string $property name
     * @return view
     */
    public function groupBy($property) {
        $this->SQL['groupBys'][] = $property;
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
            $this->SQL['selects'][$alias] = $function . '(' . $property . ') AS ' . $alias; 
            list($module, $entity) = explode('_', $tableName, 2);
            $this->fields[$alias] = new \field_ident ($module, $entity, $alias);
            $this->fields[$alias]->setLabel($alias);
            if(!isset($this->fields[$propertyName])) $this->fields[$alias]->setVisibility(0);
            else {
                unset($this->SQL['selects'][$propertyName]) ;
                unset($this->fields[$propertyName]);
            }
        }
        return $this;
    }

    /**
     * Limit the number of result rows
     * @param integer $limit
     * @return view object
     */
    public function limit($limit) {
        $this->SQL['limit'] = $limit;
        return $this;
    }
    
    /**
     * Enable or disable pagination
     * @param bool $limit
     * @return view object
     */
    public function setPagination($state) {
        $this->SQL['pagination'] = $state;
        return $this;
    }

    /**
     * Display Pagination
     * @return pagination|false
     */
    public function getPagination() {
        if (isset($this->SQL['pagination']))
            return $this->SQL['pagination'];
        else
            return FALSE;
    }

    /**
     * Build the query and his PDO statement with SQL infos already set to this object
     * @return bool
     */
    public function buildQuery() {
        \app::dispatchEvent('beforeBuildQuery', array());
        $query = 'SELECT ';
        foreach ($this->getFields() as $field) {
            $id = app::getModule($field->module)->getEntity($field->entity)->getId()->name;
            if(get_class($field) === \app::$aliasClasses['field_formasso']){
                $currentEntity = \app::getModule($field->module)->getEntity($field->entity);
                $foreignEntity = \app::getModule($field->module)->getEntity($field->entity_foreign);
                $idNameForeignEntity = $foreignEntity->getId()->name;
                $this->SQL['selects'][$field->name] = 'GROUP_CONCAT(CAST(CONCAT('. $field->module.'_'. $field->entity_foreign.'.'.$idNameForeignEntity . ',\'||\','. $field->module.'_'. $field->entity_foreign.'.'.$foreignEntity->getBehaviorTitle() . ') AS CHAR)) AS ' . $field->name;
                $this->groupBy($field->module.'_'.$field->entity.'.'.$currentEntity->getId()->name);
                $this->join($field->module.'_'.$field->entity.'.'.$currentEntity->getId()->name, $field->module.'_'.$field->entity_asso.'.'.$currentEntity->getId()->name, 'inner join');
                $this->join($field->module.'_'.$field->entity_asso.'.'.$idNameForeignEntity, $field->module.'_'.$field->entity_foreign.'.'.$idNameForeignEntity, 'inner join');
            } elseif (!isset($this->fields[$id])) {
                $this->select($field->module . '_' . $field->entity . '.' . $id, TRUE);
            }
        }
        $query .= implode(',',$this->SQL['selects']);
        if (count($this->SQL['froms']) === 1 && empty($this->SQL['joins'])) {
            $query .= ' FROM ' . reset($this->SQL['froms']);
        } else {
            $firstTable = reset($this->SQL['joins']);
            $query .= ' FROM '.strstr($firstTable['propertyLeft'], '.', true);
            foreach ($this->SQL['joins'] AS $join) {
                $query .= ' ' . $join['type'] . ' ' . strstr($join['propertyRight'], '.', true) . ' ON ' . $join['propertyLeft'] . ' = ' . $join['propertyRight'];
            }
        }
       $vars = array(); // init here for pagination
       if (isset($this->SQL['wheres'])) {
            $wheres = array();
            foreach ($this->SQL['wheres'] AS $where) {
                if(strstr($where, ':') !== FALSE){
                    preg_match_all("/\:([^\s%,\)]*)/", $where, $matches);
                    foreach($matches[1] AS $param){
                        $value = \app::$request->getParam($param);
                        if(!empty($value)){
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
                $wheres[] = $where;
            }
            if(!empty($wheres)) $query .= ' WHERE ' . implode(' AND ', $wheres);
        }
        if (isset($this->SQL['groupBys'])) {
            $query .= ' GROUP BY ' . implode(' ,', $this->SQL['groupBys']);
        }
        if (isset($this->SQL['orders'])) {
            $orders = array();
            foreach ($this->SQL['orders'] AS $property => $order) {
                $orders[] = $property . ' ' . $order;
            }
            $query .= ' ORDER BY ' . implode(',', $orders);
        }
        if (isset($this->SQL['limit'])) {
            $limit = ' LIMIT 0,' . $this->SQL['limit'];
            if (isset($this->SQL['pagination']) && $this->SQL['pagination'] === TRUE) {
                $this->SQL['pagination'] = new \pagination($query, $this->SQL['limit'], $vars);
                $start = $this->SQL['pagination']->getCurrentPage() * $this->SQL['limit'] - $this->SQL['limit'];
                $limit = ' LIMIT ' . $start . ',' . $this->SQL['limit'];
            }
            $query .= $limit;
        }
        $this->SQL['valid'] = TRUE;
        if(PREFIX !== ''){
            foreach($this->SQL['froms'] AS $table){
                $query = str_replace($table, PREFIX.$table, $query);
            }
        }
        $this->SQL['query'] = $query;//strtolower();
        if(!empty($vars)){
            $this->SQL['stmt'] = \PDOconnection::getDB()->prepare($query);
            $this->SQL['stmt']->setFetchMode(\PDO::FETCH_INTO, $this);
            $this->SQL['stmt']->execute($vars);
        }else{
            $this->SQL['stmt'] = \PDOconnection::getDB()->query($query, \PDO::FETCH_INTO, $this);
        }
        
        if (is_object($this->SQL['stmt'])) {
            return TRUE;
        } else {
            return FALSE;
        }
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
            //select
            if(isset($p['display'])){
		$this->select($p['table'].'.'.$p['property']);
            }
            //From
            $this->from($p['table']);
            //where
            if (!empty($p['where'])) {
                $this->where($p['table'] . '.' . $p['property'] .' '. $p['where']);
            }
	    //or
            if (!empty($p['or'])) {
                $this->where($p['table'] . '.' . $p['property'] .' '. $p['or']);
            }
            //aggregate
            if (!empty($p['aggregate'])) {
                $this->aggregate($p['table'] . '.' . $p['property'], $p['aggregate']);
            }
            //order
            if (!empty($p['order'])) {
                $this->order($p['table'] . '.' . $p['property'], $p['order']);
            }
        }
        return $this;
    }

    /**
     * Rewind the cursor to the first row 
     * @todo optimize - pb with PDO
     */
    public function rewind() {
        if($this->buildQuery()){
            if($this->SQL['stmt']->fetch() !== FALSE){
                return $this->SQL['position'] = 0;
            }
        }
        $this->SQL['position'] = FALSE;
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
        return $this->SQL['position'];
    }

    /**
     * Move forward to the next row 
     */
    public function next() {
        if($this->SQL['stmt']->fetch() !== FALSE){
            $this->SQL['position']++;  
        }else{
            $this->SQL['position'] = FALSE;
        }
    }
    
    /**
     * Check if current position is valid
     * @return bool
     */
    public function valid() {
        if($this->SQL['position'] === FALSE){
            return FALSE;
        }
        return TRUE;
    }
    
    public function isEmpty() {
        $this->buildQuery();
        if (is_object($this->SQL['stmt'])) return !(bool)$this->SQL['stmt']->fetch();
        else return TRUE;
    }
    
    /**
     * Return properties in order to serialize it
     * @return array of SQL properties
     */
    public function __sleep() {
        foreach ($this->fields as $key => $field) {
            $this->fields[$key] = array('module' => $field->module, 'entity' => $field->entity, 'fieldName' => $field->name);
        }
        unset($this->SQL['entities']);
        unset($this->SQL['displayView']);
        unset($this->SQL['valid']);
        unset($this->SQL['stmt']);
        unset($this->SQL['position']);
        return array('fields', 'SQL');
    }

}

?>
