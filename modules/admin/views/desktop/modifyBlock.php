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
?>
<style>
    .tabblock{width:101px;height:18px;background-color: whitesmoke;font-weight: bold;display: inline-block;text-align: center;padding: 6px;border-right: 1px solid #D3D5DB;border-left: 1px solid white;border-bottom: 1px solid #C1C1C1;}
    .tabblock a {text-decoration:none;color:black;text-transform: capitalize;}
    .ellipsis:hover {text-overflow: inherit;overflow: visible;white-space: normal;word-wrap: break-word;background-color: white;z-index: 999;border: 1px #CCC dashed;}
</style>
<div id="addblock" class="adminzone">
    <div class="adminzonemenu">
        <div class="save"><a href="#" class="ellipsis" id="adminzone_save" onclick="$('#save_configs').trigger('click');return false;"><?php echo t('Save', FALSE); ?></a></div>
        <div class="adminzonetab firstpanel"><a href="#" class="ellipsis" id=""><?php echo t('Download', FALSE); ?></a></div>
        <div class="adminzonetab"><a href="#" class="ellipsis" id=""><?php echo t('Create Block', FALSE); ?></a></div>
    </div>

    <div class="adminzonecontent">

        <form class="form" target="ajaxhack" method="POST">              
            <input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />
            <input type="hidden" name="action" value="buildNewBlock">  
            <div id="parsitabblock" class="admintabs">  
            <div class="floatleft tabblock">
                 <a href="#" rel="img"><?php echo t('img', FALSE) ?></a>
            </div>   
                <?php
                $divtab='';
                $parsiframe='';
                $blockfiles = array('adminView' => 'adminView.php', 'block' => 'block.php', 'view' => 'view.php', 'Image' => 'img.gif');
                if (isset($_POST['name_block']) && isset($_POST['choosenmodule'])) {
                    if (is_dir( 'modules/' .$_POST['choosenmodule'] . '/blocks/' . $_POST['name_block'])) {
                        foreach (glob('modules/' . $_POST['choosenmodule'] . '/blocks/' . $_POST['name_block'] . '/*.php') as $filename) {                 
                           $name = basename($filename, '.php');
                            $divtab .= '<div class="floatleft tabblock">
                                <a href="#"  rel="' . $name . '">' . t($name, FALSE) . '</a></div>';                            
                            $parsiframe .= '<iframe class="' . $name . ' none iframeblock" src="' . BASE_PATH . 'admin/editor?file=' . $filename . '" style="width:100%;height:100%"></iframe>';
                        }
                        echo $divtab . $parsiframe;
                    }                    
                }
                ?><div id="imgblock" ondragover="return false" ondrop="drop(this, event)" class="none img iframeblock" style="width: 620px;border: 1px solid #DDD;height: 220px;-moz-border-radius: 8px;-webkit-border-radius: 8px;border-radius: 8px; background: #EDEFF4;">
                                        <div class="img inline-block" style="width: 300px; height: 130px;margin: 12px 0 0 35px;border: #CCC 1px solid;-moz-border-radius: 8px;-webkit-border-radius: 8px;border-radius: 8px; background: #D8DFEA;">
                                            <label style="font-size: 18px;margin: 5px 0 0 85px;display: inline-block;"><?php echo t('Choose Your Image', FALSE) ?></label>
                                            <input type="file" onchange="upload(this.files[0]);" style="margin: 8px 0 0 25px;">
                                            <div style="color: black;border: 4px dashed #999;font-size: 15px;-moz-border-radius: 3px;-webkit-border-radius: 3px;border-radius: 3px;text-align:center;margin: 10px;padding: 5px;">
                                                <label style="text-decoration: underline"><?php echo t('Drag n\' Drop your New Image In this Window', FALSE) ?></label>
                                            </div>
                                            <input type="hidden" name="imgPath" value="<?php if (is_file('modules/' . $_POST['choosenmodule'] . '/' . $_POST['name_block'] . '/img.gif')) {echo BASE_PATH . $this->getConfig('imgPath');} ?>">
                                        </div>
                                        <div id="preview" class="inline-block" style="width: 250px;text-align: center;margin: 7px 10px">
                                            <div class="title ellipsis" style="font-size: 12px;"><?php  if($this->getConfig('imgPath')!=''){echo 'Current Name : '. basename($this->getConfig('imgPath')) ;  echo 'size : '. filesize(BASE_PATH.$this->getConfig('imgPath')). 'ko)';}  ?></div>
                                            <img class="img" title="" src="<?php if($this->getConfig('imgPath')!=''){ echo BASE_PATH.$this->getConfig('imgPath'); }else {echo BASE_PATH.'core/blocks/gallery/img.gif';} ?>" style="width:150px;height:150px;">
                                        </div>
                  </div>
                <input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>">

            </div>    
            <script>
                  $(document).on('click','div.tabblock a',function(){
                      $('.iframeblock').hide();
                      $('.'+$(this).attr("rel")).show();
                  });
                function handleReaderLoadEnd(evt) {
                    $("#preview .img").replaceWith("<img class=\"img\" title=\"\" src=\"" + evt.target.result + "\" style=\"width:150px\">");
                    $("<img/>").attr("src", evt.target.result).load(function() {
                        $("input[name=width]").val(this.width);
                        $("input[name=height]").val(this.height);
                    });
                }
                function drop(thiss,event){
                    event.stopPropagation();
                    event.preventDefault();var files = event.dataTransfer.files;
                    var count = files.length;
                    for (var i = 0; i < count; i++) {
                        var file = files[i];
                        console.log("Processing " + file.name + " - Type : " + file.type);
                        if (file.type.match(/image.*/)){
                            var reader = new FileReader();
                            reader.onloadend = handleReaderLoadEnd;
                            reader.readAsDataURL(file);
                        }
                        upload(file);
                    }
                }
    
                function upload(file){
                    var fd = new FormData();
                    fd.append("fileField", file);
                    fd.append("MODULE", "core");
                    fd.append("THEME", "DefaultTheme");
                    fd.append("THEMETYPE", "web");
                    fd.append("THEMEMODULE", "core");
                    fd.append("action", "upload");
                    fd.append("path", "modules/core/files");
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "/admin/action");
                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            //progressBar.style.width = (evt.loaded / evt.total) * 100 + "%";
                            console.log((evt.loaded / evt.total) * 100 + "%");
                        }
                    }, false);
                    xhr.addEventListener("load", function () {
                        $("input[name=imgPath]").val(xhr.response);
                        $("#preview .title").html("Current Name : " + file.name + " (size : " + file.size/1000 + "ko)");
            
                        if (file.type.match(/image.*/)){
                            var reader = new FileReader();
                            reader.onloadend = handleReaderLoadEnd;
                            reader.readAsDataURL(file);
                        }
                    }, false);
                    xhr.send(fd);
        
                }
   
            </script>
        </form>

    </div>
</div>

                              