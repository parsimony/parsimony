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
 * CSS Class 
 * Manages CSS Files
 */
class css {

	/** @var string path of file */
	private $path;

	/** @var array of css selectors */
	private $selectors = array();

	/** @var array of css selectors */
	private $cursor = 0;

	public function __construct($cssPath) {

		$this->path = $cssPath;
		$css = '';
		if (is_file($cssPath)) {
			$css = file_get_contents($this->path);
		}
		$len = strlen($css);
		$ids = 0;
		$media = 0;

		$find = 'next';
		while ($len > $this->cursor) {
			if (!isset($this->selectors[$ids]['b']))
				$this->selectors[$ids]['b'] = '';
			switch ($find) {
				case 'next':

					/* Test comment */
					$comment = strpos($css, '/*', $this->cursor);
					$comment = $comment !== FALSE ? $comment : 999999999;

					/* Test selector */
					preg_match('/[\*\}#\.a-z:-\[\]]/i', substr($css, $this->cursor), $res, PREG_OFFSET_CAPTURE);
					$selector = isset($res[0][1]) ? ($this->cursor + $res[0][1]) : 999999999;

					if ($comment < $selector) {
						$this->selectors[$ids]['b'] .= substr($css, $this->cursor, ($comment - $this->cursor));
						$this->cursor = $comment;
						$find = 'closeComment';
					} else {
						/* Test for media queries */
						if (isset($res[0][0]) && substr($res[0][0], 0, 1) == '}') {
							$this->cursor = $this->cursor + 1;
							$find = 'next';
							$media = count($this->selectors);
						} else {
							$this->selectors[$ids]['b'] .= substr($css, $this->cursor, ($selector - $this->cursor));
							$this->cursor = $selector;
							$find = 'openbracket';
						}
					}

					break;

				case 'closeComment':
					$comment = strpos($css, '*/', $this->cursor);
					if ($comment) {
						$comment += 2;
						$this->selectors[$ids]['b'] .= substr($css, $this->cursor, ($comment - $this->cursor));
						$this->cursor = $comment;
						$find = 'next';
					} else {
						break 2;
					}
					break;

				case 'openbracket':

					$selector = strpos($css, '{', $this->cursor);
					if ($selector) {
						$text = trim(substr($css, $this->cursor, ($selector - $this->cursor)));
						/* Test for media queries */
						if (substr($text, 0, 6) == '@media') {
							$this->selectors[$ids]['bmedia'] = $this->selectors[$ids]['b'];
							$this->selectors[$ids]['b'] = '';
							$media = $text;
							$this->cursor = $selector + 1;
							$find = 'next';
						} else {
							if (!is_numeric($media)) {
								$sel = $this->serializeMediaQuery($media);
								$this->selectors[$ids]['media'] = $media;
							}
							else
								$sel = '';
							$sel .= $selCache = trim(substr($css, $this->cursor, ($selector - $this->cursor)));
							$this->selectors[$sel] = $this->selectors[$ids];
							unset($this->selectors[$ids]);
							$ids = $sel;
							$this->selectors[$ids]['s'] = $selCache;
							$this->cursor = $selector;
							$find = 'closebracket';
						}
					}else {
						break 2;
					}
					break;

				case 'closebracket':
					$comment = strpos($css, '}', $this->cursor);
					if ($comment) {
						$comment -= 1;
						$this->selectors[$ids]['p'] = substr($css, ($this->cursor + 1), ($comment - $this->cursor));
						$this->cursor = $comment + 2;
						$find = 'next';
						$ids = count($this->selectors);
					} else {
						break 2;
					}
					break;

				default:
					break 2;
					break;
			}
		}
		$last = end($this->selectors);
		if (!isset($last['s']))
			array_pop($this->selectors);
	}

	/**
	 * Determine if a selector exists
	 * @param string $selector
	 * @return string|false 
	 */
	public function selectorExists($selector, $media = '') {
		$media = $this->serializeMediaQuery($media);
		if (isset($this->selectors[$media . $selector])) {
			return $this->selectors[$media . $selector]['p'];
		}
		return FALSE;
	}

	/**
	 * Get all selectors
	 * @return string|false 
	 */
	public function getAllSselectors() {
		$selectors = array();
		foreach ($this->selectors as $selector) {
			$selectors[] = $selector['s'];
		}
		return $selectors;
	}

	/**
	 * Add selector in CSS file
	 * @param string $selector
	 */
	public function addSelector($selector, $media = '') {
		$new = array('b' => PHP_EOL, 's' => $selector, 'p' => PHP_EOL);
		if (!empty($media)) {
			$new['media'] = $media;
			$new['bmedia'] = PHP_EOL;
		}
		$this->selectors[$this->serializeMediaQuery($media) . $selector] = $new;
	}

	/**
	 * Delete selector in CSS file
	 * @param string $selector
	 */
	public function deleteSelector($selector, $media = '') {
		$media = $this->serializeMediaQuery($media);
		unset($this->selectors[$media . $selector]);
	}
	
	/**
	 * Delete selectors of a block
	 * @param string $blockId
	 */
	public function deleteBlockSelectors($blockId) {
		$length = strlen($blockId);
		foreach ($this->selectors as $key => $selector) {
			if(substr($selector['s'], 0, $length) === $blockId){
				unset($this->selectors[$key]);
			}
		}
	}

	/**
	 * Determine if a property exists
	 * @param string $selector
	 * @param string $property
	 * @return string|false 
	 */
	public function propertyExists($selector, $property, $media = '') {
		$media = $this->serializeMediaQuery($media);
		$this->extractSelectorRules($selector, $media);
		if (isset($this->selectors[$media . $selector]['properties'][$property])) {
			return trim($this->selectors[$media . $selector]['properties'][$property]);
		}
		return FALSE;
	}

	/**
	 * Get value of a property
	 * @param string $selector
	 * @param string $property
	 * @return string 
	 */
	public function getPropertyValue($selector, $property, $media = '') {
		$media = $this->serializeMediaQuery($media);
		return $this->propertyExists($selector, $property, $media);
	}

	/**
	 * Add a property 
	 * @param string $selector
	 * @param string $property
	 * @param string $value
	 */
	public function addProperty($selector, $property, $value, $media = '') {
		$media = $this->serializeMediaQuery($media);
		if (isset($this->selectors[$media . $selector]['p'])) {
			$this->extractSelectorRules($selector, $media);
			$this->selectors[$media . $selector]['properties'][$property] = $value;
			$this->saveSelector($selector, $media);
		}
	}

	/**
	 * Update a property 
	 * @param string $selector
	 * @param string $property
	 * @param string $newValue
	 */
	public function updateProperty($selector, $property, $newValue, $media = '') {
		$media = $this->serializeMediaQuery($media);
		if (isset($this->selectors[$media . $selector]['p'])) {
			$this->extractSelectorRules($selector, $media);
			$this->selectors[$media . $selector]['properties'][$property] = $newValue;
			$this->saveSelector($selector, $media);
		}
	}

	/**
	 * Delete a property 
	 * @param string $selector
	 * @param string $property
	 */
	public function deleteProperty($selector, $property, $media = '') {
		$media = $this->serializeMediaQuery($media);
		if (isset($this->selectors[$media . $selector]['p'])) {
			$this->extractSelectorRules($selector, $media);
			unset($this->selectors[$media . $selector]['properties'][$property]);
			$this->saveSelector($selector, $media);
		}
	}

	/**
	 * Extract CSS rules of selectors
	 * @param string $selector
	 * @return @array 
	 */
	public function extractSelectorRules($selector, $media = '') {
		$media = $this->serializeMediaQuery($media);
		if (isset($this->selectors[$media . $selector]['p'])) {
			if (!isset($this->selectors[$media . $selector]['properties'])) {
				$this->selectors[$media . $selector]['properties'] = array();
				$_selector = trim($this->selectors[$media . $selector]['p']);
				$tabsel = explode(';', $_selector);
				if (is_array($tabsel)) {
					foreach ($tabsel AS $sele) {
						if (strstr($sele, ':')) {
							list($myProperty, $myValue) = explode(':', $sele);
							if (trim($myProperty) != '')
								$this->selectors[$media . $selector]['properties'][trim($myProperty)] = trim($myValue);
						}
					}
				}
			}
			return $this->selectors[$media . $selector]['properties'];
		}
		return FALSE;
	}

	/**
	 * Save selector
	 * @param string $selector
	 * @deprecated since version 3.0
	 */
	public function saveSelector($selector, $media = '') {
		$media = $this->serializeMediaQuery($media);
		$code = PHP_EOL;
		foreach ($this->selectors[$media . $selector]['properties'] AS $property => $value) {
			$code .= "\t" . $property . ': ' . $value . ';' . PHP_EOL;
		}
		$this->selectors[$media . $selector]['p'] = $code;
	}

	/**
	 * Replace a selector content by an other
	 * @param string $selector
	 * @param string $selectorCode
	 */
	public function replaceSelector($selector, $selectorCode, $media = '') {
		$media = $this->serializeMediaQuery($media);
		if (isset($this->selectors[$media . $selector]['p'])) {
			$this->selectors[$media . $selector]['p'] = $selectorCode;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Get all CSS after tokenize
	 * @return array
	 */
	public function getCSSValues() {
		return $this->selectors;
	}
	
	/**
	 * Serizalize a media query to be conform with w3c
	 * @return string
	 */
	public function serializeMediaQuery($mdq) {
		$stripMedia = function($mdq){
			$mdq = str_replace(' ', '', $mdq);
			$mdq = str_replace(':', ': ', $mdq);
			return $mdq;
		};
		$mdq = preg_replace('/[[:blank:]]+/',' ', $mdq);
		$mediaqueryparts = explode('(', $mdq,  2);
		$type = trim($mediaqueryparts[0]);
		if(count($mediaqueryparts) > 1) {
			$mdq = str_replace(array('(',')'), '', $mediaqueryparts[1]);
			$split = explode(' and ', $mdq);
			$split = array_map($stripMedia, $split);
			sort($split);
			$mdq = str_replace(' all and', '', $type ) . ' (' . implode(') and (', $split) . ')';
		}
		return $mdq;
	}

	/**
	 * Save file
	 * @param string $selector
	 * @return bool
	 */
	public function save() {
		$output = '';
		$media = 'first';
		$oldMedia = 'first';
		foreach ($this->selectors as $selector) {
			$media = isset($selector['media']) ? $selector['media'] : '';
			if ($oldMedia != $media) {
				if (!empty($output)) {
					if (!empty($oldMedia)) $output .= PHP_EOL . '}';
					if (!empty($media)) $output .= (isset($selector['bmedia']) ? $selector['bmedia'] : '') . $media . '{';
				}
				$oldMedia = $media;
			}
			$output .= $selector['b'] . $selector['s'] . ' {' . $selector['p'] . '}';
		}
		if (substr($media, 0, 1) == '@')
			$output .= PHP_EOL . '}';
		return \tools::file_put_contents($this->path, $output);
	}

}

?>