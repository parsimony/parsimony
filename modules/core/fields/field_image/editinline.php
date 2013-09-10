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

if(!empty($this->value)):
app::$request->page->addJSFile('lib/upload/parsimonyUpload.js');
 ?>

<div class="parsieditinline" id="img_<?php echo $row->getId()->value; ?>" style="display: none" data-module="<?php echo $this->module; ?>" data-entity="<?php echo $this->entity; ?>" data-property="<?php echo $this->name; ?>" data-id="<?php echo $row->getId()->value; ?>">
<?php echo s($this->value); ?>
</div>
<div id="upload_image_<?php echo $row->getId()->value; ?>" style="position: relative;">
	<div id="image_thumb_<?php echo $row->getId()->value; ?>" class="field-image-previewContainer">
		<img src="<?php echo BASE_PATH . $this->module.'/'.$this->path.'/'.s($this->value); ?>?x=<?php echo $this->width; ?>&y=<?php echo $this->height; ?>" class="field-image-preview" />
	</div>
	<div class="field-image-inputContainer" style="position: absolute;top:0;width:30px;">
	<input type="file" class="field-image-inputFile" style="position: absolute;opacity:0.0001;top:0;z-index:10;margin: 0;width:30px;" />
		<div class="field-image-inputText" style="cursor:pointer;line-height: 20px;z-index:1;"><a href="#" style="color: white;background: #444;padding: 0px 4px;text-decoration: none;">Edit</a></div>
	</div>
	<input type="hidden" id="image_<?php echo $row->getId()->value; ?>" name="<?php echo $this->name ?>" value="<?php echo  s($this->value) ?>" />
</div>
<script LANGUAGE="JavaScript" type="text/javascript"> 
	$(document).ready(function(){
		$("#upload_image_<?php echo $row->getId()->value; ?>").parsimonyUpload({ajaxFile: "<?php echo BASE_PATH. $this->module; ?>/callField",
			ajaxFileParams: {
				module: "<?php echo $this->module; ?>", 
				entity: "<?php echo $this->entity; ?>", 
				fieldName:"<?php echo $this->name; ?>", 
				method:'upload', 
				args:''
				},
			stop:function(response){
			document.getElementById('img_<?php echo $row->getId()->value; ?>').innerHTML = response.name;
					$('#img_<?php echo $row->getId()->value; ?>').attr('data-modified','1');
					$("#image_<?php echo $row->getId()->value; ?>").val(response.name);
			var thumb = $("#image_thumb_<?php echo $row->getId()->value; ?>");
			thumb.show().find("img").attr("src","<?php echo BASE_PATH . $this->module; ?>/<?php echo $this->path; ?>/" + response.name + "?x=150&y=150");
			}
		});
	});
</script>
<?php endif; ?>