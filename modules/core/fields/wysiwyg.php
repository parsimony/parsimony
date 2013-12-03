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
 * @title Wysiwyg
 * @description Wysiwyg
 * @version 1
 * @browsers all
 * @php_version_min 5.3
 * @modules_dependencies core:1
 */

class wysiwyg extends \field {
	
	protected $type = 'LONGTEXT';
	protected $characters_max = '';
	protected $wysiwygModules = 'bold,underline,italic,justifyLeft,justifyCenter,justifyRight,strikeThrough,subscript,superscript,orderedList,unOrderedList,undo,redo,outdent,indent,removeFormat,createLink,unlink,formatBlock,foreColor,hiliteColor';
	protected $editMode = 'fieldwysiwyg';
	
	/**
	 * Validate the value of Field
	 * @param string $value
	 * @return string|false
	 */
	public function validate($value) {
		return \tools::sanitize($value, $this->wysiwygModules);
	}
	
	public static function loadExternalFiles() {
		\app::$request->page->addJSFile('lib/HTML5editor/HTML5editor.js');
		\app::$request->page->addCSSFile('lib/HTML5editor/HTML5editor.css');
		\app::$request->page->addJSFile('core/fields/wysiwyg/script.js');\app::$request->page->addJSFile('core/blocks/wysiwyg/edit.js');
	}
	
	public function getEditOptions() {
		return  'data-wysiwygplugins="saveedit,canceledit,' . $this->wysiwygModules . '"' . str_replace('class="parsieditinline', 'class="parsieditinline field_wysiwyg', parent::getEditOptions());
	}
	

}
\app::addListener('editLoad', array('core\fields\wysiwyg', 'loadExternalFiles'));

?>
