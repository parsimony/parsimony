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
 * to contact@parsimony.mobi so we can send you a copy immediately.
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

if (is_object($block) == NULL) {
    echo t('No config for this block', false);
} else {
    ?>

<script type="text/javascript">
    $(document).ready(function() {
	$(".adminzonemenu .save a").live('click',function(event){
	    event.preventDefault();
            $('#save_configs').trigger('click');
            return false;
	});
        $('input[name="getVars"]').val($.param(window.parent.$_GET));
        $('input[name="postVars"]').val($.param(window.parent.$_POST));
    });
</script>
<div id="block_conf" class="adminzone">
    <div class="adminzonemenu">
	<div class="save"><a href="#" id="adminzone_save" class="ellipsis"><?php echo t('Save',FALSE); ?></a></div>
	<div class="firstpanel adminzonetab"><a href="#accordionBlockConfig" class="ellipsis"><?php echo t('Specific',FALSE); ?></a></div>
	<div class="adminzonetab"><a href="#accordionBlockConfigGeneral" class="ellipsis"><?php echo t('General',FALSE); ?></a></div>
    </div>
    <div class="adminzonecontent">
	<form method="POST" id="form_confs" target="ajaxhack" action="" style="height: 100%;">
	    <input type="hidden" name="action" value="save_configs" />
	    <div id="accordionBlockConfig" class="admintabs">
		    <?php
		    echo $block->getAdminView();
		    ?>
		<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />
                <input type="hidden" name="getVars" />
                <input type="hidden" name="postVars" />
		<input type="hidden" name="idBlock" value="<?php echo $_POST['idBlock']; ?>" />
		<input type="hidden" name="parentBlock" value="<?php echo $_POST['parentBlock']; ?>" />
		<input type="hidden" name="IDPage" value="<?php echo $_POST['IDPage']; ?>" />
		<input type="hidden" name="typeProgress" value="<?php echo $_POST['typeProgress']; ?>" />
		<input type="hidden" name="action" value="saveBlockConfigs" />
	    </div>
	    <div id="accordionBlockConfigGeneral" class="admintabs">
		<div class="placeholder">
		    <label> <?php echo t('Cache Seconds',FALSE); ?> </label> <input type="text" name="maxAge" value="<?php echo $block->getConfig('maxAge') ?>">
		</div>
		<div class="placeholder">
		    <label><?php echo t('Reload the page every X seconds',FALSE); ?></label> <input type="text" name="ajaxReload" value="<?php echo $block->getConfig('ajaxReload') ?>"><br />
		</div>
		<div class="placeholder">
		    <label><?php echo t('CSS Classes',FALSE); ?> </label> <input type="text" name="cssClasses" value="<?php echo $block->getConfig('cssClasses') ?>"><br />
		</div>
		<div class="placeholder">
		    <label><?php echo t('Show only in the following modules',FALSE); ?> </label> <input type="text" name="allowedModules" value="<?php echo $block->getConfig('allowedModules') ?>"><br />
		</div>
                <div  class="placeholder">
		    <label><?php echo t('HTML5 Tags',FALSE); ?> :</label> <select name="tag">
			    <?php if($block->getConfig('tag')!==false)echo '<option value="'.$block->getConfig('tag').'">'.$block->getConfig('tag').'</option>' ?>
			<option value="div">div</option>
			<option value="header">header</option>
			<option value="footer">footer</option>
			<option value="section">section</option>
			<option value="article">article</option>
			<option value="aside">aside</option>
                        <option value="hgroup">hgroup</option>
                        <option value="nav">nav</option>
		    </select>
		</div>
                <br>
		<div class="placeholder">
                    <label><?php echo t('Ajax On Page Load',FALSE); ?> :</label> <input type="hidden" name="ajaxLoad" value="0"> <input style="margin-top: 2px;margin-left: 150px;" type="checkbox" name="ajaxLoad" <?php if($block->getConfig('ajaxLoad')!==false && $block->getConfig('ajaxLoad')!=0) echo ' checked="checked"'; ?>><br />
		</div>
	    </div>
	    <input type="submit" class="none" id="save_configs" name="save_configs">
	</form>
    </div>
</div>
    <?php
}
?>