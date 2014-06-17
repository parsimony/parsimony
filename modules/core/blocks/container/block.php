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
 * @title Container
 * @description can contain other blocks and is used to structure the page
 * @copyright 1
 * @browsers all
 * @php_version_min 5.3
 * @block_category containers
 * @modules_dependencies core:1
 */
class container extends \block {

	public function display() {
		if ($this->getConfig('column')) {
			\app::$response->head .= '<style> #' . $this->getId() . ' > .parsiblock{display:inline-block} </style>';
			$this->setConfig('cssClasses', ' column' . $this->getConfig('cssClasses'));
		}
		return parent::display();
	}

	public function getView() {
		$html = '';
		if (!empty($this->blocks)) {
			foreach ($this->blocks as $block) {
				$html .= $block->display();
			}
		}
		return $html;
	}

	public function setBlocks($blocks) {
		$this->blocks = $blocks;
	}

	public function ajaxRefresh($type = FALSE) {
		if ($type === 'add') {
			return parent::ajaxRefresh($type);
		} else {
			return 'document.getElementById("preview").contentWindow.location.reload()';
		}
	}
	
	public function onMove($typeProgress, $module, $name, $copy = FALSE) {
		if (!empty($this->blocks)) {
			foreach ($this->blocks as $idBlock => $block) {
				if ($typeProgress === 'theme') {
					$idBlock = strtolower($idBlock);
					$block->setId($idBlock);
				} else {
					$idBlock = ucfirst($idBlock);
					$block->setId($idBlock);
				}
				if(method_exists($block, 'onMove')) {
					
					$block->onMove($typeProgress, $module, $name, $copy);
				}
			}
		}
	}

}

?>