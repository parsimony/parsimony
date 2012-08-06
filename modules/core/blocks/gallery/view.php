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
\app::$request->page->addJSFile(BASE_PATH . 'modules/core/blocks/gallery/plugins/slides/slides.jquery.js');
?>
<script>
    $(function(){
        $('#<?php echo $this->getId(); ?> .slider').slides({
            preload: true,
            preloadImage: '<?php echo BASE_PATH; ?>core/blocks/gallery/plugins/slides/img/loading.gif',
            play: 5000,
            pause: 2500,
            paginationClass: 'paginationSlides',
            hoverPause: true,
            animationStart: function(current){
                $('.caption').animate({
                    bottom:-35
                },100);
            },
            animationComplete: function(current){
                $('.caption').animate({
                    bottom:0
                },200);
            },
            slidesLoaded: function() {
                $('.caption').animate({
                    bottom:0
                },200);
            }
        });
    });
</script>
<style>
    #<?php echo $this->getId(); ?> .slider,#<?php echo $this->getId(); ?> .slide{width:<?php echo $this->getConfig('width'); ?>px;}
</style>
<div class="slider">
    <div class="slides_container" style="width:<?php echo $this->getConfig('width'); ?>px;height:<?php echo $this->getConfig('height'); ?>px;">
	<?php
	$imgs = $this->getConfig('img');
	if(!empty($imgs)) {
	    foreach ($imgs as $id => $image) {
		?>
		<div class="slide"> 
		    <a href="<?php echo $image['url']; ?>" title="<?php echo $image['title']; ?>" target="_blank">
			<img title="<?php echo $image['title']; ?>" style="width:<?php echo $this->getConfig('width'); ?>px;height:<?php echo $this->getConfig('height'); ?>px;" src="<?php echo BASE_PATH ?><?php echo $this->module . '/files/' . $id ?>">
		    </a>
		    <div class="caption">
			<p><?php echo $image['title']; ?></p>
		    </div>
		</div>
		<?php
	    }
	}
	?>
    </div>
    <a href="#" class="slideNav prev">
	<img src="<?php echo BASE_PATH ?>core/blocks/gallery/plugins/slides/arrow-prev.png">
    </a>
    <a href="#" class="slideNav next">
	<img src="<?php echo BASE_PATH ?>core/blocks/gallery/plugins/slides/arrow-next.png">
    </a>
</div>

