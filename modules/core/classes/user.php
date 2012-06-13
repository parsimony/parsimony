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
 * @package core\classes
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace core\classes;

/**
 * User Class 
 * Manage users 
 */
class user {

    protected $sessPath;

    public function __construct() {
        $this->sessPath = realpath($_SERVER['DOCUMENT_ROOT']). BASE_PATH . 'profiles' . DIRECTORY_SEPARATOR . PROFILE . DIRECTORY_SEPARATOR . 'sessions';
        ini_set('session.save_path', '4;' . $this->sessPath . '');

        ini_set('use_only_cookies', 1);
        ini_set('session.gc_maxlifetime', '86400');
        ini_set('session.cache_limiter', 'nocache');
        ini_set('session.cookie_httponly', true);
        ini_set('session.hash_function', 'sha256');
        ini_set('session.hash_bits_per_character', 5);

        if (!isset($_COOKIE['PHPSESSID'])) {
            $this->setSessionId();
        }elseif (!is_dir($this->sessPath . DIRECTORY_SEPARATOR . $_COOKIE['PHPSESSID'][0] . DIRECTORY_SEPARATOR . $_COOKIE['PHPSESSID'][1] . DIRECTORY_SEPARATOR . $_COOKIE['PHPSESSID'][2] . DIRECTORY_SEPARATOR . $_COOKIE['PHPSESSID'][3] . DIRECTORY_SEPARATOR)) {
            $this->setSessionId();
	}else{
            session_start();
	}
        if (!isset($_SESSION['time'])) {
            $_SESSION['time'] = time();
        } else {
            if (time() - $_SESSION['time'] > 300) {
                $this->setSessionId();
                $_SESSION['time'] = time();
            }
        }
    }

    protected function setSessionId() {
        $hash = hash('sha1', uniqid(mt_rand()));
        $dir = $this->sessPath . DIRECTORY_SEPARATOR . $hash[0] . DIRECTORY_SEPARATOR . $hash[1] . DIRECTORY_SEPARATOR . $hash[2] . DIRECTORY_SEPARATOR . $hash[3] . DIRECTORY_SEPARATOR;
        if (!is_dir($dir))
            mkdir($dir, 0777 , true);
        if (is_file($dir . 'sess_' . $hash))
            $this->setSessionId();
        $ttt = array();
        if (isset($_SESSION))
            $ttt = $_SESSION;
        $oldId = session_id();
        session_write_close();
        session_id($hash);
        session_start();
        $_SESSION = $ttt;
        if (!empty($oldId)) {
            $oldDir = $this->sessPath . DIRECTORY_SEPARATOR . $oldId[0] . DIRECTORY_SEPARATOR . $oldId[1] . DIRECTORY_SEPARATOR . $oldId[2] . DIRECTORY_SEPARATOR . $oldId[3] . DIRECTORY_SEPARATOR;
            if (is_file($oldDir . 'sess_' . $oldId)) {
                unlink($oldDir . 'sess_' . $oldId);
            }
        }
    }

    /**
     * Auth
     * @param string $login
     * @param string $password
     * @return bool
     */
    public static function authentication($login, $password) {
        if (\app::getModule('core')->getEntity('user')->pseudo->validate($login) === FALSE || \app::getModule('core')->getEntity('user')->pass->validate($password) === FALSE) {
            return FALSE;
        } else {
            $sth = PDOconnection::getDB()->prepare('SELECT pseudo, pass, id_user, core_role.id_role, core_role.state FROM core_user INNER JOIN core_role ON core_user.id_role = core_role.id_role where pseudo = :pseudo');
            $sth->execute(array(':pseudo' => $login));
            $obj = $sth->fetch();
            if (is_array($obj)) {
                $mdp = $obj['pass'];
                if ((string) $mdp == \sha1($password)) {
                    $_SESSION['login'] = $login;
                    $_SESSION['id_user'] = $obj['id_user'];
                    $_SESSION['idr'] = $obj['id_role'];
                    $_SESSION['roleBehavior'] = $obj['state'];
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
        setcookie(session_name(), '', time() - 3600, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        \session_destroy();
        header('location:index');
        exit;
    }

    /**
     * Reset a new password to an user and send by email
     * @param $userMail mail
     */
    public function resetPassword($userMail) {
        $newPass = substr(hash('sha1', uniqid(mt_rand())), 0, 8);
        \app::getModule('core')->getEntity('user')->where('mail = :mail')->update(array('pass' => $newPass, 'mail' => $userMail));

	$password = $newPass;
        ob_start();
        include('admin/views/mail/remdp.php');
        $body = ob_get_clean();
	
	if(\tools::sendMail($userMail, \app::$config['mail']['adminMail'], \app::$config['mail']['adminMail'], 'Password reset', $body)){
	    return '1';
	}else{
	    return '0';
	}
	
    }
    
    /**
     * Registration mail
     * @param $userMail mail
     * @param $pseudo pseudo
     */
    public function registrationMail($userMail, $pseudo) {
	$name = $pseudo;
	$password = $newPass;
        ob_start();
        include('admin/views/mail/registration.php');
        $body = ob_get_clean();
	
	if(\tools::sendMail($userMail, \app::$config['mail']['adminMail'], \app::$config['mail']['adminMail'], t('Your profile has been created with success'), $body)){
	    return '1';
	}else{
	    return '0';
	}
	
    }

}

?>