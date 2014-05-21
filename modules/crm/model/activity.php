<?php
namespace crm\model;
/**
* Description of entity activity
* @author Parsimony
* @top 115px
* @left 785px
*/
class activity extends \entity {

	protected $id_activity;
	protected $id_contact;
	protected $object;
	protected $duedate;
	protected $description;
	protected $type;
	protected $id_user;


	public function __construct(\field_ident $id_activity,\field_foreignkey $id_contact,\field_string $object,\field_date $duedate,\field_wysiwyg $description,\field_state $type,\field_foreignkey $id_user) {
		parent::__construct();
		$this->id_activity = $id_activity;
		$this->id_contact = $id_contact;
		$this->object = $object;
		$this->duedate = $duedate;
		$this->description = $description;
		$this->type = $type;
		$this->id_user = $id_user;

	}












// DON'T TOUCH THE CODE ABOVE ##########################################################
}
?>