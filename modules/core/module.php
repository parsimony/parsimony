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
 * @package core
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace core;

/**
 * @title Administration
 * @description Module Core
 * @copyright 1
 * @browsers all
 * @php_version_min 5.3
 * @php_extension php_pdo_mysql,php_gd2
 * @php_settings magic_quotes_gpc:0,register_globals:0
 * @displayAdmin 4
 */
class module extends \module {

	protected $name = 'core';

	public function __wakeup() {

		\app::$devices = array(
			/* TV */
			'tv' => array('name' => 'tv', 'icon' => 'M 13.415,3.939c-1.214-0.175-2.507-0.301-3.857-0.372L 11.813,1.313l-0.875-0.875L 7.867,3.508 C 7.58,3.503, 7.291,3.5, 7,3.5l0,0L 3.5,0L 2.625,0.875l 2.655,2.655c-1.653,0.058-3.231,0.199-4.695,0.409 C 0.209,5.412,0,7.039,0,8.75s 0.209,3.338, 0.585,4.811C 2.549,13.843, 4.719,14, 7,14 c 2.281,0, 4.451-0.157, 6.415-0.439C 13.791,12.088, 14,10.461, 14,8.75S 13.791,5.412, 13.415,3.939z M 11.811,11.957 C 10.338,12.146, 8.711,12.25, 7,12.25c-1.711,0-3.338-0.104-4.811-0.293C 1.907,10.975, 1.75,9.891, 1.75,8.75 c0-1.141, 0.157-2.226, 0.439-3.207C 3.662,5.354, 5.289,5.25, 7,5.25c 1.711,0, 3.338,0.104, 4.811,0.293 C 12.093,6.524, 12.25,7.609, 12.25,8.75C 12.25,9.891, 12.093,10.975, 11.811,11.957z',
				'resolution' => array('720x1280' => '720p',
				'1080x1920' => '1080p',
				'1024x768' => '4/3',
				'1280x768' => '15/9',
				'1360x768' => '16/9 HD',
				'1366x768' => '16/9 HD Ready',
				'1920x1080' => '16/9 Full HD',
				'1600x900' => '16/9 HD Extended',
				'768x1366' => '16/9 HD Ready',
				'1000x1600' => '16/10'), 'detectFunc' => function() {
				return preg_match('/TV/i', $_SERVER['HTTP_USER_AGENT']);
			}),
		
			/* Tablet */
			'tablet' => array('name' => 'tablet', 'icon' => 'M 10.938,0L 2.188,0 C 1.466,0, 0.875,0.591, 0.875,1.313l0,11.375 c0,0.722, 0.591,1.313, 1.313,1.313l 8.75,0 c 0.722,0, 1.313-0.591, 1.313-1.313L 12.25,1.313 C 12.25,0.591, 11.659,0, 10.938,0z M 6.563,13.563 c-0.242,0-0.438-0.196-0.438-0.438s 0.196-0.438, 0.438-0.438s 0.438,0.196, 0.438,0.438S 6.804,13.563, 6.563,13.563z M 10.5,12.25L 2.625,12.25 L 2.625,1.75 l 7.875,0 L 10.5,12.25 z',
				'resolution' => array('800x1280' => 'Google Nexus 10 / Samsung Galaxy Tab 2 10.1',
				'601x921' => 'Google Nexus 7',
				'600x1024' => 'Samsung Galaxy Tab 2 7.7',
				'768x1366' => 'Microsoft Surface',
				'768x1024' => 'Apple iPad'), 'detectFunc' => function() { 
				return preg_match('/(Tablet|Ipad|Kindle|Silk)|(Android(?!.*(Mobi|Opera Mini)))/i', $_SERVER['HTTP_USER_AGENT']); /* tablet must be under mobile because "Android" test is good only without "Mobile" in user agent string */
			}),
		
			/* Mobile */
			'mobile' => array('name' => 'mobile', 'icon' => 'M 5.25,0.875l 2.625,0 l0,0.875 l-2.625,0 L 5.25,0.875 z M 3.5,2.625l 6.125,0 l0,8.75 L 3.5,11.375 L 3.5,2.625 z M 6.125,12.25l 0.875,0 l0,0.875 l-0.875,0 L 6.125,12.25 z M 3.5,0C 3.063,0, 2.625,0.438, 2.625,0.875l0,12.25 c0,0.438, 0.438,0.875, 0.875,0.875l 6.125,0 c 0.438,0, 0.875-0.438, 0.875-0.875L 10.5,0.875 c0-0.438-0.438-0.875-0.875-0.875L 3.5,0 z',
				'resolution' => array('384x640' => 'Nexus 4',
				'320x568' => 'Apple iPhone 5',
				'360x640' => 'Samsung Galaxy S3&4 / HTC One',
				'320x480' => 'Nokia Lumia 900'), 'detectFunc' => function() {
				return preg_match('/Mobi|Opera Mini|BlackBerry/i', $_SERVER['HTTP_USER_AGENT']);
			}),
		
			/* Desktop */
			'desktop' => array('name' => 'desktop', 'icon' => 'M 7.875,10.5l0,0.875 l 2.625,0 l0,1.75 L 3.5,13.125 l0-1.75 l 2.625,0 l0-0.875 L0,10.5 L0,0.875 l 14,0 l0,9.625 L 7.875,10.5 z M 13.125,1.75L 0.875,1.75 l0,7 l 12.25,0 L 13.125,1.75 z M 1.75,5.223L 1.75,2.625 l 2.625,0 L 1.75,5.223z',
				'resolution' => array('max' => 'Normal',
				'640x480' => '',
				'800x600' => '',
				'1024x768' => '',
				'1280x960' => '',
				'1280x1024' => ''), 'detectFunc' => function() {
				return TRUE;
			}));
		
		\app::$aliasClasses = array('app' => 'core\classes\app',
			'request' => 'core\classes\request',
			'response' => 'core\classes\response',
			'block' => 'core\classes\block',
			'tools' => 'core\classes\tools',
			'view' => 'core\classes\view',
			'module' => 'core\classes\module',
			'PDOconnection' => 'core\classes\PDOconnection',
			'config' => 'core\classes\config',
			'queryBuilder' => 'core\classes\queryBuilder',
			'entity' => 'core\classes\entity',
			'theme' => 'core\classes\theme',
			'page' => 'core\classes\page',
			'css' => 'core\classes\css',
			'user' => 'core\classes\user',
			'pagination' => 'core\classes\pagination',
			'img' => 'core\classes\img',
			'field' => 'core\classes\field',
			'field_ident' => 'core\fields\ident',
			'field_string' => 'core\fields\string',
			'field_numeric' => 'core\fields\numeric',
			'field_decimal' => 'core\fields\decimal',
			'field_price' => 'core\fields\price',
			'field_percent' => 'core\fields\percent',
			'field_mail' => 'core\fields\mail',
			'field_password' => 'core\fields\password',
			'field_state' => 'core\fields\state',
			'field_date' => 'core\fields\date',
			'field_publication' => 'core\fields\publication',
			'field_image' => 'core\fields\image',
			'field_url' => 'core\fields\url',
			'field_url_rewriting' => 'core\fields\url_rewriting',
			'field_wysiwyg' => 'core\fields\wysiwyg',
			'field_textarea' => 'core\fields\textarea',
			'field_user' => 'core\fields\user',
			'field_ip' => 'core\fields\ip',
			'field_boolean' => 'core\fields\boolean',
			'field_file' => 'core\fields\file',
			'field_foreignkey' => 'core\fields\foreignkey',
			'field_formasso' => 'core\fields\formasso',
			'field_alias' => 'core\fields\alias'
		);

		/* on page load */
		\app::addListener('beforePageLoad', array($this, 'loadExternalFiles'));
	}

	public function loadExternalFiles() {
		if (!defined('PARSI_ADMIN')) {
			\app::$response->addCSSFile(\app::$config['defaultModule'] . '/css/style.css');
		}
		if(DEVICE !== 'desktop') { /* set viewport here allow us to override it later */
			\app::$response->page->setMeta('viewport', 'width=device-width, user-scalable=no');
			\app::$response->page->setMeta('apple-mobile-web-app-capable', 'yes');
		}
	}

	public function connectAction() {
		return $this->getView('connect');
	}

	public function loginAction($login, $password, $URL = FALSE) {
		if ($URL !== FALSE)
			\app::$response->setHeader('Location', $URL);
		if (!empty($login) && !empty($password)) {
			\app::getClass('user')->authentication($login, $password);
			if (\app::getClass('user')->VerifyConnexion()) {
				\app::$response->setFormat('json');
				return json_encode(array('TOKEN' => $_SESSION['TOKEN'])); /* send TOKEN : usefull for webapps */
			}
		}
		return FALSE;
	}

	public function logoutAction() {
		return \app::getClass('user')->logOut();
	}

	public function renewPassAction() {
		$mail = \app::$request->getParam('mail');
		if ($mail !== FALSE && filter_var($mail, FILTER_VALIDATE_EMAIL)) {
			return \app::getClass('user')->resetPassword(filter_var($mail, FILTER_VALIDATE_EMAIL));
		} else {
			return t('Invalid E-mail');
		}
	}

	public function sitemapAction() {
		return $this->getView('sitemap');
	}
	
	/**
	 * Override getRights core methods should allowed for all
	 * @param string $role
	 * @return integer
	 */
	public function getRights($role) {
		return 1;
	}
	
	/**
	 * Define admin menu for left toolbar
	 * @return array
	 */
	public function getAminMenu() {
		$links = array();
		if($_SESSION['permissions'] & 1) { /* perm 1 = settings */
			$links['#left_sidebar/settings/admin'] = 'General';
		}
		if($_SESSION['permissions'] & 65536) {  /* perm 65536 = grant */
			$links['#left_sidebar/permissions'] = 'Permissions';
			$links['#left_sidebar/model/core/role'] = 'Roles';
			$links['#left_sidebar/model/core/user'] = 'Users';
		}
		return $links;
	}
		
	
	public function install() {
		parent::install();
		$this->getEntity('role')->insertInto(array('id_role' => '1', 'name' => 'Administrator', 'permissions' => '129023'));
		$this->getEntity('role')->insertInto(array('id_role' => '2', 'name' => 'Editor', 'permissions' => '34317'));
		$this->getEntity('role')->insertInto(array('id_role' => '3', 'name' => 'Registered user', 'permissions' => '0'));
		$this->getEntity('role')->insertInto(array('id_role' => '4', 'name' => 'Anonymous user', 'permissions' => '0'));
	}

}

?>