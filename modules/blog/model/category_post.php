<?php
namespace blog\model;
/**
* Description of entity category_post
* @author Parsimony
* @top 0px
* @left 657px
*/
class category_post extends \entity {

    protected $id_category_post;

    protected $id_category;

    protected $id_post;



public function __construct(\field_ident $id_category_post,\field_foreignkey $id_category,\field_foreignkey $id_post) {
        parent::__construct();
        $this->id_category_post = $id_category_post;
        $this->id_category = $id_category;
        $this->id_post = $id_post;

}



// DON'T TOUCH THE CODE ABOVE ##########################################################

}
?>