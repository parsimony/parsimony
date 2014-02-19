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
 * @package core\fields
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace core\fields;

/**
 * @title Password
 * @description Password
 * @copyright 1
 * @browsers all
 * @php_version_min 5.3
 * @modules_dependencies core:1
 */

class password extends \field {

	/**
	 * Validate field
	 * @param string $value
	 * @return string
	 */
	public function validate($value) {
		$length = strlen($value);
		if(!empty($value) && $length >= $this->characters_min && $length <= $this->characters_max){
			return sha1($value . \app::$config['security']['salt']);
		} elseif ($this->required) {
			return FALSE;
		}
		return '';
	}

}

?>
