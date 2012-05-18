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
*  @authors Julien Gras et Benoît Lorillot
*  @copyright  Julien Gras et Benoît Lorillot
*  @version  Release: 1.0
 * @category  Parsimony
 * @package core\fields
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace core\fields;
/**
 * field_flash Class 
 * Create a field_flash field
 */ 
class field_flash extends \field {

	/** @var string $title by default 'Flash' */
        protected $title = 'Flash';
/**
 * Build a field_code field
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
 * @param string $regex by default '.*'
 * @param string $width by default ''
 * @param string $height by default ''
 * @param string $path by default 'flashs'
 */ 
        public function __construct($module, $entity, $name, $type='varchar', $characters_max='255', $characters_min=0, $label='', $text_help='', $msg_error='invalid', $default='', $required=TRUE, $regex='.*', $visibility = 7, $width='', $height='', $path='files') {
            $this->constructor(func_get_args());
        }

    }

?>