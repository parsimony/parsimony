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
 * @package core
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$cachePath = 'cache/'.urlencode($_GET['x'].$_GET['y'].$_GET['path']);
if(file_exists($cachePath)){
    list($width, $height, $type, $attr) = getimagesize($cachePath);
	 switch ($type) {
            case 1:
                header("Content-type: image/gif");
                break;
            case 2:
                header("Content-type: image/jpeg");
                break;
            case 3:
                header("Content-type: image/png");
                break;
            case 15:
                header("Content-type: image/wbmp");
                break;
            default:
                break;
        }
	echo file_get_contents($cachePath);
	exit;
}


$im = new img($_GET['path']);
if(!isset($_GET['crop'])){
	$im->resize($_GET['x'],$_GET['y']);
}else{
	$im->crop($_GET['x'],$_GET['y']);
}
$im->save($cachePath,100);
$im->display();

?>
