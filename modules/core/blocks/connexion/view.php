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
 * @package core/blocks
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
if (\app::getClass('user')->VerifyConnexion()) :
    ?>
    <h3><?php echo t('My account') ?></h3>
    <div class="userInfo">
	<?php echo t('You are connected') ?> (<a class="logout" href="logout"><?php echo t('Logout') ?></a>)
    </div>
<?php else : ?>
    <form method="POST" action="<?php echo BASE_PATH; ?>login" class="connexion">
        <div class="none error"></div>
        <div class="connectLogin"><label><?php echo t('User'); ?></label><input type="text" name="login" class="login" /></div>
        <div class="connectPassword"><label><?php echo t('Password'); ?></label><input type="password" name="password" class="password" /></div>
        <div class="connectSubmit"><input type="submit" value="<?php echo t('Login'); ?>" /></div>
    </form>
    <script>
        $(document).ready(function() {
    	$(document).on("submit","#<?php echo $this->getId(); ?> .connexion",function(e){
    	    e.preventDefault();
    	    $.post("<?php echo BASE_PATH; ?>login", { TOKEN: TOKEN, login: $(".login", this).val(), password: $(".password", this).val() },
    	    function(data) {
    		if(data != 0){
    		    window.location.reload();
    		}else{
    		    $("#<?php echo $this->getId(); ?> .error").fadeIn().html("<?php echo t('Login or/and password are invalid'); ?>");
    		}
    	    });
    	});
        });
    </script>
<?php endif; ?>
