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

/* In case the file isn't in PROFILES/ */
$viewPath = $this->getConfig('viewPath');
if (!is_file(PROFILE_PATH . $viewPath) && is_file('modules/' . $viewPath)) {
	\tools::createDirectory(dirname(PROFILE_PATH . $viewPath));
	copy('modules/' . $viewPath, PROFILE_PATH . $viewPath);
}
$this->viewPath = PROFILE_PATH . $this->getConfig('viewPath');
if (!file_exists($this->viewPath))
    tools::createDirectory(dirname($this->viewPath));
if (!file_exists($this->viewPath))
    $this->generateViewAction(array());
$view = $this->getConfig('view');
?>
<script src="<?php echo BASE_PATH; ?>lib/jquery-ui/jquery-ui-1.10.3.min.js"></script>
<script type="text/javascript" src="<?php echo BASE_PATH; ?>lib/jsPlumb/jquery.jsPlumb-1.3.16-all-min.js"></script>
<style>
	.adminzonecontent{min-width:1340px}
	.queryblock:hover .removeButton{display:block}
	.tabs{min-width: 1000px;}
	.queryblock{position: relative;box-shadow: 1px 1px 1px #e7e7e7;font-weight: bold;color: #383838;text-shadow: 0 1px 0 #ffffff;background: #fefefe;padding-bottom: 10px;margin-right: 5px;margin-left: 5px;}
	.queryblock a{top: 2px;right: 2px;position: absolute;z-index: 2;}
	._jsPlumb_endpoint{cursor: pointer;z-index: 50}
	._jsPlumb_connector{cursor: pointer;}
	.property{padding: 0 5px;cursor: pointer;line-height: 16px;font-family: sans-serif;font-size: 11px;border-bottom: dotted #ddd 1px;font-weight: normal;}
	.property:hover{background-color: rgb(231,242,255);}
	.caption{position: absolute;z-index: 100;padding: 10px 3px;background: #FAFAFA;}
	.caption div{line-height: 28px;letter-spacing: 1.2px;}
	#recipiant_sql{width: 10000px;padding-left: 70px;}
	#recipiant_sql .property{font-weight: normal;padding:5px;width: 130px;background: transparent;border: none;box-shadow:initial;}
	#recipiant_sql .table{background: transparent;border: none;box-shadow:initial;}
	#recipiant_sql .selector{width:100%}
	#recipiant_sql select{width: 100%;margin-top: 5px;background-color: #fafafa;}
	#recipiant_sql .where input{width:110px}
	#recipiant_sql input[type="checkbox"]{margin-bottom: 1px;margin-top: 4px;}
	 #recipiant_sql textarea, #recipiant_sql input[type="text"],  #recipiant_sql input[type="password"], #recipiant_sql input[type="file"], #recipiant_sql select{border: 1px solid transparent !important;}
	#schema_sql{position:absolute;height:300px;width: 185px;z-index: 999;display:none;background-color: rgb(245, 245, 245);}
	.schemasql{color:#383838;letter-spacing: 1.1px;padding-top: 3px;}
	.schemasql a{text-decoration: none;color: #333;font-weight: bolder;text-transform: capitalize;padding-left: 4px;}
	.schemasql .tableCont{border-radius: 3px;background: rgb(255, 255, 255);border: 1px solid rgb(211, 211, 211);margin:2px 2px;cursor:pointer;}
	.schemasql .tableCont .table{padding:4px;}
	.schemasql .tableCont .property{display:none;}
	#queryCanvas .menuh{overflow-x: scroll;border: 1px solid #5E9AE2;text-align: left;width: 100%;display:none;position:absolute;top:32px;z-index:8000;background:rgba(255,255,255,0.8);left: 0px;}
	#queryCanvas .tableCont {margin: 2px 2px;z-index: 60;position: absolute;color: #484848;line-height: 18px;cursor: pointer;font-size: 15px;background-color: white;font-weight: bold;border-radius: 3px;box-shadow: #666 0px 1px 3px;background: #fbfbfb;}
	#queryCanvas .tableCont .table{font-weight: bold;font-size: 12px;padding:5px 4px;color: white;background: #1b74a4;border-top-left-radius: 3px;border-top-right-radius: 3px;text-align: center;}
	.datagrid{padding-top:5px}
	.tabsadmin{width: 42%;text-align: center;margin-left: 2%;}
	.textdbquery{font-size: 12px;letter-spacing: 1px;line-height: 20px;z-index: 999;position:relative;padding: 4px;}
	.textdbquery input[type="checkbox"]{position:relative;top:4px}
	#resultpreview .pagination{display:none}
	#recipiant_sql_cont{position: relative;width: 1200px;overflow-x: auto;background: #fafafa;min-height: 222px;padding: 10px 0;}
	.aggregate,.aggregate{width:100%}
	#accordionBlockConfig h3{color: #2E63A5;padding: 7px 0;}
	#recipiant_sql input[type="text"]{background-color: #fafafa;width: 130px;}
	#recipiant_sql input[type="text"].property{background-color: transparent;color: #333;font-size: 17px;text-transform: capitalize;padding: 0px;padding-left: 15px;}
	#recipiant_sql .borderb{border-bottom: 3px solid rgb(45, 193, 238);}
	#recipiant_sql input[type="text"].table{border: none !important;background-color: transparent;padding-left: 15px;text-transform: capitalize;}
	a{text-decoration: none;}
	.propertyJoin{width:199px;line-height:25px;font-weight: bold;font-family: sans-serif}
	.propertyJoinLeft{text-align: right;padding-right: 7px}
	.propertyJoinRight{text-align: left;padding-left: 7px}
	.bloctitle {background: transparent;cursor:move;}
	.bloctitle input {text-shadow: none;font-size: 13px;border : none !important;color: white;}
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
	.deletator{display:none;cursor: pointer;position:absolute;top:2px;right:0px;color:#fff;background-image: url(<?php echo BASE_PATH; ?>admin/img/icons.png);}
	.deletator2{cursor: pointer;position:absolute;top:2px;right:0px;color:#fff;}
	.tableCont .deletator{display:block;}
	#generatedsql{display:none;margin:5px;padding:5px;border-radius:4px;border:#ccc 1px solid;line-height: 20px;}
	.removeButton{border-radius: 5px;cursor: pointer;background: url(<?php echo BASE_PATH; ?>admin/img/icons.png) -96px -128px;display: none;overflow: hidden;width: 16px;height: 16px;}
	#queryCanvasWrapper{position: relative;height:320px;margin-top: 15px;overflow: auto;background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAMAAAC67D+PAAAADFBMVEXx9vnw9fj+/v7///+vmeNIAAAAKklEQVQIHQXBAQEAAAjDoHn6dxaqrqpqAAWwMrZRs8EKAzWAshkUDIoZPCvPAOPf77MtAAAAAElFTkSuQmCC');border-bottom: 1px solid #eaeaea;}
	#regenerateview{background: url('<?php echo BASE_PATH?>admin/img/spritelockunlock.png') 0 -33px no-repeat;width: 16px;height: 16px;background-repeat: no-repeat;border: none;box-shadow: none;margin-left: 5px;}
	#regenerateview:checked{background: url('<?php echo BASE_PATH?>admin/img/spritelockunlock.png') no-repeat}
	#regenerateview:hover{background: url('<?php echo BASE_PATH?>admin/img/spritelockunlock.png') rgb(251, 251, 251) 0 -33px no-repeat;box-shadow: none;background-repeat: no-repeat;border-color: none;}
	#regenerateview:checked:hover {background: url('<?php echo BASE_PATH?>admin/img/spritelockunlock.png') rgb(251, 251, 251) no-repeat;box-shadow: none;background-repeat: no-repeat;border-color: none;}
	#regenerateview[type='checkbox']:checked::before{content : " "}
	.ui-state-highlight{border:#ccc 70px solid;float:left;}
	#form{clear: both;position: relative;margin-left: 10px;}
	.sqlorder{margin: 0 10px;}
	.sqltotal{margin: 0 10px;padding-top: 5px;}
	#recipiant_sql input[type="text"].where,#recipiant_sql input[type="text"].orcond {margin: 0 10px;}
	#recipiant_sql input[type="text"].where:hover, #recipiant_sql input[type="text"].orcond:hover,#recipiant_sql input[type="text"].where:focus, #recipiant_sql input[type="text"].orcond:focus{background: #ececec;}
	#recipiant_sql input[type="text"].property,#recipiant_sql input[type="text"].alias{background-color: transparent;pointer-events: none;color: #333;font-size: 17px;text-transform: capitalize;padding: 0px;padding-left: 15px;}
	#recipiant_sql input[type="text"].alias{line-height: 16px;font-family: sans-serif;border: none !important;}
	#recipiant_sql input[type="text"].calculated{width: 100%;}
	#recipiant_sql select:enabled:hover{background-color: #ececec;}
	#recipiant_sql .checkb{line-height: 13px;padding: 3px 0 0;}
	.calcMode {position: relative}
	.closehelper{position: absolute;top: 0px;right: -5px;cursor: pointer;background: url(/parsi201013/admin/img/icons.png) -96px -128px;width: 16px;height: 16px;}
	.helper{display: none;padding: 5px 10px 5px 5px;position: absolute;width: 100%;height: 80px;top: 25px;z-index: 9;background-color: #555;text-shadow: initial;color: white;line-height: 13px;}
</style>
<div class="helper">
	<div style="position :relative"><span class="closehelper"></span>Write your calculation (+-*/) with or without existing properties</div>
	<select id="helperSelect">						
	</select>
</div>
<?php if($this->getConfig('mode') == 'r' ): ?>
	<label class="placeholder"><?php echo t('Pagination'); ?></label>
	<div style="display:inline-block;width:200px">
		<?php echo t('Enable Pagination'); ?> <input type="hidden" value="0" name="pagination" /><input type="checkbox" id="pagination" name="pagination" value="1" <?php
		if ($this->getConfig('pagination') == 1)
			echo ' checked="checked"';
		?> />
	</div>
	<div style="display:inline-block;width:315px">
		<?php echo t('This block shows at most') . ' '; ?> <input type="text" style="line-height: 15px;height: 17px;width: 28px;padding: 0 0 0 5px;" name="nbitem" id="nbitem"  value="<?php echo $this->getConfig('nbitem') ?>" /><?php echo ' ' . t('items'); ?><br>
	</div>
<?php else: ?>
<div class="tabs">
	<ul>
		<li class="active"><a href="#tabs-admin-query"><?php echo t('Query Builder'); ?></a></li>
		<li><a href="#tabs-admin-template"><?php echo t('View'); ?></a></li>
		<li><a href="#tabs-result"><?php echo t('Result'); ?> <span id="labelresult"></span></a></li>
	</ul>
	<div class="clearboth panel" id="tabs-admin-query">
		 <div id="queryCanvasWrapper">
			<div id="addTable" class="tooltip" data-tooltip="<?php echo t('Add a table'); ?>" data-pos="e" onclick="$('#schema_sql').show()">+</div>
			<div id="manageLinks" class="tooltip" data-tooltip="<?php echo t('Relations'); ?>" data-pos="w" onclick="$('#linksWrapper').show()">&infin;</div>
			<span id="deletator" class="ui-icon ui-icon-closethick deletator"></span>
			<span id="invertRelation" class="ui-icon ui-icon-refresh"></span>
			<div id="schema_sql" style="overflow-y: auto;height:100%">
				<?php
				$aliasClasses = array_flip(\app::$aliasClasses);
				$addLinkExtendsJS = '';
				foreach (\app::$activeModules as $module => $mode) :
					$models = \app::getModule($module)->getModel();
					if (count($models) > 0) :
						?>
						<div class="schemasql ellipsis">
							<a href="#" onclick="return false"><?php echo $module; ?></a>
							<div class="menuh">
								<?php foreach ($models as $model => $entity) : 
									$extends = $entity->getExtends();
									$attrExtend = '';
									if(!empty($extends)) {
										$extendsArray = array();
										foreach ($extends as $extend) {
											$extendsArray[] = $extend->getTableName();
											$addLinkExtendsJS = 'document.querySelector(".tableCont[table=' . $extend->getTableName() . '] .property").setAttribute("link", "' . $entity->getTableName() . '");';
										}
										$attrExtend = ' data-extends="' . implode(',', $extendsArray) . '"';
									}
								?>
								<div class="tableCont" table="<?php echo $module . '_' . $model; ?>"<?php echo $attrExtend; ?>>
									<div class="table ellipsis"><?php echo $model; ?></div>
										<?php
										$obj = app::getModule($module)->getEntity($model);
										foreach ($obj->getFields() AS $field) :
												if (get_class($field) == 'core\fields\foreignkey')
													$link = ' link="' . $field->moduleLink . '_' . $field->link . '"';
												elseif (get_class($field) == 'core\fields\user') 
													$link = ' link="core_user"';
												else
													$link = '';
												if(!isset($extends[$field->getTableName()])) :
												?>
											<div class="property <?php echo $aliasClasses[get_class($field)]; ?>"<?php echo $link; ?>><?php echo $field->name; ?></div>
												<?php
												endif;
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
				<span style="display: block;padding: 3px 0 14px 20px;background: url(<?php echo BASE_PATH; ?>admin/img/puce.png) no-repeat;font-weight: bold;color: #333;"> <?php echo t('Relationship Management'); ?></span>
				<ol id="links"></ol>
			</div>
			<div id="queryCanvas" style="width:100%;height:100%"></div>
		</div>
		<h2><?php echo t('Criterias'); ?></h2>
		<div style="margin-left: 20px;  line-height: 23px;margin-bottom: 5px;">
			<?php echo t('Add a calculated Field'); ?><span class="calculatedField" style="background: rgba(191,185,169,.2);  height: 16px;  cursor: pointer;  border-radius: 3px;  width: 16px;  outline: none;  -webkit-appearance: none;  box-shadow: 0 1px 2px rgba(0,0,0,.44) inset, 0 1px 0 rgba(255,255,255,.54);padding: 0 3px 0 3px; text-align: center; margin: 0 10px;">+</span>
		</div>
		<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>">
		<div id="pattern_sql" class="queryblock floatleft none">
			<a href="#" onclick="$(this).parent('.queryblock').remove();$('#generate_query').trigger('click');">
				<span class="removeButton"></span>
			</a>
			<div class="normalMode bloctitle"><input class="property" type="text"></div>
			<div class="calcMode blocalias"><input placeholder="Alias" class="alias" type="text"></div>
			<div class="normalMode borderb"><input class="table" type="text"></div>
			<div class="calcMode borderCalculated borderb">
				<input class="calculated" placeholder="Calculation =+-/*" type="text">
			</div>
			<div class="sqltotal">
				<select class="aggregate">
					<option value=""></option>
					<option value="groupby"><?php echo t('GROUP'); ?></option>
					<option value="avg"><?php echo t('AVG'); ?></option>
					<option value="count"><?php echo t('COUNT'); ?></option>
					<option value="max"><?php echo t('MAX'); ?></option>
					<option value="min"><?php echo t('MIN'); ?></option>
					<option value="sum"><?php echo t('SUM'); ?></option>
				</select>
			</div>
			<div class="sqlorder">
				<select class="order">
					<option value=""></option>
					<option value="asc"><?php echo t('Ascending'); ?></option>
					<option value="desc"><?php echo t('Descending'); ?></option>
				</select>
			</div>
			<div class="align_center checkb"><input class="display" type="checkbox"></div>
			<div style="padding: 3px 0;"><input class="where" type="text"></div>
			<div><input class="orcond" type="text"></div>
			<div class="align_center checkb"><input class="filter" type="checkbox"></div>
			<div class="align_center checkb"><input class="sort" type="checkbox"></div>
			<div class="align_center checkb"><input class="group" type="checkbox"></div>
		</div>         
		<div id="form" action="">
			<div class="caption">
				<div style="line-height: 24px;"><?php echo t('Property'); ?></div>
				<div style="line-height: 21px;"><?php echo t('Entity'); ?></div>
				<div style="padding-top: 10px;"><?php echo t('Total'); ?></div>
				<div><?php echo t('Order'); ?></div>
				<div><?php echo t('Display'); ?></div>
				<div><?php echo t('Criteria'); ?></div>
				<div><?php echo t('Or'); ?></div>
				<div class="filter"><?php echo t('Filterable'); ?></div>
				<div class="sort"><?php echo t('Sortable'); ?></div>
				<div class="group"><?php echo t('Groupable'); ?></div>
			</div>
			<div id="recipiant_sql_cont" class="fs">
				<div id="recipiant_sql"></div>
			</div>
			<input type="button" class="none clearboth" id="generate_query" value="<?php echo t('Generate') . ' '; ?>">
			<div class="clearboth textdbquery">
				<div style="display:inline-block;width:200px">
					<?php echo t('Enable Pagination'); ?> <input type="hidden" value="0" name="pagination" /><input type="checkbox" id="pagination" name="pagination" value="1" <?php
					if ($this->getConfig('pagination') == 1)
						echo ' checked="checked"';
					?> />
				</div>
				<div style="display:inline-block;width:315px">
					<?php echo t('This block shows at most') . ' '; ?> <input type="text" style="line-height: 15px;height: 17px;width: 28px;padding: 0 0 0 5px;" name="nbitem" id="nbitem"  value="<?php echo $this->getConfig('nbitem') ?>" /><?php echo ' ' . t('items'); ?><br>
				</div>
				<div style="display:inline-block;width:110px">
					<?php echo t('Filterable'); ?> <input type="hidden" value="0" name="filter" /><input type="checkbox" id="filter" name="filter" value="1" <?php
					if ($this->getConfig('filter') == 1)
						echo ' checked="checked"';
					?> />
				</div>
				<div style="display:inline-block;width:110px">
					<?php echo t('Sortable'); ?> <input type="hidden" value="0" name="sort" /><input type="checkbox" id="sort" name="sort" value="1" <?php
					if ($this->getConfig('sort') == 1)
						echo ' checked="checked"';
					?> />
				</div>
				<div style="display:inline-block;">
					<?php echo t('Groupable'); ?> <input type="hidden" value="0" name="group" /><input type="checkbox" id="group" name="group" value="1" <?php
					if ($this->getConfig('group') == 1)
						echo ' checked="checked"';
					?> />
				</div>
			</div>
			<br>
		</div>
	</div>
	<div id="tabs-admin-template" class="panel" style="padding:0px">
		<div style="padding:9px 0">
		<?php echo t('Lock the view'); ?> <input type="hidden" value="0" name="regenerateview" /><input type="checkbox" id="regenerateview" name="regenerateview" value="1" <?php
		if ($this->getConfig('regenerateview') == 1)
		echo ' checked="checked"';
		?> />
	</div>
	<?php
	$path = $this->viewPath;
	$editorMode = 'application/x-httpd-php';
	include('modules/admin/views/editor.php');
	?>
	</div>
	<div class="clearboth panel" id="tabs-result" style="display:none">
		<div style="padding: 1px 20px 10px;">
			<div style="position:relative;text-align:right;padding:7px"><a href="#" style="color: rgb(0, 136, 213)" onclick="$('#generatedsql').slideToggle();return false;"><?php echo t('View SQL query'); ?></a></div>
			<div id="resultpreview">
				<?php
				if (is_object($view)) {
					$view->buildQuery();
					$sql = $view->getSQL();
					$search  = array('select ', ' from ', ' where ', ' order by ', ' group by ', ' limit ');
					$replace = array('<span style="font-weight:bold">SELECT</span> ', '<br><span style="font-weight:bold">FROM</span> ', '<br><span style="font-weight:bold">WHERE</span> ', '<br><span style="font-weight:bold">ORDER BY</span> ','<br><span style="font-weight:bold">GROUP BY</span> ', '<br><span style="font-weight:bold">LIMIT</span> ');
					echo '<div id="generatedsql">' . str_replace($search, $replace, $sql['query']) . '</div>';
					$obj = $view;
					$obj->limit(10);
					include('modules/admin/views/datagrid.php');
					echo '<script> document.getElementById("labelresult").textContent = "( ' . $obj->getPagination()->getNbRow() . ' )";</script>';
				}
				?>
			</div>
		</div>
	</div>
</div>
<script>
	var markerChangeEditor = false;
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
		if($('#group').is(':checked') ) $('.group').show();
		else $('.group').hide();
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

	function addProperty(propELMT, tableName, tableProperty, alias, calculation, display, aggregate, where, or, order, filter, sort, group) {
		var sqlscheme = $("#pattern_sql").clone();
		sqlscheme.attr("id","");
		if(alias.length > 0){
			var nameProp = alias;
			$(".calcMode",sqlscheme).show();
			$(".normalMode",sqlscheme).hide();
			$('.alias',sqlscheme).val(alias);
			$('.calculated',sqlscheme).val(calculation);
			$(".alias",sqlscheme).attr('name','properties[' + alias + '][alias]');
			$(".calculated",sqlscheme).attr('name','properties[' + alias + '][calculated]');
			$(".group",sqlscheme).prop( "checked", false );
			$(".group",sqlscheme).css( "pointer-events", 'none' );
			$(".aggregate",sqlscheme).css( "pointer-events", 'none' );
		}else{
			$(".normalMode",sqlscheme).show();
			$(".calcMode",sqlscheme).hide();
			var nameProp = tableName + "." + tableProperty;
			$(".table",sqlscheme).attr('value',tableName);
			$(".table",sqlscheme).attr('name','properties[' + nameProp + '][table]');
			$(sqlscheme).attr('property',nameProp);
			$(".property",sqlscheme).val(tableProperty);
			$(".property",sqlscheme).attr('name','properties[' + nameProp + '][property]');
		}
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
					$(".where",sqlscheme).val("= :session_id_user");
				}
			}
		}
		$(".orcond",sqlscheme).attr('name','properties[' + nameProp + '][or]').val(or);
		$(".order",sqlscheme).attr('name','properties[' + nameProp + '][order]').val(order);
		$(".filter",sqlscheme).attr('name','properties[' + nameProp + '][filter]')[0].checked = filter;
		$(".sort",sqlscheme).attr('name','properties[' + nameProp + '][sort]')[0].checked = sort;
		$(".group",sqlscheme).attr('name','properties[' + nameProp + '][group]')[0].checked = group;
		sqlscheme.appendTo("#recipiant_sql").slideDown();
	}

	$("select option").each(function(){
		if(!this.value.length){
			$(this).text('Default');
			this.value = "";
		};
	});

	$('#queryCanvas').on('click','#deletator',function(){
		obj = $(this).parent();
		if(confirm(t('Are you sure to delete this entity ?'))){
			$(this).appendTo($('body'));
			obj.remove();
			filterTables();
			draw();
			this.style.display = "none";
		}
	});
	
	$('#links').on("change","select",function() {
	   draw(); 
	   $("#generate_query").trigger("click");
	})
	.on('click','#deletator',function(){
		var parent = $(this).parent();
		$(this).appendTo($('body'));
		$('#invertRelation').appendTo($('body'));
		parent.remove();
		filterTables();
		checkRelations();
		draw();
		this.style.display = "none";
		document.getElementById("invertRelation").style.display = "none";
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
			addProperty(this, $(this).parent().attr('table'), $(this).text().trim(),'', '', true, "", "", "", "", true, true, true);
			$("#generate_query").trigger("click");
		}
	});
	$(document).on("click",".calculatedField",function() {
		var calculatedField = prompt("Please enter calculated Field name");
		/* to do check !exist */
		if (calculatedField != null) {
		  addProperty('', '', '', calculatedField,'', true, "", "", "", "", true, true, true);
		}
	})
	.on('click','.closehelper',function() {	
		var context = $(this).closest('.queryblock');
		$(this,context).closest(".helper").css("display","none");
	})
	.on('click','#recipiant_sql input[type="text"].calculated',function() {
		var allsqlprop = '<option></option>';
		$('#recipiant_sql .queryblock').each(function(){
			var props = $(this).attr('property');
			if (typeof props != 'undefined') allsqlprop += '<option>' + props +'</option>';		
		});
		$('#helperSelect').html(allsqlprop);
		$(this).parent().append($('.helper'));
		$(this).next().css('display','block');
	})
	.on('change','.helper select',function() {
		var context = $(this).closest('.queryblock');
		var calc = $('.calculated',context).val();
		$('.calculated',context).val(calc +$('.helper select option:selected',context).text());
	})
	.on("change","#form input,#form select",function() {
		if(!$('#pagination').is(':checked') && $('#nbitem').val().length==0) $('#nbitem').val(10);
		manageFilters();
		$("#generate_query").trigger("click");
	})
	.on("change","#regenerateview",function() {
		if(!$(this).is(":checked") && confirm(t("If you confirm, all your changes will be removed"))){
			$("#generate_query").trigger("click");
		}
	});

	$('#generate_query').click(function() {
		markerChangeEditor = true;
		$.post(BASE_PATH+'admin/datagridPreview',$('form').serialize() + "&TOKEN=<?php echo TOKEN; ?>",function(data){
			$("#resultpreview").html(data);
		});
		if(!$("#regenerateview").is(":checked")){
			$.post(BASE_PATH + '<?php $mod = $_POST['typeProgress'] === 'theme' ? $_POST['THEMEMODULE'] : $_POST['MODULE']; echo $mod; ?>/callBlock',{TOKEN: "<?php echo TOKEN; ?>", idPage:"<?php if($_POST['typeProgress']=='page') echo $_POST['IDPage']; ?>",theme: "<?php if($_POST['typeProgress']=='theme') echo $_POST['THEME']; ?>", id:"<?php echo $_POST['idBlock']; ?>", method:'generateView', properties:$('form input[name^="properties"]').add(('form select[name^="properties"]')).add('form input[name^="pagination"]').add('form input[name="filter"]').add('form input[name="sort"]').add('form input[name="group"]').serialize()},function(data){
			codeEditor.setValue(data);
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
		<?php echo $addLinkExtendsJS; ?>
		$( "#links" ).sortable({ update:function(){$("#generate_query").trigger("click");} });
		$('#tabs-admin-template').css('height','0px').css('overflow','hidden'); /* trick to init correctly code mirror */
		$(".tabs").on('click'," > ul a",function(e){
			e.preventDefault();
			$(".panel").hide();
			$(".tabs > ul .active").removeClass("active");
			$(this).parent().addClass("active");
			$($(this).attr('href')).show();
			$($(this).attr('href')).css('height','100%').css('overflow','inherit');
		});

		$(".schemasql").on("click",".tableCont",function(e){
			if(this.classList.contains("inaccessible")){
				alert("You have no relation for this table");
				return false;
			}
			var tableName = this.getAttribute("table");
			addTable( tableName, 5, 230);
			var getExtends = this.getAttribute("data-extends");
			if(getExtends){	
				var id_property = document.querySelector(".tableCont[table=" + tableName + "] .property").textContent;
				var extendsArray = getExtends.split(",");
				for(var i in extendsArray) {
					addTable( extendsArray[i], 5, 400);
					putLink(tableName,id_property,extendsArray[i],document.querySelector(".tableCont[table=" + extendsArray[i] + "] .property").textContent,"left outer join");
				}
			}
			
			draw();
		});

		$("#links").on('mouseover mouseout','.linkDef',function(event) {
			var deletator = document.getElementById("deletator");
			var invert = document.getElementById("invertRelation");
			if (event.type == 'mouseover') {
				deletator.style.display = "block";
				invert.style.display = "block";
				this.insertBefore( deletator, this.firstChild);
				this.insertBefore( invert, this.firstChild);
			} else {
				deletator.style.display = "none";
				invert.style.display = "none";
			}
		});

		$("#queryCanvas").on('mouseenter mouseleave','.tableCont',function(event) {
			var deletator = document.getElementById("deletator");
			var deletator = document.getElementById("deletator");
			if (event.type == 'mouseenter') {
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
			 $page = \app::getModule($_POST['MODULE'])->getPage($_POST['IDPage']);
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
					addProperty("", "<?php echo (isset($selected['table'])) ?$selected['table'] : '' ?>", "<?php echo (isset($selected['property'])) ? $selected['property'] : ''; ?>", '<?php echo (isset($selected['alias']) ? $selected['alias'] : '') ?>', '<?php echo (isset($selected['calculated']) ? $selected['calculated'] : '') ?>',<?php echo (isset($selected['display']) ? 'true' : 'false') ?>, "<?php echo (isset($selected['aggregate']) ? $selected['aggregate'] : '' ) ?>", "<?php echo str_replace('"', '\"',$selected['where']) ?>", "<?php echo str_replace('"', '\"',$selected['or']) ?>", "<?php echo $selected['order'] ?>", <?php echo (isset($selected['filter']) ? 'true' : 'false') ?>, <?php echo (isset($selected['sort']) ? 'true' : 'false') ?>, <?php echo (isset($selected['group']) ? 'true' : 'false') ?>);
				<?php
			}
		}
		?>
		$( "#recipiant_sql" ).sortable({
			placeholder: "ui-state-highlight",
			handle: ".bloctitle"
		  });
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
					var paintStyle = { lineWidth:2,strokeStyle:"#259bdb"};
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
				containment: '#queryCanvasWrapper',
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
		if(markerChangeEditor == false){
			$("#regenerateview").prop("checked", true);
		}else{
			markerChangeEditor = false;
		}
	}
</script>
<style>
.adminzonecontent{min-width:1200px}
</style>
<?php endif;