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
 * @copyright  Julien Gras et Benoît Lorillot
 * @version  Release: 1.0
 * @category  Parsimony
 * @package core
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        echo '<div style="top: 35px;position: relative;display: block;background: #EEE;border: 1px solid #DDD;box-shadow: 1px 2px 3px #888, -1px -1px 3px #888;padding: 10px 15px 5px 15px;border-bottom-right-radius: 10px;border-bottom-left-radius: 10px;">'
        . t('The username or password you entered is incorrect', FALSE) . '</div>';
    }
}

?>
<!DOCTYPE html> 
<html> 
    <head> 
        <title><?php echo t('Login', false); ?></title> 
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
        <meta name='robots' content='noindex,nofollow' /> 
        <link rel="stylesheet" type="text/css" href="<?php echo BASE_PATH ?>admin/style.css">
        <style>
            #header{position: fixed;min-width: 1250px;width: 100%;z-index: 999999;height: 40px;color: #555;text-shadow: white 0 1px 0;text-decoration: none;font-weight: bold;background-color: #F3F3F3;background-image: -moz-linear-gradient(bottom,#E5E9EF,#FEFEFE);background-image: -webkit-gradient(linear,left bottom,left top,from(#E5E9EF),to(#FEFEFE));box-shadow: 0 2px 6px #443;top: 0;left: 0;padding-top: 5px;}
            #header a{color: #555;text-decoration: none;cursor: pointer;padding: 0;height: 28px;}
            #header a:hover{color: rgba(191, 230, 255, 0.25);text-shadow: 0px 0px #0070A1;}
            body{background: #EEE url(<?php echo BASE_PATH ?>admin/img/connect-page.png);text-align:center;font-family: HelveticaNeue, Helvetica, Arial, sans-serif;font-size: 13px;}
            #content{margin: 200px auto;position: relative;width: 503px;height: 253px;}
            #img{float: left;width: 250px;height: 250px;margin: 0px auto;border-bottom-left-radius: 10px;background: #EEE;border: 1px solid #DDD;text-shadow: 0 1px 1px white;-webkit-box-shadow: 0 1px 1px #fff;-moz-box-shadow: 0 1px 1px #fff;box-shadow: 1px 1px 0px #fff, #F4F8FD 1px 1px 1px 0 inset;font: bold 11px Sans-Serif;padding: 6px 10px;color: #666;padding-top: 30px;}
            label{font-size: 20px;line-height: 25px;}
            label, div a{text-shadow: 0px 1px 0px white;font-size: 13px;line-height: 25px;letter-spacing: 1.5px;}
            div a{font-size: 15px;line-height: 18px;}
            #content form{width: 250px;height: 250px;border-top-right-radius: 10px;float: left;border-color: #DDD;border: 1px solid #DDD;text-shadow: 0 1px 1px white;-webkit-box-shadow: 0 1px 1px #fff;-moz-box-shadow: 0 1px 1px #fff;box-shadow: 0 1px 0px #fff;font: bold 11px Sans-Serif;padding: 6px 10px;color: #666;background: #EEE;box-shadow: 1px 1px 0px #fff, #F4F8FD 1px 1px 1px 0 inset;}
            #content > form > div{padding:11px 17px;}
            #content > form a{color: #555;}
            input:-webkit-autofill {background-color: white !important;}
            #content > form input[type="text"],#content > form input[type="password"]{width:200px;height:30px;border-radius:8px;border:solid 1px #888}
            .jquery-shadow {position: relative;}
            .jquery-shadow-lifted::before, .jquery-shadow-lifted::after {bottom: 20px;left: 10px;width: 100%;height: 20%;max-width: 485px;-webkit-transform: rotate(-3deg); -moz-transform: rotate(-3deg);-ms-transform: rotate(-3deg);-o-transform: rotate(-3deg);transform: rotate(-3deg);}
            .jquery-shadow-lifted::before, .jquery-shadow-lifted::after {bottom: 20px;left: 10px;width: 100%;height: 20%;max-width: 485px;-webkit-box-shadow: 0 15px 10px rgba(0, 0, 0, 0.7);-moz-box-shadow: 0 15px 10px rgba(0, 0, 0, 0.7);box-shadow: 0 15px 10px rgba(0, 0, 0, 0.7);-webkit-transform: rotate(-3deg);-moz-transform: rotate(-3deg);-ms-transform: rotate(-3deg);-o-transform: rotate(-3deg);transform: rotate(-3deg);}
            .jquery-shadow::before, .jquery-shadow::after {content: "";position: absolute;z-index: -2;}
            .jquery-shadow-lifted::after {right: 10px;left: auto;-webkit-transform: rotate(3deg);-moz-transform: rotate(3deg);-ms-transform: rotate(3deg);-o-transform: rotate(3deg);transform: rotate(3deg);}
            .jquery-shadow::before, .jquery-shadow::after {content: "";position: absolute;z-index: -2;}
        </style>
    </head>  
    <body> 
        <div id="header" style="padding-top: 5px;">
            <a href="http://parsimony.mobi" target="_blank" style="">
                <img title="" src="<?php echo BASE_PATH; ?>admin/img/parsimony-logo.png" alt="" style="">
            </a>
            <div style="text-shadow: 0px 1px 0px white;font-size: 15px;letter-spacing: 1.5px;color: #555;display: inline-block;bottom: 8px;position: relative;">"<?php echo t('The ability to write code is pretty much a super power in today\'s society',false); ?>" <a style="letter-spacing: 1.5px;" href="http://twitter.com/#!/mattcutts/status/172448195723530240" target="blank">Matts Cutts</a>
            </div>
        </div>
        <div id="content" class="box lifted jquery-shadow jquery-shadow-lifted">
            <div id="img">
                <img src="<?php echo BASE_PATH; ?>core/img/logo-parsimony-big.png">
            </div>
            <form action="" method="POST"> 
                <div style="text-align: left;"> 
                    <label style="padding-left:3px;"><?php echo t('Username', false); ?></label>
                    <input type="text" name="login" autofocus/>
                </div> 
                <div style="text-align: left;"> 
                    <label style="padding-left:3px;"><?php echo t('Password', false); ?></label>
                    <input type="password" name="password" />
                </div>
                <div> 
                    <input style="font-size: 16px;" type="submit" name="connexion" value="<?php echo t('Login', false); ?>" /> 
                </div>
                <div style="font-weight: normal"> 
                    <a style="text-decoration: none" href="" onclick="$('form').height(335);$('#img').height(311);$('#content').height(342);$(this).next().show();return false;"><?php echo t('Lost your password', false); ?></a> ?
                    <div style="display:none;font-size: 15px;line-height: 18px;">
                        <?php echo t('Your mail',FALSE) ?> : <input style="margin: 5px 0;" type="text" id="newmdp"/>
                        <input type="button" value="<?php echo  t('Send',FALSE) ?>" id="newmdpgo" />
                    </div>
                </div>
            </form> 
        </div> 
        <script LANGUAGE="Javascript" SRC="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"> </script>
	<script>window.jQuery || document.write('<script src="<?php echo BASE_PATH ?>lib/jquery/jquery-1.9.0.min.js"><\/script>')</script>
        <script>
            $(document).ready(function () {
                if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1){
		    $('#header').after('<div style="postion:absolute;top:0;font-size:25px;text-align:center;line-height: 50px;letter-spacing: 1.5px; color:#777;text-shadow: 0px 1px 0px white;z-index:99999999;top:80px;position:absolute;width:100%">It is highly recommended to use Parsimony Beta with <a style="color:#777;font-size:25px;text-align:center;line-height: 50px;letter-spacing: 1.5px;text-shadow: 0px 1px 0px white;" href="https://www.google.com/chrome">Google Chrome</a> for administration.</div>');
		}else if(!window.chrome){
                    $('body').empty();       
                    $('<div style="postion:fixed;padding: 100px 0;font-size:35px;text-align:center;line-height: 50px;letter-spacing: 1.5px; color:#777;text-shadow: 0px 1px 0px white;z-index:99999999;width:100%;height:100%">Parsimony Beta uses <a style="color:#777;font-size:40px;text-align:center;line-height: 50px;letter-spacing: 1.5px;text-shadow: 0px 1px 0px white;" href="https://www.google.com/chrome">Google Chrome</a> for administration.<br>Please <a style="color:#777;text-shadow: 0px 1px 0px white;font-size:35px;text-align:center;line-height: 50px;letter-spacing: 1.5px;" href="https://www.google.com/chrome">Install</a> or use Google Chrome </div>').prependTo('body');
                }
                $("#newmdpgo").on("click", function(){
                    $.post('<?php echo BASE_PATH; ?>renewPass',{mail:$("#newmdp").val()}, function(data) {
                        if(data == 1){
                            alert('<?php echo t('A new PassWord has been sent to your E-mail account', FALSE); ?>'); 
                        }else if(data == 0){
                            alert('<?php echo t('Error on mail send', FALSE); ?>');
                        }else{
                            alert(data);
                        }
                        
                    });
                });
            });
        </script>
    </body> 
</html> 