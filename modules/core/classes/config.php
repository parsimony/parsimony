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
 * Config Class 
 * Manages locale files and config files in PHP (config.php)
 */
class config implements \arrayaccess {

    /** @var string path of file */
    private $file;
    
    /** @var string content of file */
    private $content;

    /** @var array of config */
    private $config = array();

    /** @var string name of the array to process (by default array name is 'config') */
    private $variable = 'config';

    /**
     * Initialize configs
     * @param string $file 
     * @param string optional $update 
     */
    public function __construct($file, $update=FALSE) {
        $this->file = $file;
        if ($update) {
            if ($update && is_file($this->file))
                $this->content = file_get_contents($this->file);
            else
                $this->content = '<?php' . PHP_EOL;
        }
    }

    /**
     * Save configs
     * @param string $setConfigs 
     */
    public function saveConfig($setConfigs) {
        $configs = array();
        $confs = $this->saveConfigRecursive($setConfigs, '', $configs);
        foreach ($confs as $key => $value) {
            trim($value);
            if ($value == 'removeThis')
                $this->remove($key);
            elseif (preg_match('@' . preg_quote($key, '@') . '.?=.*;@Ui', $this->content))
                $this->update($key, $value);
            else
                $this->add($key, $value);
        }
        $this->save();
    }

    /**
     * Rebuild recursively configs
     * @param string $value
     * @param string $tree
     * @param string &$arr
     * @return an array of configs
     */
    private function saveConfigRecursive($value, $tree, &$arr) {
        if (is_array($value) && !empty($value)) {
            foreach ($value as $key => $val) {
                if (is_array($val)) {
                    $tree[] = '[\'' . $key . '\']';
                    $this->saveConfigRecursive($val, $tree, $arr);
                    array_pop($tree);
                } else {
                    if (is_array($tree))
                        $recur = implode('', $tree);
                    else
                        $recur = '';
                    $arr ['$' . $this->variable . $recur . '[\'' . addcslashes($key, "'") . '\']'] = $val;
                }
            }
        } else {
            echo $value;
        }
        return $arr;
    }

    /**
     * Put contents in file
     * @return bool
     */
    public function save() {
        return tools::file_put_contents($this->file, $this->content);
    }

    /**
     * Add configs content in file
     * @param string $key
     * @param string $value
     */
    public function add($key, $value) {
        $this->content = trim($this->content);
        if (substr($this->content, -2) == '?>')
            $this->content = substr($this->content, 0, -2);
        $this->content = trim($this->content) . PHP_EOL . $key . ' = \'' . addcslashes($value, "'") . '\';';
    }

    /**
     * Update the value of a config for a given key
     * @param string $key
     * @param string $value
     */
    public function update($key, $value) {
        $this->content = preg_replace('@' . preg_quote($key, '@') . '.?=.*;.*?@Ui', $key . ' = \'' . addcslashes($value, "'") . '\';', trim($this->content));
    }

    /**
     * Remove the value of a config for a given key
     * @param string $key
     */
    public function remove($key) {
        $this->content = preg_replace('@[' . PHP_EOL . ']?[.*]?' . preg_quote($key, '@') . '.?=.*;[.*' . PHP_EOL . ']?@Ui', '', trim($this->content));
    }
    
    /**
     * Get Content of config
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * In case of clone return an array
     */
    public function __clone() {
        foreach ($this->config as $key => $value) {
            if ($value instanceof self)
                $this[$key] = clone $value;
        }
    }

    /**
     * Set variable of the config array 
     * @param string $var
     */
    public function setVariable($var) {
        $this->variable = $var;
    }

    /**
     * Set variable in configs array
     * @param string $offset
     * @param string $data 
     */
    public function offsetSet($offset, $data) {
        if (is_array($data))
            $data = new self($data);
        if ($offset === null) {
            $this->config[] = $data;
        } else {
            $this->config[$offset] = $data;
        }
    }

    /**
     * Transforms a configs object in array
     * @return array of configs
     */
    public function toArray() {
        $data = $this->config;
        foreach ($data as $key => $value)
            if ($value instanceof self)
                $data[$key] = $value->toArray();
        return $data;
    }

    /**
     * Get variable in configs array
     * @param string $offset
     * @return value of an offset in configs array
     */
    public function offsetGet($offset) {
        return $this->config[$offset];
    }

    /**
     * Determine if an array offset is set
     * @param string $offset
     * @return bool if an array offset is set or not
     */
    public function offsetExists($offset) {
        return isset($this->config[$offset]);
    }

    /**
     * Unset an offset
     * @param string $offset
     */
    public function offsetUnset($offset) {
        unset($this->config);
    }

}

?>
