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
 * @copyright Julien Gras et Benoît Lorillot
 * 
 * @category Parsimony
 * @package admin
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

app::$response->addJSFile('admin/blocks/tree/block.js','footer');
?>
<div id="config_tree_selector" class="none">
	<span class="spanDND sprite sprite-csspickerlittle cssblock floatleft" data-action="onDesign"></span>
	<?php if ($_SESSION['permissions'] & 128) : ?>
		<span class="floatleft ui-icon ui-icon-wrench configure_block" rel="getViewConfigBlock" data-action="onConfigure" title="<?php echo t('Configuration'); ?>"></span>
		<?php if ($_SESSION['permissions'] & 256) : ?>
		<span draggable="true" class="floatleft move_block ui-icon ui-icon-arrow-4"></span>
		<span class="ui-icon ui-icon-trash config_destroy floatleft" data-action="onDelete"></span>
		<?php endif;
	endif; ?>
</div>
<div id="tree"> 
	<?php
	$IDPage = \app::$request->getParam('IDPage');
	if ($IDPage && is_numeric($IDPage)) {
		echo \app::getModule('admin')->structureTree(\theme::get(\app::$request->getParam('THEMEMODULE'), \app::$request->getParam('THEME')));
	}
	?>
</div>