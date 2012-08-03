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
 * @package core/blocks
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>

<script type="text/javascript" src="<?php echo BASE_PATH; ?>lib/upload/parsimonyUpload.js"></script>
<script>
    $(document).ready(function() {
        $('#images img:first').trigger('click');
        $("#droparea").parsimonyUpload({ajaxFile: "<?php echo BASE_PATH; ?>admin/action",
            ajaxFileParams: {action: "upload",path: "<?php echo PROFILE_PATH . $this->module . '/files'; ?>",MODULE: "<?php echo MODULE ?>",THEME: "<?php echo THEME ?>",THEMETYPE: "<?php echo THEMETYPE ?>",THEMEMODULE: "<?php echo THEMEMODULE ?>"},
            start:function(file){console.log("Start load : " + file.name)},
            onProgress:function(file, progress){console.log("Load:  " + file.name + " - " + progress + " %</div>")},
            stop:function(response){
                $("input[name=imgPath]").val(response.name);
                $("#preview .title").html('<span id="currentname" style="margin-left: 5px;"> <?php echo str_replace("'", "\'", t('Current Name', false)) ?> : ' + response.name + '</span>');
                var src = "<?php echo BASE_PATH . 'thumbnail?x=150&y=150&crop=1&path=' . PROFILE_PATH . $this->module . '/files'; ?>/" + response.name ;
                var srcclone = "<?php echo BASE_PATH . 'thumbnail?x=100&y=100&crop=1&path=' . PROFILE_PATH . $this->module . '/files'; ?>/" + response.name ;
                $("#preview .img").attr( 'src' ,src );
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
            }
        });
    })(jQuery);
</script>
<script>
    
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
            $('#preview img').attr('src', "<?php echo BASE_PATH . 'thumbnail?x=150&y=150&crop=1&path=' . PROFILE_PATH . $this->module ?>/files/"+imgname);
        }
       
        $("#preview .title").html('<span> <?php echo str_replace("'", "\'", t('Current Name', false)) ?> : ' + imgname + '</span>');
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
    .labels label{width: 120px;display: inline-block;text-align: left;margin: 4px 0 4px 30px;}
    .labels input:not([type=checkbox]){width:195px;}
    .title, #size{font-size: 12px;line-height: 15px;color: #27417E;}
    .title{margin: 10px auto;}
    img{border: 1px solid #CCC;}
    span.settings{padding-left: 10px;color: #276D7F;text-shadow: 0px 1px 0px white;font-size: 13px;letter-spacing: 1.5px;line-height: 21px;}
    #size{width: 100%;bottom: -15px;position: relative;}
    .template{float: left;margin: 5px;position: relative;}
    .template img{cursor: pointer}
    .deleteimg{display: none}
    #currentname{margin: 0 5px;}
    #settings {width:365px;/*border: 1px solid #CCC;background: #EDEFF4;*/margin-left: 5px;margin-top: 5px;padding: 4px 0;}
    /*#globalsettings{border: 1px solid #CCC;background: #EDEFF4;margin-left: 5px;margin-top: 5px;padding: 4px 0;}*/
    /*#specificsettings{margin: 2px 0 0 5px;margin-left: 5px;margin-top: 2px;padding: 4px 0;border: 1px solid #CCC;background: #EDEFF4;}*/
    .template:hover span.deleteimg{display: block;position: absolute;top: 0;right: 0;border: #666 solid 1px;background: url(<?php echo BASE_PATH ?>admin/img/icons_white.png) -96px -128px, #333;}
    .container{clear: both;margin-top: 10px;margin-left: 5px;border: 1px solid #DDD;-moz-border-radius: 8px;-webkit-border-radius: 8px;border-radius: 8px;background: #EDEFF4;}
    #preview{text-align: center;/*background: #D8DFEA;border: #CCC 1px solid;*/width: 300px;height:226px;margin-top: 5px;}
    #droparea{margin-top: 5px;width: 671px; height: 78px;margin: 5px;/*border: #CCC 1px solid; background: #D8DFEA;*/-moz-border-radius: 8px;-webkit-border-radius: 8px;border-radius: 8px;}
    #dropareaInner{}
    /* Overridde CSS for test */
    .gradStyle{
	border: 1px solid #ccc ;font-weight: bold;color: #383838 ;text-shadow: 0  1px  0  #ffffff ;
	background: #eee;
	background: -webkit-gradient(linear, left top, left bottom, from( #ffffff), to( #f1f1f1));
	background: -webkit-linear-gradient( #ffffff, #f1f1f1); 
	background: -moz-linear-gradient( #ffffff, #f1f1f1);
	background: -ms-linear-gradient( #ffffff, #f1f1f1);
	background: -o-linear-gradient( #ffffff, #f1f1f1);
	background: linear-gradient( #ffffff, #f1f1f1);
    }
</style>

<div class="template" id="template" style="display: none"> 
    <span onclick="$(this).parent().remove()" class="deleteimg ui-icon ui-icon-closethick"></span>
    <img title="" src="" alt="" >
    <input type="hidden" class="name" value=""/>
    <input type="hidden" class="title" value=""/>
    <input type="hidden" class="alt" value=""/>
    <input type="hidden" class="url" value=""/>
    <input type="hidden" class="description" value=""/> 
</div> 

<div>
    <div id="droparea" class="inline-block container gradStyle">
	<div style="width: 260px;text-align:center;padding-top: 5px;float: left;">
	    <label style="font-size: 18px;margin: 0px 0 0 10px;" class="ellipsis"><?php echo t('Choose Your Image', FALSE); ?></label>
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
		if (stream_resolve_include_path('core/files/' . $firstimage['name'])) {
		    echo '<span id="currentname" style="margin-left: 5px;">' . t('Current Name', false) . ' : ' . $firstimage['name'] . '</span><br>';
		}
		?></div>

            <img class="imgf" title="" style="" 
                 src="<?php echo BASE_PATH; ?>thumbnail?x=150&y=150&crop=1&path=<?php
		if ($firstimage != '') {
		    echo stream_resolve_include_path( 'core/files/' . $firstimage['name']);
		}
		?>" alt="" >

        </div>

        <div class="floatleft gradStyle" id="settings">
            <div id="globalsettings"><span class="settings"><?php echo t('Global Settings', false); ?></span><br>
                <div class="labels">
                    <label class="ellipsis" style="width: 80px;"><?php echo t('Script', false); ?> : </label>
                    <select name="script" style="width: 80px;">
                        <option value="slides">Slides</option>
                        <option value="swipe"<?php if($this->getConfig('script') == 'swipe') echo 'selected="selected"'; ?>>Swipe</option>
                       <?php /*<option value=""></option>*/ ?>
                    </select>    
                </div>
                <div class="labels" style="display: inline;">
                    <label style="width: 83px;" class="ellipsis"><?php echo t('Width', false); ?> (px): </label><input style="width: 40px;" type="text" name="width" value="<?php echo $this->getConfig('width'); ?>" />
                </div>
                <div class="labels" style="display: inline;">
                    <label style="width: 83px;margin: 4px 0 4px 10px;" class="ellipsis"><?php echo t('Height', false); ?> (px): </label><input style="width: 40px;" type="text" name="height" value="<?php echo $this->getConfig('height'); ?>" />
                </div>
            </div>
            <div id="specificsettings">
                <span class="settings"><?php echo t('Specific Settings', false); ?></span>
                <div class="labels">
                    <label class="ellipsis"><?php echo t('Title', false); ?> : </label><input id="previewtitle" type="text" value="" />
                </div>
                <div class="labels">
                    <label class="ellipsis"><?php echo t('Alternative Text', false); ?> : </label><input id="previewalt" type="text" value="" />
                </div>
                <div class="labels">
                    <label class="ellipsis"><?php echo t('URL', false); ?> : </label><input id="previewurl" type="text" value="" />
                </div>
                <div class="labels">
                    <label class="ellipsis"><?php echo t('Description', false); ?> : </label><input id="previewdescription" type="text" value="" />
                </div>
            </div>
        </div>
    </div>
    <div class="container" id="images" style="overflow: hidden;">
	<?php
	$imgs = $this->getConfig('img');
	if (!empty($imgs)) {
	    foreach ($this->getConfig('img') as $id => $image) {
                if(is_file('modules/core/files/'. $id) && !is_file(PROFILE_PATH.'core/files/'. $id)){
                    copy('modules/core/files/'. $id, PROFILE_PATH.'core/files/'. $id);
                }
		?>
		<div class="template"> 
		    <span onclick="$(this).parent().remove()" class="deleteimg ui-icon ui-icon-closethick"></span>
		    <img title="" src="<?php echo BASE_PATH ?>thumbnail?x=100&y=100&crop=1&path=<?php echo stream_resolve_include_path('core/files/'. $id) ?>" alt="">
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

