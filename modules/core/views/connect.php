<?php
/**
 * Parsimony
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@parsimony-cms.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Parsimony to newer
 * versions in the future. If you wish to customize Parsimony for your
 * needs please refer to http://www.parsimony.mobi for more information.
 *
 * @authors Julien Gras et Benoît Lorillot
 * @copyright Julien Gras et Benoît Lorillot
 * 
 * @category Parsimony
 * @package core
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

if (\app::getClass('user')->VerifyConnexion()) { 
    header('Location: '.BASE_PATH.'index');
    exit;
}

?>
<meta name='robots' content='noindex,nofollow' />
<meta http-equiv="X-UA-Compatible" content="chrome=1">

<!--[if IE]>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/chrome-frame/1/CFInstall.min.js"></script>
<style>
    .chromeFrameInstallDefaultStyle {
    width: 800px;
    border: 5px solid blue;
    z-index: 99999;
    }
</style>

<script>
alert("Parsimony chooses modern browsers to improve your experience. Click ok to install Google Frame for IE or better yet use Chrome web browsers.");
    window.attachEvent("onload", function() {
    CFInstall.check({
        mode: "inline", // the default
        oninstall: function(){
            alert("Chrome Frame is now installed. Restart your browser to start enjoying Parsimony!");
        }
    });
    });
</script>
<![endif]-->
<?php

if (isset($_POST['connexion'])) {
    $user = \app::getClass('user');
    $user->authentication($_POST['login'], $_POST['password']);
    if ($user->VerifyConnexion()) {
        header('Location: ' . BASE_PATH);
        exit;
    } else {
        echo '<div id="wrong">'. t('The username or password you entered is incorrect', FALSE) . '</div>';
    }
}

?>
<!DOCTYPE html> 
<html> 
    <head> 
        <title><?php echo t('Login'); ?></title> 
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
        <meta name='robots' content='noindex,nofollow' /> 
        <link rel="stylesheet" type="text/css" href="<?php echo BASE_PATH ?>admin/css/ui.css">
        <style>    
			#wrong{top: 35px;position: relative;padding : 10px;display: inline;background: #ECECEC;}
			#wrong:before{position: relative;color: #ee5a2d;font-size: 25px;font-weight: bold;content: "\d7";left: -5px;top: 4px;}
            body{text-align: center;font-family: HelveticaNeue, Helvetica, Arial, sans-serif;font-size: 13px;background: #fafafa;}
            #content{margin: 200px auto;position: relative;width: 503px;height: 253px;box-shadow: 2px 2px 3px #CECECE, 0px 0px 4px #CACACA;}
			#quote{font-size: 15px;letter-spacing: 1.5px;color: #555;display: inline-block;bottom: 8px;position: relative;}
			input[type="text"], input[type="password"]{border-style: none;text-shadow: none;border-radius: 0px;padding: 4px;outline: none;background: white;padding-left: 12px;}
			button, input[type='button'], input[type='submit'] {-webkit-user-select: none;-moz-user-select: none;background: rgb(45, 193, 238);color: #fafafa;font-size: inherit;margin-bottom: 0px;width: 200px;border: none;padding: 3px 12px 3px 12px;height: 35px;font-weight: bold;}
			button:hover, input[type='button']:hover, input[type='submit']:hover{background: rgb(41, 170, 209);box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.2);border-color: none;color: #fefefe;}
            #img{float: left;width: 250px;height: 250px;margin: 0px auto;font: bold 11px Sans-Serif;padding: 6px 10px;color: #666;padding-top: 30px;background: #fefefe;}
            label{font-size: 20px;line-height: 25px;}
            label, div a{font-size: 13px;line-height: 25px;font-weight: bold;}
            div a{font-size: 15px;line-height: 18px;}
            #content form{width: 250px;height: 250px;float: left;border-color: #DDD;text-shadow: 0 1px 1px white;padding: 6px 10px;color: #666;background: #fefefe;}
            #content .login > div, #content .display > div{padding:15px;}
            #content > form a{color: #555;}
            input:-webkit-autofill {background-color: #fafafa !important;-webkit-box-shadow: 0 0 0px 30px #fafafa inset;color: #777 !important;} /* Removing input background color for Chrome autocomplete trick */
            #content > form input[type="text"],#content > form input[type="password"]{width: 200px;height: 35px;background-color: #fafafa;border-left: 5px solid rgb(45, 193, 238);font-weight: bold;color: #777;}
			.mail .login{display : none;}
			.mail .display {display : block;}
			.display{display : none;}
			#mail{display:none;font-size: 15px;line-height: 18px;padding: 15px 0;}			
			#back{color:rgb(45, 193, 238);cursor: pointer;font-weight: bold;text-align: left;}
			#back:hover{color: rgb(41, 170, 209);}
			.display.flip , .login.flip {-webkit-backface-visibility: visible !important;-webkit-animation-name: flip;backface-visibility: visible !important;animation-name: flip;
			}
			.display, .login  {-webkit-animation-duration: 0.5s;animation-duration: 0.5s;-webkit-animation-fill-mode: both;	animation-fill-mode: both;}	
			@-webkit-keyframes flip {0% {-webkit-transform: perspective(400px) translateZ(0) rotateY(0) scale(0.6);	-webkit-animation-timing-function: ease-out;}
			40% {-webkit-transform: perspective(400px) translateZ(150px) rotateY(170deg) scale(0.6);-webkit-animation-timing-function: ease-out;}
			50% {-webkit-transform: perspective(400px) translateZ(150px) rotateY(190deg) scale(0.6);-webkit-animation-timing-function: ease-in;}
			80% {-webkit-transform: perspective(400px) translateZ(0) rotateY(360deg) scale(.95);-webkit-animation-timing-function: ease-in;}
			100% {-webkit-transform: perspective(400px) translateZ(0) rotateY(360deg) scale(1);-webkit-animation-timing-function: ease-in;}
			}
			@keyframes flip {0% {transform: perspective(400px) translateZ(0) rotateY(0) scale(0.6);animation-timing-function: ease-out;}
			40% {transform: perspective(400px) translateZ(150px) rotateY(170deg) scale(0.6);animation-timing-function: ease-out;}
			50% {transform: perspective(400px) translateZ(150px) rotateY(190deg) scale(0.6);animation-timing-function: ease-in;}
			80% {transform: perspective(400px) translateZ(0) rotateY(360deg) scale(.95);animation-timing-function: ease-in;}
			100% {transform: perspective(400px) translateZ(0) rotateY(360deg) scale(1);animation-timing-function: ease-in;}
			}
        </style>
    </head>  
    <body> 
        
        <div id="content" class="box lifted jquery-shadow jquery-shadow-lifted">
            <div id="img">
                <img src="<?php echo BASE_PATH; ?>core/img/logo-parsimony-big.png">
            </div>
            <form action="" method="POST"> 
				<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>">
				<div class="login">
					<div style="text-align: left;" >                    
						<input type="text" placeholder="<?php echo t('Username'); ?>" name="login" autofocus/>
                </div> 
					<div style="text-align: left;" >         
						<input type="password" placeholder="<?php echo t('Password'); ?>" name="password" />
                </div>
					<div > 
						<input style="font-size: 16px;" type="submit" name="connexion" value="<?php echo t('Login'); ?>" /> 
                </div>
					<a  style="text-decoration: none" href="" onclick="document.getElementById('content').classList.toggle('mail');document.querySelector('.display').classList.add('flip');return false;"><?php echo t('Lost your password'); ?></a><span > ?</span>
				</div>
                
                <div class="display" style="font-weight: normal"> 
                    <div id="back" onclick="document.getElementById('content').classList.toggle('mail');document.querySelector('.login').classList.add('flip');return false;">< Back to login</div>
                    <div>
						<input style="margin : 0px 0 20px 0;" placeholder="<?php echo t('Enter your email') ?>" type="text" id="newmdp"/>
                        <input type="button" value="<?php echo  t('Send') ?>" id="newmdpgo" />
                    </div>
                </div>
            </form> 
        </div> 
        <script LANGUAGE="Javascript" SRC="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"> </script>
	<script>window.jQuery || document.write('<script src="<?php echo BASE_PATH ?>lib/jquery/jquery-1.10.1.min.js"><\/script>')</script>
        <script>
            $(document).ready(function () {
                if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1){
		    $('#header').after('<div style="postion:absolute;top:0;font-size:25px;text-align:center;line-height: 50px;letter-spacing: 1.5px; color:#777;text-shadow: 0px 1px 0px white;z-index:99999999;top:80px;position:absolute;width:100%">It is recommended to use Parsimony with <a style="color:#777;font-size:25px;text-align:center;line-height: 50px;letter-spacing: 1.5px;text-shadow: 0px 1px 0px white;" href="https://www.google.com/chrome">Google Chrome</a> or <a style="color:#777;font-size:25px;text-align:center;line-height: 50px;letter-spacing: 1.5px;text-shadow: 0px 1px 0px white;" href="http://www.opera.com">Opera</a> for administration.</div>');
		}else if(!window.chrome){
                    $('body').empty();       
                    $('<div style="postion:fixed;padding: 100px 0;font-size:35px;text-align:center;line-height: 50px;letter-spacing: 1.5px; color:#777;text-shadow: 0px 1px 0px white;z-index:99999999;width:100%;height:100%">Parsimony Beta uses <a style="color:#777;font-size:40px;text-align:center;line-height: 50px;letter-spacing: 1.5px;text-shadow: 0px 1px 0px white;" href="https://www.google.com/chrome">Google Chrome</a> for administration.<br>Please <a style="color:#777;text-shadow: 0px 1px 0px white;font-size:35px;text-align:center;line-height: 50px;letter-spacing: 1.5px;" href="https://www.google.com/chrome">Install</a> or use Google Chrome </div>').prependTo('body');
                }
                $("#newmdpgo").on("click", function(){
                    $.post('<?php echo BASE_PATH; ?>renewPass',{TOKEN: "<?php echo TOKEN; ?>",mail:$("#newmdp").val()}, function(data) {
                        if(data == 1){
                            alert('<?php echo t('A new PassWord has been sent to your E-mail account'); ?>'); 
                        }else if(data == 0){
                            alert('<?php echo t('Error on mail send'); ?>');
                        }else{
                            alert(data);
                        }
                        
                    });
                });
            });
        </script>
    </body> 
</html> 