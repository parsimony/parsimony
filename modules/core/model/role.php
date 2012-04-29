<?php
namespace core\model;
/**
* Description of entity role
* @author Parsimony
* @top 44px
* @left 1144px
*/
class role extends \entity {

    protected $id_role;
    protected $name;


public function __construct(\field_ident $id_role,\field_string $name) {
        $this->id_role = $id_role;
        $this->name = $name;

}
// DON'T TOUCH THE CODE ABOVE ##########################################################

}
?>