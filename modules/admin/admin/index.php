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
						document.getElementById("stablechannel").innerHTML = '<input type="button" value="Update to version ' + version + '" class="updateVersion" data-urlupdate="' + data[version] + '">';
						break;
					} else {
						document.getElementById("numVersion").innerHTML = version;
					}
					document.getElementById("stablechannel").innerHTML = 'You have the lastest stable version';
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
		});
	});
</script>
<style>
	.admintabs > div{margin-bottom: 20px;}
	.adminzone td{padding:10px;min-width:100px;text-transform: capitalize}
	.adminzone td input{margin-left: 30px;}
	#updateVersionLoad{display:none}
	.hidden{pointer-events: none;opacity: 0.5}
</style>
<div class="adminzone" id="admin_rights">
	<div id="conf_box_title"><?php echo t('Settings') ?></div>
	<div id="admin_rights" class="adminzonemenu">
		<div class="adminzonetab firstpanel"><a href="#tabsb-2" class="ellipsis"><?php echo t('Cache', FALSE); ?></a></div>
		<?php if (PROFILE == 'www'): ?>
			<div class="adminzonetab"><a href="#tabsb-1" class="ellipsis"><?php echo t('DB', FALSE); ?></a></div>
		<?php endif; ?>			
		<div class="adminzonetab"><a href="#tabsb-9" class="ellipsis"><?php echo t('Devices', FALSE); ?></a></div>
		<div class="adminzonetab"><a href="#tabsb-3" class="ellipsis"><?php echo t('Localization', FALSE); ?></a></div>
		<?php /* <div class="adminzonetab"><a href="#tabsb-4" class="ellipsis"><?php echo t('Preferences', FALSE); ?></a></div> */ ?>
		<div class="adminzonetab"><a href="#tabsb-5" class="ellipsis"><?php echo t('Modules', FALSE); ?></a></div>
		<div class="adminzonetab"><a href="#tabsb-6" class="ellipsis"><?php echo t('Security', FALSE); ?></a></div>
		<div class="adminzonetab"><a href="#tabsb-7" class="ellipsis"><?php echo t('Development', FALSE); ?></a></div>
		<div class="adminzonetab"><a href="#tabsb-8" class="ellipsis"><?php echo t('Mailing', FALSE); ?></a></div>
		<div class="adminzonetab"><a href="#tabsb-11" class="ellipsis"><?php echo t('Site information', FALSE); ?></a></div>
		<div class="adminzonetab"><a href="#tabsb-0" class="ellipsis"><?php echo t('Ajax', FALSE); ?></a></div>
		<div class="adminzonetab"><a href="#tabsb-10" class="ellipsis"><?php echo t('Version', FALSE); ?></a></div>
	</div>
	<div class="adminzonecontent">
		<form action="" method="POST" target="formResult">
			<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />
			<input type="hidden" name="action" value="saveConfig">
			<div id="tabsconfig" style="min-width:465px;">
				<div id="tabsb-0" class="admintabs">
					<h2><?php echo t('Ajax', FALSE); ?></h2>
					<div class="placeholder">
						<label class="label"><?php echo t('Ajax navigation', FALSE); ?></label>
						<select name="config[general][ajaxnav]">
							<option value="0"><?php echo t('No', FALSE); ?></option>
							<option value="1"<?php if (isset(app::$config['general']['ajaxnav']) && app::$config['general']['ajaxnav'] == '1') echo ' selected="selected"'; ?>><?php echo t('Yes', FALSE); ?></option>
						</select>
					</div>
				</div>
				<?php if (PROFILE == 'www'): ?>
					<div id="tabsb-1" class="admintabs">
						<h2><?php echo t('Database'); ?> </h2>
						<div class="placeholder">
							<label class="label" for="host"><?php echo t('Host', FALSE); ?></label><input name="config[db][host]" type="text" value="<?php echo s(app::$config['db']['host']); ?>">
						</div>
						<div class="placeholder">
							<label class="label" for="dbname"><?php echo t('DBname', FALSE); ?></label><input  name="config[db][dbname]" type="text" value="<?php echo s(app::$config['db']['dbname']); ?>">
						</div>
						<div class="placeholder">
							<label class="label" for="user"><?php echo t('User', FALSE); ?></label><input  name="config[db][user]" type="text" value="<?php echo app::$config['db']['user'] ?>">
						</div>
						<div class="placeholder">
							<label class="label" for="password"><?php echo t('Password', FALSE); ?></label><input  name="config[db][pass]" type="text" value="<?php echo s(app::$config['db']['pass']); ?>">
						</div>
					</div>
				<?php endif; ?>
				<div id="tabsb-2" class="admintabs">
					<h2><?php echo t('Cache Management'); ?></h2>
					<div class="placeholder">
						<label class="label" for="maxage"><?php echo t('MaxAge', FALSE); ?></label><input name="config[cache][max-age]" type="text" value="<?php echo s(app::$config['cache']['max-age']); ?>">
					</div>
					<div class="placeholder">
						<label class="label" for="cachecontrol"><?php echo t('CacheControl', FALSE); ?></label><input name="config[cache][cache-control]" type="text" value="<?php echo s(app::$config['cache']['cache-control']); ?>">
					</div>
					<div class="placeholder">
						<label class="label" for="authdExt"><?php echo t('Authorized Extensions', FALSE); ?></label><input name="config[extensions_auth]" type="text" value="<?php echo s(app::$config['extensions_auth']); ?>">
					</div>
					<div class="placeholder">
						<label class="label" for="enablecache"><?php echo t('Enable Cache', FALSE); ?></label>
						<select name="config[cache][active]" id="languages"><option value="1"><?php echo t('Enabled', FALSE); ?></option><option value="0"><?php echo t('Disabled', FALSE); ?></option></select>
					</div>
				</div>
				<div id="tabsb-3" class="admintabs">
					<h2><?php echo t('Localization'); ?></h2>
					<div class="placeholder">
						<label class="label" for="Languages"><?php echo t('Languages', FALSE); ?></label>
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
						<label class="label" for="TimeZone"><?php echo t('TimeZone', FALSE); ?></label>
						<select name="config[localization][timezone]" id="timezone">  <?php
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
				<?php /*
				  <div id="tabsb-4" class="admintabs">
				  <h2><?php echo t('Preferences'); ?></h2>
				  <div class="placeholder">
				  <label class="label" for="conteneurColor"><?php echo t('Color of the Container', FALSE); ?></label>
				  <input class="colorpicker2" id="conteneurColor" pattern="#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})" name="config[preferences][conteneurColor]" type="text" value="<?php echo app::$config['preferences']['conteneurColor']; ?>">
				  </div>
				  <div class="placeholder">
				  <label class="label" for="blockColor"><?php echo t('Color of the Block', FALSE); ?></label>
				  <input class="colorpicker2" id="blockColor" pattern="#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})" name="config[preferences][blockColor]" type="text" value="<?php echo app::$config['preferences']['blockColor']; ?>">
				  </div>
				  <div class="placeholder">
				  <label class="label" for="cssPickerColor"><?php echo t('Color of the CSS Picker', FALSE); ?></label>
				  <input class="colorpicker2" id="cssPickerColor" pattern="#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})" name="config[preferences][cssPickerColor]" type="text" value="<?php echo app::$config['preferences']['cssPickerColor']; ?>">
				  </div>
				  <div class="placeholder">
				  <label class="label" for="translateColor"><?php echo t('Color of the Traductor', FALSE); ?></label>
				  <input class="colorpicker2" id="translateColor" pattern="#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})" name="config[preferences][translateColor]" type="text" value="<?php echo app::$config['preferences']['translateColor']; ?>">
				  </div>
				  </div>
				 */ ?>
				<div id="tabsb-5" class="admintabs">
					<h2><?php echo t('Module management', FALSE); ?></h2>

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
							<thead><tr><th>' . t('Module', FALSE) . '</th><th>' . t('State', FALSE) . '</th><th>' . t('Default', FALSE) . '</th></thead><tbody>' . $tplleft . '</tbody></table>';
					}
					if (!empty($tplright)) {
						echo '<table style="float:left; margin-left: 30px">
							<thead><tr><th>' . t('Module', FALSE) . '</th><th>' . t('State', FALSE) . '</th><th>' . t('Default', FALSE) . '</th></thead>' . $tplright . '</tbody></table>';
					}
					if (empty($tplright) && empty($tplleft)) {
						echo '<div style="margin-top:20px">' . t('No module detected', FALSE) . '</div>';
					}
					?>
				</div>
			</div>
			<div id="tabsb-6" class="admintabs">
				<h2><?php echo t('Security', FALSE); ?></h2>
				<div class="placeholder">
					<label class="label"><?php echo t('Allowed IP for Admin', FALSE); ?></label><input name="config[security][allowedipadmin]" type="text" value="<?php echo s(app::$config['security']['allowedipadmin']); ?>">
				</div>
			</div>
			<div id="tabsb-7" class="admintabs">
				<h2><?php echo t('Development', FALSE); ?></h2>
				<div class="placeholder">
					<label class="label"><?php echo t('Status', FALSE); ?></label>
					<select name="config[dev][status]">
						<option value="dev"><?php echo t('Development', FALSE); ?></option>
						<option value="prod"<?php if (app::$config['dev']['status'] == 'prod') echo ' selected="selected"'; ?>><?php echo t('Production', FALSE); ?></option>
					</select>
				</div>
				<div><?php echo t('Development = concatenation of CSS and JavaScript files is updated each time a change is made', FALSE); ?></div>
				<div><?php echo t('Production = CSS & JavaScript files are not updated after each modification and are cached', FALSE); ?></div>

				<?php /*
				  <div class="placeholder">
				  <label class="label"><?php echo t('Serialization', FALSE); ?></label>
				  <select name="config[dev][serialization]">
				  <option value="obj"><?php echo t('Object', FALSE); ?></option>
				  <option value="json"<?php if(app::$config['dev']['serialization']=='json') echo ' selected="selected"'; ?>><?php echo t('Json', FALSE); ?></option>
				  </select>
				  </div>
				 * */ ?>
			</div>
			<div id="tabsb-8" class="admintabs">
				<h2><?php echo t('Mailing'); ?> </h2>
				<div class="placeholder">
					<label class="label" for="config[mail][adminMail]"><?php echo t('E-mail Address', FALSE); ?></label>
					<input name="config[mail][adminMail]" type="email" value="<?php echo s(app::$config['mail']['adminMail']); ?>">
				</div>
				<div class="placeholder">
					<label class="label" for="config[mail][type]"><?php echo t('Send Type', FALSE); ?></label>
					<select name="config[mail][type]" onclick="if (this.value == 'smtp') {$('#confsmtp').show()} else {$('#confsmtp').hide()}">
						<option value="default"><?php echo t('Default', FALSE); ?></option>
						<option value="sendmail"<?php if (app::$config['mail']['type'] == 'sendmail') echo ' selected="selected"'; ?>>Sendmail</option>
						<option value="qmail"<?php if (app::$config['mail']['type'] == 'qmail') echo ' selected="selected"'; ?>>Qmail</option>
						<option value="smtp"<?php if (app::$config['mail']['type'] == 'smtp') echo ' selected="selected"'; ?>>SMTP</option>
					</select>
				</div>
				<div id="confsmtp"<?php if (app::$config['mail']['type'] != 'smtp') echo ' style="display:none"'; ?>>
					<div class="placeholder">
						<label class="label" for="server"><?php echo t('Server', FALSE); ?></label><input  name="config[mail][server]" type="text" value="<?php echo s(app::$config['mail']['server']); ?>">
					</div>
					<div class="placeholder">
						<label class="label" for="port"><?php echo t('Port', FALSE); ?></label><input  name="config[mail][port]" type="text" value="<?php echo s(app::$config['mail']['port']); ?>">
					</div>
				</div>
			</div>
			<div id="tabsb-9" class="admintabs">
				<h2><?php echo t('Devices', FALSE); ?></h2>
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
				<h2><?php echo t('Current', FALSE); ?> Parsimony <?php echo t('Version', FALSE); ?> : <span id="numVersion"><?php echo PARSIMONY_VERSION; ?></span> <img src="<?php echo BASE_PATH; ?>admin/img/load.gif" id="updateVersionLoad" /></h2>
				<div><h3>Stable channel</h3>
					<div id="stablechannel">
					</div>
				</div>
				<div><h3>Nightly channel</h3>
					<input type="button" value="Update to lastest nightly version" class="updateVersion" data-urlupdate="http://nodeload.github.com/parsimony/parsimony_cms/legacy.zip/master">
				</div>
			</div>
			<div id="tabsb-11" class="admintabs">
				<div class="placeholder">
					<label class="label" for="config[sitename]"><?php echo t('Site name', FALSE); ?></label>
					<input name="config[sitename]" type="text" value="<?php echo s(app::$config['sitename']); ?>">
				</div>
			</div>
			<?php if (PROFILE == 'www'): ?>
				<input type="hidden" name="file" value="config.php">
			<?php else: ?>
				<input type="hidden" name="file" value="<?php echo 'profiles/' . PROFILE . '/config.php'; ?>">
			<?php endif; ?>
			<input class="none" id="save_configs" type="submit">
		</form>
	</div>
	<div class="adminzonefooter">
		<div id="save_page" class="save ellipsis" onclick="$('#save_configs').trigger('click');event.preventDefault();return false;"><?php echo t('Save', FALSE); ?></div>
	</div>
</div>