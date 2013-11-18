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
 *  @authors Julien Gras et Benoît Lorillot
 *  @copyright  Julien Gras et Benoît Lorillot
 *  @version  Release: 1.0
 * @category  Parsimony
 * @package Parsimony
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

?>

<script>
	function checkVersion() {
		$.get("http://parsimony.mobi/lastversion.php", function(data) {
			if (data[0] == "{") {
				var data = JSON.parse(data);
				for (version in data) {
					if (parseFloat(version) > <?php echo PARSIMONY_VERSION; ?>) {
						document.getElementById("stablechannel").innerHTML = '<input type="button" value="'+ t('Update to version ') + version + '" class="updateVersion" data-urlupdate="' + data[version] + '">';
						break;
					} else {
						document.getElementById("numVersion").innerHTML = version;
					}
					document.getElementById("stablechannel").innerHTML = t('You have the latest stable version');
				}
			}
		});
	}
	$(document).ready(function() {

		checkVersion();

		$(document).on('click', '.updateVersion', function() {
			document.getElementById('updateVersionLoad').style.display = "inline";
			$.post(BASE_PATH + 'admin/uptodate', {url: this.dataset.urlupdate}, function(data) {
				document.getElementById('updateVersionLoad').style.display = "none";
				checkVersion();
			});
		})
		.on('click', '#tabsb-5 input[type="radio"]', function() {
			$('#tabsb-5 input[type=checkbox]').removeClass('hidden');
			var input = document.querySelector('#tabsb-5 input[type="checkbox"][name="config[modules][active][' + this.value + ']"]');
			input.classList.add('hidden');
			input.checked = true;
		})
		.on('click', '#tabsb-9 input[type="radio"]', function() {
			$('#tabsb-9 input[type=checkbox]').removeClass('hidden');
			var input = document.querySelector('#tabsb-9 input[type="checkbox"][name="config[devices][' + this.value + ']"]');
			input.classList.add('hidden');
			input.checked = true;
		}).on('click', '#addext', function() {
			var ext = $('#extname').val() ;
			var mime =  $('#extmime').val();
			$('#authorizedextensions').append('<input type="hidden" name="config[ext][' + ext+ ']" value="removeThis"><div>' + ext + ' : ' + mime + '<input type="hidden" name="config[ext][' + ext+ ']" value="' + mime + '"><div class="remext" onclick="$(this).parent().remove();">X</div></div>')
		});
	});
</script>
<style>
	.admintabs > div{margin-bottom: 20px;}
	.adminzone td{padding:10px;min-width:100px;text-transform: capitalize}
	#updateVersionLoad{display:none}
	.hidden{pointer-events: none;opacity: 0.5}
	#authorizedextensions {position: relative;top: 25px;}
	#authorizedextensions > div {padding: 7px;margin: 6px 0;border: 1px solid #ddd;}
	.remext {float: right;padding: 0 5px;cursor: pointer;}
	#extname{width: 40px;}
	.remext::before {position: relative;color: #c0ee2d;font-weight: bold;content: "\2713";}
	.remext:hover::before{position: relative;color: #ee5a2d;font-size: 25px;font-weight: bold;content: "\d7";top: -9px;left: 3px;}
</style>
<div class="adminzone" id="admin_rights">
	<div id="conf_box_title"><?php echo t('Settings') ?></div>
	<div id="admin_rights" class="adminzonemenu">
		<div class="adminzonetab firstpanel"><a href="#tabsb-2" class="ellipsis"><?php echo t('Cache'); ?></a></div>
		<?php if (PROFILE == 'www'): ?>
			<div class="adminzonetab"><a href="#tabsb-1" class="ellipsis"><?php echo t('DB'); ?></a></div>
		<?php endif; ?>			
		<div class="adminzonetab"><a href="#tabsb-9" class="ellipsis"><?php echo t('Devices'); ?></a></div>
		<div class="adminzonetab"><a href="#tabsb-3" class="ellipsis"><?php echo t('Localization'); ?></a></div>
		<?php /* <div class="adminzonetab"><a href="#tabsb-4" class="ellipsis"><?php echo t('Preferences'); ?></a></div> */ ?>
		<div class="adminzonetab"><a href="#tabsb-5" class="ellipsis"><?php echo t('Modules'); ?></a></div>
		<div class="adminzonetab"><a href="#tabsb-6" class="ellipsis"><?php echo t('Security'); ?></a></div>
		<div class="adminzonetab"><a href="#tabsb-7" class="ellipsis"><?php echo t('Development'); ?></a></div>
		<div class="adminzonetab"><a href="#tabsb-8" class="ellipsis"><?php echo t('Mailing'); ?></a></div>
		<div class="adminzonetab"><a href="#tabsb-11" class="ellipsis"><?php echo t('Site information'); ?></a></div>
		<div class="adminzonetab"><a href="#tabsb-0" class="ellipsis"><?php echo t('Ajax'); ?></a></div>
		<div class="adminzonetab"><a href="#tabsb-10" class="ellipsis"><?php echo t('Version'); ?></a></div>
	</div>
	<div class="adminzonecontent">
		<form action="" method="POST" target="formResult">
			<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />
			<input type="hidden" name="action" value="saveConfig">
			<div id="tabsconfig" style="min-width:465px;">
				<div id="tabsb-0" class="admintabs">
					<h2><?php echo t('Ajax'); ?></h2>
					<div class="placeholder">
						<label class="label"><?php echo t('Ajax navigation'); ?></label>
						<select name="config[general][ajaxnav]">
							<option value="0"><?php echo t('No'); ?></option>
							<option value="1"<?php if (isset(app::$config['general']['ajaxnav']) && app::$config['general']['ajaxnav'] == '1') echo ' selected="selected"'; ?>><?php echo t('Yes'); ?></option>
						</select>
					</div>
				</div>
				<?php if (PROFILE === 'www'): ?>
					<div id="tabsb-1" class="admintabs">
						<h2><?php echo t('Database'); ?> </h2>
						<div class="placeholder">
							<label class="label" for="host"><?php echo t('Host'); ?></label><input name="config[db][host]" type="text" value="<?php echo s(app::$config['db']['host']); ?>">
						</div>
						<div class="placeholder">
							<label class="label" for="dbname"><?php echo t('DBname'); ?></label><input  name="config[db][dbname]" type="text" value="<?php echo s(app::$config['db']['dbname']); ?>">
						</div>
						<div class="placeholder">
							<label class="label" for="user"><?php echo t('User'); ?></label><input  name="config[db][user]" type="text" value="<?php echo app::$config['db']['user'] ?>">
						</div>
						<div class="placeholder">
							<label class="label" for="password"><?php echo t('Password'); ?></label><input  name="config[db][pass]" type="text" value="<?php echo s(app::$config['db']['pass']); ?>">
						</div>
					</div>
				<?php endif; ?>
				<div id="tabsb-2" class="admintabs">
					<h2><?php echo t('Cache Management'); ?></h2>
					<div class="placeholder">
						<label class="label" for="maxage"><?php echo t('MaxAge'); ?></label><input name="config[cache][max-age]" type="text" value="<?php echo s(app::$config['cache']['max-age']); ?>">
					</div>
					<div class="placeholder">
						<label class="label" for="cachecontrol"><?php echo t('CacheControl'); ?></label><input name="config[cache][cache-control]" type="text" value="<?php echo s(app::$config['cache']['cache-control']); ?>">
					</div>
					<div class="placeholder">
						<label class="label" for="enablecache"><?php echo t('Enable Cache'); ?></label>
						<select name="config[cache][active]" id="languages"><option value="1"><?php echo t('Enabled'); ?></option><option value="0"><?php echo t('Disabled'); ?></option></select>
					</div>
				</div>
				<div id="tabsb-3" class="admintabs">
					<h2><?php echo t('Localization'); ?></h2>
					<div class="placeholder">
						<label class="label" for="Languages"><?php echo t('Languages'); ?></label>
						<select name="config[localization][default_language]" id="languages">
							<?php
							foreach (request::$locales AS $code => $locale) {
								if (app::$config['localization']['default_language'] == $code)
									echo '<option value="' . $code . '" selected="selected">' . $locale . '</option>';
								else
									echo '<option value="' . $code . '">' . $locale . '</option>';
							}
							?>
						</select>
					</div>
					<div class="placeholder">
						<label class="label" for="TimeZone"><?php echo t('TimeZone'); ?></label>
						<select name="config[localization][timezone]" id="timezone"> 
							<?php
							$timezone_identifiers = DateTimeZone::listIdentifiers();
							$continent = '';
							foreach ($timezone_identifiers as $value) {
								if (preg_match('/^(Africa|America|Antartica|Arctic|Asia|Atlantic|Australia|Europe|Indian|Pacific|Others)\//', $value)) {
									$ex = explode('/', $value, 2); //obtain continent,city
									if ($continent != $ex[0]) {
										if ($continent != "")
											echo '</optgroup>';
										echo '<optgroup label="' . $ex[0] . '">';
									}

									$city = $ex[1];
									$continent = $ex[0];
									if (\app::$config['localization']['timezone'] == $value) $selected = ' selected="selected"';
									else $selected = '';
									echo '<option value="' . $value . '"' . $selected . '>' . $city . '</option>';
								}
							}
							?>
							</optgroup></select>
					</div>
				</div>
				<div id="tabsb-5" class="admintabs">
					<h2><?php echo t('Module management'); ?></h2>

					<?php
					$i = 0;
					$tplright = '';
					$tplleft = '';
					foreach (glob('modules/*', GLOB_ONLYDIR) as $filename) {
						$module = substr(strrchr($filename, '/'), 1);
						if ($module !== 'core' && $module !== 'admin' && is_file('modules/' . $module . '/module.php')) {
							$i++;
							if (isset(\app::$config['modules']['active'][$module])) $checked = 'checked="checked"';
							else $checked = '';
							$value = '0';
							if (is_file('modules/' . $module . '/module.php')) {
								include_once('modules/' . $module . '/module.php');
								$name = $module . '\\' . $module;
								$rc = new ReflectionClass($name);
								/* ---- hasMethod() doesn't work for __wakeup */
								foreach ($rc->getMethods() AS $method) {
									if (isset($method->name) && $method->name === '__wakeup') $value = '1';
								}
								/* ----  */
							}
							$input = ' 
							<tr class="trover">
								<td>' . $module . '</td>
								<td>
									<input type="hidden" name="config[modules][active][' . $module . ']" value="removeThis">
									<input type="checkbox" name="config[modules][active][' . $module . ']" ' . ( app::$config['modules']['default'] == $module ? 'checked="checked" class="hidden"' : '') . ' value="' . $value . '" ' . $checked . '>
								</td>
								<td>
									<input type="radio" name="config[modules][default]" value="' . $module . '" ' . ( app::$config['modules']['default'] == $module ? 'checked="checked"' : '') . '>
								</td>
								';
							if ($i % 2 == 0)
								$tplright .= $input;
							else
								$tplleft .= $input;
						}
					}
					if (!empty($tplleft)) {
						echo '<table style="float:left;">
							<thead><tr><th>' . t('Module') . '</th><th>' . t('State') . '</th><th>' . t('Default') . '</th></thead><tbody>' . $tplleft . '</tbody></table>';
					}
					if (!empty($tplright)) {
						echo '<table style="float:left; margin-left: 30px">
							<thead><tr><th>' . t('Module') . '</th><th>' . t('State') . '</th><th>' . t('Default') . '</th></thead>' . $tplright . '</tbody></table>';
					}
					if (empty($tplright) && empty($tplleft)) {
						echo '<div style="margin-top:20px">' . t('No module detected') . '</div>';
					}
					?>
				</div>
			<div id="tabsb-6" class="admintabs">
				<h2><?php echo t('Security'); ?></h2>
				<div class="placeholder">
					<label class="label"><?php echo t('Allowed IP for Admin'); ?></label><input name="config[security][allowedipadmin]" type="text" value="<?php echo s(app::$config['security']['allowedipadmin']); ?>">
				</div>
				<div class="placeholder">
					<label class="label" for="authdExt"><?php echo t('Authorized Extensions'); ?></label>
					<div id="authorizedextensions">
					<?php
						foreach (app::$config['ext'] AS $ext => $mime) {
							echo '<input type="hidden" name="config[ext][' . $ext . ']" value="removeThis"><div>' . $ext . ' : ' . $mime . '<input type="hidden" name="config[ext][' . $ext . ']" value="' . $mime . '"><div class="remext" onclick="$(this).parent().remove();"></div></div>';
						}
					?>
					</div>
				</div>
				<div style="margin:40px 0">
					<label><?php echo t('Add extenssion'); ?></label>
					<input type="text" id="extname" placeholder="jpg"> with mime <input type="text" placeholder="image/jpeg" id="extmime">
					<input type="button" id="addext" value="Add">
				</div>
			</div>
			<div id="tabsb-7" class="admintabs">
				<h2><?php echo t('Development'); ?></h2>
				<div class="placeholder">
					<label class="label"><?php echo t('Status'); ?></label>
					<select name="config[dev][status]">
						<option value="dev"><?php echo t('Development'); ?></option>
						<option value="prod"<?php if (app::$config['dev']['status'] === 'prod') echo ' selected="selected"'; ?>><?php echo t('Production'); ?></option>
					</select>
				</div>
				<div><?php echo t('Development = concatenation of CSS and JavaScript files is updated each time a change is made'); ?></div>
				<div><?php echo t('Production = CSS & JavaScript files are not updated after each modification and are cached'); ?></div>
			</div>
			<div id="tabsb-8" class="admintabs">
				<h2><?php echo t('Mailing'); ?> </h2>
				<div class="placeholder">
					<label class="label" for="config[mail][adminMail]"><?php echo t('E-mail Address'); ?></label>
					<input name="config[mail][adminMail]" type="email" value="<?php echo s(app::$config['mail']['adminMail']); ?>">
				</div>
				<div class="placeholder">
					<label class="label" for="config[mail][type]"><?php echo t('Send Type'); ?></label>
					<select name="config[mail][type]" onclick="if (this.value == 'smtp') {$('#confsmtp').show()} else {$('#confsmtp').hide()}">
						<option value="default"><?php echo t('Default'); ?></option>
						<option value="sendmail"<?php if (app::$config['mail']['type'] == 'sendmail') echo ' selected="selected"'; ?>>Sendmail</option>
						<option value="qmail"<?php if (app::$config['mail']['type'] == 'qmail') echo ' selected="selected"'; ?>>Qmail</option>
						<option value="smtp"<?php if (app::$config['mail']['type'] == 'smtp') echo ' selected="selected"'; ?>>SMTP</option>
					</select>
				</div>
				<div id="confsmtp"<?php if (app::$config['mail']['type'] != 'smtp') echo ' style="display:none"'; ?>>
					<div class="placeholder">
						<label class="label" for="server"><?php echo t('Server'); ?></label><input  name="config[mail][server]" type="text" value="<?php echo s(app::$config['mail']['server']); ?>">
					</div>
					<div class="placeholder">
						<label class="label" for="port"><?php echo t('Port'); ?></label><input  name="config[mail][port]" type="text" value="<?php echo s(app::$config['mail']['port']); ?>">
					</div>
				</div>
			</div>
			<div id="tabsb-9" class="admintabs">
				<h2><?php echo t('Devices'); ?></h2>
				<table>
					<thead>
						<tr><th>Device</th><th>State</th><th>Default</th></tr>
					</thead>
					<tbody>
					<?php
					$devices = array('desktop', 'mobile', 'tablet', 'tv');
					foreach ($devices AS $device) {
					?>
						<tr class="trover">
							<td><?php echo t(ucfirst($device), FALSE); ?></td>
							<td>
								<input type="hidden" name="config[devices][<?php echo $device; ?>]" value="0">
								<input type="checkbox" name="config[devices][<?php echo $device; ?>]" value="1" <?php if (app::$config['devices'][$device]) echo 'checked="checked"'; ?><?php if (app::$config['devices']['defaultDevice'] == $device) echo ' class="hidden"'; ?>>
							</td>
							<td>
								<input type="radio" name="config[devices][defaultDevice]" onclick="top.ParsimonyAdmin.setCookie('device', '<?php echo $device; ?>', 999);" value="<?php echo $device; ?>" <?php if (app::$config['devices']['defaultDevice'] == $device) echo 'checked="checked"'; ?>>
							</td>
						</tr>
					<?php
					}
					?>
					</tbody>
				</table>
			</div>
			<div id="tabsb-10" class="admintabs">
				<h2><?php echo t('Current'); ?> Parsimony <?php echo t('Version'); ?> : <span id="numVersion"><?php echo PARSIMONY_VERSION; ?></span> <img src="<?php echo BASE_PATH; ?>admin/img/load.gif" id="updateVersionLoad" /></h2>
				<div><h3>Stable channel</h3>
					<div id="stablechannel">
					</div>
				</div>
				<div><h3>Nightly channel</h3>
					<input type="button" value="Update to lastest nightly version" class="updateVersion" data-urlupdate="http://nodeload.github.com/parsimony/parsimony_cms/legacy.zip/master">
				</div>
			</div>
			<div id="tabsb-11" class="admintabs">
				<h2><?php echo t('Site information'); ?></h2>
				<div class="placeholder">
					<label class="label" for="config[sitename]"><?php echo t('Site name'); ?></label>
					<input name="config[sitename]" type="text" value="<?php echo s(app::$config['sitename']); ?>">
				</div>
			</div>
			<input type="hidden" name="file" value="<?php echo 'profiles/' . PROFILE . '/config.php'; ?>">
			<input class="none" id="save_configs" type="submit">
		</form>
	</div>
	<div class="adminzonefooter">
		<div id="save_page" class="save ellipsis" onclick="$('#save_configs').trigger('click');event.preventDefault();return false;"><?php echo t('Save'); ?></div>
	</div>
</div>