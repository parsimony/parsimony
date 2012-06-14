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

namespace core;

class core extends \module {

    protected $title = 'Parsimony Core';
    protected $name = 'core';

    public function onLoad() {

	//add devices
	\app::$devices[] = array('name' => 'mobile', 'resolution' => array('360x480' => 'BlackBerry Torch',
		'480x800' => 'Google Nexus One',
		'480x800' => 'Samsung Galaxy S',
		'320x480' => 'iPhone 3/4',
		'480x854' => 'Motorola Droid'), 'detectFnc' => function() {
		return preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $_SERVER['HTTP_USER_AGENT']);
	    });
	\app::$devices[] = array('name' => 'tablet', 'resolution' => array('600x1024' => 'BlackBerry PlayBook',
		'768x1024' => 'iPad',
		'600x1024' => 'Samsung Galaxy Tab'), 'detectFnc' => function() {
		return preg_match('@(iPad|SCH-I800|GT-P)@', $_SERVER['HTTP_USER_AGENT']);
	    });
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
		return preg_match('@(GoogleTV)@', $_SERVER['HTTP_USER_AGENT']);
	    });
	\app::$devices[] = array('name' => 'web', 'resolution' => array('max' => 'Normal',
		'640x480' => '640 x 480',
		'800x600' => '800 x 600',
		'1024x768' => '1024 x 768',
		'1280x960' => '1280 x 960',
		'1280x1024' => '1280 x 1024'), 'detectFnc' => function() {
		return preg_match('@.*@', $_SERVER['HTTP_USER_AGENT']);
	    });

	\app::$aliasClasses = array('app' => 'core\classes\app',
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

	//on page load
	\app::addListener('pageLoad', array($this, 'loadExternalFiles'));
    }

    public function loadExternalFiles() {
	\app::$request->page->addCSSFile(BASE_PATH . 'lib/cms.css');
	\app::$request->page->addJSFile(BASE_PATH . 'lib/cms.js');
	if(\app::$config['general']['ajaxnav']){
	    \app::$request->page->addJSFile(BASE_PATH . 'core/js/ajaxNav.js');
	    \app::$request->page->addCSSFile(BASE_PATH . 'core/css/ajaxNav.css');
	}
	if (!defined('PARSI_ADMIN') || !PARSI_ADMIN) {
	    \app::$request->page->addJSFile(BASE_PATH . 'lib/fancybox/jquery.fancybox-1.3.4.js');
	    \app::$request->page->addCSSFile(BASE_PATH . 'lib/fancybox/jquery.fancybox-1.3.4.css');
	    \app::$request->page->addCSSFile(BASE_PATH . 'core/' . THEMETYPE . '.css');
	    \app::$request->page->addCSSFile(BASE_PATH . THEMEMODULE . '/themes/' . THEME . '/' . THEMETYPE . '.css');

	    if (THEMETYPE == 'mobile') {
		\app::$request->page->addCSSFile(BASE_PATH . 'lib/mobile.css');
		\app::$request->page->head .= '<meta name="viewport" content="width=device-width">';
	    }
	}
    }

    public function concatAction() {
	return $this->getView('concat', 'web');
    }

    public function connectAction() {
	return $this->getView('connect', 'web');
    }

    public function loginAjaxAction() {
	$login = \app::$request->getParam('login');
	$pass = \app::$request->getParam('password');
	if ($login && $pass) {
	    \app::getClass('user')->authentication($login, $pass);
	    if (\app::getClass('user')->VerifyConnexion()) {
		return TRUE;
	    }
	}
	return FALSE;
    }

    public function logoutAction() {
	return \app::getClass('user')->logOut();
    }

    public function renewPassAction() {
	if (isset($_POST ['mail']) && filter_var($_POST ['mail'], FILTER_VALIDATE_EMAIL)) {
	    return \app::getClass('user')->resetPassword(filter_var($_POST ['mail'], FILTER_VALIDATE_EMAIL));
	} else {
	    return t('Invalid E-mail');
	}
    }

    public function importAction() {
	return $this->getView('import', 'web');
    }

    public function thumbnailAction() {
	echo $this->getView('thumbnail', 'web');
	return $this->getView('thumbnail', 'web');
    }

    public function install() {
	parent::install();
	$this->getEntity('role')->insertInto(array('id_role' => '1', 'name' => 'admin'));
    }

}

?>