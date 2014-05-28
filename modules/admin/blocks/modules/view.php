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
app::$response->addJSFile('admin/blocks/modules/block.js', 'footer');

$activeModule = \app::$activeModules;

/* Put active module at the top of the list, and remove admin && core modules */
unset($activeModule[MODULE]);
$activeModule = array_merge(array(MODULE => '5555'), $activeModule);
unset($activeModule['admin']);
unset($activeModule['core']);
$activeModule['core'] = 0; /* add core(administration tab) module at the end */

foreach ($activeModule as $module => $type) {
	$moduleobj = \app::getModule($module);
	$moduleInfos = \tools::getClassInfos($moduleobj);
	if (!isset($moduleInfos['displayAdmin'])) {
		$moduleInfos['displayAdmin'] = 3;
	}
	$adminHTML = $moduleobj->displayAdmin();
	if ($adminHTML === FALSE)
		$htmlConfig = '';
	else
		$htmlConfig = '<a href="#left_sidebar/settings/' . $module . '" class="action configmodule" title="' . t('Administration Module') . ' ' . ucfirst(s($moduleInfos['title'])) . '"></a>';
	if ($moduleInfos['displayAdmin'] > 0) :
		?>
		<div class="moduleTab <?php echo $module === MODULE ? 'active' : ''; ?>" data-module="<?php echo $module; ?>">
			<div class="titleTab ellipsis"><?php echo ucfirst(s($moduleInfos['title'])) . $htmlConfig; ?></div>
			<div class="moduleParts">
				<?php
				if ($moduleInfos['displayAdmin'] & 4) :
					$adminLinks = $moduleobj->getAminMenu();
					if (!empty($adminLinks)) {
						echo '<ul class="menuAdmin">';
						foreach ($adminLinks as $href => $title) { 
							echo '<li><a href="' . $href . '">' . $title . '</a></li>';
						}
						echo '</ul>';
					}
				endif;
				if ($moduleInfos['displayAdmin']  & 1) :
				?>
				<div class="ellipsis subtitle" title="<?php echo t('Pages in') . ' ' . ucfirst($module); ?>"><?php echo t('Pages'); ?></div>
				<ul class="pages">
					<?php
					foreach ($moduleobj->getPages() as $id_page => $page) {
						if ($module === \app::$config['defaultModule'])
							$pageURL = BASE_PATH . $page->getURL();
						else
							$pageURL = BASE_PATH . $module . '/' . $page->getURL();
						?>
						<li class="sublist ellipsis gotopage" draggable="true" id="page_<?php echo $id_page ?>" data-title="<?php echo s($page->getTitle()); ?>" data-url="<?php echo $pageURL ?>">
							<?php echo ucfirst(s($page->getTitle())); 
							if ($_SESSION['permissions'] & 4): ?>
							<a href="#left_sidebar/page/<?php echo $module; ?>/<?php echo $id_page; ?>" class="ui-icon ui-icon-pencil" title="<?php echo t('Manage this page'); ?>"></a>
							<?php endif; ?>
						</li>
						<?php
					}
					if ($_SESSION['permissions'] & 8):
					?>
					<a href="#left_sidebar/page/<?php echo $module ?>/new" class="sublist ellipsis" title="<?php echo t('Add A Page in') . ' ' . ucfirst($module); ?>">
						<span class="ui-icon ui-icon-plus"></span>
						<?php echo t('Add A Page'); ?>
					</a>
					<?php endif; ?>
				</ul>
				<?php
				endif;
				if ($moduleInfos['displayAdmin']  & 2) :
				?>
				<div class="db ellipsis subtitle" title="<?php echo t('Content') . ' ' . ucfirst($module); ?>"><?php echo ' ' . t('Content'); ?></div> 
				<ul class="models">
					<?php
					foreach ($moduleobj->getModel() as $entityName => $entity) {
						$entityTitle = s(ucfirst($entity->getTitle()));
						if (!empty($entityTitle)) { 
							?>
							<a href="#left_sidebar/model/<?php echo $module; ?>/<?php echo $entityName; ?>" class="sublist modelSubList" title="<?php echo $entityTitle; ?>"><?php echo $entityTitle; ?></a>
							<?php
						}
					}
					if ($_SESSION['permissions'] & 4096): /* perm 4096 = db designer */ ?>
						<li class="sublist gotoDBDesigner" title="<?php echo t('Database Designer') . ' ' . ucfirst($module); ?>">
							<?php echo t('Database Designer') ?>
							<form method="POST" class="none" action="<?php echo BASE_PATH; ?>admin/dbDesigner" target="_blank">
								<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>">
								<input type="hidden" name="module" value="<?php echo $module; ?>">
							</form>
						</li>
					<?php endif; ?>
				</ul>
				<?php endif; ?>
			</div>
		</div>
		<?php
	endif;
}
if ($_SESSION['permissions'] & 16384) : /* perm 16384 = manage modules */  ?>
	<div class="ellipsis"><a href="#left_sidebar/add" title="<?php echo t('Add a Module'); ?>" id="add-module">+ <?php echo t('Add a Module'); ?></a></div>
<?php endif;
