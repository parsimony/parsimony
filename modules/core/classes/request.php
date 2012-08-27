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
 * @package core\classes
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace core\classes;

/**
 *  Request Class 
 *  Manages HTTP Request
 */
class request {

    /** @var string function */
    protected $method;

    /** @var string URL */
    protected $URL;

    /** @var string second part of URL */
    protected $secondPartURL;

    /** @var array of params */
    protected $params = array();

    /** @var string module */
    protected $module;

    /** @var page Page object */
    public $page;

    /** @var string locale language */
    protected $locale;

    /** @var string device */
    protected $device;

    public function __construct() {

	$this->initMethod();

	\app::getClass('user'); //fix to enable user
	/* Init a page */
	$this->page = new \page(0, 'core');

	/* Determine locale */
	$this->determineLocale();

	/* MODULE : search module */
	$this->determineModule();

	if (!$this->determineToken()) { //if we don't have a Token..

	    /* determine device where we are */
	    $this->determineDevice();

	    /* Define THEME */	    
	    //print_r($_COOKIE);
            if(isset($_SESSION['roleBehavior']) && $_SESSION['roleBehavior'] == 2 && isset($_COOKIE['THEME']) && isset($_COOKIE['THEMEMODULE'])){
                define('THEMEMODULE', $_COOKIE['THEMEMODULE']);
                define('THEME', $_COOKIE['THEME']);
	    }else{
                define('THEMEMODULE', app::$config['THEMEMODULE']);
                define('THEME', app::$config['THEME']);
            }

	    /* CRSF + TOKEN */
	    $this->createNewToken();
	}

	//Rights
	define('DISPLAY', 1 << 0);
	define('INSERT', 1 << 1);
	define('UPDATE', 1 << 2);
	define('DELETE', 1 << 3);

	/* determine permissions */
	$this->determineRole();

	//verify if it's admin role or not
	if (!isset($_GET['parsiframe']) && (BEHAVIOR == 1 || BEHAVIOR==2) && empty($_POST))
	    define('PARSI_ADMIN', 1);
    }

    /**
     * Get Locale
     * @return string locale language
     */
    public function getLocale() {
	return $this->locale;
    }

    /**
     * Get Param from current HTTP request (GET,POST,PUT,DELETE)
     * @param string $param
     * @return string|false
     */
    public function getParam($param) {
	if (isset($this->params[$param]))
	    return $this->params[$param];
	else
	    return FALSE;
    }

    /**
     * Get all Params from current HTTP request (GET,POST,PUT,DELETE)
     * @return array $param
     */
    public function getParams() {
	return $this->params;
    }

    /**
     * Set Params
     * @param array $params
     */
    public function setParams(array $params) {
	$this->params = array_merge($this->params, $params);
    }
    
    /**
     * Set Param
     * @param string $key
     * @param string $value
     */
    public function setParam($key, $value) {
	$this->params[$key] =  $value;
    }

    /**
     * Get all Params from current HTTP request (GET,POST,PUT,DELETE)
     * @return array $param
     */
    public function isAjax() {
	if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	    return TRUE;
	} else {
	    return FALSE;
	}
    }

    /**
     * Detetermine Locale from current HTTP request
     */
    protected function determineLocale() {
	if (isset($_COOKIE['locale']) && isset(self::$locales[$_COOKIE['locale']]))
	    $this->locale = $_COOKIE['locale'];
	else
	    $this->locale = app::$config['localization']['default_language'];
	setlocale(LC_ALL, $this->locale);
	date_default_timezone_set(app::$config['localization']['timezone']);
	$pathCache = 'cache/' . $this->locale . '-lang';
	$lang = '';
	if (is_file($pathCache . '.php')) {
	    include $pathCache . '.php';
	} else {
	    foreach (app::$activeModules as $moduleName => $type) {
		if (is_file('modules/' . $moduleName . '/locale/' . $this->locale . '.php'))
		    include('modules/' . $moduleName . '/locale/' . $this->locale . '.php');
	    }
	    $config = new \config($pathCache . '.php', TRUE);
	    $config->setVariable('lang');
	    $config->saveConfig($lang);
	    \tools::file_put_contents($pathCache . '.js', substr($config->getContent(), 5));
	}
	app::$lang = $lang;
    }

    /**
     * Detetermine constants thanks to token
     * @return bool
     */
    protected function determineToken() {
	if (isset($_POST['TOKEN'])) {
	    if (isset($_SESSION['tokens'][$_POST['TOKEN']])) {
		define('TOKEN', $_POST['TOKEN']); // verif good token
		define('THEMETYPE', $_SESSION['tokens'][TOKEN]['THEMETYPE']);
		define('THEME', $_SESSION['tokens'][TOKEN]['THEME']);
		define('THEMEMODULE', $_SESSION['tokens'][TOKEN]['THEMEMODULE']);
		define('MODULE', $_SESSION['tokens'][TOKEN]['MODULE']);
		return TRUE;
	    }
	}
	return FALSE;
    }

    /**
     * Detect device of visitor
     */
    protected function determineDevice() {
	if (isset($_COOKIE['device'])) {
	    define('THEMETYPE', $_COOKIE['device']);
	} else {
	    foreach (\app::$devices AS $device) {
		if ($device['detectFnc']() == TRUE) {
		    define('THEMETYPE', $device['name']);
		    $_COOKIE['device'] = $device['name'];
		    break;
		}
	    }
	}
    }

    /**
     * Determine Module
     */
    protected function determineModule() {
	if (empty($_GET['parsiurl']))
	    $_GET['parsiurl'] = 'index';
	$this->URL = explode('/', $_GET['parsiurl'], 2);
	if (isset(\app::$activeModules[$this->URL[0]])) {
	    $this->module = $this->URL[0];
	    if (isset($this->URL[1]))
		$this->secondPartURL = $this->URL[1];
	}else {
	    $this->URL[0] = 'core';
	    $this->module = 'core';
	    $this->secondPartURL = $_GET['parsiurl'];
	}
	if (empty($this->secondPartURL))
	    $this->secondPartURL = 'index';
    }

    /**
     * Generate TOKEN
     */
    protected function createNewToken() {
	$module = app::getModule($this->module);
	define('MODULE', $module->getName());
	if (!isset($_SESSION['tokensReverse'][THEMETYPE][THEMEMODULE][THEME][$module->getName()])) {
	    $token = sha1(THEMETYPE . THEMEMODULE . THEME . $module->getName() . \app::$config['security']['salt'] . time()); // change salt
	    $_SESSION['tokens'][$token] = array('THEMETYPE' => THEMETYPE, 'THEMEMODULE' => THEMEMODULE, 'THEME' => THEME, 'MODULE' => $module->getName());
	    $_SESSION['tokensReverse'][THEMETYPE][THEMEMODULE][THEME][$module->getName()] = $token;
	    define('TOKEN', $token);
	} else {
	    define('TOKEN', $_SESSION['tokensReverse'][THEMETYPE][THEMEMODULE][THEME][$module->getName()]);
	}
    }

    /**
     * Dispatch Request
     * @return response|string
     */
    public function dispatch() {
	if (app::getModule($this->module)->{'controller' . $this->method}($this->secondPartURL) === FALSE) {
	    //if no page found load a 404 page
	    $page = new \core\classes\page(404, 'core');
	    $block = new \core\blocks\code('error_404', FALSE);
	    $block->setConfig('pathCode', 'modules/core/views/web/error_404.php');
	    $page->addBlock($block);
	    return app::$response->setContent($page, 404);
	}
    }

    /**
     * Determine Role & permissions
     */
    protected function determineRole() {
	if (\app::getClass('user')->VerifyConnexion() &&
		( empty(app::$config['secURLty']['allowedipadmin']) || preg_match('@' . preg_quote($_SERVER['REMOTE_ADDR'], '.') . '@', app::$config['secURLty']['allowedipadmin']))) {
	    define('ID_ROLE', $_SESSION['idr']);
            define('BEHAVIOR',$_SESSION['roleBehavior']);
	}else
	    define('BEHAVIOR', 0);
    }

    /**
     * Init method HTTP and Secure incoming vars 
     */
    protected function initMethod() {
	/* By default GET */
        
	$this->method = 'GET';
	array_walk_recursive($_GET, function(&$v, &$k) {
            $v = filter_var(str_replace(chr(0), '', $v), FILTER_UNSAFE_RAW);
        });
	$this->params = $_GET;

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	    $this->method = 'POST';
	    array_walk_recursive($_POST, function(&$v, &$k) {
                $v = filter_var(str_replace(chr(0), '', $v), FILTER_UNSAFE_RAW);
            });
	    $this->params = array_merge($this->params, $_POST);
            
	} elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
	    $this->method = 'PUT';
	    parse_str(file_get_contents("php://input"), $_PUT);
	    array_walk_recursive($_PUT, function(&$v, &$k) {
                $v = filter_var(str_replace(chr(0), '', $v), FILTER_UNSAFE_RAW);
            });
	    $this->params = array_merge($this->params, $_PUT);
            
	} elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
	    $this->method = 'DELETE';
	    parse_str(file_get_contents("php://input"), $_DELETE);
	    array_walk_recursive($_DELETE, function(&$v, &$k) {
                $v = filter_var(str_replace(chr(0), '', $v), FILTER_UNSAFE_RAW);
            });
	    $this->params = array_merge($this->params, $_DELETE);
	}
    }

    /**
     * Locales
     * array of Locales
     */
    static public $locales = array(
	'af_AF' => 'Afrikaans',
	'sq_SQ' => 'Albanian',
	'ar_DZ' => 'Arabic (Algeria)',
	'ar_BH' => 'Arabic (Bahrain)',
	'ar_EG' => 'Arabic (Egypt)',
	'ar_IQ' => 'Arabic (Iraq)',
	'ar_JO' => 'Arabic (Jordan)',
	'ar_KW' => 'Arabic (Kuwait)',
	'ar_LB' => 'Arabic (Lebanon)',
	'ar_LY' => 'Arabic (libya)',
	'ar_MA' => 'Arabic (Morocco)',
	'ar_OM' => 'Arabic (Oman)',
	'ar_QA' => 'Arabic (Qatar)',
	'ar_SA' => 'Arabic (Saudi Arabia)',
	'ar_SY' => 'Arabic (Syria)',
	'ar_TN' => 'Arabic (Tunisia)',
	'ar_AE' => 'Arabic (U.A.E.)',
	'ar_YE' => 'Arabic (Yemen)',
	'ar_AR' => 'Arabic',
	'hy_HY' => 'Armenian',
	'as_AS' => 'Assamese',
	'az_AZ' => 'Azeri (Latin)',
	'eu_EU' => 'Basque',
	'be_BE' => 'Belarusian',
	'bn_BN' => 'Bengali',
	'bg_BG' => 'Bulgarian',
	'ca_CA' => 'Catalan',
	'zh_CN' => 'Chinese (China)',
	'zh_HK' => 'Chinese (Hong Kong SAR)',
	'zh_MO' => 'Chinese (Macau SAR)',
	'zh_SG' => 'Chinese (Singapore)',
	'zh_TW' => 'Chinese (Taiwan)',
	'zh_ZH' => 'Chinese',
	'hr_HR' => 'Croatian',
	'cs_CS' => 'Chech',
	'da_DA' => 'Danish',
	'nl_BE' => 'Dutch (Belgium)',
	'nl_NL' => 'Dutch (Netherlands)',
	'en_AU' => 'English (Australia)',
	'en_BZ' => 'English (Belize)',
	'en_CA' => 'English (Canada)',
	'en_EN' => 'English',
	'en_IE' => 'English (Ireland)',
	'en_JM' => 'English (Jamaica)',
	'en_NZ' => 'English (New Zealand)',
	'en_PH' => 'English (Philippines)',
	'en_ZA' => 'English (South Africa)',
	'en_TT' => 'English (Trinidad)',
	'en_GB' => 'English (United Kingdom)',
	'en_US' => 'English (United States)',
	'en_ZW' => 'English (Zimbabwe)',
	'et_ET' => 'Estonian',
	'fo_FO' => 'Faeroese',
	'fa_FA' => 'Farsi',
	'fi_FI' => 'Finnish',
	'fr_BE' => 'French (Belgium)',
	'fr_CA' => 'French (Canada)',
	'fr_FR' => 'French (France)',
	'fr_LU' => 'French (Luxembourg)',
	'fr_MC' => 'French (Monaco)',
	'fr_CH' => 'French (Switzerland)',
	'mk_MK' => 'FYRO Macedonian',
	'gd_GD' => 'Gaelic',
	'ka_KA' => 'Georgian',
	'de_AT' => 'German (Austria)',
	'de_DE' => 'German (Germany)',
	'de_LI' => 'German (Liechtenstein)',
	'de_LU' => 'German (lexumbourg)',
	'de_CH' => 'German (Switzerland)',
	'el_EL' => 'Greek',
	'gu_GU' => 'Gujarati',
	'he_HE' => 'Hebrew',
	'hi_HI' => 'Hindi',
	'hu_HU' => 'Hungarian',
	'is_IS' => 'Icelandic',
	'id_ID' => 'Indonesian',
	'it_IT' => 'Italian (Italy)',
	'it_CH' => 'Italian (Switzerland)',
	'ja_JA' => 'Japanese',
	'kn_KN' => 'Kannada',
	'kk_KK' => 'Kazakh',
	'ko_KO' => 'Korean',
	'kz_KZ' => 'Kyrgyz',
	'lv_LV' => 'Latvian',
	'lt_LT' => 'Lithuanian',
	'ms_MS' => 'Malay (Malaysia)',
	'ml_ML' => 'Malayalam',
	'mt_MT' => 'Maltese',
	'mr_MR' => 'Marathi',
	'mn_MN' => 'Mongolian (Cyrillic)',
	'ne_NE' => 'Nepali (India)',
	'no_NO' => 'Norwegian (Bokmal)',
	'nn_NO' => 'Norwegian (Nynorsk)',
	'or_OR' => 'Oriya',
	'pl_PL' => 'Polish',
	'pt_BR' => 'Portuguese (Brazil)',
	'pt_PT' => 'Portuguese (Portugal)',
	'pa_PA' => 'Punjabi',
	'rm_RM' => 'Rhaeto_Romanic',
	'ro_MD' => 'Romanian (Moldova)',
	'ro_RO' => 'Romanian',
	'ru_MD' => 'Russian (Moldova)',
	'ru_RU' => 'Russian',
	'sa_SA' => 'Sanskrit',
	'sr_SR' => 'Serbian (Latin)',
	'sk_SK' => 'Slovak',
	'ls_LS' => 'Slovenian',
	'sb_SB' => 'Sorbian',
	'es_AR' => 'Spanish (Argentina)',
	'es_BO' => 'Spanish (Bolivia)',
	'es_CL' => 'Spanish (Chile)',
	'es_CO' => 'Spanish (Colombia)',
	'es_CR' => 'Spanish (Costa Rica)',
	'es_DO' => 'Spanish (Dominican Republic)',
	'es_EC' => 'Spanish (Ecuador)',
	'es_SV' => 'Spanish (El Salvador)',
	'es_GT' => 'Spanish (Guatemala)',
	'es_HN' => 'Spanish (Honduras)',
	'es_ES' => 'Spanish (Traditional Sort)',
	'es_MX' => 'Spanish (Mexico)',
	'es_NI' => 'Spanish (Nicaragua)',
	'es_PA' => 'Spanish (Panama)',
	'es_PY' => 'Spanish (Paraguay)',
	'es_PE' => 'Spanish (Peru)',
	'es_PR' => 'Spanish (Puerto Rico)',
	'es_US' => 'Spanish (United States)',
	'es_UY' => 'Spanish (Uruguay)',
	'es_VE' => 'Spanish (Venezuela)',
	'sx_SX' => 'Sutu',
	'sw_SW' => 'Swahili',
	'sv_FI' => 'Swedish (Finland)',
	'sv_SV' => 'Swedish',
	'ta_TA' => 'Tamil',
	'tt_TT' => 'Tatar',
	'te_TE' => 'Telugu',
	'th_TH' => 'Thai',
	'ts_TS' => 'Tsonga',
	'tn_TN' => 'Tswana',
	'tr_TR' => 'Turkish',
	'uk_UK' => 'Ukrainian',
	'ur_UR' => 'Urdu',
	'uz_UZ' => 'Uzbek (Latin)',
	'vi_VI' => 'Vietnamese',
	'xh_XH' => 'Xhosa',
	'yi_YI' => 'Yiddish',
	'zu_ZU' => 'Zulu');

}

?>