<?php
namespace blog\model;
/**
* Description of entity tag
* @author Parsimony
* @top 404px
* @left 724px
*/
class tag extends \entity {

    protected $id_tag;

    protected $name;

    protected $url;

    protected $dgdfghdfhg;



public function __construct(\field_ident $id_tag,\field_string $name,\field_url_rewriting $url,\field_string $dgdfghdfhg) {
        parent::__construct();
        $this->id_tag = $id_tag;
        $this->name = $name;
        $this->url = $url;
        $this->dgdfghdfhg = $dgdfghdfhg;

}




// DON'T TOUCH THE CODE ABOVE ##########################################################

}
?>