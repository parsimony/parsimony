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

app::$request->page->addJSFile('admin/blocks/menu/block.js','footer');
?>
<div class="flexToolbar">
	<div style="-webkit-box-flex: 2;-moz-box-flex: 2;box-flex: 2;margin-top: -2px;">
		<ul class="menu">
			<li id="logo" style="box-shadow: none;">
				<a href="http://parsimony.mobi" target="_blank" style="padding:0;display: block;">
					<img src="<?php echo BASE_PATH; ?>admin/img/parsimony.png" class="biggerLogo">
					<img src="<?php echo BASE_PATH; ?>admin/img/parsimony_little.png" class="littleLogo">
				</a>
			</li>
		<?php if (\app::$config['domain']['multisite']): ?>
			<li class="subMenu" style="height:35px">
				<a href="#" class="toolbarsprite multisite-icon" data-title="<?php echo t('My domains', FALSE); ?>"></a>
				<ul>
					<?php foreach(glob('profiles/*', GLOB_ONLYDIR) AS $domainPath):
					$basen = basename($domainPath);
					?>
					<li>
						<a href="http://<?php if($basen != 'www') echo $basen.'.'.DOMAIN.'/connect'; else echo DOMAIN.'/connect'; ?>"><?php echo ucfirst($basen); ?></a>
					</li>
					<?php endforeach; ?>
				</ul>
			</li>
			<?php endif; ?>
			<?php if ($_SESSION['behavior'] === 2): ?>
			<li style="border-left:0;height:35px;">
				<a href="#modules/settings/admin"><?php echo t('Settings', FALSE); ?></a>
			</li>
			<li class="subMenu" >
				<a href="#" data-title="<?php echo t('Accounts', FALSE); ?>"><?php echo t('Accounts', FALSE); ?></a>
				<ul>
					<li>
						<a href="#modules/permissions"><?php echo t('Permissions', FALSE); ?></a>
					</li>
					<li>
						<a href="#modules/model/core/role"><?php echo t('Manage Roles', FALSE); ?></a>
					</li>
					<li>
						<a href="#modules/model/core/user"><?php echo t('Manage Users', FALSE); ?></a>
					</li>
				</ul>
			</li>
			<?php endif; ?>
		</ul>
	</div>
	<div style="-webkit-box-flex: 1;-moz-box-flex: 1;box-flex: 1;text-align: center;">
		<ul id="modesSwitcher" class="menu" style="display: inline-block;"> 
			<?php if ($_SESSION['behavior'] > 0): ?>
			<li id="previewMode" class="switchMode" <?php if(isset($_COOKIE['mode']) && $_COOKIE['mode'] == 'preview') echo 'class="selected"'; ?>onclick="ParsimonyAdmin.setPreviewMode();"><?php echo t('Preview') ?></li><?php 
			 ?><li id="editMode" class="switchMode" <?php if(isset($_COOKIE['mode']) && $_COOKIE['mode'] == 'edit') echo 'class="selected"'; ?>onclick="ParsimonyAdmin.setEditMode();"><?php echo t('Edit') ?></li><?php 
			endif; if ($_SESSION['behavior'] === 2):?><li id="creationMode" class="switchMode" <?php if(!isset($_COOKIE['mode']) || (isset($_COOKIE['mode']) &&  $_COOKIE['mode'] == 'creation')) echo 'class="selected"'; ?> onclick="ParsimonyAdmin.setCreationMode();"><?php echo t('Creation') ?></li>
			<?php endif; ?>
		</ul> 
	</div>
	<div style="-webkit-box-flex: 1;-moz-box-flex: 1;box-flex: 1;text-align: center;">
		<ul class="menu" style="display: inline-block;">
			<?php if (count(\app::$devices) > 1): ?>
			<li class="subMenu">
					<a href="#" id="info_themetype" data-title="<?php echo t('Version', FALSE); ?>"><?php echo str_replace('theme', '', THEMETYPE); ?></a>
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
				<input id="changeres" type="hidden" value="<?php if(isset($_COOKIE['screenX']) && isset($_COOKIE['screenY']) && is_numeric($_COOKIE['screenX']) && is_numeric($_COOKIE['screenY'])) echo $_COOKIE['screenX'].'x'.$_COOKIE['screenY']; ?>">
				<script>
				ParsimonyAdmin.resolutions = new Array();
				<?php
				foreach (\app::$devices AS $device) {
				echo 'ParsimonyAdmin.resolutions["' . $device['name'] . '"] = \'' . json_encode($device['resolution']) . '\';' . PHP_EOL;
				}
				?>
				</script>
				<span <?php if(isset($_COOKIE['landscape']) && $_COOKIE['landscape'] === 'landscape') echo 'class="landscape"'; ?> onclick="if(this.classList.contains('landscape')) { $('#changeorientation').val('portrait').trigger('change');} else{ $('#changeorientation').val('landscape').trigger('change'); };this.classList.toggle('landscape');"></span>
				<select id="changeorientation" class="none">
					<option value="portrait"><?php echo t('Portrait', FALSE); ?></option><option value="landscape"<?php if(isset($_COOKIE['landscape']) &&  $_COOKIE['landscape'] === 'landscape') echo 'selected="selected"'; ?>><?php echo t('Landscape', FALSE); ?></option>
				</select>
			</li>
		</ul>
	</div>
	<div style="-webkit-box-flex: 1;-moz-box-flex: 1;box-flex: 1;padding-right:10px">
		<a href="<?php echo BASE_PATH; ?>logout" class="floatright tooltip toolbarsprite close-icon" data-tooltip="<?php echo t('Logout', false); ?>" data-pos="se"></a>
		<a href="#modules/profile" class="floatright tooltip toolbarsprite userprofile-icon" data-tooltip="<?php echo t('My Profile', false); ?>" data-pos="s">
			<?php echo ucfirst(htmlentities($_SESSION['login'], ENT_QUOTES, "UTF-8")); ?>        
		</a>
		<a href="#modules/language" class="floatright tooltip toolbarsprite language-icon" data-tooltip="<?php echo t('Current Language', FALSE); ?>" data-pos="s">
			<?php echo \request::$locales[\app::$request->getLocale()] ?>
		</a>
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
</div>