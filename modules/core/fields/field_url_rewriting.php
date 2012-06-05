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
 * to contact@parsimony.mobi so we can send you a copy immediately.
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
 * field_url_rewriting Class 
 * Create a url_rewriting field
 */

class field_url_rewriting extends \field {

    /** @var string $title by default 'url_rewriting' */
    protected $title = 'url Rewriting';
    
    public function __construct($module, $entity, $name, $type='VARCHAR', $characters_max=255, $characters_min=0, $label='', $text_help='', $msg_error='invalid', $default='', $required=TRUE,$regex='[a-z0-9-]*', $visibility = 7, $propertyToURL='', $unique = FALSE) {
        $this->constructor(func_get_args());
    }
    
    public function validate($value) {
	if(isset($this->unique) && $this->unique){
	    if($this->checkUniqueAction($value) == 0) return FALSE;
	}
        return filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '#' . $this->regex . '#')));
    }
    
    public function checkUniqueAction($chars, $id = false) {
	if($this->unique){
            $entity = \app::getModule($this->module)->getEntity($this->entity);
            $obj = $entity->where($this->name.' = "'.$chars.'"');
            if($id != false && is_numeric($id)) $obj = $obj->where($entity->getId()->name.' != '.$id);
            $obj = $obj->fetch();
	    if(!$obj){
		return '1';
	    }else{
		return '0';
	    }
	}
	return FALSE;
    }

}

?>
