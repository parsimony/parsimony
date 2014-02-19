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
?>

<script type="text/javascript" src="<?php echo BASE_PATH; ?>lib/upload/parsimonyUpload.js"></script>
<script>
	$(document).ready(function() {
		$('#images img:first').trigger('click');
		$("#droparea").parsimonyUpload({ajaxFile: "<?php echo BASE_PATH; ?>admin/action",
			ajaxFileParams: {action: "upload", TOKEN: "<?php echo TOKEN; ?>", path: "<?php echo PROFILE_PATH . $_POST['MODULE'] . '/files'; ?>",MODULE: "<?php echo $_POST['MODULE'] ?>",THEME: "<?php echo $_POST['THEME'] ?>",THEMETYPE: "<?php echo $_POST['THEMETYPE'] ?>",THEMEMODULE: "<?php echo $_POST['THEMEMODULE'] ?>"},
			start:function(file){console.log("Start load : " + file.name)},
			onProgress:function(file, progress){console.log("Load:  " + file.name + " - " + progress + " %</div>")},
			stop:function(response){
				if(typeof response.name != "undefined"){
				$("input[name=imgPath]").val(response.name);
				$("#preview .title").html('<span id="currentname" style="margin-left: 5px;"> <?php echo str_replace("'", "\'", t('Current Name')) ?> : ' + response.name + '</span>');
				var src = "<?php echo BASE_PATH . $_POST['MODULE'] . '/files'; ?>/" + response.name + "?x=150&y=150&crop=1" ;
				var srcclone = "<?php echo BASE_PATH . $_POST['MODULE'] . '/files'; ?>/" + response.name + "?x=100&y=100&crop=1" ;
				$("#preview .imgf").attr( 'src' ,src );
				$("span#width").text(response.x + 'px');
				$("span#height").text(response.y + 'px');
				var clone = $('#template').clone();
				$('img',clone).attr('src',srcclone);
				$(clone).removeAttr('id');
				$("#preview .img").show(); 
				$('input.name',clone).attr('name','img['+response.name+'][name]').val(response.name);
				$('input.title',clone).attr('name','img['+response.name+'][title]');
				$('input.alt',clone).attr('name','img['+response.name+'][alt]');
				$('input.url',clone).attr('name','img['+response.name+'][url]');
				$('input.description',clone).attr('name','img['+response.name+'][description]');
				$('#images').append(clone);
				$('#images img:last').trigger('click');
				$('.container .template').show();
			}else{
					top.ParsimonyAdmin.execResult(response);
				}
			}
		});
	});
	$(document).on('click','#images img' , function(event){
		event.preventDefault();
		$('.mark').removeClass('mark'); 
		$('#images img').css('border','1px solid #CCC');
		$(this).css('border', '1px solid #276D7F');
		var imgsrc = $(this).attr('src');
		var imgname = $(this).parent().find('input.name').val();
		var imgtitle = $(this).parent().find('input.title').val();
		var imgalt = $(this).parent().find('input.alt').val();
		var imgurl = $(this).parent().find('input.url').val();
		var imgdescription = $(this).parent().find('input.description').val();
		if(imgname !=''){ 
			$('#preview img').attr('src', "<?php echo BASE_PATH . $_POST['MODULE']; ?>/files/" + imgname + "?x=150&y=150&crop=1");
		}

		$("#preview .title").html('<span> <?php echo str_replace("'", "\'", t('Current Name')) ?> : ' + imgname + '</span>');
		$('#previewtitle').val(imgtitle);
		$('#previewalt').val(imgalt);
		$('#previewurl').val(imgurl);
		$('#previewdescription').val(imgdescription);
		$(this).addClass('mark');
	});
	$(document).on('change','#specificsettings input',function() { 
		var previewtitle = $('#previewtitle').val();
		var previewalt = $('#previewalt').val();
		var previewurl = $('#previewurl').val();
		var previewdescription = $('#previewdescription').val();
		$('.mark').parent().find('input.title').val(previewtitle);
		$('.mark').parent().find('input.alt').val(previewalt);
		$('.mark').parent().find('input.url').val(previewurl);
		$('.mark').parent().find('input.description').val(previewdescription);
	});
</script>
<style>
	.labels{width: 350px;}
	.labels label{width: 120px;display: inline-block;text-align: left;margin: 11px 0 4px 30px;}
	.labels input:not([type=checkbox]){width:195px;}
	.title, #size{font-size: 12px;line-height: 15px;}
	.title{margin: 10px auto;}
	img{border: 1px solid #CCC;}
	span.settings{padding-left: 10px;color: #276D7F;font-size: 13px;letter-spacing: 1.5px;line-height: 21px;}
	#size{width: 100%;bottom: -15px;position: relative;}
	.template{float: left;margin: 5px;position: relative;}
	.template img{cursor: pointer}
	.deleteimg{display: none}
	#currentname{margin: 0 5px;}
	#settings {width:365px;margin-left: 5px;margin-top: 5px;padding: 4px 0;}
	.template:hover span.deleteimg{display: block;position: absolute;top: 0;right: 0;border: #666 solid 1px;background: url(<?php echo BASE_PATH ?>admin/img/icons_white.png) -96px -128px, #333;}
	#preview{text-align: center;width: 300px;height:226px;margin-top: 5px;}
	#droparea{margin-top: 5px;width: 671px; height: 78px;margin: 5px;border-radius: 8px;}
	#dropareaInner{}
	/* Overridde CSS for test */
	.gradStyle{border: 1px solid #E7E7E7;color: #383838 ;background: #FAFAFA;}
</style>

<div class="template" id="template" style="display: none"> 
	<span onclick="$(this).parent().remove()" class="deleteimg ui-icon ui-icon-closethick"></span>
	<img>
	<input type="hidden" class="name" />
	<input type="hidden" class="title" />
	<input type="hidden" class="alt" />
	<input type="hidden" class="url" />
	<input type="hidden" class="description" /> 
</div> 

<div>
	<div id="droparea" class="inline-block container gradStyle">
		<div style="width: 260px;text-align:center;padding-top: 5px;float: left;">
			<label style="font-size: 18px;margin: 0px 0 0 10px;" class="ellipsis"><?php echo t('Choose Your Image'); ?></label>
			<input type="file" onchange="upload(this.files[0]);" style="margin: 8px 0 0 5px;"> 
		</div>
		<div style="color: #383838;border: 4px dashed #999;font-size: 15px;font-weight: normal;border-radius: 3px;text-align:center;margin-top: 15px;margin-left: 20px;padding: 14px;width: 377px;float: left;">
			<label class="ellipsis"><?php echo t('Drag n\' Drop your New Image In this Window', FALSE); ?></label>
		</div>
		<input type="hidden" name="imgPath" value="<?php echo basename($this->getConfig('imgPath')); ?>" />
	</div>
	<div style="width: 671px;margin-left: 5px;height: 240px;margin-top: 5px;">
		<div id="preview" class="inline-block floatleft gradStyle">
			<div class="title ellipsis align_center">
				<?php
				$firstimage = current($this->getConfig('img'));
				if (stream_resolve_include_path($_POST['MODULE'] . '/files/' . $firstimage['name'])) {
					echo '<span id="currentname" style="margin-left: 5px;">' . t('Current Name') . ' : ' . $firstimage['name'] . '</span><br>';
				}?>
			</div>

			<img class="imgf" src="<?php
		if ($firstimage != '') {
			echo BASE_PATH . $_POST['MODULE'] . '/files/' . $firstimage['name'];
		}
		?>?x=150&y=150&crop=1" alt="" >

		</div>

		<div class="floatleft gradStyle" id="settings">
			<div id="globalsettings"><span class="settings"><?php echo t('Global Settings'); ?></span><br>
				<div class="labels" style="display: inline;">
					<label style="width: 83px;" class="ellipsis"><?php echo t('Width'); ?> (px)</label><input style="width: 40px;vertical-align: super;" type="text" name="width" value="<?php echo $this->getConfig('width'); ?>" />
				</div>
				<div class="labels" style="display: inline;">
					<label style="width: 83px;margin: 4px 0 4px 10px;" class="ellipsis"><?php echo t('Height'); ?> (px)</label><input style="width: 40px;vertical-align: super;" type="text" name="height" value="<?php echo $this->getConfig('height'); ?>" />
				</div>
			</div>
			<div id="specificsettings">
				<span class="settings"><?php echo t('Specific Settings'); ?></span>
				<div class="labels">
					<label class="ellipsis"><?php echo t('Title'); ?></label><input id="previewtitle" type="text" />
				</div>
				<div class="labels">
					<label class="ellipsis"><?php echo t('Alternative Text'); ?></label><input id="previewalt" type="text" />
				</div>
				<div class="labels">
					<label class="ellipsis"><?php echo t('URL'); ?></label><input id="previewurl" type="text" />
				</div>
				<div class="labels">
					<label class="ellipsis"><?php echo t('Description'); ?></label><input id="previewdescription" type="text" />
				</div>
			</div>
		</div>
	</div>
	<div class="container" id="images" style="overflow: hidden;">
	<?php
	$imgs = $this->getConfig('img');
	if (!empty($imgs)) {
		foreach ($this->getConfig('img') as $id => $image) {
				/*if(is_file('modules/core/files/'. $id) && !is_file(PROFILE_PATH.'core/files/'. $id)){
					copy('modules/core/files/'. $id, PROFILE_PATH.'core/files/'. $id);
				}*/
		?>
		<div class="template"> 
			<span onclick="$(this).parent().remove()" class="deleteimg ui-icon ui-icon-closethick"></span>
			<img title="" src="<?php echo BASE_PATH . $_POST['MODULE'] . '/files/'. $id ?>?x=100&y=100&crop=1" alt="">
			<input type="hidden" name="img[<?php echo $id ?>][name]" class="name" value="<?php echo $id; ?>" />
			<input type="hidden" name="img[<?php echo $id ?>][title]" class="title" value="<?php echo $image['title']; ?>" />
			<input type="hidden" name="img[<?php echo $id ?>][alt]" class="alt" value="<?php echo $image['alt']; ?>" />
			<input type="hidden" name="img[<?php echo $id ?>][url]" class="url" value="<?php echo $image['url']; ?>" />
			<input type="hidden" name="img[<?php echo $id ?>][description]" class="description" value="<?php echo $image['description']; ?>" /> 
		</div>
		<?php }
	}
	?>
	</div> 
</div>

