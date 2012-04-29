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
 * Googlemap Block Class 
 * Manages Googlemap Block
 */

class googlemap extends \block {

    public function saveConfigs() {
	    $this->setConfig('adress', $_POST['adress']);
            $this->setConfig('town', $_POST['town']);
            $this->setConfig('country', $_POST['country']);
            $this->setConfig('language', $_POST['language']);
            $this->setConfig('view', $_POST['view']);
            $this->setConfig('zoom', $_POST['zoom']);  
            
            
    }
     public function init() {
            $this->setConfig('adress', '19 chemin de la loge');
            $this->setConfig('town', 'Toulouse');
            $this->setConfig('country', 'France');
            $this->setConfig('language', 'fr');
            $this->setConfig('view', 'm');
            $this->setConfig('zoom', '15');  
    }



}

?>
