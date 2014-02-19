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
 * @package core\fields
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace core\fields;

/**
 * @title N:N Association Form
 * @description N:N Association Form
 * @copyright 1
 * @browsers all
 * @php_version_min 5.3
 * @modules_dependencies core:1
 */

class formasso extends \field {

	protected $type = '';
	protected $characters_max = '';
	protected $entity_asso = ''; //todo put module_...
	protected $entity_foreign = '';//todo put module_...
	protected $mode = 'default'; /* default / tag */

	/**
	 * Fill SQL Features
	 * @return FALSE
	 */
	public function sqlModel() {
		return FALSE;
	}

	public function validate($values) {
		$this->value = $values;
		\app::addListener('afterInsert', array($this, 'process'));
		\app::addListener('afterUpdate', array($this, 'process'));
		return $values;
	}

	public function process($vars) { 

		\app::removeListener('afterInsert');
		\app::removeListener('afterUpdate');

		$idEntity = $this->entity->getId()->name;
		$foreignEntity = \app::getModule($this->entity->getModule())->getEntity($this->entity_foreign);
		$idNameForeignEntity = $foreignEntity->getId()->name;
		$assoEntity = \app::getModule($this->entity->getModule())->getEntity($this->entity_asso);
		$idAsso = $assoEntity->getId()->name;

		/* Get old links */
		$old = array();
		foreach(\PDOconnection::getDB()->query('SELECT ' . PREFIX . $assoEntity->getTableName() . '.* FROM ' . PREFIX . $assoEntity->getTableName() . ' WHERE ' . $idEntity .' = '. $vars[':' . $idEntity], \PDO::FETCH_ASSOC) as $oldRows){
			$old[$oldRows[$idNameForeignEntity]] = $oldRows[$idAsso];
		}
		
		/* Add new links */
		if (!empty($this->value) && is_array($this->value)) { /* is_array in case all items are removed and $this->value == "empty" */
			foreach ($this->value as $idForeign => $value) {
				if (isset($old[$idForeign])) {
					unset($old[$idForeign]);
				} else {
					if (substr($idForeign, 0, 3) === 'new') {
						$idForeign = $foreignEntity->insertInto(array($idNameForeignEntity => '', $foreignEntity->getBehaviorTitle() => trim($value)), FALSE);
					}
					$assoEntity->insertInto(array($idAsso => '', $idEntity => $vars[':' . $idEntity], $idNameForeignEntity => $idForeign), FALSE);
				}
			}
		}

		/* Remove killed links */
		if(!empty($old)){
			foreach ($old as $linkID) {
				$assoEntity->delete($linkID, FALSE);
			}
		}
	}

}

?>
