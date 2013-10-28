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
 * @title IP Address
 * @description IP Address
 * @version 1
 * @browsers all
 * @php_version_min 5.3
 * @modules_dependencies core:1
 */

class ip extends \field {

	protected $characters_max = 45;
	protected $regex = '^(?:(?>(?>([a-f0-9]{1,4})(?>:(?1)){7})|(?>(?!(?:.*[a-f0-9](?>:|$)){8,})((?1)(?>:(?1)){0,6})?::(?2)?))|(?>(?>(?>(?1)(?>:(?1)){5}:)|(?>(?!(?:.*[a-f0-9]:){6,})((?1)(?>:(?1)){0,4})?::(?>(?3):)?))?(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])(?>\.(?4)){3}))$$';

	/**
	 * Validate field
	 * @param string $value
	 * @return string
	 */
	public function validate($value) {
		if ($this->required && empty($value)) /* By default, if it's required, we take user IP */
			return $_SERVER['REMOTE_ADDR'];
		else{
			return filter_var($value, FILTER_VALIDATE_IP);
		}
	}

}

?>
