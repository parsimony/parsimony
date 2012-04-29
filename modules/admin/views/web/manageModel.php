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
<script>
    $(function() {
        $("#searchData").keyup(function(){
            if($(this).val().length > 2){
                $.post(window.location,"TOKEN=" + TOKEN + "&module=<?php echo $module ?>&entity=<?php echo $model ?>&search=" + $(this).val() + "&action=searchData",function(data){
                    $("#datagridajaxsearch").html(data);
                    $(".adminzonemenu .searchtab").trigger("click");
                });
            }
        });
        $(".pagination a").live('click',function(e){
            e.preventDefault();
            $this = $(this);
            $.post(window.location,"TOKEN=" + TOKEN + "&module=<?php echo $module ?>&entity=<?php echo $model ?>&page=" + $(this).data('page') + "&action=datagrid",function(data){
                $this.closest(".admintabs").html(data);
            });
        });
        $(".datagrid tr").die('click');
        $(".datagrid tr").live('click',function(e){
            var ide = parseInt($(".datagrid_id",this).html(),10);
            if($('#modifmodel3 > div[title="' + ide + '"]').length==0){
                $( "#modifmodel3" ).append("<div title=\"" + ide + "\" class=\"adminzonetab\"><a href=\"#tabsamodifmodel-" + ide +"\">" + '<span class="floatright ui-icon ui-icon-closethick"></span>' + $(".datagrid_title",this).text() + "</a></div>");
                if($( "#tabsamodifmodel-" + ide ).length == 0){
                    $.post(window.location,"TOKEN=" + TOKEN + "&module=<?php echo $module ?>&entity=<?php echo $model ?>&id=" + ide + "&action=getViewUpdateForm",function(data){
                        $( "#contentajax" ).append("<div id=\"tabsamodifmodel-" + ide +"\" class=\"admintabs none\">" + data + "</div>");
                    });
                }
                $('.selections_search').show();
            }
        });
        $( "#modifmodel3 > div" ).live('click',function(){
            $('#contentajax > div').hide();
            $('#tabsamodifmodel-' + $(this).attr('title')).show();
            
        });
        $('tbody tr').live('toggle',function(){           
            $(this).removeClass('selecttr');
        },function () {
            $(this).addClass('selecttr');
        });
        $(document).on('click', 'span.ui-icon-closethick',function(e){
            $(this).parent().parent().remove();
        });
        $(".adminzonetab a").live('click',function(event){
            event.preventDefault();
            if($(this).attr("href")!='#'){
                $(".adminzonecontent .admintabs").hide();
                $(".adminzonetab a").removeClass("active");
                $(this).addClass("active");
                $($(this).attr("href")).show();

            }
        });
    });
</script>
<style>
    #modifmodel3 a{line-height: 16px;padding-left: 8px;padding-right: 8px;font-size: 12px;padding-top: 2px;padding-bottom: 2px;background: none;}
    .adminzone .adminzonemenu #modifmodel3 .adminzonetab a:hover{background: none !important;}
    .adminzonetab:hover{background: #999 !important;}
    #modifmodel3 .adminzonetab:hover span.ui-icon-closethick {display: block;margin: -1px 2px 0px 0px;border: #666 solid 1px;border-radius: 5px;cursor: pointer;}
    #modifmodel3 span.ui-icon-closethick {display: none;}
</style>
<div class="adminzone">
    <div class="adminzonemenu">
        <div class="firstpanel adminzonetab"><a href="#datagridajax" class="ellipsis"><?php echo t('Data', FALSE); ?></a></div>
        <div class="adminzonetab"><a href="#addmodel" class="ellipsis"><?php echo t('Add', FALSE); ?></a></div>
        <div class="adminzonetab"><a href="#datagridajaxsearch" class="searchtab ellipsis"><?php echo t('Search', FALSE); ?></a></div>
        <input type="text" id="searchData" placeholder="<?php echo t('Search', FALSE); ?>" style="width: 130px;margin:10px 10px">
        <h2 class="selections_search none"><?php echo t('Selections', FALSE); ?></h2>
        <div id="modifmodel3">
        </div>
    </div>
    <div id="contentajax" class="adminzonecontent">
        <div id="datagridajax" class="admintabs">
            <?php
            include('modules/admin/views/web/datagrid.php');
            ?>
        </div>
        <div id="datagridajaxsearch" class="admintabs">
            <?php
            include('modules/admin/views/web/datagrid.php');
            ?>
        </div>
        <div id="addmodel" class="admintabs none">
            <?php
            echo $obj->getViewAddForm(TRUE);
            ?>
        </div>
    </div>
</div>