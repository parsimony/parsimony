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

namespace core\classes {

    /**
     *  App Class 
     *  Is the instanciation of the site
     *  Loads HTTP request & response, manage errors & multiSite option
     */
    class app {

        /** @var @static request object HTTP request */
        public static $request;

        /** @var @static response object HTTP response */
        public static $response;

        /** @var @static integer */
        public static $timestart;

        /** @var @static array contains all configs */
        public static $config = array();

        /** @var @static array contains traductions */
        public static $lang = array();

        /** @var @static array contains all active modules */
        public static $activeModules = array();

        /** @var @static array contains all devices */
        public static $devices = array();

        /** @var @static array contains all alias of classes */
        public static $aliasClasses = array();

        /** @var @static array contains all modules */
        private static $modules = array();

        /** @var @static array contains all classes */
        private static $classes = array();

        /** @var @static array contains all listeners */
        private static $listeners = array();

        /**
         * Bootstrap of the app
         */
        public function __construct() {
            self::$timestart = microtime(true);

            /* Load  general configs */
            include('config.php');

	    define('BASE_PATH',$config['BASE_PATH']);
	    
            /* Determine the domain www.Domain Name */
            if ($this->determineMultiSite($config['domain']['multisite'], $config['domain']['sld'])) { // if we find the domain
                set_include_path('.' . PATH_SEPARATOR . './' . PROFILE_PATH . PATH_SEPARATOR . './modules/' . PATH_SEPARATOR . './modules/core/'); // set include path

                /* Load general configs */
                if (PROFILE != 'www')
                    include(PROFILE_PATH . 'config.php');
                self::$activeModules = $config['activeModules'];
                self::$config = $config;

                class_alias('core\classes\response', 'response');
                self::$response = new response();

                /* Check if it's a file */
                if (!$this->sendFile()) {
                    class_alias('core\classes\module', 'module');
                    $this->launchActiveModules();

                    self::$request = new request();

                    /* Dispatch Request in case of HTTP Response */
                    self::$request->dispatch();
                    echo self::$response->getContent();
                }
            }
        }

        /**
         * Determine MULTI SITE
         * @param $multi
         * @param $nbtld
         * @return bool
         */
        protected function determineMultiSite($multi = FALSE, $nbtld = FALSE) {
            if (isset($_SERVER['HTTP_HOST'])) {
                $_SERVER['HTTP_HOST'] = strtolower(trim($_SERVER['HTTP_HOST']));
                if (!(bool) $multi) {
                    define('DOMAIN', str_replace('www.', '', $_SERVER['HTTP_HOST']));
                    $profile = 'www';
                } elseif (strstr($_SERVER['HTTP_HOST'], '.') !== FALSE) {
                    $host = explode('.', $_SERVER['HTTP_HOST'], substr_count($_SERVER['HTTP_HOST'], '.') + 2 - $nbtld);
                    if (count($host) == 1)
                        array_unshift($host, 'www');
                    define('DOMAIN', array_pop($host));
                    $profile = implode('.', $host);
                }else {
                    define('DOMAIN', $_SERVER['HTTP_HOST']);
                    $profile = 'www';
                }
                define('PROFILE', $profile);
                define('PROFILE_PATH', 'profiles/' . PROFILE . '/modules/');
                if (is_dir(PROFILE_PATH))
                    return TRUE;
            }
            return FALSE;
        }

        /**
         * Call the onLoad method of each active modules
         */
        protected function launchActiveModules() {
            foreach (self::$activeModules as $moduleName => $type) {
                if ($type == 1) {
		    self::$modules[$moduleName] = \module::get($moduleName);
                }
            }
        }

        /**
         * Test if it's a file and send it to visitor
         * @return bool
         */
        protected function sendFile() {
            if (!empty($_GET['parsiurl'])) {
                $ext = pathinfo($_GET['parsiurl'], PATHINFO_EXTENSION);
                if ($ext && strstr(',' . self::$config['extensions_auth'] . ',', ',' . $ext . ',')) {
                    $path = stream_resolve_include_path($_GET['parsiurl']);
                    if ($path) {
                        $gmtime = gmdate('D, d M Y H:i:s T', filemtime($path));
                        self::$response->setHeader('Last-Modified', $gmtime);
                        self::$response->setHeader('Expires', gmdate('D, d M Y H:i:s', time() + self::$config ['cache']['max-age']) . ' GMT');
                        self::$response->setHeader('Cache-Control', self::$config ['cache']['cache-control'] . ';max-age=' . self::$config ['cache']['max-age']);
                        self::$response->setHeader('Content-type', response::$mimeTypes[$ext]);
                        $content = file_get_contents($path, FILE_USE_INCLUDE_PATH);
                        echo self::$response->setContent($content);
                        return TRUE;
                    }
                }
            }
            return FALSE;
        }

        /**
         * Provide autoload classes
         * @param string $className
         */
        public static function autoLoad($className) {
            if (isset(self::$aliasClasses[$className])) {
                if (!class_exists(self::$aliasClasses[$className], false))
                    include('modules/' . str_replace('\\', '/', self::$aliasClasses[$className]) . '.php');
                class_alias(self::$aliasClasses[$className], $className);
            }else {
                $className = str_replace('\\', '/', $className);
                if (strstr($className, '/blocks/')) {
                    include('modules/' . $className . '/block.php');
                } elseif (is_file('modules/' . $className . '.php')) {
                    include('modules/' . $className . '.php');
                }
            }
        }

        /**
         * Get a module and cache the instance
         * @static function
         * @param string $module
         * @return module object
         */
        public static function setDevice(array $device) {
            array_unshift(self::$devices, $device);
        }

        /**
         * Get a module and cache the instance
         * @static function
         * @param string $module
         * @return module object
         */
        public static function getModule($module) {
            if (isset(self::$modules[$module])) {
                return self::$modules[$module];
            } else {
                self::$modules[$module] = \module::get($module);
                return self::$modules[$module];
            }
        }

        /**
         * Get a class and cache the instance
         * @static function
         * @param string $classe
         * @param array $args optional
         * @todo $args
         */
        public static function getClass($classe, $args = FALSE) {
            if (isset(self::$classes[$classe])) {
                return self::$classes[$classe];
            } else {
                self::$classes[$classe] = new $classe;
                return self::$classes[$classe];
            }
        }

        /**
         * Add a Listener
         * @static function
         * @param string $eventName
         * @param array|string $callback optional
         */
        public static function addListener($eventName, $callback = array()) {
            self::$listeners[$eventName][] = $callback;
        }

        /**
         * Add a Listener
         * @static function
         * @param string $eventName
         * @param array|string $callback optional
         */
        public static function deleteListener($eventName) {
            if (isset(self::$listeners[$eventName]))
                unset(self::$listeners[$eventName]);
        }

        /**
         * Dispatch an event
         * @static function
         * @param string $eventName
         * @param array $data optional
         */
        public static function dispatchEvent($eventName, $data = array()) {
            if (!empty(self::$listeners[$eventName])) {
                foreach (self::$listeners[$eventName] as $callback) {
                    call_user_func_array($callback, $data);
                }
            }
        }

        /**
         * Catch errors and convert them in exception
         * @static function
         * @param integer $errno 
         * @param string $errstr
         * @param string $errfile
         * @param integer $errline
         */
        public static function errorHandler($errno, $errstr, $errfile, $errline) {
            self::errorLog($errno, $errfile, $errline, $errstr);
        }

        /**
         * Catch fatal error
         * @static function
         */
        public static function errorHandlerFatal() {
            $lastError = error_get_last();
            if ($lastError['type'] == 1 || $lastError['type'] == 4 || $lastError['type'] == 256)
                self::errorLog($lastError['type'], $lastError['file'], $lastError['line'], $lastError['message']);
        }

        /**
         * Catch exceptions and log them
         * @static function
         * @param exception object $e
         */
        public static function exceptionHandler($e) {
            self::errorLog($e->getCode(), $e->getFile(), $e->getLine(), $e->getMessage());
        }

        /**
         * Log errors and display it if you are admin
         * @static function
         * @param integer $code
         * @param string $file
         * @param integer $line
         * @param string $message
         */
        protected static function errorLog($code, $file, $line, $message) {
            $trace = $code . ' - ' . $file . ' - ' . $line . ' - ' . $message . PHP_EOL;
            $root = realpath($_SERVER['DOCUMENT_ROOT']) . BASE_PATH;
            if (is_file($root . 'modules/core/errors.log'))
                file_put_contents($root . 'modules/core/errors.log', $trace, FILE_APPEND);
            self::dispatchEvent('error', array($code, $file, $line, $message));
            if (!isset($_SESSION['idr']) || $_SESSION['idr'] == 1) {
                if (isset($_POST['action'])) {
                    if (ob_get_level()) ob_clean();
                    echo json_encode(array('notification' => $file . ' : ' . $message . ' in line ' . $line, 'notificationType' => 'negative'));
                } else {
                    include($root . 'modules/core/views/web/error.php');
                }
                exit;
            }
        }

    }

}

namespace {

    /**
     *  These 2 functions are the only procedural functions of Parsimony 
     *  
     */
    if (isset($_SESSION['roleBehavior']) && $_SESSION['roleBehavior'] == 2) {

        function t($text, $modAdmin = TRUE) {
            $before = '';
            $after = '';
            if ($modAdmin != false) {
                $before = '<span data-key="' . $text . '" class="traduction">';
                $after = '</span>';
            }
            if (isset(app::$lang[$text])) {
                if (is_array($modAdmin))
                    return $before . vsprintf(app::$lang[$text], $modAdmin) . $after;
                else
                    return $before . app::$lang[$text] . $after;
            } else {
                if (is_array($modAdmin))
                    return $before . vsprintf($text, $modAdmin) . $after;
                else
                    return $before . $text . $after;
            }
        }

    } else {

        function t($text, $params = FALSE) {
            if (isset(app::$lang[$text])) {
                if ($params)
                    return vsprintf(app::$lang[$text], $params);
                else
                    return app::$lang[$text] ;
            }else {
                if ($params)
                    return vsprintf($text, $params);
                else
                    return $text;
            }
        }

    }

    function s($text) {
        return htmlentities($text, ENT_QUOTES | ENT_IGNORE, 'utf-8');
    }

}