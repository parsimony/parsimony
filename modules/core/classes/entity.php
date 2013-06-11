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
 * @abstract Entity Class 
 * Provides the mapping of a SQL table in a PHP object
 * Iterator interface allows you to:
 * $users = app::getModule('core')->getEntity('user');
 * foreach($users as $user){
 *      echo $user->pseudo;
 * }
 */
abstract class entity implements \Iterator {

    /** @var string _module name */
    protected $_module;

    /** @var string table name for example $_tableName = $module_$entityName */
    protected $_tableName;
    
    /** @var string title */
    protected $_tableTitle;

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

    /** @var array of _rights */
    protected $_rights = array();

    /** @var array of SQL properties in order to build SQL Query */
    protected $_SQL = array();
    
    /**
     * Constructor: init entity vars
     */
    public function __construct() {
        list( $this->_module, $entity, $this->_entityName) = explode('\\', get_class($this));
	$this->_tableName = $this->_module . '_' . $this->_entityName;
    }
    


    /**
     * Get the name of a given entity
     * @param string $name
     * @return field object | false
     */
    public function __get($name) {
        return $this->$name->value;
    }

    /**
     * Set name of entity
     * @param string $name
     * @param string $value
     */
    public function __set($name, $value) {  
        if (isset($this->$name)) {   
            $this->$name->setValue($value);
        } else {
            $this->$name = $value;
        }
    }
    
    /**
     * Get the name of a given entity
     * @param string $name
     * @return field object | false
     */
    public function __call($name, $args) {
        return $this->$name;
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
     * Get Name
     * @return string
     */
    public function getName() {
        return $this->_entityName;
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
        foreach ($this->getFields() as $name => $field) {
            $sqlField = $field->sqlModel();
            if ($sqlField != FALSE)
                $sql .= $sqlField . ',';
        }
        $sql = substr($sql, 0, -1) . ') ENGINE=MyISAM DEFAULT CHARSET=utf8;';
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
    public function insertInto(array $vars) {
        if($this->getRights($_SESSION['id_role']) & INSERT){
            $vars = $this->beforeInsert($vars);
            if($vars === FALSE) return FALSE;
            $query = 'INSERT INTO ' . PREFIX . $this->_tableName . '(';
            $params = '';
            foreach ($this->getFields() as $field) {
                if (get_class($field) !== \app::$aliasClasses['field_formasso']) {
                    foreach ($field->getColumns() AS $column){
                        $query .= $column . ',';
                        $params .= ':' . $column . ',';
                    }
                }
            }
            $query = substr($query, 0, -1) . ') VALUES(' . substr($params, 0, -1) . ');';
            $sth = PDOconnection::getDB()->prepare($query);

            $values = $this->prepareValues($vars);
            if (!is_array($values)) 
                return $values; // FALSE : error message
            $res = $sth->execute($values);
            $this->purgeSQL();
            if($res !== FALSE) {
                $lastId = $values[':'.$this->getId()->name] = \PDOconnection::getDB()->lastInsertId(); // should be before afterInsert
                $this->afterInsert($values);
                \app::dispatchEvent('afterInsert', array($vars, &$this));
                return  $lastId;
            }
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
    public function update(array $vars) {
        if($this->getRights($_SESSION['id_role']) & UPDATE){
            $vars = $this->beforeUpdate($vars);
            if($vars === FALSE) return FALSE;
            $query = 'UPDATE ' . PREFIX . $this->_tableName . ' SET ';
            foreach ($this->getFields() as $name => $field) {
                if (get_class($field) !== \app::$aliasClasses['field_formasso'] && isset($vars[$name]))
                    foreach ($field->getColumns() AS $column)
                        $query .= $column . ' = :' . $column . ',';
            }
            $query = substr($query, 0, -1);
            if (isset($this->_SQL['wheres'])) {
                $query .= ' WHERE ' . implode(' AND ', $this->_SQL['wheres']);
            } else {
                $query .= ' WHERE ' . $this->getId()->name . ' = :' . $this->getId()->name . ';';
            }
            $sth = PDOconnection::getDB()->prepare($query);
            $values = $this->prepareValues($vars, 'UPDATE');
            if (!is_array($values)) 
                return $values; // FALSE : error message
            $res = $sth->execute($values);
            $this->purgeSQL();
            if($res !== FALSE) {
                $this->afterUpdate($values);
                \app::dispatchEvent('afterUpdate', array($vars, &$this));
                return TRUE;
            }
            return FALSE;
        }else{
            throw new \Exception(t('Update forbidden on ' . $this->_tableName, FALSE));
        }
    }

    /**
     * Delete in DB 
     * @param string $id
     * @return bool
     */
    public function delete() {
        if($this->getRights($_SESSION['id_role']) & UPDATE){
            $this->beforeDelete();
            $query = 'DELETE FROM ' . PREFIX . $this->_tableName;
            if (isset($this->_SQL['wheres'])) {
                $query .= ' WHERE ' . implode(' AND ', $this->_SQL['wheres']);
            }
            $res = PDOconnection::getDB()->exec($query);
            $this->afterDelete();
            \app::dispatchEvent('afterDelete', array(&$this));
            return $res;
        }else{
            throw new \Exception(t('Delete forbidden on ' . $this->_tableName, FALSE));
        }
    }

    /**
     * Prepare an array in order to execute insert and update prepared query
     * @param array $vars
     * @return array
     */
    protected function prepareValues(array $vars, $type= 'INSERT') {
        $values = array();
        $val = 'insert';
        if($type === 'UPDATE') {
            $idName = $this->getId()->name;
            if(isset($vars[$idName])){
                $val = $vars[$idName];
            } else {
                throw new \Exception(t('ID must be filled to update', FALSE));
            }
        }
        foreach ($this->getFields() as $name => $field) {
            if ($type === 'INSERT' || isset($vars[$name])) {
                $columns = $field->getColumns();
                if (count($columns) === 1){
                    /* If the field has one column */
                    $value = isset($vars[$name]) && $field->getRights($_SESSION['id_role']) & constant($type)  ? $vars[$name] : '';
                    $value = $field->validate($value,$val, $vars);
                }else{
                    /* If the field has severals columns */
                    $columnsValues = array_intersect_key($vars, array_flip($columns));
                    $value = $field->validate($columnsValues,$val, $vars);
                }
                if ($value === FALSE)
                    return $field->label . ', ' . $field->msg_error; // return error message
		else 
		    $field->setValue($value);
                
                /* If field is a field_formasso */
                if (get_class($field) !== \app::$aliasClasses['field_formasso']) {
                    foreach ($columns AS $column)
                        if (count($columns) === 1)
                            $values[':' . $column] = $value;
                        else {
                            foreach ($value AS $key => $val)
                                $values[':' . $key] = $val;
                        }
                }
            }
        }
        return $values;
    }

    /**
     * Display the view of entity fields
     * @return string
     */
    public function display() {
        if($this->getRights($_SESSION['id_role']) & DISPLAY){
            if (!isset($this->_SQL['stmt']))
                $this->buildQuery();
            $html = '';
            foreach ($this->getFields() as $name => $field) {
                $html .= '<div class="' . $name . '">' . $field . '</div>';
            }
            return $html;
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
            if (!isset($this->_SQL['stmt']))
                $this->buildQuery();
            $html = '<form method="post" class="form" action="">
            <input type="hidden" name="TOKEN" value="' . TOKEN . '" />
            <input type="hidden" name="action" value="addNewEntry">
            <input type="hidden" name="entity" value="' . $this->_module . ' - ' . $this->_entityName . '">';
            $col1 = '';
            $col2 = '';
            foreach ($this->getFields() as $field) {
                if ($field->visibility & INSERT) {
                    if (get_class($field) === \app::$aliasClasses['field_formasso'] || get_class($field) === \app::$aliasClasses['field_publication'] || get_class($field) === \app::$aliasClasses['field_state'] || get_class($field) === \app::$aliasClasses['field_foreignkey'] || get_class($field) === \app::$aliasClasses['field_date'] || get_class($field) === \app::$aliasClasses['field_user'])
                        $col2 .= $field->form((isset($_POST[$field->name]) ? $_POST[$field->name] : ''));
                    else
                        $col1 .= $field->form((isset($_POST[$field->name]) ? $_POST[$field->name] : ''));
                } 
            }
            $html .= '<h2 style="position:relative">' . t('Add in', false) . ' ' . $this->_entityName . '<input style="position:absolute;right:3px;top:3px;" type="submit" value="' . t('Save', FALSE) . '" name="add"></h2><div class="cols">';
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
            if (!isset($this->_SQL['stmt']))
                $this->buildQuery();
            if (!$this->_SQL['stmt']->fetch())
                return '';
            $html = '<form method="post" class="form" action="">
            <input type="hidden" name="TOKEN" value="' . TOKEN . '" />
            <input type="hidden" name="action" value="updateEntry">
            <input type="hidden" name="entity" value="' . $this->_module . ' - ' . $this->_entityName . '">';
            $col1 = '';
            $col2 = '';
            foreach ($this->getFields() as $field) {
                if ($field->visibility & UPDATE) {
                    if (get_class($field) == \app::$aliasClasses['field_formasso'] || get_class($field) == \app::$aliasClasses['field_publication'] || get_class($field) == \app::$aliasClasses['field_state'] || get_class($field) == \app::$aliasClasses['field_foreignkey'] || get_class($field) == \app::$aliasClasses['field_date'] || get_class($field) == \app::$aliasClasses['field_user'])
                        $col2 .= $field->form($field->value, $this);
                    else
                        $col1 .= $field->form($field->value, $this);
                }
            }
            $html .= '<h2 style="position:relative">' . t('Record', FALSE) . ' N°' . $this->getId()->value;
            $html .= '<div style="position:absolute;right:3px;top:3px;"><input type="submit" name="update" value="' . t('Update', FALSE) . '">';
            if ($this->getRights($_SESSION['id_role']) & DELETE)
                $html .= '<input type="submit" name="delete" value="' . t('Delete', FALSE) . '" onclick="if(!confirm(\'' . t('Are you sure you want to delete ?', FALSE) . '\')) {event.preventDefault();return FALSE;}">';

            $html .= '</div></h2><div class="cols">';
            $html .= '<div class="col col1">' . $col1 . '</div>';
            if (!empty($col2))
                $html .= '<div class="col col2">' . $col2 . '</div>';
            $html .= '</div><div class="clearboth"></div></form>';
            return $html;
        }else{
            throw new \Exception(t('Update forbidden on ' . $this->_tableName, FALSE));
        }
    }

    /**
     * Get pagination
     * @return pagination object
     */
    public function getPagination() {
        if (isset($this->_SQL['limit'])) {
            $limit = explode(',', $this->_SQL['limit']);
            if (count($limit) > 1)
                $diff = (int) trim($limit[1]);
            else
                $diff = (int) trim($this->_SQL['limit']);
            return new \pagination(strstr($this->_SQL['query'], 'limit', true), $diff);
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
     */
    public function beforeUpdate($values) {
        return $values;
    }

    /**
     * Event before insert
     */
    public function beforeInsert($values) {
        return $values;
    }

    /**
     * Event before delete
     */
    public function beforeDelete() {
        
    }

    /**
     * Event after select
     */
    public function afterSelect() {
        
    }

    /**
     * Event after update
     */
    public function afterUpdate($values) {
        
    }

    /**
     * Event after insert
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
        $properties = $this->getFields();
        return $this->{key($properties)};
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
            $properties = $this->getFields();
            foreach ($properties as $name => $property) {
                if (get_class($property) === \app::$aliasClasses['field_string']) {
                    $this->behaviorTitle = $name;
                    return $name;
                }
            }
            reset($properties);
            return key($properties);
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
            $properties = $this->getFields();
            foreach ($properties as $name => $property) {
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
            $properties = $this->getFields();
            foreach ($properties as $name => $property) {
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
            $properties = $this->getFields();
            foreach ($properties as $name => $property) {
                if (get_class($property) === \app::$aliasClasses['field_user']) {
                    $this->behaviorAuthor = $name;
                    return $name;
                }
            }
        }
        return FALSE;
    }

    /**
     * Get fields
     * @return array of fields
     */
    public function getFields() {
        $fields = get_object_vars($this);
        foreach ($fields as $name => $property) {
            if (!($property instanceof \field)) {
                unset($fields[$name]);
            }
        }
        reset($fields);
        return $fields;
    }
    
    /**
     * Get field
     * @param string $name
     * @return field|false
     */
    public function getField($name) {
	if (isset($this->$name)) {
	    return $this->$name;
	}
        return FALSE;
    }

    /**
     * Put content in file.obj
     * @return string
     */
    public function save() {
        return \tools::serialize('modules/' . $this->_module . '/model/' . $this->_entityName, $this);
    }
    
    /**
     * Clean the entity object
     */
    public function purgeSQL() {
        unset($this->_SQL['selects']);
        unset($this->_SQL['wheres']);
        unset($this->_SQL['joins']);
        unset($this->_SQL['orders']);
        unset($this->_SQL['limit']);
    }

    /**
     * 
     * 
     */
    public function __sleep() {
        $this->purgeSQL();
        $properties = get_object_vars($this);
        unset($properties['fields']);
        $fields = array_keys($properties);
        return $fields;
    }
    
    /**
     * Prepare fields for display
     */
    public function prepareFieldsForDisplay() {
        
        /* Determine if current user has the right to editinline */
        $displayView = 'display.php';
        if ($_SESSION['behavior'] > 0 && $this->getRights($_SESSION['id_role']) & UPDATE ) {
            $displayView = 'editinline.php';
        }
        
        /* Insert an entity reference in each field */
        $fields = $this->getFields();
        foreach ($fields as &$field) {
            $field->row = $this;
            $field->views = array();
            
            $fieldPath = $field->fieldPath;

            /* Display View */
            if ($field->getRights($_SESSION['id_role']) & DISPLAY ) {
                $field->views['display'] = $fieldPath.'/'.$displayView;
                $field->views['grid'] = $fieldPath.'/grid.php';
            }else{
                $field->views['display'] = $field->views['grid'] = 'php://temp'; // display nothing, to avoid a "if" in each field->display() call
            }
        }
    }


    /**
     * Select in DB 
     * @param $clauses
     * @return entity object
     */
    public function select($clause = '') {
        $this->_SQL = array();
        $selects = explode(',', $clause);
        if (!empty($clause)) {
            foreach ($selects AS $select) {
                $select = trim($select);
                $this->_SQL['selects'][$select] = $select;
            }
        }
        return $this;
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
     * Set a table to Join
     * @param string $propertyLeft ID of left table
     * @param string $propertyRight ID of right table
     * @param string $type of join
     * @return view object
     */
    public function join($propertyLeft, $propertyRight, $type = 'left outer join') {
        $this->_SQL['joins'][] = array('propertyLeft' => $propertyLeft, 'propertyRight' => $propertyRight, 'type' => $type);
        return $this;
    }

    /**
     * Build SQL Query 
     * @return bool 
     */
    public function buildQuery() {
        $this->beforeSelect();
        $query = 'SELECT ';
        if (isset($this->_SQL['selects'])) {
            $query .= implode(',', $this->_SQL['selects']);
        } else {
            $query .= '*';
        }
        $query .= ' FROM ' . $this->_tableName;
        if (isset($this->_SQL['joins'])) {
            foreach ($this->_SQL['joins'] AS $join) {
                $query .= ' ' . $join['type'] . ' ' . strstr($join['propertyRight'], '.', true) . ' ON ' . $join['propertyLeft'] . ' = ' . $join['propertyRight'];
            }
        }
        if (isset($this->_SQL['wheres'])) {
            $wheres = array();
            foreach ($this->_SQL['wheres'] AS $property => $where) {
                $wheres[] = $where;
            }
            $query .= ' WHERE ' . implode(' AND ', $wheres);
        }
        if (isset($this->_SQL['orders'])) {
            $orders = array();
            foreach ($this->_SQL['orders'] AS $property => $order) {
                $orders[] = $property . ' ' . $order;
            }
            $query .= ' ORDER BY ' . implode(',', $orders);
        }
        if (isset($this->_SQL['limit'])) {
            $query .= ' LIMIT ' . $this->_SQL['limit'];
        }
        $this->_SQL['query'] = str_replace($this->_tableName, PREFIX.$this->_tableName, strtolower($query));
        $this->_SQL['stmt'] = \PDOconnection::getDB()->query($this->_SQL['query'], \PDO::FETCH_INTO, $this);
        $this->prepareFieldsForDisplay();
        if (is_object($this->_SQL['stmt'])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /* Iterator interface */

    /**
     * Rewind the cursor to the first row 
     * @return entity object
     */
    public function fetch() {
        foreach ($this as $obj) {
            return $obj;
        }
    }

    /**
     * Rewind the cursor to the first row
     */
    public function rewind() {
        if($this->buildQuery()){
            if($this->_SQL['stmt']->fetch() !== FALSE){
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
            $this->afterSelect();
            return FALSE;
        }  
    }

}

?>