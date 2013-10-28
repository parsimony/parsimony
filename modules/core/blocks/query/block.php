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
 * @copyright  Julien Gras et Benoît Lorillot
 * @version  Release: 1.0
 * @category  Parsimony
 * @package core/blocks
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace core\blocks;

/**
 * @title Query
 * @description is a point-and-click interface to build and display SQL Queries
 * @version 1
 * @browsers all
 * @php_version_min 5.3
 * @block_category database
 * @modules_dependencies core:1
 */
class query extends code {

	protected $viewPath;

	public function __construct($id) {
		parent::__construct($id);
		$this->setConfig('regenerateview', 0);
	}

	public function saveConfigs() {

		$viewPath = PROFILE_PATH . $this->getConfig('viewPath');

		/* Mode Read/Write */
		if ($_POST['mode'] === '') {
			$this->setConfig('pagination', $_POST['pagination']);
			$this->setConfig('nbitem', $_POST['nbitem']);
			$this->setConfig('selected', $_POST['properties']);
			$this->setConfig('filter', $_POST['filter']);
			$this->setConfig('sort', $_POST['sort']);
			$this->setConfig('group', $_POST['group']);
			$this->setConfig('regenerateview', $_POST['regenerateview']);
			if (isset($_POST['tables']))
				$this->setConfig('tables', $_POST['tables']);

			$myView = new \view();
			if (isset($_POST['relations']))
				$myView = $myView->initFromArray($_POST['properties'], $_POST['relations']);
			else
				$myView = $myView->initFromArray($_POST['properties']);
		}elseif ($_POST['mode'] === 'r') {
			$myView = $this->getConfig('view');
		}

		/* Pagination for all modes */
		$this->setConfig('pagination', $_POST['pagination']);
		$this->setConfig('nbitem', $_POST['nbitem']);
		if ($this->getConfig('pagination'))
			$myView->setPagination(TRUE);
		if ($this->getConfig('nbitem') != '')
			$myView->limit($this->getConfig('nbitem'));

		$this->setConfig('view', $myView);

		if ($_POST['mode'] === '') {
			/* Test for errors in view and save */
			\app::addListener('error', array($this, 'catchError'));
			/* Test if new file contains errors */
			$testIfHasError = \tools::testSyntaxError($_POST['editor'], array('_this' => $this, 'view' => $myView));
			/* If new file contains errors */
			if ($testIfHasError === TRUE) {
				/* If there's no errors, Save new file */
				if ($this->getConfig('regenerateview') == 0) {
					\tools::file_put_contents($viewPath, $this->generateViewAction($_POST['properties']));
				} else {
					\tools::file_put_contents($viewPath, $_POST['editor']);
				}
			}
		}
	}

	public function generateViewAction($properties, $pagination = '', $filter = '', $sort = '') {
		$view_code = '<?php if (!$view->isEmpty()) : ?>' . PHP_EOL;
		$view_code .= "\t" . '<?php foreach ($view as $row) : ?>' . PHP_EOL;
		$view_code .= "\t\t" . '<div class="itemscope">' . PHP_EOL;
		$myView = new \view();
		if (!empty($properties)) {
			$myView = $myView->initFromArray($properties);
			foreach ($myView->getFields() AS $sqlName => $field) {
				$name = $field->module . '_' . $field->entity . '_' . $field->name;
				if(isset($properties[$name]['display'])){
					if (get_class($field) !== 'core\fields\ident')
						$displayLine = '()';
					else
						$displayLine = '';
					$view_code .= "\t\t\t" . '<div class="itemprop ' . $sqlName . '"><?php echo $row->' . $sqlName . $displayLine . '; ?></div>' . PHP_EOL;
				}
				
			}
		} else {
			$view_code .= "\t\t\t<?php //You have to create your query before ?>" . PHP_EOL;
		}
		$view_code .= "\t\t" . '</div>' . PHP_EOL;
		$view_code .= "\t" . '<?php endforeach; ?>' . PHP_EOL;
		$view_code .= '<?php else: ?>' . PHP_EOL;
		$view_code .= "\t" . '<div class="noResults"><?php echo t(\'No results\'); ?></div>' . PHP_EOL;
		$view_code .= '<?php endif; ?>' . PHP_EOL;
		if ($this->getConfig('pagination') || ($pagination == 1))
			$view_code .= PHP_EOL . PHP_EOL . '<?php echo $view->getPagination(); ?>' . PHP_EOL;
		return $view_code;
	}

	public function catchError($code, $file, $line, $message) {
		$mess = $message . ' ' . t('in line') . ' ' . $line;
		if ($code == 0 || $code == 2 || $code == 8 || $code == 256 || $code == 512 || $code == 1024 || $code == 2048 || $code == 4096 || $code == 8192 || $code == 16384) {
			/* If it's a low level error, we save but we notice the dev */
			if ($this->getConfig('regenerateview') == 0) {
				\tools::file_put_contents(PROFILE_PATH . $this->getConfig('viewPath'), $this->generateViewAction($_POST['properties']));
			} else {
				\tools::file_put_contents(PROFILE_PATH . $this->getConfig('viewPath'), $_POST['editor']);
			}
			$return = array('eval' => '$("#' . $this->getId() . '",ParsimonyAdmin.currentBody).html("' . $mess . '");', 'notification' => t('Saved but', FALSE) . ' : ' . $mess, 'notificationType' => 'normal');
		} else {
			$return = array('eval' => '$("#' . $this->getId() . '",ParsimonyAdmin.currentBody).html("' . $mess . '");', 'notification' => t('Error', FALSE) . ' : ' . $mess, 'notificationType' => 'negative');
		}
		if (ob_get_level()) ob_clean();
		echo json_encode($return);
		exit;
	}

	public function getView() {
		ob_start();
		\app::addListener('beforeBuildQuery', array($this, 'process'));
		$view = $this->getConfig('view');
		if ($view != FALSE) {
			include($this->getConfig('viewPath'));
		} else {
			echo t('Please check the query configuration');
		}
		return ob_get_clean();
	}

	public function process() {
		$view = $this->getConfig('view');
		if (is_object($view)) {
			$selected = $this->getConfig('selected');
			if(isset($_POST['filter']) && is_array($_POST['filter']) && $this->getConfig('filter') ){
				$_POST['filter'] = array_filter($_POST['filter']);//remove all empty() values
				foreach ($_POST['filter'] as $property => $value) {
					$field = $view->getField($property);
					$name = $field->module . '_' . $field->entity . '_' . $field->name;
					if($field !== FALSE && isset($selected[$name]['filter'])){ /* IF field exists and filter is allowed */
						$filterRes = $field->sqlFilter($_POST['filter'][$field->name]);
						if(!empty($filterRes)){
							$view->where($filterRes);
						}
					}
				}
			}
			
			if(isset($_POST['group']) && is_array($_POST['group']) && $this->getConfig('group') ){
				foreach ($_POST['group'] as $property => $value) {
					$field = $view->getField($property);
					$name = $field->module . '_' . $field->entity . '_' . $field->name;
					if($field !== FALSE && isset($selected[$name]['group'])){ /* IF field exists and group is allowed */
						$view->groupBy($field->sqlGroup($_POST['group'][$field->name]));
					}
				}
			}
			
			if (isset($_POST['sort']) && is_array($_POST['sort']) && $this->getConfig('sort')) {
				$_POST['sort'] = array_filter($_POST['sort']);//remove all empty() values
				foreach ($_POST['sort'] as $property => $value) {
					$field = $view->getField($property);
					$name = $field->module . '_' . $field->entity . '_' . $field->name;
					if($field !== FALSE && isset($selected[$name]['sort'])){ /* IF field exists and sort is allowed */
						$cut = strrpos($_POST['sort'], '_');
						$sort = substr($_POST['sort'], $cut + 1);
						if ($sort === 'asc' || $sort === 'desc')
							$view->order($field->module . '_' . $field->entity . '.' . substr($_POST['sort'], 0, $cut), $sort);
					}
				}
			}
		}
		\app::removeListener('beforeBuildQuery'); /* have to filter once a page load  */
	}
	
	public function onMove($typeProgress, $module, $name, $themeType = 'desktop') {
		$oldPath = $this->getConfig('viewPath');
		if ($typeProgress === 'theme')
			$path = $module . '/themes/' . $name . '/' . $themeType . '/views/' . $this->id . '.php';
		else
			$path = $module . '/pages/views/' . $themeType . '/' . $this->id . '.php';
		
		if (is_file(PROFILE_PATH . $path) === FALSE) { /* check if a view with this path already exists in profile */
			$this->setConfig('viewPath', $path); /* save the new path */
			if (!empty($oldPath) && stream_resolve_include_path($oldPath) !== FALSE) { /* Check if we have to move an old view  : moveBlock */
				\tools::file_put_contents(PROFILE_PATH . $path, file_get_contents($oldPath, FILE_USE_INCLUDE_PATH));
				if(is_file(PROFILE_PATH . $oldPath))
					rename(PROFILE_PATH . $oldPath, PROFILE_PATH . $oldPath . '.back'); //do only for profile, not modules
			} else { /* add block */
				\tools::file_put_contents(PROFILE_PATH . $path, '<h1>' . t('Start programming in this area', false) . '</h1>');
			}
		} else {
			return FALSE; // a view with this ID already exists
		}
	}

	public function destruct() {
		$path = PROFILE_PATH . $this->getConfig('viewPath');
		if (is_file($path) === TRUE) {
			rename($path, $path . '.back');
		}
	}

}
?>
