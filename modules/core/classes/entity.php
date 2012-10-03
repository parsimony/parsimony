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
 * @abstract Entity Class 
 * Provides the mapping of a SQL table in a PHP object
 */
abstract class entity implements \Iterator {

    /** @var string _module name */
    protected $_module;

    /** @var string table name for example $_tableName = core_entityName */
    protected $_tableName;
    
    /** @var string table name for example $_tableName = core_entityName */
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
     * Get the name of a given entity
     * @param string $name
     * @return field object | false
     */
    public function __get($name) {
        if (isset($this->$name)) {
            if (!isset($this->_SQL['stmt']))
                $this->buildQuery();
            return $this->$name;
        } else {
            return FALSE;
        }
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
            //$this->$name = new \field_string('','','');
            //$this->$name->setValue($value);
        }
    }

    /**
     * Update Rights
     * @param string $role
     * @param string $rights
     */
    public function updateRights($role, $rights) {
        $this->_rights[$role] = $rights;
    }

    /**
     * Get Rights
     * @param string $role
     * @return string
     */
    public function getRights($role) {
        if (isset($this->_rights[(String) $role]))
            return $this->_rights[(String) $role];
    }
    
    /**
     * Set Rights
     * @param array $rights
     * @return string
     */
    public function setRights(array $rights) {
            return $this->_rights = $rights;
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
        $sql = 'CREATE TABLE IF NOT EXISTS ' . $this->_tableName . ' (';
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
        $sql = 'DROP TABLE ' . $this->_tableName;
        rename('modules/' . $this->_module . '/model/' . $this->_entityName . '.php', 'modules/' . $this->_module . '/model/' . $this->_entityName . '.php.back');
        rename('modules/' . $this->_module . '/model/' . $this->_entityName . '.' . \app::$config['dev']['serialization'], 'modules/' . $this->_module . '/model/' . $this->_entityName . '.' . \app::$config['dev']['serialization'] . '.back');
        return (bool) PDOconnection::getDB()->exec($sql);
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
     * Insert Into DB 
     * @param array $vars
     * @return bool
     */
    public function insertInto(array $vars) {
        $vars = $this->beforeInsert($vars);
        $query = 'INSERT INTO ' . $this->_tableName . '(';
	$params = '';
        foreach ($this->getFields() as $name => $field) {
            if (get_class($field) != \app::$aliasClasses['field_formasso']) {
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
            return $values; // FALSE
        $res = $sth->execute($values);
        $this->purgeSQL();
        if(!$res) {
	    return FALSE;
	}else{
	    $this->afterInsert($values);
	    \app::dispatchEvent('afterInsert', array($vars, &$this));
	    return  \PDOconnection::getDB()->lastInsertId();
	}
    }

    /**
     * Update in DB 
     * @param array $vars
     * @return bool
     */
    public function update(array $vars) {
        $vars = $this->beforeUpdate($vars);
        $query = 'UPDATE ' . $this->_tableName . ' SET ';
        foreach ($this->getFields() as $name => $field) {
            if (get_class($field) != \app::$aliasClasses['field_formasso'] && isset($vars[$name]))
                foreach ($field->getColumns() AS $column)
                    $query .= $column . ' = :' . $column . ',';
        }
        $query = substr($query, 0, -1);
        if (isset($this->_SQL['wheres'])) {
            $wheres = array();
            foreach ($this->_SQL['wheres'] AS $property => $where) {
                $wheres[] = $where;
            }
            $query .= ' WHERE ' . implode(' AND ', $wheres);
        } else {
            $query .= ' WHERE ' . $this->getId()->name . ' = :' . $this->getId()->name . ';';
        }
        $sth = PDOconnection::getDB()->prepare($query);
        $values = $this->prepareValues($vars, 'update');
        if (!is_array($values)) 
            return $values; // FALSE
        $res = $sth->execute($values);
        $this->purgeSQL();
        if(!$res) {
	    return FALSE;
	}else{
	    $this->afterUpdate($values);
	    \app::dispatchEvent('afterUpdate', array($vars, &$this));
	    return TRUE;
	}
    }

    /**
     * Delete in DB 
     * @param string $id
     * @return bool
     */
    public function delete() {
        $this->beforeDelete();
        $query = 'DELETE FROM ' . $this->_tableName;
        if (isset($this->_SQL['wheres'])) {
            $wheres = array();
            foreach ($this->_SQL['wheres'] AS $property => $where) {
                $wheres[] = $where;
            }
            $query .= ' WHERE ' . implode(' AND ', $wheres);
        }
        $res = PDOconnection::getDB()->exec($query);
        $this->afterDelete();
        \app::dispatchEvent('afterDelete', array(&$this));
        return $res;
    }

    /**
     * Prepare an array in order to execute insert and update prepared query
     * @param array $vars
     * @return array
     */
    public function prepareValues(array $vars, $type= 'insert') {
        $values = array();
        $val = 'insert';
        if($type == 'update') $val = $vars[$this->getId()->name];
        foreach ($this->getFields() as $name => $field) {
            if ($type=='insert' || isset($vars[$field->name])) {
                $value = '';
                $columns = $field->getColumns();
                $columnsValues = array_intersect_key($vars, array_flip($columns));
                if(isset($vars[$field->name])) $value = $vars[$field->name];
                if (count($columns) == 1)
                    $value = $field->validate($value,$val, $vars);
                else
                    $value = $field->validate($columnsValues,$val, $vars);
                if ($value === FALSE)
                    return $field->label . '. ' . $field->msg_error; // return error message
                if (get_class($field) != \app::$aliasClasses['field_formasso']) {
                    foreach ($field->getColumns() AS $column)
                        if (count($columns) == 1)
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
        if (!isset($this->_SQL['stmt']))
            $this->buildQuery();
        $html = '';
        foreach ($this->getFields() as $name => $field) {
            $html .= '<div>' . $field . '</div>';
        }
        return $html;
    }

    /**
     * Get the view of adding form
     * @param string $ajax by default False
     * @return string
     */
    public function getViewAddForm($ajax = FALSE) {
        if (!isset($this->_SQL['stmt']))
            $this->buildQuery();
        if ($ajax == TRUE)
            $ajaxtarget = ' target="ajaxhack"';
        else
            $ajaxtarget = '';
        $html = '<form method="post" class="form" ' . $ajaxtarget . 'action="">
	<input type="hidden" name="TOKEN" value="' . TOKEN . '" />';
        $col1 = '';
        $col2 = '';
        if ($ajax == TRUE) {
            $html .= '<input type="hidden" name="action" value="addNewEntry">';
            $html .= '<input type="hidden" name="entity" value="' . $_POST['model'] . '">';
        }
        foreach ($this->getFields() as $name => $field) {
            if ($field->visibility & INSERT) {
                if (get_class($field) == \app::$aliasClasses['field_formasso'] || get_class($field) == \app::$aliasClasses['field_publication'] || get_class($field) == \app::$aliasClasses['field_state'] || get_class($field) == \app::$aliasClasses['field_foreignkey'] || get_class($field) == \app::$aliasClasses['field_date'] || get_class($field) == \app::$aliasClasses['field_user'])
                    $col2 .= $field->formAdd();
                else
                    $col1 .= $field->formAdd();
            }
        }
        $html .= '<h2 style="position:relative">' . t('Add in', false) . ' ' . $this->_entityName . '<input style="position:absolute;right:3px;top:3px;" type="submit" value="' . t('Save', FALSE) . '" name="add"></h2><div class="cols">';
        $html .= '<div class="col col1">' . $col1 . '</div>';
        if (!empty($col2))
            $html .= '<div class="col col2">' . $col2 . '</div>';
        $html .= '</div><div class="clearboth"></div></form>';
        return $html;
    }

    /**
     * Get the view of updating form
     * @param string $ajax by default False
     * @return string
     */
    public function getViewUpdateForm($ajax = FALSE) {
        if (!isset($this->_SQL['stmt']))
            $this->buildQuery();
        if (!$this->_SQL['stmt']->fetch())
            return '';
        if ($ajax == TRUE)
            $ajaxtarget = ' target="ajaxhack"';
        else
            $ajaxtarget = '';
        $html = '<form method="post" class="form" ' . $ajaxtarget . 'action="">
	<input type="hidden" name="TOKEN" value="' . TOKEN . '" />';
        $col1 = '';
        $col2 = '';
        if ($ajax == TRUE) {
            $html .= '<input type="hidden" name="action" value="updateEntry">';
            $html .= '<input type="hidden" name="entity" value="' . $_POST['module'] . ' - ' . $_POST['entity'] . '">';
        }
        foreach ($this->getFields() as $name => $field) {
            if ($field->visibility & UPDATE) {
                if (get_class($field) == \app::$aliasClasses['field_formasso'] || get_class($field) == \app::$aliasClasses['field_publication'] || get_class($field) == \app::$aliasClasses['field_state'] || get_class($field) == \app::$aliasClasses['field_foreignkey'] || get_class($field) == \app::$aliasClasses['field_date'] || get_class($field) == \app::$aliasClasses['field_user'])
                    $col2 .= $field->formUpdate($this->{$field->name}, $this);
                else
                    $col1 .= $field->formUpdate($this->{$field->name}, $this);
            }
        }
        $html .= '<h2>' . t('Record', FALSE) . ' N° ' . $this->getId()->value;
        $html .= '<div style="position:absolute;right:3px;top:3px;"><input type="submit" name="update" value="' . t('Update', FALSE) . '">';
        if ($this->getRights(ID_ROLE) & DELETE)
            $html .= '<input type="submit" name="delete" value="' . t('Delete', FALSE) . '" onclick="if(!confirm(\'' . t('Are you sure you want to delete ?', FALSE) . '\')) {event.preventDefault();return FALSE;}">';

        $html .= '</h2>';
        $html .= '<div class="cols">';
        $html .= '<div class="col col1">' . $col1 . '</div>';
        if (!empty($col2))
            $html .= '<div class="col col2">' . $col2 . '</div>';

        $html .= '</div></div></form>';
        return $html;
    }

    /**
     * Get pagination
     * @param string $ajax by default False
     * @return pagination object
     */
    public function getPagination($ajax = FALSE) {
        if (isset($this->_SQL['limit'])) {
            $limit = explode(',', $this->_SQL['limit']);
            if (count($limit) > 1)
                $diff = (int) trim($limit[1]);
            else
                $diff = (int) trim($this->_SQL['limit']);
            return new \pagination(strstr($this->_SQL['query'], 'limit', true), $diff);
        }
    }

    /*     * *************************************************************
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
        }
        else
            throw new Exception(t('ID isn\'t numeric'));
    }

    /**
     * Get the ID
     * @return field obejct
     */
    public function getId() {
        $properties = $this->getFields();
        return reset($properties);
    }

    /**
     * Get META title
     * @param integer $id
     * @return string|false
     */
    public function getBehaviorTitle() {
        if (!empty($this->behaviorTitle)) {
            return $this->behaviorTitle;
        } else {
            foreach ($this->getFields() as $name => $property) {
                if (get_class($property) == \app::$aliasClasses['field_string']) {
                    $this->behaviorTitle = $name;
                    return $name;
                }
            }
            $properties = $this->getFields();
            return reset($properties);
        }
        return FALSE;
    }

    /**
     * Get META description
     * @return string|false
     */
    public function getBehaviorDescription() {
        if (!empty($this->behaviorDescription)) {
            return $this->behaviorDescription;
        } else {
            foreach ($this->getFields() as $name => $property) {
                if (get_class($property) == \app::$aliasClasses['field_textarea']) {
                    $this->behaviorDescription = $name;
                    return $name;
                }
            }
        }
        return FALSE;
    }

    /**
     * Get META Keywords
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
     * Get META Image
     * @return string|false
     */
    public function getBehaviorImage() {
        if (!empty($this->behaviorImage)) {
            return $this->behaviorImage;
        } else {
            foreach ($this->getFields() as $name => $property) {
                if (get_class($property) == \app::$aliasClasses['field_image']) {
                    $this->behaviorImage = $name;
                    return $name;
                }
            }
        }
        return FALSE;
    }

    /**
     * Get META Author
     * @return string|false
     */
    public function getBehaviorAuthor() {
        if (!empty($this->behaviorAuthor)) {
            return $this->behaviorAuthor;
        } else {
            foreach ($this->getFields() as $name => $property) {
                if (get_class($property) == \app::$aliasClasses['field_user']) {
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
        return $fields;
    }
    
    /**
     * Get field
     * @param string $name
     * @return field
     */
    public function getField($name) {
	if (($this->$name instanceof \field)) {
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
     * 
     * 
     */
    public function purgeSQL() {
        unset($this->_SQL['selects']);
        unset($this->_SQL['wheres']);
        unset($this->_SQL['joins']);
        unset($this->_SQL['orders']);
        unset($this->_SQL['limit']);
        return TRUE;
    }

    /**
     * 
     * 
     */
    public function __sleep() {
        $this->purgeSQL();
        $fields = get_object_vars($this);
        $fields = array_keys($fields);
        return $fields;
    }
    
    /**
     * 
     * 
     */
    public function __wakeup() {
	if(!isset($this->_tableName)){
	    list( $this->_module, $entity, $this->_entityName) = explode('\\', get_class($this));
	    $this->_tableName = $this->_module . '_' . $this->_entityName;
	}
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
        $query = 'SELECT ';
        if (isset($this->_SQL['selects'])) {
            $query .= implode(',', $this->_SQL['selects']);
        } else {
            //$query .= implode(',', array_keys($this->getFields()));
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
        $this->_SQL['query'] = strtolower($query);
        $this->_SQL['stmt'] = \PDOconnection::getDB()->query($query, \PDO::FETCH_INTO, $this);
        if (is_object($this->_SQL['stmt'])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

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
     * @todo optimize - pb with PDO
     */
    public function rewind() {
        $this->buildQuery();
        $this->beforeSelect();
        $this->_SQL['position'] = 0;
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
        $this->_SQL['position']++;
    }

    /**
     * Check if current position is valid
     * @return bool
     */
    public function valid() {
        if (is_object($this->_SQL['stmt']))
            return $this->_SQL['stmt']->fetch();
        else
            return FALSE;
    }

}

?>