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
 * to contact@parsimony.mobi so we can send you a copy immediately.
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
     * @param string $title module name
     */
    public function __construct($name='',$title='') {
        if(!empty($name)) $this->name = $name;
        if(!empty($title)) $this->title = $title;
    }

    /**
     * Set name of the module
     * @param string $name
     */
    public function setName($name) {
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
     * Get name of the module
     * @return string
     */
    public function getTitle() {
        if(!is_null($this->title)) return $this->title;
	else return $this->name;
    }

    /**
     * Get module of a given name
     * @static
     * @param string $name
     * @return module
     */
    public static function get($name) { 
	if (isset(app::$activeModules[$name])) {
	    if (!class_exists($name . '\\' . $name, false))
		include('modules/' . str_replace('\\', '/', $name) . '/module.php');
            $path = PROFILE_PATH . str_replace('\\', '/', $name) . '/module';
	    if(is_file($path. '.' .\app::$config['dev']['serialization'])){
                return \tools::unserialize($path);
            }else{
                $className = '\\'.$name.'\\'.$name;
                $module = new $className($name,  ucfirst($name));
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
	    if ($page != FALSE)
		$pages[$key] = $page;
	}
	return $pages;
    }
    
    /**
     * Reoder pages of the module
     * @param array $pages
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
	if (is_file(PROFILE_PATH . $this->name . '/pages/' . $id .  '.' .\app::$config['dev']['serialization']))
	    return \tools::unserialize(PROFILE_PATH . $this->name . '/pages/' . $id );
	else
	    throw new \Exception(t('Page doesn\'t exist', FALSE).' ,'.$this->name.' : '.$id);
    }
    

    /**
     * Get list of pages 
     * @static
     * @param string $name
     * @return array
     */
    public static function getPageList($name) {
	$links = array();
	foreach ($this->getPages() AS $page) {
	    $links[$page->getURL()] = $page->getTitle();
	}
	return $links;
    }
    
    /**
     * Call a method of block
     * @param string $name
     * @param string $method
     * @param string $args
     * @return mixed
     */
    public function callBlockAction($name, $method, $args) {
	$blockName = '\\'.$this->name.'\\blocks\\'.$name;
	$params = array();
	parse_str($args, $params);
	return call_user_func_array(array(new $blockName('call'), $method),$params);
    }

    /**
     * Get fields
     * @return array $fields
     */
    public function getFields() {
	$fields = array();
	foreach (glob(PROFILE_PATH . $this->name . '/fields/*.php') as $filename) {
	    $field = basename($filename, '.php');
	    $fields[] = $field;
	}
	return $fields;
    }

    /**
     * Get configs
     * @return config
     */
    public function getConfigs() {
	$this->initConfig();
	return $this->config;
    }

    /**
     * Get a config 
     * @param string $key
     * @return string|false
     */
    public function getConfig($key) {
	$this->initConfig();
	if (isset($this->config[$key]))
	    return $this->config[$key];
	else
	    return FALSE;
    }

    /**
     * Set a config 
     * @param string $key
     * @param string $val
     */
    public function setConfig($key, $val) {
	$this->initConfig();
	$this->config[$key] = $val;
    }

    /**
     * Get model : all entities declared in the module
     * @return array
     */
    public function getModel() {
	foreach (glob('modules/' . $this->name . '/model/*.php') as $filename) {
	    $model = basename($filename, '.php');
	    $this->model[$model] = $this->getEntity($model);
	}
	return $this->model;
    }
    
    /**
     * GetView
     * @return string
     */
    public function getView($name,$themeType = FALSE) {
	$themeType = ($themeType !== FALSE) ? $themeType : THEMETYPE;
	ob_start();
	$path = $this->name.'/views/'.$themeType.'/'.$name.'.php';
	//if(is_file(PROFILE_PATH.$path)) require(PROFILE_PATH.$path);
	//else require('modules/'.$path);
	include($path);
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
	    if (is_file('modules/' . $this->name . '/model/' . $entity . '.' .\app::$config['dev']['serialization'])) {
		if (!class_exists($this->name . '\\model\\' . $entity))
		    include ('modules/' . $this->name . '/model/' . $entity . '.php');
                
		$this->model[$entity] = \tools::unserialize('modules/' . $this->name . '/model/' . $entity );
		return $this->model[$entity];
	    } else {
		throw new \Exception(t('Entity doesn\'t exist', FALSE));
	    }
	}
    }

    /**
     * Get themes : all themes declared in the module
     * @return array
     */
    public function getThemes() {
	$themes = array();
	foreach (glob(PROFILE_PATH . $this->name . '/themes/*', GLOB_ONLYDIR) as $filename) {
	    $themeName = basename($filename);
	    $themes[] = $themeName;
	}
	return $themes;
    }

    /**
     * Save the module
     * @return bool
     */
    public function save() {
	return \tools::file_put_contents(PROFILE_PATH . $this->name . '/module.obj', serialize($this));
    }

    /**
     * Get admin view of module if exists
     * @return string|false
     */
    public function displayAdmin() {
	if (file_exists('modules/' . $this->name . '/admin/index.php')) {
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
     * Controller of GET Request
     * @param string $url , by default http://mysite.com/mymodule/ --> onepage.html <--
     * @return page|false
     */
    public function controller($url) {
	if (method_exists($this, $url.'Action')) {
	    $class = new \ReflectionClass($this);
	    $method = $class->getMethod($url.'Action');
	    $params = array();
	    foreach ($method->getParameters() as $i => $param) {
		$name = $param->getName();
		$value = app::$request->getParam($name);
		if ($value !== FALSE) {
		    $params[] = $value;
		} elseif ($param->isDefaultValueAvailable()) {
		    $params[] = $param->getDefaultValue();
		}
	    }
	    return (string)app::$response->setContent(call_user_func_array(array($this, $url.'Action'), $params),200);
	}
	foreach ($this->pages AS $index => $regex) {
	    if (preg_match($regex, $url, $_GET)) {
		app::$request->setParams($_GET);
		$page = $this->getPage($index);
		//if(isset($_SESSION['idr']) && ($_SESSION['idr']==1 || $page->getRights($_SESSION['idr']) & DISPLAY))
		return app::$response->setContent($page,200);
	    }
	}
	return FALSE;
    }

    /**
     * Controller of GET Request
     * @param string $url , by default http://mysite.com/mymodule/ --> onepage.html <--
     * @return page|false
     */
    public function controllerGET($url) {
	return $this->controller($url);
    }

    /**
     * Controller of POST Request, redirect to controllerGET but we can overridde it in  a child module
     * @param string $url , by default http://mysite.com/mymodule/ --> onepage.html <--
     * @return page|false
     */
    public function controllerPOST($url) {
	return $this->controller($url);
    }

    /**
     * Controller of PUT Request, redirect to controllerGET but we can overridde it in  a child module
     * @param string $url , by default http://mysite.com/mymodule/ --> onepage.html <--
     * @return page|false
     */
    public function controllerPUT($url) {
	return $this->controller($url);
    }

    /**
     * Controller of DELETE Request, redirect to controllerGET but we can overridde it in  a child module
     * @param string $url , by default http://mysite.com/mymodule/ --> onepage.html <--
     * @return page|false
     */
    public function controllerDELETE($url) {
	return $this->controller($url);
    }

    /**
     * Controller of GET Request
     * @param string $url , by default http://mysite.com/mymodule/ --> onepage.html <--
     * @return page|false
     */
    /* public function __call($name, $arguments) {
      foreach ($this->pages AS $index => $regex) {
      if (preg_match($regex, $url, $_GET)) {
      $page = $this->getPage($index);
      //if(isset($_SESSION['idr']) && ($_SESSION['idr']==1 || $page->getRights($_SESSION['idr']) & DISPLAY))
      return $page;
      }
      }
      return FALSE;
      } */

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
		return unlink(PROFILE_PATH . $this->name . '/pages/' . $page->getId() .  '.' .\app::$config['dev']['serialization']);
	    }
	}
	return FALSE;
    }

    /**
     * Init config of the module
     */
    public function initConfig() {
	if (!isset($this->config) || !is_object($this->config)) {
	    if ($this->name == 'admin')
		$configPath = 'config.php';
	    else
		$configPath = PROFILE_PATH . $this->name . '/config.php';
	    $this->config = new \config($configPath);
	}
    }

    /**
     * Update rights for a role
     * @param string $role
     * @return integer $rights
     */
    public function updateRights($role, $rights) {
	$this->rights[$role] = $rights;
    }

    /**
     * Get an entity
     * @param string $role
     * @return integer
     */
    public function getRights($role) {
	if (isset($this->rights[(String) $role]))
	    return $this->rights[(String) $role];
    }

    /**
     * Generates the code to build a module
     * @static function
     * @param string $name module name
     * @param string $title module title
     */
    public static function build($name, $title) {
	$name = tools::sanitizeString($name);
	tools::createDirectory('modules/' . $name);
	tools::createDirectory('modules/' . $name . '/blocks/');
	tools::createDirectory(PROFILE_PATH . $name . '/model/');
	tools::createDirectory(PROFILE_PATH . $name . '/pages/');
	tools::createDirectory(PROFILE_PATH . $name . '/views/');
	$template = '<?php
            namespace ' . $name . ';
            class ' . $name . ' extends \module {
                protected $title = \''.str_replace('\'', '\\\'', $title).'\';
            }
            ?>';
	file_put_contents('modules/' . $name . '/module.php', $template);
	include('modules/' . $name . '/module.php');
	$name2 = $name . '\\' . $name;
	$mod = new $name2();
	$mod->name = $name;
	$mod->title = $title;
	$page = new \page('1');
        $page->setModule($name);
	$page->setTitle('Index ' . $name);
	$page->setRegex('@index@');
	$mod->addPage($page);
	$mod->save();
	if (PROFILE == 'www')
	    $config = new \config('config.php', TRUE);
	else
	    $config = new \config(PROFILE_PATH . 'config.php', TRUE);
	$config->add('$config[\'activeModules\'][\'' . $name . '\']', '0');
	return $config->save();
    }

    public function __sleep() {
	$props = get_object_vars($this);
	unset($props['model']);
	unset($props['title']);
	return array_keys($props);
    }

}

?>