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
 * @package admin
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>

<ul class="blocks">
    <?php
    $activeModule = \app::$activeModules;
    unset($activeModule[MODULE]);
    $activeModule = array_merge(array(MODULE => '1'),\app::$activeModules);
    $blocksCat = array();

    foreach ($activeModule as $module => $type) {
	$moduleobj = \app::getModule($module);
	if (file_exists('modules/' . $moduleobj->getName() . '/blocks')) {
	    if ($moduleobj->getName() != 'admin') {
		$blocklist = glob('modules/' . $moduleobj->getName() . '/blocks/*/block.php');
		foreach ($blocklist as $path) {
		    $blockName = substr(strrchr(substr($path, 0, -10), '/block.php'), 1);
		    if ($blockName !== 'error404' && $blockName !== 'page') {
			$blockClassName = $moduleobj->getName() . '\blocks\\' . $blockName;
			$obj = new ReflectionClass('\\' . $blockClassName);
			$props = $obj->getDefaultProperties();
			if (!isset($props['allowedTypes']) || (isset($props['allowedTypes']) && in_array(THEMETYPE, $props['allowedTypes']))) {
			    if (isset($props['category']))
				$categBlock = $props['category'];
			    else
				$categBlock = $moduleobj->getName();
			    if (!isset($blocksCat[$categBlock]))
				$blocksCat[$categBlock] = '';
			    $blocksCat[$categBlock] .= '<div class="admin_core_block tooltip" data-tooltip="' . ucfirst($blockName) . '" draggable="true" id="' . $blockClassName . '" style="float:left;position:relative;background:url(' . BASE_PATH . 'modules/' . $moduleobj->getName() . '/blocks/' . $blockName . '/img.gif) center center;"></div>';
			}
		    }
		}
	    }
	}
    }
    foreach ($blocksCat as $title => $blocks) {
	echo '<div class="titleTab ellipsis" style="background: url('.BASE_PATH.'admin/img/bloc.png) no-repeat 7px #777;padding-left: 35px;"></span> ' . t(ucfirst($title),FALSE) . '</div>';
	echo '<div id="blocks_' . $title . '" style="padding:0px;">';
	echo $blocks;
	echo '</div>';
    }
    ?>
</ul>