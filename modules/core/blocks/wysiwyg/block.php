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
 * @authors Julien Gras et Benoît Lorillot
 * @copyright  Julien Gras et Benoît Lorillot
 * @version  Release: 1.0
 * @category  Parsimony
 * @package core/blocks
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace core\blocks;

/**
 * Wysiwyg Block Class 
 * Manages Wysiwyg Block
 */

class wysiwyg extends \block {

    protected $title = 'displays a rich text editor';
    
    public function init() {
        if (isset($_POST['typeProgress']) && $_POST['typeProgress'] == 'Theme')
            $path =  THEMEMODULE . '/views/' . THEMETYPE . '/' . $this->id . '.php';
        else
            $path =  MODULE . '/views/' . THEMETYPE . '/' . $this->id . '.php';
        if (!is_file($path))
            \tools::file_put_contents(PROFILE_PATH .$path, '<h1>' .t('Put your content in this area',false).'</h1>');
        $this->setConfig('path', $path);
    }
    
        
    public function getView() {
	ob_start();
	include($this->getConfig('path'));
	return ob_get_clean();
    }

    public function saveConfigs() {
        \tools::file_put_contents( PROFILE_PATH .$this->getConfig('path'), $_POST['editor']);
    }
    
    public function setContent($html) {
        if (\app::getClass('user')->VerifyConnexion() && BEHAVIOR == 2) {
            return \tools::file_put_contents(PROFILE_PATH.$this->getConfig('path'),\tools::sanitize($html));
        }
	return FALSE;
    }
    
    public function saveWYSIWYGAction($html) {
        if(substr($html,-29) == '<div class="clearboth"></div>') $html = substr($html,0,-29);
        if($this->setContent($html)){
	    $return = array('eval' => '', 'notification' => t('The data have been saved', FALSE), 'notificationType' => 'positive');
	}else{
	    $return = array('eval' => '', 'notification' => t('The data has not been saved', FALSE), 'notificationType' => 'negative');
	}
	\app::$response->setHeader('X-XSS-Protection', '0');
	\app::$response->setHeader('Content-type', 'application/json');
	return json_encode($return);
    }

}

?>
