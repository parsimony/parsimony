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
 * @package admin
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
?>
<script>
	$(function() {
		$("#searchData").keyup(function(){
			$.post(window.location,"TOKEN=" + TOKEN + "&module=<?php echo $module ?>&entity=<?php echo $model ?>&search=" + $(this).val() + "&action=searchData",function(data){
				$(".adminzonecontent .admintabs").hide();
				$("#datagridajaxsearch").html(data).show();
			});
		});
		$(document).on('click',".pagination a",function(e){
			e.preventDefault();
			$this = $(this);
			$.post(window.location,"TOKEN=" + TOKEN + "&module=<?php echo $module ?>&entity=<?php echo $model ?>&page=" + $(this).data('page') + "&action=datagrid",function(data){
				$this.closest(".admintabs").html(data);
			});
		})
		.off('click',".datagrid tr")
		.on('click',".datagrid td.updateBTN",function(e){
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
				$('#selections_search').css("display","inline-block");
			}else{
		$('#modifmodel3 > div[title="' + ide + '"]').trigger("click");
		}
		top.ParsimonyAdmin.resizeConfBox();
		})
		.on('click',"#modifmodel3 > div", function(){
			$('#contentajax > div').hide();
			$('#tabsamodifmodel-' + $(this).attr('title')).show();
		})
		.on('click', 'span.ui-icon-closethick',function(e){
			$(this).parent().parent().remove();
		})
		.on('click',".adminzonetab a",function(event){
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
	#modifmodel3 {display:none;position: absolute;top:17px;left:0;z-index:1}
	#selections_search{position: relative;}
	#selections_search:hover #modifmodel3{display:block;}
	#modifmodel3 a{line-height: 16px;display: block;padding-left: 8px;padding-right: 25px;font-size: 12px;padding-top: 2px;padding-bottom: 2px;background: none;}
	#modifmodel3 .adminzonetab {margin:0;padding:0;height:auto;float:none}
	#modifmodel3 .adminzonetab a {position: relative;color: #555;background: #ECECEC;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;}
	#modifmodel3 .adminzonetab a:hover {background: #ccc;}
	#modifmodel3 .adminzonetab:hover span.ui-icon-closethick {display: inline-block;position: absolute;right: 0;margin: 1px -2px 0px 0px;cursor: pointer;background: url(<?php echo BASE_PATH; ?>admin/img/icons.png) -96px -128px;}
	#modifmodel3 span.ui-icon-closethick {display: none;}
	#modifmodel3 span{border-radius: 5px;cursor: pointer;background: url(<?php echo BASE_PATH; ?>admin/img/icons.png) -96px -128px;display: block;overflow: hidden;width: 16px;height: 16px;background-color: #777;}
	.updateBTN{text-align: center;width: 20px;padding:2px}
	.adminzone{background: #fefefe;padding-top: 15px;}
	.adminzonecontent{min-width:900px;bottom:0;top:60px;left:0;border-left:0;background: transparent}
	.adminzonecontent .cols {width: 100%;}
	#searchData {margin: 10px 10px;border-radius: 25px;text-shadow: none;line-height: 20px;border: none;
	background-color: #F1F1F1;text-decoration: none;color: white;font-family: inherit;font-size: 14px;width: 115px;display: inline-block;padding: 4px;text-align: center;color: #777;}
	#searchData::-webkit-input-placeholder{color : #777}
	#searchData::-moz-placeholder{color : #777}
	.adminzonetab{font-size: 20px;height: 30px;padding: 0 10px;margin: 10px;float: left;}
	.adminzonetab a {text-decoration: none;color: #777;padding: 0 10px 5px 10px;}
	#conf_box_close{border:0;background-color:transparent}
	#addmodelbtn{padding: 4px 25px;border-radius: 18px;line-height: 25px;background-color: #44C5EC;
				 display: block;color: #fff;text-align: center;text-transform: uppercase;font-size: 14px;}
</style>
<div class="adminzone">
	<div class="firstpanel adminzonetab"><a href="#datagridajax"> ☰ </a></div>
	<input type="text" id="searchData" placeholder="<?php echo t('Search'); ?> ... ">
	<div id="selections_search" class="none"><?php echo t('Selection'); ?>
		<div id="modifmodel3"></div>
	</div>
	<div id="contentajax" class="adminzonecontent">
		<div id="datagridajax" class="admintabs" style="display: block">
			<?php 
			$modifModel = TRUE;
			include('modules/admin/views/datagrid.php');
			?>
			<div class="adminzonetab" style="float: right;margin:20px"><a href="#addmodel" id="addmodelbtn"><?php echo t('Add'); ?></a></div>
		</div>
		<div id="datagridajaxsearch" class="admintabs"></div>
		<div id="addmodel" class="admintabs none">
			<?php
			echo str_replace('action=""', 'target="formResult" action=""', $obj->getViewAddForm());
			?>
		</div>
	</div>
</div>