<?php
namespace crm\model;
/**
* Description of entity contact
* @author Parsimony
* @top 82px
* @left 572px
*/
class contact extends \entity {

	protected $id_contact;
	protected $id_company;
	protected $name;
	protected $firstname;
	protected $title;
	protected $street;
	protected $city;
	protected $state;
	protected $code;
	protected $country;
	protected $description;
	protected $mail;
	protected $skype;
	protected $twitter;
	protected $phone;
	protected $mobile;
	protected $websiteurl;
	protected $employees;
	protected $annualrevenue;
	protected $type;
	protected $leadsource;
	protected $stage;
	protected $status;
	protected $assessment;
	protected $industry;


	public function __construct(\field_ident $id_contact,\field_foreignkey $id_company,\field_string $name,\field_string $firstname,\field_string $title,\field_string $street,\field_string $city,\field_string $state,\field_string $code,\field_string $country,\field_wysiwyg $description,\field_mail $mail,\field_string $skype,\field_string $twitter,\field_string $phone,\field_string $mobile,\field_url $websiteurl,\field_string $employees,\field_string $annualrevenue,\field_state $type,\field_state $leadsource,\field_state $stage,\field_state $status,\field_state $assessment,\field_state $industry) {
		parent::__construct();
		$this->id_contact = $id_contact;
		$this->id_company = $id_company;
		$this->name = $name;
		$this->firstname = $firstname;
		$this->title = $title;
		$this->street = $street;
		$this->city = $city;
		$this->state = $state;
		$this->code = $code;
		$this->country = $country;
		$this->description = $description;
		$this->mail = $mail;
		$this->skype = $skype;
		$this->twitter = $twitter;
		$this->phone = $phone;
		$this->mobile = $mobile;
		$this->websiteurl = $websiteurl;
		$this->employees = $employees;
		$this->annualrevenue = $annualrevenue;
		$this->type = $type;
		$this->leadsource = $leadsource;
		$this->stage = $stage;
		$this->status = $status;
		$this->assessment = $assessment;
		$this->industry = $industry;

	}












// DON'T TOUCH THE CODE ABOVE ##########################################################
}
?>