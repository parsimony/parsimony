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
 * @package Parsimony
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

?>

<script>
	function checkVersion() {
		$.get("http://parsimony.mobi/lastversion.php", function(data) {
			if (data[0] == "{") {
				var data = JSON.parse(data);
				for (version in data) {
					if (parseFloat(version) > parseFloat("<?php echo PARSIMONY_VERSION; ?>")) {
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
		.on('click', '#tabsb-9 input[type="radio"]', function() {
			$('#tabsb-9 input[type=checkbox]').removeClass('hidden');
			var input = document.querySelector('#tabsb-9 input[type="checkbox"][name="config[devices][' + this.value + ']"]');
			input.classList.add('hidden');
			input.checked = true;
		}).on('click', '#addext', function() {
			var ext = $('#extname').val() ;
			var mime =  $('#extmime').val();
			$('#authorizedextensions').append('<input type="hidden" name="config[ext][' + ext+ ']" value="removeThis"><div>' + ext + ' : ' + mime + '<input type="hidden" name="config[ext][' + ext+ ']" value="' + mime + '"><div class="remitem" onclick="$(this).parent().remove();"></div></div>')
		})
		.on("click.creation", ".explorer", function() {
			top.callbackExplorerID = this.getAttribute("rel");
			top.callbackExplorer = function(file) {
				$("#" + top.callbackExplorerID).val(file);
				$("#" + top.callbackExplorerID).trigger('change');
				top.callbackExplorer = function(file) {
					return false;
				};
				top.ParsimonyAdmin.explorer.close();
			}
			top.ParsimonyAdmin.displayExplorer();
		});
		
		$("#tabsb-5").on('click', '.installModule', function(e) {
			e.preventDefault();
			$.post(BASE_PATH + 'admin/installModule', {TOKEN: TOKEN, module: this.dataset.module}, function(data) {
				if(data == 1) {
					top.document.location.href = top.document.location.origin + top.document.location.pathname + "#" ;
					top.document.location.href = top.document.location.origin + top.document.location.pathname + "#left_sidebar/settings/admin";
				} else {
					alert("Error");
				}
			});
		})
		.on('click', '.activeModule', function() {
			this.parentNode.parentNode.classList.toggle("active");
		})
		.on('click', 'input[type="radio"]', function() {
			$('#tabsb-5 input[type=checkbox]').removeClass('hidden');
			var input = document.querySelector('#tabsb-5 input[type="checkbox"][name="config[modules][' + this.value + ']"]');
			input.classList.add('hidden');
			input.checked = true;
		})
		.on('click', '.developmentMode', function() {
			var input = this.parentNode.parentNode.querySelector('.onOff');
			if(this.checked == true) {
				this.nextElementSibling.classList.remove("none");
				if((input.value & 4) == 0){
					input.value = parseInt(input.value) + 4;
				}
			} else if(this.checked == false) {
				this.nextElementSibling.classList.add("none");
				if((input.value & 4) == 4){
					input.value = parseInt(input.value) - 4;
				}
			}
		})
		.on('click', '.package', function(e) {
			e.preventDefault();
			$.post(BASE_PATH + 'admin/packageModule', {TOKEN: TOKEN, module: this.dataset.module}, function(data) {
				if(data != 0) {
					document.location = BASE_PATH + data;
				} else {
					alert("Error");
				}
			});
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
	#extname{width: 40px;}
	#tabsb-5 .package{margin-left:15px;visibility:hidden}
	#tabsb-5 .developmentMode{visibility:hidden}
	#tabsb-5 .active .package{visibility:visible}
	#tabsb-5 .active .developmentMode{visibility:visible}
</style>
<div class="adminzone" id="admin_rights">
	<div id="conf_box_title"><?php echo t('Settings') ?></div>
	<div id="admin_rights" class="adminzonemenu">
		<div class="adminzonetab firstpanel"><a href="#tabsb-11" class="ellipsis"><?php echo t('Site information'); ?></a></div>
		<?php if ($_SESSION['permissions'] & 2): ?>
			<div class="adminzonetab"><a href="#tabsb-2" class="ellipsis"><?php echo t('Cache'); ?></a></div>
			<div class="adminzonetab"><a href="#tabsb-1" class="ellipsis"><?php echo t('DB'); ?></a></div>
			<div class="adminzonetab"><a href="#tabsb-3" class="ellipsis"><?php echo t('Localization'); ?></a></div>
			<?php if ($_SESSION['permissions'] & 16384): ?>
				<div class="adminzonetab"><a href="#tabsb-5" class="ellipsis"><?php echo t('Modules'); ?></a></div>
			<?php endif; ?>
			<div class="adminzonetab"><a href="#tabsb-6" class="ellipsis"><?php echo t('Security'); ?></a></div>
			<div class="adminzonetab"><a href="#tabsb-7" class="ellipsis"><?php echo t('Development'); ?></a></div>
			<div class="adminzonetab"><a href="#tabsb-8" class="ellipsis"><?php echo t('Mailing'); ?></a></div>
			<div class="adminzonetab"><a href="#tabsb-12" class="ellipsis"><?php echo t('Sessions'); ?></a></div>
			<div class="adminzonetab"><a href="#tabsb-10" class="ellipsis"><?php echo t('Version'); ?></a></div>
		<?php endif; ?>
	</div>
	<div class="adminzonecontent">
		<form action="" method="POST" target="formResult">
			<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />
			<input type="hidden" name="action" value="saveConfig">
			<div id="tabsconfig" style="min-width:465px;">
			<div id="tabsb-11" class="admintabs">
				<h2><?php echo t('Site information'); ?></h2>
				<div class="placeholder">
					<label class="label" for="config[sitename]"><?php echo t('Site name'); ?></label>
					<input name="config[sitename]" type="text" value="<?php echo s(app::$config['sitename']); ?>">
				</div>
				<div class="placeholder">
					<label class="label" for="config[favicon]"><?php echo t('Favicon'); ?></label>
					<div style="position: relative">
						<input name="config[favicon]" id="favicon" type="text" value="<?php echo s((isset(\app::$config['favicon']) ? \app::$config['favicon'] : 'core/img/favicon.png')); ?>">
						<span class="ui-icon ui-icon-folder-open explorer" rel="favicon" style="position: absolute;top: 29px;right: 5px;cursor: pointer;"></span>
					</div>
					
				</div>
			</div>
			<?php if ($_SESSION['permissions'] & 2): ?>
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
				<?php if ($_SESSION['permissions'] & 16384): ?>
				<div id="tabsb-5" class="admintabs">
					<h2><?php echo t('Module management'); ?></h2>
					<?php
					$modules = glob('modules/*', GLOB_ONLYDIR);
					if (count($modules) > 2) { /* minimum 2: core && admin */
						?>
					<table style="width: 100%;">
						<thead>
							<tr>
								<th><?php echo t('Module') ?></th>
								<th><?php echo t('State') ?></th>
								<th><?php echo t('Default') ?></th>
								<th><?php echo t('Development') ?></th>
							</tr>
						</thead>
						<tbody>
					<?php
						foreach ($modules as $filename) {
							$module = substr(strrchr($filename, '/'), 1);
							if ($module !== 'core' && $module !== 'admin' && is_file('modules/' . $module . '/module.php')) {
								if(isset(\app::$config['modules'][$module])):
									$checked = '';
									$value = 1;
									if (\app::$config['modules'][$module] & 1) {
										$checked = 'checked="checked"';
										$value = \app::$config['modules'][$module];
									}
									include_once('modules/' . $module . '/module.php');
									$name = $module . '\\module';
									$rc = new ReflectionClass($name);
									/* ---- hasMethod() doesn't work for __wakeup */
									foreach ($rc->getMethods() AS $method) {
										if (isset($method->name) && $method->name === '__wakeup' && (\app::$config['modules'][$module] & 2) != 2) {
											$value = $value + 2;
										}
									}
									/* ----  */
								?>
								<tr<?php echo ( !empty($checked) ? ' class="active"' : '') ?>>
									<td><?php echo $module ?></td>
									<td>
										<input type="hidden" name="config[modules][<?php echo $module ?>]" value="0">
										<input type="checkbox" name="config[modules][<?php echo $module ?>]" value="<?php echo $value ?>" <?php echo $checked  ?> class="activeModule onOff<?php echo ( app::$config['defaultModule'] == $module ? ' hidden' : '') ?> ">
									</td>
									<td>
										<input type="radio" name="config[defaultModule]" value="<?php echo $module ?>" <?php echo ( app::$config['defaultModule'] == $module ? 'checked="checked"' : '') ?>>
									</td>
									<td>
										<input type="checkbox" class="onOff developmentMode" <?php echo ( $value & 4 ? 'checked="checked"' : '') ?>>
										<button class="package highlight<?php echo ( $value & 4 ? '' : ' none') ?>" data-module="<?php echo $module ?>"><?php echo t('Package') ?></button>
									</td>
								</tr>
								<?php
								else:
									?>
								<tr style="background: #f5f5f5">
									<td><?php echo $module ?></td>
									<td colspan="3"><button class="installModule highlight" data-module="<?php echo $module ?>">Install</button></td>
								</tr>
								<?php
								endif;
							}
						}?>
							</tbody>
						</table><?php
					} else {
						echo '<div style="margin-top:20px">' . t('No module detected') . '</div>';
					}
					?>
				</div>
			<?php endif; ?>
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
							echo '<input type="hidden" name="config[ext][' . $ext . ']" value="removeThis"><div>' . $ext . ' : ' . $mime . '<input type="hidden" name="config[ext][' . $ext . ']" value="' . $mime . '"><div class="remitem" onclick="$(this).parent().remove();"></div></div>';
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
			<div id="tabsb-12" class="admintabs">
				<h2><?php echo t('Sessions'); ?></h2>
				<div class="placeholder">
					<label class="label" for="config[session][renew]"><?php echo t('Renew session n each sec'); ?></label>
					<input name="config[session][renew]" type="text" value="<?php echo s(app::$config['session']['renew']); ?>">
				</div>
				<div class="placeholder">
					<label class="label" for="config[session][maxlifetime]"><?php echo t('Maximum life time'); ?></label>
					<input name="config[session][maxlifetime]" type="text" value="<?php echo s(app::$config['session']['maxlifetime']); ?>">
				</div>
				<div class="placeholder">
					<label class="label" for="config[session][depth]"><?php echo t('Directory tree for storing file session'); ?></label>
					<input name="config[session][depth]" type="text" value="<?php echo s(app::$config['session']['depth']); ?>">
					<a href="http://www.php.net/manual/en/session.configuration.php#ini.session.save-path" target="_blank">You must create directory tree before use.</a>
				</div>
			</div>
			<?php endif; ?>
			<input type="hidden" name="file" value="<?php echo 'profiles/' . PROFILE . '/config.php'; ?>">
			<input class="none" id="save_configs" type="submit">
		</form>
	</div>
	<div class="adminzonefooter">
		<button id="save_page" class="save highlight" onclick="$('#save_configs').trigger('click');event.preventDefault();return false;"><?php echo t('Save'); ?></button>
	</div>
</div>