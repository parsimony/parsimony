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
 * @title Mail
 * @description Mail
 * @version 1
 * @browsers all
 * @php_version_min 5.3
 * @modules_dependencies core:1
 */

class field_mail extends \field {

	/**
	 * Build a field_mail field
	 * @param string $module
	 * @param string $entity 
	 * @param string $name 
	 * @param string $type by default 'varchar'
	 * @param integer $characters_max by default '255'
	 * @param integer $characters_min by default 0
	 * @param string $label by default ''
	 * @param string $text_help by default ''
	 * @param string $msg_error by default invalid
	 * @param string $default by default ''
	 * @param bool $required by default true
	 * @param string $regex by default '^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$'
	 */
	public function __construct($module, $entity, $name, $type = 'varchar', $characters_max = '255', $characters_min = 0, $label = '', $text_help = '', $msg_error = 'invalid', $default = '', $required = TRUE, $regex = '^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$', $visibility = 7, $unique = FALSE) {
		$this->constructor(func_get_args());
	}

	/**
	 * Validate field
	 * @param string $value
	 * @return string
	 */
	public function validate($value) {
		$length = strlen($value);
		if($length >= $this->characters_min && $length <= $this->characters_max){
			if (!$this->required && empty($value))
				return '';
			else{
				$args = func_get_args();
				if($this->unique && isset($args[1])){
					if($args[1] === 'insert')
						$args[1] = FALSE;
					if($this->checkUniqueAction($value, $args[1]) == 0)
						return FALSE;
				}
				return filter_var($value, FILTER_VALIDATE_EMAIL);
			}
		}
		return FALSE;
	}

	public function checkUniqueAction($chars, $id = FALSE) {
		$entity = \app::getModule($this->module)->getEntity($this->entity);
		$query = 'SELECT ' . $this->name . ' FROM ' . PREFIX . $this->module . '_' . $this->entity . ' WHERE ' . $this->name .' = :chars';
		$params = array(':chars' => $chars);
		if($id !== FALSE) {
			$query .= ' AND '.$entity->getId()->name.' != :id';
			$params[':id'] = $id;
		}
		$sth = \PDOconnection::getDB()->prepare($query);
		$sth->execute($params);
		if($sth->fetch() !== FALSE){
			return '0';
		}else{
			return '1';
		}
	}

}

?>
