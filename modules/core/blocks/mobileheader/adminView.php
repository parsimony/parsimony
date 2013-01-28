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
?>
<style>
    .boxes{border: 1px solid #CCC;
	   margin-left: 5px;
	   margin-top: 8px;
	   background: #EDEFF4;
	   padding: 8px 0;}
    label{padding-left: 10px;
	  color: #555;
	  text-shadow: 0px 1px 0px #ccc;
	  font-size: 15px;
	  letter-spacing: 1.5px;display: inline-block;
	  width: 180px;
	  text-align: left;
	  margin: 15px 0 4px 80px;}
    .title{padding-left: 10px;
	   color: #276D7F;
	   text-shadow: 0px 1px 0px white;
	   font-size: 15px;
	   letter-spacing: 1.5px;}
    input{width: 240px;}

</style>
<div class="boxes">
    <label><?php echo t('Title') ?></label><input type="text" name="text" value="<?php echo $this->getConfig('text'); ?>">
</div>
<div class="boxes">
    <label><?php echo t('Color') ?></label><input type="color" name="color" value="<?php if($this->getConfig('color')) echo $this->getConfig('color'); else echo '#7C8DB6'; ?>">
</div>
<div class="boxes">
    <div class="title"><?php echo t('Left Button') ?></div>
    <label><?php echo t('URL') ?></label><input type="text" name="lefturl" value="<?php echo $this->getConfig('lefturl'); ?>">

    <label><?php echo t('Label') ?></label><input type="text" name="leftbutton" value="<?php echo $this->getConfig('leftbutton'); ?>">
</div>

<div class="boxes">
    <div class="title"><?php echo t('Right Button') ?></div>
    <label><?php echo t('URL') ?></label><input type="text" name="righturl" value="<?php echo $this->getConfig('righturl'); ?>">

    <label><?php echo t('Label') ?></label><input type="text" name="rightbutton" value="<?php echo $this->getConfig('rightbutton'); ?>">
</div> 
