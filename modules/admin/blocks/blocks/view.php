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
 * @package admin
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
app::$request->page->addJSFile('admin/blocks/blocks/block.js', 'footer');
?>
<ul class="blocks">
	<?php
	$activeModule = \app::$config['modules']['active'];

	/* Put active module at the top of the list, and remove admin */
	unset($activeModule[MODULE]);
	$activeModule = array_merge(array(MODULE => '1'), $activeModule);
	unset($activeModule['admin']);

	$blocksCat = array();
	$stylableElements = array();
	foreach ($activeModule as $module => $type) {
		$blocklist = glob('modules/' . $module . '/blocks/*/block.php');
		foreach ((is_array($blocklist) ? $blocklist : array()) as $path) {
			$blockName = substr(strrchr(substr($path, 0, -10), '/block.php'), 1);
			if ($blockName !== 'page') {
				$blockClassName = $module . '\blocks\\' . $blockName;
				$reflect = new ReflectionClass('\\' . $blockClassName);
				$blockInfos = \tools::getClassInfos($reflect);
				if (!isset($blockInfos['allowed_types']) || (isset($blockInfos['allowed_types']) && strstr(',' . str_replace(' ', '', $blockInfos['allowed_types']) . ',', ',' . THEMETYPE . ','))) {
					if (isset($blockInfos['block_category']))
						$categBlock = $blockInfos['block_category'];
					else
						$categBlock = $module;
					if (!isset($blocksCat[$categBlock]))
						$blocksCat[$categBlock] = '';
					if (isset($blockInfos['description']))
						$description = ucfirst(s($blockInfos['description']));
					$blocksCat[$categBlock] .= '<div class="admin_core_block tooltip" data-title="' . trim(ucfirst(s($blockInfos['title']))) . '" data-tooltip="' . $description . '" draggable="true" id="' . str_replace('\\', '', $blockClassName) . '" data-block="' . $blockClassName . '" style="background:url(' . BASE_PATH . 'modules/' . $module . '/blocks/' . $blockName . '/icon.png) center center no-repeat;"></div>';
				}
			}
			/* List default stylables selecteurs */
			if (is_file('modules/' . $module . '/blocks/' . $blockName . '/default.css')) {
				$css = new css('modules/' . $module . '/blocks/' . $blockName . '/default.css');
				$CSSValues = $css->getCSSValues();
				if(!isset($stylableElements[$module.'_'.$blockName])) $stylableElements[$module.'_'.$blockName] = array();
				foreach ($CSSValues as $selecteur => $components) {
					preg_match('@\{(.*)\}@', $components['b'], $res);
					if (!empty($res)) {
						$stylableElements[$module.'_'.$blockName][$res[1]] = $selecteur;
					}
				}
				
			}
		}
	}
	foreach ($blocksCat as $title => $blocks) {
		echo '<div class="titleTab ellipsis">' . t(ucfirst($title), FALSE) . '</div>';
		echo '<div id="blocks_' . $title . '">' . $blocks . '</div>';
	}
		?>
</ul>
<script>
ParsimonyAdmin.stylableElements = <?php echo json_encode($stylableElements); ?>;
</script>