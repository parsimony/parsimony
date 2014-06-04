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
 * @package Parsimony
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace admin;

/**
 * @title Admin
 * @description Manage the administration of Parsimony
 * @copyright 1
 * @browsers all
 * @php_version_min 5.3
 * @php_extension php_pdo_mysql
 * @php_settings magic_quotes_gpc:0,register_globals:0
 */

class module extends \module {

	protected $name = 'admin';

	/** @var string theme name */
	private $theme;

	/** @var string module name */
	private $module;

	/**
	 * Controller post
	 * @param string $action
	 * @param string $httpMethod
	 * @return string|false 
	 */
	public function controller($action, $httpMethod = 'GET') {
		$rights = array('addBlock' => 32,
				'removeBlock' => 256,
				'saveCSS' => 16,
				'moveBlock' => 256,
				'dbDesigner' => 4096,
				'addTheme' => 64,
				'changeTheme' => 32,
				'deleteTheme' => 64,
				'addModule' => 16384,
				'saveRights' => 65536,
				'saveModel' => 4096,
				'uptodate' => 2,
				'saveBlockConfigs' => 128,
				'getViewConfigBlock' => 128,
				'savePage' => 4,
				'reorderPages' => 8,
				'getViewUpdatePage' => 4,
				'deleteThisPage' => 8,
				'checkOverridedPage' => 8,
				'saveTranslation' => 32768,
				'getViewAddModule' => 16384,
				'installModule' => 16384,
				'packageModule' => 16384,
				'saveConfig' => 1,
				'structureTree' => 128,
				'getViewAdminRights' => 65536,
				'upload' => 1024,
				'explorer' => 512,
				'files' => 512,
				'explorerEditor' => 512,
				'getBackUp' => 512,
				'saveCode' => 512,
				'createDir' => 512,
				'deleteDir' => 512,
				'deleteFile' => 512,
				'savePicture' => 512);

		if (!isset($rights[$action]) || $_SESSION['permissions'] & $rights[$action]) {
			return parent::controller($action, $httpMethod);
		} else {
			return \app::$response->setContent($this->returnResult(array('notification' => t('Permission denied'), 'notificationType' => 'negative')), 200);
		}
	}

	/**
	 * Add a block
	 * @param string $popBlock
	 * @param string $parentBlock
	 * @param string $idBlock
	 * @param string $id_next_block
	 * @param string $stop_typecont
	 * @return string 
	 */
	protected function addBlockAction($popBlock, $parentBlock, $idBlock, $id_next_block, $stop_typecont, $content, $MODULE, $THEMEMODULE, $THEME) {
		$this->initObjects();
		$idBlock = strtolower($idBlock);
		if($stop_typecont === 'page') { /* block id in page have to start with a capital */
			$idBlock = ucfirst($idBlock);
		}
		$tempBlock = new $popBlock($idBlock);
		$idBlock = $tempBlock->getId(); /* To sanitize id */	
		if (method_exists($tempBlock, 'onMove')) { /* init path of views */
			if($stop_typecont === 'theme') {
				if ($tempBlock->onMove('theme', $THEMEMODULE, $THEME)) {
					return $this->returnResult(array('notification' => t('ID block already exists in this theme, please choose antother')));
				}
			} else {
				if ($tempBlock->onMove('page', $MODULE, $this->page->getId())) {
					return $this->returnResult(array('notification' => t('ID block already exists in this page, please choose antother')));
				}
			}
		}

		if (!empty($content) && method_exists($tempBlock, 'setContent')){ /* external DND */
			$tempBlock->setContent($content);
		}
		$block = $this->$stop_typecont->searchBlock($parentBlock);
		$block->addBlock($tempBlock, $id_next_block);

		/* If exists : Add default block CSS in current theme  */
		if (is_file('modules/' . str_replace('\\', '/', $popBlock) . '/default.css')) {
			$css = new \css('modules/' . str_replace('\\', '/', $popBlock) . '/default.css');
			if (!is_file(PROFILE_PATH . $THEMEMODULE . '/themes/' . $THEME . '/style.css') && is_file('modules/' . $this->theme->getModule() . '/themes/' . $this->theme->getName() . '/style.css')) {
				file_put_contents(PROFILE_PATH . $THEMEMODULE . '/themes/' . $THEME . '/style.css', file_get_contents('modules/' . $this->theme->getModule() . '/themes/' . $this->theme->getName() . '/style.css'));
			}
			$cssCurrentTheme = new \css(PROFILE_PATH . $THEMEMODULE . '/themes/' . $THEME. '/style.css');
			foreach ($css->getAllSselectors() as $selector) {
				$rules = $css->extractSelectorRules($selector);
				if (!empty($rules)) {
					$newSelector = '#' . $idBlock . ' ' . str_replace(':host', '', $selector);
					if (!$cssCurrentTheme->selectorExists($newSelector)) {
						$cssCurrentTheme->addSelector($newSelector);
					}
					foreach ($rules as $property => $value) {
						$value = str_replace('BASE_PATH', BASE_PATH, $value);
						if (!$cssCurrentTheme->propertyExists($newSelector, $property)) {
							$cssCurrentTheme->addProperty($newSelector, $property, $value);
						} else {
							$cssCurrentTheme->updateProperty($newSelector, $property, $value);
						}
					}
				}
			}
			$cssCurrentTheme->save();
		}
		$response = $tempBlock->ajaxRefresh('add'); /* Get content before __sleep() */
		$this->saveAll();
		if ($this->$stop_typecont->searchBlock($idBlock) != NULL) {
			$return = array('eval' => $response, 'notification' => t('The Block is saved'), 'notificationType' => 'positive');
		} else {
			$return = array('notification' => t('Error on drop'), 'notificationType' => 'negative');
		}
		return $this->returnResult($return);
	}

	/**
	 * Save the block configs
	 * @param string $typeProgress
	 * @param string $idBlock
	 * @param string $maxAge
	 * @param string $tag
	 * @param string $allowedModules
	 * @param string $ajaxReload
	 * @param string $ajaxLoad
	 * @param string $cssClasses
	 * @return string 
	 */
	protected function saveBlockConfigsAction($typeProgress, $idBlock, $headerTitle, $maxAge, $tag, $ajaxReload, $ajaxLoad, $cssClasses, $mode, $devices = array(), $allowedModules = array(), $allowedRoles = array(), $CSSFiles = array(), $JSFiles = array()) {
		$this->initObjects();
		$block = $this->$typeProgress->searchBlock($idBlock);

		if(!empty($headerTitle)) $block->setConfig('headerTitle', $headerTitle);
		else $block->removeConfig('headerTitle');
		if(is_numeric($maxAge) && $maxAge != 0) $block->setConfig('maxAge', $maxAge);
		else $block->removeConfig('maxAge');
		if(!empty($tag) && $tag != 'div') $block->setConfig('tag', $tag);
		else $block->removeConfig('tag');
		if(!empty($allowedModules)) $block->setConfig('allowedModules', $allowedModules);
		else $block->removeConfig('allowedModules');
		if(!empty($devices) && count($devices) < count(\app::$devices)) $block->setConfig('devices', $devices);
		else $block->removeConfig('devices');
		$block->removeConfig('exclude');
		if(!empty($allowedRoles)) $block->setConfig('allowedRoles', $allowedRoles);
		else $block->removeConfig('allowedRoles');
		if(!empty($ajaxReload)) $block->setConfig('ajaxReload', $ajaxReload);
		else $block->removeConfig('ajaxReload');
		if(!empty($ajaxLoad)) $block->setConfig('ajaxLoad', $ajaxLoad);
		else $block->removeConfig('ajaxLoad');
		$cssClasses = trim($cssClasses);
		if(!empty($cssClasses)) $block->setConfig('cssClasses', $cssClasses);
		else $block->removeConfig('cssClasses');
		$block->removeConfig('css_classes');
		if(!empty($CSSFiles)) $block->setConfig('CSSFiles', $CSSFiles);
		else $block->removeConfig('CSSFiles');
		if(!empty($JSFiles)) $block->setConfig('JSFiles', $JSFiles);
		else $block->removeConfig('JSFiles');
		if(!empty($mode)) $block->setConfig('mode', $mode);
		else $block->removeConfig('mode');

		\app::$response->page = new \page(999, 'core');
		if(isset($_POST['getVars'])){
			parse_str($_POST['getVars'],$outVars);
			array_merge($_GET,$outVars);
			\app::$request->setParams($outVars);
			unset($_POST['getVars']);
		}
		if(isset($_POST['postVars'])){ 
			parse_str($_POST['postVars'],$outVars);
			array_merge($_POST,$outVars);
			\app::$request->setParams($outVars);
			unset($_POST['postVars']);
		}
		unset($_POST['TOKEN']);
		if (method_exists($block, 'saveConfigs')) {
			$block->saveConfigs();
		} else {
			$rm = array('action', 'MODULE', 'THEME', 'DEVICE', 'THEMEMODULE', 'idBlock', 'parentBlock', 'typeProgress', 'maxAge', 'tag', 'ajaxReload', 'css_classes', 'allowedModules', 'allowedRoles', 'devices');
			$rm = array_flip($rm);
			$configs = array_diff_key($_POST, $rm);
			foreach ($configs AS $configName => $value) {
				$val = $_POST[$configName];
				$block->setConfig($configName, $val);
			}
		}
		$return = array('eval' => $block->ajaxRefresh(),  'jsFiles' => json_encode(\app::$response->getJSFiles()), 'CSSFiles' => json_encode(\app::$response->getCSSFiles()), 'notification' => t('The Config has been saved'), 'notificationType' => 'positive');
		$this->saveAll(); // save objects in last to avoid call __sleep() before getting content of the block ( this->display in ajaxRefresh() ), eg. block query
		return $this->returnResult($return);
	}

	/**
	 * Return results in json
	 * @param string $results
	 * @return string 
	 */
	private function returnResult($results) {
		if (!isset($results['notificationType'])) $results['notificationType'] = 'normal';
		\app::$response->setHeader('X-XSS-Protection', '0');
		\app::$response->setHeader('Content-type', 'application/json');
		if (ob_get_level()) ob_clean();
		return json_encode($results);
	}

	/**
	 * Remove block
	 * @param string $typeProgress
	 * @param string $parentBlock
	 * @param string $idBlock
	 * @return string
	 */
	protected function removeBlockAction($typeProgress, $parentBlock, $idBlock, $THEMEMODULE, $THEME) {
		$this->initObjects();
		$parent = $this->$typeProgress->searchBlock($parentBlock);
		$block = $this->$typeProgress->searchBlock($idBlock);
		if(is_object($block) && method_exists($block, 'destruct')){
			$block->destruct();
		}
		$parent->rmBlock($idBlock);
		$test = $this->$typeProgress->searchBlock($idBlock);
		if ($test == NULL){
			$path = PROFILE_PATH . $THEMEMODULE . '/themes/' . $THEME . '/style.css';
			if(is_file($path)){
				$css = new \css($path);
				$css->deleteBlockSelectors('#' . $idBlock);
				$css->save();
			}
			$this->saveAll();
			$return = array('eval' => '$("#" + Parsimony.blocks["admin_blocks"].inProgress,ParsimonyAdmin.currentBody).remove();$("#changeres").trigger("change");', 'notification' => t('The block has been deleted'), 'notificationType' => 'positive');
		}else
			$return = array('notification' => t('Container block cannot be deleted'), 'notificationType' => 'negative');
		return $this->returnResult($return);
	}

	/**
	 * Save page : add or update
	 * @param string $module
	 * @param string $id_page
	 * @param string $title
	 * @param string $meta
	 * @param array $URLcomponents
	 * @param string $regex
	 * @return string 
	 */
	protected function savePageAction($module, $id_page, $title, $meta, $regex, $theme = FALSE, array $URLcomponents = array()) {
		$moduleObj = \app::getModule($module);
		try {
			$page = $moduleObj->getPage($id_page);
		} catch (\Exception $exc) {
			$page = new \page($id_page, $module);
			/* Set rights forbidden for non admins, admins are allowed by default */
			foreach (\app::getModule('core')->getEntity('role') as $role) {
				if($role->permissions == 0){
					$page->setRights($role->id_role, 0);
				}
			}
			$page->setModule($module);
			$moduleObj->addPage($page);
		}
		$page->setTitle($title);
		if ($theme !== FALSE && $_SESSION['permissions'] & 32) { /* 32 = choose theme*/
			$page->setTheme($theme);
		}
		$page->setMetas($meta);
		if ($_SESSION['permissions'] & 8) {
			if (isset($URLcomponents))
				$page->setURLcomponents($URLcomponents);
			$page->setRegex('@^' . $regex . '$@');
		}
		$moduleObj->updatePage($page); //modif
		if (\tools::serialize(PROFILE_PATH . $module . '/module', $moduleObj)) {
			$return = array('eval' => 'ParsimonyAdmin.loadBlock(\'left_sidebar\');', 'notification' => t('The page has been saved'), 'notificationType' => 'positive');
			return $this->returnResult($return);
		}
	}

	/**
	 * Reorder Pages of a module
	 * @param string $module
	 * @param array $order
	 * @return boolean 
	 */
	protected function reorderPagesAction($module, array $order = array()) {
		$module = \app::getModule($module);
		$newOrder = array();
		foreach ($order as $value) {
			$id = substr($value,  strpos($value, '_') +1 );
			if(!empty($id)) $newOrder[] = $id; 
		}
		return $module->reoderPages($newOrder);
	}

	/**
	 * Get the view to update the page
	 * @param string $module
	 * @param string $page
	 * @return string|false
	 */
	protected function getViewUpdatePageAction($module, $page) {
		$moduleObj = \app::getModule($module);
		if ($page === 'new') {
			/* define a default name to this new page, we don't use getPages for right puposes */
			$idPage = 1;
			while (stream_resolve_include_path($module . '/pages/' . $idPage . '.obj') !== FALSE) {
				$idPage++;
			}
			$page = new \page($idPage, $module);
			$page->setTitle('Page ' . $idPage);
			$page->setRegex('@^page_' . $idPage . '$@');
		} else {
			$page = $moduleObj->getPage($page);
		}
		$module = $moduleObj;
		ob_start();
		include ('modules/admin/views/managePage.php');
		return ob_get_clean();
	}

	/**
	 * Delete the Page
	 * @param string $module
	 * @param integer $id_page
	 * @return string 
	 */
	protected function deleteThisPageAction($module, $id_page) {
		$module = \app::getModule($module);
		$page = $module->getPage($id_page);
		$module->deletePage($page);
		$module->save();
		$url = '';
		if($module->getName() != 'core') $url = $module->getName().'/';
		$return = array('eval' => 'window.location = "' . BASE_PATH . $url . 'index";', 'notification' => t('The page has been deleted'), 'notificationType' => 'positive');
		return $this->returnResult($return);
	}

	/**
	 * Save CSS
	 * @param string $filePath
	 * @param string $selector
	 * @return string 
	 */
	protected function saveCSSAction($changes) {
		$changes = json_decode($changes, TRUE);
		if(!empty($changes)){
			foreach ($changes AS $file => $selectors) {
				/* If CSS file doesn't exists in profile/ dir, we create a copy from modules/ */
				if (!is_file(PROFILE_PATH . $file) && is_file('modules/' . $file))
					\tools::file_put_contents(PROFILE_PATH . $file, file_get_contents('modules/' . $file));

				$filePath = PROFILE_PATH . $file;
				$cssFile = new \css($filePath);
				if(!empty($selectors)){
					foreach ($selectors AS $rule) {
						$code = trim($rule['value']);
						if(!empty($code)){
							if (!$cssFile->selectorExists($rule['selector'], $rule['media'])) {
							$cssFile->addSelector($rule['selector'], $rule['media']);
							}
						}
						$cssFile->replaceSelector($rule['selector'], $code, $rule['media']);
					}
					$cssFile->save();
				}
			}
		}
		$return = array('notification' => t('The style sheet has been saved'), 'notificationType' => 'positive');
		return $this->returnResult($return);
	}

	/**
	 * Move Block from a container to another
	 * @param string $start_typecont
	 * @param string $idBlock
	 * @param string $popBlock
	 * @param string $startParentBlock
	 * @param string $id_next_block
	 * @param string $stop_typecont
	 * @param string $parentBlock
	 * @return string 
	 */
	protected function moveBlockAction($start_typecont, $idBlock, $popBlock, $startParentBlock, $id_next_block, $stop_typecont, $parentBlock, $MODULE, $THEMEMODULE, $THEME) {
		$this->initObjects();
		
		//start
		$block = $this->$start_typecont->searchBlock($idBlock);
		$blockparent = $this->$start_typecont->searchBlock($startParentBlock);
		$blockparent->rmBlock($idBlock);
		
		$js = 'Parsimony.blocks["admin_blocks"].moveBlock("' . $idBlock . '");';
		if ($start_typecont === 'page' && $stop_typecont === 'theme') {
			$idBlock = strtolower($idBlock);
			$block->setId($idBlock);
			$js = 'Parsimony.blocks["admin_blocks"].moveBlock("' . ucfirst($idBlock) . '", "pageToTheme");';
		} elseif ($start_typecont === 'theme' && $stop_typecont === 'page') {
			$idBlock = ucfirst($idBlock);
			$block->setId($idBlock);
			$js = 'Parsimony.blocks["admin_blocks"].moveBlock("' . strtolower($idBlock) . '", "themeToPage");';
		}

		//stop
		if ($id_next_block === '' || $id_next_block === 'undefined')
			$id_next_block = FALSE;
		$block2 = $this->$stop_typecont->searchBlock($parentBlock); /* Get the parent */
		$block2->addBlock($block, $id_next_block); /* add the block in his parent */
		if ($this->$stop_typecont->searchBlock($idBlock) !== NULL){
			if (method_exists($block, 'onMove')) { /* init path of views */
				if($stop_typecont === 'theme') {
					if ($block->onMove('theme', $THEMEMODULE, $THEME)) {
						return $this->returnResult(array('notification' => t('ID block already exists in this theme, please choose antother')));
					}
				} else {
					if ($block->onMove('page', $MODULE, $this->page->getId())) {
						return $this->returnResult(array('notification' => t('ID block already exists in this page, please choose antother')));
					}
				}
			}
			$this->saveAll();
			$return = array('eval' => $js, 'notification' => t('The move has been saved'), 'notificationType' => 'positive');
		}else
			$return = array('notification' => t('Error on drop'), 'notificationType' => 'negative');
		return $this->returnResult($return);
	}

	/**
	 * Get the view of the configuration block
	 * @param string $typeProgress
	 * @param string $idBlock
	 * @return string|false
	 */
	protected function getViewConfigBlockAction($typeProgress, $idBlock) {
		$this->initObjects();
		$block = $this->$typeProgress->searchBlock($idBlock);
		ob_start();
		require('modules/admin/views/manageBlock.php');
		return ob_get_clean();
	}

	/**
	 * Get the adding view of the module
	 * @return string|false 
	 */
	protected function getViewAddModuleAction() {
		return $this->getView('addModule');
	}

	/**
	 * Save translation
	 * @param string $key
	 * @param string $val
	 * @return string 
	 */
	protected function saveTranslationAction($key, $val) {
		$locale = \app::$request->getLocale();
		\unlink('cache/' . $locale . '-lang.php');
		if (isset($_COOKIE['locale']))
			$locale = $_COOKIE['locale'];
		else
			$locale = \app::$config['localization']['default_language'];
		$path = 'modules/' . MODULE . '/locale/' . $locale . '.php';
		if (file_exists($path))
			include($path);
		$lang[$key] = $val;
		$config = new \config($path, TRUE);
		$config->setVariable('lang');
		$config->saveConfig($lang);
		$return = array('eval' => '$(\'span[data-key="' . $key . '"]\',ParsimonyAdmin.currentBody).html("' . $val . '")', 'notification' => t('The translation has been saved'), 'notificationType' => 'positive');
		return $this->returnResult($return);
	}

	/**
	 * Get view of dbDesigner
	 * @return string 
	 */
	protected function dbDesignerAction($module = FALSE) {
		if ($module === FALSE || !isset(\app::$activeModules[$module])) {
			$module = \app::$config['defaultModule'];
		}
		$moduleObj = \app::getModule($module);
		ob_start();
		include('admin/views/dbDesigner.php');
		return ob_get_clean();
	}

	/**
	 * Get view of the file explorer
	 * @return string 
	 */
	protected function explorerAction() {
		/* Init a page */
		\app::$response->page = new \page(1, 'admin');
		return $this->getView('explorer');
	}

	/**
	 * Get view of the files for explorer
	 * @return string 
	 */
	protected function filesAction($dirPath) {
		return $this->getView('files');
	}

	/**
	 * Get view of the code editor for explorer
	 * @return string 
	 */
	protected function explorerEditorAction($file) {
		return $this->getView('explorerEditor');
	}

	/**
	 * Add a theme to the site
	 * @param string $thememodule
	 * @param string $name
	 * @param string $template
	 * @param string $patterntype
	 * @param string $url
	 * @return false 
	 */
	protected function addThemeAction($thememodule, $name, $patterntype, $template, $url = '') {
		$name = \tools::sanitizeTechString($name);
		if (!is_dir(PROFILE_PATH . $thememodule . '/themes/' . $name)) {
			\tools::createDirectory(PROFILE_PATH . $thememodule . '/themes/' . $name, 0777);
			if ($patterntype === 'template' && !empty($template)) {
				list($oldModule, $oldName) = explode(';', $template);
				$theme = array();
				foreach (\app::$devices AS $device) {
					$theme[$device['name']] = \theme::get($oldModule, $oldName, $device['name']);
					$theme[$device['name']]->setModule($thememodule);
					$theme[$device['name']]->setName($name);
					$theme[$device['name']]->onMove('theme', $thememodule, $name, $device['name'], TRUE);
				}
				if(is_dir('modules/' . $thememodule . '/themes/' . $oldName . '/')){
					\tools::copy_dir('modules/' . $thememodule . '/themes/' . $oldName . '/', PROFILE_PATH . $thememodule . '/themes/' . $name . '/');
				} else {
					\tools::copy_dir(PROFILE_PATH . $thememodule . '/themes/' . $oldName . '/', PROFILE_PATH . $thememodule . '/themes/' . $name . '/');
				}
				foreach (\app::$devices AS $device) {
					$theme[$device['name']]->save();
				}
			} else {
				$theme = new \theme('container', $name, 'desktop', $thememodule);
				$theme->save();
			}
			/* Set theme in preview mode */
			setcookie('THEMEMODULE', $thememodule, time() + 60 * 60 * 24 * 30, '/');
			setcookie('THEME', $name, time() + 60 * 60 * 24 * 30, '/');
			$return = array('eval' => 'top.window.location.reload()', 'notification' => t('The Theme has been created'), 'notificationType' => 'positive');
		} else {
			$return = array('notification' => t('The Theme has not been created, theme already exists'), 'notificationType' => 'negative');
		}
		return $this->returnResult($return);
	}

	/**
	 * Change the theme of the site
	 * @param string $THEMEMODULE
	 * @param string $name
	 * @return string 
	 */
	protected function changeThemeAction($THEMEMODULE, $name) {
		$path = stream_resolve_include_path($THEMEMODULE . '/themes/' . $name . '/desktop/theme.' .\app::$config['dev']['serialization']);
		if ($path) {
				$configObj = new \core\classes\config('profiles/' . PROFILE . '/config.php', TRUE);
				$update = array('THEMEMODULE' => $THEMEMODULE,'THEME' => $name);
				$configObj->saveConfig($update);
				setcookie('THEMEMODULE', $THEMEMODULE, time()+60*60*24*30, '/');
			setcookie('THEME', $name, time()+60*60*24*30, '/');
			$return = array('eval' => 'document.getElementById("preview").contentWindow.location.reload(); ParsimonyAdmin.loadBlock("themes")', 'notification' => t('The Theme has been changed'), 'notificationType' => 'positive');
		} else {
			$return = array('notification' => t('The Theme has\'nt been changed', FALSE), 'notificationType' => 'negative');
		}
		return $this->returnResult($return);
	}

	/**
	 * Delete the theme
	 * @param string $THEMEMODULE
	 * @param string $name
	 * @return string 
	 */
	protected function deleteThemeAction($THEMEMODULE, $name) {
		if (is_dir(PROFILE_PATH . $THEMEMODULE . '/themes/' . $name)) {
			\tools::rmdir(PROFILE_PATH . $THEMEMODULE . '/themes/' . $name);
		}
		$return = array('eval' => "$('#theme_".$name."').remove()", 'notification' => t('The Theme has been deleted'), 'notificationType' => 'positive');
		return $this->returnResult($return);
	}

	/**
	 * Add a module
	 * @param string $name_module
	 * @param string $name_titre
	 * @return string 
	 */
	protected function addModuleAction($name_module, $name_titre) {
		if (\module::build($name_module, $name_titre)) {
			$return = array('eval' => 'top.window.location.href = "' . BASE_PATH . $name_module . '/index"', 'notification' => t('The Module has been created'), 'notificationType' => 'positive');
		} else {
			$return = array('notification' => t('Module already exists, please choose another name'), 'notificationType' => 'negative');
		}
		return $this->returnResult($return);
	}

	/**
	 * Get the the view of user profile
	 * @return string 
	 */
	protected function getViewUserProfileAction() {
		return $this->getView('userProfile');
	}

	/**
	 * Change Locale language
	 * @param string $locale
	 */
	protected function changeLocaleAction($locale) {
		\unlink('var/cache/' . \app::$request->getLocale() . '-lang.php'); /* recalc cache */
		/*$config = new \core\classes\config('profiles/' . PROFILE . '/config.php', TRUE);
		$config->saveConfig(array('localization' => array('default_language' => $locale)));*/
	}

	/**
	 * Display the administration of a given module
	 * @param string $module
	 * @return string 
	 */
	protected function getViewModuleAdminAction($module) {
		return \app::getModule($module)->displayAdmin();
	}

	/**
	 * Save configuration in the file
	 * @param string $file
	 * @param string $config
	 * @return string 
	 */
	protected function saveConfigAction($file, $config) {
		$configObj = new \config($file, TRUE);
		$action = 'ParsimonyAdmin.loadBlock(\'modules\');';
		if ($_SESSION['permissions'] & 2) {
			if (isset($config['sitename']) && $config['sitename'] != \app::$config['sitename']) {
				$action = 'top.window.location.reload()';
			}
		} else {
			if (isset($config['sitename']) && $config['sitename'] != \app::$config['sitename']) {
				$action = 'top.window.location.reload()';
			}
			$config = array('sitename' => $config['sitename']);
		}
		$configObj->saveConfig($config);
		return $this->returnResult(array('eval' => $action, 'notification' => t('The Config has been saved'), 'notificationType' => 'positive'));
	}

	/**
	 * Search data in db of a given model 
	 * @param string $module
	 * @param string $entity
	 * @param string $search
	 * @return string|false
	 */
	protected function searchDataAction($module, $entity, $search, $limit = 10) {
		$obj = \app::getModule($module)->getEntity($entity);
		$wheres = array();
		foreach ($obj->getFields() as $field) {
			if ($field->type !== ''){ /* field_formasso */
				$wheres[] = $field->sqlFilter($search);	
			}
		}
	
		$obj->where(implode(' OR ', $wheres))->limit($limit);
		$modifModel = TRUE; /* To enable edit link */
		ob_start();
		require('modules/admin/views/datagrid.php');
		return ob_get_clean();
	}

	/**
	 * Display the datagrid
	 * @param string $module
	 * @param string $entity
	 * @param string $page
	 * @return string|false
	 */
	protected function datagridAction($module, $entity, $page, $limit = 10) {
		$obj = \app::getModule($module)->getEntity($entity)->limit($limit);
		$modifModel = TRUE; /* To enable edit link */
		ob_start();
		require('modules/admin/views/datagrid.php');
		return ob_get_clean();
	}

	public function structureTree($obj) {
		$idPage = '';
		$id = $obj->getId();
		$textContent = $id;
		if($id === 'content'){
			$this->initObjects();
			$obj = \app::$response->page;
			$idPage = ' data-page="' . $obj->getId() . '"';
			$textContent = '&nbsp;content - <span class="tree_selector_title">Page '. $obj->getTitle() . '</span>';
		} elseif($id === 'container') {
			$textContent = '&nbsp;container -  <span class="tree_selector_title">Theme ' . $obj->getName() . '</span>';
		}
		$html = '<ul class="tree_selector container parsicontainer" id="treedom_' . $id . '"' . $idPage . '><span class="arrow_tree"></span>' . $textContent;
		foreach ($obj->getBlocks() AS $block) {
			$idBlock = $block->getId();
			if ($block instanceof \core\blocks\container || $idBlock === 'content'){
				$html .= $this->structureTree($block);
			} else {
				$devices = $block->getConfig('devices');
				if(is_array($block->getConfig('devices'))){
					$html .= '<li class="tree_selector parsimonyblock' . (in_array(DEVICE, $devices) ? '' : ' notDisplayed') . '" id="treedom_' . $idBlock . '"> ' . $idBlock . ' <img src="http://parsimony.mobi/admin/img/devices/' . implode('.svg" width="14"> <img src="http://parsimony.mobi/admin/img/devices/', $block->getConfig('devices') ). '.svg" width="14"> </li>';
				} else {
					$html .= '<li class="tree_selector parsimonyblock" id="treedom_' . $idBlock . '"> ' . $idBlock . '</li>';
				}
			}
		}
		$html .= '</ul>';
		return $html;
	}

	/**
	 * Display the datagrid preview
	 * @param string $properties
	 * @param string $relations
	 * @param string $pagination
	 * @param string $nbitem
	 * @return string|false
	 */
	protected function datagridPreviewAction(array $properties = array(), array $relations = array(), $pagination = false, $nbitem = 5) {
		if (!empty($properties)) {
			if (isset($relations)) $view = new \view($properties, $relations);
			else $view = new \view($properties);
			$view->limit(10);
			$view->setPagination(TRUE);
			$view->buildQuery();
			$obj = $view;
			ob_start();
			$sql = $obj->getSQL();
			$search = array('select ', ' from ', ' where ', ' order by ', ' group by ', ' limit ');
			$replace = array('<span style="font-weight:bold">SELECT</span> ', '<br><span style="font-weight:bold">FROM</span> ', '<br><span style="font-weight:bold">WHERE</span> ', '<br><span style="font-weight:bold">ORDER BY</span> ', '<br><span style="font-weight:bold">GROUP BY</span> ', '<br><span style="font-weight:bold">LIMIT</span> ');
			echo '<div id="generatedsql">' . str_replace($search, $replace, $sql['query']) . '</div>';
			require('modules/admin/views/datagrid.php');
			echo '<script> document.getElementById("labelresult").textContent = "( ' . $sql['pagination']->getNbRow() . ' )";</script>';
			return ob_get_clean();
		} else {
			return t('No data for this query.');
		}
	}

	/**
	 * Get the view Update Form of a given id
	 * @param string $module
	 * @param string $entity
	 * @param string $id
	 * @return string 
	 */
	protected function getViewUpdateFormAction($module, $entity, $id) {
		$obj = \app::getModule($module)->getEntity($entity);
		\app::$request->setParam('idviewupdate' , $id); // set value to be used in prepared query
		return str_replace('action=""','target="formResult" action=""',$obj->where($module . '_' . $entity . '.' . $obj->getId()->name. '=:idviewupdate')->fetch()->getViewUpdateForm());
	}

	/**
	 * Get the admin view of a given model
	 * @param string $model
	 * @return string|false
	 */
	protected function getViewAdminModelAction($model) {
		list($module, $model) = explode(' - ', $model);
		$obj = \app::getModule($module)->getEntity($model)->limit('10');
		ob_start();
		require('modules/admin/views/manageModel.php');
		return ob_get_clean();
	}

	/**
	 * Add a new entry in a table
	 * @param string $entity
	 * @return string 
	 */
	protected function addNewEntryAction($entity) {
		list($module, $entity) = explode(' - ', $entity);
		$obj = \app::getModule($module)->getEntity($entity);
		$res = $obj->insertInto($_POST);
		if (is_numeric($res)) {
			$return = array('eval' => 'ParsimonyAdmin.displayConfBox(BASE_PATH + "admin/action", "TOKEN=" + TOKEN + "&model=' . $module . ' - ' . $entity . '&action=getViewAdminModel", true);document.getElementById("preview").contentWindow.location.reload()', 'notification' => t('The data have been added'), 'notificationType' => 'positive');
		} elseif ($res === FALSE) {
			$return = array('notification' => t('The data haven\'t been added', FALSE), 'notificationType' => 'negative');
		} else {
			$return = array('notification' => t('The data haven\'t been added', FALSE) . ' : ' . $res, 'notificationType' => 'negative');
		}
		return $this->returnResult($return);
	}
	
	protected function displayInsertFormAssoAction($entity) {
		list($module, $entity) = explode(' - ', $entity);
		$obj = \app::getModule($module)->getEntity($entity);
		$css = '<style>h2,#conf_box_close{display:none}</style>';
		return $css . str_replace('name="action" value="addNewEntry"', 'name="action" value="addNewEntryFormAsso"', $obj->getViewAddForm());
	}
	
	protected function addNewEntryFormAssoAction($entity) {
		list($module, $entity) = explode(' - ', $entity);
		$obj = \app::getModule($module)->getEntity($entity);
		$res = $obj->insertInto($_POST);
		if (is_numeric($res)) {
			$values = $_POST[$module . '_' . $entity];
			$return = array('id' => $res, 'title' => $values[$obj->getBehaviorTitle()]);
			return json_encode($return);
		} elseif ($res === FALSE) {
			$return = array('notification' => t('The data haven\'t been added', FALSE), 'notificationType' => 'negative');
		} else {
			$return = array('notification' => t('The data haven\'t been added', FALSE) . ' : ' . $res, 'notificationType' => 'negative');
		}
		return $this->returnResult($return);
	}

	/**
	 * Update an entry in a table
	 * @param string $entity
	 * @return string 
	 */
	protected function updateEntryAction($entity) {
		list($module, $entity) = explode(' - ', $entity);
		$obj = \app::getModule($module)->getEntity($entity);
		if (isset($_POST['update'])) {
			$res = $obj->update($_POST);
		} elseif (isset($_POST['delete'])) {
			$res = $obj->delete($_POST[$module . '_' . $entity][$obj->getId()->name]);
		}
		if ($res === TRUE) {
			$return = array('eval' => 'ParsimonyAdmin.displayConfBox(BASE_PATH + "admin/action", "TOKEN=" + TOKEN + "&model=' . $module . ' - ' . $entity . '&action=getViewAdminModel", true);document.getElementById("preview").contentWindow.location.reload()', 'notification' => t('The data have been modified'), 'notificationType' => 'positive');
		} elseif ($res === FALSE) {
			$return = array('notification' => t('The data haven\'t been modified', FALSE), 'notificationType' => 'negative');
		} else {
			$return = array('notification' => t('The data haven\'t been modified', FALSE) . ' : ' . $res, 'notificationType' => 'negative');
		}
		return $this->returnResult($return);
	}

	/**
	 * Get the view of rights
	 * @return string|false
	 */
	protected function getViewAdminRightsAction() {
		return $this->getView('manageRights');
	}

	/**
	 * Get the view in order to change current language
	 * @return string|false
	 */
	protected function getViewAdminLanguageAction() {
		return $this->getView('manageLanguage');
	}

	/**
	 * Get the preview of the adding form
	 * @param string $module
	 * @param string $model
	 * @return string 
	 */
	protected function getPreviewAddFormAction($module, $model) {
		return \app::getModule($module)->getEntity($model)->getViewAddForm();
	}

	/**
	 * Upload file
	 * @param string $path
	 * @return string 
	 */
	protected function uploadAction($path, $size = 104857600, $allowedExt = 'image') {
		try {
			$upload = new \core\classes\upload($size, $allowedExt, $path . '/');
			$result = $upload->upload($_FILES['fileField']);
		} catch (\Exception $exc) {
			$return = array('notification' => $exc->getMessage(), 'notificationType' => 'negative');
			return $this->returnResult($return);
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

	 /**
	 * Get the rules of a css selector and return them in json
	 * @param string $filePath
	 * @param string $selector
	 * @return string 
	 */
	protected function loadBlockAction($blockName) {
		ob_start();
		include('admin/blocks/'.$blockName.'/view.php');
		$html = ob_get_clean();
		return $html;
	}

	/**
	 * Save rights
	 * @param array $type
	 * @param array $modulerights
	 * @param array $modelsrights
	 * @param array $pagesrights
	 * @return string 
	 */
	protected function saveRightsAction($type, $modulerights = array(), $modelsrights = array(), $pagesrights = array()) {

		/* can't update rights for role admin ou his own role */
		if (isset($modulerights[1])) {
			unset($modulerights[1]);
		}
		if (isset($modulerights[$_SESSION['id_role']])) {
			unset($modulerights[$_SESSION['id_role']]);
		}

		/* Save rights for modules */
		if (!empty($modulerights)) {
			foreach (reset($modulerights) as $moduleName => $value) {
				$module = \module::get($moduleName);
				foreach ($modulerights as $numRole => $rights) {
					if ($modulerights[$numRole][$moduleName] === 'on' || $moduleName === 'core')
						$module->setRights($numRole, 1);
					else
						$module->setRights($numRole, 0);

				}
				$module->save();
			}
		}

		/* Save rights for models : entities/fields */
		if (!empty($modelsrights)) {
				foreach (reset($modelsrights) as $moduleName => $module) {
					$moduleObj = \app::getModule($moduleName);
					foreach ($modulerights as $numRole => $rights) {
						$modelsrights[$numRole][$moduleName] = json_decode($modelsrights[$numRole][$moduleName], TRUE);
					}
					foreach ($moduleObj->getModel() as $entityName => $entity) {
						foreach ($modulerights as $numRole => $rights) {
							$entity->setRights($numRole, (int)$modelsrights[$numRole][$moduleName][$entityName]['rights']);
							foreach ($modelsrights[$numRole][$moduleName][$entityName]['fields'] as $fieldName => $rights) {
								$entity->getField($fieldName)->setRights($numRole, (int)$rights);
							}
						}
						$entity->save();
					}
				}
		}
			 /* Save rights for pages */
		if (!empty($pagesrights)) {
				foreach (reset($pagesrights) as $moduleName => $module) {
					$moduleObj = \app::getModule($moduleName);
					foreach ($module as $pageId => $pages) {
						$page = $moduleObj->getPage($pageId);
						foreach ($modulerights as $numRole => $rights) {
							if ($pagesrights[$numRole][$moduleName][$pageId]['display'] === 'on' || $numRole === 1)
								$page->setRights($numRole, 1);
							else
								$page->setRights($numRole, 0);
						}
						$page->save();
					}
				}
		}

		$return = array('notification' => t('The Permissions have been saved'), 'notificationType' => 'positive');
		return $this->returnResult($return);
	}

	/**
	 * Save model
	 * @return string
	 */
	protected function saveModelAction($module, $list, $oldSchema) {
		$schema = json_decode($list);
		$oldSchema = json_decode($oldSchema, TRUE);
		$tableExists = array();
		/* Get roles ids with behavior anonymous */
		$rolesBehaviorAnonymous = array();
		foreach (\app::getModule('core')->getEntity('role') as $role) {
			if($role->permissions == 0) {
				$rolesBehaviorAnonymous[] = $role->id_role;
			}
		}
		if (is_array($schema)) {
			foreach ($schema as $tableKey => $table) {

				/* Prepare entity's properties */
				$tplProp = '';
				$tplParam = '';
				$tplAssign = '';
				$args = array();
				$matchOldNewNames = array();
			
				foreach ($table->properties as $fieldName => $fieldProps) {
					list($name, $type) = explode(':', $fieldName);
					$tplProp .= "\t".'protected $' . $name . ';'.PHP_EOL; //generates attributes
					$tplParam .= '\\' . $type . ' $' . $name . ','; //generates the constructor parameters
					$tplAssign .= "\t\t".'$this->' . $name . ' = $' . $name . ";\n"; //generates assignments in the constructor
					$reflectionObj = new \ReflectionClass($type);
					$fieldProps = json_encode($fieldProps);
					$fieldProps = json_decode($fieldProps, true);
					if(isset($fieldProps['oldName']) && ($fieldProps['oldName'] != $name && !empty($fieldProps['oldName']))) $matchOldNewNames[$name] = $fieldProps['oldName'];
					unset($fieldProps['oldName']);

					$field = $reflectionObj->newInstanceArgs(array('name' => $fieldName, 'properties' => $fieldProps));
					if(!isset($fieldProps['rights'])){
						/* Set rights forbidden for non admins, admins are allowed by default */
						foreach ($rolesBehaviorAnonymous as $id_role) {
							$field->setRights($id_role, 0);
						}
					}
					$args[] = $field;
					
				}
				
				/* Prepare entity's php file */
			$tpl = '<?php
namespace ' . $module . '\model;
/**
* Description of entity ' . $table->name . '
* @author Parsimony
* @top ' . $table->top . '
* @left ' . $table->left . '
*/
class ' . $table->name . ' extends \entity {

' . $tplProp . '

	public function __construct(' . substr($tplParam, 0, -1) . ') {
		parent::__construct();
' . $tplAssign . '
	}
';

				$model = 'modules/' . $module . '/model/' . $table->name . '.php';
				if (!is_file($model)) {
					$tpl .= '// DON\'T TOUCH THE CODE ABOVE ##########################################################'.PHP_EOL.'}'.PHP_EOL.'?>';
				} else {
					$code = file_get_contents($model);
					$tpl = preg_replace('@<\?php(.*)}(.*)?(ABOVE ##########################################################)?@Usi', $tpl, $code);
				}
				
				\tools::file_put_contents($model, $tpl);
				include_once($model);
				$oldObjModel = FALSE;
				if (is_file('modules/' . $module . '/model/' . $table->oldName . '.' . \app::$config['dev']['serialization'])) {
					$oldObjModel = \tools::unserialize('modules/' . $module . '/model/' . $table->oldName);
				}

				// Change table Name if changes
				if ($table->name !== $table->oldName) {
					\PDOconnection::getDB()->exec('ALTER TABLE ' . PREFIX . $module . '_' . $table->oldName . ' RENAME TO ' . $module . '_' . $table->name . ';');
					unlink('modules/' . $module . '/model/' . $table->oldName . '.php');
					unlink('modules/' . $module . '/model/' . $table->oldName . '.' . \app::$config['dev']['serialization']);
				}
				
				// make a reflection object
				$reflectionObj = new \ReflectionClass($module . '\\model\\' . $table->name);
				
				$newObj = $reflectionObj->newInstanceArgs($args);
				$newObj->__wakeup();
				
				/* Set entity's properties */
				$newObj->setTitle($table->title);
				$newObj->behaviorTitle = $table->behaviorTitle;
				$newObj->behaviorDescription = $table->behaviorDescription;
				$newObj->behaviorKeywords = $table->behaviorKeywords;
				$newObj->behaviorImage = $table->behaviorImage;
				/* Set entity's rights */
				if (is_object($oldObjModel)) {
					$newObj->setAllRights($oldObjModel->getAllRights());
				} else {
					/* Set rights forbidden for non admins, admins are allowed by default */
					foreach ($rolesBehaviorAnonymous as $id_role) {
						$newObj->setRights($id_role, 0);
					}
				}
				if ($oldObjModel !== FALSE) {
					$nameFieldBefore = '';
					$newObj->__wakeup(); /* to insert entity's ref into fields */
					foreach ($newObj->getFields() as $field) {
						if (isset($oldSchema[$table->name]) && isset($oldSchema[$table->name][$field->name])) {
							$field->alterColumn($nameFieldBefore);
						} elseif (isset($matchOldNewNames[$field->name])) {
							$field->alterColumn($nameFieldBefore, $matchOldNewNames[$field->name]);
						} else {
							$field->addColumn($nameFieldBefore);
						}
						if ($field->type !== ''){ /* field_formasso */
							$nameFieldBefore = $field->name;
						}
					}
					if (isset($oldSchema[$table->name])) {
						foreach ($oldSchema[$table->name] as $fieldName => $field) {
							$newFields = $newObj->getFields();
							if (!isset($newFields[$fieldName]) && !in_array($fieldName, $matchOldNewNames)) {
								$sql = 'ALTER TABLE ' . PREFIX . $module . '_' . $table->name . ' DROP ' . $fieldName;
								\PDOconnection::getDB()->exec($sql);
								//$field->deleteColumn();  //removed to avoid old includes
							}
						}
					}
				}else {
					$newObj->createTable();
				}
				\tools::serialize('modules/' . $module . '/model/' . $table->name , $newObj);
				$tableExists[] = $table->name;
			}
		}
		$entities = glob('modules/' . $module . '/model/*.php');
		foreach (is_array($entities) ? $entities : array() as $filename) {
			$modelName = substr(substr(strrchr($filename, "/"), 1), 0, -4);
			if (!in_array($modelName, $tableExists)) {
				\app::getModule($module)->getEntity($modelName)->deleteTable();
			}
		}
		return ' ';
	}

	protected function uptodateAction($url) {

		$majTitle = 'maj-'.time();
		$zipFile = 'var/maj/'.$majTitle.'.zip';
		if (!is_dir('var/maj/'.$majTitle.'/extract'))
			mkdir('var/maj/'.$majTitle.'/extract', 0755, TRUE);
		if (!is_dir('var/maj/'.$majTitle.'/backup/modules'))
			mkdir('var/maj/'.$majTitle.'/backup/modules', 0755, TRUE);

		if(copy($url,'var/maj/'.$majTitle.'.zip') === TRUE){
			//echo 'Maj downloaded';
			$zip = new \ZipArchive();
			if($zip->open($zipFile) === TRUE){
				if($zip->extractTo('var/maj/'.$majTitle.'/extract') === TRUE){
					//echo 'Zip extracted';
					//get main directory 
					$searchDir = glob('var/maj/'.$majTitle.'/extract/parsimony*', GLOB_ONLYDIR);
					if(is_array($searchDir)){
						$mainDir = basename($searchDir[0]);
					}
					if(is_dir('var/maj/'.$majTitle.'/extract/'.$mainDir.'/lib') && is_dir('var/maj/'.$majTitle.'/extract/'.$mainDir.'/modules/core') && is_dir('var/maj/'.$majTitle.'/extract/'.$mainDir.'/modules/admin') && is_dir('var/maj/'.$majTitle.'/extract/'.$mainDir.'/modules/blog')){
						if(rename('lib', 'var/maj/'.$majTitle.'/backup/lib') === TRUE){
							//echo 'Lib directory saved in backup';
							if(rename('modules/core', 'var/maj/'.$majTitle.'/backup/modules/core') === TRUE && rename('modules/admin', 'var/maj/'.$majTitle.'/backup/modules/admin') === TRUE && rename('modules/blog', 'var/maj/'.$majTitle.'/backup/modules/blog') === TRUE){
								//echo 'Module directory saved in backup';
								if(rename('var/maj/'.$majTitle.'/extract/'.$mainDir.'/lib', 'lib') === TRUE){
									if(rename('var/maj/'.$majTitle.'/extract/'.$mainDir.'/modules/core', 'modules/core') === TRUE && rename('var/maj/'.$majTitle.'/extract/'.$mainDir.'/modules/admin', 'modules/admin') === TRUE && rename('var/maj/'.$majTitle.'/extract/'.$mainDir.'/modules/blog', 'modules/blog') === TRUE){
										//echo 'Succesfull';
										return TRUE;
									}else{
										rename('lib', 'var/maj/'.$majTitle.'/extract/'.$mainDir.'/lib');
										rename('var/maj/'.$majTitle.'/backup/modules/core', 'modules/core');
										rename('var/maj/'.$majTitle.'/backup/modules/admin', 'modules/admin');
										rename('var/maj/'.$majTitle.'/backup/modules/blog', 'modules/blog');
										rename('var/maj/'.$majTitle.'/backup/lib', 'lib');
									}
								}else{
									rename('var/maj/'.$majTitle.'/backup/modules/core', 'modules/core');
									rename('var/maj/'.$majTitle.'/backup/modules/admin', 'modules/admin');
									rename('var/maj/'.$majTitle.'/backup/modules/blog', 'modules/blog');
									rename('var/maj/'.$majTitle.'/backup/lib', 'lib');
								}
							}else{
								rename('var/maj/'.$majTitle.'/lib', 'lib');
							}
						}
					}
					$zip->close();
				}else{

				}
			}
		}
		return FALSE;
	}

	/**
	 * Save module page in putting data in module.obj
	 */
	public function saveAll() {
		$this->theme->save();
		if (isset($this->page) && is_object($this->page) ) {
			\tools::serialize(PROFILE_PATH . \app::$request->getParam('MODULE') . '/pages/' . $this->page->getId(), $this->page);
		}
		\tools::serialize(PROFILE_PATH . \app::$request->getParam('MODULE') . '/module', $this->module);
	}

	/**
	 * Sanitize url
	 * @param string $url
	 * @return string 
	 */
	protected function titleToUrlAction($url) {
		$url = \tools::sanitizeString($url);
		return $url;
	}

	/**
	 * Check if a page is overrided by another
	 * @param string $module
	 * @param string $idpage
	 * @param string $regex
	 * @return string 
	 */
	public function checkOverridedPageAction($module, $idpage, $regex) {
		try {
			$module = \app::getModule($module);
			$page = $module->checkIfPageOverrideAnother($idpage, $regex);
			if ($page !== FALSE) {
				return $page->getId() . ' : ' . s($page->getTitle());
			}
			return '';
		} catch (\Exception $exc) {
			return '';
		}
	}

	/**
	 * Get a back Up
	 * @param string $replace
	 * @param string $file
	 * @return string 
	 */
	protected function getBackUpAction($replace, $file) {
		$old = file_get_contents('var/backup/'.PROFILE.'/' . $file . '-' . $replace . '.bak');
		$old = preg_replace('#.*<\?php __halt_compiler\(\); \?>#Usi', '', $old);
		file_put_contents($file, $old);
		return $old;
	}

	/**
	 * Save content 
	 * @param string $file
	 * @param string $code
	 * @return string 
	 */
	protected function saveCodeAction($file, $code) {
		return \tools::file_put_contents($file, $code);
	}
	
	/**
	 * Create directory 
	 * @param string $directory
	 * @param string $mask
	 * @return string 
	 */
	protected function createDirAction($directory, $mask=0755) {
		return \tools::createDirectory($directory, $mask=0755);
	}
	
	/**
	 * Remove directory recursively
	 * @param string $directory
	 * @return string 
	 */
	protected function deleteDirAction($dir){
		return \tools::rmdir($dir);
	}
	
	/**
	 * Unlink file
	 * @param string $file
	 * @return string 
	 */
	protected function deleteFileAction($file){
		str_replace('..', '', $file); // parent directory forbidden
		return unlink($file);
	}

	 /**
	 * Get, decode base 64 & file put content
	 * @param string $file
	 * @param string $code
	 * @return string 
	 */
	protected function savePictureAction($file, $code) {
		return \tools::file_put_contents($file, base64_decode($code));
	}
	
	/**
	 * Install a module
	 * @param string $module
	 * @return string 
	 */
	protected function installModuleAction($module = FALSE) {
		if(is_file('modules/' . $module . '/module.php') && !isset(\app::$config['modules'][$module])) {
			try {
				\app::$activeModules[$module] = '1';
				$moduleObj = \app::getModule($module);
				if($moduleObj->install()){
					$configObj = new \core\classes\config('profiles/' . PROFILE . '/config.php', TRUE);
					$update = array('modules' => array($module => (method_exists($moduleObj, '__wakeup')) ? '3' : '1'));
					$configObj->saveConfig($update);
					return TRUE;
				}
			} catch (\Exception $ex) {
				return FALSE;
			}
			
		}
	}
	
	/**
	 * Package a module
	 * @param string $module
	 * @return string 
	 */
	protected function packageModuleAction($module = 'core') {
		\tools::rmdir('var/pack/');
		\tools::copy_dir('modules/' . $module . '/', 'var/pack/' . $module . '/');
		
		/* If this module has a profile, retrieve profile */
		if(is_dir('profiles/' . PROFILE . '/modules/' . $module . '/')) { 
			/* Remove concats */
			$concats = glob('profiles/' . PROFILE . '/modules/' . $module . '/*concat_*');
			if(is_array($concats) && !empty($concats)) {
				foreach ($concats as $file) {
					unlink($file);
				}
			}
			\tools::copy_dir('profiles/' . PROFILE . '/modules/' . $module . '/', 'var/pack/' . $module . '/');
		}
		
		/* Clean rights: keep only default rights */
		
		/* Module Rights */
		$objModule = \app::getModule($module);
		$rolesToRemove = array();
		foreach (\app::getModule('core')->getEntity('role')->where('id_role > 4') as $role) {
			$rolesToRemove[] = $role->id_role;
			$objModule->setRights($role->id_role, 1);
		}
		\tools::serialize('var/pack/' . $module . '/module', $objModule);
		
		/* Pages rights */
		foreach($objModule->getPages() AS $page) {
			foreach ($rolesToRemove as $idRole) {
				$page->setRights($idRole, 1);
			}
			\tools::serialize('var/pack/' . $module . '/pages/' . $page->getId(), $page);
		}
		
		/* Model rights */
		foreach($objModule->getModel() AS $entity){
			foreach ($rolesToRemove as $idRole) {
				$entity->setRights($idRole, 15);
				foreach($entity->getFields() AS $field)  {
					$field->setRights($idRole, 7);
				}
			}
			\tools::serialize('var/pack/' . $module . '/model/' . $entity->getName(), $entity);
		}
		
		/* generate and send Zip to client */
		$phar = new \PharData('profiles/' . PROFILE . '/modules/' . $module . '.zip');
		$phar->buildFromDirectory('./var/pack');
		\app::$response->setHeader('Content-type', 'application/zip');
		return 'profiles/' . PROFILE . '/modules/' . $module . '.zip';

	}
	
	/**
	 * Init objects theme module & page
	 */
	private function initObjects() {
		$this->theme = \theme::get(\app::$request->getParam('THEMEMODULE'), \app::$request->getParam('THEME'));
		$this->module = \app::getModule(\app::$request->getParam('MODULE'));
		$IDPage = \app::$request->getParam('IDPage');
		if ($IDPage && is_numeric($IDPage)) {
			\app::$response->page = $this->page = $this->module->getPage($IDPage);
		}
	}

	/**
	 * Wrap result of an action in an instance of Page in order to display it in a popup
	 * @return string 
	 */
	protected function actionAction() {
		/* Init a page */
		\app::$response->page = new \page(1, 'admin');
		if (isset($_POST['action'])) {
			$content = $this->controller($_POST['action'], 'POST');
			if (isset($_POST['popup']) && $_POST['popup'] === 'yes') {
				ob_start();
				require('modules/admin/views/popup.php');
				return ob_get_clean();
			} else {
				return $content;
			}
		}
	}

	/**
	 * Get an entity
	 * @param string $role
	 * @return integer
	 */
	public function getRights($role) { /* to respect prototype */
		if ($_SESSION['permissions'] > 0)
			return 1;
		else
			return 0;
	}

}

?>
