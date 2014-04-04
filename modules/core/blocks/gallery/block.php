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
 * @package core/blocks
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace core\blocks;

/**
 * @title Gallery
 * @description allows you to build an image gallery in drag n drop
 * @copyright 1
 * @browsers all
 * @php_version_min 5.3
 * @modules_dependencies core:1
 */

class gallery extends \block {

	public function saveConfigs() {
		$this->setConfig('img', $_POST['img']);
		$this->setConfig('width', $_POST['width']);
		$this->setConfig('height', $_POST['height']);
	}

	public function __construct($id) {
		parent::__construct($id);
		$img = array('Parsimony.png' => array('name' => 'core/files/Parsimony.png', 'title' => 'Parsimony A new Generation of CMS', 'alt' => 'Parsimony A new Generation of CMS', 'url' => 'core/files/Parsimony.png', 'description' => 'Helps the web to have talent and share it'));
		$this->setConfig('img', $img);
		$this->setConfig('width', '500');
		$this->setConfig('height', '250');
	}
	
	public function onMove($typeProgress, $module, $name, $themeType = 'desktop', $copy = FALSE) {
		$imgs = $this->getConfig('img');
		foreach($imgs as $img){
			$oldPath = $img['name'];
			$imagePath = substr(strstr($oldPath, '/files/'), 7);
			$newPath = $module . '/files/' . $imagePath;
			if($oldPath !== $newPath){
				if (stream_resolve_include_path($newPath) !== FALSE) { /* check if an image with this path already exists in profile */
					$fileInfo = pathinfo($imagePath);
					$extension = strtolower($fileInfo['extension']);
					$filename = $fileInfo['filename'];

					/* allow to not overload filename with name_0_3_2_0 ... */
					$generatedPart = strrchr($filename, '_');
					if ($generatedPart !== FALSE && is_numeric(substr($generatedPart, 1))) {
						$filename = substr($fileInfo['filename'], 0, -(strlen($generatedPart)));
					}

					$nbn = 0;
					while (stream_resolve_include_path($newPath)) {
						$imagePath = $filename . '_' . $nbn . '.' . $extension;
						$newPath = $module . '/files/' . $imagePath;
						$nbn++;
					}
				}
				$this->setConfig('imgPath', $newPath);
				\tools::file_put_contents(PROFILE_PATH . $newPath, file_get_contents($oldPath, FILE_USE_INCLUDE_PATH));
			}
		}
	}

	public function ajaxRefresh($type = FALSE) {
		/*if ($type === 'add') {
			return parent::ajaxRefresh($type);
		} else {*/
			return 'document.getElementById("preview").contentWindow.location.reload()';
		//}
	}
	
}

\app::$response->addJSFile('modules/core/blocks/gallery/block.js', 'footer');



