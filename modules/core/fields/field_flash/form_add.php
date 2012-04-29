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
 * @package core/fields
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>
<script type="text/javascript" src="<?php echo BASE_PATH; ?>lib/upload/parsimonyUpload.js"></script>
<div class="placeholder" id="upload_flash_<?php echo $this->name ?>">
    <label for="<?php echo $this->name ?>">
	<?php echo $this->label ?>
	<?php if (!empty($this->text_help)): ?>
    	<span class="tooltip ui-icon ui-icon-info" data-tooltip="<?php echo $this->text_help ?>"></span>
	<?php endif; ?>
    </label>
    <input type="file" />
    <div id="flash_thumb_<?php echo $this->name ?>" class="none">
	<div style="border:1px solid #cccccc;background-color:#EFEFEF;padding:10px;">
	    <div style="padding:5px 0;"><?php echo t('Name', FALSE) ?> : <a href="" style="text-decoration: none;width:400px;display: inline-block;" class="nameIMG ellipsis" target="_blank"></a></div>
	    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="180" height="140">
		<param name="movie" value="">
		<param name="quality" value="high">
		<embed src="" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="180" height="140"></embed>
	    </object>
	</div>
    </div>
    <input type="hidden" id="flash_<?php echo $this->name ?>" name="<?php echo $this->name ?>" />
</div>
<script LANGUAGE="JavaScript" type="text/javascript">
    $(document).ready(function(){
	$("#upload_flash_<?php echo $this->name ?>").parsimonyUpload({ajaxFile: "<?php echo BASE_PATH; ?>admin/action",
	    ajaxFileParams: {action: "upload",path: "<?php echo PROFILE_PATH . $this->module . '/' . $this->path; ?>",MODULE: "<?php echo MODULE ?>",THEME: "<?php echo THEME ?>",THEMETYPE: "<?php echo THEMETYPE ?>",THEMEMODULE: "<?php echo THEMEMODULE ?>"},
	    stop:function(response){
		$("#flash_<?php echo $this->name ?>").val(response.name);
		$("#flash_thumb_<?php echo $this->name ?>").find(".nameIMG").text(response.name);
		$("#flash_thumb_<?php echo $this->name ?>").find("a").attr("href",response.name);
		$("#flash_thumb_<?php echo $this->name ?>").find("embed").attr("src","<?php echo BASE_PATH . $this->module; ?>/<?php echo $this->path; ?>/" + response.name);
		$("#flash_thumb_<?php echo $this->name ?>").show().find('param[name="movie"]').val("<?php echo BASE_PATH.$this->module; ?>/<?php echo $this->path; ?>/" + response.name);
	    }
	});
    });
</script>