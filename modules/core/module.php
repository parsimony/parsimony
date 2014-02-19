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
 * @title Parsimony
 * @description Module Core
 * @copyright 1
 * @browsers all
 * @php_version_min 5.3
 * @php_extension php_pdo_mysql,php_gd2
 * @php_settings magic_quotes_gpc:0,register_globals:0
 * @displayAdmin 0
 * @mode r
 */
class module extends \module {

	protected $name = 'core';

	public function __wakeup() {

		/* Add devices */
		$devices = \app::$config['devices'];

		/* TV */
		if ($devices['tv']) {
			\app::$devices[] = array('name' => 'tv', 'resolution' => array('720x1280' => '720p',
					'1080x1920' => '1080p',
					'1024x768' => '4/3',
					'1280x768' => '15/9',
					'1360x768' => '16/9 HD',
					'1366x768' => '16/9 HD Ready',
					'1920x1080' => '16/9 Full HD',
					'1600x900' => '16/9 HD Extended',
					'768x1366' => '16/9 HD Ready',
					'1000x1600' => '16/10'), 'detectFnc' => function() {
					return preg_match('/TV/i', $_SERVER['HTTP_USER_AGENT']);
				});
		}
	
		/* Tablet */
		if ($devices['tablet']) {
			\app::$devices[] = array('name' => 'tablet', 'resolution' => array('800x1280' => 'Google Nexus 10 / Samsung Galaxy Tab 2 10.1',
					'601x921' => 'Google Nexus 7',
					'600x1024' => 'Samsung Galaxy Tab 2 7.7',
					'768x1366' => 'Microsoft Surface',
					'768x1024' => 'Apple iPad'), 'detectFnc' => function() {
					return preg_match('/(Tablet|Ipad|Kindle|Silk)|(Android(?!.*(Mobi|Opera Mini)))/i', $_SERVER['HTTP_USER_AGENT']); /* tablet must be under mobile because "Android" test is good only without "Mobile" in user agent string */
				});
		}
		
		/* Mobile */
		if ($devices['mobile']) {
			\app::$devices[] = array('name' => 'mobile', 'resolution' => array('384x640' => 'Nexus 4',
					'320x568' => 'Apple iPhone 5',
					'360x640' => 'Samsung Galaxy S3&4 / HTC One',
					'320x480' => 'Nokia Lumia 900'), 'detectFnc' => function() {
					return preg_match('/Mobi|Opera Mini|BlackBerry/i', $_SERVER['HTTP_USER_AGENT']);
				});
		}
		
		/* Desktop */
		if ($devices['desktop']) {
			\app::$devices[] = array('name' => 'desktop', 'resolution' => array('max' => 'Normal',
					'640x480' => '',
					'800x600' => '',
					'1024x768' => '',
					'1280x960' => '',
					'1280x1024' => ''), 'detectFnc' => function() {
					return TRUE;
				});
		}

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
			\app::$response->addCSSFile(\app::$config['modules']['default'] . '/css/' . THEMETYPE . '/style.css');
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
		$mail = app::$request->getParam('mail');
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

	public function install() {
		parent::install();
		$this->getEntity('role')->insertInto(array('id_role' => '1', 'name' => 'Super Admin', 'state' => '2'));
		$this->getEntity('role')->insertInto(array('id_role' => '2', 'name' => 'Admin', 'state' => '2'));
		$this->getEntity('role')->insertInto(array('id_role' => '3', 'name' => 'Developer', 'state' => '2'));
		$this->getEntity('role')->insertInto(array('id_role' => '4', 'name' => 'Webmaster', 'state' => '1'));
		$this->getEntity('role')->insertInto(array('id_role' => '5', 'name' => 'Subscriber', 'state' => '0'));
		$this->getEntity('role')->insertInto(array('id_role' => '6', 'name' => 'Anonymous', 'state' => '0'));
	}

}

?>