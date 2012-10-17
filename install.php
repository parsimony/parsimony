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
 * @package core
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

$lang = array();
$lang['fr']['Step'] = 'Etape';
$lang['fr']['You have to accept license agreement to continue'] = 'Veuillez accepter la licence pour continuer l\'installation';
$lang['fr']['Default Language'] = 'Langue par défaut';
$lang['fr']['License agreement'] = 'Contrat de licence';
$lang['fr']['I accept the terms of the license agreement'] = 'J\'approuve les termes et conditions du contrat ci-dessus';
$lang['fr']['Server Settings'] = 'Configuration du serveur';
$lang['fr']['Write permission of files and directories'] = 'Droit en écriture des fichiers et dossiers';
$lang['fr']['PHP Extensions'] = 'Extensions PHP';
$lang['fr']['PHP.ini Settings'] = 'Configuration PHP.ini';
$lang['fr']['TimeZone'] = 'Fuseaux Horaires (TimeZone)';
$lang['fr']['Admin E-mail'] = 'E-mail de l\'administrateur';
$lang['fr']['Invalid E-mail'] = 'Adresse E-mail invalide';
$lang['fr']['The PHP "mail()" function is used by default'] = 'La fonction PHP "mail()" est utilisée par défaut';
$lang['fr']['The administrator E-mail address is invalid'] = 'Veuillez entrer un E-mail administrateur valide';
$lang['fr']['Your server configuration is invalid. Please fix the issues to continue'] = 'Votre configuration serveur n\'est pas correcte. Merci de corriger ces problèmes pour continuer';
$lang['fr']['Database Settings'] = 'Configuration base de données';
$lang['fr']['Account Settings'] = 'Configuration compte';
$lang['fr']['Server'] = 'Serveur';
$lang['fr']['Database'] = 'Base de données';
$lang['fr']['Server'] = 'Serveur';
$lang['fr']['Port server'] = 'Port du serveur';
$lang['fr']['Database Name'] = 'Nom de la base de données';
$lang['fr']['User Name'] = 'Nom d\'utilisateur';
$lang['fr']['Tables Prefix'] = 'Préfixe des tables';
$lang['fr']['Finish'] = 'Fini';
$lang['fr']['Login'] = 'Identifiant';
$lang['fr']['Next Step'] = 'Etape suivante';
$lang['fr']['Prev Step'] = 'Retour';
$lang['fr']['Congratulations, Parsimony is now ready'] = 'Félicitation, Parsimony a été installé avec succès';
$lang['fr']['Let\'s Go !'] = 'C\'est Parti !';
$lang['fr']['Your database connection settings are not valid'] = 'Les données de la connection à la base de données sont incorrectes';
$lang['fr']['Site Name'] = 'Nom du Site Principal';
$lang['fr']['Admin Account'] = 'Compte administrateur';
$lang['fr']['Password confirmation is different'] = 'La confirmation du mot de passe n\'est pas correcte';
$lang['fr']['Please choose a login'] = 'Veuillez choisir un identifiant';
$lang['fr']['Please give a name to your site'] = 'Veuillez donner un nom à votre site';
$lang['fr']['Install and enable the'] = 'Installez et activez l\'extenssion';
$lang['fr']['extension enabled'] = 'est activé';
$lang['fr']['Permissions are Ok for'] = 'Les droits conviennent pour';
$lang['fr']['Set write permissions on'] = 'Mettre les permissions d\'écriture pour';
$lang['fr']['Set write permissions on'] = 'Mettre les permissions d\'écriture pour';
$lang['fr']['Set read/write permissions on'] = 'Mettre les permissions de lecture/écriture pour';
$lang['fr']['Set read permissions on'] = 'Mettre les permissions de lecture pour';
$lang['fr']['directory (and sub-directories) using an FTP client'] = '( et ces sous-dossiers) via un client FTP';
$lang['fr']['You are running PHP v '] = 'Vous êtes sous PHP v ';
$lang['fr']['is off'] = 'est désactivé';
$lang['fr']['is set'] = 'est défini';
$lang['fr']['Set the <span>date.timezone</span> setting in php.ini (like Europe/London).'] = 'Définir  la configuration <span>date.timezone</span> dans php.ini (ex Europe/London)';
$lang['fr']['Set'] = 'Définir';
$lang['fr'][' to <span>off</span> in php.ini.'] = ' à <span>off</span> dans php.ini.';
$lang['fr']['Apache does not have <span>Mod_Rewrite</span>.'] = 'Le module d\'Apache  <span>Mod_Rewrite</span> n\'est pas activé';
$lang['fr']['Apache has mod_rewrite.'] = 'Le module d\'Apache  <span>Mod_Rewrite</span> est activé';
$lang['fr'][', but Parsimony needs at least PHP "5.3.0" to run.'] = ', mais Parsimony a besoin au minimum de PHP "5.3.0" pour s\'executer';
$lang['fr']['Enable Multi-Site'] = 'Activer le Multi-Site';
$lang['fr']['Only if you want to manage several subdomains'] = 'Seulement si vous souhaitez gérer plusieurs sous-domaines';
$lang['fr']['Is your domain a Second Level domain'] = 'Le nom de domaine est t\'il de second niveau';
$lang['fr']['Yes'] = 'Oui';
$lang['fr']['No'] = 'Non';
$lang['fr']['What is it ?'] = 'Qu\'est ce que c\'est ?';
$lang['fr']['Synchronise with parsimony.mobi in order to access to downloadable modules'] = 'Synchroniser avec Parsimony.mobi pour accéder aux modules téléchargeables';
$lang['fr']['at least 6 characters alphanumeric'] = 'au minimum 6 caractères alphanumériques';
$lang['fr']['at least 8 characters alphanumeric'] = 'au minimum 8 caractères alphanumériques';
$lang['fr']['Login must contains at least 6 characters alphanumeric'] = 'L\' identifiant doit contenir au minimum 6 caractères alphanumériques';
$lang['fr']['Password must contains at least 6 characters alphanumeric'] = 'Le mot de passe doit contenir au minimum 8 caractères alphanumériques';
$lang['fr']['http://en.wikipedia.org/wiki/Second-level_domain'] = 'http://fr.wikipedia.org/wiki/Domaine_de_deuxième_niveau';
$lang['fr']['My WebSite'] = 'Mon site';
$lang['fr']['Password'] = 'Mot de passe';
$lang['fr']['Confirm Password'] = 'Confirmer le mot de passe';
$lang['fr']['Check Password'] = 'Vérifier le mot de passe';


set_include_path('.' . PATH_SEPARATOR . './www/modules/' . PATH_SEPARATOR . './modules/' . PATH_SEPARATOR . './modules/core/'); // set include path
define('BASE_PATH',str_replace('\\','/',str_replace('//','/',dirname($_SERVER['PHP_SELF']).'/')));

if (isset($_POST['step']))
    $step = $_POST['step'];
elseif (isset($_GET['step']))
    $step = $_GET['step'];
elseif (!isset($_GET['step']) || !is_numeric($_GET['step']) || $_GET['step'] < 1 || $_GET['step'] > 4)
    $step = 1;

function se($text) {
    return htmlentities($text, ENT_QUOTES | ENT_IGNORE, 'utf-8');
}

function tr($text) {
    global $lang;
    if(isset($_COOKIE['lang']) && $_COOKIE['lang']=='fr_FR' && isset($lang['fr'][$text])) return $lang['fr'][$text];
    else return $text;
}

function displayNotif($ok, $high, $low) {
    $serverOK = TRUE;
    if (!empty($ok)) {
        foreach ($ok as $messOk) {
            echo '<div class="notify positive">' . $messOk . '</div>';
        }
    }
    if (!empty($high)) {
        foreach ($high as $problem) {
            $serverOK = FALSE;
            echo '<div class="notify negative">' . $problem . '</div>';
        }
    }
    if (!empty($low)) {
        foreach ($low as $info) {
            echo '<div class="notify normal">' . $info . '</div>';
        }
    }
    return $serverOK;
}

ob_start();
while (1) {
    switch ($step) {

        case 'validstep1':
            if (isset($_POST['agreewithlicence']))
                $step = 2;
            else {
                echo '<div class="notify negative">' . tr('You have to accept license agreement to continue') . '</div>';
                $step = 1;
            }
            break;

        case 1:
            ?>
            <h1><?php echo tr('Step'); ?> 1</h1>
            <div>
                <h2><?php echo tr('Default Language'); ?></h2>
                <select name="lang" onchange="document.cookie = 'lang=' + this.value;window.location.reload()">
                    <option value="en_EN">English</option>
                    <option value="fr_FR"<?php  if(isset($_COOKIE['lang']) && $_COOKIE['lang']=='fr_FR') echo ' selected="selected"'; ?>>Français</option>
                </select>
                <h2><?php echo tr('License agreement'); ?> : Open Software License v. 3.0</h2>
                <div style="overflow-y: scroll;height:250px;border:#ccc solid 1px;">
                    <div class="content clear-block">
                        <h1>Open Software License v. 3.0 (OSL-3.0)</h1>

                        <p>This Open Software License (the "License") applies to any original work of authorship (the "Original Work") whose owner (the "Licensor") has placed the following licensing notice adjacent to the copyright notice for the Original Work:</p>

                        <p class="c1">Licensed under the Open Software License version 3.0</p>

                        <p>1) <b>Grant of Copyright License.</b> Licensor grants You a worldwide, royalty-free, non-exclusive, sublicensable license, for the duration of the copyright, to do the following:</p>

                        <p>a) to reproduce the Original Work in copies, either alone or as part of a collective work;</p>

                        <p>b) to translate, adapt, alter, transform, modify, or arrange the Original Work, thereby creating derivative works ("Derivative Works") based upon the Original Work;</p>

                        <p>c) to distribute or communicate copies of the Original Work and Derivative Works to the public, <u>with the proviso that copies of Original Work or Derivative Works that You distribute or communicate shall be licensed under this Open Software License</u>;</p>

                        <p>d) to perform the Original Work publicly; and</p>

                        <p>e) to display the Original Work publicly.</p>

                        <p>2) <b>Grant</b> of Patent License. Licensor grants You a worldwide, royalty-free, non-exclusive, sublicensable license, under patent claims owned or controlled by the Licensor that are embodied in the Original Work as furnished by the Licensor, for the duration of the patents, to make, use, sell, offer for sale, have made, and import the Original Work and Derivative Works.</p>

                        <p>3) <b>Grant</b> of Source Code License. The term "Source Code" means the preferred form of the Original Work for making modifications to it and all available documentation describing how to modify the Original Work. Licensor agrees to provide a machine-readable copy of the Source Code of the Original Work along with each copy of the Original Work that Licensor distributes. Licensor reserves the right to satisfy this obligation by placing a machine-readable copy of the Source Code in an information repository reasonably calculated to permit inexpensive and convenient access by You for as long as Licensor continues to distribute the Original Work.</p>

                        <p>4) <b>Exclusions From License Grant.</b> Neither the names of Licensor, nor the names of any contributors to the Original Work, nor any of their trademarks or service marks, may be used to endorse or promote products derived from this Original Work without express prior permission of the Licensor. Except as expressly stated herein, nothing in this License grants any license to Licensor's trademarks, copyrights, patents, trade secrets or any other intellectual property. No patent license is granted to make, use, sell, offer for sale, have made, or import embodiments of any patent claims other than the licensed claims defined in Section 2. No license is granted to the trademarks of Licensor even if such marks are included in the Original Work. Nothing in this License shall be interpreted to prohibit Licensor from licensing under terms different from this License any Original Work that Licensor otherwise would have a right to license.</p>

                        <p>5) <b>External Deployment.</b> The term "External Deployment" means the use, distribution, or communication of the Original Work or Derivative Works in any way such that the Original Work or Derivative Works may be used by anyone other than You, whether those works are distributed or communicated to those persons or made available as an application intended for use over a network. As an express condition for the grants of license hereunder, You must treat any External Deployment by You of the Original Work or a Derivative Work as a distribution under section 1(c).</p>

                        <p>6) <b>Attribution Rights.</b> You must retain, in the Source Code of any Derivative Works that You create, all copyright, patent, or trademark notices from the Source Code of the Original Work, as well as any notices of licensing and any descriptive text identified therein as an "Attribution Notice." You must cause the Source Code for any Derivative Works that You create to carry a prominent Attribution Notice reasonably calculated to inform recipients that You have modified the Original Work.</p>

                        <p>7) <b>Warranty of Provenance and Disclaimer of Warranty.</b> Licensor warrants that the copyright in and to the Original Work and the patent rights granted herein by Licensor are owned by the Licensor or are sublicensed to You under the terms of this License with the permission of the contributor(s) of those copyrights and patent rights. Except as expressly stated in the immediately preceding sentence, the Original Work is provided under this License on an "AS IS" BASIS and WITHOUT WARRANTY, either express or implied, including, without limitation, the warranties of non-infringement, merchantability or fitness for a particular purpose. THE ENTIRE RISK AS TO THE QUALITY OF THE ORIGINAL WORK IS WITH YOU. This DISCLAIMER OF WARRANTY constitutes an essential part of this License. No license to the Original Work is granted by this License except under this disclaimer.</p>

                        <p>8) <b>Limitation of Liability.</b> Under no circumstances and under no legal theory, whether in tort (including negligence), contract, or otherwise, shall the Licensor be liable to anyone for any indirect, special, incidental, or consequential damages of any character arising as a result of this License or the use of the Original Work including, without limitation, damages for loss of goodwill, work stoppage, computer failure or malfunction, or any and all other commercial damages or losses. This limitation of liability shall not apply to the extent applicable law prohibits such limitation.</p>

                        <p>9) <b>Acceptance and Termination.</b> If, at any time, You expressly assented to this License, that assent indicates your clear and irrevocable acceptance of this License and all of its terms and conditions. If You distribute or communicate copies of the Original Work or a Derivative Work, You must make a reasonable effort under the circumstances to obtain the express assent of recipients to the terms of this License. This License conditions your rights to undertake the activities listed in Section 1, including your right to create Derivative Works based upon the Original Work, and doing so without honoring these terms and conditions is prohibited by copyright law and international treaty. Nothing in this License is intended to affect copyright exceptions and limitations (including "fair use" or "fair dealing"). This License shall terminate immediately and You may no longer exercise any of the rights granted to You by this License upon your failure to honor the conditions in Section 1(c).</p>

                        <p>10) <b>Termination for Patent Action.</b> This License shall terminate automatically and You may no longer exercise any of the rights granted to You by this License as of the date You commence an action, including a cross-claim or counterclaim, against&nbsp;Licensor or any licensee alleging that the Original Work infringes a patent. This termination provision shall not apply for an action alleging patent infringement by combinations of the Original Work with other software or hardware.</p>

                        <p>11) <b>Jurisdiction, Venue and Governing Law.</b> Any action or suit relating to this License may be brought only in the courts of a jurisdiction wherein the Licensor resides or in which Licensor conducts its primary business, and under the laws of that jurisdiction excluding its conflict-of-law provisions. The application of the United Nations Convention on Contracts for the International Sale of Goods is expressly excluded. Any use of the Original Work outside the scope of this License or after its termination shall be subject to the requirements and penalties of copyright or patent law in the appropriate jurisdiction. This section shall survive the termination of this License.</p>

                        <p>12) <b>Attorneys' Fees.</b> In any action to enforce the terms of this License or seeking damages relating thereto, the prevailing party shall be entitled to recover its costs and expenses, including, without limitation, reasonable attorneys' fees and costs incurred in connection with such action, including any appeal of such action. This section shall survive the termination of this License.</p>

                        <p>13) <b>Miscellaneous.</b> If any provision of this License is held to be unenforceable, such provision shall be reformed only to the extent necessary to make it enforceable.</p>

                        <p>14) <b>Definition of "You" in This License.</b> "You" throughout this License, whether in upper or lower case, means an individual or a legal entity exercising rights under, and complying with all of the terms of, this License. For legal entities, "You" includes any entity that controls, is controlled by, or is under common control with you. For purposes of this definition, "control" means (i) the power, direct or indirect, to cause the direction or management of such entity, whether by contract or otherwise, or (ii) ownership of fifty percent (50%) or more of the outstanding shares, or (iii) beneficial ownership of such entity.</p>

                        <p>15) <b>Right to Use.</b> You may use the Original Work in all ways not otherwise restricted or conditioned by this License or by law, and Licensor promises not to interfere with or be responsible for such uses by You.</p>

                        <p>16) <b class="c2">Modification of This License.</b> This License is Copyright © 2005 Lawrence Rosen. Permission is granted to copy, distribute, or communicate this License without modification. Nothing in this License permits You to modify this License as applied to the Original Work or to Derivative Works. However, You may modify the text of this License and copy, distribute or communicate your modified version (the "Modified License") and apply it to other original works of authorship subject to the following conditions: (i) You may not indicate in any way that your Modified License is the "Open Software License" or "OSL" and you may not use those names in the name of your Modified License; (ii) You must replace the notice specified in the first paragraph above with the notice "Licensed under &lt;insert your license name here&gt;" or with a notice of your own that is not confusingly similar to the notice in this License; and (iii) You may not claim that your original works are open source software unless your Modified License has been approved by Open Source Initiative (OSI) and You comply with its license review and certification process.</p>

                        <p><a href="http://www.rosenlaw.com/OSL3.0-explained.pdf">A brief explanation of this license is available</a>.</p>
                    </div>
                </div>
                <input type="hidden" name="step" value="validstep1" />
                <input type="checkbox" name="agreewithlicence"><?php echo tr('I accept the terms of the license agreement'); ?>.

            </div>
            <?php
            break 2;

        case 'validstep2':
            $step = 3;
	    
            
            if (!isset($_POST['serverok']) || empty($_POST['serverok']) || $_POST['serverok'] != 1) {
                echo '<div class="notify negative">' . tr('Your server configuration is invalid. Please fix the issues to continue') . '</div>';
                $step = 2;
            }
            if (!isset($_POST['mailadmin']) || empty($_POST['mailadmin']) || !filter_var($_POST['mailadmin'], FILTER_VALIDATE_EMAIL)) {
                echo '<div class="notify negative">' . tr('The administrator E-mail address is invalid') . '.</div>';
                $step = 2;
            }

            include('modules/core/classes/config.php');
            include('modules/core/classes/tools.php');
            $configObj = new \core\classes\config('config.php', TRUE);
            $lang = 'en_EN';
            if(isset($_COOKIE['lang'])) $lang = $_COOKIE['lang'];
            $update = array('localization' => array('timezone' => $_POST['timezone'],'default_language' => $lang),
		'mail' => array('adminMail' => $_POST['mailadmin']),
		'security' => array('salt' => substr(hash('sha1', uniqid(mt_rand())), 0, 8)),
		'domain' => array('sld' => $_POST['sld'],
		    'multisite' => $_POST['multisite']));
            $configObj->saveConfig($update);

            break;

        case 2:
            ?>
            <h1><?php echo tr('Step'); ?> 2 : <?php echo tr('Server Settings'); ?></h1>

            <h2><?php echo tr('Write permission of files and directories'); ?></h2>
            <?php
            $serverOK = TRUE;
            $high = array();
            $low = array();
            $ok = array();

            if (!is_readable('index.php') || !is_readable('config.php') || !is_readable('install.php') || !is_writable('index.php') || !is_writable('config.php') || !is_writable('install.php')) {
                $high[] = tr('Set read/write permissions on').' <span>"index.php, config.php, install.php"</span> '.tr('directory (and sub-directories) using an FTP client');
            } else {
                $ok[] = tr('Permissions are Ok for').' <span>"index.php, config.php, install.php"</span>';
            }
            
            if (!is_readable('lib/') || !is_readable('lib/cms.css') ){
                $high[] = tr('Set read permissions on').' <span>"lib/"</span> '.tr('directory (and sub-directories) using an FTP client');
            } else {
                $ok[] = tr('Permissions are Ok for').'  <span>"lib/"</span>';
            }
            
            if (!is_readable('modules/') || !is_readable('modules/core/') || !is_readable('modules/core/model/') || !is_writable('modules/') || !is_writable('modules/core/') || !is_writable('modules/core/model/') || !is_writable('modules/core/model/post.php') ){
                $high[] = tr('Set read/write permissions on').' <span>"modules/"</span> '.tr('directory (and sub-directories) using an FTP client');
            } else {
                $ok[] = tr('Permissions are Ok for').'  <span>"modules/"</span>';
            }
            
            if (!is_readable('profiles/') || !is_writable('profiles/')) {
                $high[] = tr('Set read/write permissions on').' <span>"profiles/"</span> '.tr('directory (and sub-directories) using an FTP client');
            } else {
                $ok[] = tr('Permissions are Ok for').'  <span>"profiles/"</span>';
            }

            if(!displayNotif($ok, $high, $low) && $serverOK ){
		$serverOK = FALSE;
	    }
            ?>
            <h2><?php echo tr('PHP Extensions'); ?></h2>
            <?php
            $high = array();
            $low = array();
            $ok = array();

            if (function_exists('apache_get_modules') && !in_array('mod_rewrite', apache_get_modules())) {
                $high[] = tr('Apache does not have <span>Mod_Rewrite</span>.');
            } elseif (function_exists('apache_get_modules')) {
                $ok[] = tr('Apache has mod_rewrite.');
            }

            if (version_compare(PHP_VERSION, '5.3.2') < 0) {
                $high[] = tr('You are running PHP v ') . PHP_VERSION . tr(', but Parsimony needs at least PHP "5.3.2" to run.');
            } else {
                $ok[] = tr('You are running PHP v ') . PHP_VERSION . '.';
            }

            if (!extension_loaded('pdo_mysql')) {
                $high[] = tr('Install and enable the').' <span>pdo_mysql</span> extension.';
            } else {
                $ok[] = '<span>pdo_mysql</span> '.tr('extension enabled');
            }

            if (!extension_loaded('mbstring')) {
                $high[] = tr('Install and enable the').' <span>mbstring</span> extension.';
            } else {
                $ok[] = '<span>mbstring</span> '.tr('extension enabled');
            }

            if (!extension_loaded('json')) {
                $high[] = tr('Install and enable the').' <span>json</span> extension.';
            } else {
                $ok[] = '<span>json</span> '.tr('extension enabled');
            }

            if (!extension_loaded('gd')) {
                $high[] = tr('Install and enable the').' <span>gd</span> extension.';
            } else {
                $ok[] = '<span>gd</span> '.tr('extension enabled');
            }

            if(!displayNotif($ok, $high, $low) && $serverOK ){
		$serverOK = FALSE;
	    }
            ?>
            <h2><?php echo tr('PHP.ini Stettings'); ?></h2>
            <?php
            $high = array();
            $low = array();
            $ok = array();
            if (!ini_get('date.timezone')) {
                $low[] = tr('Set the <span>date.timezone</span> setting in php.ini (like Europe/London).');
            } else {
                $ok[] = '<span>date.timezone</span> '.tr('is set');
            }

            if (get_magic_quotes_gpc()) {
                $low[] = tr('Set').' <span>magic_quotes_gpc</span> '.tr(' to <span>off</span> in php.ini.');
            } else {
                $ok[] = '<span>magic_quotes_gpc</span> '.tr('is off');
            }

            if (ini_get('register_globals')) {
                $low[] = tr('Set').' <span>register_globals</span> '.tr(' to <span>off</span> in php.ini.');
            } else {
                $ok[] = '<span>register_globals</span> '.tr('is off');
            }

            if (ini_get('session.auto_start')) {
                $low[] = tr('Set').' <span>session.auto_start</span> '.tr(' to <span>off</span> in php.ini.');
            } else {
                $ok[] = '<span>session.auto_start</span> '.tr('is off');
            }

            if(!displayNotif($ok, $high, $low) && $serverOK ){
		$serverOK = FALSE;
	    }
            ?>
            <input type="hidden" name="serverok" value="<?php echo (string) $serverOK ?>">
            <br><br>
            <label><?php echo tr('TimeZone'); ?></label>
            <select name="timezone" id="timezone">
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
                        if ((date_default_timezone_get() && date_default_timezone_get() == $value) || (isset($_POST['timezone']) && $_POST['timezone'] == $value))
                            $selected = ' selected="selected"';
                        else
                            $selected = '';
                        echo '<option value="' . $value . '"' . $selected . '>' . $city . '</option>';
                    }
                }
                ?>
            </optgroup>
            </select><br><br>
            <div>
                <label><?php echo tr('Admin E-mail'); ?></label>
                <input type="text" name="mailadmin" value="<?php if (isset($_POST['mailadmin'])) echo se($_POST['mailadmin']); ?>"><br>
                <?php tr('The PHP "mail()" function is used by default'); ?>
            </div><br>
	    <div>
                <label><?php echo tr('Enable Multi-Site'); ?> ?</label>
		<select name="multisite">
		    <option value="0"><?php echo tr('No'); ?></option>
		    <option value="1" <?php if(isset($_POST['multisite']) && $_POST['multisite']=='1') echo ' selected="selected"'; ?>><?php echo tr('Yes'); ?></option>
		</select><br>
                <?php echo tr('Only if you want to manage several subdomains'); ?>(ex: en.mysite.com,fr.mysite.com)
            </div><br>
	    <div>
                <label class="nocapital"><?php echo tr('Is your domain a Second Level domain'); ?>(ex : mysite<u>.co.uk</u>)</span></label>
                <select name="sld">
		    <option value="2"><?php echo tr('No'); ?></option>
		    <option value="3" <?php if(isset($_POST['sld']) && $_POST['sld']=='1') echo ' selected="selected"'; ?>><?php echo tr('Yes'); ?></option>
		</select><br>
                <a href="<?php echo tr('http://en.wikipedia.org/wiki/Second-level_domain'); ?>" target="_blank" style="color:#444"><?php echo tr('What is it ?'); ?></a>
            </div>
            <input type="hidden" name="step" value="validstep2" />
            <?php
            break 2;

        case 'validstep3':
            $connect = FALSE;
            if (isset($_POST['db_server']) && !empty($_POST['db_server']) &&
                    isset($_POST['db_name']) && !empty($_POST['db_name']) &&
                    isset($_POST['db_port']) && !empty($_POST['db_port']) && is_numeric($_POST['db_port']) &&
                    isset($_POST['db_user']) && !empty($_POST['db_user']) &&
                    isset($_POST['db_pass'])) {
                $connect = TRUE;
                try {
                    $dbh = new pdo('mysql:host=' . $_POST['db_server'] . ';port=' . $_POST['db_port'] . ';dbname=' . $_POST['db_name'],
                                    $_POST['db_user'],
                                    $_POST['db_pass'],
                                    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
                } catch (PDOException $ex) {
                    $connect = FALSE;
                }
            }
            if ($connect) {
                $step = 4;
            } else {
                echo '<div class="notify negative">' . tr('Your database connection settings are not valid') . '</div>';
                $step = 3;
            }
	    
	    include('modules/core/classes/config.php');
            include('modules/core/classes/tools.php');
            $configObj = new \core\classes\config('config.php', TRUE);
            $update = array('BASE_PATH' => BASE_PATH ,'db' => array('host' => $_POST['db_server'], 'dbname' => $_POST['db_name'], 'user' => $_POST['db_user'], 'pass' => $_POST['db_pass']));
            $configObj->saveConfig($update);

            break;

        case 3:
            ?>
            <h1><?php echo tr('Step'); ?> 3</h1>
            <h2><?php echo 'MySql '.tr('Database Settings'); ?></h2>
            <div>
                <label><?php echo tr('Server'); ?> *</label>
                <input type="text" name="db_server" value="localhost" required>
            </div>
            <div>
                <label><?php echo tr('Port server'); ?> *</label>
                <input type="text" name="db_port" value="3306" required>
            </div>
            <div>
                <label><?php echo tr('Database Name'); ?> *</label>
                <input type="text" name="db_name" placeholder="ex : parsimony" value="<?php if (isset($_POST['db_name'])) echo se($_POST['db_name']); ?>"  required>
            </div>
            <div>
                <label><?php echo tr('User Name'); ?> *</label>
                <input type="text" name="db_user" placeholder="ex : root" value="<?php if (isset($_POST['db_user'])) echo se($_POST['db_user']); ?>" required>
            </div>
            <div>
                <label><?php echo tr('Password'); ?></label>
                <input type="text" name="db_pass" value="<?php if (isset($_POST['db_pass'])) echo se($_POST['db_pass']); ?>">
            </div>
			<?php /*
            <div>
                <label><?php echo tr('Tables Prefix'); ?></label>
                <input type="text" name="db_prefix" value="<?php if (isset($_POST['db_prefix'])) echo se($_POST['db_prefix']); ?>" placeholder="ex : parsi_">
            </div>
			*/ ?>
            <input type="hidden" name="step" value="validstep3" />
            <?php
            break 2;

        case 'validstep4':
            $ok = TRUE;
            if (!isset($_POST['name']) || empty($_POST['name'])) {
                $ok = FALSE;
                echo '<div class="notify negative">' . tr('Please give a name to your site') . '.</div>';
            }
            if (!isset($_POST['identifiant']) || empty($_POST['identifiant'])) {
                $ok = FALSE;
                echo '<div class="notify negative">' . tr('Please choose a login') . '</div>';
            }
            if (!isset($_POST['mail']) || empty($_POST['mail']) || !filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) {
                $ok = FALSE;
                echo '<div class="notify negative">' . tr('Invalid E-mail') . '.</div>';
            }
            
            if (isset($_POST['identifiant']) && strlen($_POST['identifiant']) < 6) {
                $ok = FALSE;
                echo '<div class="notify negative">'.tr('Login must contains at least 6 characters alphanumeric').'.</div>';
            }
            
            
            if (isset($_POST['pass1']) && strlen($_POST['pass1']) < 8) {
                $ok = FALSE;
                echo '<div class="notify negative">'.tr('Password must contains at least 8 characters alphanumeric').'.</div>';
            }
	    
            if ($ok) {
                $step = 5;
            } else {
                $step = 4;
            }
            break;
        case 4:
	    include('config.php');
            ?>
            <h1><?php echo tr('Step'); ?> 4</h1>
            <h2><?php echo tr('Account Settings'); ?></h2>
            <div>
                <label><?php echo tr('Site Name'); ?> * </label>
                <input type="text" name="name" value="<?php echo tr('My WebSite'); ?>" required>
            </div>

            <h2><?php tr('Admin Account Settings'); ?></h2>
            <div>
                <label><?php echo tr('Login'); ?> * <small>(<?php echo tr('at least 6 characters alphanumeric'); ?>)</small> </label>
                <input type="text" name="identifiant" value="<?php if (isset($_POST['identifiant'])) echo se($_POST['identifiant']); ?>" required>
            </div>
            <div>
                <label>E-mail *</label>
                <input type="text" name="mail" value="<?php if (isset($_POST['mail'])) echo se($_POST['mail']); else echo $config['mail']['adminMail']; ?>" required>
            </div>
            <div>
                <label><?php echo tr('Password'); ?> * <small>(<?php echo tr('at least 8 characters alphanumeric'); ?>)</small></label>
                <input type="password" name="pass1" id="pass1" required>
            </div>
            <div>
                <input type="checkbox" onclick="if(this.checked) document.getElementById('pass1').type = 'text'; else document.getElementById('pass1').type = 'password';"><?php echo tr('Check Password'); ?>
            </div>
	    <div><br>
		
	    </div>
            <input type="hidden" name="step" value="validstep4" />
            <?php
            break 2;
        case 5:
	    
	    include('modules/core/classes/app.php');
	    class_alias('core\classes\app','app');
	    include('config.php');
	    app::$config = $config;
		 $config['aliasClasses'] = array('app' => 'core\classes\app',
	    'request' => 'core\classes\request',
	    'response' => 'core\classes\response',
	    'block' => 'core\classes\block',
	    'tools' => 'core\classes\tools',
	    'view' => 'core\classes\view',
	    'module' => 'core\classes\module',
	    'PDOconnection' => 'core\classes\PDOconnection',
	    'config' => 'core\classes\config',
	    'entity' => 'core\classes\entity',
	    'theme' => 'core\classes\theme',
	    'page' => 'core\classes\page',
	    'css' => 'core\classes\css',
	    'user' => 'core\classes\user',
	    'pagination' => 'core\classes\pagination',
	    'img' => 'core\classes\img',
	    'field' => 'core\classes\field',
	    'field_ident' => 'core\fields\field_ident',
	    'field_string' => 'core\fields\field_string',
	    'field_numeric' => 'core\fields\field_numeric',
	    'field_decimal' => 'core\fields\field_decimal',
	    'field_price' => 'core\fields\field_price',
	    'field_percent' => 'core\fields\field_percent',
	    'field_mail' => 'core\fields\field_mail',
	    'field_password' => 'core\fields\field_password',
	    'field_state' => 'core\fields\field_state',
	    'field_date' => 'core\fields\field_date',
	    'field_publication' => 'core\fields\field_publication',
	    'field_image' => 'core\fields\field_image',
	    'field_flash' => 'core\fields\field_flash',
	    'field_url' => 'core\fields\field_url',
	    'field_url_rewriting' => 'core\fields\field_url_rewriting',
	    'field_wysiwyg' => 'core\fields\field_wysiwyg',
	    'field_textarea' => 'core\fields\field_textarea',
	    'field_user' => 'core\fields\field_user',
	    'field_ip' => 'core\fields\field_ip',
	    'field_vote' => 'core\fields\field_vote',
	    'field_foreignkey' => 'core\fields\field_foreignkey',
	    'field_formasso' => 'core\fields\field_formasso'
	);
	    app::$aliasClasses = $config['aliasClasses'];
            app::$activeModules = $config['activeModules'];
            define('PROFILE_PATH','profiles/www/modules/');
	    $toInclude = array('config', 'entity', 'field', 'field_ident', 'field_string', 'field_numeric','field_decimal','field_price','field_percent','field_mail','field_password','field_state','field_date','field_publication','field_image','field_flash','field_url','field_url_rewriting','field_wysiwyg','field_textarea','field_user','field_ip','field_vote','field_foreignkey','field_formasso','PDOconnection', 'tools', 'module');
	    
	    foreach($config['aliasClasses'] AS $alias => $class){
		if(in_array($alias,$toInclude)){
		    include('modules/'.  str_replace('\\', '/', $class).'.php');
		    class_alias($class,$alias);
		}
	    }

	    //create SQL tables
            include('modules/core/module.php');
            $core = new \core\core('core');
            
            $core->install();
	    echo '<div style="display:none">';
            $core->getEntity('user')->insertInto(array('id_user' => '', 'pseudo' => $_POST['identifiant'],'mail' => $_POST['mail'],'pass' => $_POST['pass1'],'state' => '1','id_role' => '1'));
            $core->getEntity('tag')->insertInto(array('id_tag' => '1', 'name' => 'Article','url' => 'article'));
            $core->getEntity('tag_post')->insertInto(array('id_tag_post' => '1', 'id_tag' => '1','id_post' => '1'));
            $core->getEntity('category')->insertInto(array('id_category' => '1', 'name' => 'General','id_parent' => null,'url' => 'general','description' => ''));
            $core->getEntity('category_post')->insertInto(array('id_user' => '1', 'id_category' => '1','id_post' => '1'));
            $core->getEntity('post')->insertInto(array('id_post' => '1', 'title' => 'Hello World','url' => 'my-first-post','content' => '<p>Welcome to Parsimony. This is your first post. </p><p>Click on the edit button in the header toolbar to edit the text, modify or delete it.</p> <p>Start blogging by clicking in the left toolbar on Data button then Posts!</p>','excerpt' => '','publicationGMT' => gmdate('Y-m-d H:i:s', time()),'publicationGMT_visibility' => '0', 'publicationGMT_status' => '0', 'author' => '1','has_comment' => '1','ping_status' => '1'));
	    echo '</div>';
            $configObj = new \core\classes\config('config.php', TRUE);
            $update = array('sitename' => $_POST['name']);
            $configObj->saveConfig($update);
	    
	    //unlock site
	    $index = file('index.php');
	    if(trim($index[1]) == 'include(\'install.php\');exit;'){
		unset($index[1]);
		file_put_contents('index.php', implode('',$index));
	    }
	    
	    //lock install
	    $install = file('install.php');
	    $install[0] = '<?php exit; ';
	    file_put_contents('install.php',  implode('',$install));
	    
	    //synchronize with parsimony.mobi
	    if(isset($_POST['synchro'])){
		//do synchro
	    }
            ?>
            <h1 style="text-align: center;"><?php echo tr('Congratulations, Parsimony is now ready'); ?></h1>

            <?php
            break 2;

        default:
            break 2;
    }
}
$content = ob_get_clean();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Parsimony Install</title>
    </head>
    <body>
        <div id="container">
            <div id="title"><img style="float:left;font-family:  Lucida Grande, sans-serif" src="http://parsimony.mobi/core/files/parsimony.png"><h1>Installation</h1></div>
            <div id="content">
                <ul id="breadcrumbs">
                    <li><a href="#"<?php if ($step == 1) echo ' class="current"'; ?>><?php echo tr('License'); ?></a></li>
                    <li><a href="#"<?php if ($step == 2) echo ' class="current"'; ?>><?php echo tr('Server'); ?></a></li>
                    <li><a href="#"<?php if ($step == 3) echo ' class="current"'; ?>><?php echo tr('Database'); ?></a></li>
                    <li><a href="#"<?php if ($step == 4) echo ' class="current"'; ?>><?php echo tr('Admin Account'); ?></a></li>
                    <li><a href="#"<?php if ($step == 5) echo ' class="current"'; ?>><?php echo tr('Finish'); ?></a></li>
                </ul>
                <div>
                    <form method="post" id="form" class="form">
                        <?php echo $content; ?>
                        <div class="btns">
                            <?php if ($step != 5) : ?>
                                <?php if ($step != 1) : ?>
                                    <p class="containerNext">
                                        <a href="#" class="prev" onclick="window.history.back();return false;"><?php echo tr('Prev Step'); ?></a>
                                    </p>
                                <?php endif; ?>
                                <p class="containerNext">
                                    <a href="#" class="next" onclick="document.getElementById('form').submit();return false;"><?php echo tr('Next Step'); ?></a>
                                </p>
                            <?php else : ?>
                                <p class="containerNext">
                                    <a href="connect" class="next"><?php echo tr('Let\'s Go !'); ?></a>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both"></div>
                    </form>
                </div>
            </div>
        </div>
        <style>
            body{color:#484848;background: url(http://parsimony.mobi/admin/img/concrete_wall_3.png);font-family: HelveticaNeue, Helvetica, Arial, sans-serif;font-size: 13px;}

            #container{width:700px;margin:70px auto;}
            #container h1{font-family: sans-serif;font-size: 21px;text-align: right;text-shadow: -2px -2px 0px #303030;color: rgba(191, 230, 255, 0.25);font-weight: bold;}
            #container h2{font-family: sans-serif;font-size: 18px;text-shadow: -2px -2px 0px #303030;color: rgba(191, 230, 255, 0.25);font-weight: bold;}
            #content{padding:5px;background: #fff;border-radius: 9px;box-shadow: 3px 1px 9px #999;}
            #content p{padding:5px;}
            .containerNext{text-align: center;}

            .positive{background:#D7FFB8;border:solid #5C8011 1px;}
            .negative{background:#FFE5B5;border:solid #E0960B 1px;}
            .normal{background:#eee;border:solid #333 1px;}
            .notify{margin:5px 0;padding:4px;color:#444;border-radius:3px;}
            .notify span{font-weight: bold;text-transform: capitalize;}

            * {
                -moz-box-sizing: border-box;
                -webkit-box-sizing: border-box;
                box-sizing: border-box;
            }
            .form {
                padding:5px 30px;
                border-radius:5px;
            }
            .form label {
                text-transform: capitalize;
                padding: 8px 0;
                border-radius: 5px;
                min-width: 120px;
                position: relative;
                display: block;
                text-align: left;
                font-weight: bold;
                color: #666;
                text-shadow: 0px 1px 0px white;
                font-size: 16px;
            }
            .form input[type="text"],.form input[type="password"]{
                width: 340px;
                height: 30px;
                border-radius: 8px;
                border: solid 1px #888;
                border: none!important;
                border-style: none;
                box-shadow: 0 0px 2px rgba(0, 0, 0, .3),inset 0 1px 2px rgba(0, 0, 0, .2);
                padding: 5px 0;
                text-shadow: 0px 1px 0px white;
                outline: none;
                border: 1px solid #C1C1C1;
                color: #333;
                border-radius: 3px;
                background: #EFF0F0;
                background: -webkit-gradient(linear, left top, left bottom, from(#EDEFF0), to(white));
                background: -moz-linear-gradient(top, #EDEFF0, white);
                background: -o-linear-gradient(top, #EDEFF0, white);
                filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#edeff0', endColorstr='#ffffff');
                margin-right: 40px;
		padding-left: 5px;
            }
            input[type="button"], input[type="submit"] {
                border-radius: 2px;
                box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.1);
                -webkit-user-select: none;
                background: -webkit-linear-gradient(#FAFAFA, #F4F4F4 40%, #E5E5E5);
		background: -moz-linear-gradient(#FAFAFA, #F4F4F4 40%, #E5E5E5);
                border: 1px solid #AAA;
                color: #444;
                font-size: inherit;
                margin-bottom: 0px;
                min-width: 4em;
                padding: 3px 12px 3px 12px;
                margin: 20px 278px;
            }


            ul{
                margin: 0;
                padding: 0;
                list-style: none;
            }
            #breadcrumbs{
                background: #eee;
                border-width: 1px;
                border-style: solid;
                border-color: #f5f5f5 #e5e5e5 #ccc;
                border-radius: 5px;
                box-shadow: 0 0 2px rgba(0,0,0,.2);
                overflow: hidden;
                width: 100%;
            }

            #breadcrumbs li{
                float: left;
            }

            #breadcrumbs a,.next,.prev{
                padding: .7em 1em .7em 2em;
                float: left;
                text-decoration: none;
                color: #444;
                position: relative;
                text-shadow: 0 1px 0 rgba(255,255,255,.5);
                background-color: #ddd;
                background-image: -webkit-gradient(linear, left top, right bottom, from(whiteSmoke), to(#DDD));
                background-image: -webkit-linear-gradient(left, whiteSmoke, #DDD);
                background-image: -moz-linear-gradient(left, whiteSmoke, #DDD);
                background-image: -ms-linear-gradient(left, whiteSmoke, #DDD);
                background-image: -o-linear-gradient(left, whiteSmoke, #DDD);
                background-image: linear-gradient(to right, whiteSmoke, #DDD);
            }
            .next:hover,.prev:hover{
                background-color: #86F024;
                background-image: -webkit-gradient(linear, left top, right bottom, from(#fff), to(#86F024));
                background-image: -webkit-linear-gradient(left, #fff, #86F024);
                background-image: -moz-linear-gradient(left, #fff, #86F024);
                background-image: -ms-linear-gradient(left, #fff, #86F024);
                background-image: -o-linear-gradient(left, #fff, #86F024);
                background-image: linear-gradient(to right, #fff, #86F024);
            }
            .prev{
                background-image: -webkit-gradient(linear, left top, right bottom, from(#DDD), to(whiteSmoke));
                background-image: -webkit-linear-gradient(right, whiteSmoke, #DDD);
                background-image: -moz-linear-gradient(right, whiteSmoke, #DDD);
                background-image: -ms-linear-gradient(right, whiteSmoke, #DDD);
                background-image: -o-linear-gradient(right, whiteSmoke, #DDD);
                background-image: linear-gradient(to left, whiteSmoke, #DDD);
            }
            .prev:hover{
                background-image: -webkit-gradient(linear, left top, right bottom, from(#fff), to(#86F024));
                background-image: -webkit-linear-gradient(right, #fff, #86F024);
                background-image: -moz-linear-gradient(right, #fff, #86F024);
                background-image: -ms-linear-gradient(right, #fff, #86F024);
                background-image: -o-linear-gradient(right, #fff, #86F024);
                background-image: linear-gradient(to left, #fff, #86F024);
            }

            #breadcrumbs li:first-child a{
                padding-left: 1em;
                border-radius: 5px 0 0 5px;
            }

            #breadcrumbs a:hover{
                background: #fff;
            }

            #breadcrumbs a::after,
            #breadcrumbs a::before,.next::before,.next::after{
                content: "";
                position: absolute;
                top: 50%;
                margin-top: -1.5em;
                border-top: 1.5em solid transparent;
                border-bottom: 1.5em solid transparent;
                border-left: 1em solid;
                right: -1em;
            }
            
            .prev::after{
                content: "";
                position: absolute;
                top: 50%;
                margin-top: -1.5em;
                border-top: 1.5em solid transparent;
                border-bottom: 1.5em solid transparent;
                border-right: 1em solid;
                left: -1em;
            }

            #breadcrumbs a::after,.next::after{
                z-index: 2;
                border-left-color: #ddd;
            }

            #breadcrumbs a::before,.next::before{
                border-left-color: #ccc;
                right: -1.1em;
                z-index: 1;
            }
            
            .prev::after{
                z-index: 2;
                border-right-color: #ddd;
            }

            .prev::before{
                border-right-color: #ccc;
                right: -1.1em;
                z-index: 1;
            }

            #breadcrumbs a:hover::after,.next:hover::after,.prev:hover::after{
                border-left-color: #fff;
            }

            #breadcrumbs .current,
            #breadcrumbs .current:hover{
                font-weight: bold;
                background: none;
                background: #ddd;
            }

            #breadcrumbs a.current:hover::after{
                border-left-color: #ddd;
            }
            .next:hover::after{
                border-left-color: #86F024;
            }
            .prev:hover::after{
                border-right-color: #86F024;
            }

            .containerNext{display: inline-block;}
            .btns{margin:0 auto;float:none;width:240px}
            label.nocapital{text-transform: none;}
	    
	    /* Select webkit */
	    select:enabled:hover {box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);color: #333;background-image: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABMAAAAICAYAAAAbQcSUAAAAWklEQVQokWNgoAOIAuI0PDiKaJMSgYCZmfkbkPkfHYPEQfJEG/b//3+FBQsWLGRjY/uJbBCIDxIHyRNtGDYDyTYI3UA+Pr4vFBmEbODbt2+bKDYIyUBWYtQBAIRzRP/XKJ//AAAAAElFTkSuQmCC"), -webkit-linear-gradient(#fefefe, #f8f8f8 40%, #e9e9e9);}
	    select {background-position: center right;background-repeat: no-repeat;border: 1px solid #AAA;color: #555;font-size: inherit;margin: 0;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;
		    -webkit-appearance: button;-moz-appearance:button;appearance:button;
		    border-radius: 2px;box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
		    -webkit-padding-end: 15px;-moz-padding-end: 0px;-webkit-padding-start: 2px;-moz-padding-start: 2px;
		    -moz-user-select: none;-o-user-select: none;-webkit-user-select: none;user-select: none;
		    background-image: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABMAAAAICAYAAAAbQcSUAAAAWklEQVQokWNgoAOIAuI0PDiKaJMSgYCZmfkbkPkfHYPEQfJEG/b//3+FBQsWLGRjY/uJbBCIDxIHyRNtGDYDyTYI3UA+Pr4vFBmEbODbt2+bKDYIyUBWYtQBAIRzRP/XKJ//AAAAAElFTkSuQmCC"), -webkit-linear-gradient(#fefefe, #f8f8f8 40%, #e9e9e9);
		    background-image:-moz-linear-gradient(#fefefe, #f8f8f8 40%, #e9e9e9);
		    background-image:-o-linear-gradient(#fefefe, #f8f8f8 40%, #e9e9e9);
		    font-size: 13px;
		    margin: 0 7px 4px 0;
	    }
        </style>
    </body>
</html>
