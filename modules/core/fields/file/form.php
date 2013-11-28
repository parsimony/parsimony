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

app::$request->page->addJSFile('lib/upload/parsimonyUpload.js');
echo $this->displayLabel($fieldName);
 ?>
<div class="uploadfile">
	<div id="upload_file_<?php echo $fieldName?>">
		<div style="padding:5px 0;" class="field-file-fileName<?php if(empty($value)) echo ' none'; ?>"><?php echo t('Name') ?> : <a href="<?php echo s($value) ?>" style="text-decoration: none;width:400px;display: inline-block;" class="field-file-fileNameLink ellipsis" target="_blank"><?php echo s($value) ?></a></div>
		<div class="field-file-inputContainer" style="position: relative">
			<input type="file" class="field-file-inputFile" style="position: absolute;opacity:0.0001;top:0;z-index:10;height:20px;cursor:pointer;margin: 0;" />
			<div class="field-file-inputText" style="cursor:pointer;line-height: 20px;z-index:1"><a href="#"><?php echo t('Choose an file or Drag & Drop it'); ?></a></div>
		</div>
		<input type="hidden" id="file_<?php echo $fieldName; ?>" name="<?php echo $this->name ?>" value="<?php echo s($value) ?>" />
	</div>
</div>
<script LANGUAGE="JavaScript" type="text/javascript">
	$(document).ready(function(){
		$("#upload_file_<?php echo $fieldName; ?>").parsimonyUpload({ajaxFile: "<?php echo BASE_PATH. $this->entity->getModule(); ?>/callField",
			ajaxFileParams: {
				module: "<?php echo $this->entity->getModule(); ?>", 
				entity: "<?php echo $this->entity->getName(); ?>", 
				fieldName:"<?php echo $this->name; ?>", 
				method:'upload', 
				args:''
				},
			stop:function(response){
				alert();
				$("#file_<?php echo $fieldName; ?>").val(response.name);
				var filename = $("#upload_file_<?php echo $fieldName?>");
				console.log(filename);
				$(".field-file-fileNameLink", filename).text(response.name).attr("href","<?php echo BASE_PATH .$this->entity->getModule().'/'.$this->path.'/'; ?>" + response.name);
				$(".field-file-fileName", filename).show();
			}
		});
	});
</script>