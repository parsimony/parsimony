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
?>
<div class="viewtitle">
    <h1><?php echo $this->getConfig('text') ?></h1>
    <?php if ($this->getConfig('lefturl') || $this->getConfig('leftbutton')): ?>
        <span class="button floatleft" style="top: 5px;left: 5px;"><a href="<?php echo $this->getConfig('lefturl'); ?>"><?php echo $this->getConfig('leftbutton'); ?></a></span>
    <?php endif; ?>
    <?php if ($this->getConfig('righturl') || $this->getConfig('rightbutton')): ?>
        <span class="button right large" style="top: 5px;right: 5px;"><a href="<?php echo $this->getConfig('righturl'); ?>"><?php echo $this->getConfig('rightbutton'); ?></a></span>
    <?php endif; ?>
</div>
<style>
    .viewtitle a {margin: 0 ;font-size: 15px;color: white;text-decoration: none;}
    .viewtitle {font-family: 'Helvetica Neue', Helvetica, sans-serif;
		position: relative;
		height: 42px;
		border: 1px solid #CAD6E2;
		border-bottom-color: #22374A;
		border-right: 0;
		border-left: 0;
		border-image: initial;
		text-align: center;
                background: <?php echo $this->getConfig('color') ?>;
                background-image: -webkit-gradient(linear, 0 0, 0 100%, color-stop(.5, rgba(255, 255, 255, .1)), color-stop(.5, transparent), to(transparent));
                background-image: -moz-linear-gradient(rgba(255, 255, 255, .1) 50%, transparent 50%, transparent);
                background-image: linear-gradient(rgba(255, 255, 255, .1) 50%, transparent 50%, transparent);
    }
    .viewtitle h1 {
        font-size: 20px;
        color: white;
        text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.4);
        margin: 8px 0 0 0;
        display: block;
        font-size: 20px;
        font-weight: bold;
    }
    .viewtitle .left {
        top: 6px;
        left: 10px;
    }
    .viewtitle .right {
        top: 6px;
        right: 10px;
    }

    .viewtitle .button {
        font-size: 14px;
        font-weight: bold;
        color: white;
        text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.4);
        position: absolute;
        padding: 7px 12px 5px 12px;
        line-height: 20px;
        border: 1px solid rgba(0, 0, 0, 0.4);
        -webkit-box-shadow: 0 1px 0 rgba(255,255,255, 0.35), inset 0 1px 1px rgba(0,0,0, 0.25);
        -webkit-border-radius: 5px;
        background: <?php echo $this->getConfig('color') ?>;
                background-image: -webkit-gradient(linear, 0 0, 0 100%, color-stop(.5, rgba(255, 255, 255, .1)), color-stop(.5, transparent), to(transparent));
                background-image: -moz-linear-gradient(rgba(255, 255, 255, .1) 50%, transparent 50%, transparent);
                background-image: linear-gradient(rgba(255, 255, 255, .1) 50%, transparent 50%, transparent);
        border-image: initial;
    }
</style>