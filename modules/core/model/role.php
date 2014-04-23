<?php
namespace core\model;
/**
* Description of entity role
* @author Parsimony
* @top 43px
* @left 643px
*/
class role extends \entity {

	protected $id_role;
	protected $name;
	protected $permissions;


	public function __construct(\field_ident $id_role,\field_string $name,\field_numeric $permissions) {
		parent::__construct();
		$this->id_role = $id_role;
		$this->name = $name;
		$this->permissions = $permissions;

	}

	public function beforeDelete($id) {
		if ($id < 5) { /* Can't delete predefined roles' */
			return FALSE;
		}
	}
	
	public function getViewAddForm() {
		$this->setNewPermissionsView();
		return parent::getViewUpdateForm();
	}
	
	public function getViewUpdateForm() {
		$this->setNewPermissionsView();
		return parent::getViewUpdateForm();
	}
	protected function setNewPermissionsView(){
		$views = $this->fields['permissions']->views;
		$views['form'] = 'modules/admin/views/permissions.php';
		$this->fields['permissions']->views = $views;
	}

	public function beforeInsert($vars) {
		return $this->savePerm($vars);
	}
	
	public function beforeUpdate($vars) {
		$oldPerm = $this->where('id_role = ' . $vars['id_role'])->fetch()->permissions->value;
		return $this->savePerm($vars, $oldPerm);
	}
	
	public function savePerm($vars, $oldPerm = 0) {
		$newperm = 0;
		foreach ($this->permissionGroups as $groupTitle => $permissionGroup) {
			foreach ($permissionGroup as $key => $right) {
				if ($_SESSION['permissions'] & $key) {
					if (isset($vars['permissions'][$key])) {
						$newperm += $key;
					}
				} elseif ($oldPerm & $key) {
					$newperm += $key;
				}
			}
		}
		$vars['permissions'] = $newperm;
		return $vars;
	}
	
	protected $permissionGroups = array(
		'Settings' => array(
			1 => 'Basic settings',
			2 => 'Development settings'),
		'Pages' => array(
			4 => 'Modify SEO for pages',
			8 => 'Add, modify delete pages'),
		'Design' => array(
			16 => 'Design CSS Styles',
			32 => 'Choose a theme',
			64 => 'Add, delete, ducplicate and choose themes',
			128 => 'Configure blocks',
			256 => 'Add, delete, move blocks',
			4096 => 'DB designer'),
		'Files' => array(
			512 => 'File Explorer',
			1024 => 'File upload',
			2048 => 'Restrict file operations to medias'),
		'Administration' => array(
			8192 => 'Multisite',
			16384 => 'Module management',
			32768 => 'Translate'),
		'Accounts' => array(65536 => 'Grant'));

	public function getPermissionGroups() {
		return $this->permissionGroups;
	}
	
	public function __sleep() {
		unset($this->permissionGroups);
		return parent::__sleep();
	}

// DON'T TOUCH THE CODE ABOVE ##########################################################

}
?>