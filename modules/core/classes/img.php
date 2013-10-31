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
 * @package Parsimony
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace core\classes;

/**
 * Image Class 
 * Manages images
 */
class img {

	/** @var string path of the picture */
	private $pathPicture;

	/** @var string type of the picture */
	private $type;
	
	/** @var string mime of the picture */
	private $mime;

	/** @var integer width */
	private $width;

	/** @var integer height */
	private $height;

	/** @var string picture name */
	private $picture;

	/**
	 * Build image
	 * @param string $path 
	 */
	public function __construct($path) {
		$this->pathPicture = $path;
		$params = getimagesize($path);
		if (is_array($params)) {
			list($this->width, $this->height, $this->type) = $params;
			$this->mime = $params['mime'];
			switch ($this->type) {
				case 1:
					$this->picture = imagecreatefromgif($this->pathPicture);
					break;
				case 2:
					$this->picture = imagecreatefromjpeg($this->pathPicture);
					break;
				case 3:
					$this->picture = imagecreatefrompng($this->pathPicture);
					break;
				default:
					return t('The file format is not compatible');
					break;
			}
		} else { /* Error img */
			list($this->width, $this->height, $this->type) = array(100, 100, 2);
			$this->mime = 'image/jpeg';
			$this->picture = imagecreate(100, 100);
			imagecolorallocate($this->picture, 255, 255, 255);
		}
	}

	/**
	 * Resize the image
	 * @param integer $max_x
	 * @param integer $max_y
	 */
	public function resize($max_x, $max_y) {
		if ($max_x < $this->width || $max_y < $this->height) {
			if ($this->width > $this->height) {
				$coef = $this->width / $max_x;
				$y = $this->height / $coef;
				$x = $max_x;
			} else {
				$coef = $this->height / $max_y;
				$x = $this->width / $coef;
				$y = $max_y;
			}
		} else {
			$x = $this->width;
			$y = $this->height;
		}
		$newPicture = imagecreatetruecolor($x, $y);
		if (($this->type === 1) || ($this->type === 3)) {
			imagesavealpha($newPicture, true);
			imagealphablending($newPicture, false);
			$transparent = imagecolorallocatealpha($newPicture, 255, 255, 255, 127);
			imagefilledrectangle($newPicture, 0, 0, $x, $y, $transparent);
		}
		imagecopyresampled($newPicture, $this->picture, 0, 0, 0, 0, $x, $y, $this->width, $this->height);
		$this->picture = $newPicture;
	}

	/**
	 * Cut the image
	 * @param integer $max_x
	 * @param integer $max_y
	 */
	public function crop($max_x, $max_y) {

		if ($max_x < $this->width || $max_y < $this->height) {
			if ($this->width > $this->height) {
				$coef = $this->height / $max_y;
				$x = $this->width / $coef;
				$y = $max_y;
				$debut_x = ($x - $max_x) / 2;
				$debut_y = 0;
			} else {
				$coef = $this->width / $max_x;
				$y = $this->height / $coef;
				$x = $max_x;
				$debut_x = 0;
				$debut_y = ($y - $max_y) - 100 / 2 / 2;
			}
		} else {
			$x = $this->width;
			$y = $this->height;
			$debut_x = 0;
			$debut_y = 0;
		}
		$newPicture = imagecreatetruecolor($x, $y);
		if (($this->type == 1) OR ($this->type == 3)) {
			imagesavealpha($newPicture, true);
			imagealphablending($newPicture, false);
			$transparent = imagecolorallocatealpha($newPicture, 255, 255, 255, 127);
			imagefilledrectangle($newPicture, 0, 0, $x, $y, $transparent);
		}
		imagecopyresampled($newPicture, $this->picture, 0, 0, 0, 0, $x, $y, $this->width, $this->height);
		$newPicture2 = imagecreatetruecolor($max_x, $max_y);
		imagecopyresampled($newPicture2, $newPicture, 0, 0, $debut_x, $debut_y, $max_x, $max_y, $max_x, $max_y);
		$this->picture = $newPicture2;
	}

	/**
	 * Reverses all colors of the image
	 */
	public function reverseColors() {
		imagefilter($this->picture, IMG_FILTER_NEGATE);
	}

	/**
	 * Converts the image into grayscale
	 */
	public function grayScale() {
		imagefilter($this->picture, IMG_FILTER_GRAYSCALE);
	}

	/**
	 * Changes the brightness of the image
	 * @param string $poucentage
	 */
	public function brightness($poucentage) {
		imagefilter($this->picture, IMG_FILTER_BRIGHTNESS, $poucentage);
	}

	/**
	 * Changes the contrast of the image
	 * @param string $poucentage
	 */
	public function contrast($poucentage) {
		imagefilter($this->picture, IMG_FILTER_CONTRAST, $poucentage);
	}

	/**
	 * Converts the image into anonther color
	 * @param integer Red
	 * @param integer Green
	 * @param integer Blue
	 */
	public function colorize($r, $g, $b) {
		imagefilter($this->picture, IMG_FILTER_COLORIZE, $r, $g, $b);
	}

	/**
	 * Uses edge detection to highlight the edges in the image
	 */
	public function edgeDetect() {
		imagefilter($this->picture, IMG_FILTER_EDGEDETECT);
	}

	/**
	 * Embosses the image
	 */
	public function emboss() {
		imagefilter($this->picture, IMG_FILTER_EMBOSS);
	}

	/**
	 * Blurs the image using the Gaussian method
	 */
	public function gaussianBlur() {
		imagefilter($this->picture, IMG_FILTER_GAUSSIAN_BLUR);
	}

	/**
	 * Blurs the image
	 */
	public function selectiveBlur() {
		imagefilter($this->picture, IMG_FILTER_SELECTIVE_BLUR);
	}

	/**
	 * Uses mean removal to achieve a "sketchy" effect
	 */
	public function meanRemoval() {
		imagefilter($this->picture, IMG_FILTER_MEAN_REMOVAL);
	}

	/**
	 * Makes the image smoother
	 * @param string $poucentage
	 */
	public function smooth($poucentage) {
		imagefilter($this->picture, IMG_FILTER_SMOOTH, $poucentage);
	}

	/**
	 * Applies pixelation effect to the image
	 * @param string $pixelsize
	 * @param string $effectmode optional
	 */
	public function pixelate($pixelsize, $effectmode=FALSE) {
		imagefilter($this->picture, IMG_FILTER_SMOOTH, $pixelsize, $effectmode);
	}

	/**
	 * Output of the image
	 * @param optional $path by default false
	 * @param optional $quality by default 100
	 */
	private function output($path = '', $quality = 100) {
		switch ($this->type) {
			case 1:
				imagegif($this->picture, $path);
				break;
			case 2:
				imagejpeg($this->picture, $path, $quality);
				break;
			case 3:
				$quality = round($quality / 10);
				if ($quality === 10)
					$quality = 9;
				imagepng($this->picture, $path, $quality);
				break;
			default:
				return t('The format is not supported');
				break;
		}
	}

	/**
	 * Content-type of the image
	 */
	public function display() {
		header('Content-type: ' . $this->mime);
		$this->output(); /* Without save img */
	}

	/**
	 * Save the image into a directory
	 * @param $path 
	 * @param optional $quality by default 100
	 */
	public function save($path, $quality = 100) {
		if (!is_dir(dirname($path)))
			mkdir(dirname($path), 0755, TRUE);
		$this->output($path, $quality);
	}

}

?>