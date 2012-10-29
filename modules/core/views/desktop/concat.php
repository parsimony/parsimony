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
 * @package core
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
if ($_REQUEST['format'] == 'css')
    app::$response->setFormat('css');
else
    app::$response->setFormat('js');
$hash = md5(app::$request->getParam('files'));
$pathCache = 'cache/' . $hash . '.' . $_REQUEST['format'];
if (is_file($pathCache) && app::$config['dev']['status'] == 'prod') {
    app::$response->setHeader('Expires', gmdate( 'D, d M Y H:i:s', time() + 999999 ) . ' GMT' );
    include($pathCache);
} else {
    $files = explode(',', $_REQUEST['files']);
    ob_start();
    foreach ($files as $file) {
	$pathParts = pathinfo($file,PATHINFO_EXTENSION);
	if($pathParts == 'js' || $pathParts=='css'){
	    $path = stream_resolve_include_path ($file);
	    if($path) include($path);
	}
    }
    $html = ob_get_clean();
    echo $html;
    file_put_contents($pathCache,$html);
}

?>