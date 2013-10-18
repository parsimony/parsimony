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
 * @package core/blocks
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace core\blocks;

/**
 * @title Filter
 * @description displays a Code editor (PHP, js, HTML, CSS)
 * @version 1
 * @browsers all
 * @php_version_min 5.3
 * @modules_dependencies core:1
 */
class filter extends \block {
	
	public function getView() {
		ob_start();
		echo '<form method="post" action="">';
		$blockquery = $this->getConfig('blockquery') ? $this->getConfig('blockquery') : 'rapports';
		$block = \app::$request->page->searchBlock($blockquery);
		if($block){
			$propeties = $this->getConfig('properties');
			$selected = $block->getConfig('selected');
			foreach ($selected as $value) {
				if(isset($value['filter'])){
					$table = $value['table'];
					$property = $value['property'];
					list($module, $entity) = explode('_', $table, 2);
					$field = \app::getModule($module)->getEntity($entity)->getField($property);
					$template = isset($propeties[$table.'.'.$property]) ? $propeties[$table.'.'.$property] : 'string';
					include('modules/core/blocks/filter/views/'.$template.'.php');
				}
			}
		}
		echo '<select name="group[datecreation]"><option></option><option>day</option><option>month</option><option>year</option></select><input type="submit"></form>';
		return ob_get_clean();
	}

	/**
	 * Save the block configs
	 */
	public function saveConfigs() {
		$this->setConfig('blockquery', $_POST['blockquery']);
		$this->setConfig('properties', ( isset($_POST['properties']) ? $_POST['properties'] : array()));
	}

}
?>
