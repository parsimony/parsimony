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
 * @package core/blocks
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace core\blocks;

/**
 * @title Code
 * @description displays a Code editor (PHP, js, HTML, CSS)
 * @version 1
 * @browsers all
 * @php_version_min 5.3
 * @modules_dependencies core:1
 */
class code extends \block {
	
	public function getView() {
		ob_start();
		include($this->getConfig('viewPath'));
		return ob_get_clean();
	}

	/**
	 * Save the block configs
	 */
	public function saveConfigs() {
		\app::addListener('error', array($this, 'catchError'));
		/* Test if new file contains errors */
		$testIfHasError = \tools::testSyntaxError($_POST['editor'], array('_this' => $this));
		/* If new file contains errors */
		if ($testIfHasError === TRUE) {
			/* If there's no errors, Save new file */
			\tools::file_put_contents(PROFILE_PATH . $this->getConfig('viewPath'), $_POST['editor']);
		}
	}

	/**
	 * Catch errors
	 * @param string $code
	 * @param string $file
	 * @param integer $line
	 * @param string $message
	 */
	public function catchError($code, $file, $line, $message) {
		$mess = $message . ' ' . t('in line') . ' ' . $line;
		if ($code == 0 || $code == 2 || $code == 8 || $code == 256 || $code == 512 || $code == 1024 || $code == 2048 || $code == 4096 || $code == 8192 || $code == 16384) {
			/* If it's a low level error, we save but we notice the dev */
			\tools::file_put_contents(PROFILE_PATH . $this->getConfig('viewPath'), $_POST['editor']);
			$return = array('eval' => '$("#' . $this->getId() . '",ParsimonyAdmin.currentBody).html("' . $mess . '");', 'notification' => t('Saved but', FALSE) . ' : ' . $mess, 'notificationType' => 'normal');
		} else {
			$return = array('eval' => '$("#' . $this->getId() . '",ParsimonyAdmin.currentBody).html("' . $mess . '");', 'notification' => t('Error', FALSE) . ' : ' . $mess, 'notificationType' => 'negative');
		}
		if (ob_get_level())
			ob_clean();
		echo json_encode($return);
		exit;
	}

	public function forkAction($newBlock, $newModule) {
		$configs = $this->getConfigs();
		$viewPath = $configs['viewPath'];
		$configs['viewPath'] = 'modules/' . $newModule . '/blocks/' . $newBlock . '/view.php';
		$configs['mode'] = 'r';
		$configs = base64_encode(serialize($configs));
		return self::build($newModule, $newBlock, get_class($this), $configs, $viewPath);
	}
	
	public function onMove($typeProgress, $module, $name, $themeType = 'desktop') {
		$oldPath = $this->getConfig('viewPath');
		if ($typeProgress === 'theme')
			$path = $module . '/themes/' . $name . '/' . $themeType . '/views/' . $this->id . '.php';
		else
			$path = $module . '/pages/views/' . $themeType . '/' . $name . '_' . $this->id . '.php';
		
		if (is_file($path) === FALSE) { /* check if a view with this path already exists in profile */
			$this->setConfig('viewPath', $path); /* save the new path */
			if (!empty($oldPath) && stream_resolve_include_path($oldPath) !== FALSE) { /* Check if we have to move an old view  : moveBlock */
				\tools::file_put_contents(PROFILE_PATH . $path, file_get_contents($oldPath, FILE_USE_INCLUDE_PATH));
				if(is_file(PROFILE_PATH . $oldPath))
					rename(PROFILE_PATH . $oldPath, PROFILE_PATH . $oldPath . '.back'); //do only for profile, not modules
			} else { /* add block */
				\tools::file_put_contents(PROFILE_PATH . $path, '<h1>' . t('Start programming in this area', false) . '</h1>');
			}
		} else {
			return FALSE; // a view with this ID already exists
		}
	}

	public function destruct() {
		$path = PROFILE_PATH . $this->getConfig('viewPath');
		if (is_file($path) === TRUE) {
			rename($path, $path . '.back');
		}
	}
}
?>
