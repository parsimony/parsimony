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
 * @title Alias
 * @description Alias
 * @version 1
 * @browsers all
 * @php_version_min 5.3
 * @modules_dependencies core:1
 */

class alias extends \field {

	protected $visibility = 1;
	protected $calculation = '';
	
	public function validate($value) {
		return $value;
	}
	
	public function display() {
		return $this->value;
	}
	
	public function editInline() {
		return (string) $this->value; /* called by toString() */
	}
	
	public function editInlineForAuthor($authorID) {
		return $this->value;
	}

	public function saveEditInlineAction($id, $data = FALSE) {
		return FALSE;
	}

	/**
	 * Display grid
	 * @return string
	 */
	public function displayGrid() {
		return $this->value;
	}

	/**
	 * Display Updating Form
	 * @return string
	 */
	public function form() {
		return '';
	}
	
	public function editInlineFormAction($id) {
		return FALSE;
	}
	
	public function getTableName() {
		return $this->name;
	}
	
	public function getFullName() {
		return $this->name;
	}
	
	public function sqlGroup($group) { /* can't group by an alias cause of function doesn't work in group by clause */
		return '';
	}

}

?>
