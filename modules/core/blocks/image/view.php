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
 * @copyright Julien Gras et Benoît Lorillot
 * 
 * @category Parsimony
 * @package core/blocks
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

if($this->getConfig('lightbox') == '1'): ?>
	<style>
		.lightbox{display:none;position: fixed;top:0;bottom: 0;left:0;right:0;background: rgba(0,0,0,0.5);z-index: 9999;text-align: center;}
		.lightbox:target {outline: none;display:block;}
		.lightbox img {max-width: 80%;max-height: 80%;margin-top: 5%;background: #fff;padding: 10px;border-radius: 7px;box-shadow: 0 0 20px #222;}
	</style>

	<a href="#<?php echo $this->id ?>_img">
	  <img src="<?php echo BASE_PATH . $this->getConfig('imgPath'); ?>" title="<?php echo $this->getConfig('title'); ?>" alt="<?php echo $this->getConfig('alt'); ?>" style="box-sizing:border-box;width:<?php if($this->getConfig('width')){echo $this->getConfig('width') . 'px';}else echo '100%' ?>;<?php if($this->getConfig('height')){echo 'height:' . $this->getConfig('height') . 'px';} ?>;">
	</a>
	<a href="#<?php echo $this->id ?>_" class="lightbox" id="<?php echo $this->id ?>_img">
	  <img src="<?php echo BASE_PATH . $this->getConfig('imgPath'); ?>" title="<?php echo $this->getConfig('title'); ?>" alt="<?php echo $this->getConfig('alt'); ?>">
	</a>
<?php elseif($this->getConfig('url')): ?>
	<a href="<?php echo $this->getConfig('url'); ?>">
		<img title="<?php echo $this->getConfig('title'); ?>" src="<?php echo BASE_PATH.$this->getConfig('imgPath'); ?>" alt="<?php echo $this->getConfig('alt'); ?>" style="box-sizing:border-box;width:<?php if($this->getConfig('width')){echo $this->getConfig('width') . 'px';}else echo '100%' ?>;<?php if($this->getConfig('height')){echo 'height:' . $this->getConfig('height') . 'px';} ?>;" />
	</a>
<?php else: ?>
		<img title="<?php echo $this->getConfig('title'); ?>" src="<?php echo BASE_PATH . $this->getConfig('imgPath'); ?>" alt="<?php echo $this->getConfig('alt'); ?>" style="box-sizing:border-box;width:<?php if($this->getConfig('width')){echo $this->getConfig('width') . 'px';}else echo '100%' ?>;<?php if($this->getConfig('height')){echo 'height:' . $this->getConfig('height') . 'px';} ?>;" />
<?php endif; ?>
