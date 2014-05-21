<?php
namespace crm\model;
/**
* Description of entity company
* @author Parsimony
* @top 50px
* @left 300px
*/
class company extends \entity {

	protected $id_company;
	protected $name;
	protected $street;
	protected $city;
	protected $state;
	protected $code;
	protected $country;
	protected $description;
	protected $phone;
	protected $websiteurl;
	protected $employees;
	protected $accounttype;
	protected $ownership;
	protected $industry;
	protected $annualrevenue;


	public function __construct(\field_ident $id_company,\field_string $name,\field_string $street,\field_string $city,\field_string $state,\field_string $code,\field_string $country,\field_wysiwyg $description,\field_string $phone,\field_url $websiteurl,\field_string $employees,\field_state $accounttype,\field_state $ownership,\field_state $industry,\field_string $annualrevenue) {
		parent::__construct();
		$this->id_company = $id_company;
		$this->name = $name;
		$this->street = $street;
		$this->city = $city;
		$this->state = $state;
		$this->code = $code;
		$this->country = $country;
		$this->description = $description;
		$this->phone = $phone;
		$this->websiteurl = $websiteurl;
		$this->employees = $employees;
		$this->accounttype = $accounttype;
		$this->ownership = $ownership;
		$this->industry = $industry;
		$this->annualrevenue = $annualrevenue;

	}












// DON'T TOUCH THE CODE ABOVE ##########################################################
}
?>