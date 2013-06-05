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
 * Page Class 
 * Manages pages
 */
class page extends \block {

    /** @var string module name */
    private $moduleName;

    /** @var string */
    private $title;

    /** @var string */
    private $regex;

    /** @var array */
    private $URLcomponents = array();

    /** @var bool */
    private $structure = TRUE;

    /** @var array */
    private $metas = array();

    /** @var string */
    private $includes = array('header' => array('css' => array('http' => array(), 'local' => array()), 'js' => array('http' => array(), 'local' => array())), 'footer' => array('css' => array('http' => array(), 'local' => array()), 'js' => array('http' => array(), 'local' => array())));

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
    public function __construct( $id, $module = FALSE) {
        parent::__construct($id);
        if(!$module) $module = \app::$config['modules']['default'];
        $this->moduleName = $module;
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
     * @return page object
     */
    public function setRegex($regex) {
        $this->regex = $regex;
        return $this;
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
     * @return page object
     */
    public function setURLcomponents(array $URLcomponents) {
        $this->URLcomponents = $URLcomponents;
        return $this;
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
     * @return page object
     */
    public function setTitle($title) {
        if (!empty($title)) {
            $this->title = $title;
            return $this;
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

    /**
     * Set if the page require to display structure or no
     * @param bool $bool
     * @return page object
     */
    public function setStructure($bool) {
        (bool) $this->structure = $bool;
        return $this;
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
     * @return page object
     */
    public function setMetas(array $metas) {
        $this->metas = $metas;
        return $this;
    }

    /**
     * Set meta of a given key
     * @param string $name
     * @param string $value
     * @return page object
     */
    public function setMeta($name, $value) {
        $this->metas[$name] = $value;
        return $this;
    }

    /**
     * Add a block
     * @param block $block
     * @param string $idNext optional
     */
    public function addBlock(block $block, $idNext = false) {
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
        return $this;
    }

    /**
     * Remove a block
     * @param string $idBlock
     * @return page object
     */
    public function rmBlock($idBlock) {
        if (isset($this->blocks[THEMETYPE][$idBlock])) {
            unset($this->blocks[THEMETYPE][$idBlock]);
            return $this;
        } else {
            return FALSE;
        }
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
     * @return page object
     */
    public function setBlocks(array $blocks) {
        $this->blocks[THEMETYPE] = $blocks;
        return $this;
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
            return FALSE;
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
            $url = substr($this->getRegex(), 2,-2);
        }
        return $url;
    }

    /**
     * Get metas
     * @return string
     */
    public function printMetas() {
        $html = PHP_EOL;
        foreach ($this->metas as $name => $value) {
            if (!empty($value))
                $html .= "\t" . '<meta name="' . $name . '" content="' . $value . '">';
        }
        return $html;
    }
    
    /**
     * Get inclusions
     * @return string
     */
    public function getInclusions($position = 'header') {
        return $this->includes[$position];
    }

    /**
     * Get HTML inclusions
     * @return string
     */
    public function printInclusions($position = 'header') {
        $html = PHP_EOL;
        if (!empty($this->includes[$position]['css']['http']))
            $html .= PHP_EOL . "\t\t" . '<link rel="stylesheet" type="text/css" href="' . implode('" /><link rel="stylesheet" type="text/css" href="', $this->includes[$position]['css']['http']) . '" />';
        if (!empty($this->includes[$position]['css']['local'])) {
                $html .= PHP_EOL . "\t\t" . '<link rel="stylesheet" type="text/css" href="' . BASE_PATH . $this->concatFiles($this->includes[$position]['css']['local'], 'css') . '" />';
        }
        if (!empty($this->includes[$position]['js']['http']))
            $html .= PHP_EOL . "\t\t" . '<SCRIPT type="text/javascript" SRC="' . implode('"> </SCRIPT><SCRIPT type="text/javascript" SRC="', $this->includes[$position]['js']['http']) . '"> </SCRIPT>';
        if (!empty($this->includes[$position]['js']['local'])) {
                $html .= PHP_EOL . "\t\t" . '<SCRIPT type="text/javascript" SRC="' . BASE_PATH . $this->concatFiles($this->includes[$position]['js']['local'], 'js') . '"> </SCRIPT>' . PHP_EOL;
        }
        return $html;
    }
    
    /**
     * Concat JS or CSS Files
     * @param array $module
     */
    public function concatFiles(array $files, $format) {
        $hash = $format.'concat_'.md5(implode('', $files));
        $pathCache = 'profiles/' . PROFILE .'/modules/'.app::$config['modules']['default'].'/'. $hash . '.' . $format;
        if (is_file($pathCache) && app::$config['dev']['status'] === 'prod') {
            include($pathCache);
        } else {
            ob_start();
            foreach ($files as $file) {
                $pathParts = pathinfo($file,PATHINFO_EXTENSION);
                if($pathParts === 'js' || $pathParts === 'css'){
                    $path = stream_resolve_include_path ($file);
		    if($_SESSION['behavior'] && $pathParts == 'css') echo '.parsimonyMarker{background-image: url('.$file.') }'.PHP_EOL;
                    if($path) include($path);
		    echo PHP_EOL; //in order to split JS script and avoid "}function"
                }else{
		    return FALSE;
		}
            }
            $content = ob_get_clean();
            \tools::createDirectory(dirname($pathCache));
            file_put_contents($pathCache,$content);
        }
        return $hash . '.' . $format;
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
        return \tools::serialize(PROFILE_PATH . $this->moduleName . '/pages/' . $this->getId(), $this);
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
     * @param string $position header or footer
     * @param string $cssFile
     */
    public function addCSSFile($cssFile, $position = 'header') {
        if (substr($cssFile, 0, 2) == '//' || substr($cssFile, 0, 7) == 'http://') {
            if (!in_array($cssFile, $this->includes[$position]['css']['http']))
                $this->includes[$position]['css']['http'][] = $cssFile;
        }else {
            if (!in_array($cssFile, $this->includes[$position]['css']['local']))
                $this->includes[$position]['css']['local'][] = $cssFile;
        }
    }

    /**
     * Get CSS Files included
     * @param string $position header or footer
     * @return array
     */
    public function getCSSFiles($position = 'header') {
        return array_merge($this->includes[$position]['css']['http'], $this->includes[$position]['css']['local']);
    }

    /**
     * Add Javascript File to includes
     * @param string $position header or footer
     * @param string $jsFile
     */
    public function addJSFile($jsFile, $position = 'header') {
        if (strstr($jsFile, '//')) {
            if (!in_array($jsFile, $this->includes[$position]['js']['http']))
                $this->includes[$position]['js']['http'][] = $jsFile;
        }else {
            if (!in_array($jsFile, $this->includes[$position]['js']['local']))
                $this->includes[$position]['js']['local'][] = $jsFile;
        }
    }

    /**
     * Get Javascript Files included
     * @param string $position header or footer
     * @return array
     */
    public function getJSFiles($position = 'header') {
        return array_merge($this->includes[$position]['js']['http'], $this->includes[$position]['js']['local']);
    }

    /**
     * Update rights for a role
     * @param string $role
     * @return integer $rights
     */
    public function setRights($role, $rights) {
        /* We remove role entry if the role has the maximum of rights ( 1 = DISPLAY:1 ) #performance */
        if($rights === 1){
            if(isset($this->rights[$role])){
                unset($this->rights[$role]);
            }
        }else{
            $this->rights[$role] = $rights;
        }
    }

    /**
     * Get an entity
     * @param string $role
     * @return integer
     */
    public function getRights($role) {
        if (isset($this->rights[$role]))
            return $this->rights[$role];
        return 1;
    }

    public function __sleep() {
        return array('id', 'moduleName', 'blocks', 'title', 'regex', 'URLcomponents', 'metas', 'rights');
    }

}

?>