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
\app::$request->page->addCSSFile(BASE_PATH . 'modules/core/blocks/gallery/plugins/slides/css.css');
?>
<script>
    $(function(){
        $('#<?php echo $this->getId(); ?>_slider').slides({
            preload: true,
            preloadImage: '/core/blocks/gallery/plugins/slides/img/loading.gif',
            play: 5000,
            pause: 2500,
            paginationClass: 'paginationSlides',
            hoverPause: true,
            animationStart: function(current){
                $('.caption').animate({
                    bottom:-35
                },100);
                if (window.console && console.log) {
                    // example return of current slide number
                    console.log('animationStart on slide: ', current);
                };
            },
            animationComplete: function(current){
                $('.caption').animate({
                    bottom:0
                },200);
                if (window.console && console.log) {
                    // example return of current slide number
                    console.log('animationComplete on slide: ', current);
                };
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
    .template{float: left}
    .paginationSlides {margin: 3px auto 0;width: 65px;}
    .slides_container div.slide {width:<?php echo $this->getConfig('width'); ?>px;height:<?php echo $this->getConfig('height'); ?>px;display:block;}
    .slides_container {width:<?php echo $this->getConfig('width'); ?>px;}
    .slides .next {left:<?php echo $this->getConfig('width'); ?>px;}
    .paginationSlides {width: <?php echo $this->getConfig('width'); ?>px;}
    .paginationSlides li a {background-image:url(<?php echo BASE_PATH; ?>core/blocks/gallery/plugins/slides/pagination.png);}
</style>

<div id="<?php echo $this->getId(); ?>_slider" class="slides">
    <div class="slides_container">
	<?php
	$imgs = $this->getConfig('img');
	if(!empty($imgs)) {
	    foreach ($imgs as $id => $image) {
		?>
		<div class="slide"> 
		    <a href="<?php echo $image['url']; ?>" title="<?php echo $image['title']; ?>" target="_blank"><img class="imgpanel" title=""  src="<?php echo BASE_PATH ?>thumbnail?x=<?php echo $this->getConfig('width'); ?>&y=<?php echo $this->getConfig('height'); ?>&crop=1&path=<?php echo PROFILE_PATH.$this->module . '/files/' . $id ?>" alt=""></a>
		    <div class="caption">
			<p><?php echo $image['title']; ?></p>
		    </div>
		</div>
		<?php
	    }
	}
	?>
    </div>
    <a href="#" class="prev"><img src="<?php echo BASE_PATH ?>core/blocks/gallery/plugins/slides/arrow-prev.png" width="24" height="43" alt="Arrow Prev"></a>
    <a href="#" class="next"><img src="<?php echo BASE_PATH ?>core/blocks/gallery/plugins/slides/arrow-next.png" width="24" height="43" alt="Arrow Next"></a>
</div>

