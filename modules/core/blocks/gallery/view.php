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
<ul class="slides">
<?php
$pagination = '';
$imgs = $this->getConfig('img');
if(!empty($imgs)) {
	foreach ($imgs as $id => $image) {
		?>
		<li class="slide"> 
			<a href="<?php echo $image['url']; ?>" title="<?php echo $image['title']; ?>" target="_blank">
				<img title="<?php echo $image['title']; ?>" style="width:<?php echo $this->getConfig('width'); ?>px;height:<?php echo $this->getConfig('height'); ?>px;" src="<?php echo BASE_PATH ?><?php echo MODULE . '/files/' . $id ?>">
			</a>
			<div class="caption"><?php echo $image['title']; ?></div>
		</li>
		<?php
		$pagination .= '<li data-slide="' . $id . '"></li>';
	}
}
?>
</ul>
<a href="#" class="slideNav prev">&lt;</a>
<a href="#" class="slideNav next">&gt;</a>
<ul class="pagination"><?php echo $pagination; ?></ul>
