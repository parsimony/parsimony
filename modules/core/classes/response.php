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

namespace core\classes{

/**
 *  Response Class 
 *  Manages HTTP Response 
 */
class response {

	/** @var integer status of HTTP response */
	protected $status = 200;

	/** @var string format of HTTP response */
	protected $format = 'html';

	/** @var string charset of HTTP response */
	protected $charset = 'utf-8';

	/** @var array of headers */
	protected $headers = array();
	
	/** @var string */
	private $includes = array('header' => array('css' => array('http' => array(), 'local' => array()), 'js' => array('http' => array(), 'local' => array())), 'footer' => array('css' => array('http' => array(), 'local' => array()), 'js' => array('http' => array(), 'local' => array())));

	/** @var string */
	public $head = '';

	/** @var string $body */
	protected $body = '';
	
	/** @var page Page object */
	public $page;

	/**
	 * Get content to client
	 * @param mixed $body optional
	 */
	 public function getContent() {
		return $this->body;
	 }

	/**
	 * Send content to client
	 * @param mixed $body optional
	 */
	public function setContent($body = '', $status = FALSE) {
		
		if($status !== FALSE){
			$this->setStatus($status);
		}

		if ($body instanceof page) { /* If it's a page object */

			$this->page = $body; /* Save page object */
			
			/* Init defaults JS and CSS for CMS pages */
			$this->addJSFile('core/js/parsimony.js');
			$this->addCSSFile('core/css/parsimony.css');
			
			\app::dispatchEvent('beforePageLoad');

			$theme = $this->page->getTheme();
			
			if ($theme instanceof theme) {
				define('THEMEMODULE', $theme->getModule());
				define('THEME', $theme->getName());
				$this->addCSSFile(THEMEMODULE . '/themes/' . THEME . '/style.css');
				$body = $theme->display(); /* Display with theme */
			} else {
				define('THEMEMODULE', '');
				define('THEME', '');
				$body = $body->display(); /* Display without theme */
			}
			
			/* Set page infos to admin */
			if (!defined('PARSI_ADMIN') && $_SESSION['permissions'] > 0 && \app::$request->getParam('popup') !== '') {
				$timer = isset($_SERVER['REQUEST_TIME_FLOAT']) ? round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 4) : '~ ' . floor(microtime(true) - $_SERVER['REQUEST_TIME']);

				/* Be sure that editiniline can be used in edit mode */
				\app::$response->addJSFile('core/js/editinline.js');
				\app::$response->addCSSFile('core/css/editinline.css');
				
				/* correct resMax with preview screen */
				\app::$response->head .= '<script>document.cookie = "DW=" + (top.ParsimonyAdmin.getCookie("screenX") || screen.width) + ";path=/";document.cookie = "DH=" + (top.ParsimonyAdmin.getCookie("screenY") || screen.height)  + ";path=/";</script>';
				
				$body .= '<script>top.document.getElementById("infodev_timer").textContent="' . $timer . ' s";top.document.getElementById("infodev_module").textContent="' . MODULE . '";top.document.getElementById("infodev_theme").textContent="' . THEME . '";top.setActiveTheme("' . THEME . '");top.document.getElementById("infodev_page").textContent="' . $this->page->getId() . '";';

				$pathTheme = THEMEMODULE . '/themes/' . THEME . '/style.css';
				
				if ($_SESSION['permissions'] & 16) {
					/* Store on client side all CSS selectors from theme style */
					$css = new css(stream_resolve_include_path($pathTheme));
					$CSSValues = $css->getCSSValues();
					$body .= 'top.Parsimony.blocks["admin_css"].CSSValues["' . $pathTheme . '"] = ' . json_encode($CSSValues) . ';';
				}
				$body .= 'top.history.replaceState({url:document.location.pathname}, document.title, document.location.pathname.replace("?preview=ok","").replace("preview=ok",""));top.$_GET=' . json_encode($_GET) . ';top.$_POST=' . json_encode($_POST) . ';top.CSSTHEMEPATH = "' . $pathTheme . '";top.CSSPAGEPATH = "' . MODULE . '/css/' . DEVICE . '.css";document.addEventListener("DOMContentLoaded", function() {top.ParsimonyAdmin.initPreview();});  </script>';
			}
			
			\app::dispatchEvent('afterPageLoad');
			
			/* Wrap body with HTML structure */
			ob_start();
			include('core/views/index.php');
			$body = ob_get_clean();
			
			\app::dispatchEvent('beforePageDisplay', array(&$body)); /* allow to process files before they are sent to the client */
			
		}

		/* Set headers */
		header($_SERVER['SERVER_PROTOCOL'] . ' ' . $this->status . ' ' . self::$HTTPstatus[$this->status], true, $this->status);
		header('Content-type: ' . \app::$config['ext'][$this->format] . '; charset=' . $this->charset);
		header('Vary: User-Agent'); /* Dynamically serving different HTML on the same URL */
		foreach ($this->headers AS $label => $header) {
			header($label . ': ' . $header);
		}
		
		/* Set body to client */
		$this->body = $body;
	}

	/**
	 * Set HTTP status
	 * @param integer $status
	 */
	public function setStatus($status) {
		if (isset(self::$HTTPstatus[$status]))
			$this->status = $status;
		else
			throw new \Exception(t('Parsimony doesn\'t know this HTTP status', FALSE));
	}

	/**
	 * Get HTTP status
	 * @return integer
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * Set format of response
	 * @param string $format
	 */
	public function setFormat($format) {
		if (isset(\app::$config['ext'][$format]))
			$this->format = $format;
		else
			throw new \Exception(t('Parsimony doesn\'t know this extension', FALSE));
	}

	/**
	 * Get format of response
	 * @return string
	 */
	public function getFormat() {
		return $this->format;
	}

	/**
	 * Set header of response
	 * @param string $label
	 * @param string $head
	 */
	public function setHeader($label, $head) {
		$this->headers[$label] = $head;
	}

	/**
	 * Set header of response
	 * @param string $label
	 * @return string
	 */
	public function getHeader($label) {
		return $this->headers[$label];
	}
	
	/**
	 * Concat JS or CSS Files
	 * @param array $module
	 */
	public function concatFiles(array $files, $format) {
		$hash = $format . 'concat_' . md5(implode('', $files));
		$pathCache = 'profiles/' . PROFILE . '/modules/' . app::$config['defaultModule'] . '/' . $hash . '.' . $format;
		$dltCache = '';
		if (!is_file($pathCache) || app::$config['dev']['status'] !== 'prod') {
			ob_start();
			foreach ($files as $file) {
				$pathParts = pathinfo($file, PATHINFO_EXTENSION);
				if ($pathParts === 'js' || $pathParts === 'css') {
					$path = stream_resolve_include_path($file);
					if ($path){
						if ($_SESSION['permissions'] & 16 && $pathParts === 'css')
							echo '.parsimonyMarker{background-image: url(' . $file . ') }' . PHP_EOL;
						include($path);
					}
					echo PHP_EOL; //in order to split JS script and avoid "}function"
				}else {
					return FALSE;
				}
			}
			$content = ob_get_clean();
			\tools::createDirectory(dirname($pathCache));
			file_put_contents($pathCache, $content);
			$dltCache = '?' . time();
		}
		return $hash . '.' . $format . $dltCache;
	}
	
	
	/**
	 * Get inclusions
	 * @return string
	 */
	public function getInclusions($position = 'header') {
		return $this->includes[$position];
	}

	/**
	 * Get HTML inclusions
	 * @return string
	 */
	public function printInclusions($position = 'header') {
		$html = PHP_EOL;
		if (!empty($this->includes[$position]['css']['http']))
			$html .= PHP_EOL . "\t\t" . '<link rel="stylesheet" type="text/css" href="' . implode('" /><link rel="stylesheet" type="text/css" href="', $this->includes[$position]['css']['http']) . '" />';
		if (!empty($this->includes[$position]['css']['local']))
			$html .= PHP_EOL . "\t\t" . '<link rel="stylesheet" type="text/css" href="' . BASE_PATH . $this->concatFiles($this->includes[$position]['css']['local'], 'css') . '" />';
		if (!empty($this->includes[$position]['js']['http']))
			$html .= PHP_EOL . "\t\t" . '<SCRIPT type="text/javascript" SRC="' . implode('"> </SCRIPT><SCRIPT type="text/javascript" SRC="', $this->includes[$position]['js']['http']) . '"> </SCRIPT>';
		if (!empty($this->includes[$position]['js']['local']))
			$html .= PHP_EOL . "\t\t" . '<SCRIPT type="text/javascript" SRC="' . BASE_PATH . $this->concatFiles($this->includes[$position]['js']['local'], 'js') . '"> </SCRIPT>' . PHP_EOL;
		return $html;
	}
	
	
	/**
	 * Add CSS File to includes
	 * @param string $position header or footer
	 * @param string $cssFile
	 */
	public function addCSSFile($cssFile, $position = 'header') {
		$type = 'local';
		if (strstr($cssFile, '//')) {
			$type = 'http';
		}
		if (!in_array($cssFile, $this->includes[$position]['css'][$type])) {
				$this->includes[$position]['css'][$type][] = $cssFile;
		}
	}

	/**
	 * Get CSS Files included
	 * @param string $position header or footer
	 * @return array
	 */
	public function getCSSFiles($position = 'header') {
		return array_merge($this->includes[$position]['css']['http'], $this->includes[$position]['css']['local']);
	}

	/**
	 * Add Javascript File to includes
	 * @param string $position header or footer
	 * @param string $jsFile
	 */
	public function addJSFile($jsFile, $position = 'header') {
		$type = 'local';
		if (strstr($jsFile, '//')) {
			$type = 'http';
		}
		if (!in_array($jsFile, $this->includes[$position]['js'][$type])){
			$this->includes[$position]['js'][$type][] = $jsFile;
		}
	}

	/**
	 * Get Javascript Files included
	 * @param string $position header or footer
	 * @return array
	 */
	public function getJSFiles($position = 'header') {
		return array_merge($this->includes[$position]['js']['http'], $this->includes[$position]['js']['local']);
	}

	/**
	 * HTTP status codes
	 * array of status
	 */
	static public $HTTPstatus = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		118 => 'Connexion timed out',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		310 => 'Too Many Redirect',
		324 => 'Empty Response',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		426 => 'Upgrade Required',
		428 => 'Precondition Required',
		429 => 'Too Many Requests',
		431 => 'Request Header Fields Too Large',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		509 => 'Bandwidth Limit Exceeded',
		511 => 'Network Authentication Required'
	);

}
}
namespace {

	/**
	 *  t() must bedefined here
	 */

	function t($text, $params = FALSE) {
		if (isset(app::$lang[$text])) {
			if ($params !== FALSE)
				return vsprintf(app::$lang[$text], $params);
			else
				return app::$lang[$text];
		}else {
			if ($params !== FALSE)
				return vsprintf($text, $params);
			else
				return $text;
		}
	}

}
