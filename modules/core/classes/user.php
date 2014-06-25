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
 * @package core\classes
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace core\classes;

/**
 * User Class 
 * Manage users 
 */
class user {

	public function __construct() {
		
		ini_set('use_only_cookies', 1);
		ini_set('session.cookie_httponly', true);
		/* Store sessions in PROFILE */
		$savePath = DOCUMENT_ROOT . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'sessions' . DIRECTORY_SEPARATOR . PROFILE;
		if(\app::$config['session']['depth'] > 0) {
			ini_set('session.save_path', \app::$config['session']['depth'] . ';"' . $savePath . '"');
		} else {
			ini_set('session.save_path',  $savePath);
		}
		ini_set('session.gc_maxlifetime', \app::$config['session']['maxlifetime']);
		//ini_set('session.cache_limiter', 'nocache'); value by default for PHP
		ini_set('session.hash_function', 'sha256');
		ini_set('session.hash_bits_per_character', 5);
		
		session_start();
		
		if (!isset($_SESSION['time'])) {
			/* Init session */
			$_SESSION['time'] = time();
			$_SESSION['TOKEN'] = sha1(session_id() . \app::$config['security']['salt'] . microtime()); /* generated at the first session id https://www.owasp.org/index.php/Cross-Site_Request_Forgery_%28CSRF%29_Prevention_Cheat_Sheet */

		} elseif (time() - $_SESSION['time'] > \app::$config['session']['renew']) {
			session_regenerate_id(TRUE);
			$_SESSION['time'] = time();
		}
	}

	/**
	 * Auth
	 * @param string $login
	 * @param string $password
	 * @return bool
	 */
	public static function authentication($login, $password) {
		if (\app::getModule('core')->getEntity('user')->pseudo()->validate($login) === FALSE || \app::getModule('core')->getEntity('user')->pass()->validate($password) === FALSE) {
			return FALSE;
		} else {
			$sth = PDOconnection::getDB()->prepare('SELECT pseudo, pass, id_user, ' . PREFIX . 'core_role.id_role, ' . PREFIX . 'core_role.permissions FROM ' . PREFIX . 'core_user INNER JOIN ' . PREFIX . 'core_role ON ' . PREFIX . 'core_user.id_role = ' . PREFIX . 'core_role.id_role WHERE pseudo = :pseudo AND ' . PREFIX . 'core_user.state = 1');
			$sth->execute(array(':pseudo' => $login));
			$obj = $sth->fetch();
			if (is_array($obj)) {
				$mdp = $obj['pass'];
				if ((string) $mdp === \sha1($password . \app::$config['security']['salt'])) {
					$_SESSION['login'] = $login;
					$_SESSION['id_user'] = (int) $obj['id_user'];
					$_SESSION['id_role'] = (int) $obj['id_role'];
					$_SESSION['permissions'] = (int) $obj['permissions'];
					return TRUE;
				} else {
					return FALSE;
				}
			} else {
				return FALSE;
			}
		}
	}

	/**
	 * Check if we are log in
	 * @return bool
	 */
	public function VerifyConnexion() {
		if (isset($_SESSION['login'])) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Log Out
	 * @static function
	 */
	public static function logOut() {
		$_SESSION = array();
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
		\session_destroy();
		header('location:index');
		exit;
	}

	/**
	 * Reset a user's password and send the new one by email
	 * @param $userMail mail
	 */
	public function resetPassword($userMail) {
		$newPass = substr(hash('sha1', uniqid(mt_rand())), 0, 8);
		\app::getModule('core')->getEntity('user')->where('mail = :mail')->update(array('pass' => $newPass, 'mail' => $userMail));

		$password = $newPass;
		ob_start();
		include('admin/views/mail/remdp.php');
		$body = ob_get_clean();

		if (\tools::sendMail($userMail, \app::$config['mail']['adminMail'], \app::$config['mail']['adminMail'], 'Password reset', $body)) {
			return '1';
		} else {
			return '0';
		}
	}

}

?>