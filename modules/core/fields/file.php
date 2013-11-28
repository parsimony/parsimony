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
 * @package core\fields
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace core\fields;

/**
 * @title File
 * @description File
 * @version 1
 * @browsers all
 * @php_version_min 5.3
 * @modules_dependencies core:1
 */

class file extends \field {
	
	protected $path = 'files';
	
	public function validate($value) {
		
		if (is_array($value) && isset($value['path']) && isset($value['dataURL'])) {
			$fileName = str_replace('..', '', $value['path']); /* secure relative path */
			
			/* find an unused name */
			$fileInfo = pathinfo($fileName);
			$base = $fileInfo['filename'];
			$ext = $fileInfo['extension'];
			$dir = empty($fileInfo['dirname']) ? '' : $fileInfo['dirname'] . '/';  /*  $fileInfo['dirname'] in case that filename contains a part of dirname */
			$path = PROFILE_PATH . $this->entity->getModule() . '/' . $this->path . '/';
			$nbn = 0;
			while (is_file($path . $fileName)) {
				$fileName = $dir . $base . '_' . $nbn . '.' . $ext;
				$nbn++;
			}
			if (!is_dir($path . $dir)) 
				\tools::createDirectory($path . $fileInfo['dirname'] . '/');
			
			/* decode dataURL */
			$cut = explode(',', $value['dataURL']);  
			$dataURL = $cut[1];  
			$dataURL = base64_decode(str_replace(' ', '+', $dataURL));
			/* save and check image */
			if (file_put_contents($path . $fileName, $dataURL)) {
				return $fileName;
			}else{
				return FALSE; /* can't write image */
			}
		} else {
			return parent::validate($value);
		}
	}
	
	/**
	 * Upload file
	 * @param string $path
	 * @return string 
	 */
	public function uploadAction() {
		$maxUploadSize = str_replace('m', '000000', strtolower(ini_get('upload_max_filesize')));
		try {
			$allowedExts = implode(',',array_keys(\app::$config['ext']));
			$upload = new \core\classes\upload($maxUploadSize, $allowedExts, PROFILE_PATH . $this->entity->getModule() . '/' . $this->path . '/');
			$result = $upload->upload($_FILES['fileField']);
		} catch (\Exception $exc) {
			\app::$response->setHeader('X-XSS-Protection', '0');
			\app::$response->setHeader('Content-type', 'application/json');
			if (ob_get_level())
				ob_clean();
			return json_encode(array('eval' => '', 'notification' => $exc->getMessage(), 'notificationType' => 'negative'));
		}
		if ($result !== FALSE) {
			$arr = $_FILES['fileField'];
			$arr['name'] = $result;
			unset($arr['tmp_name']);
			\app::$response->setHeader('Content-type', 'application/json');
			return json_encode($arr);
		} else
			return FALSE;
	}

}

?>
