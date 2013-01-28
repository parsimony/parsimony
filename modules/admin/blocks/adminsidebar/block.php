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
 * @package admin/blocks
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace admin\blocks;

class adminsidebar extends \core\blocks\container {

    protected $name = 'Admin Sidebar';
    protected $side = 'left';

    public function display() {
	$html = '';
	$style = '';
	if (isset($_COOKIE[$this->side . 'ToolbarCoordX']) && $_COOKIE[$this->side . 'ToolbarCoordX'] != 0)
	    $style .= $this->side . ':' . $_COOKIE[$this->side . 'ToolbarCoordX'] . ';top:' . $_COOKIE[$this->side . 'ToolbarCoordY'] . ';';
	if (isset($_COOKIE[$this->side . 'ToolbarX']))
	    $style .= 'width:' . $_COOKIE[$this->side . 'ToolbarX'] . ';';
	if (isset($_COOKIE['mode']) && $_COOKIE['mode'] == 'edit' && $this->side == 'right')
	    $style .= 'display:none';
	elseif (isset($_COOKIE['mode']) && $_COOKIE['mode'] == 'preview')
	    $style .= 'display:none';
	$classes = '';
	if (isset($_COOKIE[$this->side.'ToolbarOpen']) && $_COOKIE[$this->side.'ToolbarOpen'] == 0)
	    $classes .= ' close';
	$html = '<div id="' . $this->id . '" data-side="'.$this->side.'" class="block sidebar container pin ' . $classes . ' ' . (string) $this->getConfig('cssClasses') . '" style="' . $style . '">';
	$html .= $this->getView();
	$html .= '</div>';
	return $html;
    }

    public function setSide($side) {
	if ($side == 'right' || $side == 'left')
	    $this->side = $side;
    }

}
?>