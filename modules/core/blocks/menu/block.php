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
        foreach ($items AS &$item) {
            $item['title'] = $_POST['title'][$item['id']];
            $item['url'] = $_POST['url'][$item['id']];
            if (isset($item['children']))
                $item['children'] = $this->arbo($item['children']);
        }
        return $items;
    }

    public function saveConfigs() {
        $this->setConfig('position', $_POST['position']);
        $menu = $this->arbo(json_decode($_POST['toHierarchy'], true));
        $this->setConfig('menu', json_encode($menu));
    }

    public function init() {
        $menu = array(array('id' => 1, 'title' => 'Home', 'url' => 'index.html'));
        $this->setConfig('menu', json_encode($menu));
    }

    public function drawadminmenu($items) {
        foreach ($items AS $item) {
            ?>
            <li id="itemlist_<?php echo $item['id'] ?>">
                <div>
                    <div class="inline-block" style="width: 46%;box-sizing: border-box;"><input style="width: 100%;box-sizing: border-box;" type="text" class="input_title" name="title[<?php echo $item['id'] ?>]" value="<?php echo $item['title'] ?>" /></div>
                    <div class="inline-block" style="width: 46%;box-sizing: border-box;"><input style="width: 100%;box-sizing: border-box;" class="input_url floatright" type="text" name="url[<?php echo $item['id'] ?>]"  value="<?php echo $item['url'] ?>" /></div>
                    <div class="inline-block none"><input type="checkbox" class="input_active" /></div>
                    <div class="inline-block floatright" style="width: 4%;box-sizing: border-box;"><a href="#" onclick="$(this).closest('li').remove();refreshPos();"><span class="ui-icon ui-icon-closethick"></span></a></div>
                </div><?php
            if (isset($item['children'])) {
                echo '<ol>';
                $this->drawadminmenu($item['children']) . '';
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
            if(isset($_GET[0]) && BASE_PATH.$_GET[0] == $item['url']) $classes[] = 'current';
            if($count == $cpt) $classes[] = 'last';
            if($cpt==1) $classes[] = 'first';
            if(count($classes) > 0) $class = 'class="'.implode(' ',$classes).'"';
            ?>
            <li id="itemlist_<?php echo $item['id'] ?>" <?php echo $class; ?>>
                <a href="<?php echo $item['url'] ?>"><?php echo $item['title'] ?></a>
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
