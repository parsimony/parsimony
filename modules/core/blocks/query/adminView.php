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
$this->pathOfViewFile = PROFILE_PATH .$this->getConfig('pathOfViewFile');
if (!file_exists($this->pathOfViewFile))
    tools::createDirectory(dirname($this->pathOfViewFile));
if (!file_exists($this->pathOfViewFile))
    $this->generateView(array());
$view = $this->getConfig('view');
?>
<style>
    .tabs{max-width: 1000px;}
    .queryblock{margin:1px 1px;border-radius:3px;padding:1px;
	border: 1px solid #ccc ;font-weight: bold;color: #383838 ;text-shadow: 0  1px  0  #ffffff ;
	background: #eee;
	background: -webkit-gradient(linear, left top, left bottom, from( #f2f2f2), to( #ddd));
	background: -webkit-linear-gradient( #f2f2f2, #ddd); 
	background: -moz-linear-gradient( #f2f2f2, #ddd);
	background: -ms-linear-gradient( #f2f2f2, #ddd);
	background: -o-linear-gradient( #f2f2f2, #ddd);
	background: linear-gradient( #f2f2f2, #ddd);
    }
    .property{padding: 0 4px;cursor:pointer;line-height: 20px;font-family: sans-serif}
    .property:hover{background:#CBDDF3}
    .caption{width:65px;position: absolute;left:0px;background:white;z-index: 100;float:left;}
    .caption div{line-height: 27px;padding-left: 7px;border-bottom: #EFEFEF 1px solid;font-weight: bold;letter-spacing: 1.2px;}
    #recipiant_sql{width: 10000px;padding-left: 70px;}
    #recipiant_sql .property{font-weight: normal;padding:5px;width: 135px;background: transparent;border: none;box-shadow:initial;}
    #recipiant_sql .table{background: transparent;border: none;box-shadow:initial;}
    #recipiant_sql .display{text-align:center;top: -2px;}
    #recipiant_sql .selector{width:100%}
    #recipiant_sql select{width:100%}
    #recipiant_sql .where input{width:110px}
    #schema_sql{position:relative;}
    .schemasql{color:#383838;border: 1px solid #99BBE8;letter-spacing: 1.1px;box-shadow: #F4F8FD 0 1px 0px 0 inset;background-color: #CBDDF3;width: 100px;line-height: 30px;text-align: center;}
    .schemasql a{text-decoration: none;color: #333;font-weight: bolder;text-transform: capitalize;padding-left: 4px;}
    .schemasql .menuh{overflow-x: scroll;border: 1px solid #5E9AE2;text-align: left;width: 100%;display:none;position:absolute;top:32px;z-index:8000;background:rgba(255,255,255,0.8);left: 0px;}
    .schemasql .tableCont{border-radius: 3px;background:#E8F4FF;border:1px solid #5E9AE2;width:97px;margin:2px 2px;}
    .schemasql .tableCont .table{padding:5px 4px;line-height: 20px;font-weight: bold;color: white;background: #5E9AE2;
				 background: -webkit-gradient(linear, left top, left bottom, from(#5E9AE2), to(#3570B8));
				 background: -moz-linear-gradient(top, #5E9AE2, #3570B8);}
    #recipiant_sql select{margin-bottom: 5px;margin-top: 5px;}
    .datagrid{padding-top:5px}
    .tabsadmin{width: 42%;text-align: center;margin-left: 2%;}
    #links{margin-bottom:15px;}
    #textdbquery{padding: 12px 0px 0px 50px;font-size: 15px;letter-spacing: 1px;line-height: 20px;}
    #resultpreview .pagination{display:none}
    #recipiant_sql_cont{position:relative;width: 100%;overflow-x: scroll;padding: 4px 0px;background: white;margin-top: 5px;min-height:217px}
    .aggregate,.aggregate{width:100%}
    h3{color: #2E63A5;padding: 7px 0;}
    #recipiant_sql input[type="text"],#recipiant_sql input[type="password"] {padding:3px}
    a{text-decoration: none;}
    .propertyJoin{width:250px;line-height:25px;font-weight: bold;font-family: sans-serif}
    .propertyJoinLeft{text-align: right;padding-right: 7px}
    .propertyJoinRight{text-align: left;padding-left: 7px}
    .bloctitle .property{color:#fff}
    .bloctitle{border-radius: 3px;background: #5E9AE2;
	       background: -webkit-gradient(linear, left top, left bottom, from(#5E9AE2), to(#3570B8));
	       background: -moz-linear-gradient(top, #5E9AE2, #3570B8);}
    input.filter,input.sort{margin:3px 0}
    .removeButton{border-radius: 5px;cursor: pointer;background: url(<?php echo BASE_PATH; ?>admin/img/icons_white.png) -96px -128px; whiteSmoke;display: block;overflow: hidden;width: 16px;height: 16px;}
</style>
<div class="tabs">
    <ul>
        <li class="active"><a href="#tabs-admin-query"><?php echo t('Query Editor', FALSE); ?></a></li>
        <li><a href="#tabs-admin-template"><?php echo t('View', FALSE); ?></a></li>
    </ul>
    <div class="clearboth panel" id="tabs-admin-query">
        <div style="padding: 5px;line-height: 28px;color: #666;text-shadow: white 0 1px 0;font-size: 14px;letter-spacing: 1.2px;font-weight: bold;"><?php echo t('Select Properties below', FALSE); ?></div>
        <div id="schema_sql">
	    <?php
	    $aliasClasses = array_flip(\app::$aliasClasses);
	    foreach (\app::$activeModules as $module => $mode) :
		$models = \app::getModule($module)->getModel();
		if (count($models) > 0) :
		    ?>
		    <div class="floatleft schemasql ellipsis">
			<a href="#" onclick="return false"><?php echo $module; ?></a>
			<div class="menuh">
			    <?php foreach ($models as $model => $entity) : ?>
	    		    <div class="tableCont inline-block" table="<?php echo $module . '_' . $model; ?>">
	    			<div class="table ellipsis"><?php echo $model; ?></div>
				    <?php
				    $obj = app::getModule($module)->getEntity($model);
				    foreach ($obj->getFields() AS $field) :
					    if (get_class($field) == 'core\fields\field_foreignkey')
						$link = ' link="' . $module . '_' . $field->link . '"';
                                            elseif (get_class($field) == 'core\fields\field_user') 
                                                $link = ' link="core_user"';
					    else
						$link = '';
					    ?>
		    			<div class="ellipsis property <?php echo $aliasClasses[get_class($field)]; ?>"<?php echo $link; ?>><?php echo $field->name; ?></div>
					    <?php
				    endforeach;
				    ?>
	    		    </div>
			    <?php endforeach; ?>
			</div>
		    </div>
		    <?php
		endif;
	    endforeach;
	    ?>
	</div>
	<div id="pattern_sql" class="queryblock floatleft none">
	    <a href="#" onclick="$(this).parent('.queryblock').remove();generateLinks();$('#generate_query').trigger('click');" class="floatright">
		<span class="removeButton"></span>
	    </a>
	    <div class="bloctitle"><input class="property" type="text" value=""></div>
	    <div><input class="table" type="text" value=""></div>
	    <div class="sqlselect">
		<select class="aggregate">
		    <option value=""></option>
		    <option value="groupby"><?php echo t('GROUP', FALSE); ?></option>
		    <option value="avg"><?php echo t('AVG', FALSE); ?></option>
		    <option value="count"><?php echo t('COUNT', FALSE); ?></option>
		    <option value="max"><?php echo t('MAX', FALSE); ?></option>
		    <option value="min"><?php echo t('MIN', FALSE); ?></option>
		    <option value="sum"><?php echo t('SUM', FALSE); ?></option>
		</select>
	    </div>
	    <div>
		<select class="order">
		    <option value=""></option>
		    <option value="asc"><?php echo t('Ascending', FALSE); ?></option>
		    <option value="desc"><?php echo t('Descending', FALSE); ?></option>
		</select>
	    </div>
	    <div class="align_center"><input  class="display" type="checkbox" checked="checked"></div>
	    <div><input class="where" type="text"></div>
	    <div><input class="or" type="text"></div>
            <div class="align_center"><input class="filter" type="checkbox" checked="checked"></div>
            <div class="align_center"><input class="sort" type="checkbox" checked="checked"></div>
	</div>         
	<div class="clearboth"></div>
	<div id="form" action="" style="position: relative">
	    <div class="caption">
		<div><?php echo t('Property', FALSE); ?></div>
		<div><?php echo t('Entity', FALSE); ?></div>
		<div><?php echo t('Total', FALSE); ?></div>
		<div><?php echo t('Order', FALSE); ?></div>
		<div><?php echo t('Display', FALSE); ?></div>
		<div><?php echo t('Criteria', FALSE); ?></div>
		<div><?php echo t('Or', FALSE); ?></div>
                <div class="filter"><?php echo t('Filter', FALSE); ?></div>
                <div class="sort"><?php echo t('Sort', FALSE); ?></div>
	    </div>
	    <div id="recipiant_sql_cont" class="fs">
		<div id="recipiant_sql">
		    <?php
		    $tab_selected = $this->getConfig('selected');
		    if (!empty($tab_selected)) {
			foreach ($tab_selected AS $selected) {
			    ?>
			    <div class="queryblock floatleft" property="<?php echo $selected['table'] . '_' . $selected['property']; ?>">
				<a href="#" onclick="$(this).parent('.queryblock').remove();generateLinks();$('#generate_query').trigger('click');" class="floatright">
				    <span class="removeButton"></span>
				</a>
				<div class="bloctitle"><input type="text" class="property" value="<?php echo $selected['property']; ?>" name="properties[<?php echo $selected['table'] . '_' . $selected['property']; ?>][property]"></div>
				<div><input type="text" class="table" name="properties[<?php echo $selected['table'] . '_' . $selected['property']; ?>][table]" value="<?php echo $selected['table']; ?>"></div>

				<div>
				    <select class="aggregate" name="properties[<?php echo $selected['table'] . '_' . $selected['property']; ?>][aggregate]" style="width:100%">
					<option></option> 
					<option value="groupby" <?php
		    if ($selected['aggregate'] == 'groupby') {
			echo 'selected="selected"';
		    }
			    ?>>GROUP</option>
					<option value="avg" <?php
					if ($selected['aggregate'] == 'avg') {
					    echo 'selected="selected"';
					}
			    ?>>AVG</option>
					<option value="count" <?php
					if ($selected['aggregate'] == 'count') {
					    echo 'selected="selected"';
					}
			    ?>>COUNT</option>
					<option value="max" <?php
					if ($selected['aggregate'] == 'max') {
					    echo 'selected="selected"';
					}
			    ?>>MAX</option>
					<option value="min" <?php
					if ($selected['aggregate'] == 'min') {
					    echo 'selected="selected"';
					}
			    ?>>MIN</option>
					<option value="sum" <?php
					if ($selected['aggregate'] == 'sum') {
					    echo 'selected="selected"';
					}
			    ?>>SUM</option>
				    </select>
				</div>
				<div>
				    <select class="order" name="properties[<?php echo $selected['table'] . '_' . $selected['property']; ?>][order]">
					<option></option>
					<option value="asc" <?php
					if ($selected['order'] == 'asc') {
					    echo 'selected="selected"';
					}
			    ?>><?php echo t('Ascending', FALSE); ?></option>
					<option value="desc" <?php
					if ($selected['order'] == 'desc') {
					    echo 'selected="selected"';
					}
			    ?>><?php echo t('Descending', FALSE); ?></option>
				    </select>
				</div>
				<div class="align_center"><input type="checkbox" name="properties[<?php echo $selected['table'] . '_' . $selected['property']; ?>][display]" class="display" <?php
					if (isset($selected['display']) && $selected['display']) {
					    echo ' checked="checked"';
					}
			    ?>></div>
				<div><input type="text" class="where" name="properties[<?php echo $selected['table'] . '_' . $selected['property']; ?>][where]" value="<?php echo s($selected['where']); ?>"></div>
				<div><input type="text" class="or" name="properties[<?php echo $selected['table'] . '_' . $selected['property']; ?>][or]" value="<?php echo s($selected['or']); ?>"></div>
                                <div class="align_center"><input type="checkbox" name="properties[<?php echo $selected['table'] . '_' . $selected['property']; ?>][filter]" class="filter" <?php
					if (isset($selected['filter']) && $selected['filter']) {
					    echo ' checked="checked"';
					}
			    ?>></div>
                                <div class="align_center"><input type="checkbox" name="properties[<?php echo $selected['table'] . '_' . $selected['property']; ?>][sort]" class="sort" <?php
					if (isset($selected['sort']) && $selected['sort']) {
					    echo ' checked="checked"';
					}
			    ?>></div>
			    </div><?php
						     }
						 }
		    ?>
		</div>
	    </div>
	    <div class="clearboth"></div>
	    <input type="button" class="none" id="generate_query" value="<?php echo t('Generate', FALSE) . ' '; ?>">
	    <div class="clearboth" id="textdbquery">
		<div style="display:inline-block;width:300px">
		    <?php echo t('Active Pagination', FALSE); ?> : <input type="hidden" value="0" name="pagination" /><input type="checkbox" id="pagination" name="pagination" value="1" <?php
		    if ($this->getConfig('pagination') == 1)
			echo ' checked="checked"';
		    ?> />
		</div>
		<div style="display:inline;width:300px">
		    <?php echo t('Site Pages show at most', FALSE) . ' '; ?> <input type="text" style="width:40px;" name="nbitem" id="nbitem"  value="<?php echo $this->getConfig('nbitem') ?>" /><?php echo ' ' . t('items', FALSE); ?><br>
		</div>
	    </div>
            <div class="clearboth" id="textdbquery">
		<div style="display:inline-block;width:300px">
		    <?php echo t('Active Filters', FALSE); ?> : <input type="hidden" value="0" name="filter" /><input type="checkbox" id="filter" name="filter" value="1" <?php
		    if ($this->getConfig('filter') == 1)
			echo ' checked="checked"';
		    ?> />
		</div>
		<div style="display:inline-block;width:300px">
		    <?php echo t('Active Sort', FALSE); ?> : <input type="hidden" value="0" name="sort" /><input type="checkbox" id="sort" name="sort" value="1" <?php
		    if ($this->getConfig('sort') == 1)
			echo ' checked="checked"';
		    ?> />
		</div>
	    </div>
	    <br>
	    <a href="" style="display: block;margin-bottom: 15px;font-weight: bold;color: #333;" onclick="$('#links').slideToggle();return false;"> > <?php echo t('Relationship Management', FALSE); ?></a>
	    <div id="links" class="none">
		<?php
                if(is_object($view)){ 
                    $sql = $view->getSQL();
                    if (!empty($sql['joins'])) {
                        foreach ($sql['joins'] as $join) {
                            list($table1, $idTableLeft) = explode('.', $join['propertyLeft']);
                            list($table2, $idTableRight) = explode('.', $join['propertyRight']);
                            ?>
                            <div>
                                <input type="hidden" name="relations[<?php echo $table1 . '_' . $table2; ?>][propertyLeft]" value="<?php echo $table1 . '.' . $idTableLeft; ?>">
                                <div class="propertyJoin propertyJoinLeft inline-block">
                                    <?php echo $table1 . ' . ' . $idTableLeft; ?>
                                </div>
                                <select name="relations[<?php echo $table1 . '_' . $table2; ?>][type]">
                                    <option><?php echo $join['type']; ?></option>
                                    <option>inner join</option>
                                    <option>join</option>
                                    <option>left join</option>
                                    <option>left outer join</option>
                                    <option>right join</option>
                                    <option>right outer join</option>
                                </select>
                                <div class="propertyJoin propertyJoinRight inline-block">
                                    <?php echo $table2 . ' . ' . $idTableRight; ?>
                                </div>
                                <input type="hidden" name="relations[<?php echo $table1 . '_' . $table2; ?>][propertyRight]" value="<?php echo $table2 . '.' . $idTableRight; ?>">
                            </div>
                            <?php
                        }
                    }
                }
		?>
	    </div>
	    <div id="linkstransit" class="none"></div>
	</div>
	<div style="padding: 1px 20px 10px;box-shadow: rgb(119, 119, 119) 2px 1px 13px;">
	    <h3><?php echo t('Result Preview', FALSE); ?></h3><br>
	    <div id="resultpreview">
		<?php
		if (is_object($view)) {
		    $obj = $view;
		    if ($this->getConfig('pagination') != 1)
			$obj->limit(10);
		    include('modules/admin/views/web/datagrid.php');
		}
		?>
	    </div>
	</div>
    </div>
    <div id="tabs-admin-template" class="panel" style="padding:0px">
	<div style="padding:9px 0">
	    <?php echo t('Regenerate the view', FALSE); ?> ? <input type="hidden" value="0" name="regenerateview" /><input type="checkbox" id="regenerateview" name="regenerateview" value="1" <?php
	    if ($this->getConfig('regenerateview') == 1)
		echo ' checked="checked"';
	    ?> />
	</div>
	<?php
	$path = $this->pathOfViewFile;
	include('modules/admin/views/web/editor.php');
	?>
    </div>
</div>
<script>
    function putLink(table1,table2,type) {
	if($('#schema_sql div[table="' + table2 +  '"] div[link="' + table1 + '"]').text().length == 0){
	    var idTableRight = $("#schema_sql div[table=" + table2 + "]").find(".field_ident").text();
	    var idTableLeft = $('#schema_sql div[table="' + table1 +  '"] div[link="' + table2 + '"]').text();
	}else{
	    var idTableLeft = $("#schema_sql div[table=" + table1 + "]").find(".field_ident").text();
	    var idTableRight = $('#schema_sql div[table="' + table2 +  '"] div[link="' + table1 + '"]').text();
	}
	$("<div><input type=\"hidden\" name=\"relations[" + table1 + "_" + table2 + "][propertyLeft]\" value=\"" + table1 + "." + idTableLeft + "\"><div class=\"propertyJoin propertyJoinLeft inline-block align_right\">" + table1 + "." + idTableLeft + "</div><select name=\"relations[" + table1 + "_" + table2 + "][type]\"><option>" + type + "</option><option>inner join</option><option>join</option><option>left join</option><option>left outer join</option><option>right join</option><option>right outer join</option></select><div class=\"propertyJoin propertyJoinRight inline-block\">" + table2 + "." +  idTableRight + "</div><input type=\"hidden\" name=\"relations[" + table1 + "_" + table2 + "][propertyRight]\" value=\"" + table2 + "." +  idTableRight + "\"></div>").appendTo("#linkstransit");

    }
    function manageFilters() {
        if($('#filter').is(':checked')) $('.filter').show();
        else $('.filter').hide();
        if($('#sort').is(':checked') ) $('.sort').show();
        else $('.sort').hide();
    }
    
    function generateLinks() {
	var tables = [], links = [], tableCount = [];
	$('#recipiant_sql .queryblock').each(function(){
	    var table = $(".table",this).val();
	    if($.inArray(table, tables) == -1){
		tables.push(table);
	    }
	});
	$("#schema_sql .property[link]").each(function(i){
	    var table1 = $(this).parent().attr("table");
	    var table2 = $(this).attr('link');
	    if($.inArray(table1, tables) != -1 && $.inArray(table2, tables) != -1){//si ce n'est pas un link de table d'asso'
		if(table1 != table2) {
		    links.push(table2 + "=>" + table1);
		    //tableCount[table2] = (tableCount[table2] || 0) + 1;
		}
	    }else if($.inArray(table1, tables) != -1 || $.inArray(table2, tables) != -1){ // si c'est in link de table d'asso'
		var tableProv = Array();
		$('.property[link]', $(this).parent()).each(function(i){
		    var table2 = $(this).attr('link');
		    if($.inArray(table2, tables) != -1) tableProv.push(table1 + "=>" + table2);
		});
		if(tableProv.length >= 2){
		    $.each( tableProv, function(i, val){
			if($.inArray(val, links) == -1){
			    links.push(val);
			    var cut = val.split("=>");
			    //tableCount[cut[1]] = (tableCount[cut[1]] || 0) + 1;
			}
		    });
		}
	    }
	});

	var alreadyIncluded = [];
	var cpt = 0;
	while( Object.keys(links).length != 0 ){
	    for(var key in links){
		var myTables = links[key].split("=>");
		var type="inner join"
		    
		if(cpt == 0 || ($.inArray(myTables[0], alreadyIncluded) != -1 && $.inArray(myTables[1], alreadyIncluded) == -1)){
		    var linkbefore = $('#links [name="relations[' + myTables[0] + '_' + myTables[1] + '][type]"]');
		    if(linkbefore.length > 0) type = linkbefore.val();
		    putLink(myTables[0],myTables[1],type);
		    delete links[key];
		    if(cpt == 0) alreadyIncluded.push(myTables[0]);
		    alreadyIncluded.push(myTables[1]);
		    cpt++;
		}else if($.inArray(myTables[1], alreadyIncluded) != -1 && $.inArray(myTables[0], alreadyIncluded) == -1){
		    var linkbefore = $('#links [name="relations[' + myTables[1] + '_' + myTables[0] + '][type]"]');
		    if(linkbefore.length > 0) type = linkbefore.val();
		    putLink(myTables[1],myTables[0],type);
		    delete links[key];
		    alreadyIncluded.push(myTables[0]);
		    cpt++; 
		}  
	    }
	}
	$("#links").html($("#linkstransit").html());
	$("#linkstransit").empty();
	
	$("#schema_sql .tableCont").hide();
	$('#recipiant_sql .queryblock').each(function(){
	    var table = $(".table",this).val();
	    $('#schema_sql .tableCont[table="' + table + '"]').show();
	    $('.property[link="' + table + '"]').each(function(i){
		$(this).parent().show();
		$('.property[link]',$(this).parent()).each(function(i){
		    $('#schema_sql .tableCont[table="' + $(this).attr("link") + '"]').show();
		});
	    });
	});
    };
	
    $("select option").each(function(){
	if(!$(this).val().length){
	    $(this).text('Default');
	    $(this).val('');
	};
    });
                
    $('#schema_sql .property').click(function() {
	if($(".queryblock[property=" + $(this).parent().attr('table') + "_" + $(this).text() + "]").length==0){
	    var sqlscheme = $("#pattern_sql").clone();
	    sqlscheme.attr("id","");
	    var nameProp = $(this).parent().attr('table') + "_" + $(this).text();
	    $(".table",sqlscheme).attr('value',$(this).parent().attr('table'));
	    $(".table",sqlscheme).attr('name','properties[' + nameProp + '][table]');
	    $(sqlscheme).attr('property',$(this).parent().attr('table') + "_" + $(this).text());
	    $(".property",sqlscheme).val($(this).text());
	    $(".property",sqlscheme).attr('name','properties[' + nameProp + '][property]');
	    $(".display",sqlscheme).attr('name','properties[' + nameProp + '][display]');
	    $(".aggregate",sqlscheme).attr('name','properties[' + nameProp + '][aggregate]');
	    $(".where",sqlscheme).attr('name','properties[' + nameProp + '][where]');
	    $(".or",sqlscheme).attr('name','properties[' + nameProp + '][or]');
	    $(".order",sqlscheme).attr('name','properties[' + nameProp + '][order]');
            $(".filter",sqlscheme).attr('name','properties[' + nameProp + '][filter]');
            $(".sort",sqlscheme).attr('name','properties[' + nameProp + '][sort]');
	    sqlscheme.appendTo("#recipiant_sql").slideDown();
	    $("#generate_query").trigger("click");
	}
    });
    $(document).on("change","#form input,#form select",function() {
	if($('#pagination').is(':checked') && $('#nbitem').val().length==0) $('#nbitem').val(10);
        manageFilters();
	$("#generate_query").trigger("click");
    });
    $('#generate_query').click(function() {
	generateLinks();
	$.post(BASE_PATH+'admin/datagridPreview',$('form').serialize(),function(data){
	    $("#resultpreview").html(data);
	});
	if($("#regenerateview").is(":checked")){
	    $.post(BASE_PATH+'core/callBlock',{name:"query",method:'generateView',args:$('form input').serialize()},function(data){
		editor.setValue(data);
		$("#regenerateview").attr("checked","checked");
		editor.refresh();
	    });
	}
    });
    $(document).ready(function() {
	$('#tabs-admin-template').css('height','0px').css('overflow','hidden');
	$(document).on('click',".tabs li a",function(e){
	    e.preventDefault();
	    $(".panel").hide();
	    $(".tabs ul .active").removeClass("active");
	    $(this).parent().addClass("active");
	    $($(this).attr('href')).show();
	    $($(this).attr('href')).css('height','100%').css('overflow','inherit');
	});
	$("#schema_sql > div").hover(function() {
	    $("a",this).next().show();
	},function() {
	    $("a",this).next().hide();
	});
        manageFilters();
	//$("#generate_query").trigger("click");
    });
    function editorChange(){
	$("#regenerateview").removeAttr("checked");
    }
</script>