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
	public function __construct($id) {
		$this->setId($id);
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
	 * @return block object
	 */
	public function setId($id) {
		$id = \tools::sanitizeTechString($id);
		if (!empty($id)) {
			$this->id = $id;
		} else {
			throw new \Exception(t('ID can\'t be empty', FALSE));
		}
		return $this;
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
	 * @return block object
	 */
	public function setConfig($key, $value) {
		if (!is_resource($value)) {
			$this->configs[$key] = $value;
			return $this;
		} else {
			throw new Exception(t('A block config can\'t be a resource.', FALSE));
		}
	}

	/**
	 * Remove one config of block 
	 * @param string $key key of the config
	 * @return block object
	 */
	public function removeConfig($key) {
		if (isset($this->configs[$key])){
			unset($this->configs[$key]);
		}
		return $this;
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
	 * @return block object
	 */
	public function addBlock(\block $block, $idNext = 'last') {
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
		return $this;
	}

	/**
	 * Remove a block of container block
	 * @param string $idBlock 
	 * @return bool|block object
	 */
	public function rmBlock($idBlock) {
		if(isset($this->blocks[$idBlock])) {
			unset($this->blocks[$idBlock]);
			return $this;
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
		include($this->moduleName . '/blocks/' . $this->blockName . '/view.php'); 
		return ob_get_clean();
	}

	/**
	 * Returns the Admin view of a block (displayed in back office)
	 * @return string
	 */
	public function getAdminView() {
		ob_start();
		include($this->moduleName . '/blocks/' . $this->blockName . '/adminView.php');
		return ob_get_clean();
	}

	/**
	 * Returns HTML view, manages cache of a block...
	 * @return string
	 */
	public function display() {

		$html = '';
		$balise = 'div';
		$maxAge = 0;
		$headerTitle = $CSSclasses = $attributes = '';
		$ajaxLoad = FALSE;

		foreach($this->configs AS $name => $config){
			switch ($name) {
				case 'allowedModules':
					if (is_array($config) && !in_array(MODULE, $config))
						return '';
					break;
				case 'allowedRoles':
				   if (is_array($config) && !in_array($_SESSION['id_role'], $config))
						return '';
					break;
				case 'tag':
				   $balise = $config;
					break;
				case 'maxAge':
				   $maxAge = (int) $config;
					break;
				case 'headerTitle':
				   if($config) $headerTitle = '<h2 class="parsiTitle">'.t($config).'</h2>';
					break;
				case 'cssClasses':
				   $CSSclasses .= ' '.$config;
					break;
				case 'attributes':
				   $attributes .= ' '.$config;
					break;
				case 'ajaxReload':
				   if((int)$config > 0) \app::$request->page->head .= '<script>$(document).ready(function(){setInterval("loadBlock(\'' . MODULE . '\', \'' . \app::$request->page->getId() . '\', \'' . $this->id . '\')", ' . $config . '000);});</script>';
					break;
				case 'ajaxLoad':
					if($config == 1) {
						$ajaxLoad = TRUE;
						\app::$request->page->head .= '<script>$(document).ready(function(){loadBlock("' . MODULE . '", "' . \app::$request->page->getId() . '", "' . $this->id . '")});</script>';
					}
					break;
				case 'CSSFiles':
					foreach ($config AS $file => $pos)
						\app::$request->page->addCSSFile(strstr($file, '//') ? $file : $file, $pos);
						break;
				case 'JSFiles':
					foreach ($config AS $file => $pos)
						\app::$request->page->addJSFile(strstr($file, '//') ? $file : $file, $pos);
					break;
				default:
					break;
			}
		}

		$cacheFile = 'var/cache/' . PROFILE_PATH . $this->moduleName . '/blocks/' . $this->blockName . '/' . THEME . '_' . MODULE . '_' . $this->id . '.cache';
		if ($maxAge > 0 && is_file($cacheFile) && filemtime($cacheFile) + $maxAge > time()) {
			ob_start();
			include($cacheFile);
			$html .= ob_get_clean();
		} else {
			$html .= '<' . $balise . ' id="' . $this->id . '" class="parsiblock '.$this->moduleName.'_'.$this->blockName. $CSSclasses . '"' . $attributes . '>'.$headerTitle;
			if ($ajaxLoad === FALSE) {
				/* Catch all exceptions or error in order to keep tha page structure in creation mode */
				try {
					$view = $this->getView();
					$html .= $view;
				} catch (\Exception $e) {
					if($_SESSION['behavior'] == 2){
						/* Display Error or exception just for the dev */
						ob_clean();
						$html .= '<div class="PHPError"><div class="titleError"><strong>Block </strong>#'.$this->getId().' </div>';
						$html .= '<div class="error"> <strong>'.t('Error').' '.t('in line').' </strong>'.$e->getLine().' : </strong>'.$e->getMessage().'</div>';
						$html .= '<div class="file"><strong>File : </strong>'.$e->getFile().'</div></div>';
					}
				}
			}
			$html .= '</' . $balise . '>';
			if ($maxAge > 0)
				tools::file_put_contents($cacheFile, $html);
		}
		return $html;
	}

	public function __toString() {
		$this->display();
	}

	public function __get($property) {
		$className = get_class($this);
		if ($className !== 'page')
			list( $moduleName, $block, $blockName) = explode("\\", $className);
		return $$property;
	}

	/**
	 * Generates the code to build a block
	 * @static 
	 * @param string $moduleName Module name where the block is created
	 * @param string $blockName Block name to create
	 * @param string $extends
	 * @param string $configs
	 */
	public static function build($moduleName, $blockName, $extends, $configs, $viewPath) {

		$moduleName = tools::sanitizeString($moduleName);
		$blockName = tools::sanitizeString($blockName);
		$licence = str_replace('{{module}}', $blockName, file_get_contents("modules/admin/licence.txt"));
		$dir = 'modules/' . $moduleName . '/blocks/' . $blockName;
		tools::createDirectory('modules/' . $moduleName . '/blocks/' . $blockName);
		list($moduleFrom, $b, $nameFrom) = explode('\\', $extends);
		$template = '<?php
' . $licence . '
	
namespace '.$moduleName.'\blocks;

/**
 * @title '.$blockName.'
 * @description '.$blockName.'
 * @version 1
 * @browsers all
 * @php_version_min 5.3
 * @block_category '.$moduleName.'
 * @modules_dependencies '.$moduleFrom.':1
 */

class '.$blockName.' extends \\'.$extends.' {

	public function __construct($id) {
		parent::__construct($id);
		$configs = \''.$configs.'\';
		$this->configs = unserialize(base64_decode($configs));
	}

	public function getAdminView() {
		ob_start();
		include(\'modules/'.$moduleFrom.'/blocks/'.$nameFrom.'/adminView.php\');
		return ob_get_clean();
	}

}
?>';
		if (is_dir($dir)) {
			file_put_contents('modules/' . $moduleName . '/blocks/' . $blockName . '/block.php', $template);
			file_put_contents('modules/' . $moduleName . '/blocks/' . $blockName . '/icon.png', file_get_contents('modules/' . $moduleFrom . '/blocks/' . $nameFrom . '/icon.png'));
			file_put_contents('modules/' . $moduleName . '/blocks/' . $blockName . '/view.php', file_get_contents($viewPath, FILE_USE_INCLUDE_PATH));
			$return = array('eval' => '', 'notification' => t('Block has been created', FALSE), 'notificationType' => 'positive');
		} else {
			$return = array('eval' => '', 'notification' => t('Block has\'nt been created', FALSE), 'notificationType' => 'negative');
		}
		\app::$response->setHeader('X-XSS-Protection', '0');
		\app::$response->setHeader('Content-type', 'application/json');
		return json_encode($return);
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