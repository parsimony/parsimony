<?php
namespace core\model;
/**
* Description of entity user
* @author Parsimony
* @top 302px
* @left 1427px
*/
class user extends \entity {

    protected $id_user;






public function __construct(\field_ident $id_user,\field_string $pseudo,\field_mail $mail,\field_password $pass,\field_foreignkey $id_role) {
        $this->id_user = $id_user;
        $this->pseudo = $pseudo;
        $this->mail = $mail;
        $this->pass = $pass;
        $this->id_role = $id_role;

}
// DON'T TOUCH THE CODE ABOVE ##########################################################

}

?>