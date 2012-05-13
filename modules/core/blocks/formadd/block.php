<?php

namespace core\blocks;

/**
 * Formadd Block Class 
 * Manages formadd Block
 */
class formadd extends \block {

    protected $category = 'query';
    
    public function init() {
        $this->setConfig('regenerateview', 1);
	$this->setConfig('success', 'Success');
	$this->setConfig('fail', 'Fail');
	if (isset($_POST['stop_typecont']) && $_POST['stop_typecont'] == 'page') {
            $pathOfView = MODULE . '/views/' . THEMETYPE;
        } else {
            $pathOfView = THEMEMODULE . '/views/' . THEMETYPE;
        }
	$this->setConfig('pathOfView', $pathOfView . '/' . $this->id . '.php');
    }
    
    public function saveConfigs() {
        if (isset($_POST['entity'])) {
            $cut = explode(' - ', $_POST['entity']);
            $this->setConfig('module', $cut[0]);
            $this->setConfig('entity', $cut[1]);
            $this->setConfig('regenerateview', $_POST['regenerateview']);
            $this->setConfig('success', $_POST['success']);
            $this->setConfig('fail', $_POST['fail']);
            
            \app::addListener('error', array($this, 'catchError'));

            $pathOfView = PROFILE_PATH .$this->getConfig('pathOfView');
            if ($this->getConfig('regenerateview') == 1) {
                \tools::file_put_contents($pathOfView, $this->generateView($this->getConfig('module'),$this->getConfig('entity')));
            } else {
                \tools::file_put_contents($pathOfView, $_POST['editor']);
            }

            $testIfHasError = exec('php -l ' . $pathOfView);
            if (!strstr($testIfHasError, 'No syntax errors detected')) {
                file_put_contents($pathOfView, $testIfHasError . PHP_EOL . '<?php __halt_compiler(); ?>' . $_POST['editor']);
            }
        }
    }
    
    public function generateView($module,$entity) {
        $entity = \app::getModule($module)->getEntity($entity);
        $html = '<form method="post" class="form" action="">
	<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />'.PHP_EOL;
        foreach($entity->getFields() AS $name => $field){
            $html .= "\t\t".'<?php echo $entity->'.$name.'->formAdd(); ?>'.PHP_EOL;
        }
        $html .= "\t".'<input type="submit" value="<?php echo t(\'Save\', FALSE); ?>" name="add">'.PHP_EOL;
        $html .= '</form>';
	return $html;
    }
    
    public function catchError($code, $file, $line, $message) {
        $mess = $message.' in '.$file.' in line '. $line ;
        \tools::file_put_contents( $this->getConfig('pathCode'), $mess .PHP_EOL . '<?php __halt_compiler(); ?>' . $_POST['editor']);
        $return = array('eval' => '$("#' . basename($file,'.php') . '",ParsimonyAdmin.currentBody).html("' . $mess . '");', 'notification' => $mess, 'notificationType' => 'negative');
        echo json_encode($return);
        exit;
    }

}

?>
