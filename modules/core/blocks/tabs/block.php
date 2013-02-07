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
 * @title Container tabs
 * @description can contains blocks and organize them into tabs, is used to structure the page
 * @version 1
 * @browsers all
 * @php_version_min 5.3
 * @block_category containers
 * @modules_dependencies core:1
 */

class tabs extends \core\blocks\container{

    public function getView(){
	$html = '';
        if (!empty($this->blocks)) {
	    \app::$request->page->head .= '<style> #' . $this->getId() . ' > .block{display:none;}#' . current($this->blocks)->getId() . '.block{display:block;} </style><script>
		$(document).ready(function() {$("#'.$this->getId().'").on("click","a",function (e) {
		    e.preventDefault();
		    $("#'.$this->getId().' > .block").hide();
		    $($(this).attr("href")).show();
		});});
		</script>';
	    $html .= '<ul>';
	    foreach ($this->blocks as $selected_block) {
		$title = $selected_block->getConfig('headerTitle');
		$html .= '<li><a href="#'.$selected_block->getId().'">'.(empty($title) ? $selected_block->getId() : $title).'</a></li>';
	    }
	    $html .= '</ul>';
	}
	return $html;
    }
    
    public function getAdminView(){
	return '';
    }

}

?>