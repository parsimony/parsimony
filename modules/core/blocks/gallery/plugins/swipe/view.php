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
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
\app::$request->page->addJSFile(BASE_PATH . 'modules/core/blocks/gallery/plugins/swipe/swipe.min.js');
\app::$request->page->addCSSFile(BASE_PATH . 'modules/core/blocks/gallery/plugins/swipe/style.css');
?>

<div id="slider">
    <ul>
	<?php
	$imgs = $this->getConfig('img');
	if (!empty($imgs)) {
	    foreach ($imgs as $id => $image) {
		?>
		<li> 
		    <a href="<?php echo $image['url']; ?>" title="<?php echo $image['title']; ?>" target="_blank"><img class="imgpanel" title=""  src="/<?php BASE_PATH ?>thumbnail?x=<?php echo $this->getConfig('width'); ?>&y=<?php echo $this->getConfig('height'); ?>&crop=1&path=<?php echo PROFILE_PATH.$this->module . '/files/' . $id ?>" alt=""></a>
		    </lu>
		    <?php
		}
	    }
	    ?>
    </ul>
</div>
<script>
    $(function(){
        window.mySwipe = new Swipe(document.getElementById('slider'));
    });
</script>