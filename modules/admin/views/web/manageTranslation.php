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
 * @authors Julien Gras et Benoît Lorillot
 * @copyright  Julien Gras et Benoît Lorillot
 * @version  Release: 1.0
 * @category  Parsimony
 * @package admin
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if(isset($_COOKIE['locale'])) $locale = $_COOKIE['locale'];
else $locale = \app::$config['localization']['default_language'];
$path = 'modules/'.MODULE.'/locale/'.$locale.'.php';
if(file_exists($path)) include($path);
?>
<div style="width:500px;height:150px">
    <div style="padding:10px;">
	<form method="POST" id="form_confs" target="ajaxhack" action="" style="height: 100%;">
	    <input type="hidden" name="action" value="save_configs" />
	    <input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />
	    <input type="hidden" name="action" value="saveTranslation" />
	    <input type="hidden" name="key" value="<?php echo s($_POST['key']); ?>" />
	    <div><h2>Traduction to <?php echo \app::$config['locales'][$locale]; ?></h2></div><br><br>
	    <?php echo s($_POST['key']); ?> : <input type="text" name="val" value="<?php if(isset($lang[$_POST['key']])) echo s($lang[$_POST['key']]); else echo s($_POST['key']);?>" required="required"><br><br>
	    <input type="submit" value="Save">
	</form>
    </div>
</div>