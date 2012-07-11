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
 * Image Block Class 
 * Manages Image Block
 */

class image extends \block {
    
    protected $title = 'displays a configurable image in drag n drop';
    
    public function saveConfigs() {
        
	if (isset($_POST['imgPath'])) {
	    $this->setConfig('imgPath', $this->module . '/files/' . $_POST['imgPath']);
	    $this->setConfig('width', $_POST['width']);
	    $this->setConfig('height', $_POST['height']);
            $this->setConfig('title', $_POST['title']);
            $this->setConfig('alt', $_POST['alt']);
            $this->setConfig('url', $_POST['url']);
            if(isset($_POST['fancybox'])){
                $this->setConfig('fancybox', '1');
            }else{
                $this->setConfig('fancybox', '0');
            }
            
	}
    }
    public function init() {
            $this->setConfig('imgPath','core/files/Parsimony.png');
	    $this->setConfig('width', '200');
	    $this->setConfig('height', '200');
            $this->setConfig('title', 'parsimony');
            $this->setConfig('alt', 'Parsimony');
            $this->setConfig('fancybox', '0');

    }

}

?>
