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
 * @authors Julien Gras et BenoÃ®t Lorillot
 * @copyright  Julien Gras et BenoÃ®t Lorillot
 * @version  Release: 1.0
 * @category  Parsimony
 * @package admin
 * @license    http://opensource.org/licenses/osl-3.0.php 
 *  Open Software License (OSL 3.0)
 */
?>
<script src="<?php echo BASE_PATH; ?>lib/jquery-ui/jquery-ui-1.10.3.min.js"></script>
<style>
	.showcomponent{text-align: center;padding: 5px 0;width: 700px;overflow-x: auto;} 
	.paramstatique, .paramdyn{position: relative;background: #CBDDF3;border: 1px solid #9CC1D3;float: left; width: 112px; border: 1px solid rgb(204, 204, 204); margin: 0px 2px;}
	#tabs-admin-query{position:relative;text-align: left}
	.modulecss{padding: 5px;list-style: none;border: 1px solid #99BBE8;background-color: #CBDDF3;text-transform: capitalize;}
	.modulecss a{text-decoration: none;color:#333;}
	.details{display:none;position:absolute;top:23px;z-index:1;background: #fff;width: 650px;border: 1px solid #99BBE8;padding: 3px;overflow-x: scroll}
	.detailsCont{width: 1500px;}
	.entity{border-radius: 3px;box-shadow: #666 0px 1px 3px;background: #FBFBFB;margin:2px 2px;}
	.cent{width:100%;box-sizing:border-box;}
	div.type{cursor: move;}
	.entityname{font-weight: bold;font-size: 12px;padding:5px 4px;color: white;background: #1b74a4;border-top-left-radius: 3px;border-top-right-radius: 3px;text-align: center;}
	.property{padding: 0 5px;cursor: pointer;line-height: 16px;font-family: sans-serif;font-size: 11px;border-bottom: dotted #ddd 1px;font-weight: normal;}
	.property:hover{background:#CBDDF3}
	#recipiant_sql select{margin-bottom: 5px;margin-top: 5px;}
	.choicebuilder{display: inline-block;vertical-align: top;width: 225px;margin: 8px;padding: 7px;background: #FCFCFC;border: 1px solid #C2C2C2;color: black;height: 110px;}
	.choicetitle{padding: 3px;font-size: 15px;text-align: left;margin: 2px 0px 7px;border-bottom: 1px solid #DDD;}
	.parsiplusone {display: inline-block;vertical-align: top;cursor: cell;
				   background: url("<?php echo BASE_PATH; ?>admin/img/add.png") no-repeat;width: 16px;height: 16px;}
	#col > div,.paramdyn > div,.paramstatique > div{height:30px;line-height:30px;text-align:left;padding: 0 5px}
	#col > div{line-height: 28px;padding-left: 5px;border-bottom: #EFEFEF 1px solid;font-weight: bold;letter-spacing: 1.2px;}
	#col{background: white;max-width: 120px;border: 1px solid #eee;float: left;margin-left: -7px;margin-right: 5px;}
	.del{position: absolute;top: -6px;right: -10px;}  
	#addparam {margin-top: 10px;}
	#container{width:10000px}
	.ui-state-highlight{border:#ccc 61px solid;float:left;height:52px;}
	#container .ui-icon-closethick{margin: 5px;border: #666 solid 1px;border-radius: 5px;margin: 0px auto;display: none}
	#container > div:hover .ui-icon-closethick{display:block}
	#container .ui-icon-closethick{background-color: #F9F9F9;}
	.robots{margin: 1px 0;}
	.robots tr{background: white;}
	.robots td{padding: 5px 2px 5px 15px;color: #444;font-size: 12px;text-align: left !important;line-height: 22px;}
	.robots .opt{padding: 5px 2px 5px 15px;text-align: center !important;}
	th, td {height: 23px;width: 87px;}
	.disabled{pointer-events: none;opacity:0.8}
</style>

<div class="adminzone" id="adminformpage">
	<?php if(stream_resolve_include_path($module->getName() . '/pages/' . $page->getId() . '.obj') === FALSE): ?>
	<style>.notNew{visibility: hidden}</style>
	<div id="conf_box_title"><?php echo t('Add a page in').' '.$module->getName() ?></div>
	<?php else: ?>
	<div id="conf_box_title"><?php echo t('Manage this page') ?></div>
	<?php endif; ?>
	<div id="admin_page" class="adminzonemenu"> 
		<div id="goto_page" class="adminzonetab notNew"><a href="#" class="ellipsis"><?php echo t('See', FALSE); ?></a></div>
		<div id="delete_page" class="adminzonetab notNew"><a href="#" class="ellipsis"><?php echo t('Delete', FALSE); ?></a></div>   
	</div>
	<div id="contentformpage"  class="adminzonecontent">
		<form class="form" target="formResult" method="POST">
			<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />
			<input type="hidden" name="id_page" value="<?php echo $page->getId(); ?>">
			<input type="hidden" name="action" value="savePage">
			<div class="tabs">
				<ul>
					<li class="active"><a href="#tabs-1"><?php echo t('URL & Rewriting', FALSE); ?></a></li>
					<li><a href="#tabs-2"><?php echo t('SEO', FALSE); ?></a></li>
				</ul>
				<div class="clearboth" style="padding-top: 10px;"></div>
				<div id="tabs-1" class="panel">
					<?php
					echo '<input type="hidden" name="module" value="' . s($module->getName()) . '">';
					?>
					<div class="placeholder">
						<label for="title"><?php echo t('Title', FALSE); ?></label><input type="text" name="title" style="width:95%;" value="<?php echo s($page->getTitle()); ?>">
					</div>
					<div class="placeholder inputregex">
						<label for="title"><?php echo t('URL', FALSE); ?></label><input type="text" id="patternurlregex" name="regex" style="width:540px;" value="<?php echo s(substr($page->getRegex(), 2, -2)); ?>">
					</div>
					<div style="top: 5px;position: relative;left: 7px;text-overflow:ellipsis;font-size:13px">
						<span for="genereURL"><?php echo t('URL', FALSE); ?> : </span><span id="totalurl">http://<?php echo $_SERVER['HTTP_HOST'] . BASE_PATH ?><span class="modulename"><?php
								$modulename = $module->getName();
								if ($modulename != \app::$config['modules']['default'])
									echo $modulename;
								?></span><?php if ($modulename != \app::$config['modules']['default']) echo '/'; ?><span id="patternurl" ><?php echo $page->getURL(); ?></span></span>
					</div>
					<?php if ($_SESSION['behavior'] == 2): ?>
						<div style="position: absolute;left: 570px;top: 82px;cursor:pointer;color: #333;line-height: 15px;" onclick="$('#tabs-admin-querieur').toggle();">
							<span style="position: relative;top: 0px;right: 4px;" class="parsiplusone"></span><?php echo t('Dynamic page', FALSE); ?>
						</div>
					<?php endif; ?>
					<div id="pageOverride" style="position: relative;top: 16px;left: 7px;"></div>
					<div style="position:relative;padding-top: 30px;">
					<?php if ($_SESSION['behavior'] == 2): ?>
						<?php $components = $page->getURLcomponents(); ?>
							<div id="tabs-admin-querieur" class="none" style="">
								<fieldset id="tabs-admin-query" style="background: none;">
									<legend><?php echo t('URL Builder', False); ?></legend>
									<div class="showcomponent <?php if (empty($components)) echo 'none'; ?>">
										<div id="col">
											<div><?php echo t('Name', FALSE); ?></div>
											<div><?php echo t('Component', FALSE); ?></div>
											<div><?php echo t('Regex', FALSE); ?></div>
											<div><?php echo t('Default Value', FALSE); ?></div>
										</div> 
										<div id="abc" class="none paramdyn">
											<div class="parsiname"><input type="text" style="width:100px"></div>
											<div class="type"><?php echo t('Regex', FALSE); ?></div>
											<div class="regex"><input type="text" style="width:100px"></div>
											<div class="modelProperty" style="display:none"><input type="hidden"></div>
											<div class="val"><input type="text" style="width:100px"></div>
											<div class="del"><a href="" onClick="if (confirm('<?php echo t('Are you sure to delete this component ?', FALSE); ?>'))$(this).parent().parent().remove();genereregex();return false;"><span class="ui-icon ui-icon-closethick"></span></a></div>
										</div>
										<div id="abcd" class="none paramstatique">
											<div class="text"><input type="text" style="width:100px"></div>
											<div class="type" style="line-height: 30px;height: 90px;"><?php echo t('Text', FALSE); ?></div>
											<div class="del"><a href="" onClick="if (confirm('<?php echo t('Are you sure to delete this component ?', FALSE); ?>'))$(this).parent().parent().remove();return false;"><span class="ui-icon ui-icon-closethick"></span></a></div>
										</div>
										<div id="container">
											<?php
											if (!empty($components)) {
												foreach ($page->getURLcomponents() AS $idc => $component) {
													if (isset($component['regex'])) {
														?>
														<div class="paramdyn">
															<div class="parsiname"><input value="<?php echo $component['name']; ?>" name="URLcomponents[<?php echo $idc; ?>][name]" style="width:100px" type="text" ></div>
															<div class="type"><?php echo t('Regex', FALSE); ?></div>

															<div class="regex"><input value="<?php echo $component['regex']; ?>" name="URLcomponents[<?php echo $idc; ?>][regex]" type="text" style="width:100px" ></div>
															<div class="modelProperty" style="display:none"><input value="<?php if (isset($component['modelProperty'])) echo $component['modelProperty']; ?>" name="URLcomponents[<?php echo $idc; ?>][modelProperty]" type="hidden"></div>
															<div class="val"><input value="<?php echo $component['val']; ?>" name="URLcomponents[<?php echo $idc; ?>][val]" type="text" style="width:100px"></div>
															<div class="del" style="text-align:center"><a href="" onClick="if(confirm('<?php echo t('Are you sure to delete this component ?', FALSE); ?>'))$(this).parent().parent().remove();genereregex();return false;"><span class="ui-icon ui-icon-closethick"></span></a></div>
														</div>
														<?php
													} else {
														?>
														<div class="paramstatique">
															<div class="text" colspan="3"><input type="text" class="cent" name="URLcomponents[<?php echo $idc ?>][text]" value="<?php echo $component['text'] ?>"></div>
															<div class="type" style="line-height: 30px;height: 90px;"><?php echo t('Text', FALSE); ?></div>
															<div class="del"><a href="" onClick="if (confirm('<?php echo t('Are you sure to delete this component ?', FALSE); ?>'))$(this).parent().parent().remove();genereregex();return false;"><span class="ui-icon ui-icon-closethick"></span></a></div>
														</div>
													<?php
												}
											}
										}
										?>
										</div>
									</div>
									<div style="clear: both;padding-top: 15px;"><?php echo t('To create your URL, Choose between these elements :', False); ?></div>
									<div id="schema_sql" class="choicebuilder" style="width: 175px;">
										<div class="choicetitle"><?php echo t('A SQL property', False); ?> :</div>
										<?php
										$models = $module->getModel();
										$allowedField = array('field_ident' => '1', 'field_string' => 'example', 'field_numeric' => '1', 'field_numeric' => '1', 'field_url_rewriting' => 'example', 'field_user' => '1');
										$aliasClasses = array_flip(\app::$aliasClasses);
										if (count($models) > 0) {
											echo '<div class="floatleft ui-tabs-nav" style="position:relative;">
											<li class="ui-state-default ui-corner-top modulecss">' . $module->getName() . '</li><div class="details"><div class="detailsCont">';
											foreach ($models as $modelName => $model) {
												echo '<div class="inline-block entity" table="' . $module->getName() . '_' . $modelName . '">
								<div class="table entityname ellipsis">' . $module->getName() . '_' . $modelName . '</div>';
												$obj = app::getModule($module->getName())->getEntity($modelName);
												foreach ($obj->getFields() AS $field) {
													$className = get_class($field);
													if (isset($aliasClasses[$className]) && isset($allowedField[$aliasClasses[$className]])) {
														echo '<div name="' . $field->name . '" regex="(' . $field->regex . ')" val="'.$allowedField[$aliasClasses[$className]].'" class="ellipsis property ' . $className . '">' . $field->name . '</div>';
													}
												}
												echo '</div>';
											}
											echo '</div></div></div>';
										}
										?>
										<div class="clearboth"></div>
									</div>
									<div class="choicebuilder">
										<div class="choicetitle"><?php echo t('A regex parameter', False); ?> :</div>
										<input type="text" style="width: 120px;margin-right: 10px;" id="paramname">
										<select id="paramregex"><option value="(.*)"></span><?php echo t('Text', False); ?></option><option value="([0-9]*)"></span><?php echo t('Numeric', False); ?></option></select>
										<input type="button" id="addparam" value="<?php echo t('Add Text Component', False); ?>">
									</div>
									<div class="choicebuilder">
										<div class="choicetitle"><?php echo t('A simple textual parameter :', False); ?></div>
										<input type="button" id="addtextcomposant" value="<?php echo t('Add Text Component', False); ?>">
									</div>
								</fieldset>
								<div class="none"><a href="#" onClick="$('input[name=\'regex\']');return false;"><?php echo t('Dynamise your page with numbers', FALSE); ?></a> <a href="#" onClick="$(this).next().slideToggle();return false;"><?php echo t('Dynamise your page with String', FALSE); ?></a></div>
								<div class="clearboth"></div>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<div id="tabs-2" class="fields_to_update panel none">
					<div class="placeholder"><label for="meta[description]"><?php echo t('Description', FALSE); ?></label><textarea class="cent" name="meta[description]" row="7" cols="50"><?php echo s($page->getMeta('description')); ?></textarea></div>
					<div class="placeholder"><label for="meta[keywords]"><?php echo t('Keywords', FALSE); ?></label><textarea class="cent" name="meta[keywords]" row="7" cols="50"><?php echo s($page->getMeta('keywords')); ?></textarea></div>
					<div class="placeholder"><label for="meta[author]"><?php echo t('Author', FALSE); ?></label><textarea class="cent" name="meta[author]" row="7" cols="50"><?php echo s($page->getMeta('author')); ?></textarea></div>
					<div class="placeholder">
						<label style="border-bottom: 1px solid #C1C1C1;"><?php echo t('Robots', FALSE); ?></label>
						<input type="hidden" name="meta[robots]" id="SEOrobots" value="<?php echo s($page->getMeta('robots')); ?>" /><br><br>
						<table class="robots">
							<tbody>
							<tr>
								<td><?php echo t('No index', FALSE); ?></td><td class="opt"><input type="checkbox" class="robotsOptions" data-option="noindex" <?php if (strstr($page->getMeta('robots'), 'noindex')) echo ' checked="checked"'; ?> /></td>
							</tr>
							<tr>
								<td><?php echo t('No follow', FALSE); ?></td><td class="opt"><input type="checkbox" class="robotsOptions" data-option="nofollow" <?php if (strstr($page->getMeta('robots'), 'nofollow')) echo ' checked="checked"'; ?> /></td>
							</tr>
							<tr>
								<td><?php echo t('No archive', FALSE); ?></td><td class="opt"><input type="checkbox" class="robotsOptions" data-option="noarchive" <?php if (strstr($page->getMeta('robots'), 'noarchive')) echo ' checked="checked"'; ?> /></td>
							</tr>
							</tbody>
						</table>
					</div>
				</div>
				<input class="none" type="submit" id="sendFormPage">
			</div>
		</form>
	</div>
	<div class="adminzonefooter">
		<div id="save_page" class="save ellipsis"><?php echo t('Save', FALSE); ?></div>
	</div>
</div>
<script type="text/javascript">
	$(document).on('click', ".tabs li a", function(e) {
		e.preventDefault();
		$(".panel").hide();
		$(".tabs ul .active").removeClass("active");
		$(this).parent().addClass("active");
		$($(this).attr('href')).show();
	})
	.on('mousedown change keyup', '.inputregex', function() {
		if ($('#container > div').length == 0) {
			$('#patternurlregex').removeClass("disabled");
		} else {
			$('#patternurlregex').addClass("disabled");
		}
	})
	.on('change keyup', '#patternurlregex', function() {
		$('#goto_page').hide();
		$("#patternurl").text(this.value);
	})
	.on('click', '#save_page', function(e) {
		e.preventDefault();
		$('.notNew').css("visibility", "visible");
		$('#patternurlregex').removeClass("disabled");
		$('#conf_box input[name="action"]').val("savePage");
		$('#sendFormPage').trigger('click');
		$('#goto_page').show();
		if ($('#container > div').length > 0) {
			$('#patternurlregex').addClass("disabled");
		}
	})
	.on('click', '#goto_page', function(e) {
		e.preventDefault();
		parent.location = $('#totalurl').text();
	})
	.on('click', '#delete_page', function(e) {
		e.preventDefault();
		var trad = t('Are you sure to delete this page ?');
		if (confirm(trad)) {
			$('#adminformpage input[name="action"]').val("deleteThisPage");
			$('#sendFormPage').trigger('click');
		}
	})

	.on('click', '#schema_sql .property', function() {
		var obj = $('#abc').clone().attr('id', '');
		$(".parsiname input", obj).val($(this).attr('name'));
		$(".regex input", obj).val($(this).attr('regex'));
		$(".val input", obj).val($(this).attr('val'));
		$(".modelProperty input", obj).val($(this).parent().attr("table") + "." + $(this).text());
		obj.appendTo('#container').show();
		$("#container").sortable("refresh");
		genereregex();
	})
	.on('change keyup', '.showcomponent input', function() {
		genereregex();
	})

	.on('click', '#addparam', function() {
		obj = $('#abc').clone();
		obj.removeAttr("id");
		$('.parsiname input', obj).val($('#paramname').val());
		$('.regex input', obj).val($('#paramregex').val());
		if ($('#paramregex').val() == '(.*)')
			$('.val input', obj).val('abcd');
		else
			$('.val input', obj).val('123');
		obj.appendTo('#container').show();
		$('#paramname').val('');
		genereregex();
	})

	.on('click', '#addtextcomposant', function() {
		$('#abcd').clone().removeAttr("id").appendTo('#container').show();
		genereregex();
	})

	.on('click', '.robotsOptions', function() {
		var robots = "";
		$('.robotsOptions:checked').each(function() {
			robots += $(this).data("option") + ",";
		});
		$('#SEOrobots').val(robots.substring(0, robots.length - 1));
	});

	$('input[name="title"]').blur(function() {
		if ($('input[name="title"]').val().length > 0 && $('input[name="regex"]').val().length == 0) {
			$('input[name="regex"]').addClass('active');
			$.post(BASE_PATH + "admin/titleToUrl", {TOKEN: TOKEN, url: $(this).val()},
			function(data) {
				$('input[name="regex"]').val(data);
			});
		}
	});
	$(function() {
		$("#schema_sql > div").hover(function() {
			$("li", this).next().show();
		}, function() {
			$("li", this).next().hide();
		});
		$("#container").sortable({
			placeholder: "ui-state-highlight",
			stop: function() {
				genereregex();
			}
		});
		$(".showcomponent").disableSelection();
		checkOveride('<?php echo $page->getRegex() ?>');
	});

	function genereregex() {
		$('#goto_page').hide();
		var url = '';
		var urlRegex = '';
		$('#container > div:not(#abc,#abcd)').each(function(i) {
			$("input", this).each(function() {
				$(this).attr("name", "URLcomponents[" + i + "][" + $(this).parent().attr("class").replace("parsi", "") + "]");
			});
			if ($(this).hasClass('paramdyn')) {
				url += $(".val input", this).val();
				urlRegex += "(\?<" + $(".parsiname input", this).val() + ">" + $(".regex input", this).val() + ')';
			} else {
				url += $(".text input", this).val();
				urlRegex += $(".text input", this).val();
			}
		});
		$("#patternurl").text(url);
		$("#patternurlregex").val(urlRegex);
		$(".showcomponent").show();
		checkOveride("@^" + urlRegex + "$@");
	}

	function checkOveride(regex) {
		$.post(BASE_PATH + "admin/checkOverridedPage", {module: '<?php echo MODULE ?>', idpage: '<?php echo $page->getId() ?>', regex: regex}, function(data) {
			if (data.length > 0) {
				$("#pageOverride").html('<div style="background: #44C5EC;width: 531px;padding: 5px;color: #FBFBFB;">Attention this page is suspected to override and hide page ' + data + '</div>');
			} else {
				$("#pageOverride").html("");
			}
		});
	}
</script>