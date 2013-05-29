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
 * @copyright  Julien Gras et Benoît Lorillot
 * @version  Release: 1.0
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

        /** @var @static array contains all configs */
        public static $config = array();

        /** @var @static array contains translations */
        public static $lang = array();

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

            /* Load  general configs */
            include('config.php');

            /* Determine the domain www.Domain Name */
            if ($this->determineMultiSite($config['domain']['multisite'], $config['domain']['sld'])) { // if we find the domain
                set_include_path('.' . PATH_SEPARATOR . './' . PROFILE_PATH . PATH_SEPARATOR . './modules/' . PATH_SEPARATOR . './profiles/'.PROFILE.'/modules/'.$config['modules']['default'].'/' . PATH_SEPARATOR . './modules/'.$config['modules']['default'].'/'); // set include path

                /* Load Profile configs */
                if (PROFILE != 'www')
                    include('profiles/'.PROFILE . '/config.php');

                self::$config = $config;
                
                /* Check if it's a file */
                if ($this->sendFile() === FALSE) {
                    
                    /* If it isn't a file, Parsimony will search and display the good page */
                    define('BASE_PATH', $config['BASE_PATH']);
                    define('PREFIX', $config['db']['prefix']);
                    
                    /* Init autoload */
                    spl_autoload_register('\core\classes\app::autoLoad');
                    
                    /* Init active modules - set class_alias  */
                    class_alias('core\classes\app','app');
                    class_alias('core\classes\module', 'module');
                    $this->launchActiveModules();
                   
                    /* Init request and response */
                    self::$request = new request();
                    self::$response = new response();

                    /* Dispatch Request and display response */
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
            $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
            $host = strtolower(trim(s($host)));
            if (!(bool) $multi) {
                define('DOMAIN', str_replace('www.', '', $host));
                define('PROFILE', 'www');
            } elseif (strstr($host, '.') !== FALSE) {
                $host = explode('.', $host, substr_count($host, '.') + 2 - $nbtld);
                if (count($host) == 1)
                    array_unshift($host, 'www');
                define('DOMAIN', array_pop($host));
                define('PROFILE', implode('.', $host));
            }else {
                define('DOMAIN', $host);
                define('PROFILE', 'www');
            }
            define('PROFILE_PATH', 'profiles/' . PROFILE . '/modules/');
            if (is_dir(PROFILE_PATH))
                return TRUE;
            return FALSE;
        }

        /**
         * Call the onLoad method of each active modules
         */
        protected function launchActiveModules() {
            foreach (self::$config['modules']['active'] as $moduleName => $type) {
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
                    if ($path !== FALSE) {
                        $gmtime = gmdate('D, d M Y H:i:s T', filemtime($path));
                        /* We use native functions for perfs purposes */
                        header('HTTP/1.1 200 OK', true, 200);
                        header('Last-Modified: ' . $gmtime);
                        header('Expires: ' .  gmdate('D, d M Y H:i:s', time() + self::$config ['cache']['max-age']) . ' GMT');
                        header('Cache-Control: ' .  self::$config['cache']['cache-control'] . ';max-age=' . self::$config['cache']['max-age']);
                        header('Content-type: ' . self::$mimeTypes[$ext]);
                        echo file_get_contents($path, FILE_USE_INCLUDE_PATH);
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
        public static function errorHandler($code, $message, $file, $line) {
            if(error_reporting() == 0){
                return true; // continue script execution
            }
            /* convert errors ( E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE) in exceptions */ 
            throw new \ErrorException($message, $code, $code, $file, $line);
        }

        /**
         * Catch fatal error
         * @static function
         */
        public static function errorHandlerFatal() {

            $lastError = error_get_last();
            if(isset($lastError['type'])){ // for error type :  1, 4, 256
		$code = $lastError['type'];
		$file = $lastError['file'];
		$line = $lastError['line'];
		$message = $lastError['message'];
                self::errorLog($lastError['type'], $lastError['file'], $lastError['line'], $lastError['message']);
                if (isset($_SESSION['behavior']) && $_SESSION['behavior'] == 2) {
                    if (ob_get_level()) ob_clean();
                    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        echo json_encode(array('notification' =>  $message.' in '.$file.' '.t('in line').' '. $line, 'notificationType' => 'negative'));
                    } else {
                        include(\app::$config['DOCUMENT_ROOT'] . '/modules/core/views/desktop/error.php');
                    }
                }
            }
            exit;
        }

        /**
         * Catch exceptions and log them
         * @static function
         * @param exception object $e
         */
        public static function exceptionHandler($e) { 
            self::errorLog($e->getCode(), $e->getFile(), $e->getLine(), $e->getMessage());
            return TRUE; // continue script execution
        }

        /**
         * Log errors and display it if you are admin
         * @static function
         * @param integer $code
         * @param string $file
         * @param integer $line
         * @param string $message
         */
        public static function errorLog($code, $file, $line, $message) {
            self::dispatchEvent('error', array($code, $file, $line, $message));
            
            /* Log error */
            if (is_file(\app::$config['DOCUMENT_ROOT'] . '/modules/core/errors.log'))
                file_put_contents(\app::$config['DOCUMENT_ROOT'] . '/modules/core/errors.log', $message.'-||-'.$file.'-||-'. $line , FILE_APPEND);

        }
        
        /**
        * Type MIME
        */
       static public $mimeTypes = array(
           'txt' => 'text/plain',
           'htm' => 'text/html',
           'html' => 'text/html',
           'php' => 'text/html',
           'css' => 'text/css',
           'js' => 'application/x-javascript',
           'json' => 'application/json',
           'xml' => 'application/xml',
           'swf' => 'application/x-shockwave-flash',
           'flv' => 'video/x-flv',
           // images
           'png' => 'image/png',
           'jpe' => 'image/jpeg',
           'jpeg' => 'image/jpeg',
           'jpg' => 'image/jpeg',
           'gif' => 'image/gif',
           'bmp' => 'image/bmp',
           'ico' => 'image/vnd.microsoft.icon',
           'tiff' => 'image/tiff',
           'tif' => 'image/tiff',
           'svg' => 'image/svg+xml',
           'svgz' => 'image/svg+xml',
           // archives
           'zip' => 'application/zip',
           'rar' => 'application/x-rar-compressed',
           'exe' => 'application/x-msdownload',
           'msi' => 'application/x-msdownload',
           'cab' => 'application/vnd.ms-cab-compressed',
           // audio/video
           'mp3' => 'audio/mpeg',
           'qt' => 'video/quicktime',
           'mov' => 'video/quicktime',
           // adobe
           'pdf' => 'application/pdf',
           'psd' => 'image/vnd.adobe.photoshop',
           'ai' => 'application/postscript',
           'eps' => 'application/postscript',
           'ps' => 'application/postscript',
           // ms office
           'doc' => 'application/msword',
           'rtf' => 'application/rtf',
           'xls' => 'application/vnd.ms-excel',
           'ppt' => 'application/vnd.ms-powerpoint',
           // open office
           'odt' => 'application/vnd.oasis.opendocument.text',
           'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
       );

    }

}

namespace {

    /**
     *  These 2 functions are the only procedural functions of Parsimony 
     *  
     */
    if (isset($_SESSION['behavior']) && $_SESSION['behavior'] == 2) {

        function t($text, $modAdmin = TRUE) {
            $before = '';
            $after = '';
            if ($modAdmin != false) {
                $before = '<span data-key="' . $text . '" class="translation">';
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