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
 * @package core/fields
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 ?>
<div style="margin: 10px 0;font-size: 13px;padding-left: 10px;"><?php echo t('Properties of 1:n Relationship',False) ; ?> </div>
<div><label><?php echo t('Referring Module',False) ; ?> </label><input type="text" name="moduleLink"></div>
<div><label><?php echo t('Referring Entity',False) ; ?> </label><input type="text" name="link"></div>
<div><label><?php echo t('Template of link',False) ; ?> </label><input type="text" name="templatelink"></div>