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
<?php if($this->getConfig('fancybox') == '1' || $this->getConfig('url')): 
	\app::$request->page->addJSFile('lib/fancybox/jquery.fancybox-1.3.4.pack.js');
	\app::$request->page->addJSFile('lib/fancybox/fancybox_setting.js');
	\app::$request->page->addCSSFile('lib/fancybox/jquery.fancybox-1.3.4.css');
	?>
	<a <?php if($this->getConfig('fancybox') == "1") echo 'class="fancybox"' ?> href="<?php if($this->getConfig('url')){ echo $this->getConfig('url');} else echo PROFILE_PATH.$this->getConfig('imgPath'); ?>">
		<img title="<?php echo $this->getConfig('title'); ?>" src="<?php echo BASE_PATH.$this->getConfig('imgPath'); ?>" alt="<?php echo $this->getConfig('alt'); ?>" style="box-sizing:border-box;width:<?php if($this->getConfig('width')){echo $this->getConfig('width') . 'px';}else echo '100%' ?>;<?php if($this->getConfig('height')){echo 'height:' . $this->getConfig('height') . 'px';} ?>;" />
	</a>
<?php else: ?>
		<img title="<?php echo $this->getConfig('title'); ?>" src="<?php echo BASE_PATH.$this->getConfig('imgPath'); ?>" alt="<?php echo $this->getConfig('alt'); ?>" style="box-sizing:border-box;width:<?php if($this->getConfig('width')){echo $this->getConfig('width') . 'px';}else echo '100%' ?>;<?php if($this->getConfig('height')){echo 'height:' . $this->getConfig('height') . 'px';} ?>;" />
<?php endif; ?>
