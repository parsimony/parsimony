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
 *  @authors Julien Gras et Benoît Lorillot
 *  @copyright  Julien Gras et Benoît Lorillot
 *  @version  Release: 1.0
 * @category  Parsimony
 * @package Parsimony
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

?>
<link type="text/css" rel="stylesheet" href="<?php echo BASE_PATH ?>lib/colorpicker/colorpicker.css">
<script type="text/javascript" src="<?php echo BASE_PATH ?>lib/colorpicker/colorpicker.js"></script>
<script>
    $(document).ready(function(){
        $("select option").each(function(){
            if(!$(this).val().length){
                $(this).text('Default');
                $(this).val('');
            };
        });
        /* Color Picker */
        var currentColorPicker = $(".colorpicker2");
        var picker = new Color.Picker({
            callback: function(hex) {
                currentColorPicker.val("#" + hex).trigger("change");
            }
        });
        $(".adminzonecontent").on('click','.colorpicker2',function(){
            currentColorPicker = $(this);
            picker.el.style.display = "block";
            picker.el.style.top = ($(this).offset().top) + 25 + "px";
            picker.el.style.left = ($(this).offset().left + 200) + "px";
        });
    });
</script>
<style>
    .admintabs > div{margin-bottom: 20px;} 
</style>
<div class="adminzone" id="admin_rights">
    <div id="admin_rights" class="adminzonemenu">
        <div class="save"><a href="#" class="ellipsis" onclick="$('#save_configs').trigger('click');event.preventDefault();return false;"><?php echo t('Save', FALSE); ?></a></div>
        <div class="adminzonetab firstpanel"><a href="#tabsb-2" class="ellipsis">Cache</a></div>
        <?php if (PROFILE == 'www'): ?>
            <div class="adminzonetab"><a href="#tabsb-1" class="ellipsis"><?php echo t('DB', FALSE); ?></a></div>
        <?php endif; ?>
        <div class="adminzonetab"><a href="#tabsb-3" class="ellipsis"><?php echo t('Localization', FALSE); ?></a></div>
        <?php /*<div class="adminzonetab"><a href="#tabsb-4" class="ellipsis"><?php echo t('Preferences', FALSE); ?></a></div>*/ ?>
        <div class="adminzonetab"><a href="#tabsb-5" class="ellipsis"><?php echo t('Enable Module', FALSE); ?></a></div>
        <div class="adminzonetab"><a href="#tabsb-6" class="ellipsis"><?php echo t('Security', FALSE); ?></a></div>
	<div class="adminzonetab"><a href="#tabsb-7" class="ellipsis"><?php echo t('Development', FALSE); ?></a></div>
        <div class="adminzonetab"><a href="#tabsb-8" class="ellipsis"><?php echo t('Mailing', FALSE); ?></a></div>
    </div>
    <div class="adminzonecontent">
        <form action="" method="POST" target="ajaxhack">
            <input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />
            <input type="hidden" name="action" value="saveConfig">
            <div id="tabsconfig" style="min-width:465px;">
                <?php if (PROFILE == 'www'): ?>
                    <div id="tabsb-1" class="admintabs">
                        <h2><?php echo t('Database'); ?> </h2>
                        <div class="placeholder">
                            <label class="label" for="host"><?php echo t('Host', FALSE); ?></label><input name="config[db][host]" type="text" value="<?php echo app::$config['db']['host'] ?>">
                        </div>
                        <div class="placeholder">
                            <label class="label" for="dbname"><?php echo t('DBname', FALSE); ?></label><input  name="config[db][dbname]" type="text" value="<?php echo app::$config['db']['dbname'] ?>">
                        </div>
                        <div class="placeholder">
                            <label class="label" for="user"><?php echo t('User', FALSE); ?></label><input  name="config[db][user]" type="text" value="<?php echo app::$config['db']['user'] ?>">
                        </div>
                        <div class="placeholder">
                            <label class="label" for="password"><?php echo t('Password', FALSE); ?></label><input  name="config[db][pass]" type="text" value="<?php echo app::$config['db']['pass'] ?>">
                        </div>
                    </div>
                <?php endif; ?>
                <div id="tabsb-2" class="admintabs">
                    <h2><?php echo t('Cache Management'); ?></h2>
                    <div class="placeholder">
                        <label class="label" for="maxage"><?php echo t('MaxAge', FALSE); ?></label><input name="config[cache][max-age]" type="text" value="<?php echo s(app::$config['cache']['max-age']); ?>">
                    </div>
                    <div class="placeholder">
                        <label class="label" for="cachecontrol"><?php echo t('CacheControl', FALSE); ?></label><input name="config[cache][cache-control]" type="text" value="<?php echo app::$config['cache']['cache-control']; ?>">
                    </div>
                    <div class="placeholder">
                        <label class="label" for="authdExt"><?php echo t('Authorized Extensions', FALSE); ?></label><input name="config[extensions_auth]" type="text" value="<?php echo app::$config['extensions_auth']; ?>">
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
                        <select  name="config[localization][timezone]" id="timezone">  <?php
                            $timezone_identifiers = DateTimeZone::listIdentifiers();
                            $continent = '';
                            foreach ($timezone_identifiers as $value) {
                                if (preg_match('/^(America|Antartica|Arctic|Asia|Atlantic|Europe|Indian|Pacific)\//', $value)) {
                                    $ex = explode('/', $value, 2); //obtain continent,city
                                    if ($continent != $ex[0]) {
                                        if ($continent != "")
                                            echo '</optgroup>';
                                        echo '<optgroup label="' . $ex[0] . '">';
                                    }

                                    $city = $ex[1];
                                    $continent = $ex[0];
				    if(\app::$config['localization']['timezone'] == $value) $selected = ' selected="selected"';
				    else $selected = '';
                                    echo '<option value="' . $value . '"'.$selected.'>' . $city . '</option>';
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
                    <h2><?php echo t('Enable Module', FALSE); ?></h2>

                    <?php
                    $i = 0;
                    $tplright = '';
                    $tplleft = '';
                    $right = '<table style="float:left;margin-left: 80px;margin-right: 1px;">
                        <thead ><tr><th>'.t('Module', FALSE).'</th><th>'.t('State', FALSE).'</th></thead>';
                    $left = '<table style="float:left; margin: 0px 30px;border-left: solid #CCC 1px;padding: 0px 25px;">
                        <thead ><tr><th>'.t('Module', FALSE).'</th><th>'.t('State', FALSE).'</th></thead><tbody>';
                    foreach (glob(PROFILE_PATH . '*', GLOB_ONLYDIR) as $filename) {
                        $module = substr(strrchr($filename, '/'), 1);
                        if ($module != 'core' && $module != 'db' && $module != 'admin') {
                            $i++;
                            if (isset(\app::$activeModules[$module]))
                                $checked = 'checked="checked"';
                            else
                                $checked = '';
			    $value = '0';
			    if(is_file('modules/'.$module.'/module.php')){
				include_once('modules/'.$module.'/module.php');
				$name = $module.'\\'.$module;
				$rc = new ReflectionClass($name);
			    }
			    if($rc->hasMethod('onLoad')) $value = '1';
                            $input = ' 
                            <tr class="trover">
                                <td style="padding: 0px 6px;text-align: center;padding-left: 5px;line-height: 30px;font-size: 16px;text-transform: capitalize;letter-spacing: 2px;vertical-align: middle">' . $module . '</td>
                                <td><div class="scale" style="padding: 3px 6px;line-height: 30px;text-align: center">
                                <input type="hidden" name="config[activeModules][' . $module . ']" value="removeThis">
                                <input type="checkbox" name="config[activeModules][' . $module . ']" class="display" value="' . $value . '" ' . $checked . '></div>
                            </td> ';
                            if ($i % 2 == 0)
                                $tplleft .= $input; else
                                $tplright .= $input/* .'</div>' */;
                        }
                    }
                    echo $right . $tplright . '</tbody></table>';
                    echo $left . $tplleft . '</tbody></table>';
                    ?>
                </div>
            </div>
	    <div id="tabsb-6" class="admintabs">
                <h2><?php echo t('Security', FALSE); ?></h2>
                <div class="placeholder">
                    <label class="label"><?php echo t('Allowed IP for Admin', FALSE); ?></label><input name="config[security][allowedipadmin]" type="text" value="<?php echo app::$config['security']['allowedipadmin'] ?>">
                </div>
            </div>
	    <div id="tabsb-7" class="admintabs">
                <h2><?php echo t('Development', FALSE); ?></h2>
                <div class="placeholder">
                    <label class="label"><?php echo t('Status', FALSE); ?></label>
		    <select name="config[dev][status]">
			<option value="dev"><?php echo t('Development', FALSE); ?></option>
			<option value="prod"<?php if(app::$config['dev']['status']=='prod') echo ' selected="selected"'; ?>><?php echo t('Production', FALSE); ?></option>
		    </select>
                </div>
		<div class="placeholder">
                    <label class="label"><?php echo t('Serialization', FALSE); ?></label>
		    <select name="config[dev][serialization]">
			<option value="obj"><?php echo t('Object', FALSE); ?></option>
			<option value="json"<?php if(app::$config['dev']['serialization']=='json') echo ' selected="selected"'; ?>><?php echo t('Json', FALSE); ?></option>
		    </select>
                </div>
            </div>
             <div id="tabsb-8" class="admintabs">
                <h2><?php echo t('Mailing'); ?> </h2>
                <div class="placeholder">
                    <label class="label" for="config[mail][type]"><?php echo t('Send Type', FALSE); ?></label>
                    <select name="config[mail][type]" onclick="if(this.value=='smtp'){ $('#confsmtp').show()}else{$('#confsmtp').hide()}">
			<option value="default"><?php echo t('Default', FALSE); ?></option>
			<option value="sendmail"<?php if(app::$config['mail']['type']=='sendmail') echo ' selected="selected"'; ?>>Sendmail</option>
                        <option value="qmail"<?php if(app::$config['mail']['type']=='qmail') echo ' selected="selected"'; ?>>Qmail</option>
                        <option value="smtp"<?php if(app::$config['mail']['type']=='smtp') echo ' selected="selected"'; ?>>SMTP</option>
		    </select>
                </div>
                <div id="confsmtp"<?php if(app::$config['mail']['type'] != 'smtp') echo ' style="display:none"'; ?>>
                    <div class="placeholder">
                        <label class="label" for="server"><?php echo t('Server', FALSE); ?></label><input  name="config[mail][server]" type="text" value="<?php echo app::$config['mail']['server'] ?>">
                    </div>
                    <div class="placeholder">
                        <label class="label" for="port"><?php echo t('Port', FALSE); ?></label><input  name="config[mail][port]" type="text" value="<?php echo app::$config['mail']['port'] ?>">
                    </div>
                </div>
            </div>
            <?php if (PROFILE == 'www'): ?>
                <input type="hidden" name="file" value="config.php">
            <?php else: ?>
                <input type="hidden" name="file" value="<?php echo PROFILE_PATH; ?>config.php">
            <?php endif; ?>
            <input class="none" id="save_configs" type="submit">
        </form>
    </div>
</div>