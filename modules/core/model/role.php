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
	protected $state;


	public function __construct(\field_ident $id_role,\field_string $name,\field_state $state) {
		parent::__construct();
		$this->id_role = $id_role;
		$this->name = $name;
		$this->state = $state;

	}

	public function beforeDelete($id) {
		if ($id < 7) { /* Can't delete predefined roles' */
			return FALSE;
		}
	}






// DON'T TOUCH THE CODE ABOVE ##########################################################

}
?>