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

namespace core\classes{

/**
 *  Response Class 
 *  Manages HTTP Response 
 */
class response {

	/**
	 * @var integer status of HTTP response
	 */
	protected $status = 200;

	/**
	 * @var string format of HTTP response
	 */
	protected $format = 'html';

	/**
	 * @var string charset of HTTP response
	 */
	protected $charset = 'utf-8';

	/**
	 * @var array of headers 
	 */
	protected $headers = array();

	/** 
	 * @var string $body 
	 */
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
			
			\app::dispatchEvent('beforePageLoad');
			
			$theme = $this->page->getTheme();
			
			if ($theme instanceof theme) {
				define('THEMEMODULE', $theme->getModule());
				define('THEME', $theme->getName());
				$this->page->addCSSFile(THEMEMODULE . '/themes/' . THEME . '/' . THEMETYPE . '/style.css');
				$body = $theme->display(); /* Display with theme */
			} else{
				define('THEMEMODULE', '');
				define('THEME', '');
				$body = $body->display(); /* Display without theme */
			}
			
			/* Set page infos to admin */
			if (!defined('PARSI_ADMIN') && $_SESSION['behavior'] > 0 && \app::$request->getParam('popup') !== '') {
				$timer = isset($_SERVER['REQUEST_TIME_FLOAT']) ? round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 4) : '~ ' . floor(microtime(true) - $_SERVER['REQUEST_TIME']);

				/* Store on client side all CSS selectors from theme style */
				$pathTheme = THEMEMODULE . '/themes/' . THEME . '/' . THEMETYPE . '/style.css';
				$css = new css(PROFILE_PATH . $pathTheme);
				$CSSValues = $css->getCSSValues();

				if ($_SESSION['behavior'] === 2) {
					$script = 'top.document.getElementById("infodev_timer").textContent="' . $timer . ' s";top.document.getElementById("infodev_module").textContent="' . MODULE . '";top.document.getElementById("infodev_theme").textContent="' . THEME . '";top.setActiveTheme("' . THEME . '");top.document.getElementById("infodev_page").textContent="' . $this->page->getId() . '";';
				}
				$body .= '<script>top.history.replaceState({url:document.location.pathname}, document.title, document.location.pathname.replace("?preview=ok","").replace("preview=ok",""));top.$_GET=' . json_encode($_GET) . ';top.$_POST=' . json_encode($_POST) . ';top.CSSTHEMEPATH = "' . $pathTheme . '";top.CSSPAGEPATH = "' . MODULE . '/css/' . THEMETYPE . '.css";top.ParsimonyAdmin.CSSValues = ' . json_encode(array($pathTheme => $CSSValues)) . ';' . $script . 'document.addEventListener("DOMContentLoaded", function() {top.ParsimonyAdmin.initPreview();});  </script>';
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
	if ($_SESSION['behavior'] === 2) {

		function t($text, $params = FALSE) {
			$before = '';
			$after = '';
			if (isset($_GET['preview']) && \app::$response->page instanceof page) {
				$before = '<span data-key="' . $text . '" class="translation">';
				$after = '</span>';
			}
			if (isset(app::$lang[$text])) {
				if ($params !== FALSE)
					return $before . vsprintf(app::$lang[$text], $params) . $after;
				else
					return $before . app::$lang[$text] . $after;
			} else {
				if ($params !== FALSE)
					return $before . vsprintf($text, $params) . $after;
				else
					return $before . $text . $after;
			}
		}

	} else {

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

}
