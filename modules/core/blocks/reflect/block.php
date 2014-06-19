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
 * @copyright Julien Gras et Benoît Lorillot
 * 
 * @category Parsimony
 * @package core/blocks
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace core\blocks;

/**
 * @title Reflect
 * @description reflect another block
 * @copyright 1
 * @browsers all
 * @php_version_min 5.3
 * @block_category database
 * @modules_dependencies core:1
 */
class reflect extends \block {
	
	public function display() {

		$reflectedBlock = $this->getConfig('reflectedBlock');
		if($reflectedBlock){
			if($this->getConfig('blockType') === 'page') {
				$block = \app::$response->page->searchBlock($reflectedBlock);
			} else {
				$block = \app::$response->page->getTheme()->searchBlock($reflectedBlock);
			}

			if($block !== null){
				$block->setId($this->id);
				return $block->display();

			}
		}
		return 'Please enter  the ident of reflected block';

	}

	/**
	 * Save the block configs
	 */
	public function saveConfigs() {
		$this->setConfig('reflectedBlock', $_POST['reflectedBlock']);
		if(\app::$response->page->searchBlock($_POST['reflectedBlock'])) {
			$this->setConfig('blockType', 'page');
		} elseif(\app::$response->page->getTheme()->searchBlock($_POST['reflectedBlock'])) {
			$this->setConfig('blockType', 'theme');
		}
		
	}
	
}
?>
