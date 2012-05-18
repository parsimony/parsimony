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
<div class="placeholder" id="upload_image_<?php echo $this->name.'_'.$row->getId()->value ?>">
    <label for="<?php echo $this->name ?>">
	<?php echo $this->label ?>
	<?php if (!empty($this->text_help)): ?>
    	<span class="tooltip ui-icon ui-icon-info" data-tooltip="<?php echo t($this->text_help) ?>"></span>
	<?php endif; ?>
    </label>
    <input type="file" />
    <div id="image_thumb_<?php echo $this->name.'_'.$row->getId()->value; ?>">
	<div style="border:1px solid #cccccc;background-color:#EFEFEF;padding:10px;">
		<div style="padding:5px 0;"><?php echo t('Name',FALSE) ?> : <a href="<?php echo s($value) ?>" style="text-decoration: none;width:400px;display: inline-block;" class="nameIMG ellipsis" target="_blank"><?php echo s($value) ?></a></div>
		<img src="<?php echo BASE_PATH; ?>thumbnail?path=<?php echo PROFILE_PATH . $this->module; ?>/<?php echo $this->path; ?>/<?php echo s($value) ?>&x=150&y=150" />
	</div>
    </div>
    <input type="hidden" id="image_<?php echo $this->name.'_'.$row->getId()->value; ?>" name="<?php echo $this->name ?>" value="<?php echo s($value) ?>" />
</div>
<script LANGUAGE="JavaScript" type="text/javascript">
    $(document).ready(function(){
	$("#upload_image_<?php echo $this->name.'_'.$row->getId()->value; ?>").parsimonyUpload({ajaxFile: "<?php echo BASE_PATH; ?>admin/action",
	    ajaxFileParams: {action: "upload",path: "<?php echo PROFILE_PATH . $this->module . '/' . $this->path; ?>",MODULE: "<?php echo MODULE ?>",THEME: "<?php echo THEME ?>",THEMETYPE: "<?php echo THEMETYPE ?>",THEMEMODULE: "<?php echo THEMEMODULE ?>"},
	    stop:function(response){
		$("#image_<?php echo $this->name.'_'.$row->getId()->value; ?>").val(response.name);
		$("#image_thumb_<?php echo $this->name.'_'.$row->getId()->value; ?>").find(".nameIMG").text(response.name);
		$("#image_thumb_<?php echo $this->name.'_'.$row->getId()->value; ?>").find("a").attr("href",response.name);
		$("#image_thumb_<?php echo $this->name.'_'.$row->getId()->value; ?>").show().find("img").attr("src","<?php echo BASE_PATH; ?>thumbnail?path=<?php echo PROFILE_PATH . $this->module; ?>/<?php echo $this->path; ?>/" + response.name + "&x=150&y=150");
	    }
	});
    });
</script>