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
 * @package core/blocks
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>
<script type="text/javascript" src="<?php echo BASE_PATH; ?>lib/upload/parsimonyUpload.js"></script>
<script>
	$(document).ready(function() {
		$("#droparea").parsimonyUpload({ajaxFile: "<?php echo BASE_PATH; ?>admin/action",
			ajaxFileParams: {action: "upload",type: "image",path: "<?php echo PROFILE_PATH . $this->moduleName . '/files'; ?>",MODULE: "<?php echo MODULE ?>",THEME: "<?php echo THEME ?>",THEMETYPE: "<?php echo THEMETYPE ?>",THEMEMODULE: "<?php echo THEMEMODULE ?>"},
			start:function(file){console.log("Start load : " + file.name)},
			onProgress:function(file, progress){console.log("Load: " + file.name + " - " + progress + " %")},
			stop:function(response){
					if(typeof response.name != "undefined"){
						$("input[name=imgPath]").val(response.name);
						$("#preview .title").html('<span id="currentname"> <?php echo str_replace('\'','\\\'',t('Current Name', false)) ?> : ' + response.name + '</span>');
						var src = "<?php echo BASE_PATH . $this->moduleName . '/files'; ?>/" + response.name + "?x=150&y=150&crop=1";
						$("#preview .img").attr( 'src' ,src );
						$("span#width").text(response.x + 'px');
						$("span#height").text(response.y + 'px');
					}else{
						top.ParsimonyAdmin.execResult(response);
					}
			}
		});
	});
</script>
<style>
	#preview{width: 280px;line-height: 15px;text-align: center;margin: 7px 10px}
	#imageover {text-overflow: inherit; overflow: visible;white-space: normal;word-wrap: break-word;background-color: white;z-index: 999; border: 1px #CCC dashed;}
	#size{margin: 5px 0px;color: rgba(255, 255, 255, 0.347656);text-shadow: -2px -2px 0px #555;}
	.dragndropimage{width: 100%;border: 1px solid #ccc;height: 210px;border-radius: 8px; background: #f9f9f9;color:#222;}
	.dragndropimageInner{text-align:center;width: 300px; height: 165px;margin: 30px 0 0 35px;border: #CCC 1px solid;-moz-border-radius: 8px;-webkit-border-radius: 8px;border-radius: 8px; background: #D8DFEA;}
	.boxDropImage{border: 4px dashed #999;font-size: 15px;border-radius: 3px;text-align:center;margin: 10px;padding: 20px;}

	/* Overridde CSS for test */
	.dragndropimageInner{
	border: 1px solid #ccc ;font-weight: bold;color: #383838 ;text-shadow: 0 1px 0 #ffffff ;
	background: #eee;
	background: -webkit-gradient(linear, left top, left bottom, from( #ffffff), to( #f1f1f1));
	background: -webkit-linear-gradient( #ffffff, #f1f1f1); 
	background: -moz-linear-gradient( #ffffff, #f1f1f1);
	background: -ms-linear-gradient( #ffffff, #f1f1f1);
	background: linear-gradient( #ffffff, #f1f1f1);
	}
</style>
 
<div ondragover="return false" id="droparea">
	<div class="inline-block dragndropimage">
		<div class="img inline-block dragndropimageInner">
			<label style="font-size: 17px;margin-top: 5px;display: inline-block;"><?php echo t('Choose Your Image'); ?></label>
			<input type="file" style="margin: 15px 0 0 0px;"> 
			<div class="boxDropImage">
				<label style="font-weight: normal;"><?php echo t('Drag n\' Drop your New Image In this Window', FALSE); ?></label>
			</div>
			<input type="hidden" name="imgPath" value="<?php
				if ($this->getConfig('imgPath') != '') {
					echo basename($this->getConfig('imgPath'));
				}
				?>">
		</div>
		<div id="preview" class="inline-block">
			<div class="title ellipsis" style="font-weight: bold;font-size: 12px;height:26px;color: rgba(255, 255, 255, 0.347656);text-shadow: -2px -2px 0px #555;">
		 <?php
		 if (stream_resolve_include_path($this->getConfig('imgPath'))) {
			 echo t('Current Name',FALSE).' : ' . basename($this->getConfig('imgPath'));
		 }
		 ?></div>
			<img class="img" title="" style="" 
				 src="<?php
		 if ($this->getConfig('imgPath') != '') {
			 echo BASE_PATH . $this->getConfig('imgPath');
		 }
		 ?>?x=150&y=150&crop=1" alt="" >
			<div id="size">
<?php 
$size = array(0,0);
$pathIMG = stream_resolve_include_path($this->getConfig('imgPath'));
if($pathIMG) $size = @getimagesize($pathIMG);
echo '<label>' . t('Width') . ' : </label> <span id="width">' . $size[0] . 'px' . '</span> ; <label>' . t('Height') . ' : </label><span id="height">' .  $size[1] . 'px' . '</span>'; ?>            
			</div>
		</div>

	</div>
	<br><br>
	<h2><?php echo t('Change the image settings'); ?></h2>
	<div class="placeholder" style="display: inline-block;width:338px">
		<label><?php echo t('Width'); ?> (px): </label><input type="text" name="width" placeholder="100% by default" value="<?php if ($this->getConfig('width')) echo s($this->getConfig('width')); ?>" />
	</div>
	<div class="placeholder" style="display: inline-block;width:338px">
		<label><?php echo t('Height'); ?> (px): </label><input type="text" name="height" value="<?php if ($this->getConfig('height')) echo s($this->getConfig('height')); ?>" />
	</div>
	<div class="placeholder">
		<label><?php echo t('Title'); ?> : </label><input type="text" name="title" value="<?php if ($this->getConfig('title')) echo s($this->getConfig('title')); ?>" />
	</div>
	<div class="placeholder">
		<label><?php echo t('Alternative Text'); ?> : </label><input type="text" name="alt" value="<?php if ($this->getConfig('alt')) echo s($this->getConfig('alt')); ?>" />
	</div>
	<div class="placeholder">
		<label><?php echo t('URL'); ?> : </label><input type="text" name="url" value="<?php if ($this->getConfig('url')) echo s($this->getConfig('url')); ?>" />
	</div>
	<div>
		<label><?php echo t('Fancy Box'); ?> : </label>
		<input type="checkbox" name="fancybox" <?php
			if ($this->getConfig('fancybox') == '1') {
			echo 'checked="checked"';
			}
		?>/>
	</div>
</div>