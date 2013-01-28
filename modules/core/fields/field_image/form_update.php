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
 * @package core/fields
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/* todo remove style attribute */
app::$request->page->addJSFile(BASE_PATH . 'lib/upload/parsimonyUpload.js');
$id = $this->name.'_'.$row->getId()->value;
 ?>
<div class="placeholder" id="upload_image_<?php echo $id?>">
    <label for="<?php echo $this->name ?>" style="position: relative;display: inline-block;">
	<?php echo $this->label ?>
	<?php if (!empty($this->text_help)): ?>
    	<span class="tooltip ui-icon ui-icon-info" data-tooltip="<?php echo t($this->text_help) ?>"></span>
	<?php endif; ?>
    </label>
    
    <div id="image_thumb_<?php echo $id; ?>" class="field-image-previewContainer<?php if(empty($this->value)) echo ' none'; ?>" style="border:1px solid #cccccc;background-color:#EFEFEF;padding:10px;">
	    <div style="padding:5px 0;" class="field-image-fileName"><?php echo t('Name',FALSE) ?> : <a href="<?php echo s($value) ?>" style="text-decoration: none;width:400px;display: inline-block;" class="field-image-fileNameLink ellipsis" target="_blank"><?php echo s($value) ?></a></div>
	    <img src="<?php echo BASE_PATH; ?>thumbnail?path=<?php echo PROFILE_PATH . $this->module; ?>/<?php echo $this->path; ?>/<?php echo s($value) ?>&x=150&y=150" class="field-image-preview" />
    </div>
    <div class="field-image-inputContainer" style="position: relative">
	<input type="file" class="field-image-inputFile" style="position: absolute;opacity:0.0001;top:0;z-index:10;height:20px;cursor:pointer;margin: 0;" />
	<div class="field-image-inputText" style="cursor:pointer;line-height: 20px;z-index:1"><a href="#"><?php echo t('Choose an image or Drag & Drop it'); ?></a></div>
    </div>
    <input type="hidden" id="image_<?php echo $id; ?>" name="<?php echo $this->name ?>" value="<?php echo s($value) ?>" />
</div>
<script LANGUAGE="JavaScript" type="text/javascript">
    $(document).ready(function(){
	$("#upload_image_<?php echo $id; ?>").parsimonyUpload({ajaxFile: "<?php echo BASE_PATH; ?>admin/action",
	    ajaxFileParams: {action: "upload",path: "<?php echo PROFILE_PATH . $this->module . '/' . $this->path; ?>",MODULE: "<?php echo MODULE ?>",THEME: "<?php echo THEME ?>",THEMETYPE: "<?php echo THEMETYPE ?>",THEMEMODULE: "<?php echo THEMEMODULE ?>"},
	    stop:function(response){
		$("#image_<?php echo $id; ?>").val(response.name);
		var thumb = $("#image_thumb_<?php echo $id; ?>");
		$(".field-image-fileNameLink",thumb).text(response.name).attr("href",response.name);
		thumb.show().find("img").attr("src","<?php echo BASE_PATH; ?>thumbnail?path=<?php echo PROFILE_PATH . $this->module; ?>/<?php echo $this->path; ?>/" + response.name + "&x=150&y=150");
	    }
	});
    });
</script>