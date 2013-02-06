<?php

namespace core\blocks;

/**
 * @title Form
 * @description displays in one click the add/update form of db model
 * @version 1
 * @browsers all
 * @php_version_min 5.3
 * @block_category database
 * @modules_dependencies core:1
 */

class form extends \block {
    
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
            list($module, $entity) = explode(' - ', $_POST['entity']);
            $this->setConfig('module', $module);
            $this->setConfig('entity', $entity);
            $this->setConfig('regenerateview', $_POST['regenerateview']);
            $this->setConfig('success', $_POST['success']);
            $this->setConfig('fail', $_POST['fail']);
            
            $pathOfView = PROFILE_PATH .$this->getConfig('pathOfView');
            
            /* Test for errors in view and save */
            \app::addListener('error', array($this, 'catchError'));
            /* Test if new file contains errors */
            $testIfHasError = \tools::testSyntaxError($_POST['editor'],array('entity' => \app::getModule($this->getConfig('module'))->getEntity($this->getConfig('entity'))));
            /* If new file contains errors */
            if (!$testIfHasError){
                /* If there's no errors, Save new file */
                if ($this->getConfig('regenerateview') == 1) {
                    \tools::file_put_contents($pathOfView, $this->generateViewAction($this->getConfig('module'),$this->getConfig('entity')));
                } else {
                    \tools::file_put_contents($pathOfView, $_POST['editor']);
                }
            }
        }
    }
    
    public function generateViewAction($module,$entity) {
        $entity = \app::getModule($module)->getEntity($entity);
        $html = '<?php
if(isset($_POST[\'add\'])){
	$res = $entity->insertInto($_POST);
	if($res === TRUE || is_numeric($res)){
		echo \'<div class="notify positive">\'.t($this->getConfig(\'success\')).\'</div>\';
	}else{
		echo \'<div class="notify negative">\'.t($this->getConfig(\'fail\')).\'</div>\';
	}
}
?>
<form method="post" class="form" action="">
	<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />'.PHP_EOL;
        foreach($entity->getFields() AS $name => $field){
            $html .= "\t\t".'<?php echo $entity->'.$name.'->form(); ?>'.PHP_EOL;
        }
        $html .= "\t".'<input type="submit" value="<?php echo t(\'Save\', FALSE); ?>" name="add" class="submit">'.PHP_EOL;
        $html .= '</form>';
	return $html;
    }
    
    public function catchError($code, $file, $line, $message) {
        $mess = $message.' '.t('in line').' '. $line ;
        if($code == 0 || $code == 2 || $code == 8 || $code == 256 || $code == 512 || $code == 1024 || $code == 2048 || $code == 4096 || $code == 8192 || $code == 16384){
            /* If it's a low level error, we save but we notice the dev */
            if ($this->getConfig('regenerateview') == 1) {
		list($module, $entity) = explode(' - ', $_POST['entity']);
                \tools::file_put_contents(PROFILE_PATH . $this->getConfig('pathOfViewFile'), $this->generateViewAction($module,$entity));
            } else {
                \tools::file_put_contents(PROFILE_PATH . $this->getConfig('pathOfViewFile'), $_POST['editor']);
            }
            $return = array('eval' => '$("#' . $this->getId() . '",ParsimonyAdmin.currentBody).html("' . $mess . '");', 'notification' => t('Saved but', FALSE) . ' : ' . $mess, 'notificationType' => 'normal');
        }else{
            $return = array('eval' => '$("#' . $this->getId() . '",ParsimonyAdmin.currentBody).html("' . $mess . '");', 'notification' => t('Error', FALSE) . ' : ' . $mess, 'notificationType' => 'negative');
        }
        if (ob_get_level()) ob_clean();
	echo json_encode($return);
        exit;
    }

}

?>
