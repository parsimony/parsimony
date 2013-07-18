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
        $(document).on('click',".pagination a",function(e){
            e.preventDefault();
            $this = $(this);
            $.post(window.location,"TOKEN=" + TOKEN + "&module=<?php echo $module ?>&entity=<?php echo $model ?>&page=" + $(this).data('page') + "&action=datagrid",function(data){
                $this.closest(".admintabs").html(data);
            });
        });
        $(document).off('click',".datagrid tr");
        $(document).on('click',".datagrid td.updateBTN",function(e){
            var ide = parseInt($(".datagrid_id",$(this).parent()).html(),10);
            if($('#modifmodel3 > div[title="' + ide + '"]').length==0){
                $( "#modifmodel3" ).append("<div title=\"" + ide + "\" class=\"adminzonetab\"><a href=\"#tabsamodifmodel-" + ide +"\">" + '<span class="floatright ui-icon ui-icon-closethick"></span>' + $(".datagrid_title",$(this).parent()).text() + "</a></div>");
                if($( "#tabsamodifmodel-" + ide ).length == 0){
                    $.post(window.location,"TOKEN=" + TOKEN + "&module=<?php echo $module ?>&entity=<?php echo $model ?>&id=" + ide + "&action=getViewUpdateForm",function(data){
                        $( "#contentajax" ).append("<div id=\"tabsamodifmodel-" + ide +"\" class=\"admintabs none\">" + data + "</div>");
			$('#modifmodel3 > div[title="' + ide + '"]').trigger("click");
			top.ParsimonyAdmin.resizeConfBox();
                    });
                }
                $('.selections_search').show();
            }else{
		$('#modifmodel3 > div[title="' + ide + '"]').trigger("click");
	    }
	    top.ParsimonyAdmin.resizeConfBox();
        });
        $(document).on('click',"#modifmodel3 > div", function(){
            $('#contentajax > div').hide();
            $('#tabsamodifmodel-' + $(this).attr('title')).show();
            
        });

        $(document).on('click', 'span.ui-icon-closethick',function(e){
            $(this).parent().parent().remove();
        });
        $(document).on('click',".adminzonetab a",function(event){
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
    #modifmodel3 .adminzonetab a {color: #555;background: #ECECEC;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;}
    #modifmodel3 .adminzonetab:hover span.ui-icon-closethick {display: block;margin: 1px -2px 0px 0px;cursor: pointer;background: url(<?php echo BASE_PATH; ?>admin/img/icons.png) -96px -128px;}
    #modifmodel3 span.ui-icon-closethick {display: none;}
    #modifmodel3 span{border-radius: 5px;cursor: pointer;background: url(<?php echo BASE_PATH; ?>admin/img/icons_white.png) -96px -128px;display: block;overflow: hidden;width: 16px;height: 16px;background-color: #777;}
    .updateBTN{text-align: center;width: 20px;padding:2px}
    .adminzone{margin-bottom: 0;}
    .adminzonecontent{min-width:900px;padding-bottom:0}
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
	    $modifModel = TRUE;
            include('modules/admin/views/desktop/datagrid.php');
            ?>
        </div>
        <div id="datagridajaxsearch" class="admintabs">
            <?php
            include('modules/admin/views/desktop/datagrid.php');
            ?>
        </div>
        <div id="addmodel" class="admintabs none">
            <?php
            echo str_replace('action=""','target="formResult" action=""',$obj->getViewAddForm(TRUE));
            ?>
        </div>
    </div>
</div>