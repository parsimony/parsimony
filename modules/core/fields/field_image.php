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
 * @title Image
 * @description Image
 * @version 1
 * @browsers all
 * @php_version_min 5.3
 * @modules_dependencies core:1
 */

class field_image extends \field {
	
	protected $width = '100';
	protected $height = '100';
	protected $path = 'files';

	/**
	 * Set width
	 * @param string $width
	 */
	public function setWidth($width) {
		$this->width = $width;
		return $this;
	}

	/**
	 * Set height
	 * @param string $height
	 */
	public function setHeight($height) {
		$this->height = $height;
		return $this;
	}

	/**
	 * Upload file
	 * @param string $path
	 * @return string 
	 */
	public function uploadAction() {
		$maxUploadSize = str_replace('m','000000',strtolower(ini_get('upload_max_filesize')));
		try {
			$upload = new \core\classes\upload($maxUploadSize, 'image', PROFILE_PATH . $this->module . '/' . $this->path . '/');
			$result = $upload->upload($_FILES['fileField']);
		} catch (\Exception $exc) {
		\app::$response->setHeader('X-XSS-Protection', '0');
		\app::$response->setHeader('Content-type', 'application/json');
		if (ob_get_level())
			ob_clean();
		return json_encode(array('eval' => '', 'notification' => $exc->getMessage(), 'notificationType' => 'negative'));
	}
	if($result !== FALSE){
		$arr = $_FILES['fileField'];
		$arr['name'] = $result;
		$params = @getimagesize($path.'/'.$result);
		list($width, $height, $type, $attr) = $params;
		if($params){
			$arr['x'] = $width;
			$arr['y'] = $height;
			$arr['type'] = $type;
		}
		unset($arr['tmp_name']);
		\app::$response->setHeader('Content-type', 'application/json');
		return json_encode($arr);
	}else
		return FALSE;
	}

}

?>
