<?php
namespace blog\model;
/**
* Description of entity tag
* @author Parsimony
* @top 383px
* @left 873px
*/
class tag extends \entity {

	protected $id_tag;
	protected $name;
	protected $url;


	public function __construct(\field_ident $id_tag,\field_string $name,\field_url_rewriting $url) {
		parent::__construct();
		$this->id_tag = $id_tag;
		$this->name = $name;
		$this->url = $url;

	}



// DON'T TOUCH THE CODE ABOVE ##########################################################

}
?>