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
<ul class="tab_bar">
    <?php
        $menu = $this->getConfig('menu');
        if (is_array($this->getConfig('menu'))) {
            $cpt = 0;
            foreach($menu AS $id => $item){
                $cpt++;
                $url = BASE_PATH.$item['url'];
            ?>
                    <li<?php if(isset($_GET[0]) && BASE_PATH.$_GET[0] == $url) echo ' class="selected"'; ?>><a href="<?php echo $url; ?>" style="background-position:center -7px;background-image:url(<?php echo BASE_PATH; ?>lib/glyphish/glyphish-<?php if(isset($_GET[0]) && BASE_PATH.$_GET[0] == $url) echo 'blue'; else echo 'gray'; ?>/<?php echo $item['icon'] ?>.png)"><?php echo $item['title'] ?></a></li>
            <?php
            }
        }
        ?>
</ul>
<style>
    .tab_bar {
        width:100%;
        height: 50px;
        margin: 0;
        background: -webkit-gradient(linear, left top, left bottom, from(black), to(black), color-stop(0.02, #545454), color-stop(0.04, #3B3B3B), color-stop(0.5, #1D1D1D), color-stop(0.51, black));
    }
    .tab_bar ul {
        height:100%
    }
    .tab_bar li {
        width: <?php echo floor(100/count($menu))-2 ?>%;
        text-align: center;
        float: left;
        position: relative;
        height: 46px;
        margin: 1px;
        padding: 0 2px;
        
        box-sizing: border-box;
    }
    .tab_bar li a{
        display: block;
        height: 100%;
        font-size: 14px;
        color: #CCC;
        text-decoration: none;
        background-repeat: no-repeat;
        line-height: 77px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-family: arial;
    }
    .tab_bar li a img{
        display: block;
        margin: 0 auto;
    }
    .tab_bar li.selected ,.tab_bar li:focus,.tab_bar li:hover { background: rgba(255, 255, 255, 0.15);-webkit-border-radius: 10px; border-radius: 4px;}
    .tab_bar li.selected a,.tab_bar li:focus a,.tab_bar li:hover a{ color:#fff;}
</style>