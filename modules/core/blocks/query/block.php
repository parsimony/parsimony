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
 * Query Block Class 
 * Manages Query Block
 */

class query extends \block {

    protected $category = 'query';
    protected $pathOfViewFile;

    public function init() {
        $this->setConfig('regenerateview', 1);
	if (isset($_POST['stop_typecont']) && $_POST['stop_typecont'] == 'page') {
            $pathOfView = MODULE . '/views/' . THEMETYPE;
        } else {
            $pathOfView = THEMEMODULE . '/views/' . THEMETYPE;
        }
	$this->setConfig('pathOfViewFile', $pathOfView . '/' . $this->id . '.php');
    }

    public function saveConfigs() {
        
        $pathOfViewFile = PROFILE_PATH .$this->getConfig('pathOfViewFile');
	
        $this->setConfig('selected', $_POST['properties']);
        $this->setConfig('pagination', $_POST['pagination']);
        $this->setConfig('regenerateview', $_POST['regenerateview']);
        $this->setConfig('nbitem', $_POST['nbitem']);
        
        \app::addListener('error', array($this, 'catchError'));

        if ($this->getConfig('regenerateview') == 1) {
	    \tools::file_put_contents($pathOfViewFile, $this->generateView($_POST['properties']));
        } else {
            \tools::file_put_contents($pathOfViewFile, $_POST['editor']);
        }
        
        $testIfHasError = exec('php -l '.$pathOfViewFile);
        if (!empty($testIfHasError) && !strstr($testIfHasError, 'No syntax errors detected')){
            file_put_contents($pathOfViewFile, $testIfHasError .PHP_EOL . '<?php __halt_compiler(); ?>' . $_POST['editor']);
        }
        
        $myView = new \view();
        if (isset($_POST['relations']))
            $myView = $myView->initFromArray($_POST['properties'], $_POST['relations']);
        else
            $myView = $myView->initFromArray($_POST['properties']);
        if ($this->getConfig('pagination'))
            $myView->limit($this->getConfig('nbitem'));
        $myView->buildQuery();
        $this->setConfig('view', $myView);
    }

    public function generateView(array $tab_selected) {
        $view_code = '';
        $view_code .= '<?php foreach ($view as $key => $line) : ?>' . "\n";
        $view_code .= "\t" . '<div class="clearboth">' . "\n";
	$myView = new \view();
        if (!empty($tab_selected)) {
	    $myView = $myView->initFromArray($tab_selected);
            foreach ($myView->getFields() AS $sqlName => $field) {
		if(substr($sqlName,0,3) != 'id_') $displayLine = '->display($line)';
		else  $displayLine = '';
                $view_code .= "\t\t" . '<div class="' . $sqlName . '"><?php echo $line->' . $sqlName . $displayLine . '; ?></div>' . "\n";
            }
        } else {
            $view_code .= "\t\t<?php //You have to create your query before ?>\n";
        }
        $view_code .= "\t" . '</div>' . "\n";
        $view_code .= '<?php endforeach; ?>';
	return $view_code;
    }
    
    public function catchError($code, $file, $line, $message) {
        $mess = $message.' in '.$file.' in line '. $line ;
        \tools::file_put_contents( PROFILE_PATH .$this->getConfig('pathOfViewFile'), $mess .PHP_EOL . '<?php __halt_compiler(); ?>' . $_POST['editor']);
        $return = array('eval' => '$("#' . basename($file,'.php') . '",ParsimonyAdmin.currentBody).html("' . $mess . '");', 'notification' => $mess, 'notificationType' => 'negative');
        echo json_encode($return);
        exit;
    }

}

?>
