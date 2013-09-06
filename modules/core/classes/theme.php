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
 *  Theme Class 
 *  Manages themes
 */
class theme extends \core\blocks\container {

	/** @var string name */
	private $name;

	/** @var string Theme id */
	protected $id = 'container';

	/** @var string themetype */
	protected $themetype;

	/** @var string block type name */
	protected $blockName = 'container';

	/**
	 * Build a block object
	 * @param string $id Block ID 
	 */
	public function __construct($id, $name, $themetype, $module) {
		$this->setId($id);
		$this->setName($name);
		$this->setThemeType($themetype);
		$this->setModule($module);
		$this->addBlock(new \core\blocks\page('content'));
	}

	/**
	 * Get theme name
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Set theme name
	 * @param string $name
	 */
	public function setName($name) {
		if (!empty($name)) {
			$this->name = \tools::sanitizeTechString($name);
		} else {
			throw new \Exception(t('Name can\'t be empty', FALSE));
		}
	}

	/**
	 * Get theme type
	 * @return string 
	 */
	public function getThemeType() {
		return $this->themetype;
	}

	/**
	 * Get module
	 * @return string 
	 */
	public function getModule() {
		return $this->module;
	}

	/**
	 * Set theme type
	 * @param string $themetype
	 */
	public function setThemeType($themetype) {
		if (!empty($themetype)) {
			$this->themetype = $themetype;
		} else {
			throw new \Exception(t('Themetype can\'t be empty', FALSE));
		}
	}

	/**
	 * Set module
	 * @param string $module
	 */
	public function setModule($module) {
		$this->module = $module;
	}


	/**
	 * Serialize and Save this theme object
	 * @return bool
	 */
	public function save() {
		return \tools::serialize(PROFILE_PATH . $this->module . '/themes/' . $this->name . '/' . $this->themetype . '/theme', $this);
	}

	/**
	 * Serialize and Save this theme object
	 * @param string $module
	 * @param string $name
	 * @param string $themetype
	 */
	public static function get($module, $name, $themetype) {
		$file = stream_resolve_include_path($module . '/themes/' . $name . '/' . $themetype. '/theme.' .\app::$config['dev']['serialization']) ;
		if ($file) {
			return \tools::unserialize(substr($file,0,-4));
		} else {
			$theme = new theme('container', $name, $themetype, $module);
			$theme->save();
			return $theme;
		}
	}

}

?>
