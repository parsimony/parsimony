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
 * @package Parsimony
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace admin;

/**
 * Admin Class 
 * Manage the administration of Parsimony
 */
class admin extends \module {
    
    protected $name = 'admin';

    /** @var string theme name */
    private $theme;

    /** @var string module name */
    private $module;

    /**
     * Controller post
     * @param string $action
     * @return false 
     */
    public function controllerPOST($action) {
	$justForCreators = array('addBlock', 'removeBlock', 'saveCSS', 'moveBlock', 'dbDesigner', 'addTheme', 'changeTheme', 'deleteTheme', 'addModule', 'saveRights', 'saveModel');
	if (BEHAVIOR == 0 || ( BEHAVIOR == 1  && in_array($action, $justForCreators)))
	    return \app::$response->setContent($this->returnResult(array('eval' => '', 'notification' => t('Permission denied', FALSE), 'notificationType' => 'negative')), 200);
	if (!empty($action)) {
	    $this->theme = \theme::get(THEMEMODULE, THEME, THEMETYPE);
	    $this->module = \app::getModule(MODULE);
	    if(isset($_POST['IDPage']) && is_numeric($_POST['IDPage'])) $this->page = $this->module->getPage($_POST['IDPage']);
	    return $this->controller($action);
	}
    }

    /**
     * Edit In Line
     * @param string $module
     * @param string $model
     * @param string $property
     * @param string $id
     * @param string $value
     * @return string 
     */
    protected function editInLineAction($module, $model, $property, $id, $value) {
	unset($_POST['action']);
	$obj = \app::getModule($module)->getEntity($model);
	$query = 'UPDATE ' . $module . '_' . $model . ' SET ' . $property . ' = \' ' . addslashes($value) . '\'';
	$query .= ' WHERE ' . $obj->getId()->name . '=' . $id . ';';
	$res = \PDOconnection::getDB()->exec($query);
	if ($res) {
	    $return = array('eval' => '', 'notification' => t('The data has been saved', FALSE), 'notificationType' => 'positive');
	} else {
	    $return = array('eval' => '', 'notification' => t('The data has not been saved', FALSE), 'notificationType' => 'negative');
	}
	return $this->returnResult($return);
    }

    /**
     * Check If Id Exists 
     * @param string $id
     * @return bool 
     */
    private function checkIfIdExists($id,$themetype = 'web') {
	if ($this->theme->search_block($id) != NULL)
	    return TRUE;
	
	foreach (\app::$activeModules as $module => $type) {
	    $moduleObj = \app::getModule($module);
	    foreach ($moduleObj->getPages() as $key => $page) {
		$block = $page->search_block($id);
		if ($block != NULL)
		    return TRUE;
	    }
	    /*if (is_file('modules/' . $module . '/views/web/' . $id . '.php'))
		return TRUE;echo 'coucou';exit;*/
            if (is_file(PROFILE_PATH . $module . '/views/'.$themetype.'/' . $id . '.php'))
		return TRUE;
	}
	return FALSE;
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
    protected function addBlockAction($popBlock, $parentBlock, $idBlock, $id_next_block, $stop_typecont, $content) {
        $tempBlock = new $popBlock($idBlock);
	if(!empty($content) &&  method_exists($tempBlock, 'setContent')) $tempBlock->setContent($content); /* external DND */
        $idBlock = $tempBlock->getId(); /* In case of sanitizing */
        if ($this->checkIfIdExists($idBlock,$stop_typecont)) {
            return $this->returnResult(array('eval' => '', 'notification' => t('ID block already exists, please choose antother', FALSE)));
        }
	$block = $this->$stop_typecont->search_block($parentBlock);
        $block->addBlock($tempBlock, $id_next_block);
	
	/* If exists : Add default block CSS in current theme  */
	if (is_file('modules/' . str_replace('\\', '/', $popBlock) . '/default.css')) {
	    $css = new \css('modules/' . str_replace('\\', '/', $popBlock) . '/default.css');
	    $cssCurrentTheme = new \css(PROFILE_PATH . THEMEMODULE . '/themes/' . THEME . '/' . THEMETYPE . '.css');
	    foreach ($css->getAllSselectors() as $selector) {
		$newSelector = '#'.$idBlock.' '.$selector;
		if (!$cssCurrentTheme->selectorExists($newSelector)) {
		    $cssCurrentTheme->addSelector($newSelector);
		}
		foreach ($css->extractSelectorRules($selector) as $property => $value) {
		    $value = str_replace('BASE_PATH',BASE_PATH,$value);
		    if (!$cssCurrentTheme->propertyExists($newSelector, $property)) {
                        $cssCurrentTheme->addProperty($newSelector, $property, $value);
                    } else {
                        $cssCurrentTheme->updateProperty($newSelector, $property, $value);
                    }
		}
	    }
	    $cssCurrentTheme->save();
	}
        $this->saveAll();
	
        if ($this->$stop_typecont->search_block($idBlock) != NULL) {
            \app::$request->page = new \page(999, 'core');
	    $return = array('eval' => $tempBlock->ajaxRefresh('add'), 'jsFiles' => json_encode(\app::$request->page->getJSFiles()), 'CSSFiles' => json_encode(\app::$request->page->getCSSFiles()), 'notification' => t('The Block is saved', FALSE), 'notificationType' => 'positive');
        }else
            $return = array('eval' => '', 'notification' => t('Error on drop', FALSE), 'notificationType' => 'negative');
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
    protected function saveBlockConfigsAction($typeProgress, $idBlock,$headerTitle, $maxAge, $tag, $allowedModules, $ajaxReload, $ajaxLoad, $cssClasses) {
	$block = $this->$typeProgress->search_block($idBlock);
        $block->setConfig('headerTitle', $headerTitle);
	$block->setConfig('maxAge', $maxAge);
	$block->setConfig('tag', $tag);
	$block->setConfig('allowedModules', $allowedModules);
	$block->setConfig('ajaxReload', $ajaxReload);
	\app::$request->page = new \page(999, 'core');
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
	if ($ajaxLoad != 0)
	    $block->setConfig('ajaxLoad', '1');
	else
	    $block->setConfig('ajaxLoad', '0');
	$block->setConfig('cssClasses', $cssClasses);
	if (method_exists($block, 'saveConfigs')) {
	    $block->saveConfigs();
	} else {
	    $rm = array('action', 'MODULE', 'THEME', 'THEMETYPE', 'THEMEMODULE', 'idBlock', 'parentBlock', 'typeProgress', 'maxAge', 'tag', 'ajaxReload', 'css_classes', 'allowedModules', 'save_configs');
	    $rm = array_flip($rm);
	    $configs = array_diff_key($_POST, $rm);
	    foreach ($configs AS $configName => $value) {
		$val = $_POST[$configName];
		$block->setConfig($configName, $val);
	    }
	}
	$this->saveAll();
	$return = array('eval' => $block->ajaxRefresh(),  'jsFiles' => json_encode(\app::$request->page->getJSFiles()), 'CSSFiles' => json_encode(\app::$request->page->getCSSFiles()), 'notification' => t('The Config has been saved', FALSE), 'notificationType' => 'positive');
	return $this->returnResult($return);
    }

    /**
     * Return results in json
     * @param string $results
     * @return string 
     */
    private function returnResult($results) {
	if (!isset($results['notificationType']))
	    $results['notificationType'] = 'normal';
	\app::$response->setHeader('X-XSS-Protection', '0');
	\app::$response->setHeader('Content-type', 'application/json');
	return json_encode($results);
    }

    /**
     * Remove block
     * @param string $typeProgress
     * @param string $parentBlock
     * @param string $idBlock
     * @return string
     */
    protected function removeBlockAction($typeProgress, $parentBlock, $idBlock) {
	$block = $this->$typeProgress->search_block($parentBlock);
	$block->rmBlock($idBlock);
	$test = $this->$typeProgress->search_block($idBlock);
	if ($test == NULL){
            $path = PROFILE_PATH . THEMEMODULE . '/themes/' . THEME . '/' . THEMETYPE . '.css';
            if(is_file($path)){
                $css = new \css($path);
                $css->deleteSelector('#' . $idBlock);
                $css->save();
            }
	    $this->saveAll();
	    $return = array('eval' => '$("#" + ParsimonyAdmin.inProgress,ParsimonyAdmin.currentBody).remove();$("#changeres").trigger("change");', 'notification' => t('The block has been deleted', FALSE), 'notificationType' => 'positive');
	}else
	    $return = array('eval' => '', 'notification' => t('Container block cannot be deleted', FALSE), 'notificationType' => 'negative');
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
    protected function savePageAction($module, $id_page, $title, $meta, $regex, array $URLcomponents = array()) {
	$moduleObj = \app::getModule($module);
	$page = $moduleObj->getPage($id_page, $module);
	$page->setModule($module);
	$page->setTitle($title);
	$page->setMetas($meta);
	if (isset($URLcomponents))
	    $page->setURLcomponents($URLcomponents);
	$page->setRegex('@^' . $regex . '$@');
	$moduleObj->updatePage($page); //modif
	if (\tools::serialize(PROFILE_PATH . $module . '/module', $moduleObj)) {
	    $return = array('eval' => 'ParsimonyAdmin.loadBlock(\'panelmodules\');', 'notification' => t('The page has been saved', FALSE), 'notificationType' => 'positive');
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
	if ($page == 'new') {
	    $lastPage = array_keys($moduleObj->getPages());
	    if(!empty($lastPage)) $idPage = max($lastPage) + 1;
            else $idPage = 1;          
	    $page = new \page($idPage, $module);
	    $page->setTitle('Page '.$idPage);
	    $page->setRegex('@^page_'.$idPage.'$@');
	    $page->save();
	    $moduleObj->addPage($page); //modif
	} else {
	    $page = $moduleObj->getPage($page);
	}
	$moduleObj->save();
        $module = $moduleObj;
	ob_start();
	include ('modules/admin/views/web/managePage.php');
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
	$return = array('eval' => 'window.location = "' . BASE_PATH . $url . 'index";', 'notification' => t('The page has been deleted', FALSE), 'notificationType' => 'positive');
	return $this->returnResult($return);
    }

    /**
     * Get the rules of a css selector and return them in json
     * @param string $filePath
     * @param string $selector
     * @return string 
     */
    protected function getCSSSelectorRulesAction($filePath, $selector) {
        if(is_file(PROFILE_PATH. $filePath)) $filePath2 =  PROFILE_PATH. $filePath;
        else $filePath2 =  'modules/'. $filePath;
        $css = new \css($filePath2);
        $selectorText = str_replace("\t", '', trim($css->selectorExists($selector)));
        if (!$selectorText)
            $selectorText = '';
        $CSSJson = array('selector' => $selector,'filePath' => $filePath, 'code' => $selectorText, 'values' => $css->extractSelectorRules($selector));
        return json_encode($CSSJson);
    }
    
    /**
     * Get the selectors of a css file and return them in json
     * @param string $term
     * @param string $filePath
     * @return string 
     */
    protected function getCSSSelectorsAction($filePath) {
        if(is_file(PROFILE_PATH. $filePath)) $filePath =  PROFILE_PATH. $filePath;
        else $filePath =  'modules/'. $filePath;
        $css = new \css($filePath);
        return json_encode($css->getAllSselectors());
    }
    
    /**
     * Get the rules of css selectors and return them in json
     * @param string $json
     * @return string 
     */
    protected function getCSSSelectorsRulesAction($json) {
        $selectors = json_decode($json);
        $res = array();
	$selectorText = '';
        foreach($selectors AS $selector){
            if(is_file(PROFILE_PATH.  $selector->url)) $filePath2 =  PROFILE_PATH. $selector->url;
            else $filePath2 =  'modules/'.  $selector->url;
            $css = new \css($filePath2);
            $selectorText =  preg_replace('@;[^a-zA-Z\-]+@m',';'.PHP_EOL, trim($css->selectorExists($selector->selector)));
            $res[] = array('selector' => $selector, 'filePath' => $selector->url, 'nbstyle' => $selector->nbstyle, 'nbrule' => $selector->nbrule, 'cssText' => $selectorText);
        }
        \app::$response->setHeader('X-XSS-Protection', '0');
	\app::$response->setHeader('Content-type', 'application/json');
        return json_encode($res);
    }

    /**
     * Save CSS
     * @param string $filePath
     * @param string $selector
     * @return string 
     */
    protected function saveCSSAction($filePath, $selector, $typeofinput = 'form', array $selectors = array()) {
        $css3 = array(
            'box-shadow' => array('-moz-box-shadow', '-webkit-box-shadow'),
            'border-radius' => array('-moz-border-radius', '-webkit-border-radius'),
            'border-image' => array('-moz-border-image', '-webkit-border-image'),
            'transform' => array('-webkit-transform', '-moz-transform', '-ms-transform', '-o-transform'),
            'transition' => array('-webkit-transition', '-moz-transition', '-ms-transition', '-o-transition'),
            'text-shadow' => array(),
            'background-size' => array('-moz-background-size', '-webkit-background-size'),
            'column-count' => array('-moz-column-count', '-webkit-column-count'),
            'column-gap' => array('-moz-column-gap', '-webkit-column-gap'),
            'background-clip' => array('-moz-background-clip', '-webkit-background-clip'),
            'background-origin' => array('-webkit-background-origin'),
            'transform-origin' => array('-ms-transform-origin', '-webkit-transform-origin', '-moz-transform-origin', '-o-transform-origin'),
            'transform-style' => array('-webkit-transform-style'),
            'perspective' => array('-webkit-perspective'),
            'perspective-origin' => array('-webkit-perspective-origin'),
            'backface-visibility' => array('-webkit-backface-visibility'),
            'transition-property' => array('-moz-transition-property', '-webkit-transition-property', '-o-transition-property'),
            'transition-duration' => array('-moz-transition-duration', '-webkit-transition-duration', '-o-transition-duration'));

        if ($typeofinput == 'form') {
            if (!is_file(PROFILE_PATH . $filePath) && is_file('modules/' . $filePath))
                \tools::file_put_contents(PROFILE_PATH . $filePath, file_get_contents('modules/' . $filePath));
            $filePath2 = PROFILE_PATH . $filePath;
            $css = new \css($filePath2);
            unset($_POST['current_selector_update']);
            unset($_POST['action']);
            unset($_POST['filePath']);
            unset($_POST['selector']);
            unset($_POST['typeofinput']);
            unset($_POST['save']);
            if (!$css->selectorExists($selector)) {
                unset($_POST['action']);
                $css->addSelector($selector);
            }
            unset($_POST['selectors']);
            $isCSS3 = FALSE;
            foreach ($_POST AS $key => $value) {
                $value = trim($value);
                if ($value != '') {
                    if (!$css->propertyExists($selector, $key)) {
                        if (isset($css3[$key])) {
                            $isCSS3 = TRUE;
                            foreach ($css3[$key] as $property) {
                                $css->addProperty($selector, $property, $value);
                            }
                        }
                        $css->addProperty($selector, $key, $value);
                    } else {
                        if (isset($css3[$key])) {
                            $isCSS3 = TRUE;
                            foreach ($css3[$key] as $property) {
                                $css->updateProperty($selector, $property, $value);
                            }
                        }
                        $css->updateProperty($selector, $key, $value);
                    }
                } else {
                    if (isset($css3[$key])) {
                        $isCSS3 = TRUE;
                        foreach ($css3[$key] as $property) {
                            $css->deleteProperty($selector, $property);
                        }
                    }
                    $css->deleteProperty($selector, $key);
                }
            }
            if (!$css->propertyExists($selector, 'behavior') && $isCSS3) {
                $css->addProperty($selector, 'behavior', 'url(/lib/csspie/PIE.htc)');
            }
            $css->save();
        } elseif ($typeofinput == 'code') {
            $csstab = array();
            foreach ($selectors AS $css) {
		$css['selector'] = urldecode($css['selector']);
                if (!isset($csstab[$css['file']])){
                     if (!is_file(PROFILE_PATH . $css['file']) && is_file('modules/' . $css['file']))
                        \tools::file_put_contents(PROFILE_PATH . $css['file'], file_get_contents('modules/' . $css['file']));
                    $filePath = PROFILE_PATH . $css['file'];
                    $csstab[$css['file']] = new \css($filePath);
                }
                $css['code'] = trim($css['code']);
                if(!empty($css['code'])){
                    if (!$csstab[$css['file']]->selectorExists($css['selector'])) {
                        $csstab[$css['file']]->addSelector($css['selector']);
                    }
                }
                $csstab[$css['file']]->replaceSelector($css['selector'], $css['code']);
            }
            foreach ($csstab AS $css) {
                $css->save();
            }
        }
        $return = array('eval' => '', 'notification' => t('The style sheet has been saved', FALSE), 'notificationType' => 'positive');
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
    protected function moveBlockAction($start_typecont, $idBlock, $popBlock, $startParentBlock, $id_next_block, $stop_typecont, $parentBlock) {
	//start
	if (empty($start_typecont)) {
	    $temp = substr($popBlock, 0, -10);
	    $newblock = new $temp($idBlock);
	} else {
	    $block = $this->$start_typecont->search_block($idBlock);  	
            $blockparent = $this->$start_typecont->search_block($startParentBlock);
	    $blockparent->rmBlock($idBlock);
	    $this->saveAll();
	    $newblock = $block;
	}
	//stop
	if ($id_next_block === '' || $id_next_block === 'undefined')
	    $id_next_block = FALSE;
	$block2 = $this->$stop_typecont->search_block($parentBlock);
	$block2->addBlock($newblock, $id_next_block);
	$this->saveAll();
	if ($this->$stop_typecont->search_block($idBlock) != NULL)
	    $return = array('eval' => 'ParsimonyAdmin.moveMyBlock("' . $idBlock . '","dropInPage");', 'notification' => t('The move has been saved', FALSE), 'notificationType' => 'positive');
	else
	    $return = array('eval' => '', 'notification' => t('Error on drop', FALSE), 'notificationType' => 'negative');
	return $this->returnResult($return);
    }

    /**
     * Get the view of the configuration block
     * @param string $typeProgress
     * @param string $idBlock
     * @param string $parentBlock
     * @return string|false
     */
    protected function getViewConfigBlockAction($typeProgress, $idBlock, $parentBlock) {
	$block = $this->$typeProgress->search_block($idBlock);
	ob_start();
	require('modules/admin/views/web/manageBlock.php');
	return ob_get_clean();
    }

    /**
     * Get the view of the theme form
     * @return string|false 
     */
    protected function getViewConfigThemesAction() {
	return $this->getView('manageThemes','web');
    }

    /**
     * Get the view of the translation form
     * @return string|false 
     */
    protected function getViewTranslationAction() {
	return $this->getView('manageTranslation','web');
    }

    /**
     * Get the adding view of the module
     * @return string|false 
     */
    protected function getViewAddModuleAction() {
	return $this->getView('addModule','web');
    }

    /**
     * Get the adding view of the block
     * @return string|false 
     */
    protected function getViewAddBlockAction() {
	return $this->getView('addBlock','web');
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
	$return = array('eval' => '$(\'span[data-key="' . $key . '"]\',ParsimonyAdmin.currentBody).html("' . $val . '")', 'notification' => t('The translation has been saved', FALSE), 'notificationType' => 'positive');
	return $this->returnResult($return);
    }

    /**
     * Cross the nodes from a given node
     * @param string $node
     * @return string 
     */
    private function domToArray($node) {
	if ($node->children()) {
	    $tts = array();
	    foreach ($node->children() as $kid) {
                if($kid->tag == 'center') {$kid->id = uniqid();$kid->tag = 'div';$kid->class = 'align_center';}
                $allowTags = array('div','header','footer','section','article','aside','hgroup','nav');
		if (in_array($kid->tag, $allowTags)/* && !empty($kid->id)*/) {
                    if(empty($kid->id)){
                        $kid->id = uniqid();
                    }
		    $tts[$kid->id]['content'] = $kid->id;
                    $tts[$kid->id]['tag'] = $kid->tag;
                    if(isset($kid->class)) $tts[$kid->id]['class'] = $kid->class;
		    if ($kid->children()) {
                        $mark = true;
                        foreach ($kid->children() as $sskid){ //verif if children be part of structure
                            if(!in_array($sskid->tag, $allowTags) && empty($sskid->id)) {
                                $mark = false;
                                //echo $sskid->tag ;exit;
                                break;
                            }
                        } 
			$res = $this->domToArray($kid);
                        $tts[$kid->id] = array('content' => '');
			if (empty($res) || !$mark)
			    $tts[$kid->id]['content'] = $kid->innertext;
			else
			    $tts[$kid->id]['content'] = $res;
                    }
		}else  {echo $kid->tag.'tdddt'.$kid->id;}
	    }
	    return $tts;
	}
    }

    /**
     * Dump an array into a container theme
     * @param string $node
     * @param string $id by default container
     * @return string 
     */
    private function arrayToBlocks($node, $id = 'container') {
	if (is_array($node)) {
            if($id=='content') $block = new \core\blocks\page($id);
            else $block = new \core\blocks\container($id);
            if(isset($node['tag'])) $block->setConfig('tag',$node['tag']);
	    foreach ($node as $id => $ssnode) {
		$b = $this->arrayToBlocks($ssnode['content'], $id);
		$block->addBlock($b);
	    }
	} else {
	    $block = new \core\blocks\wysiwyg($id);
	    $block->setContent(utf8_encode($node));
	    if(isset($node['class'])) $block->setConfig('css_classes',$node['class']);
	}
	return $block;
    }
    
    /**
     * Get view of dbDesigner
     * @return string 
     */
    protected function dbDesignerAction() {
	return $this->getView('dbDesigner','web');
    }
    
    /**
     * Get view of the file explorer
     * @return string 
     */
    protected function explorerAction() {
	return $this->getView('explorer','web');
    }
    
    /**
     * Get view of the files for explorer
     * @return string 
     */
    protected function filesAction($dirPath) {
	return $this->getView('files','web');
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
            set_time_limit(0);
	    \tools::createDirectory(PROFILE_PATH . $thememodule . '/themes/' . $name, 0777);
	    if ($patterntype == 'url' && !empty($url)) {
		include('lib/simplehtmldom/simple_html_dom.php');
		$str = file_get_contents($url);
		substr($url, -1) == '/' ? $baseurl = dirname($url . 'index') : $baseurl = dirname($url);
		$str = \tools::absolute_url($str, $baseurl);
                $str = preg_replace('#<!--(.*?)-->#is', '', $str);
                $str = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $str);
                $str = preg_replace('#<noscript(.*?)>(.*?)</noscript>#is', '', $str);
                $str = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $str);
		$html = str_get_html($str);
		preg_match_all('/<.*href="(.*\.css).*[^"]/i', $str, $out);
		$allCSS = '';
		foreach ($out[1] as $css) {
		    $code = file_get_contents($css);
                    $base = dirname($css).'/';
                    $host = 'http://'.parse_url($css, PHP_URL_HOST).'/';
                    $code = preg_replace('@url\s+\(\s+@', 'url(', $code);
                    $code = preg_replace('@url\((["\'])?/@', 'url(\1'.$host, $code);
                    $code = preg_replace('@url\((["\'])?@', 'url(\1'.$base, $code);
                    $code = str_replace($base.'http://', 'http://', $code);
                    $code = str_replace($base.'http://', 'http://', $code);
                    $allCSS .= $code;
		}
		file_put_contents(PROFILE_PATH . $thememodule . '/themes/' . $name . '/web.css', utf8_encode($allCSS));
		$body = $html->find('body');
		$tree = $this->domToArray($body[0]);
		$structure1 = $this->arrayToBlocks(array('dvdxc'=> array('content' => $tree)));
		$theme = new \theme('container', $name, 'web', $thememodule);
                $conts = $structure1->getBlocks();
                $cont = reset($conts);
		$theme->setBlocks($cont->getBlocks());
		$theme->save();
	    } else if ($patterntype == 'template' && !empty($template)) {
                list($oldModule, $oldName) = explode(';',$template);
                $theme = array();
                foreach (\app::$devices AS $device) {
                    $theme[$device['name']] = \theme::get($oldModule, $oldName, $device['name']);
                    $theme[$device['name']]->setModule($thememodule);
                    $theme[$device['name']]->setName($name);
                }
		\tools::copy_dir(PROFILE_PATH . $thememodule . '/themes/' . $oldName . '/', PROFILE_PATH . $thememodule . '/themes/' . $name . '/');
                foreach (\app::$devices AS $device) {
                    $theme[$device['name']]->save();
                }
	    } else {
		$theme = new \theme('container', $name, 'web', $thememodule);
		$theme->save();
	    }
	    /* Set theme in preview mode */
	    setcookie('THEMEMODULE', $thememodule, time()+60*60*24*30, '/');
	    setcookie('THEME', $name, time()+60*60*24*30, '/');
	    $return = array('eval' => 'top.window.location.reload()', 'notification' => t('The Theme has been created', FALSE), 'notificationType' => 'positive');
	} else {
	    $return = array('eval' => '', 'notification' => t('The Theme has not been created, theme already exists', FALSE), 'notificationType' => 'negative');
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
        $path = stream_resolve_include_path($THEMEMODULE . '/themes/' . $name . '/web.' .\app::$config['dev']['serialization']);
	if ($path) {
            $configObj = new \core\classes\config('config.php', TRUE);
            $update = array('THEMEMODULE' => $THEMEMODULE,'THEME' => $name);
            $configObj->saveConfig($update);
            setcookie('THEMEMODULE', $THEMEMODULE, time()+60*60*24*30, '/');
	    setcookie('THEME', $name, time()+60*60*24*30, '/');
	    $return = array('eval' => 'document.getElementById("parsiframe").contentWindow.location.reload()', 'notification' => t('The Theme has been changed', FALSE), 'notificationType' => 'positive');
	} else {
	    $return = array('eval' => '', 'notification' => t('The Theme has\'nt been changed', FALSE), 'notificationType' => 'negative');
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
	$return = array('eval' => "$('#theme_".$name."').remove()", 'notification' => t('The Theme has been deleted', FALSE), 'notificationType' => 'positive');
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
	    $return = array('eval' => 'top.window.location.href = "' . BASE_PATH . $name_module . '/index"', 'notification' => t('The Module has been created', FALSE), 'notificationType' => 'positive');
	} else {
	    $return = array('eval' => '', 'notification' => t('Module already exists, please choose another name', FALSE), 'notificationType' => 'negative');
	}
	return $this->returnResult($return);
    }

    /**
     * Get the the view of user profile
     * @return string 
     */
    protected function getViewUserProfileAction() {
	return $this->getView('userProfile','web');
    }

    /**
     * Change Locale language
     * @param string $locale
     */
    protected function changeLocaleAction($locale) {
	$config = new \config('config.php', TRUE);
	$config->saveConfig(array('localization' => array('default_language' => $locale)));
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
	\unlink('cache/' . \app::$request->getLocale() . '-lang.php');
	$configObj = new \config($file, TRUE);
	$configObj->saveConfig($config);
	$return = array('eval' => 'ParsimonyAdmin.loadBlock(\'panelmodules\');', 'notification' => t('The Config has been saved', FALSE), 'notificationType' => 'positive');
	return $this->returnResult($return);
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
	$sql = '';
	foreach ($obj->getFields() as $field) {
            if(get_class($field) != \app::$aliasClasses['field_formasso'])
	    $sql .= ' ' . $field->name . ' like \'%' . addslashes($search) . '%\' OR';
	}
	$obj = $obj->where(substr($sql, 0, -3))->limit($limit);
	$modifModel = TRUE; /* To enable edit link */
	ob_start();
	require('modules/admin/views/web/datagrid.php');
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
	$obj = \app::getModule($module)->getEntity($entity)->limit((($page - 1) * $limit) . ','.$limit);
	$modifModel = TRUE; /* To enable edit link */
	ob_start();
	require('modules/admin/views/web/datagrid.php');
	return ob_get_clean();
    }
    
    public function structureTree($obj) {
        $idPage = '';
        if($obj->getId() == 'content') $idPage = ' data-page="'.\app::$request->page->getId().'"';
	$html = '<ul class="tree_selector container parsicontainer" style="clear:both" id="treedom_' . $obj->getId() . '"'.$idPage.'><span class="arrow_tree"></span>' . $obj->getId();
	if ($obj->getId() == 'content'){
	    $obj = \app::$request->page;
	}
	foreach ($obj->getBlocks() AS $block) {
	    if (get_class($block) == 'core\blocks\container' || $block->getId() == 'content')
		$html .= $this->structureTree($block);
	    else
		$html .= '<li class="tree_selector parsiblock" id="treedom_' . $block->getId() . '"> ' .$block->getId() . '</li>';
	};
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
	$maview = new \view();
	if (!empty($properties)) {
	    if (isset($relations))
		$maview = $maview->initFromArray($properties, $relations);
	    else
		$maview = $maview->initFromArray($properties);
	    if ($pagination)
		$maview->limit($nbitem);
	    else 
		$maview->limit(10);
	} else {
	    return t('No data for this query.', FALSE);
	}
        $maview->buildQuery();
	$obj = $maview;
	ob_start();
        $sql = $obj->getSQL();
        echo '<div id="generatedsql">'.$sql['query'].'</div>';
	require('modules/admin/views/web/datagrid.php');
	return ob_get_clean();
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
	return $obj->where($obj->getId()->name. '=' . $id)->getViewUpdateForm(TRUE);
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
	require('modules/admin/views/web/manageModel.php');
	return ob_get_clean();
    }

    /**
     * Add a new entry in a table
     * @param string $entity
     * @return string 
     */
    protected function addNewEntryAction($entity) {
	unset($_POST['action']);
	unset($_POST['add']);
	unset($_POST['TOKEN']);
	list($module, $entity) = explode(' - ', $entity);
	$obj = \app::getModule($module)->getEntity($entity);
	unset($_POST['entity']);
	$res = $obj->insertInto($_POST);
	if(is_numeric($res) || $res == 1){
	    $return = array('eval' => '$(\'a[rel="' . $module . ' - ' . $entity . '"]\').trigger("click");document.getElementById("parsiframe").contentWindow.location.reload()', 'notification' => t('The data have been added', FALSE), 'notificationType' => 'positive');
	}elseif($res === FALSE){
	    $return = array('eval' => '', 'notification' => t('The data haven\'t been added', FALSE), 'notificationType' => 'negative');
	}else{
	    $return = array('eval' => '', 'notification' => t('The data haven\'t been added', FALSE).' : '.$res, 'notificationType' => 'negative');
	}
	return $this->returnResult($return);
    }

    /**
     * Update an entry in a table
     * @param string $entity
     * @return string 
     */
    protected function updateEntryAction($entity) {
	unset($_POST['action']);
	unset($_POST['TOKEN']);
	list($module, $entity) = explode(' - ', $entity);
	$obj = \app::getModule($module)->getEntity($entity);
	unset($_POST['entity']);
	if (isset($_POST['update'])) {
	    unset($_POST['update']);
	    $res = $obj->update($_POST);
	} elseif (isset($_POST['delete'])) {
	    unset($_POST['delete']);
	    $res = $obj->where($obj->getId()->name.' = '.$_POST[$obj->getId()->name])->delete();
	}
	if(is_numeric($res) || $res == 1){
	    $return = array('eval' => '$(\'a[rel="' . $module . ' - ' . $entity . '"]\').trigger("click");document.getElementById("parsiframe").contentWindow.location.reload()', 'notification' => t('The data have been modified', FALSE), 'notificationType' => 'positive');
	}elseif($res === FALSE){
	    $return = array('eval' => '', 'notification' => t('The data haven\'t been modified', FALSE), 'notificationType' => 'negative');
	}else{
	    $return = array('eval' => '', 'notification' => t('The data haven\'t been modified', FALSE).' : '.$res, 'notificationType' => 'negative');
	}
	return $this->returnResult($return);
    }

    /**
     * Get the view of rights
     * @return string|false
     */
    protected function getViewAdminRightsAction() {
	return $this->getView('manageRights','web');
    }
    
    /**
     * Get the view in order to change current language
     * @return string|false
     */
    protected function getViewAdminLanguageAction() {
	return $this->getView('manageLanguage','web');
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
    protected function uploadAction($path, $size = 999999, $allowedExt = 'jpg|png|gif') {
	$upload = new \core\classes\upload($size, $allowedExt, $path . '/');
	$result = $upload->upload($_FILES['fileField']);
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
     * @param string $modulerights
     * @param string $pagesrights
     * @return string 
     */
    protected function saveRightsAction($type, $modelsrights, $modulerights, $pagesrights) {
        if (is_array($type)) {
	    foreach ($type as $numRole => $role) {
		\app::getModule('core')->getEntity('role')->where('id_role = '.$numRole)->update(array('id_role' => $numRole, 'state' => $role ));
	    }
	}
	if (is_array($modulerights)) {
	    foreach ($modulerights as $numRole => $role) {
		foreach ($role as $moduleName => $value) {
		    $module = \app::getModule($moduleName);
		    if ($value == 'on')
			$module->updateRights($numRole, 1);
		    else
			$module->updateRights($numRole, 0);
		    $module->save();
		}
	    }
	}
	if (is_array($modelsrights)) {
	    foreach ($modelsrights as $numRole => $role) {
		foreach ($role as $moduleName => $modules) {
		    foreach ($modules as $entityName => $entities) {
			$nb = 0;
			$model = \app::getModule($moduleName)->getEntity($entityName);
			foreach ($entities as $right => $value) {
			    if ($right == 'display' && $value == 'on')
				$nb += 1;
			    if ($right == 'insert' && $value == 'on')
				$nb += 2;
			    if ($right == 'update' && $value == 'on')
				$nb += 4;
			    if ($right == 'delete' && $value == 'on')
				$nb += 8;
			}
			$model->updateRights($numRole, $nb);
			$model->save();
		    }
		}
	    }
	}
	if (is_array($pagesrights)) {
	    foreach ($pagesrights as $numRole => $role) {
		foreach ($role as $moduleName => $modules) {
		    foreach ($modules as $pageId => $pages) {
			$nb = 0;
			$mod = \app::getModule($moduleName);
			$page = $mod->getPage($pageId);
			foreach ($pages as $right => $value) {
			    if ($right == 'display' && $value == 'on')
				$nb += 1;
			}
			$page->updateRights($numRole, $nb);
			$page->save();
		    }
		}
	    }
	}
	$return = array('eval' => '', 'notification' => t('The Permissions have been saved', FALSE), 'notificationType' => 'positive');
	return $this->returnResult($return);
    }
    
    /**
     * Save model
     * @return string
     */
    protected function saveModelAction($module,$list) {
	$schema = json_decode($list);
	$tableExists = array();
	if (is_array($schema)) {
	    foreach ($schema as $table) {
		
		if ($table->name != $table->oldName) include_once('modules/' . $module . '/model/' . $table->oldName . '.php');

		$tplProp = '';
		$tplParam = '';
		$tplAssign = '';
		$args = array();
		$matchOldNewNames = array();

		foreach ($table->properties as $fieldName => $property) {
		    list($name, $type) = explode(':', $fieldName);
		    $tplProp .= '    protected $' . $name . ";\n\r"; //genere les atributs
		    $tplParam .= '\\' . $type . ' $' . $name . ','; //génère les paramètres du constructeur
		    $tplAssign .= '        $this->' . $name . ' = $' . $name . ";\n"; //génère les affectations dans le constructeur
		    $reflectionObj = new \ReflectionClass($type);
		    $property = json_encode($property);
		    $property = json_decode($property, true);
                    
		    $args[] = $reflectionObj->newInstanceArgs($property);
		    if(isset($property['oldName']) && ($property['oldName'] != $name && !empty($property['oldName']))) $matchOldNewNames[$name] = $property['oldName'];
		}
		$tpl = 
'<?php
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
' . $tplAssign . '
}
// DON\'T TOUCH THE CODE ABOVE ##########################################################
';

		$model = 'modules/' . $module . '/model/' . $table->name . '.php';
		if (!is_file($model)) {
		    $tpl .= '}'.PHP_EOL.'?>';
		} else {
		    $code = file_get_contents($model);
		    $tpl = preg_replace('@<\?php(.*)}(.*)?(ABOVE ##########################################################)?@Usi', $tpl, $code);
		}

		\tools::file_put_contents($model, $tpl);
		include_once($model);
		$oldFields = array();
		$oldObjModel = FALSE;
		if (is_file('modules/' . $module . '/model/' . $table->oldName . '.'.\app::$config['dev']['serialization'])) {
		    $oldObjModel = \tools::unserialize('modules/' . $module . '/model/' . $table->oldName);
		    $oldFields = $oldObjModel->getFields();
		}
		
		// Change table Name if has change
		if ($table->name != $table->oldName) {
		    \PDOconnection::getDB()->exec('ALTER TABLE ' . $module . '_' . $table->oldName . ' RENAME TO ' . $module . '_' . $table->name . ';');
		    unlink('modules/' . $module . '/model/' . $table->oldName . '.php');
		    unlink('modules/' . $module . '/model/' . $table->oldName .  '.' .\app::$config['dev']['serialization']);
		    //require_once('modules/' . $module . '/model/' . $table->name . '.php');
		}
		// make a reflection object
		$reflectionObj = new \ReflectionClass($module . '\\model\\' . $table->name);
		$newObj = $reflectionObj->newInstanceArgs($args);
		$newObj = unserialize(serialize($newObj)); // in order to call __wakeup method
                $newObj->setTitle($table->title);
		$newObj->behaviorTitle = $table->behaviorTitle;
		$newObj->behaviorDescription = $table->behaviorDescription;
		$newObj->behaviorKeywords = $table->behaviorKeywords;
		$newObj->behaviorImage = $table->behaviorImage;
		if ($oldObjModel != FALSE) {
		    $nameFieldBefore = '';
		    foreach ($args as $fieldName => $field) {
			if (isset($oldFields[$field->name])) {
			    $field->alterColumn($nameFieldBefore);
			} elseif (isset($matchOldNewNames[$field->name])) {
			    $field->alterColumn($nameFieldBefore,$matchOldNewNames[$field->name]);
			} else {
			    $field->addColumn($nameFieldBefore);
			}
			if(get_class($field) != \app::$aliasClasses['field_formasso']) $nameFieldBefore = $field->name;
		    }
		    foreach ($oldObjModel->getFields() as $fieldName => $field) {
			if (is_object($field) && (!property_exists($newObj, $fieldName) && !in_array($fieldName, $matchOldNewNames) ))
			    $field->deleteColumn();
		    }
		}else {
		    $newObj->createTable();
		}
		\tools::serialize('modules/' . $module . '/model/' . $table->name , $newObj);
		$tableExists[] = $table->name;
	    }
	}
	foreach (glob('modules/' . $module . '/model/*.php') as $filename) {
	    $modelName = substr(substr(strrchr($filename, "/"), 1), 0, -4);
	    if (!in_array($modelName, $tableExists)) {
		\app::getModule($module)->getEntity($modelName)->deleteTable();
	    }
	}
	return ' ';
    }

    /**
     * Save module page in putting data in module.obj
     */
    private function saveAll() {
	$this->theme->save();
	if (isset($this->page) && is_object($this->page) ) {
	    \tools::serialize(PROFILE_PATH . MODULE . '/pages/' . $this->page->getId(), $this->page);
	}
	\tools::serialize(PROFILE_PATH . MODULE . '/module', $this->module);
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
     * Get a back Up
     * @param string $replace
     * @param string $file
     * @return string 
     */
    protected function getBackUpAction($replace, $file) {
	$old = file_get_contents('profiles/'.PROFILE.'/backup/' . $file . '-' . $replace . '.bak');
        $old = preg_replace('#.*<\?php __halt_compiler\(\); \?>#Usi', '', $old);
	file_put_contents($file, $old);
	return $old;
    }

    /**
     * Build a new block in a given module
     * @param string $choosenmodule
     * @param string $name_block
     * @return string 
     */
    protected function buildNewBlockAction($choosenmodule, $name_block) {
	if (isset($name_block) && isset($choosenmodule) && !empty($choosenmodule)) {
	    if (!empty($name_block)) {
		if (!file_exists('modules/' . $choosenmodule . '/blocks/' . $name_block)) {
		    \block::build($choosenmodule, $name_block);
		} else {
		    $return = array('eval' => '', 'notification' => t('The Block name already exists', FALSE), 'notificationType' => 'negative');
		}
	    } else {
		$return = array('eval' => '', 'notification' => t('The Block name is required', FALSE), 'notificationType' => 'negative');
	    }
	    //$message = t('The Block name is required', FALSE);
	    $return = array('eval' => 'ParsimonyAdmin.displayConfBox("' . BASE_PATH . 'admin/action","Block","choosenmodule=' . $choosenmodule . '&name_block=' . $name_block . '&action=displayModifyBlock");', 'notification' => 'The Block name is saved', 'notificationType' => 'positive');
	}
	return $this->returnResult($return);
    }

    /**
     * Wrap result of an action in an instance of Page in order to display it in a popup
     * @return string 
     */
    protected function actionAction() {
	if (isset($_POST['action'])) {
	    $content = $this->controllerPOST($_POST['action']);
	    if (isset($_POST['popup']) && $_POST['popup'] == 'yes') {
		ob_start();
		require('modules/admin/views/web/popup.php');
		return ob_get_clean();
	    } else {
		return $content;
	    }
	}
    }

}

?>