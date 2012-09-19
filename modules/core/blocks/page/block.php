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
 * @title Page
 * @description  
 * @version 1
 * @browsers all
 * @php_version_min 5.3
 * @modules_dependencies core:1
 */

class page extends \block {
    
    public function getAdminView(){
	return t('No config for this block').'<script type="text/javascript">$(document).ready(function() {window.setTimeout(\'$(".adminzonetab a[href=#accordionBlockConfigGeneral]").trigger("click");\', 500);});</script>';
    }

    public function display() {
	$rep_cache = PROFILE_PATH . $this->module . '/blocks/' . $this->blockName . '/';
	$fichier_cache = 'cache/' . $rep_cache . THEME . '_' . MODULE . '_' . \app::$request->page->getId() . '_' . $this->id . '.cache';
	$secondes = $this->getConfig('maxAge');
	$html = '';
	if (file_exists($fichier_cache) && filemtime($fichier_cache) + $secondes > time() && $secondes != 0) {
	    ob_start();
	    require($fichier_cache);
	    $html .= ob_get_clean();
	} else {
	    if ($this->getConfig('tag') !== false)
		$balise = $this->getConfig('tag');
	    else
		$balise = 'div';
	    $html = '<' . $balise . ' id="' . $this->id . '" data-page="' . \app::$request->page->getId() . '" class="block container container_page ' . (string) $this->getConfig('cssClasses') . '">';
	    $html .= $this->getView();
	    $html .= '<div class="clearboth"></div></' . $balise . ' >';
	    if ($secondes != 0)
		tools::file_put_contents($fichier_cache, $html);
	}
	return $html;
    }

}

?>