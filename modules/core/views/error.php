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
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>PHP Error</title>
	<style>
	    body{color: #D8D5D2;background: #D8D5D2;}
	    #message{background: #FBFAF5;border:1px #444 solid;position: relative;top:200px;margin:0 auto;max-width:650px;padding:10px;color:#484848;border-radius:10px}
	    #message div{margin:5px}
	    .label{text-decoration: underline;font-weight: bold;}
	</style>
    </head>
    <body>
	<div id="message">
	    <div><span class="label"><?php echo t('Code') ?></span>: <?php echo $code ?></div>
	    <div><span class="label"><?php echo t('File') ?> </span>: <?php echo $file ?></div>
	    <div><span class="label"><?php echo t('In line') ?> </span>: <?php echo $line ?></div>
	    <div><span class="label"><?php echo t('Message') ?> </span>: <?php echo $message ?></div>
	</div>
    </body>
</html>