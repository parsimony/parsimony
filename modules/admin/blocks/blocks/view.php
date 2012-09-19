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

app::$request->page->addJSFile(BASE_PATH . 'admin/blocks/blocks/script.js');
$arrayScript = array();
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
			$reflect = new ReflectionClass('\\' . $blockClassName);
                        $blockInfos = \tools::getClassInfos($reflect);
			if (!isset($blockInfos['allowed_types']) || (isset($blockInfos['allowed_types']) && strstr(','.str_replace(' ', '', $blockInfos['allowed_types']).',',','.THEMETYPE.','))) {
			    if (isset($blockInfos['block_category']))
				$categBlock = $blockInfos['block_category'];
			    else
				$categBlock = $moduleobj->getName();
			    if (!isset($blocksCat[$categBlock]))
				$blocksCat[$categBlock] = '';
                            if(isset($blockInfos['description'])) $description = ucfirst(s($blockInfos['description']));
			    $blocksCat[$categBlock] .= '<div class="admin_core_block tooltip" data-tooltip="' . ucfirst(s($blockInfos['title'])).' '.$description. '" draggable="true" id="' . $blockClassName . '" style="float:left;position:relative;background:url(' . BASE_PATH . 'modules/' . $moduleobj->getName() . '/blocks/' . $blockName . '/icon.png) center center;"></div>';
			}
		    }
                    if(is_file('modules/'.$moduleobj->getName() . '/blocks/' . $blockName . '/script.js')){
                        \app::$request->page->addJSFile(BASE_PATH . $moduleobj->getName() . '/blocks/' . $blockName . '/script.js');
                        $arrayScript[] = $blockName;
                    }
		}
	    }
	}
    }
    foreach ($blocksCat as $title => $blocks) {
	echo '<div class="titleTab ellipsis"><span class="sprite sprite-bloc"></span> ' . t(ucfirst($title),FALSE) . '</div>';
	echo '<div id="blocks_' . $title . '" style="padding:0px;">';
	echo $blocks;
	echo '</div>';
    }
    ?>
</ul>
<script>
    $(document).ready(function() {
        var mod = new blockAdminBlocks();
        mod.setBlock(new block());
        <?php
        foreach($arrayScript AS $nameBlock)
            echo 'mod.setBlock(new block_'.$nameBlock.'());'
        ?>
        ParsimonyAdmin.setPlugin(mod);
    });
</script>