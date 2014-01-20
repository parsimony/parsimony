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

namespace core\classes;

/**
 * Module Class 
 * Manages modules
 */
class module {

	/** @var string */
	protected $title;

	/** @var string */
	protected $name;

	/** @var array */
	protected $pages = array();

	/** @var array */
	private $model = array();

	/** @var array */
	protected $rights = array();

	/**
	 * Construct a module
	 * @param string $name module name
	 */
	public function __construct($name) {
		$name = preg_replace('@[^a-zA-Z0-9]@', '', $name);
		if (!empty($name)) {
			$this->name = $name;
		} else {
			throw new \Exception(t('Name can\'t be empty', FALSE));
		}
	}

	/**
	 * Get name of the module
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Get module of a given name
	 * @static
	 * @param string $name
	 * @return module
	 */
	public static function get($name) {
		if (isset(app::$config['modules']['active'][$name]) || $name === 'admin') {
			if (!class_exists($name . '\\module', false))
				include('modules/' . $name. '/module.php');
			$path = stream_resolve_include_path($name . '/module.' . \app::$config['dev']['serialization']);
			if ($path !== FALSE) {
				return unserialize(file_get_contents($path));
			} else {
				$className = '\\' . $name . '\\module';
				$module = new $className($name);
				$module->save();
				return $module;
			}
		} else
			throw new \Exception(t('Module is disabled', FALSE) . ' : ' . s($name));
	}

	/**
	 * Get pages of the module
	 * @return array of pages
	 */
	public function getPages() {
		$pages = array();
		foreach ($this->pages as $key => $page) {
			$page = $this->getPage($key);
			if ($page !== FALSE)
				$pages[$key] = $page;
		}
		return $pages;
	}

	/**
	 * Check if a page can override another page regarding its position and regex
	 * @return page|FALSE
	 */
	public function checkIfPageOverrideAnother($idPage, $regex = FALSE) {
		$pageToCheck = $this->getPage($idPage);
		if ($regex === FALSE)
			$regex = $pageToCheck->getRegex();
		$pages = $this->getPages();
		$mark = FALSE;
		foreach ($pages AS $id => $page) {
			if ($idPage == $id) {
				$mark = TRUE;
			} elseif ($mark === TRUE) {
				if (preg_match($regex, $page->getURL())) {
					return $page;
				}
			}
		}
		return FALSE;
	}

	/**
	 * Reoder pages of the module
	 * @param array $order
	 * @return array of pages
	 */
	public function reoderPages($order) {
		$newOrder = array();
		foreach ($order as $value) {
			$newOrder[$value] = $this->pages[$value];
		}
		$this->pages = $newOrder;
		return $this->save();
	}

	/**
	 * Get page of a given id
	 * @param integer $id
	 * @return page
	 */
	public function getPage($id) {
		$file = stream_resolve_include_path($this->name . '/pages/' . $id . '.' . \app::$config['dev']['serialization']);
		if ($file !== FALSE) {
			return \tools::unserialize(substr($file, 0, -4));
		} else {
			throw new \Exception(t('Page doesn\'t exist', FALSE) . ' ,' . $this->name . ' : ' . $id);
		}
	}

	/**
	 * Call a method of block
	 * @param string $idpage
	 * @param string $theme
	 * @param string $id
	 * @param string $method
	 * @return mixed
	 */
	public function callBlockAction($idPage, $theme, $id, $method) {
		if (empty($theme)) {
			$blockObj = & $this->getPage($idPage)->searchBlock($id);
		} else {
			$theme = \theme::get($this->name, $theme, THEMETYPE);
			$blockObj = $theme->searchBlock($id, $theme);
		}
		if (method_exists($blockObj, $method . 'Action')) {
			return $this->callMethod($blockObj, $method . 'Action');
		}
		return FALSE;
	}

	/**
	 * Call a method of field
	 * @param string $entity
	 * @param string $fieldName
	 * @param string $method
	 * @return mixed
	 */
	public function callFieldAction($entity, $fieldName, $method) {
		$fieldObj = $this->getEntity($entity)->getField($fieldName);
		if (method_exists($fieldObj, $method . 'Action')) {
			return $this->callMethod($fieldObj, $method . 'Action');
		}else{
			return FALSE;
		}
	}

	/**
	 * Get fields
	 * @return array $fields
	 */
	public function getFields() {
		$fieldList = glob('modules/' . $this->name . '/fields/*.php');
		$fieldList = is_array($fieldList) ? $fieldList : array(); // fix
		foreach ($fieldList as &$filename) {
			$filename = basename($filename, '.php');
		}
		return $fieldList;
	}

	/**
	 * Get module configs
	 * @return array|false
	 */
	public function getConfigs() {
		if (isset(\app::$config[$this->name]))
			return \app::$config[$this->name];
		else
			return FALSE;
	}

	/**
	 * Get a config 
	 * @param string $key
	 * @return string|false
	 */
	public function getConfig($key) {
		if (isset(\app::$config[$this->name][$key]))
			return \app::$config[$this->name][$key];
		else
			return FALSE;
	}

	/**
	 * Set a config 
	 * @param string $key
	 * @param string $val
	 * @return module
	 */
	public function setConfig($key, $val) {
		\app::$config[$this->name][$key] = $val;
		return $this;
	}

	/**
	 * Get model : all entities declared in the module
	 * @return array
	 */
	public function getModel() {
		$entities = glob('modules/' . $this->name . '/model/*.php');
		$entities = is_array($entities) ? $entities : array(); // fix
		foreach ($entities as &$filename) {
			$model = basename($filename, '.php');
			$this->model[$model] = $this->getEntity($model);
		}
		return $this->model;
	}

	/**
	 * GetView
	 * @param string $name view name
	 * @return string
	 */
	public function getView($name) {
		ob_start();
		include($this->name . '/views/' . $name . '.php');
		return ob_get_clean();
	}

	/**
	 * Get an entity
	 * @param string $entity
	 * @return entity|false
	 */
	public function getEntity($entity) {
		if (isset($this->model[$entity])) {
			return $this->model[$entity];
		} else {
			if (is_file('modules/' . $this->name . '/model/' . $entity . '.' . \app::$config['dev']['serialization'])) {
				if (!class_exists($this->name . '\\model\\' . $entity))
					include ('modules/' . $this->name . '/model/' . $entity . '.php');
				$this->model[$entity] = \tools::unserialize('modules/' . $this->name . '/model/' . $entity);
				return $this->model[$entity];
			} else {
				throw new \Exception(t('Entity doesn\'t exist', FALSE). ' : ' . s($entity));
			}
		}
	}

	/**
	 * Get themes : all themes declared in the module
	 * @return array
	 */
	public function getThemes() {
		$themes = array();
		$themesModules = glob('modules/' . $this->name . '/themes/*', GLOB_ONLYDIR); // for themes by default
		$themesProfiles = glob(PROFILE_PATH . $this->name . '/themes/*', GLOB_ONLYDIR); // for themes created by user
		$themelist = array_merge((is_array($themesModules) ? $themesModules : array()), (is_array($themesProfiles) ? $themesProfiles : array()));
		foreach ($themelist as $filename) {
			$themeName = basename($filename);
			$themes[$themeName] = $themeName;
		}
		return $themes;
	}

	/**
	 * Save the module
	 * @return bool
	 */
	public function save() {
		return \tools::serialize(PROFILE_PATH . $this->name . '/module',$this);
	}

	/**
	 * Get admin view of module if exists
	 * @return string|false
	 */
	public function displayAdmin() {
		if (is_file('modules/' . $this->name . '/admin/index.php')) {
			ob_start();
			require('modules/' . $this->name . '/admin/index.php');
			return ob_get_clean();
		} else {
			return FALSE;
		}
	}

	/**
	 * Install Module, create model in DB
	 */
	public function install() {
		foreach ($this->getModel() AS $model) {
			$this->getEntity($model->getName())->createTable();
		}
	}

	/**
	 * Uninstall Module, delete model of DB
	 */
	public function uninstall() {
		foreach ($this->getModel() AS $model) {
			$this->getEntity($model->getName())->deleteTable();
		}
	}

	/**
	 * Controller of HTTP Request: GET,POST,PUT,DELETE
	 * @param string $url , by default http://mysite.com/mymodule/ --> onepage.html <--
	 * @param string $httpMethod
	 * @return page|false
	 */
	public function controller($url, $httpMethod = 'GET') {
		if (!method_exists($this, $url . $httpMethod . 'Action')) {
			$httpMethod = '';
		}
		$methodName = $url . $httpMethod . 'Action';
		if (method_exists($this, $methodName)) {
			return $this->callMethod($this, $methodName);
		}
		foreach ($this->pages AS $index => $regex) {
			if (preg_match($regex, $url, $_GET)) {
				app::$request->setParams($_GET);
				$page = $this->getPage($index);
				if($page->getRights($_SESSION['id_role']) & DISPLAY)
					return $page;
			}
		}
		return FALSE;
	}
	
	/**
	 * Fill params of methods with params request and call method
	 * @param ojbect $object (type: block, field, module)
	 * @param string $methodName
	 * @return string
	 */
	protected function callMethod($object, $methodName) {
		$class = new \ReflectionClass($object);
		$method = $class->getMethod($methodName);
		$params = array();
		foreach ($method->getParameters() as $param) {
			$name = $param->getName();
			$value = app::$request->getParam($name);
			if ($value !== FALSE) {
				$params[] = $value;
			} elseif ($param->isDefaultValueAvailable()) {
				$params[] = $param->getDefaultValue();
			}else{
				$params[] = '';
			}
		}
		return (string) call_user_func_array(array($object, $methodName), $params); /* cast to stringify booleans */
	}

	/**
	 * Add a Page to the module
	 * @param page $page
	 */
	public function addPage(page $page) {
		$pages = array_reverse($this->pages, true);
		$pages[$page->getId()] = $page->getRegex();
		$pages = array_reverse($pages, true);
		$this->pages = $pages;
		$page->save($this->name);
	}

	/**
	 * Update a Page of the module
	 * @param page $page
	 */
	public function updatePage(page $page) {
		$id = $page->getId();
		$this->pages[$id] = $page->getRegex();
		$page->save();
	}

	/**
	 * Delete a Page of the module
	 * @param page $page
	 */
	public function deletePage(page $page) {
		foreach ($this->pages as $idPage => $thepage) {
			if ($idPage == $page->getId()) {
				unset($this->pages[$idPage]);
				return unlink(PROFILE_PATH . $this->name . '/pages/' . $page->getId() . '.' . \app::$config['dev']['serialization']);
			}
		}
		return FALSE;
	}

	/**
	 * Init config of the module
	 */
	public function initConfig() {
		$config = array();
		include($this->name . '/config.php');
		\app::$config = array_merge(\app::$config, $config);
	}

	/**
	 * Init config of the module
	 */
	public function saveConfig() {
		$configObj = new \config(PROFILE_PATH . '/' . $this->name . '/config.php', TRUE);
		$config = array();
		include($this->name . '/config.php');
		$config = array_intersect_key(\app::$config, $config);
		$configObj->saveConfig($config);
	}

	/**
	 * Update rights for a role
	 * @param string $role
	 * @param integer $rights
	 */
	public function setRights($role, $rights) {
		/* We remove role entry if the role has the maximum of rights ( 1 = DISPLAY:1 ) #performance */
		if($rights === 1){
			if(isset($this->rights[$role])){
				unset($this->rights[$role]);
			}
		}else{
			$this->rights[$role] = $rights;
		}
	}

	/**
	 * Get rights for a role
	 * @param string $role
	 * @return integer
	 */
	public function getRights($role) {
		if (isset($this->rights[$role]))
			return $this->rights[$role];
		return 1;
	}

	/**
	 * Generates the code to build a module
	 * @static function
	 * @param string $name module name
	 * @param string $title module title
	 */
	public static function build($name, $title) {
		$reservedKeywords = array('__halt_compiler', 'abstract', 'and', 'array', 'as', 'break', 'callable', 'case', 'catch', 'class', 'clone', 'const', 'continue', 'declare', 'default', 'die', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach', 'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends', 'final', 'for', 'foreach', 'function', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'isset', 'list', 'namespace', 'new', 'or', 'print', 'private', 'protected', 'public', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'unset', 'use', 'var', 'while', 'xor');

		if (!is_dir('modules/' . $name) && !is_numeric($name) && !in_array($name, $reservedKeywords)) {
			$name = preg_replace('@[^a-zA-Z0-9]@', '', $name);
			$licence = str_replace('{{module}}', $name, file_get_contents("modules/admin/licence.txt"));
			tools::createDirectory('modules/' . $name);
			$template = '<?php
' . $licence . '

namespace ' . $name . ';

/**
 * @title ' . str_replace('\'', '\\\'', $title) . '
 * @description ' . str_replace('\'', '\\\'', $title) . '
 * @version 1
 * @browsers all
 * @php_version_min 5.3
 */

class module extends \module {
	protected $name = \'' . str_replace('\'', '\\\'', $name) . '\';
}
?>';
			file_put_contents('modules/' . $name . '/module.php', $template);
			include('modules/' . $name . '/module.php');
			$name2 = $name . '\\module';
			$mod = new $name2($name);
			$page = new \page(1, $name);
			$page->setModule($name);
			$page->setTitle('Index ' . $name);
			$page->setRegex('@^index$@');
			/* Set rights forbidden for non admins, admins are allowed by default */
			foreach (\app::getModule('core')->getEntity('role') as $role) {
				if($role->state == 0){
					$mod->setRights($role->id_role, 0);
					$page->setRights($role->id_role, 0);
				}
			}
			$mod->addPage($page);
			$mod->save();
			$config = new \config('profiles/'.PROFILE . '/config.php', TRUE);
			$config->add('$config[\'modules\'][\'active\'][\'' . $name . '\']', '0');
			return $config->save();
		}else {
			return FALSE;
		}
	}

	public function __sleep() {
		$props = get_object_vars($this);
		unset($props['model']);
		unset($props['title']);
		unset($props['name']);
		return array_keys($props);
	}

}
