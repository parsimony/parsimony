<?php
namespace core\model;
/**
* Description of entity user
* @author Parsimony
* @top 280px
* @left 808px
*/
class user extends \entity {

	protected $id_user;
	protected $pseudo;
	protected $mail;
	protected $pass;
	protected $registration;
	protected $state;
	protected $id_role;


	public function __construct(\field_ident $id_user,\field_string $pseudo,\field_mail $mail,\field_password $pass,\field_date $registration,\field_boolean $state,\field_foreignkey $id_role) {
		parent::__construct();
		$this->id_user = $id_user;
		$this->pseudo = $pseudo;
		$this->mail = $mail;
		$this->pass = $pass;
		$this->registration = $registration;
		$this->state = $state;
		$this->id_role = $id_role;

	}







// DON'T TOUCH THE CODE ABOVE ##########################################################

	public function beforeInsert($vars) {
		$this->_newPassword = $vars['pass'];
		return $vars;
	}

	public function afterInsert($vars) {
		$name = $login = $vars[':pseudo'];
		$password = $this->_newPassword;
		$companyMail = \app::$config['mail']['adminMail'];
		ob_start();
		include('admin/views/mail/registration.php');
		$body = ob_get_clean();

		if (\tools::sendMail($vars[':mail'], \app::$config['mail']['adminMail'], \app::$config['mail']['adminMail'], t('Your profile has been created with success', FALSE), $body)) {
			return '1';
		} else {
			return '0';
		}
	}

}

?>