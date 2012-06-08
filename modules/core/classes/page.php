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
 * Page Class 
 * Manages pages
 * 
 */

class page extends \block {

    /** @var array of blocks */
    protected $blocks = array();
    
    /** @var string module name */
    // @todo remove optional core
    private $moduleName;
    
    /** @var string Page ID */
    protected $id;

    /** @var string */
    private $title;

    /** @var string */
    private $regex;

    /** @var array */
    private $URLcomponents = array();

    /** @var bool */
    private $structure = true;

    /** @var array */
    private $metas = array();

    /** @var string */
    private $CSS_inc = array();

    /** @var array */
    private $CSS_inc_http = array();

    /** @var array */
    private $JS_inc = array();

    /** @var array */
    private $JS_inc_http = array();

    /** @var array */
    private $rights = array();

    /** @var string */
    public $head = '';

    /** @var bool */
    public $current = FALSE;

    /**
     * Build a page object
     * @param integer $id page id
     * 
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Get Id
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set a new regex to the page
     * @param string $regex
     */
    public function setRegex($regex) {
        $this->regex = $regex;
    }

    /**
     * Get current regex
     * @return string
     */
    public function getRegex() {
        return $this->regex;
    }

    /**
     * Get all URL components
     * @return integer
     */
    public function getURLcomponents() {
        return $this->URLcomponents;
    }

    /**
     * Set all URL components
     * @param array $URLcomponents
     */
    public function setURLcomponents(array $URLcomponents) {
        $this->URLcomponents = $URLcomponents;
    }

    /**
     * Get current title
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set current title
     * @param string $title
     */
    public function setTitle($title) {
        if (!empty($title)) {
            $this->title = $title;
        } else {
            throw new \Exception(t('Title can\'t be empty', FALSE));
        }
    }

    /**
     * Check if the page require to display structure or no
     * @return bool
     */
    public function getStructure() {
        return $this->structure && !\app::$request->getParam('nostructure');
    }
    
    public function setmod($tt) {
        return $this->moduleName = $tt;
    }

    /**
     * Set if the page require to display structure or no
     * @param bool $bool
     */
    public function setStructure($bool) {
        (bool) $this->structure = $bool;
    }

    /**
     * Get Metas
     * @return array
     */
    public function getMetas() {
        return $this->metas;
    }

    /**
     * Get meta of a given key
     * @param string $name
     * @return string
     */
    public function getMeta($name) {
        if (isset($this->metas[$name]))
            return $this->metas[$name];
        else
            return '';
    }

    /**
     * Set Metas
     * @param array $metas
     */
    public function setMetas(array $metas) {
        $this->metas = $metas;
    }

    /**
     * Set meta of a given key
     * @param string $name
     * @param string $value
     */
    public function setMeta($name, $value) {
        $this->metas[$name] = $value;
    }

    /**
     * Add a new meta tag to the page
     * @param string $meta
     */
    public function addMeta($meta) {
        $this->metas[] = $meta;
    }

    /**
     * Add a block
     * @param block $block
     * @param string $idNext optional
     */
    public function addBlock(block $block, $idNext=false) {
        if (!isset($this->blocks[THEMETYPE]))
            $this->blocks[THEMETYPE] = array();
        if (!$idNext) {
            $this->blocks[THEMETYPE][$block->getId()] = $block;
        } else {
            $tempBlocks = array();
            foreach ($this->blocks[THEMETYPE] as $idBlock => $tempBlock) {
                if ($idBlock === $idNext) {
                    $tempBlocks[$block->getId()] = $block;
                }
                $tempBlocks[$idBlock] = $tempBlock;
            }
            if ($idNext == 'last')
                $tempBlocks[$block->getId()] = $block;
            $this->blocks[THEMETYPE] = $tempBlocks;
        }
    }

    /**
     * Remove a block
     * @param string $idBlock 
     */
    public function rmBlock($idBlock) {
        unset($this->blocks[THEMETYPE][$idBlock]);
    }

    /**
     * Get children blocks
     * @return array of blocks
     */
    public function getBlocks() {
        if (!isset($this->blocks[THEMETYPE]))
            $this->blocks = array(THEMETYPE => array());
        return $this->blocks[THEMETYPE];
    }

    /**
     * Set children blocks
     * @param array of blocks
     */
    public function setBlocks(array $blocks) {
        $this->blocks[THEMETYPE] = $blocks;
    }

    /**
     * Get a block child 
     * @param string $idBlock
     * @return an block object
     */
    public function getBlock($idBlock) {
        if (isset($this->blocks[THEMETYPE][$idBlock]))
            return $this->blocks[THEMETYPE][$idBlock];
        else
            throw new \Exception(t('This block doesn\'t exist', FALSE));
    }

    /**
     * Get URL or an example of URL if there are regex
     * @return string
     */
    public function getURL() {
        $url = '';
        if (!empty($this->URLcomponents)) {
            foreach ($this->URLcomponents AS $component) {
                if (isset($component['text']))
                    $url .= $component['text'];
                else
                    $url .= $component['val'];
            }
        }else {
            $url = substr(substr($this->getRegex(), 1), 0, -1);
        }
        return $url;
    }

    /**
     * Get metas
     * @return string
     */
    public function printMetas() {
        $html = "\r";
        foreach ($this->metas as $name => $value) {
            if (!empty($value))
                $html .= "\t" . '<META NAME="' . $name . '" CONTENT="' . $value . '">';
        }
        return $html;
    }
    /**
     * Get inclusions
     * @return string
     */
    public function printInclusions() {
        $html = "\r";
        if (!empty($this->CSS_inc_http))
            $html .= PHP_EOL . "\t\t" . '<link rel="stylesheet" type="text/css" href="' . implode('" /><link rel="stylesheet" type="text/css" href="', $this->CSS_inc_http) . '" />';
	if (!empty($this->CSS_inc)){
            if(ID_ROLE != 1 || defined('PARSI_ADMIN') || isset($_POST['popup'])) $html .= PHP_EOL . "\t\t" . '<link rel="stylesheet" type="text/css" href="' . BASE_PATH . 'concat?format=css&files=' . implode(',', $this->CSS_inc) . '" />';
	    else{
		foreach($this->CSS_inc AS $css)
		     $html .= PHP_EOL . "\t\t" . '<link rel="stylesheet" type="text/css" href="' .$css. '" />';
	    }
	}
        if (!empty($this->JS_inc_http))
            $html .= PHP_EOL . "\t\t" . '<SCRIPT type="text/javascript" SRC="' . implode('"> </SCRIPT><SCRIPT type="text/javascript" SRC="', $this->JS_inc_http) . '"> </SCRIPT>';
        if (!empty($this->JS_inc)){
            if(ID_ROLE != 1 || defined('PARSI_ADMIN') || isset($_POST['popup'])){ 
		$html .= PHP_EOL . "\t\t" . '<SCRIPT type="text/javascript" SRC="' . BASE_PATH . 'concat?format=js&files=' . implode(',', $this->JS_inc) . '"> </SCRIPT>' . PHP_EOL;
	    }else{
		foreach($this->JS_inc AS $css)
		     $html .= PHP_EOL . "\t\t" . '<SCRIPT type="text/javascript" SRC="' .$css. '"> </SCRIPT>';
	    }
	}
        return $html;
    }
    
    /**
     * Set module
     * @param string $module
     */
    public function setModule($module) {
        $this->moduleName = $module;
    }

    /**
     * Save the module
     * @return bool
     */
    public function save() {
        return \tools::serialize(PROFILE_PATH . $this->moduleName . '/pages/' . $this->getId() , $this); 
    }

    /**
     * Returns HTML of view
     * @return string
     */
    public function display() {
        \app::$request->page->current = TRUE;
	$html = '';
        if (!empty($this->blocks[THEMETYPE])) {
            foreach ($this->blocks[THEMETYPE] as $selected_block) {
                $html .= $selected_block->display();
            }
        }
        \app::$request->page->current = FALSE;
        return $html;
    }

    /**
     * Add CSS File to includes
     * @param string $cssFile
     */ 
    public function addCSSFile($cssFile) {
        if (substr($cssFile, 0, 7) == 'http://'){
            if(!in_array($cssFile,$this->CSS_inc_http)) $this->CSS_inc_http[] = $cssFile;
	}else{
            if(!in_array($cssFile,$this->CSS_inc)) $this->CSS_inc[] = $cssFile;
	}
    }
    
    /**
     * Get CSS Files included
     * @return array
     */
    public function getCSSFiles() {
        return array_merge($this->CSS_inc_http, $this->CSS_inc);
    }

    /**
     * Add Javascript File to includes
     * @param string $jsFile
     */
    public function addJSFile($jsFile) {
        if (substr($jsFile, 0, 7) == 'http://'){
            if(!in_array($jsFile,$this->JS_inc_http)) $this->JS_inc_http[] = $jsFile;
	}else{
            if(!in_array($jsFile,$this->JS_inc)) $this->JS_inc[] = $jsFile;
	}
    }
    
    /**
     * Get Javascript Files included
     * @return array
     */
    public function getJSFiles() {
        return array_merge($this->JS_inc_http, $this->JS_inc);
    }

    /**
     * Update rights for a role
     * @param string $role
     * @return integer $rights
     */
    public function updateRights($role, $rights) {
        $this->rights[$role] = $rights;
    }

    /**
     * Get an entity
     * @param string $role
     * @return integer
     */
    public function getRights($role=1) {
        if (isset($this->rights[(String) $role]))
            return $this->rights[(String) $role];
    }

    public function __sleep() {
        return array('id', 'blocks', 'title', 'regex', 'URLcomponents', 'metas', 'rights');
    }

}

?>