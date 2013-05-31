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
 * @category  Blog
 * @package blog/blocks
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace blog\blocks;

/**
 * @title Categories
 * @description displays categories of your articles
 * @version 1
 * @browsers all
 * @php_version_min 5.3
 * @block_category blog
 * @modules_dependencies core:1
 */

class categories extends \block {

    public function saveConfigs() {
	$this->setConfig('display', $_POST['display']);
	$this->setConfig('URLpattern', $_POST['URLpattern']);
	if (isset($_POST['exclude']) && is_array($_POST['exclude']))
	    $this->setConfig('exclude', $_POST['exclude']);
	else
	    $this->setConfig('exclude', '');
    }

    protected function getCategories($admin = FALSE) {
	$display = $this->getConfig('display');
	$exclude = $this->getConfig('exclude');
	$obj = \app::getModule('blog')->getEntity('category');
	$finalTree = array();
	$list = array();
	$subtree = '';
	foreach ($obj as $key => $line) {
	    $thisref = &$finalTree[$line->id_category];
	    $thisref['parent_id'] = $line->id_parent;
	    $thisref['name'] = $line->name;
            $thisref['url'] = $line->url;
	    if (!is_array($exclude) || (is_array($exclude) && !in_array($line->id_category, $exclude)) || $admin) {
		if ($line->id_category == 0) {
		    $list[$line->id_category] = &$thisref;
		} else {
		    $finalTree[$line->id_parent]['children'][$line->id_category] = &$thisref;
		}
	    }
	    if ($display != 'no' && $line->id_category == $display && !$admin) {
		$subtree = array($line->id_category => &$thisref);
	    }
	}
	if (!empty($subtree))
	    return $subtree;
	elseif(isset($finalTree[0]['children']))
	    return $finalTree[0]['children'];
        return '';
    }

    protected function drawTree($arr) {
	$html = '';
	if (is_array($arr)) {
	    $html .= '<ul>';
	    foreach ($arr as $child) {
		$url = str_replace('%url%',$child['url'],$this->getConfig('URLpattern'));
		$classe = '';
		if(isset($_GET[0]) && $_GET[0] == $url) $classe = ' class="current"';
		$html .= '<li><a href="'.BASE_PATH. $url . '"'.$classe.'>' . $child['name'] . '</a>';
		if (isset($child['children'])) {
		    $html .= $this->drawTree($child['children']);
		}
		$html .= '</li>';
	    }
	    $html .= '</ul>';
	}
	return $html;
    }

    protected function drawTreeAdmin($arr, $type = FALSE, $n = 0) {
	$html = '';
	if (is_array($arr)) {
	    foreach ($arr as $id => $child) {
		if ($type == 'display' && $this->getConfig('display') == $id)
		    $se = ' selected="selected"';
		elseif ($type == 'exclude' && is_array($this->getConfig('exclude')) && in_array($id, $this->getConfig('exclude')))
		    $se = ' selected="selected"';
		else
		    $se = '';
		$html .= '<option value="' . $id . '"' . $se . '>' . str_repeat('---', $n) . $child['name'] . '</option>';
		if (isset($child['children'])) {
		    $html .= $this->drawTreeAdmin($child['children'], $type, $n + 1);
		}
	    }
	}
	return $html;
    }

}

?>
