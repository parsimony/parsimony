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

/* In case the file isn't in PROFILES/ */

if(!is_file(PROFILE_PATH.$this->getConfig('pathOfViewFile')) && is_file('modules/'.$this->getConfig('pathOfViewFile'))){
    \tools::createDirectory(dirname(PROFILE_PATH.$this->getConfig('pathOfViewFile')));
    copy('modules/'.$this->getConfig('pathOfViewFile'), PROFILE_PATH.$this->getConfig('pathOfViewFile'));
}
$this->pathOfViewFile = PROFILE_PATH . $this->getConfig('pathOfViewFile');
if (!file_exists($this->pathOfViewFile))
    tools::createDirectory(dirname($this->pathOfViewFile));
if (!file_exists($this->pathOfViewFile))
    $this->generateViewAction(array());
$view = $this->getConfig('view');
?>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.js" type="text/javascript"></script>
<script>typeof jQuery.ui != 'undefined' || document.write('<script src="<?php echo BASE_PATH; ?>lib/jquery-ui/jquery-ui-1.8.23.min.js"><\/script>')</script>
<script type="text/javascript" src="<?php echo BASE_PATH; ?>lib/jsPlumb/jquery.jsPlumb-1.3.16-all-min.js"></script>
<style>
    .adminzonecontent{min-width:1340px}
    .tabs{min-width: 1000px;}
    .innerTabs ul li a {padding: 0 7px;line-height: 23px;}
    .queryblock{margin:1px 1px;border-radius:3px;padding:1px;border: 1px solid #ccc ;font-weight: bold;color: #383838 ;text-shadow: 0 1px 0 #ffffff ;background: #F7F7F7;}
    ._jsPlumb_endpoint{cursor: pointer;z-index: 50}
    ._jsPlumb_connector{cursor: pointer;}
    .property{padding: 0 5px;cursor:pointer;line-height: 16px;font-family: sans-serif;font-size: 11px;}
    .property:hover{background:#CBDDF3}
    .caption{box-shadow: 2px 0 2px #CCC;width: 72px;position: absolute;left: -5px;background: white;z-index: 100;float: left;}
    .caption div{line-height: 25px;padding-left: 5px;border-bottom: #EFEFEF 1px solid;font-weight: bold;letter-spacing: 1.2px;}
    #recipiant_sql{width: 10000px;padding-left: 70px;}
    #recipiant_sql .property{font-weight: normal;padding:5px;width: 130px;background: transparent;border: none;box-shadow:initial;}
    #recipiant_sql input.property{pointer-events: none}
    #recipiant_sql .table{background: transparent;border: none;box-shadow:initial;}
    #recipiant_sql .display{text-align:center;top: -2px;}
    #recipiant_sql .selector{width:100%}
    #recipiant_sql select{width:100%}
    #recipiant_sql .where input{width:110px}
    #schema_sql{position:absolute;height:300px;width: 185px;z-index: 999;display:none;background-color: rgb(245, 245, 245);}
    .schemasql{color:#383838;letter-spacing: 1.1px;padding-top: 3px;}
    .schemasql a{text-decoration: none;color: #333;font-weight: bolder;text-transform: capitalize;padding-left: 4px;}
    .schemasql .tableCont{border-radius: 3px;background: rgb(255, 255, 255);border: 1px solid rgb(211, 211, 211);margin:2px 2px;cursor:pointer;}
    .schemasql .tableCont .table{padding:4px;}
    .schemasql .tableCont .property{display:none;}
     
    #queryCanvas .menuh{overflow-x: scroll;border: 1px solid #5E9AE2;text-align: left;width: 100%;display:none;position:absolute;top:32px;z-index:8000;background:rgba(255,255,255,0.8);left: 0px;}
    #queryCanvas .tableCont{position: absolute;border-radius: 3px;background:#E8F4FF;border:1px solid #5E9AE2;margin:2px 2px;}
    #queryCanvas .tableCont .table{padding:5px 4px;line-height: 20px;font-weight: bold;color: white;background: #5E9AE2;
				 background: -webkit-gradient(linear, left top, left bottom, from(#5E9AE2), to(#3570B8));
				 background: -moz-linear-gradient(top, #5E9AE2, #3570B8);}

    #recipiant_sql select{margin-bottom: 5px;margin-top: 5px;}
    .datagrid{padding-top:5px}
    .tabsadmin{width: 42%;text-align: center;margin-left: 2%;}
    
    .textdbquery{font-size: 12px;letter-spacing: 1px;line-height: 20px;background:#eee;z-index: 999;position:relative;padding: 4px;}
    #resultpreview .pagination{display:none}
    #recipiant_sql_cont{position:relative;width: 1200px;overflow-x: auto;padding: 0px 0px;background: white;margin: 6px 2px 0 0;min-height:182px}
    .aggregate,.aggregate{width:100%}
    h3{color: #2E63A5;padding: 7px 0;}
    #recipiant_sql input[type="text"],#recipiant_sql input[type="password"] {padding: 1px 3px 3px 2px;}
    a{text-decoration: none;}
    .propertyJoin{width:199px;line-height:25px;font-weight: bold;font-family: sans-serif}
    .propertyJoinLeft{text-align: right;padding-right: 7px}
    .propertyJoinRight{text-align: left;padding-left: 7px}
    .bloctitle .property{color:#fff}
    .bloctitle{border-radius: 3px;background: #5E9AE2;
	       background: -webkit-gradient(linear, left top, left bottom, from(#5E9AE2), to(#3570B8));
	       background: -moz-linear-gradient(top, #5E9AE2, #3570B8);}
    .bloctitle input {text-shadow: none;font-size: 13px;}
    input.filter,input.sort{margin:3px 0}
    #linksWrapper{padding:5px;border-bottom:1px solid #ddd;border-left:1px solid #ddd;position: absolute;right:0;width:525px;background: #fff;display:none;z-index: 999;}
    #links{margin-bottom:15px;padding-left: 22px;}
    #links select {width: 85px;padding: 0;position: relative;top: 3px;}
    .linkDef {cursor:move;}
    .linkDef.ok {background:rgba(189, 255, 207,0.2);}
    .linkDef.ko {background:rgba(255, 176, 176,0.2);}
    .linkDef:hover{position:relative;background:#f1f1f1}
    .linkDef .deletator{display:block;cursor:pointer;background-image: url(<?php echo BASE_PATH; ?>admin/img/icons.png);}
    .linkDef #invertRelation{display:block}
    #addTable,#manageLinks{position:absolute;font-size:40px;cursor:pointer;}
    #manageLinks{right:10px}
    #invertRelation{display:none;cursor:pointer;background-image: url(<?php echo BASE_PATH; ?>admin/img/icons.png);background-position: -64px -80px;position: absolute;right: 20px;top: 2px;}
    #addTable:hover,#manageLinks:hover{position:absolute;font-size:40px;cursor:pointer;color:#999}
    .labelConnectorsd:hover{background:#eee;cursor:pointer}
    .inaccessible{opacity:0.1}
    .accessible{opacity:0.5}
    .deletator{display:none;cursor: pointer;position:absolute;top:2px;right:0px;color:#fff;background-image: url(<?php echo BASE_PATH; ?>admin/img/icons_white.png);}
    .deletator2{cursor: pointer;position:absolute;top:2px;right:0px;color:#fff;}
    .tableCont .deletator{display:block;}
    #generatedsql{display:none;margin:5px;padding:5px;border-radius:4px;border:#ccc 1px solid;line-height: 20px;}
    .removeButton{border-radius: 5px;cursor: pointer;background: url(<?php echo BASE_PATH; ?>admin/img/icons_white.png) -96px -128px; whiteSmoke;display: block;overflow: hidden;width: 16px;height: 16px;}
</style>
<div class="tabs">
    <ul>
        <li class="active"><a href="#tabs-admin-query"><?php echo t('Query Editor', FALSE); ?></a></li>
        <li><a href="#tabs-admin-template"><?php echo t('View', FALSE); ?></a></li>
    </ul>
    <div class="clearboth panel" id="tabs-admin-query">
         <div id="queryCanvasWrapper" style="position: relative;height:320px;background:  url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAMAAAC67D+PAAAADFBMVEXx9vnw9fj+/v7///+vmeNIAAAAKklEQVQIHQXBAQEAAAjDoHn6dxaqrqpqAAWwMrZRs8EKAzWAshkUDIoZPCvPAOPf77MtAAAAAElFTkSuQmCC');overflow:hidden">
            <div id="addTable" class="tooltip" data-tooltip="<?php echo t('Add a table', FALSE); ?>" data-pos="e" onclick="$('#schema_sql').show()">+</div>
            <div id="manageLinks" class="tooltip" data-tooltip="<?php echo t('Relations', FALSE); ?>" data-pos="w" onclick="$('#linksWrapper').show()">∞</div>
            <span id="deletator" class="ui-icon ui-icon-closethick deletator"></span>
            <span id="invertRelation" class="ui-icon ui-icon-refresh"></span>
            <div id="schema_sql" style="overflow-y: auto;height:100%">
                <?php
                $aliasClasses = array_flip(\app::$aliasClasses);
                foreach (\app::$activeModules as $module => $mode) :
                    $models = \app::getModule($module)->getModel();
                    if (count($models) > 0) :
                        ?>
                        <div class="schemasql ellipsis">
                            <a href="#" onclick="return false"><?php echo $module; ?></a>
                            <div class="menuh">
                                <?php foreach ($models as $model => $entity) : ?>
                                <div class="tableCont" table="<?php echo $module . '_' . $model; ?>">
                                    <div class="table ellipsis"><?php echo $model; ?></div>
                                        <?php
                                        $obj = app::getModule($module)->getEntity($model);
                                        foreach ($obj->getFields() AS $field) :
                                                if (get_class($field) == 'core\fields\field_foreignkey')
                                                    $link = ' link="' . $field->moduleLink . '_' . $field->link . '"';
                                                elseif (get_class($field) == 'core\fields\field_user') 
                                                    $link = ' link="core_user"';
                                                else
                                                    $link = '';
                                                ?>
                                            <div class="property <?php echo $aliasClasses[get_class($field)]; ?>"<?php echo $link; ?>><?php echo $field->name; ?></div>
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
            <div id="linksWrapper">
                <div class="deletator2 ui-icon ui-icon-closethick " onclick="$('#linksWrapper').hide();"></div>
                <span style="display: block;padding: 3px 0 14px 20px;background: url(<?php echo BASE_PATH; ?>admin/img/puce.png) no-repeat;font-weight: bold;color: #333;"> <?php echo t('Relationship Management', FALSE); ?></span>
                <ol id="links"></ol>
            </div>
            <div id="queryCanvas" style="width:100%;height:100%"></div>
        </div>
        <div class="innerTabs" style="margin-top:10px">
            <ul>
                <li class="active"><a href="#tabs-criterias"><?php echo t('Criterias', FALSE); ?></a></li>
                <li><a href="#tabs-result"><?php echo t('Result', FALSE); ?></a></li>
            </ul>
            <div class="innerPanel" id="tabs-criterias">
                <div id="pattern_sql" class="queryblock floatleft none">
                    <a href="#" onclick="$(this).parent('.queryblock').remove();$('#generate_query').trigger('click');" class="floatright">
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
                    <div class="align_center" style="padding:4px 0px 2px;"><input class="display" type="checkbox" checked="checked"></div>
                    <div style="padding: 3px 0;"><input class="where" type="text"></div>
                    <div><input class="or" type="text"></div>
                    <div class="align_center"><input class="filter" type="checkbox" checked="checked"></div>
                    <div class="align_center"><input class="sort" type="checkbox" checked="checked"></div>
                </div>         
                <div id="form" action="" style="clear: both;position: relative">
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
                        <div id="recipiant_sql"></div>
                    </div>
                    <input type="button" class="none clearboth" id="generate_query" value="<?php echo t('Generate', FALSE) . ' '; ?>">
                    <div class="clearboth textdbquery">
                        <div style="display:inline-block;width:200px">
                            <?php echo t('Active Pagination', FALSE); ?> : <input type="hidden" value="0" name="pagination" /><input type="checkbox" id="pagination" name="pagination" value="1" <?php
                            if ($this->getConfig('pagination') == 1)
                                echo ' checked="checked"';
                            ?> />
                        </div>
                        <div style="display:inline-block;width:315px">
                            <?php echo t('This block shows at most', FALSE) . ' '; ?> <input type="text" style="line-height: 15px;height: 17px;width: 28px;padding: 0 0 0 5px;" name="nbitem" id="nbitem"  value="<?php echo $this->getConfig('nbitem') ?>" /><?php echo ' ' . t('items', FALSE); ?><br>
                        </div>
                        <div style="display:inline-block;width:110px">
                            <?php echo t('Filters', FALSE); ?> : <input type="hidden" value="0" name="filter" /><input type="checkbox" id="filter" name="filter" value="1" <?php
                            if ($this->getConfig('filter') == 1)
                                echo ' checked="checked"';
                            ?> />
                        </div>
                        <div style="display:inline-block;">
                            <?php echo t('Sort', FALSE); ?> : <input type="hidden" value="0" name="sort" /><input type="checkbox" id="sort" name="sort" value="1" <?php
                            if ($this->getConfig('sort') == 1)
                                echo ' checked="checked"';
                            ?> />
                        </div>
                    </div>
                    <br>
                </div>
            </div>
            <div id="tabs-result" class="innerPanel" style="display:none">
                <div style="padding: 1px 20px 10px;box-shadow: #777 1px 1px 4px;">
                    <div style="position:relative;text-align:right;padding:7px"><a href="#" style="color: rgb(0, 136, 213)" onclick="$('#generatedsql').slideToggle();return false;"><?php echo t('View SQL query', FALSE); ?></a></div>
                    <div id="resultpreview">
                        <?php
                        if (is_object($view)) {
                            $sql = $view->getSQL();
                            $search  = array('select ', ' from ', ' where ', ' order by ', ' group by ', ' limit ');
                            $replace = array('<span style="font-weight:bold">SELECT</span> ', '<br><span style="font-weight:bold">FROM</span> ', '<br><span style="font-weight:bold">WHERE</span> ', '<br><span style="font-weight:bold">ORDER BY</span> ','<br><span style="font-weight:bold">GROUP BY</span> ', '<br><span style="font-weight:bold">LIMIT</span> ');
                            echo '<div id="generatedsql">'.str_replace($search,$replace,$sql['query']).'</div>';
                            $obj = $view;
                            if ($this->getConfig('pagination') != 1)
                                $obj->limit(10);
                            include('modules/admin/views/desktop/datagrid.php');
                        }
                        ?>
                    </div>
                </div>
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
	include('modules/admin/views/desktop/editor.php');
        ?>
    </div>
</div>
<script>
    function putLink(sourceTable,sourceProperty,targetTable,targetProperty,type) {
	$("<li class=\"linkDef\"><input type=\"hidden\" name=\"relations[" + sourceTable + "_" + targetTable + "][propertyLeft]\" value=\"" + sourceTable + "." + sourceProperty + "\"><div class=\"propertyJoin propertyJoinLeft inline-block align_right\">" + sourceTable + "." + sourceProperty + "</div><select name=\"relations[" + sourceTable + "_" + targetTable + "][type]\"><option>" + type + "</option><option>inner join</option><option>join</option><option>left join</option><option>left outer join</option><option>right join</option><option>right outer join</option></select><div class=\"propertyJoin propertyJoinRight inline-block\">" + targetTable + "." +  targetProperty + "</div><input type=\"hidden\" name=\"relations[" + sourceTable + "_" + targetTable + "][propertyRight]\" value=\"" + targetTable + "." +  targetProperty + "\"></li>").appendTo("#links");
        $( "#links" ).sortable( "refresh" );
        checkRelations();
    }
    function manageFilters() {
        if($('#filter').is(':checked')) $('.filter').show();
        else $('.filter').hide();
        if($('#sort').is(':checked') ) $('.sort').show();
        else $('.sort').hide();
    }

    function checkRelations() {
        var tables = Array();
        var state = true;
        $('.linkDef').each(function(i){
            var tableLeft = $(".propertyJoinLeft",this).text().split(".")[0];
            var tableRight = $(".propertyJoinRight",this).text().split(".")[0];
             $(this).removeClass("ok ko");
            if(i == 0){
                tables.push(tableLeft);
                tables.push(tableRight);
                $(this).addClass("ok");
            }else{
                if(state){
                    if(tables.indexOf(tableLeft) > -1 && tables.indexOf(tableRight) == -1){
                        tables.push(tableRight);
                        $(this).addClass("ok");
                    }else{
                        $(this).addClass("ko");
                        state = false;
                    }
                }else{
                    $(this).addClass("ko");
                }
            }
        });
    }
    
    function filterTables(){
        if($('#queryCanvas .tableCont').length == 0) {
            $("#schema_sql .tableCont").removeClass("accessible inaccessible");
        }else{
            $("#schema_sql .tableCont").removeClass("accessible").addClass("inaccessible");
        }
        $('#queryCanvas .tableCont').each(function(){
            var table = $(this).attr("table");
            $('#schema_sql .property[link="' + table + '"]').each(function(i){
                $(this).parent().removeClass("inaccessible").addClass("accessible");
            });
            $('#schema_sql .tableCont[table="' + table + '"] .property[link]').each(function(i){
                $('#schema_sql .tableCont[table="' + $(this).attr("link") + '"]').removeClass("inaccessible").addClass("accessible");
            });
        });
         $('#queryCanvas .tableCont').each(function(){
             $('#schema_sql .tableCont[table="' + $(this).attr("table") + '"]').removeClass("accessible inaccessible");
         });
    }
    
    function addTable(tableName, top, left) {
        var table = $('#schema_sql .tableCont[table="' + tableName + '"]').clone();
        table.append('<input type="hidden" class="top" name="tables[' + tableName + '][top]" value="' + top + '">');
        table.append('<input type="hidden" class="left" name="tables[' + tableName + '][left]" value="' + left + '">');
        var tableID = "table_" + tableName;
        if($("#" + tableID ,$("#queryCanvas")).length == 0){
            table.css({left: left + "px",top: top + "px"}).removeClass("accessible inaccessible");
            table.attr('id',tableID);
            $(".property",table).each(function(){
                this.id = tableID + this.textContent;
            });
            $("#queryCanvas").append(table);
            $("#schema_sql").hide();

            filterTables();
            draw();
        }
    }
    
    function addProperty(propELMT, tableName, tableProperty, display, aggregate, where, or, order, filter, sort) {
        var sqlscheme = $("#pattern_sql").clone();
        sqlscheme.attr("id","");
        var nameProp = tableName + "_" + tableProperty;
        $(".table",sqlscheme).attr('value',tableName);
        $(".table",sqlscheme).attr('name','properties[' + nameProp + '][table]');
        $(sqlscheme).attr('property',tableName + "_" + tableProperty);
        $(".property",sqlscheme).val(tableProperty);
        $(".property",sqlscheme).attr('name','properties[' + nameProp + '][property]');
        $(".display",sqlscheme).attr('name','properties[' + nameProp + '][display]')[0].checked = display;
        $(".aggregate",sqlscheme).attr('name','properties[' + nameProp + '][aggregate]').val(aggregate);
        $(".where",sqlscheme).attr('name','properties[' + nameProp + '][where]').val(where);
        if(typeof propELMT == "object"){
            /* Proposal for where */
            var prop = tableName + "." + tableProperty;
            if(typeof propParams[prop] != "undefined"){
                if(confirm(t("Your SQL property \"" + tableProperty + "\" matches the current dynamic page parameter (" + propParams[prop].replace(":","") + ").\n Do you want to filter SQL records with the value of $_GET['" + propParams[prop].replace(":","") + "']? \n e.g. where "+ prop +"= $_GET['" + propParams[prop].replace(":","") + "']" ))){
                    $(".where",sqlscheme).val("= " + propParams[prop]);
                }
            }
            if($(propELMT).hasClass("field_user") || nameProp == "core_user_id_user"){
                if(confirm(t("Your SQL property \"" + tableProperty + "\" matches a User ID.\n Do you want to filter SQL records with the current user $_SESSION['id_user']? \n e.g. where "+ prop +"= $_SESSION['id_user']" ))){
                    $(".where",sqlscheme).val("= :id_user");
                }
            }
        }
        $(".or",sqlscheme).attr('name','properties[' + nameProp + '][or]').val(or);
        $(".order",sqlscheme).attr('name','properties[' + nameProp + '][order]').val(order);
        $(".filter",sqlscheme).attr('name','properties[' + nameProp + '][filter]')[0].checked = filter;
        $(".sort",sqlscheme).attr('name','properties[' + nameProp + '][sort]')[0].checked = sort;
        sqlscheme.appendTo("#recipiant_sql").slideDown();
    }

    $("select option").each(function(){
	if(!this.value.length){
	    $(this).text('Default');
	    this.value = "";
	};
    });
    
    $('#links').on("change","select",function() {
       draw(); 
    });
    
    $('#queryCanvas').on('click','#deletator',function(){
        obj = $(this).parent();
        if(confirm(t('Are you sure to delete this entity ?'))){
            $(this).appendTo($('body'));
            obj.remove();
            filterTables();
            draw();
        }
    });
    
    $('#links').on('click','#deletator',function(){
        var parent = $(this).parent();
        $(this).appendTo($('body'));
        $('#invertRelation').appendTo($('body'));
        parent.remove();
        filterTables();
        checkRelations();
        draw();
    })
    .on('click','#invertRelation',function(){
        var parent = $(this).parent();
        $(this).appendTo($('body'));
        var left = $(".propertyJoinLeft",parent).text();
        var right = $(".propertyJoinRight",parent).text();
        var tableLeft = left.split(".")[0];
        var tableRight = right.split(".")[0];
        $(".propertyJoinLeft",parent).text(right);
        $(".propertyJoinRight",parent).text(left);
        $("input",parent).remove();
        $('<input type="hidden" name="relations[' + tableRight + '_' + tableLeft + '][propertyLeft]" value="' + right + '">').appendTo(parent);
        $('<input type="hidden" name="relations[' + tableRight + '_' + tableLeft + '][propertyRight]" value="' + left + '">').appendTo(parent);
        var select = $("select",parent).attr("name","relations[" + tableRight + "_" + tableLeft + "][type]");
        checkRelations();
        draw();
        $("#generate_query").trigger("click");
    });
                
    $('#queryCanvas').on("click",".property",function() {
	if($(".queryblock[property=" + $(this).parent().attr('table') + "_" + $(this).text().trim() + "]").length==0){
	    addProperty(this, $(this).parent().attr('table'), $(this).text().trim(), true, "", "", "", "", true, true);
	    $("#generate_query").trigger("click");
	}
    });
    
    $(document).on("change","#form input,#form select",function() {
	if($('#pagination').is(':checked') && $('#nbitem').val().length==0) $('#nbitem').val(10);
        manageFilters();
	$("#generate_query").trigger("click");
    });
    
    $('#generate_query').click(function() {
	$.post(BASE_PATH+'admin/datagridPreview',$('form').serialize(),function(data){
	    $("#resultpreview").html(data);
	});
	if($("#regenerateview").is(":checked")){
	    $.post(BASE_PATH + 'core/callBlock',{module:"<?php $mod = $_POST['typeProgress']=='theme' ? THEMEMODULE : MODULE; echo $mod; ?>", idPage:"<?php if($_POST['typeProgress']=='page') echo $_POST['IDPage']; ?>",theme: "<?php if($_POST['typeProgress']=='theme') echo THEME; ?>", id:"<?php echo $_POST['idBlock']; ?>", method:'generateView', args:$('form input[name^="properties"]').add('form input[name^="pagination"]').add('form input[name="filter"]').add('form input[name="sort"]').serialize()},function(data){
		codeEditor.setValue(data);
		$("#regenerateview").attr("checked","checked");
		codeEditor.refresh();
	    });
	}
    });
    
     /* JsPlumb */
    jsPlumb.importDefaults({     
        Container : $("#queryCanvas"),
        DragOptions : { cursor: "pointer", zIndex:2000 }
    });

    $(document).ready(function() {
        $( "#links" ).sortable({ update:function(){$("#generate_query").trigger("click");} });
	$('#tabs-admin-template').css('height','0px').css('overflow','hidden');
	$(".tabs").on('click'," > ul a",function(e){
	    e.preventDefault();
	    $(".panel").hide();
	    $(".tabs > ul .active").removeClass("active");
	    $(this).parent().addClass("active");
	    $($(this).attr('href')).show();
	    $($(this).attr('href')).css('height','100%').css('overflow','inherit');
	});

        $(".innerTabs").on('click'," > ul a",function(e){
	    e.preventDefault();
	    $(".innerPanel").hide();
	    $(".innerTabs ul .active").removeClass("active");
	    $(this).parent().addClass("active");
	    $($(this).attr('href')).show();
	    $($(this).attr('href')).css('height','100%').css('overflow','inherit');
	});

        $(".schemasql").on('click',".tableCont",function(e){
            if($(this).hasClass("inaccessible")){
                alert("You have no relation for this table");
                return false;
            }
            var table = $(this).clone();
            addTable( $(this).attr("table"), 50, 230);
	});
        
                    
        $("#links").on('mouseover mouseout','.linkDef',function(event) {
            var deletator = document.getElementById("deletator");
            var invert = document.getElementById("invertRelation");
            if (event.type == 'mouseover') { console.log("uyu");
                deletator.style.display = "block";
                invert.style.display = "block";
                this.insertBefore( deletator, this.firstChild);
                this.insertBefore( invert, this.firstChild);
            } else {
                deletator.style.display = "none";
                invert.style.display = "none";
            }
        });

        $("#queryCanvas").on('mouseover mouseout','.tableCont',function(event) {
            var deletator = document.getElementById("deletator");
            if (event.type == 'mouseover') {
                deletator.style.display = "block";
                this.insertBefore( deletator, this.firstChild);
            } else {
                deletator.style.display = "none";
            }
        });

        manageFilters();
        
         <?php
         /* Add relations  */
        if(is_object($view)){ 
            $sql = $view->getSQL();
            if (!empty($sql['joins'])) {
                foreach ($sql['joins'] as $join) {
                    list($table1, $idTableLeft) = explode('.', $join['propertyLeft']);
                    list($table2, $idTableRight) = explode('.', $join['propertyRight']);
                    ?>
                     putLink("<?php echo $table1; ?>","<?php echo $idTableLeft; ?>","<?php echo $table2; ?>","<?php echo $idTableRight; ?>","<?php echo $join['type']; ?>")
                    <?php
                }
            }
        }

        /* Add tables on canvas */
        $tables = $this->getConfig('tables');
        if(!empty($tables)){
            foreach($tables AS $tableName => $table){
                echo 'addTable( "'.$tableName.'", '.$table['top'].', '.$table['left'].'); ';
            }
        }
        ?>
        window.propParams = new Array();
        <?php
        /* Save params of the page for future proposals */
        if($_POST['typeProgress'] == 'page'){
             $page = \app::getModule(MODULE)->getPage($_POST['IDPage']);
             foreach($page->getURLcomponents() AS $urlRegex){
                 if(isset($urlRegex['modelProperty'])){
                     ?>
                     propParams["<?php echo $urlRegex['modelProperty']; ?>"] = ":<?php echo $urlRegex['name']; ?>";
                     <?php
                 }
             }
         }

        /* Display saved properties */
        $tab_selected = $this->getConfig('selected');
        if (!empty($tab_selected)) {
            foreach ($tab_selected AS $selected) {
                ?>
                   addProperty("", "<?php echo $selected['table']; ?>", "<?php echo $selected['property']; ?>", <?php echo (isset($selected['display']) ? 'true' : 'false') ?>, "<?php echo $selected['aggregate'] ?>", "<?php echo $selected['where'] ?>", "<?php echo $selected['or'] ?>", "<?php echo $selected['order'] ?>", "<?php echo (isset($selected['filter']) ? 'true' : 'false') ?>", "<?php echo (isset($selected['sort']) ? 'true' : 'false') ?>");
                <?php
            }
        }
        ?>
	//$("#generate_query").trigger("click");
    });
    
    function draw(){
        /* Draw connectors */
        jsPlumb.reset();

        $('.property[link]',$('#queryCanvas')).each(function(i){
            var sourceTable = $(this).closest(".tableCont").attr("table");
            var targetTable = $(this).attr("link");
            var idTargetTable = "table_" + targetTable;
            if($("#" + idTargetTable).length > 0) {
                var typeRelation = $('select[name="relations\\[' + sourceTable + '_' + targetTable + '\\]\\[type\\]"]').val() || $('select[name="relations\\[' + targetTable + '_' + sourceTable + '\\]\\[type\\]"]').val() || " ";
                if(typeRelation.length > 1){
                    var paintStyle = { lineWidth:2,strokeStyle:"#666"};
                }else{
                    typeRelation = "unactive";
                    var paintStyle = { lineWidth:2,strokeStyle:"#ddd",dashstyle:"4 1"};
                }
                jsPlumb.connect({source:this, 
                    target:$("#" + idTargetTable + " .field_ident")[0],
                    endpoint:[ "Dot", { radius:3 } ],
                    connector:[ "Bezier", { curviness:100 } ],
                    detachable:false,
                    paintStyle:paintStyle,
                    hoverPaintStyle:{ strokeStyle:"rgb(106, 180, 71)" },
                    overlays : [ ["Label", {cssClass:"labelConnectors",
                                            label : typeRelation, 
                                            location:0.7,
                                            events:{"click":function(label, evt) {
                                                            $("#linksWrapper").show();
                                                    }
                                            }
                                      } ],
                            ],
                    anchor:["LeftMiddle","RightMiddle"]});
            }
            jsPlumb.draggable($("#queryCanvas .tableCont"),{
                cursor: 'move',
                handle : '.table',
                containment: '#queryCanvas',
                drag:function(){
                    jsPlumb.repaint( $(".property",this).add(this).toArray());
                },
                stop:function(){
                    $(".top",this).val(parseInt($(this).css("top")));
                    $(".left",this).val(parseInt($(this).css("left")));
                }
            });

        });
        jsPlumb.bind("click", function(connection, originalEvent) {
                var sourceTable = $("#" + connection.sourceId).closest(".tableCont").attr("table");
                var sourceProperty =  $("#" + connection.sourceId).text();

                var targetTable = $("#" + connection.targetId).closest(".tableCont").attr("table");
                var targetProperty = $("#" + connection.targetId).text();
                
                var relation = $('select[name="relations\\[' + sourceTable + '_' + targetTable + '\\]\\[type\\]"]');
                var typeRelation = relation.val() || "";
                if(typeRelation.length > 0){
                    if(confirm("Would you really want to delete this relation ?")) relation.parent().remove();
                }else{
                    putLink(sourceTable,sourceProperty,targetTable,targetProperty,"inner join");
                    $("#generate_query").trigger("click");
                }
                draw();
                $("#linksWrapper").show();
            });
    }
    function editorChange(){
	$("#regenerateview").removeAttr("checked");
    }
    
  
</script>
<style>
.adminzonecontent{min-width:1200px}
</style>