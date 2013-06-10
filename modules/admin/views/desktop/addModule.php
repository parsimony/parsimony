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
 * @package admin
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>

<script>
    $(document).ready(function() {
        $("#name_module").keyup(function(){
            this.value = this.value.toLowerCase().replace(/[^a-z]+/,"");
        });
        $("#name_titre").keyup(function(){
            this.value = this.value.toLowerCase().replace(/[^a-zA-Z ]+/,"");
        });
    });     
</script>
<div id="addmodule" class="adminzone">
    <div class="adminzonemenu">
        <?php /*<div class="adminzonetab"><a href="#" class="ellipsis" id=""><?php echo t('Download', FALSE); ?></a></div>*/ ?>
        <div class="adminzonetab"><a href="#" class="ellipsis" id=""><?php echo t('Create Module', FALSE); ?></a></div>
    </div>
    <div class="adminzonecontent">
    <form class="form" target="formResult" method="POST">
		<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />
		<input type="hidden" name="action" value="addModule">
                <div class="placeholder">
                    <label><?php echo t('Title', FALSE); ?>: </label><input type="text" name="name_titre" id="name_titre" required>
                </div>
                <div class="placeholder">
                    <label><?php echo t('Name', FALSE); ?>: </label><input type="text" name="name_module" id="name_module" required>
                </div>
                <input type="submit" name="saveAddModule" value="<?php echo t('Save', FALSE); ?>">
	</form>
    </div>
</div>