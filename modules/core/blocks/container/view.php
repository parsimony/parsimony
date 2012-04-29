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
 * @package core/blocks
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Softwarve License (OSL 3.0)
 */

$blocks = $this->getBlocks();
if($this->getConfig('column')){
    $width = $this->getConfig('with');
    if(!empty($width)){
        $width = 'width:'.$width.'px';
    }
    \app::$request->page->head .= '
    <style>
        #'.$this->getId().'{text-align: justify;-ms-text-justify: distribute-all-lines;text-justify: distribute-all-lines;}
        #'.$this->getId().' .clearboth {width: 100%;display: inline-block;font-size: 0;line-height: 0}
        #'.$this->getId().' .block{text-align: auto;-ms-text-justify: auto;text-justify: auto;'.$width.';font-size:inherit;display:inline-block;vertical-align:top;*display: inline;zoom: 1;
        -moz-box-sizing: border-box;-webkit-box-sizing: border-box;box-sizing: border-box;}
    </style>';
}
?>