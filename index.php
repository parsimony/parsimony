<?php
include('install.php');exit;
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
 * @package Parsimony
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define('PARSIMONY_VERSION','0.4');

error_reporting(E_ALL);
ini_set('display_errors', '1');

include('modules/core/classes/app.php');
spl_autoload_register('\core\classes\app::autoLoad');
set_error_handler('\core\classes\app::errorHandler');
set_exception_handler('\core\classes\app::exceptionHandler');
register_shutdown_function('\core\classes\app::errorHandlerFatal');
class_alias('core\classes\app','app');
new \core\classes\app();
?>