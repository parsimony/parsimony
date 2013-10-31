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
 * @package core/blocks
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if ($this->getConfig('mode') !== 'r') {
	$viewPath = $this->getConfig('viewPath');
	/* In case the file isn't in PROFILES/ */
	if (!is_file(PROFILE_PATH . $viewPath) && is_file('modules/' . $viewPath)) {
		\tools::createDirectory(dirname(PROFILE_PATH . $this->getConfig('viewPath')));
		copy('modules/' .$viewPath, PROFILE_PATH . $viewPath);
	}
	$path = PROFILE_PATH . $viewPath;
	$editorMode = 'application/x-httpd-php';
	include('modules/admin/views/editor.php');
}
?>
