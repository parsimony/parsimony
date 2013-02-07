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

namespace core\blocks;

/**
 * @title Container
 * @description can contains other blocks and is used to structure the page
 * @version 1
 * @browsers all
 * @php_version_min 5.3
 * @block_category containers
 * @modules_dependencies core:1
 */

class container extends \block{

    public function display(){
        $cacheDir = PROFILE_PATH . $this->module . '/blocks/' . $this->blockName . '/';
        $cacheFile = 'cache/' . $cacheDir . THEME . '_' . MODULE . '_' . \app::$request->page->getId() . '_' . $this->id . '.cache';
        $maxage = $this->getConfig('maxAge');
        $html = $classes = '';
        if ($maxage > 0 && is_file($cacheFile) && filemtime($cacheFile) + $maxage > time()) {
            ob_start();
            include($cacheFile);
            $html .= ob_get_clean();
        } else {
	    $view = $this->getView(); // for children classes
            if ($this->getConfig('tag') !== false)
                $tag = $this->getConfig('tag');
            else
                $tag = 'div';
            if ($this->getConfig('cssClasses') != false )
                $classes = ' ' . $this->getConfig('cssClasses');
            if ($this->getConfig('column')) {
                \app::$request->page->head .= '<style> #' . $this->getId() . ' > .block{float:left} </style>';
                $classes .= ' column';
            }
            $html .= '<' . $tag . ' id="' . $this->id . '" class="block container' . $classes . '">';
	    $html .= $view;
            if (!empty($this->blocks)) {
                foreach ($this->blocks as $selected_block) {
                    $html .= $selected_block->display() . PHP_EOL;
                }
            }
            $html .= '</' . $tag . ' >';
            if ($maxage > 0)
                \tools::file_put_contents($cacheFile, $html);
        }
        return $html;
    }
    
    public function getView(){
        return '';
    }

    public function setBlocks($blocks){
        $this->blocks = $blocks;
    }

    public function ajaxRefresh($type = FALSE){
        if ($type == 'add') {
            return parent::ajaxRefresh($type);
        } else {
            return 'document.getElementById("parsiframe").contentWindow.location.reload()';
        }
    }

}

?>