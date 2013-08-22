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
app::$request->page->addJSFile('admin/blocks/modules/block.js', 'footer');

$activeModule = \app::$config['modules']['active'];

/* Put active module at the top of the list, and remove admin && core modules */
unset($activeModule[MODULE]);
$activeModule = array_merge(array(MODULE => '5555'), $activeModule);
unset($activeModule['admin']);
unset($activeModule['core']);

foreach ($activeModule as $module => $type) {
	$moduleobj = \app::getModule($module);
	$moduleInfos = \tools::getClassInfos($moduleobj);
	if (!isset($moduleInfos['displayAdmin']) || $moduleInfos['displayAdmin'] == 4) {
		$icon = '';
		if (is_file('modules/' . $module . '/icon.png'))
			$icon = 'background:url(' . BASE_PATH . $module . '/icon.png)';
		$adminHTML = $moduleobj->displayAdmin();
		if ($adminHTML == FALSE)
			$htmlConfig = '';
		else
			$htmlConfig = '<a href="#modules/settings/' . $module . '" class="action floatright" style="display:block;margin:3px; line-height:0;" title="' . t('Administration Module', FALSE) . ' ' . ucfirst(s($moduleInfos['title'])) . '"><img src="' . BASE_PATH . 'admin/img/config.png"/></a>';
			echo '<div class="titleTab ellipsis"><span style="margin: 5px 7px 0px 7px;' . $icon . '" class="sprite sprite-module floatleft"></span> ' . ucfirst(s($moduleInfos['title'])) . $htmlConfig . '</div>';
		$display = '';
		if ($module != MODULE)
			$display = 'none';
		?>
		<div class="moduleParts <?php echo $display; ?>" data-module="<?php echo $module; ?>">
			<div class="datatopages subTabsContainer">
				<div rel="pages" class="ssTab ellipsis switchtodata active" title="<?php echo t('Pages in', FALSE) . ' ' . ucfirst($module); ?>"><?php echo t('Pages', FALSE); ?>                  </div>
				<div rel="models" class="ssTab db ellipsis switchtopages" title="<?php echo t('Content', FALSE) . ' ' . ucfirst($module); ?>"><?php echo ' ' . t('Content', FALSE); ?></div> 
			</div>
			<ul class="none models tabPanel">
				<?php
				$models = $moduleobj->getModel();
				if (count($models) > 0) {
					foreach ($moduleobj->getModel() as $entity) {
						$entityName = $entity->getName();
						$entityTitle = s(ucfirst($entity->getTitle()));
						if ($module != 'core' || !empty($entityTitle)) {
							?>
							<a href="#modules/model/<?php echo $module; ?>/<?php echo $entityName; ?>" class="sublist modelSubList" title="<?php echo $entityTitle; ?>"><?php echo $entityTitle; ?></a>
							<?php
						}
					}
				}
				if ($_SESSION['behavior'] === 2): ?>
					<li class="sublist gotoDBDesigner" title="<?php echo t('Database Designer', FALSE) . ' ' . ucfirst($module); ?>">
						<?php echo t('Database Designer', FALSE) ?>
						<form method="POST" class="none" action="<?php echo BASE_PATH; ?>admin/dbDesigner" target="_blank">
							<input type="hidden" name="module" value="<?php echo $module; ?>">
						</form>
					</li>
				<?php endif; ?>
			</ul>
			<ul class="pages tabPanel" style="display:block;">
				<?php
				foreach ($moduleobj->getPages() as $id_page => $page) {
					if ($module === \app::$config['modules']['default'])
						$pageURL = BASE_PATH . $page->getURL();
					else
						$pageURL = BASE_PATH . $module . '/' . $page->getURL();
					?>
					<li class="sublist ellipsis gotopage" draggable="true" id="page_<?php echo $id_page ?>" data-title="<?php echo s($page->getTitle()); ?>" data-url="<?php echo $pageURL ?>">
						<?php echo ucfirst(s($page->getTitle())); ?>
						<a href="#modules/page/<?php echo $module; ?>/<?php echo $id_page; ?>" class="ui-icon ui-icon-pencil" title="<?php echo t('Manage this page', FALSE); ?>"></a>
					</li>
					<?php
				}
				?>
				<a href="#modules/page/<?php echo $module ?>/new" class="sublist ellipsis" title="<?php echo t('Add A Page in', FALSE) . ' ' . ucfirst($module); ?>">
					<span class="ui-icon ui-icon-plus" style="position: relative;top: 2px;float: left;"></span>
					<?php echo t('Add A Page', FALSE); ?>
				</a>
			</ul>
		</div>
		<?php
	}
}
if ($_SESSION['behavior'] === 2): ?>		
	<div class="titleTab ellipsis" style="padding-left: 31px;"><span class="ui-icon ui-icon-plus" style="top: 5px;  left: 6px;  position: absolute;"></span><a href="#modules/add" style="color: #444;text-decoration: none" title="<?php echo t('Add a Module', FALSE); ?>" id="add-module"><?php echo t('Add a Module', FALSE); ?></a></div>
<?php endif; ?>
