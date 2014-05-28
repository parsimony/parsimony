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
		
		/** @var @static array contains all active modules */
		public static $activeModules = array();

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

			/* Determine called profile */
			$this->determineProfile();
			
			/* Load Profile configs before include path get the good default module */
			include('profiles/' . PROFILE . '/config.php');
			self::$config = $config;

			/* set include path with profile before module to allow profile's files to override module's files */
			set_include_path('.' . PATH_SEPARATOR . './' . PROFILE_PATH . PATH_SEPARATOR . './modules/' . PATH_SEPARATOR . './profiles/' . PROFILE . '/modules/' . $config['defaultModule'] . '/' . PATH_SEPARATOR . './modules/' . $config['defaultModule'] . '/');

			/* Check if it's a file */
			if ($this->sendFile($_GET['parsiurl']) === FALSE) {

				/* If it isn't a file, Parsimony will search and display the good page */
				define('BASE_PATH', $config['BASE_PATH']);
				define('PREFIX', $config['db']['prefix']);

				/* Init autoload */
				spl_autoload_register('\core\classes\app::autoLoad');

				/* Init active modules - set class_alias */
				class_alias('core\classes\app', 'app');
				class_alias('core\classes\module', 'module');
				$this->launchActiveModules();

				/* Init request and response */
				self::$request = new request($_GET['parsiurl']);
				self::$response = new response();

				/* Dispatch Request and display response */
				self::$request->dispatch();
				echo self::$response->getContent();

			}
		}

		/**
		 * Determine Profile
		 */
		private function determineProfile() {
			$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']; /* SERVER_NAME for HTTP 1.0 */
			$cut = explode('.', strtolower($host));
			if (count($cut) > 2 && is_file('profiles/' . $cut[0] . '/config.php') === TRUE) { /*  Profile must have a general config file, also to avoid pb with .co.uk ext */ 
				define('PROFILE', $cut[0]);
				unset($cut[0]);
				define('DOMAIN', s(implode('.', $cut)));
			} else {
				define('PROFILE', 'www');
				define('DOMAIN', s($host));
			}
			define('PROFILE_PATH', 'profiles/' . PROFILE . '/modules/');
		}

		/**
		 * Call the onLoad method of each active modules
		 */
		protected function launchActiveModules() {
			self::$activeModules = array_filter(self::$config['modules']);
			foreach (self::$activeModules as $moduleName => $type) {
				if ($type & 1) {
					self::$modules[$moduleName] = \module::get($moduleName);
				}
			}
		}

		/**
		 * Test if it's a file and send it to visitor
		 * @return bool
		 */
		protected function sendFile($path) {
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			if (isset(self::$config['ext'][$ext])) {
				$path = stream_resolve_include_path($path);
				if ($path !== FALSE) {
					if(count($_GET) > 1 && in_array($ext, array('png', 'jpg', 'jpeg', 'gif'))){ /* if it's an image and there is more than parsiurl param */
						$path = $this->imageFile($path);
					}
					$gmtime = gmdate('D, d M Y H:i:s T', filemtime($path));
					/* We use native functions for perfs purposes */
					header('HTTP/1.1 200 OK', true, 200);
					header('Last-Modified: ' . $gmtime);
					header('Expires: ' . gmdate('D, d M Y H:i:s', time() + self::$config ['cache']['max-age']) . ' GMT');
					header('Cache-Control: ' . self::$config['cache']['cache-control'] . ';max-age=' . self::$config['cache']['max-age']);
					header('Content-type: ' . self::$config['ext'][$ext]);
					echo file_get_contents($path);
					return TRUE;
				}
			}
			return FALSE;
		}
		
		/**
		 * Process images
		 * @return string
		 */
		protected function imageFile($path) {
			$params = array_intersect_key($_GET, array('x' => '', 'y' => '', 'adapt' => '', 'crop' => ''));
			$params['path'] = $path;
			
			/* Adaptive imgs */
			$resMax = 0;
			if (isset($params['adapt']) && isset($_COOKIE['resMax'])) {
				$resMax = $params['adapt'] = ceil($_COOKIE['resMax'] / 100) * 100; /* to limit amount of cached images versions */
				if (isset($params['x']) && $params['x'] > $resMax) {
					$params['x'] = $resMax;
				}
			}
			
			$cachePath = 'var/cache/' . str_replace('/', '_',http_build_query($params, '', '_')); /* get only allowed vars and secure generated path */
			if (!is_file($cachePath)) { /* if cache doesn't exists */
				include('modules/core/classes/img.php');
				$img = new img($path);

				if (isset($params['x']) && isset($params['y'])) {
					if (isset($params['crop'])) {
						$img->crop($params['x'], $params['y']);
					} else {
						$img->resize($params['x'], $params['y']);
					}
				} elseif ($resMax > 0) { /* If there isn't x and y params we resize img to max Resolution of user's screen */
					$img->resize($resMax, 9999);
				}
				$img->save($cachePath, 80);
			}
			return $cachePath;
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
		public static function removeListener($eventName) {
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
			$GLOBALS['lastError'] = array('type' => $code, 'message' => $message, 'file' => $file, 'line' => $line);
			/* convert errors ( E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE) in exceptions */ 
			throw new \ErrorException($message, $code, $code, $file, $line);
		}

		/**
		 * Catch fatal error
		 * @static function
		 */
		public static function errorHandlerFatal() {
			$lastError = isset($GLOBALS['lastError']) && is_array($GLOBALS['lastError']) ? $GLOBALS['lastError'] : error_get_last();
			if(isset($lastError['type'])){ // for error type :  1, 4, 256
				$code = $lastError['type'];
				$file = $lastError['file'];
				$line = $lastError['line'];
				$message = $lastError['message'];
				self::errorLog($lastError['type'], $lastError['file'], $lastError['line'], $lastError['message']);{
				if (isset($_SESSION['permissions']) && $_SESSION['permissions'] > 0) 
					if (ob_get_level()) ob_clean();
					if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
						echo json_encode(array('notification' => $message.' in '.$file.' '.t('in line').' '. $line, 'notificationType' => 'negative'));
					} else {
						include(DOCUMENT_ROOT . '/modules/core/views/error.php');
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
			if (is_dir(DOCUMENT_ROOT . '/var'))
				file_put_contents(DOCUMENT_ROOT . '/var/errors.log', $message.' - in - '.$file.' - on line - '. $line.PHP_EOL , FILE_APPEND);

		}

	}

}

namespace {

	function s($text) {
		return htmlentities($text, ENT_QUOTES | ENT_IGNORE, 'utf-8');
	}

}