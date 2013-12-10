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
 * @authors Julien Gras et BenoÃ®t Lorillot
 * @copyright  Julien Gras et BenoÃ®t Lorillot
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

		$html = '';
		$blockquery = $this->getConfig('blockquery');

		$block = \app::$request->page->searchBlock($blockquery);
		if($block !== null){
			
			$filterhtml = '';
			$grouphtml = '';
			$sorthtml = '';
			$properties = $this->getConfig('properties');
			$selected = $block->getConfig('selected');
			
			$countFil = -1;$endfil = '';$filhtml = '';
			foreach ($selected as $tabprop => $val) {
				
				// Define property & field
				
				if(isset($val['alias'])){
					$property = $name = $val['alias'];
					$field = new \core\fields\alias ($name, array('label' => $name , 'calculation' => ' ( '. $val['calculated']. ' ) '));
				}else{
					$table = $val['table'];
					$property = $val['property'];
					list($module, $entity) = explode('_', $table, 2);
					$field = \app::getModule($module)->getEntity($entity)->getField($property);	
				}
				/* Default SORT value */
				if(isset($val['sort'])){
					$sortconfigs = $properties[$tabprop]['sort'];
					if(!isset($_POST['submitfilter'])){
						$_POST['sort'][$property] = isset($sortconfigs) ? $sortconfigs : '';
					}
				}
				/* Default GROUP value */
				if(isset($val['group'])){	
					$groupconfigs = $properties[$tabprop]['group'];
					if(!isset($_POST['submitfilter'])){
						$_POST['group'][$property] = isset($groupconfigs) ? $groupconfigs : '';
					}
				}
				
				/* FILTER */
				ob_start();
				if(isset($val['filter'])){
					$template = isset($properties[$tabprop]['tpl']) ? $properties[$tabprop]['tpl'] : 'string';
					$configs = $properties[$tabprop];
					/* Default FILTER value */
					$defaultconfigs = $properties[$tabprop]['default'];
					if(!isset($_POST['submitfilter'])){
						
						/* Default FILTER TPL */
						if($configs['tpl'] == 'string' || $configs['tpl'] == 'select') {
							$_POST['filter'][$property] = isset($defaultconfigs['rangeStart']) ? $defaultconfigs['rangeStart'] : '';
							
						}elseif($configs['tpl'] == 'choice' && isset($defaultconfigs['rangeStart']) && $defaultconfigs['rangeStart'] !== '') {
							$_POST['filter'][$property][] = $defaultconfigs['rangeStart'];
							
						}elseif($configs['tpl'] == 'range') {
							$_POST['filter'][$property]['start'] = isset($defaultconfigs['rangeStart']) ? $defaultconfigs['rangeStart'] : '';
							$_POST['filter'][$property]['end'] = isset($defaultconfigs['rangeEnd']) ? $defaultconfigs['rangeEnd'] : '';
							
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
				$filterhtml .= ob_get_clean();
					
				/* GROUP */
				ob_start();
				if(isset($val['group'])){
						if(get_class($field) === 'core\fields\date' || get_class($field) === 'core\fields\publication'){
							?>
							<div><label><?php echo $property ?></label>
							<select name="group[<?php echo $property ?>]">
								<option></option>
								<option <?php echo (isset($_POST['group']) && isset($_POST['group'][$property]) && $_POST['group'][$property] === 'day' ? ' selected="selected"' : '') ?>>day</option>
								<option <?php echo (isset($_POST['group']) && isset($_POST['group'][$property]) && $_POST['group'][$property] === 'month' ? ' selected="selected"' : '') ?>>month</option>'
								<option <?php echo (isset($_POST['group']) && isset($_POST['group'][$property]) && $_POST['group'][$property] === 'year' ? ' selected="selected"' : '') ?>>year</option>
							</select>
							</div>
						<?php
						}else{ 
							?>
							<div>
								<label><?php echo $property ?></label>
								<input type="checkbox" name="group[<?php echo $property ?>]" <?php echo  (isset($_POST['group']) && (isset($_POST['group'][$property])) && ($_POST['group'][$property] == 1) ? ' checked="checked"' : '') ?>>
							</div>
						<?php
						}
				}
				$grouphtml .= ob_get_clean();
			
				/* SORT */
				ob_start();
				if(isset($val['sort'])){
					?>
					<div><label><?php echo $property ?></label>
						<select name="sort[<?php echo $property ?>]">
								<option></option>
								<option value="asc" <?php echo  (isset($_POST['sort']) && isset($_POST['sort'][$property]) && $_POST['sort'][$property] === 'asc' ? ' selected="selected"' : '') ?>>ASC</option>
								<option value="desc" <?php echo  (isset($_POST['sort']) && isset($_POST['sort'][$property]) && $_POST['sort'][$property] === 'desc' ? ' selected="selected"' : '') ?>>DESC</option>
						</select>
					</div>
					<?php
				}
				$sorthtml .= ob_get_clean();
				}
		}	
		$filtertitle = '<h2>'.t('Filter by').'</h2>';
		$grouptitle = '<h2>'.t('Group by').'</h2>';
		$sorttitle = '<h2>'.t('Sort by').'</h2>';
		$html = '<form method="post" action=""><input type="hidden" name="TOKEN" value="'.TOKEN .'"/>'.((!empty($filterhtml)) ? '<div>'.$filtertitle.$filterhtml.'</div class="filter">' : ''). ((!empty($grouphtml)) ? '<div class="groupfilter">'.$grouptitle.$grouphtml.'</div>' : '').((!empty($sorthtml)) ? '<div class="sortfilter">'.$sorttitle.$sorthtml.'</div>' : '').'<input type="submit" name="submitfilter"></form>';
		return $html;
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
