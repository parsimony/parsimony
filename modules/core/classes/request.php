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

	/**
	 * Init a new request
	 * @param string $url
	 */
	public function __construct($URL) {

		$this->URL = $URL;

		/* Determine HTTP request */
		$this->initMethod($_SERVER['REQUEST_METHOD']);

		/* Determine locale */
		$this->determineLocale();

			/* Rights */
		define('DISPLAY', 1);
		define('INSERT', 2);
		define('UPDATE', 4);
		define('DELETE', 8);

		/* Determine role of user */
		$this->determineRole();

		/* MODULE : search module */
		$this->determineModule();

		if ($this->determineToken() === FALSE) { /* If we don't have a Token */

			/* Determine device where we are */
			$this->determineDevice();

			/* Define THEME */
			if($_SESSION['behavior'] === 2 && isset($_COOKIE['THEME']) && isset($_COOKIE['THEMEMODULE'])){
				define('THEMEMODULE', $_COOKIE['THEMEMODULE']);
				define('THEME', $_COOKIE['THEME']);
			}else{
				define('THEMEMODULE', app::$config['THEMEMODULE']);
				define('THEME', app::$config['THEME']);
			}

			/* CRSF + TOKEN */
			$this->createNewToken();
		}

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
		$this->params[$key] = $value;
	}

	/**
	 * Get all Params from current HTTP request (GET,POST,PUT,DELETE)
	 * @return array $param
	 */
	public function isAjax() {
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
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
		$pathCache = 'var/cache/' . $this->locale . '-lang';
		$lang = '';
		if (is_file($pathCache . '.php')) {
			include $pathCache . '.php';
		} else {
			foreach (app::$config['modules']['active'] as $moduleName => $type) {
				if (is_file('modules/' . $moduleName . '/locale/' . $this->locale . '.php'))
					include('modules/' . $moduleName . '/locale/' . $this->locale . '.php');
			}
			$config = new \config($pathCache . '.php', TRUE);
			$config->setVariable('lang');
			$config->saveConfig($lang);
			\tools::file_put_contents($pathCache . '.js', substr($config->getContent(), 5, false));
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
		if (!isset($_COOKIE['device'])) {
			$_COOKIE['device'] = \app::$config['devices']['defaultDevice'];
			if(count(\app::$devices) > 1){
				foreach (\app::$devices AS $device) {
					if ($device['detectFnc']() === TRUE) {
						$_COOKIE['device'] = $device['name'];
						break;
					}
				}
			}
		}
		define('THEMETYPE', $_COOKIE['device']);
	}

	/**
	 * Determine Module
	 */
	protected function determineModule() {
		if (empty($this->URL))
			$this->URL = 'index';
		$this->URL = explode('/', $this->URL, 2);
		if (isset(\app::$config['modules']['active'][$this->URL[0]])) {
			$this->module = $this->URL[0];
			if (isset($this->URL[1]))
				$this->secondPartURL = $this->URL[1];
		}else {
			/* We test if core Module can respond */
			if(method_exists(app::getModule('core'), $this->URL[0] . 'Action')){
				$this->module = 'core';
			}else{
				$this->module = \app::$config['modules']['default'];
			}
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
		$moduleName = $module->getName();
		define('MODULE', $moduleName);
		if (!isset($_SESSION['tokensReverse'][THEMETYPE][THEMEMODULE][THEME][$moduleName])) {
			$token = sha1(THEMETYPE . THEMEMODULE . THEME . $moduleName . \app::$config['security']['salt'] . time()); // change salt
			$_SESSION['tokens'][$token] = array('THEMETYPE' => THEMETYPE, 'THEMEMODULE' => THEMEMODULE, 'THEME' => THEME, 'MODULE' => $moduleName);
			$_SESSION['tokensReverse'][THEMETYPE][THEMEMODULE][THEME][$moduleName] = $token;
			define('TOKEN', $token);
		} else {
			define('TOKEN', $_SESSION['tokensReverse'][THEMETYPE][THEMEMODULE][THEME][$moduleName]);
		}
	}

	/**
	 * Dispatch Request
	 * @return response|string
	 */
	public function dispatch() {
		$module = app::getModule($this->module);
		if($module->getRights($_SESSION['id_role']) === 1 || $this->module === 'core'){
			if ($module->controller($this->secondPartURL, $this->method) === FALSE) {
				//if Page not found
				return app::$response->setContent(app::getModule('core')->getView('404', 'desktop'), 404);
			}
		}else{
		   return app::$response->setContent(app::getModule('core')->getView('403', 'desktop'), 403); 
		}
	}

	/**
	 * Determine Role & permissions
	 */
	protected function determineRole() {
		if (\app::getClass('user')->VerifyConnexion() === TRUE &&
			( empty(app::$config['security']['allowedipadmin']) || preg_match('@' . preg_quote($_SERVER['REMOTE_ADDR'], '.') . '@', app::$config['security']['allowedipadmin']))) {

			/* Mainly to use in query block */
			$this->setParams(array('id_user' => $_SESSION['id_user'],
									'id_role' => $_SESSION['id_role'],
									'behavior' => $_SESSION['behavior'],
									'login' => $_SESSION['login']));

			if($_SESSION['behavior'] > 0){
				/* Add admin module */
				\app::$config['modules']['active']['admin'] = 1;
				/* If user is a creator we display errors and active admin module*/
				if($_SESSION['behavior'] === 2){
					error_reporting(-1);
					ini_set('display_errors', 1);

				}
				// check if it's admin role or not
				if (!isset($_GET['parsiframe']) && $this->method === 'GET')
					define('PARSI_ADMIN', 1);
			}

		}else{
			$_SESSION['behavior'] = 0;
			$_SESSION['id_role'] = 6;
		}
	}

	/**
	 * Init method HTTP and Secure incoming vars
	 * @param string $method optional
	 */
	protected function initMethod($method = 'GET') {
		$this->method = $method;

		/* Regardless of the method (POST, PUT, DELETE), GET method is always treated ! */
		array_walk_recursive($_GET, function(&$v, &$k) {
			$v = str_replace(chr(0), '', $v);
		});
		$this->params = $_GET;

		if($method !== 'GET'){
			switch ($method) {
				case 'POST':
					array_walk_recursive($_POST, function(&$v, &$k) {
						$v = str_replace(chr(0), '', $v);
					});
					$this->params = array_merge($this->params, $_POST);
					break;
				case 'PUT':
					parse_str(file_get_contents("php://input"), $_PUT);
					array_walk_recursive($_PUT, function(&$v, &$k) {
						$v = str_replace(chr(0), '', $v);
					});
					$this->params = array_merge($this->params, $_PUT);
					break;
				case 'DELETE':
					parse_str(file_get_contents("php://input"), $_DELETE);
					array_walk_recursive($_DELETE, function(&$v, &$k) {
						$v = str_replace(chr(0), '', $v);
					});
					$this->params = array_merge($this->params, $_DELETE);
					break;
			}
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
	'fa_IR' => 'Persian (Iran)',
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