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
 * @package core\fields
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace core\fields;

/**
 * @title Date
 * @description Date
 * @version 1
 * @browsers all
 * @php_version_min 5.3
 * @modules_dependencies core:1
 */

class field_date extends \field {

	protected $type = 'DATETIME';
	protected $characters_max = '';
	protected $regex = '^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$';
	protected $use = 'normal';
	protected $templateDisplay = '%d %B %Y ,%H:%M';
	protected $templateForms = '%year% / %month% / %day% %hour% : %minute% : %second%';

	/**
	 * Validate field
	 * @param string $value
	 * @return string
	 */
	public function validate($value) {
		if(empty($value)){
			if($this->use === 'normal'){
				if(!$this->required)
					return '';
			}else{ // update || creation
				return gmdate('Y-m-d H:i:s', time());
			}
		}else{
			if(is_array($value)){
				return (isset($value['year']) ? str_pad($value['year'], 4, '0', STR_PAD_LEFT) : '0000') . '-' .
						(isset($value['month']) ? str_pad($value['month'], 2, '0', STR_PAD_LEFT) : '00') . '-' .
						(isset($value['day']) ? str_pad($value['day'], 2, '0', STR_PAD_LEFT) : '00') . ' ' .
						(isset($value['hour']) ? str_pad($value['hour'], 2, '0', STR_PAD_LEFT) : '00') . ':' .
						(isset($value['minute']) ? str_pad($value['minute'], 2, '0', STR_PAD_LEFT) : '00') . ':' .
						(isset($value['second']) ? str_pad($value['second'], 2, '0', STR_PAD_LEFT) : '00');
			}elseif (preg_match('#'.$this->regex.'#', $value, $date)) {
				if (checkdate($date[2], $date[3], $date[1])) {
					return $value;
				}
			}
		}
		return FALSE;
	}
	
	public function sqlGroup($group) {
		$sql = '';
		$name = $this->module . '_' . $this->entity . '.' . $this->name;
		switch($group){
			case 'day':
				$sql .= 'DAY(' . $name . ') + ';
			case 'month':
				$sql .= 'MONTH(' . $name . ') + ';
			case 'year':
				$sql .= 'YEAR(' . $name . ')';
		}
		return $sql;
	}

}

?>
