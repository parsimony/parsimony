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
 * @package core/blocks
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace core\blocks;

/**
 * @title Form
 * @description displays in one click the add/update form of db model
 * @copyright 1
 * @browsers all
 * @php_version_min 5.3
 * @block_category database
 * @modules_dependencies core:1
 */
class form extends code {

	public function __construct($id) {
		parent::__construct($id);
		$this->setConfig('regenerateview', 0);
		$this->setConfig('success', 'Success');
		$this->setConfig('fail', 'Fail');
	}

	public function saveConfigs() {
		/* mode write*/
		if ($_POST['mode'] === '' && isset($_POST['entity'])) {
			list($module, $entity) = explode(' - ', $_POST['entity']);
			$this->setConfig('module', $module);
			$this->setConfig('entity', $entity);
			$this->setConfig('regenerateview', $_POST['regenerateview']);
			$this->setConfig('updateparam', $_POST['updateparam']);

			$viewPath = PROFILE_PATH . $this->getConfig('viewPath');

			/* Test for errors in view and save */
			\app::addListener('error', array($this, 'catchError'));
			/* Test if new file contains errors */
			$entity = \app::getModule($this->getConfig('module'))->getEntity($this->getConfig('entity'));
			$testIfHasError = \tools::testSyntaxError($_POST['editor'], array('_this' => $this, 'entity' => $entity));
			/* If new file contains errors */
			if ($testIfHasError === TRUE) {
				/* If there's no errors, Save new file */
				if ($this->getConfig('regenerateview') == 0) {
					\tools::file_put_contents($viewPath, $this->generateViewAction($this->getConfig('module'), $this->getConfig('entity'), !empty($_POST['updateparam'])));
				} else {
					\tools::file_put_contents($viewPath, $_POST['editor']);
				}
			}
		}
		/* mode read && write*/
		$this->setConfig('success', $_POST['success']);
		$this->setConfig('fail', $_POST['fail']);
	}

	public function generateViewAction($module, $entity, $update = FALSE) {
		$entity = \app::getModule($module)->getEntity($entity);
		$html = '<?php
if(isset($_POST[\'add\'])){
	$res = $entity->' . ($update ? 'update' : 'insertInto') . '($_POST);
	if($res === TRUE || is_numeric($res)){ /* TRUE in update context or last insert id for insert */
		echo \'<div class="notify positive">\'.t($this->getConfig(\'success\')).\'</div>\';
	}else{
		echo \'<div class="notify negative">\'.t($this->getConfig(\'fail\')).\'</div>\';
	}
}
?>
<form method="post" action="">
	<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />' . PHP_EOL;
		foreach ($entity->getFields() AS $name => $field) {
			$html .= "\t\t" . '<?php echo $entity->' . $name . '()->form(); ?>' . PHP_EOL;
		}
		$html .= "\t" . '<input type="submit" value="<?php echo t(\'Save\', FALSE); ?>" name="add" class="submit">' . PHP_EOL;
		$html .= '</form>';
		return $html;
	}

	public function catchError($code, $file, $line, $message) {
		$mess = $message . ' ' . t('in line') . ' ' . $line;
		if ($code == 0 || $code == 2 || $code == 8 || $code == 256 || $code == 512 || $code == 1024 || $code == 2048 || $code == 4096 || $code == 8192 || $code == 16384) {
			/* If it's a low level error, we save but we notice the dev */
			if ($this->getConfig('regenerateview') == 1) {
				list($module, $entity) = explode(' - ', $_POST['entity']);
				\tools::file_put_contents(PROFILE_PATH . $this->getConfig('viewPath'), $this->generateViewAction($module, $entity));
			} else {
				\tools::file_put_contents(PROFILE_PATH . $this->getConfig('viewPath'), $_POST['editor']);
			}
			$return = array('eval' => '$("#' . $this->getId() . '",ParsimonyAdmin.currentBody).html("' . $mess . '");', 'notification' => t('Saved but') . ' : ' . $mess, 'notificationType' => 'normal');
		} else {
			$return = array('eval' => '$("#' . $this->getId() . '",ParsimonyAdmin.currentBody).html("' . $mess . '");', 'notification' => t('Error') . ' : ' . $mess, 'notificationType' => 'negative');
		}
        if (ob_get_level()) ob_clean();
		echo json_encode($return);
		unset($GLOBALS['lastError']); /* to avoid to display error at the end of page load */
		\app::getModule('admin')->saveAll(); /* finish to save config */
	}
	
	public function getView() {
		ob_start();
		if($this->getConfig('module')){
			$entity = \app::getModule($this->getConfig('module'))->getEntity($this->getConfig('entity'));
			if($this->getConfig('updateparam') && \app::$request->getParam($this->getConfig('updateparam'))){
				$entity->select()->where($entity->getModule() . '_' . $entity->getName() . '.' . $entity->getId()->name . ' = :' . $this->getConfig('updateparam'))->fetch();
				/* have to remove where clause, cause update have to happen just on desired id */
				$entity->clearQuery();
			}
			include($this->getConfig('viewPath'));
		}else {
			echo t('Please configure this block');
		}
		return ob_get_clean();
	}
	
}
?>
