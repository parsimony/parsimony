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
 * @package core\fields
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace core\fields;

/**
 * @title Decimal
 * @description Decimal
 * @version 1
 * @browsers all
 * @php_version_min 5.3
 * @modules_dependencies core:1
 */

class field_decimal extends \field {

    /**
     * Build a field_numeric field
     * @param string $module
     * @param string $entity 
     * @param string $name 
     * @param string $type by default 'INT'
     * @param integer $characters_max by default '2'
     * @param integer $characters_min by default 0
     * @param string $label by default ''
     * @param string $text_help by default ''
     * @param string $msg_error by default invalid
     * @param string $default by default ''
     * @param bool $required by default true
     * @param string $regex by default '[0-9]*'
     */
    public function __construct($module, $entity, $name, $type = 'DECIMAL', $characters_max = '20,6', $characters_min = 0, $label = '', $text_help = '', $msg_error = 'invalid', $default = '', $required = TRUE, $regex = '[0-9\.,]*', $visibility = 7) {
        $this->constructor(func_get_args());
    }
    /**
     * Validate field
     * @param string $value
     * @return string
     */
    public function validate($value) {
        if(!$this->required && empty($value)) return $value;
	$value = str_replace(',', '.', $value);
	$testValue = str_replace('.', '', $value);
	$cutMax = explode(',',$this->characters_max);
	$cutValue = explode('.',$value);
        if(is_numeric($testValue) && strlen($value) <= $cutMax[0] && strlen($cutValue[1]) <= $cutMax[1]){
	    return $value;
	}else{
	    return FALSE;
	} 
    }

}

?>
