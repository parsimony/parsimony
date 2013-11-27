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
app::$request->page->addJSFile('admin/blocks/menu/block.js', 'footer');
?>
<ul class="creation leftSidebarMenu" data-sidebar="left" style="width:200px;">
	<li class="icons modules sprite floatleft" data-panel="modules"></li>
	<li class="icons panelblocks sprite floatleft" data-panel="panelblocks"></li>
	<?php if ($_SESSION['behavior'] === 2): ?>
		<li class="icons sprite floatleft manage" data-panel="manage"></li>
	<?php endif; ?>
</ul>

<ul style="flex-grow:1;display: flex;align-items: center;">
	<?php if ($_SESSION['behavior'] === 2): ?>
		<li class="roundBTN creation tooltip sprite sprite-bdd" data-tooltip="<?php echo t('Db Modeling'); ?>" data-pos="s" onclick="$(this).next('form').trigger('submit');"></li>
		<form method="POST" class="none" action="<?php echo BASE_PATH ?>admin/dbDesigner" target="_blank"></form>
	<?php endif; ?>
	<li class="roundBTN creation tooltip sprite sprite-dir" data-tooltip="<?php echo t('Files Explorer'); ?>" data-pos="s" onclick="ParsimonyAdmin.displayExplorer();"></li>
	<li class="edit" style="font-size: 22px;" onclick="$('#left_sidebar').toggleClass('pin2');$(this).toggleClass('active');
			document.body.classList.toggle('closeleft')">☰</li>
	<img class="edit" src="<?php echo BASE_PATH; ?>admin/img/parsimony.png" style="margin-left: 15px;">
</ul>
<div style="flex-grow:2;display: flex;justify-content: center;align-items: center;">
	<ul id="modesSwitcher"> 
		<?php if ($_SESSION['behavior'] > 0): ?>
			<li id="previewMode" class="switchMode" <?php if (isset($_COOKIE['mode']) && $_COOKIE['mode'] == 'preview') echo 'class="selected"'; ?>onclick="ParsimonyAdmin.setPreviewMode();"><?php echo t('Preview') ?></li><?php
			?><li id="editMode" class="switchMode" <?php if (isset($_COOKIE['mode']) && $_COOKIE['mode'] == 'edit') echo 'class="selected"'; ?>onclick="ParsimonyAdmin.setEditMode();"><?php echo t('Edit') ?></li><?php
		endif;
		if ($_SESSION['behavior'] === 2):
			?><li id="creationMode" class="switchMode" <?php if (!isset($_COOKIE['mode']) || (isset($_COOKIE['mode']) && $_COOKIE['mode'] == 'creation')) echo 'class="selected"'; ?> onclick="ParsimonyAdmin.setCreationMode();"><?php echo t('Design') ?></li>
			<?php endif; ?>
	</ul>
</div>
<ul class="menu" style="flex-grow:2;display: flex;justify-content: center">
	<?php
	$profiles = glob('profiles/*', GLOB_ONLYDIR);
	if (count($profiles) > 1) :
	?>
		<li class="subMenu" style="height:36px">
			<a href="#" class="toolbarsprite multisite-icon" data-title="<?php echo t('My domains'); ?>"></a>
			<ul>
				<?php
				foreach ($profiles AS $domainPath):
					$basen = basename($domainPath);
					?>
					<li>
						<a href="http://<?php
						if ($basen !== 'www')
							echo $basen . '.' . DOMAIN . '/connect';
						else
							echo DOMAIN . '/connect';
						?>"><?php echo ucfirst($basen); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php if (count(\app::$devices) > 1): ?>
		<li class="subMenu">
			<a href="#" id="info_themetype" data-title="<?php echo t('Version'); ?>"><?php echo str_replace('theme', '', THEMETYPE); ?></a>
			<ul id="changeDevice" class="changeVersion">
				<?php foreach (\app::$devices AS $device): ?>
					<li data-device="<?php echo $device['name']; ?>"><?php echo ucfirst($device['name']); ?></li>
				<?php endforeach; ?>
			</ul>
		</li>
	<?php endif; ?>
	<li style="border-left: 0;position: relative" class="subMenu">
		<a href="#" style="position: relative;"><span id="currentRes"></span></a>
		<ul id="listres" class="changeVersion"></ul>
	</li>
	<li class="orientation" style="box-shadow: none;padding-top: 8px;background: none;padding-left: 10px;opacity: 0.95;">
		<input id="changeres" type="hidden" value="<?php if (isset($_COOKIE['screenX']) && isset($_COOKIE['screenY']) && is_numeric($_COOKIE['screenX']) && is_numeric($_COOKIE['screenY'])) echo $_COOKIE['screenX'] . 'x' . $_COOKIE['screenY']; ?>">
		<script>
		ParsimonyAdmin.resolutions = new Array();
		<?php
		foreach (\app::$devices AS $device) {
			echo 'ParsimonyAdmin.resolutions["' . $device['name'] . '"] = \'' . json_encode($device['resolution']) . '\';' . PHP_EOL;
		}
		?>
		</script>
		<span <?php if (isset($_COOKIE['landscape']) && $_COOKIE['landscape'] === 'landscape') echo 'class="landscape"'; ?> onclick="if (this.classList.contains('landscape')) {
				$('#changeorientation').val('portrait').trigger('change');
			} else {
				$('#changeorientation').val('landscape').trigger('change');
			}
			;
			this.classList.toggle('landscape');"></span>
		<select id="changeorientation" class="none">
			<option value="portrait"><?php echo t('Portrait'); ?></option><option value="landscape"<?php if (isset($_COOKIE['landscape']) && $_COOKIE['landscape'] === 'landscape') echo 'selected="selected"'; ?>><?php echo t('Landscape'); ?></option>
		</select>
	</li>
</ul>
<div style="flex-grow:1">
	<ul class="menu" style="display: inline-block;float:right;position: relative;">
		<li class="subMenu">
			<a href="#modules/profile" class="toolbarsprite userprofile-icon" style="padding-left: 20px;">
<?php echo ucfirst(htmlentities($_SESSION['login'], ENT_QUOTES, "UTF-8")); ?>        
			</a>
			<ul>
				<li>
					<a href="#modules/language" class="toolbarspriteblack language-icon">
<?php echo \request::$locales[\app::$request->getLocale()] ?>
					</a>
				</li>
				<li>
					<a href="<?php echo BASE_PATH; ?>logout" class="toolbarspriteblack close-icon"><?php echo t('Logout'); ?></a>
				</li>
			</ul>
		</li>
	</ul>

<?php if ($_SESSION['behavior'] == 2): ?>
		<a class="floatright tooltip toolbarsprite info-icon" href="#" data-tooltip="#infodev" data-pos="s"></a>
		<div id="infodev">
			<div>Time : <span id="infodev_timer"></span></div>
			<div>Current Theme : <span id="infodev_theme"></span></div>
			<div>Current Module : <span id="infodev_module"></span></div>
			<div>Current Page : <span id="infodev_page"></span></div>
			<div>PHP version : <span><?php echo phpversion(); ?></span></div>
		</div>
<?php endif; ?>
</div>
<ul class="creation rightSidebarMenu" data-sidebar="right" style="width:230px;">
	<li class="icons sprite paneltree floatright" data-panel="paneltree"></li>
	<li class="icons sprite panelcss floatright" data-panel="panelcss"></li>
	<li class="icons sprite themes floatright" data-panel="themes"></li>
</ul>
