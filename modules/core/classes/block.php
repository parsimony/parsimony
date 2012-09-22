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
 * @package core/classes
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace core\classes;

/**
 * @abstract Block Class  
 * Manages blocks
 */
abstract class block {

    /** @var string Block ID */
    protected $id;

    /** @var array of blocks if block is a container */
    protected $blocks = array();

    /** @var array contains a configuration array of a block */
    protected $configs = array();

    /**
     * Build a block object
     * @param string $id Block ID 
     * @param string $init have to init the block or not
     */
    public function __construct($id, $init = true) {
        $this->setId($id);
        if(method_exists($this, 'init') && $init) $this->init();
    }

    /**
     * Get Block ID 
     * @return string ID
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set Block ID 
     * @param string $id 
     */
    public function setId($id) {
        if (!empty($id)) {
            $this->id = \tools::sanitizeTechString($id);
        } else {
            throw new Exception(t('ID can\'t be empty', FALSE));
        }
    }
    
    /**
     * Get Block Name
     * @return string Name
     */
    public function getName() {
        if(isset($this->name)) return $this->name;
	else return $this->id;
    }

    /**
     * Get configs of block 
     * @return array of configs
     */
    public function getConfigs() {
        return $this->configs;
    }

    /**
     * Get one config of block 
     * @param string $key
     * @return mixed|false
     */
    public function getConfig($key) {
        if (isset($this->configs[$key]))
            return $this->configs[$key];
        else
            return false;
    }

    /**
     * Set one config of block 
     * @param string $key key of the config
     * @param mixed $value value of the config
     */
    public function setConfig($key, $value) {
	if (!is_resource($value)) {
            $this->configs[$key] = $value;
        } else {
            throw new Exception(t('A block config can\'t be a resource.', FALSE));
        }
    }

    /**
     * Get children blocks of container block
     * @return array of blocks
     */
    public function getBlocks() {
        return $this->blocks;
    }

    /**
     * Get a child block
     * @param string $idBlock
     * @return an block object
     */
    public function getBlock($idBlock) {
        if(isset($this->blocks[$idBlock])) {
            return $this->blocks[$idBlock];
        } else {
            return FALSE;
        }
        
    }

    /**
     * Add a child block in container block
     * @param block $block
     * @param string $idNext optional
     */
    public function addBlock(block $block, $idNext = 'last') {
        $tempBlocks = array();
        if($this->id == 'container' && count($this->blocks)==1 && isset($this->blocks['content'])) $idNext='content';
        foreach ($this->blocks as $idBlock => $temp_block) {
            if ($idBlock === $idNext) {
                $tempBlocks[$block->getId()] = $block;
            }
            $tempBlocks[$idBlock] = $temp_block;
        }
        if ($idNext == 'last')
            $tempBlocks[$block->getId()] = $block;
        $this->blocks = $tempBlocks;
    }

    /**
     * Remove a block of container block
     * @param string $idBlock 
     */
    public function rmBlock($idBlock) {
        if(isset($this->blocks[$idBlock])) {
            unset($this->blocks[$idBlock]);
	    return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Returns the HTML view of a block (displayed in front office)
     * @return string
     */
    public function getView() {
        ob_start();
	include($this->module . '/blocks/' . $this->blockName . '/view.php'); 
        return ob_get_clean();
    }

    /**
     * Returns the Admin view of a block (displayed in back office)
     * @return string
     */
    public function getAdminView() {
        ob_start();
	include($this->module . '/blocks/' . $this->blockName . '/adminView.php');
        return ob_get_clean();
    }

    /**
     * Returns HTML of view, manages cache of a block...
     * @return string
     */
    public function display() {
        if ($this->getConfig('allowedModules') != false) {
            $mods = explode(' ', $this->getConfig('allowedModules'));
            if (!in_array(MODULE, $mods))
                return '';
        }     
        $cacheFile = 'cache/' . PROFILE_PATH . $this->module . '/blocks/' . $this->blockName . '/' . THEME . '_' . MODULE . '_' . $this->id . '.cache';
        $maxAge = $this->getConfig('maxAge');
        if ($maxAge > 0 && is_file($cacheFile) && filemtime($cacheFile) + $maxAge > time()) {
            ob_start();
            include($cacheFile);
            $html = ob_get_clean();
        } else {
            if ($this->getConfig('tag') !== false)
                $balise = $this->getConfig('tag');
            else
                $balise = 'div';
            $html = '<' . $balise . ' id="' . $this->id . '" class="block '.$this->blockName.' ' . (string) $this->getConfig('cssClasses') . '">';
            if ($this->getConfig('headerTitle')) {
                    $html .= '<h2 class="parsiTitle">'.t($this->getConfig('headerTitle')).'</h2>';
            }
            if ($this->getConfig('ajaxReload')) {
                \app::$request->page->head .= '<script>$(document).ready(function(){setInterval("loadBlock(\'' . MODULE . '\', \'' . \app::$request->page->getId() . '\', \'' . $this->id . '\')", ' . $this->getConfig('ajaxReload') . '000);});</script>';
            }
            if ($this->getConfig('ajaxLoad')) {
                \app::$request->page->head .= '<script>$(document).ready(function(){loadBlock("' . MODULE . '", "' . \app::$request->page->getId() . '", "' . $this->id . '")});</script>';
            } else {
                /* Catch all exceptions or error in order to keep tha page structure in creation mode */
                try {
                    $view = $this->getView();
                    $html .= $view;
                } catch (\Exception $e) {
                    if(BEHAVIOR == 2){
                        /* Display Error or exception just for the dev */
                        ob_clean();
                        $html .= '<div class="PHPError"><div class="titleError"><strong>Block </strong>#'.$this->getId().' </div>';
                        $html .= '<div class="error"> <strong>'.t('Error').' '.t('in line').' </strong>'.$e->getLine().' : </strong>'.$e->getMessage().'</div>';
                        $html .= '<div class="file"><strong>File : </strong>'.$e->getFile().'</div></div>';
                    }
                    
                }
            }
            $html .= '<div class="clearboth"></div></' . $balise . '>';
            if ($maxAge > 0)
                tools::file_put_contents($cacheFile, $html);
        }
        return $html;
    }

    public function __toString() {
        $this->display();
    }
    
    public function __get($property) {
	if($property == 'module' || $property == 'blockName'){
	    $className = get_class($this);
	    if ($className != 'page')
		list( $module, $block, $blockName) = explode("\\", $className);
	    return $$property;
	}
	return FALSE;
    }

    /**
     * Generates the code to build a block
     * @static 
     * @param string $moduleName Module name where the block is created
     * @param string $blockName Block name to create
     */
    public static function build($moduleName, $blockName) {

        $moduleName = tools::sanitizeString($moduleName);
        $blockName = tools::sanitizeString($blockName);
        $dir = 'modules/' . $moduleName . '/blocks/' . $blockName;
        tools::createDirectory('modules/' . $moduleName . '/blocks/' . $blockName);
        $template = '<?php
        namespace ' . $moduleName . '\blocks;
        class ' . $blockName . ' extends \block {
        public function saveConfigs() {
        }
        }
        ?>';
        if(is_dir($dir)){
        file_put_contents('modules/' . $moduleName . '/blocks/' . $blockName . '/block.php', $template);
        file_put_contents('modules/' . $moduleName . '/blocks/' . $blockName . '/adminView.php', '');       
        file_put_contents('modules/' . $moduleName . '/blocks/' . $blockName . '/view.php', '');
            return TRUE;
        }else{
            return False;
        }
        
    }
    /**
     * Returns JS to eval in iframe admin when an ajax refresh occur 
     * @param string type og refresh
     * @return string JS
     */
    public function ajaxRefresh($type= FALSE) {
	if($type == 'add'){
	    return 'ParsimonyAdmin.addBlock("' . $this->id . '","' . preg_replace("@<script[^>]*>[\S\s]*?<\/script[^>]*>@i", "", str_replace('"', '\"', str_replace("\0", '', preg_replace("@[\t\n\r\v\x0B]@", "", $this->display())))) . '","dropInPage");$("#changeres").trigger("change");';
	}else{
	    return '$("#' . $this->id . '",ParsimonyAdmin.currentBody).replaceWith("' .  str_replace('"', '\"', str_replace("\0", '', preg_replace("@[\t\n\r\v\x0B]@", "", $this->display()))) . '");$("#changeres").trigger("change");';
	}
    }
    
    /**
     * Returns HTML of view, manages cache of a block
     * @param string $container
     * @param string $ident
     * @return block reference
     */
    public function &search_block($ident, $container = FALSE) {
        if($container === FALSE) $container = $this;
	if ($container->getId() === $ident || (is_numeric($ident) && $container->getId() === (int) $ident))
	    return $container;
	$blocks = $container->getBlocks();
	if (!empty($blocks)) {
	    foreach ($blocks AS $id => $block) {
		if ($id === $ident) {
		    return $block;
		} else {
		    $rbloc = & $this->search_block($ident, $block);
		    if (isset($rbloc))
			return $rbloc;
		}
	    }
	}
	return $rbloc;
    }

}

?>