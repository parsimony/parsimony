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
 * @package admin
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>
<div id="addblock" class="adminzone">
    <div class="adminzonemenu">
        <div class="save"><a href="#" class="ellipsis" id="adminzone_save" onclick="$('#save_configs').trigger('click');return false;"><?php echo t('Save', FALSE); ?></a></div>
        <div class="adminzonetab"><a href="#" class="ellipsis" id=""><?php echo t('Download', FALSE); ?></a></div>
        <div class="adminzonetab"><a href="#" class="ellipsis" id=""><?php echo t('Create Block', FALSE); ?></a></div>
    </div>

    <div class="adminzonecontent">

        <form class="form" target="ajaxhack" method="POST">              
            <input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />
            <input type="hidden" name="action" value="buildNewBlock">           
            <div id="labels">
                <div class="placeholder">
                    <label><?php echo t('Block Name', FALSE); ?>: </label><input type="text" id="name_block" name="name_block">
                </div>
                <div class="placeholder">
                    <label><?php echo t('Module', FALSE); ?>: </label>
                    <select id="choosenmodule" name="choosenmodule">
                        <?php
                        $modules = glob('modules/*', GLOB_ONLYDIR);
                        foreach ($modules as $filename) {
                            $moduleName = basename($filename);
                            echo '<option value="' . $moduleName . '">' . $moduleName . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <input type="submit" name="addNewBlock" id="addNewBlock" value="<?php echo t('Create Block', FALSE); ?>">
            </div>
            <script>
                $('#labels').on('click','#addNewBlock',function(){   
                    var currentTime = new Date();
                    var time = currentTime.getTime();
                    alert(time);
                } 
//                    $.post("<?php /* echo BASE_PATH; */?>admin/action", {action:"buildNewBlock",name_block:$('#name_block').val(),choosenmodule:$('#choosenmodule').val()},
//                    function(data) {  
//                        if(data=='0'){           
//                            window.parent.ParsimonyAdmin.notify("Bloc name exists","negative");
//                        }else if(data=='1'){
//                            window.parent.ParsimonyAdmin.notify("Block name : required","negative");                          
//                        }else if(data=='2'){
//                            window.parent.ParsimonyAdmin.notify("Block name : saved","positive");
//                            $('#labels').hide();
//                            $('#parsitabblock').removeClass('none');
//                            $('#parsitabblock').addClass('inline-block');
//                            $('#parsitabblock').append("<div class=\"floatleft tabblock\"><a href=\"#\" id=\"parsiaddblockclass\">Block Class</a></div><div class=\"floatleft tabblock\"><a href=\"#\" id=\"parsiaddblockadminview\">Admin View</a></div><div class=\"floatleft tabblock\"><a href=\"#\" id=\"parsiaddblockview\">View</a></div><div class=\"floatleft tabblock\"><a href=\"#\" title=\"\" id=\"parsiaddblockimage\">Image</a></div>");
//                            var obj = { 'adminView':'adminView.php', 'BlockClass':'block.php', 'View':'view.php', 'Image':'img.gif' };
//                            var modulename ='core';
//                            var blockname = 'code';
//                            var name_block =$('#name_block').val();
//                            var choosenmodule=$('#choosenmodule').val();
//                            jQuery.each(obj, function(i, val) {
//                                $('#parsitabblock').html("<div class=\"floatleft parsiblock\" style=\"width:101px;height:18px;background-color: whitesmoke;font-weight: bold;display: inline-block;text-align: center;
//                                padding: 6px;border-right: 1px solid #D3D5DB;border-left: 1px solid white;border-bottom: 1px solid #C1C1C1;\">
//                                <a href=\"#\" style=\"text-decoration:none;color:black\" title=\"\" class=\"' +blockname+ '\"> + blockname+ </a></div>");
//                                
//                            });
                            
//                        }
//                    });  
//                });
                
//                    
//                $('').on('click','a.parsiblock',function(){ 
//                   
//                });
//            $.each( { name: "John", lang: "JS" }, function(k, v){
//                alert( "Key: " + k + ", Value: " + v );
//           });
//
// 
//            $('.imgblock').on('click','.Image',function(){
//                $('.imgblock').removeClass('none');
//                $('.imgblock').addClass('inline-block');
//            });
            </script> 
                <input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>">
        </form>

    </div>
</div>
