<?php
//If block is configured
if($this->getConfig('module')){
$entity = \app::getModule($this->getConfig('module'))->getEntity($this->getConfig('entity'));
//If submit button is pressed
	if(isset($_POST['add'])){
	//Recaptcha Verification Code
		if($this->getConfig('recaptcha_activation') == 1)
		{ 
		require_once('lib/recaptcha/recaptchalib.php');
		$privatekey = $this->getConfig('recaptcha_privatekey');
		$resp = recaptcha_check_answer ($privatekey,
										$_SERVER["REMOTE_ADDR"],
										$_POST["recaptcha_challenge_field"],
										$_POST["recaptcha_response_field"]);
			if (!$resp->is_valid) {
			//if Captcha was entered wrong
			$captcha = false;
			echo '<div class="notify negative">'.t('Captcha was not entered correctly, please retry').'</div>';
			} else {
			// Captcha is correct
			$captcha = true;
			}
		} else {
		//If recaptcha is unactive disregard captcha verification
		$captcha = true;
		}
	
		//Database insertion 
		if ($captcha == true)
		{
		
		
		if($entity->insertInto($_POST)){
		echo '<div class="notify positive">'.t($this->getConfig('success')).'</div>';
		}else{
		echo '<div class="notify negative">'.t($this->getConfig('fail')).'</div>';
		}
			
	//Creation of the email to send to the defined email
		
	//Retrieve values that will be sent (skip token, captcha and id)
		$i = 0;
		$table_id = "id_".$this->getConfig('entity');
			foreach($_POST as $key => $val)
			{
				if ($key != "TOKEN" && $key !="recaptcha_challenge_field" && $key !="recaptcha_response_field" && $key !="add" && $key !=$table_id)
				{
				$form_keys[$i] = $key;
				$form_values[$i] = $val;
				$i++;
				}
			}
		unset($i);
	
		//Creation of the Subject 
		$form_name = $this->getName();
		$subject = $form_name.' '.t('Responded');
	
		//Send Email
		ob_start();
		include('admin/views/mail/contactform.php');
		$body = ob_get_clean();
		tools::sendMail($this->getConfig('notifyemail'),$this->getConfig('notifyemail'), $this->getConfig('notifyemail'),$subject,$body);
		}
	}
		include(PROFILE_PATH .$this->getConfig('pathOfView'));
} else {echo t('Please configure this block');}
	
?>