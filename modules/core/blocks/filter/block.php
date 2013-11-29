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
 * @block_category database
 * @modules_dependencies core:1
 */
class filter extends \block {
	
	public function getView() {
		ob_start();
		echo '<form method="post" action="">';
		$blockquery = $this->getConfig('blockquery');

		$block = \app::$request->page->searchBlock($blockquery);
		if($block !== null){
			$properties = $this->getConfig('properties');
			$selected = $block->getConfig('selected');
			foreach ($selected as $value) {
				if(isset($value['filter'])){
					$table = $value['table'];
					$property = $value['property'];
					list($module, $entity) = explode('_', $table, 2);
					$field = \app::getModule($module)->getEntity($entity)->getField($property);
					$template = isset($properties[$table.'.'.$property]['tpl']) ? $properties[$table.'.'.$property]['tpl'] : 'string';
					$configs = $properties[$table.'.'.$property];
					$defaultconfigs = $properties[$table.'.'.$property]['default'];
					if(!isset($_POST['submitfilter'])){
						if($configs['tpl'] == 'string') {
							$_POST['filter'][$property] = isset($defaultconfigs['rangeStart']) ? $defaultconfigs['rangeStart'] : '';
							
						}elseif($configs['tpl'] == 'choice' && isset($defaultconfigs['rangeStart']) && $defaultconfigs['rangeStart'] !== '') {
							$_POST['filter'][$property][] = $defaultconfigs['rangeStart'];
							
						}elseif($configs['tpl'] == 'range') {
							$_POST['filter'][$property]['start'] = isset($defaultconfigs['rangeStart']) ? $defaultconfigs['start'] : '';
							$_POST['filter'][$property]['end'] = isset($defaultconfigs['rangeStart']) ? $defaultconfigs['end'] : '';
							
						}elseif($configs['tpl'] == 'datetimerange' || $configs['tpl'] == 'daterange' ) {
							$now = new \DateTime('now');
							$nowEnd = new \DateTime('now');
							$nowformat = $now->format('Y-m-d H:i');
							$nowformat = str_replace(' ', 'T', $nowformat);
							if(!isset($defaultconfigs['state'] )){ // static values for fields\date or fields\publication
								if(isset($defaultconfigs['start'] )){
									
									$_POST['filter'][$property]['start'] = $defaultconfigs['start'];
								}
								if(isset($defaultconfigs['end'])){
									$_POST['filter'][$property]['end'] = $defaultconfigs['end'];
								}
							}else{ // dynamic values
								if(!isset($defaultconfigs['now-start'])){
									$selstart = $defaultconfigs['select-start'];
									$ys = $defaultconfigs['year-start'];
									$ms = $defaultconfigs['month-start'];
									$ds = $defaultconfigs['day-start'];
									if($ys == '' && $ms == '' && $ds =='') $_POST['filter'][$property]['start'] = '';
									else{
									$dateStart =  (($ys != '') ? $selstart . ' ' . $ys . ' year ' : '') . (($ms != '') ? $selstart . ' ' . $ms . ' month ' : '') . (($ds != '') ? $selstart . ' ' . $ds . ' day ' : '');
									 // date modify with after or before values
									$dynstart = $now->modify($dateStart);
									$dynstart =  $dynstart->format('Y-m-d H:i');
									$dynstart = str_replace(' ', 'T', $dynstart);

									// Start -> set dynamic property
									$_POST['filter'][$property]['start'] = $dynstart;
									}
								}else{ // now
									// Start -> set dynamic property to NOW
									$_POST['filter'][$property]['start'] = $nowformat;
								}
								if(!isset($defaultconfigs['now-end'])){
									$selend = $defaultconfigs['select-end'];
									$ys = $defaultconfigs['year-end'];
									$ms = $defaultconfigs['month-end'];
									$ds = $defaultconfigs['day-end'];										
									if($ys == '' && $ms == '' && $ds =='') $_POST['filter'][$property]['end'] = '';
									else{
									$dateEnd = (($ys != '') ? $selend . ' ' . $ys . ' year ' : '') . (($ms != '') ? $selend . ' ' . $ms . ' month ' : '') . (($ds != '') ? $selend . ' ' . $ds . ' day ' : '');
									 // date modify with after or before values
									$dynend = $nowEnd->modify($dateEnd);
									$dynend =  $dynend->format('Y-m-d H:i');
									$dynend = str_replace(' ', 'T', $dynend);

									$_POST['filter'][$property]['end'] = $dynend;
									}

								}else{ 
									// End -> set dynamic property to NOW
									$_POST['filter'][$property]['end'] = $nowformat;
								}
							}
						}

					}
				
					include('modules/core/blocks/filter/views/'.$template.'.php');
				}
			}
			echo '<div class="groupfilter"><h2>Group by</h2>';
			foreach ($selected as $value) {
				if(isset($value['filter'])){
					$table = $value['table'];
					$prop = $value['property'];
					list($module, $entity) = explode('_', $table, 2);
					$field = \app::getModule($module)->getEntity($entity)->getField($prop);
					if(get_class($field) === 'core\fields\date' || get_class($field) === 'core\fields\publication'){
						echo '<div>' . $prop . ' : <select name="group['.$prop.']"><option></option><option>day</option><option>month</option><option>year</option></select></div>';
					}else{
						echo '<div>' . $prop . ' : <input type="checkbox" name="group['.$prop.']"></div>';
					}
				}
			}
		}	
		echo '</div><input type="submit" name="submitfilter"></form>';
		return ob_get_clean();
	}

	/**
	 * Save the block configs
	 */
	public function saveConfigs() {
		$blockquery = $this->getConfig('blockquery');
		$block = \app::$request->page->searchBlock($blockquery);
		$this->setConfig('blockquery', $_POST['blockquery']);
		if(isset($_POST['properties'])){
			$this->setConfig('properties', $_POST['properties']);	
		}
		
	}

}
?>
