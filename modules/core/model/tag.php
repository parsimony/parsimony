<?php
namespace core\model;
/**
* Description of entity tag
* @author Parsimony
* @top 413px
* @left 272px
*/
class tag extends \entity {

    protected $id_tag;
    protected $name;
    protected $url;


public function __construct(\field_ident $id_tag,\field_string $name,\field_url_rewriting $url) {
        $this->id_tag = $id_tag;
        $this->name = $name;
        $this->url = $url;

}
// DON'T TOUCH THE CODE ABOVE ##########################################################

}
?>