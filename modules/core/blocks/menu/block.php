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

namespace core\blocks;

/**
 * Menu Block Class 
 * Manages Menu Block
 */

class menu extends \block {

    public function arbo($items) {
	if(!empty($items)){
	    foreach ($items AS &$item) {
		if(isset($_POST['title'][$item['id']])) $item['title'] = $_POST['title'][$item['id']];
		if(isset($_POST['url'][$item['id']])) $item['url'] = $_POST['url'][$item['id']];
		if(isset($_POST['module'][$item['id']])) $item['module'] = $_POST['module'][$item['id']];
		if(isset($_POST['page'][$item['id']])) $item['page'] = $_POST['page'][$item['id']];
		if (isset($item['children']))
		    $item['children'] = $this->arbo($item['children']);
	    }
	}
        return $items;
    }

    public function saveConfigs() {
        $this->setConfig('position', $_POST['position']);
        $menu = $this->arbo(json_decode($_POST['toHierarchy'], true));
        $this->setConfig('menu', json_encode($menu));
    }

    public function init() {
        $menu = array(array('id' => 1, 'title' => 'Home', 'url' => 'index'));
        $this->setConfig('menu', json_encode($menu));
    }

    public function drawAdminMenu($items) {
        foreach ($items AS $item) {
            ?>
            <li id="itemlist_<?php echo $item['id'] ?>">
                <div>
		    <?php if(isset($item['url'])): ?>
			<div class="inline-block" style="width: 46%;box-sizing: border-box;"><input style="width: 100%;box-sizing: border-box;" type="text" class="input_title" name="title[<?php echo $item['id'] ?>]" value="<?php echo $item['title'] ?>" /></div>
			<div class="inline-block" style="width: 46%;box-sizing: border-box;"><input style="width: 100%;box-sizing: border-box;" class="input_url floatright" type="text" name="url[<?php echo $item['id'] ?>]"  value="<?php echo $item['url'] ?>" /></div>
                    <?php else: 
			if(!empty($item['module'])) $title = \app::getModule($item['module'])->getPage($item['page'])->getTitle();
			else $title = '';
			?>
			<div class="inline-block" style="width: 92%;box-sizing: border-box;"><input type="hidden" class="module" name="module[<?php echo $item['id'] ?>]" value="<?php echo $item['module'] ?>" /><input type="hidden" class="page" name="page[<?php echo $item['id'] ?>]" value="<?php echo $item['page'] ?>" /><span class="titlePage"><?php echo 'Module : '.$item['module'].'  - Title : '.$title ?></span></div>
		    <?php endif; ?>
		    <div class="inline-block none"><input type="checkbox" class="input_active" /></div>
                    <div class="inline-block floatright" style="width: 4%;box-sizing: border-box;"><a href="#" onclick="$(this).closest('li').remove();refreshPos();"><span class="ui-icon ui-icon-closethick"></span></a></div>
                </div><?php
            if (isset($item['children'])) {
                echo '<ol>';
                $this->drawAdminMenu($item['children']) . '';
                echo '</ol>';
            }
            ?>
            </li>
            <?php
        }
    }

    public function drawmenu($items) {
        $cpt = 1;
        $count = count($items);
        foreach ($items AS $item) {
            $classes = array();
            $class = '';
	     if(isset($item['url'])){
		 $url = $item['url'];
		 $title = $item['title'];
	     } else{
		 $page = \app::getModule($item['module'])->getPage($item['page']);
		 $url = substr($page->getRegex(), 1, -1);
		 $title = $page->getTitle();
	     }
            if(isset($_GET[0]) && BASE_PATH.$_GET[0] == $url) $classes[] = 'current';
            if($count == $cpt) $classes[] = 'last';
            if($cpt==1) $classes[] = 'first';
            if(count($classes) > 0) $class = 'class="'.implode(' ',$classes).'"';
            ?>
            <li id="itemlist_<?php echo $item['id'] ?>" <?php echo $class; ?>>
		    <a href="<?php echo $url ?>"><?php echo $title ?></a>
                <?php
                if (isset($item['children'])) {
                    echo '<ul>';
                    $this->drawmenu($item['children']) . '';
                    echo '</ul>';
                }
                ?>
            </li>
            <?php
            $cpt++;
        }
    }

}
?>
