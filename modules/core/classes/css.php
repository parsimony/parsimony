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
 * CSS Class 
 * Manages CSS Files
 */
class css {

    /** @var string path of file */
    private $path;

    /** @var string css content */
    private $CSS = '';

    /** @var array of css selectors */
    private $selectors = array();

    /**
     * Init CSS 
     * @param string $cssPath 
     */
    public function __construct($cssPath) {
        $this->path = $cssPath;
        if (is_file($cssPath)){
            $this->CSS = file_get_contents($this->path, FILE_USE_INCLUDE_PATH); 
        }
    }

    /**
     * Determine if a selector exists
     * @param string $selector
     * @return string|false 
     */
    public function selectorExists($selector) {
        if (preg_match('@^' . str_replace(' ', '[\s]*', preg_quote($selector, '@')) . '[\s]*{(?<rules>[^}]*)\}@Usi', $this->CSS, $output)) {
            return $output['rules'];
        }
        return FALSE;
    }

    /**
     * Get all selectors
     * @return string|false 
     */
    public function getAllSselectors() {
        $css = preg_replace('#/\*(?:.(?!/)|[^\*](?=/)|(?<!\*)/)*\*/#s','',$this->CSS);
        if (preg_match_all('@/?(.*)[\s]*{[^}]*\}@Usi', $css, $output)) {
            $selectors = array_map('trim', $output[1]);
            return $selectors;
        }
        return FALSE;
    }

    /**
     * Add selector in CSS file
     * @param string $selector
     */
    public function addSelector($selector) {
        $this->CSS .= "\n\n" . $selector . "{\n\t\n}";
    }

    /**
     * Delete selector in CSS file
     * @param string $selector
     */
    public function deleteSelector($selector) {
        $this->CSS = preg_replace('@' . preg_quote($selector, '@') . '[\s]*\}@Usi', ' ', $this->CSS);
    }

    /**
     * Determine if a property exists
     * @param string $selector
     * @param string $property
     * @return string|false 
     */
    public function propertyExists($selector, $property) {
        $this->extractSelectorRules($selector);
        if (isset($this->selectors[$selector][$property])) {
            return trim($this->selectors[$selector][$property]);
        }
        return FALSE;
    }

    /**
     * Get value of a property
     * @param string $selector
     * @param string $property
     * @return string 
     */
    public function getPropertyValue($selector, $property) {
        $this->extractSelectorRules($selector);
        if (isset($this->selectors[$selector][$property]))
            return $this->selectors[$selector][$property];
        else
            return '';
    }

    /**
     * Add a property 
     * @param string $selector
     * @param string $property
     * @param string $value
     */
    public function addProperty($selector, $property, $value) {
        $this->extractSelectorRules($selector);
        $this->selectors[$selector][$property] = $value;
        $this->saveSelector($selector);
    }

    /**
     * Update a property 
     * @param string $selector
     * @param string $property
     * @param string $newValue
     */
    public function updateProperty($selector, $property, $newValue) {
        $this->extractSelectorRules($selector);
        $this->selectors[$selector][$property] = $newValue;
        $this->saveSelector($selector);
    }

    /**
     * Delete a property 
     * @param string $selector
     * @param string $property
     */
    public function deleteProperty($selector, $property) {
        $this->extractSelectorRules($selector);
        unset($this->selectors[$selector][$property]);
        $this->saveSelector($selector);
    }

    /**
     * Extract CSS rules of selectors
     * @param string $selector
     * @return @array 
     */
    public function extractSelectorRules($selector) {
        if (isset($this->selectors[$selector]))
            return $this->selectors[$selector];
        $this->selectors[$selector] = array();
        $_selector = trim($this->selectorExists($selector));
        $_selector = preg_replace('!/\*.*?\*/!s', '', $_selector);
        $_selector = preg_replace('/\n\s*\n/', "\n", $_selector);
        $tabsel = explode(';', $_selector);
        if (is_array($tabsel)) {
            foreach ($tabsel AS $sele) {
                if (strstr($sele, ':')) {
                    list($myProperty, $myValue) = explode(':', $sele);
                    if (trim($myProperty) != '')
                        $this->selectors[$selector][trim($myProperty)] = trim($myValue);
                }
            }
        }
        return $this->selectors[$selector];
    }

    /**
     * Save selector
     * @param string $selector
     */
    public function saveSelector($selector) {
        $_selector = $this->selectorExists($selector);
        $_selector = trim($_selector);
        $code = $selector . ' {' . PHP_EOL;
        foreach ($this->selectors[$selector] AS $property => $value) {
            $code .= "\t" . $property . ' : ' . $value . ' ;' . PHP_EOL;
        }
        $code .= '}';
        $this->CSS = preg_replace('@^' . str_replace(' ', '[\s]*', preg_quote($selector, '@')) . '[\s]*{(?<rules>[^}]*)\}@Usi', $code, $this->CSS);
    }

    /**
     * Replace a selector content by an other
     * @param string $selector
     * @param string $selectorCode
     */
    public function replaceSelector($selector, $selectorCode) {
        $_selector = $this->selectorExists($selector);
        $_selector = trim($_selector);
        $code = $selector . ' {' . PHP_EOL;
        $code .= $selectorCode . '}';
        $this->CSS = preg_replace('@^' . str_replace(' ', '[\s]*', preg_quote($selector, '@')) . '[\s]*{(?<rules>[^}]*)\}@Usi', addslashes($code), $this->CSS);
    }

    /**
     * Save file
     * @param string $selector
     * @return bool
     */
    public function save() {
        return tools::file_put_contents($this->path, $this->CSS);
    }

}
?>