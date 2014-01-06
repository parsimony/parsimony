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
if (!\app::getClass('user')->VerifyConnexion())
	exit;
if (!isset($_POST['module']))
	$_POST['module'] = \app::$config['modules']['default'];
if (!isset($_COOKIE['connectorchoice'])) {
	$_COOKIE['connectorchoice'] = 'Bezier';
}
$moduleObj = \app::getModule($_POST['module']);
$modulesInfos = \tools::getClassInfos($moduleObj);
if (isset($modulesInfos['mode']) && strstr($modulesInfos['mode'], 'r')) {
	?>
	<style>
		.areaWrite { display:none }
	</style>
	<?php
}
?>
<link rel="stylesheet" href="<?php echo BASE_PATH; ?>lib/cms.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo BASE_PATH; ?>admin/css/ui.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo BASE_PATH; ?>lib/tooltip/parsimonyTooltip.css" type="text/css" media="all" />
<script src="<?php echo BASE_PATH; ?>lib/jquery/jquery-2.0.2.min.js"></script>
<script src="<?php echo BASE_PATH; ?>lib/jquery-ui/jquery-ui-1.10.3.min.js"></script>
<script type="text/javascript">
	var BASE_PATH = '<?php echo BASE_PATH ?>';
</script>
<script type="text/javascript" src="<?php echo BASE_PATH; ?>lib/jsPlumb/jquery.jsPlumb-1.3.16-all-min.js"></script>
<script type="text/javascript" src="<?php echo BASE_PATH; ?>lib/tooltip/parsimonyTooltip.js"></script>
<script type="text/javascript" src="<?php echo BASE_PATH; ?>admin/script.js"></script>
<script type="text/javascript" src="<?php echo BASE_PATH; ?>var/cache/<?php echo app::$request->getLocale(); ?>-lang.js"></script>
<style>.ui-state-disabled, .ui-widget-content .ui-state-disabled { opacity: .85; filter:Alpha(Opacity=85); background-image: none; }
</style>
<style type="text/css">
	#toolbar{position: fixed;left:0;right:0;min-width: 980px;z-index: 4;height:35px;color: white;
font-size: 12px;background-color: #272727;background-image: -webkit-linear-gradient(top, #333333, #222222);
			box-shadow: 0px 1px 0px rgb(41, 41, 41);border-bottom: 1px solid rgb(17, 17, 17);}
	
	
	.ui-icon { width: 16px; height: 16px;background-color:transparent; background-image: url(<?php echo BASE_PATH; ?>admin/img/icons.png);display: block;overflow: hidden;}
	body{margin:0;padding:0;height:100%;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;}
	select {background-image: url("<?php echo BASE_PATH; ?>admin/img/select.png")}
	select:enabled:hover {background-image: url("<?php echo BASE_PATH; ?>admin/img/select.png");}
	#container_bdd{margin:0;padding:0;top:36px;left:200px;background:  url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAMAAAC67D+PAAAADFBMVEXx9vnw9fj+/v7///+vmeNIAAAAKklEQVQIHQXBAQEAAAjDoHn6dxaqrqpqAAWwMrZRs8EKAzWAshkUDIoZPCvPAOPf77MtAAAAAElFTkSuQmCC');position:absolute;width: 2500px;height: 2500px;}
	#canvas{position:absolute;width:100%;height:100%}
	._jsPlumb_endpoint{cursor: pointer;z-index: 2}
	._jsPlumb_connector{cursor: pointer;}
	#field_list{margin: 0;padding: 0;border-radius: 8px;padding-left: 5px;}
	#field_list .myfield{position: relative;font-size: 12px;color: #222;width: 187px;margin: 2px;cursor: move;text-align: left;padding: 6px;background-color: #fbfbfb;background-repeat: no-repeat;padding-left: 32px;background-position: 7px 5px;border: 1px solid #C7C7C7;}
	#field_list .myfield:hover{background-color: #CBD8E8;}
	#field_list .myfield span{display:none;position: absolute;right: 5px;top: 5px;}
	#field_list .myfield:hover span{display:block}
	#update_table{display: none;font-size: 12px;}
	#update_field > div{display: none;overflow: auto;position: absolute;top: 48px;bottom: 0;}
	.table {z-index:3;position:absolute; color:#484848;line-height:18px;cursor:pointer;
			font-size:15px;background-color:white;font-weight:bold;border-radius: 3px;box-shadow: #666 0px 1px 3px;background: #fbfbfb;}
	.table:hover{box-shadow: 0px 0px 9px #777;}
	.ui-draggable-dragging:hover{box-shadow: #666 0px 1px 3px;} /*perf enhancement on drag table */
	.property{position:relative;cursor: pointer;border-bottom: dotted #ddd 1px;padding: 2px 10px;padding-right:15px;padding-left:22px;background-repeat:no-repeat;background-position: 2px 3px ;font-size: 12px;font-weight: normal;}
	.property.current_property,.table .property:hover{background-color: rgb(231,242,255)}
	.property[type_class=field_ident]{cursor: pointer;text-decoration:underline}
	.table .property:last-child{ border-radius: 0 0 3px 3px; }
	.ombre{box-shadow: 0px 0px 20px #34afb6;}
	.dragActive { border:4px dotted #b634af; border-radius:50px;}
	label{line-height: 26px;width: 140px;display: inline-block;padding-left: 10px;}
	h2, .title {text-align:center;font-size: 12px;padding:7px;color: white;background: #1b74a4;}
	.title{border-top-left-radius: 3px;border-top-right-radius: 3px;text-align: center;/*text-decoration: underline;*/}
	#leftsidebar{box-shadow: 1px 1px 5px #444;z-index:10; text-align: center;width:200px;position:fixed;left:0px;top:36px;bottom:0;background: #f9f9f9;}
	#rightsidebar{display:none;box-shadow: -2px 1px 8px #444;position:fixed;width:320px;background:#f9f9f9;right:0;top:36px;bottom: 0;}
	#deletator{cursor: pointer;position:absolute;top:2px;right:0px;color:#fff;background-image: url(<?php echo BASE_PATH; ?>admin/img/icons.png);}
	.property #deletator{padding: 0px 2px 0px 0px;color: #FF4D4D;background-image: url(<?php echo BASE_PATH; ?>admin/img/icons.png);}
	#outline{position:fixed;right:20px;bottom: 20px;border: 1px solid #97B2D2;z-index: 1;}
	h3{margin:10px 0;font-size:16px;padding-left: 5px;}
	.component{font-size: 11px;cursor:help;padding:4px 2px;background-color: #F1F5F9;border: 1px solid #97B2D2;opacity:0.6}
	.component:hover{opacity:1}
	.rightbar{padding: 3px 0}
	#editor:hover{display:table}
	.connection{color:#2E63A5;text-transform: capitalize;}
	.popup{text-align: left;font-family: 'Segoe UI',Tahoma,Helvetica,sans-serif;overflow: hidden;border-radius: 2px;width: 50%;position: relative;margin: 0 auto;top: 110px;z-index: 999998;display: none;background-color: #fbfbfb;}
	.question{font-size: 14px;color: #333;padding: 5px;border: 1px solid #e5e5e5;margin: 11px;line-height: 20px;}
	.question input{margin-right: 10px;}
	.conf_box_close{background-image: url(<?php echo BASE_PATH; ?>admin/img/icons.png);margin: 2px 5px;position: absolute;top: 4px;right: 0px;color: white;cursor: pointer;}
	.entity2,.entity1{font-weight:bold}
	.title_popup{border-radius: 2px 2px 0 0;position: relative;background: #259BDB;text-align: center;color: white;border-color: #2E63A5;font-size: 18px;line-height: 39px;}
	.tooltitle{font-size:13px;line-height: 15px;padding-left: 30px;font-weight: bold;}
	.toolimg{position: absolute;top:5px;left:15px;}
	.toolfield{position: relative;}
	.tooldef{font-size:12px;font-style: italic;margin: 10px 5px;line-height : 15px;width: 250px;white-space: normal;}
	.tooltype{margin: 0px 5px;}
	.tooltab{margin: 10px 5px 0;font-size: 10px;font-family: inherit;color: white;border-top: 1px solid whitesmoke;border-left: 1px solid whitesmoke;border-bottom: 1px solid whitesmoke;}
	.tooltab td{width:60px;height:40px;text-align: center;vertical-align: middle;border-right: 1px solid whitesmoke}
	/*     tbody td:first-child{margin:0 10px}*/
	.tooltab td input{width: 50px;font-size: inherit;height: 20px;}
	.tooltab tbody{border-top: 1px solid whitesmoke}
	.tooltab td progress{box-sizing: border-box;-moz-box-sizing: border-box;-webkit-box-sizing: content-box;margin:3px;width: 50px}
	.boxDropImage {color: white;border: 4px dashed #999;border-radius: 3px;text-align: center;margin: 5px;padding: 5px;}
	#toolbar{font-weight: normal;line-height: 36px;color:#FBFBFB}
	.specialprop{border: none;border-radius: 0;padding: 5px;background: none;}
	#extLink {position: fixed;right: 14px;top: 45px;height: 100px;width: 100px;line-height: 25px;padding-top: 20px;}
	#btnLinkToExternal{margin-bottom: 15px;}
	.dragActive2 {z-index: 1;border-radius: 100px;font-size: 12px;text-align: center;background: #1b74a4;color: #fff;padding: 17px 5px;}
	#save.haveToSave{color: white;font-weight: bold;
					 background-image: -webkit-linear-gradient(top, #44C5EC, #259BDB);
					 background-image: -moz-linear-gradient(top, #44C5EC, #259BDB);
					 background-image: -ms-linear-gradient(top, #44C5EC, #259BDB);
					 background-image: linear-gradient(top, #44C5EC, #259BDB);border: 1px solid #0F76F3;}
	#conf_box_overlay{z-index: 9999;text-align: center;position: fixed;width: 100%;height: 100%;background: rgba(0, 0, 0, 0.85);}
	#notify {top:35px}
	#currentModule{font-weight: bold;padding-left: 5px;margin-left: 10px;}
	.hdb{background: transparent;font-weight: normal;font-size: 20px;height: 28px;color: #777;border-bottom: 2px solid #2DC1EE;padding: 0;margin: 10px 10px 11px 11px;}
	input[disabled] {background:#ddd}
	#connectorchoice{margin-left: 10px;}
	.behaviorProperty {width: 136px;}
	#rightsidebar{font-size:12px;}
	#rightsidebar input[type='text']{width:159px}
</style> 
<div id="extLink"><?php echo t('Link to an external module'); ?></div>
<div id="tooltip-new-fields" class="none toolfield">
	<p class="tooldef ellipsis"><?php echo t('Create an entity and drag n\'drop fields in order to develop your DB model !'); ?></p>
</div>

<div id="tooltip-field_string" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/string/icon.png"><span class="tooltitle"><?php echo t('String Field') ?></span></div>
	<p class="tooldef ellipsis"><?php echo t('A String Field manages any finite sequence of characters (i.e., letters, numerals, symbols and punctuation marks.)'); ?></p>
	<div class="tooltype"> SQL Type : VARCHAR 255 by default</div>
	<table class="tooltab">
		<thead>
			<tr>
				<td>Display</td>
				<td>Add</td>
				<td>Update</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>eF(_5</td>
				<td><input type="text"></td>
				<td><input type="text" value="eF(_5"></td>
			</tr>
		</tbody>
	</table>
</div>
<div id="tooltip-field_numeric" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/numeric/icon.png"><span class="tooltitle"><?php echo t('Numeric Field') ?></span></div>
	<p class="tooldef ellipsis"><?php echo t('A Numeric Field is a data field that holds only numbers to be calculated (without any decimal places).'); ?></p>
	<div class="tooltype"> SQL Type : INT 2 by default</div>
	<table class="tooltab">
		<thead>
			<tr>
				<td>Display</td>
				<td>Add</td>
				<td>Update</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>45</td>
				<td><input type="text"></td>
				<td><input type="text" value="45"></td>
			</tr>
		</tbody>
	</table>
</div> 

<div id="tooltip-field_decimal" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/decimal/icon.png"><span class="tooltitle"><?php echo t('Decimal Field') ?></span></div>    
	<p class="tooldef ellipsis"> <?php echo t('A Decimal Field is a data field that holds fixed-precision decimal numbers.') ?></p>
	<div class="tooltype"> SQL Type : DECIMAL 20,6 by default</div>
	<table class="tooltab">
		<thead>
			<tr>
				<td>Display</td>
				<td>Add</td>
				<td>Update</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>45,12</td>
				<td><input type="text"></td>
				<td><input type="text" value="45,12"></td>
			</tr>
		</tbody>
	</table>
</div> 

<div id="tooltip-field_price" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/price/icon.png"><span class="tooltitle"><?php echo t('Price Field') ?></span></div>
	<p class="tooldef ellipsis"> <?php echo 'A Price Field stores a money value in your entity. ' ?></p>
	<div class="tooltype"> SQL Type : DECIMAL 7,2 by default</div>
	<table class="tooltab">
		<thead>
			<tr>
				<td>Display</td>
				<td>Add</td>
				<td>Update</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>12,34</td>
				<td><input type="text"></td>
				<td><input type="text" value="12,34"></td>
			</tr>
		</tbody>
	</table>
</div>

<div id="tooltip-field_percent" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/percent/icon.png"><span class="tooltitle"><?php echo t('Percent Field') ?></span></div>
	<p class="tooldef ellipsis"><?php echo 'A Percent Field specializes in handling percentage data and displays a value between 0 and 100. ' ?></p>
	<div class="tooltype"> SQL Type : DECIMAL 5,2 by default</div>
	<table class="tooltab">
		<thead>
			<tr>
				<td>Display</td>
				<td>Add</td>
				<td>Update</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>100</td>
				<td><input type="text"></td>
				<td><input type="text" value="100"></td>
			</tr>
		</tbody>
	</table>
</div>

<div id="tooltip-field_mail" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/mail/icon.png"><span class="tooltitle"><?php echo t('Mail Field') ?></span></div>
	<p class="tooldef ellipsis"><?php echo 'A Mail Field is used when the data entered by the user has to be an email. ' ?></p>
	<div class="tooltype"> SQL Type : VARCHAR 255 by default</div>
	<table class="tooltab">
		<thead>
			<tr>
				<td>Display</td>
				<td>Add</td>
				<td>Update</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>abcdef@ghi.jk</td>
				<td><input type="text"></td>
				<td><input type="text" value="abcdef@ghi.jk"></td>
			</tr>
		</tbody>
	</table>
</div>

<div id="tooltip-field_password" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/password/icon.png"><span class="tooltitle"><?php echo t('Password Field') ?></span></div>
	<p class="tooldef ellipsis"><?php echo 'A password field stores in sha-1 hash the password + a salt. It displays a password input type.'; ?></p>
	<div class="tooltype"> SQL Type : VARCHAR 255 by default</div>
	<table class="tooltab">
		<thead>
			<tr>
				<td>Display</td>
				<td>Add</td>
				<td>Update</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>******</td>
				<td><input type="password"></td>
				<td><input type="password" value="abcdef@ghi"></td>
			</tr>
		</tbody>
	</table>
</div>

<div id="tooltip-field_state" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/state/icon.png"><span class="tooltitle"><?php echo t('State Field') ?></span></div>
	<p class="tooldef ellipsis"><?php echo 'A State field manages the status of an Entity. The state can be used as a Boolean (True / False) or can contain several values (Yes,Perhaps,No) separated by a comma (CSV). '; ?></p>
	<div class="tooltype"> SQL Type : INT 2 by default</div>
	<table class="tooltab">
		<thead>
			<tr>
				<td>Display</td>
				<td>Add</td>
				<td>Update</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><select type="text"> 
						<option value="0">Yes</option>';
						<option value="1" selected="selected">No</option>
						<option value="2">Perhaps</option>';
					</select></td>
				<td><select type="text"> 
						<option value="0" selected="selected">Yes</option>';
						<option value="1">No</option>
						<option value="2">Perhaps</option>';
					</select></td>
				<td><select type="text" name="ping_status"> 
						<option value="0">Yes</option>';
						<option value="1">No</option>
						<option value="2" selected="selected">Perhaps</option>';
					</select></td>
			</tr>
		</tbody>
	</table>
</div>

<div id="tooltip-field_date" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/date/icon.png"><span class="tooltitle"><?php echo t('Date Field') ?></span></div>
	<p class="tooldef ellipsis"><?php echo 'A date field is a component for presenting date and time.'; ?></p>
	<div class="tooltype"> SQL Type : DATETIME by default</div>
	<table class="tooltab">
		<thead>
			<tr>
				<td>Display</td>
				<td>Add</td>
				<td>Update</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>2012-07-06 09:42:30</td>
				<td><input type="text"></td>
				<td><input type="text" value="2012-07-06 09:42:30"></td>
			</tr>
		</tbody>
	</table>
</div>

<div id="tooltip-field_publication" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/date/icon.png"><span class="tooltitle"><?php echo t('Publication Field') ?></span></div>
	<p class="tooldef ellipsis"><?php echo 'A Publication Field contains the published or scheduled date. It provides a Visibility Mode (public, private, protected by password) and also a workflow with different status like Pending, Draft and Published. '; ?></p>
	<div class="tooltype"> SQL Type : DATETIME by default</div>
	<table class="tooltab">
		<thead>
			<tr>
				<td>Display</td>
				<td>Add</td>
				<td>Update</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>2012-07-07 11:15:52</td>
				<td><input type="text"></td>
				<td><input type="text" value="2012-07-07 11:15:52"></td>
			</tr>
		</tbody>
	</table>
</div>

<div id="tooltip-field_progress" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/progress/icon.png"><span class="tooltitle"><?php echo t('Progress Field') ?></span></div>
	<p class="tooldef ellipsis"><?php echo 'A Progress Field creates a progress bar. '; ?></p>
	<div class="tooltype"> SQL Type : INT 3 by default</div>
	<table class="tooltab">
		<thead>
			<tr>
				<td>Display</td>
				<td>Add</td>
				<td>Update</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><progress value="22" max="100"></progress></td>
				<td><progress value="" max=""></progress></td>
				<td><progress value="50" max="100"></progress></td>
			</tr>
		</tbody>
	</table>
</div>

<div id="tooltip-field_image" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/image/icon.png"><span class="tooltitle"><?php echo t('Image Field') ?></span></div>
	<p class="tooldef ellipsis"><?php echo 'This field is used to store the path and display a configurable image in drag n drop.'; ?></p>
	<div class="tooltype"> SQL Type : VARCHAR 255 by default</div>
	<table class="tooltab">
		<thead>
			<tr>
				<td>Display</td>
				<td>Add</td>
				<td>Update</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><img title="" src="<?php echo BASE_PATH ?>core/fields/image/icon.png"></td>
				<td>     
					<div class="boxDropImage">
						<input style="height: 25px;width: 120px;" type="file">
						<label style="font-size: 10px;line-height: 15px;width: 140px;display: block;float: none;padding-left: 0px;">Drag n' Drop your New Image In this Window</label>      
					</div>
				</td>
				<td style="width: 145px;">       
					<div class="boxDropImage" style="margin-top: 20px">
						<input style="height: 25px;width: 120px;" type="file">
						<label style="font-size: 10px;line-height: 15px;width: 140px;display: block;float: none;padding-left: 0px;">Drag n' Drop your New Image In this Window</label>      
					</div>
					<img title="" style="padding: 0 5px" src="<?php echo BASE_PATH ?>core/fields/image/icon.png">
				</td>
			</tr>
		</tbody>
	</table>
</div>

<div id="tooltip-field_url" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/url/icon.png"><span class="tooltitle"><?php echo t('URL Field') ?></span></div>
	<p class="tooldef ellipsis"><?php echo 'This field is used to specify a url.'; ?></p>
	<div class="tooltype"> SQL Type : VARCHAR 255 by default</div>
	<table class="tooltab">
		<thead>
			<tr>
				<td>Display</td>
				<td>Add</td>
				<td>Update</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td style="margin: 0 3px;width: 70px;">abc.def/ghijk</td>
				<td><input type="text"></td>
				<td style=""><input style="margin: 0 5px;width: 70px;" type="text" value="bcdef.gh/ijkl"></td>
			</tr>
		</tbody>
	</table>
</div>

<div id="tooltip-field_url_rewriting" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/url_rewriting/icon.png"><span class="tooltitle"><?php echo t('Url rewriting Field') ?></span></div>
	<p class="tooldef ellipsis"><?php echo 'This field is a unique identifier of a record for the SEO generated by the title of your record (i.e. This is my article, /this-is-my-article).<br> URL rewriting allows to provide a better search engine optimization.<br> URL\'s appearance is modified to have more relevant links to web pages.'; ?></p>
	<div class="tooltype"> SQL Type : VARCHAR 255 by default</div>
	<table class="tooltab">
		<thead>
			<tr>
				<td>Display</td>
				<td>Add</td>
				<td>Update</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>url.rew/riting</td>
				<td><input type="text"></td>
				<td><input type="text" value="url.rew/riting"></td>
			</tr>
		</tbody>
	</table>
</div>

<div id="tooltip-field_wysiwyg" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/wysiwyg/icon.png"><span class="tooltitle"><?php echo t('WYSIWIG Field') ?></span></div>
	<p class="tooldef ellipsis"><?php echo 'This field is used to display a rich content such as text, images or videos.'; ?></p>
	<div class="tooltype"> SQL Type : LONGTEXT by default</div>
	<table class="tooltab">
		<thead>
			<tr>
				<td>Display</td>
				<td>Add</td>
				<td>Update</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Responsive Design Server Side</td>
				<td><img style="margin:5px" title="" src="<?php echo BASE_PATH ?>core/files/wysiwyg.png"></td>
				<td><img style="margin:5px" title="" src="<?php echo BASE_PATH ?>core/files/wysiwyg-update.png"></td>
			</tr>
		</tbody>
	</table>
</div>

<div id="tooltip-field_textarea" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/textarea/icon.png"><span class="tooltitle"><?php echo t('Text Field') ?></span></div>
	<p class="tooldef ellipsis"><?php echo 'This field holds any type of character with a maximum length of 4,294,967,295.'; ?></p>
	<div class="tooltype"> SQL Type : LONGTEXT by default</div>
	<table class="tooltab">
		<thead>
			<tr>
				<td>Display</td>
				<td>Add</td>
				<td>Update</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Development on the fly</td>
				<td><textarea></textarea>
				<td><textarea>Development on the fly</textarea>
			</tr>
		</tbody>
	</table>
</div>

<div id="tooltip-field_user" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/user/icon.png"><span class="tooltitle"><?php echo t('User Field') ?></span></div>
	<p class="tooldef ellipsis"><?php echo 'A User Field manages the relationship with user entity. It contains a registered user in Parsimony.'; ?></p>
	<div class="tooltype"> SQL Type : INT 11 by default</div>
	<table class="tooltab">
		<thead>
			<tr>
				<td>Display</td>
				<td>Add</td>
				<td>Update</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>1</td>
				<td><input type="text"></td>
				<td><input type="text" value="15"></td>
			</tr>
		</tbody>
	</table>
</div>

<div id="tooltip-field_ip" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/ip/icon.png"><span class="tooltitle"><?php echo t('IP Field') ?></span></div>
	<p class="tooldef ellipsis"><?php echo 'This field allows to store an IP address.'; ?></p>
	<div class="tooltype"> SQL Type : VARCHAR 45 by default</div>
	<table class="tooltab">
		<thead>
			<tr>
				<td>Display</td>
				<td>Add</td>
				<td>Update</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>172.16.254.1</td>
				<td><input type="text"></td>
				<td><input type="text" value="172.16.254.1"></td>
			</tr>
		</tbody>
	</table>
</div>

<div id="tooltip-field_vote" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/vote/icon.png"><span class="tooltitle"><?php echo t('Vote Field') ?></span></div>
	<p class="tooldef ellipsis"><?php echo 'This field is under construction.'; ?></p>
	<div class="tooltype"> SQL Type : FLOAT 20 by default</div>
	<table class="tooltab">
		<thead>
			<tr>
				<td>Display</td>
				<td>Add</td>
				<td>Update</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>0,12</td>
				<td><input type="text"></td>
				<td><input type="text" value="2,34"></td>
			</tr>
		</tbody>
	</table>
</div>

<div id="tooltip-field_formasso" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/formasso/icon.png"><span class="tooltitle"><?php echo t('N:N Association Form') ?></span></div>
	<p class="tooldef ellipsis"><?php echo 'N:N Association Form manages the display in the same form of two different entities connected with a N:N relationship.'; ?></p>
	<div class="tooltype"> SQL Type : VARCHAR by default</div>
	<table class="tooltab">
		<thead>
			<tr>
				<td>Display</td>
				<td>Add</td>
				<td>Update</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>My TAGS e.g.</td>
				<td><img style="margin:5px" title="" src="<?php echo BASE_PATH ?>core/files/n-n-relation-add.png"></td>
				<td><img style="margin:5px" title="" src="<?php echo BASE_PATH ?>core/files/n-n-relation.png"></td>
			</tr>
		</tbody>
	</table>
</div>

<div id="toolbar">
	<a href="#" onclick="setTimeout(function() {var ww = window.open(window.location, '_self');ww.close();}, 0);" style="padding:0;height:28px;">
		<img src="<?php echo BASE_PATH; ?>admin/img/parsimony.png">
	</a>
	<div class="toolbarbonus inline-block">
		<div class="floatleft" style="border-right: 1px solid #0c0c0c;padding-left: 20px;padding-right: 35px;">	
			<?php echo t('Connector'); ?>
			<form action="" method="POST" style="display:inline-block;margin: 0;">
				<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>">
				<select id="connectorchoice" name="connectorchoice" onchange="ParsimonyAdmin.setCookie('connectorchoice', this.value, 999);$(this).parent().trigger('submit');">
					<option>Bezier</option>
					<option <?php if (isset($_COOKIE['connectorchoice']) && $_COOKIE['connectorchoice'] == 'Flowchart') echo ' selected="selected"'; ?>>Flowchart</option>
				</select>
				<span style="padding-left: 35px;"><?php echo t('Current Module'); ?></span>
				<select id="currentModule" name="module" onchange="$(this).parent().trigger('submit');">
					<?php
					foreach (\app::$config['modules']['active'] as $moduleName => $module) {
						if ($moduleName == $_POST['module']) {
							$selected = 'selected = "selected"';
						} else {
							$selected = '';
						}
						if ($moduleName != 'admin')
							echo '<option ' . $selected . '>' . $moduleName . '</option>';
					}
					?>
				</select> 
			</form>
		</div>
		<div class="floatleft areaWrite" style="border-left: 1px solid #3c3c3c;padding-left: 35px;padding-right: 10px;">
			<?php echo t('Add an Entity'); ?>
			<form id="add_table" style="display:inline-block;margin: 0;">
				<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>">
				<input type="text" id="table_name" style="padding:1px;">
				<input type="submit" style="height: 19px;line-height: 12px;" value="<?php echo t('Add'); ?>"> 
			</form>
		</div>
		<div class="inline-block" style="position: absolute;right: 30px;top: 6px;">
			<input type="button" id="save" class="areaWrite" value="<?php echo t('Save model'); ?>" style="height: 22px;margin-top: 1px;" />
		</div>
	</div>
</div>
<div id="notify"></div>
<div id="container_bdd">
	<canvas id="outline" width="150" height="100"></canvas>
	<div id="conf_box_overlay" class="none ">
		<div id="popup" class="popup">   
			<div class="title_popup"><?php echo t('Cardinality'); ?>
				<span class="conf_box_close ui-icon ui-icon-closethick right"></span>
			</div>
			<div class="question"><input type="button" id="button1" value="✔">(1 <span class="entity2"></span> - &infin; <span class="entity1"></span>) -- <?php echo t('For 1'); ?> " <span class="entity2"></span>",<?php echo ' ' . t('are there several'); ?> " <span class="entity1"></span> " ?</div>
			<div class="question"><input type="button" id="button2" value="✔">(1 <span class="entity1"></span> - &infin; <span class="entity2"></span>) -- <?php echo t('For 1'); ?> " <span class="entity1"></span>",<?php echo ' ' . t('are there several'); ?> " <span class="entity2"></span> " ?</div>
			<div class="question"><input type="button" id="button3" value="✔">(&infin; <span class="entity1"></span> - &infin; <span class="entity2"></span>) -- <?php echo t('For several'); ?> " <span class="entity1"></span> " ,<?php echo ' ' . t('are there several'); ?> " <span class="entity2"></span> " ?</div>
		</div>
		<div id="popup2" class="popup" style="text-align: center;width:300px;">
			<div class="title_popup"><?php echo t('Link to another module'); ?>
				<span class="conf_box_close ui-icon ui-icon-closethick right"></span>
			</div>
			<div style="line-height: 30px;margin-top: 10px;color: #333;">Choose a table</div>
			<div style="margin:10px 0 20px">
				<select id="linkToExternal">
					<?php
					foreach (\app::$config['modules']['active'] as $moduleName => $module) {
						if ($moduleName != 'admin' && $moduleName != $_POST['module']) {
							foreach (\app::getModule($moduleName)->getModel() as $entityName => $entity) {
								echo '<option>' . $moduleName . ' - ' . $entityName . '</option>';
							}
						}
					}
					?>
				</select>
			</div>          
			<input type="button" id="btnLinkToExternal" value="<?php echo t('Do the Link'); ?>">
		</div>
	</div>
	<div id="leftsidebar" class="areaWrite">
		<div>
			<h2 data-tooltip="#tooltip-new-fields" class="tooltip hdb"><?php echo t('New Fields'); ?></h2>
			<div id="field_list">
				<?php
				function filterprops($val){ return $val !== NULL;};
				$aliasClasses = array_flip(\app::$aliasClasses);
				foreach ($aliasClasses AS $class => $alias) {
					if (strstr($alias, 'field_')) {
						if (!class_exists($alias))
							class_alias($class, $alias);
					}
				}
				$html = '';
				$classes = array_unique(get_declared_classes());
				foreach ($classes as $class) {
					if (is_subclass_of($class, 'field') ) {
						if (isset($aliasClasses[$class])) {
							$class = $aliasClasses[$class];
						}
						$field = new $class('');
						$fieldInfos = \tools::getClassInfos($field);
						$reflect = new ReflectionClass($class);
						$args = $reflect->getDefaultProperties();
						$args = array_filter($args, 'filterprops');
						unset($args['entity']);
						$args['oldName'] = $field->name;
						$args['required'] = (int) $args['required'];
						$args['name'] = '';
						if ($class == 'field_ident' || $class == 'field_foreignkey' || $class == 'field_alias')
							$none = ' style="display:none"';
						else
							$none = '';
						echo '<style>.property[type_class=' . $class . '],.myfield[type_class=' . $class . ']{background-image:url(' . BASE_PATH . str_replace('\\', '/', \app::$aliasClasses[$class]) . '/icon.png); }</style>';
						echo '<div type_class="' . $class . '" data-attributs=\'' . s(json_encode($args)) . '\' class="myfield ellipsis" ' . $none . '>' . t(ucfirst(s($fieldInfos['title'])), FALSE) . '<span class="tooltip ui-icon ui-icon-info" data-tooltip="#tooltip-' . $class . '"></span></div>';
						$html .= '<div id="update_' . $class . '">
<div class="rightbar"><label class="ellipsis">' . t('Name') . ' </label><input type="text" name="name">
<label class="ellipsis">' . t('Field') . ' </label><div class="inline-block" style="position:relative;top:3px">' . ucfirst(substr(strstr(strrchr(get_class($field), '\\'), '_'), 1)) . '</div>    
</div>
<div><h3>' . t('SQL Properties') . '</h3>
	<div class="rightbar"><label class="ellipsis">' . t('Type') . ' </label><div class="inline-block" style="position:relative;top:3px"><input type="hidden" name="type">' . $field->type . '</div></div>
	<div class="rightbar" style="clear: both;"><label class="ellipsis">' . t('Max Characters') . ' </label><input type="text" name="characters_max"></div>
	<div class="rightbar"><label class="ellipsis">' . t('Min Characters') . ' </label><input type="text" name="characters_min"></div>
</div>
<div><h3>' . t('Form View') . '</h3>
<div  class="rightbar"><label class="ellipsis">' . t('Label') . ' </label><input type="text" name="label"></div>
<div class="rightbar"><label class="ellipsis">' . t('Text help') . ' </label><input type="text" name="text_help"></div>
<div class="rightbar"><label class="ellipsis">' . t('Error Message') . '</label><input type="text" name="msg_error"></div>
<div class="rightbar"><label class="ellipsis">' . t('Default Values') . '</label><input type="text" name="default"></div>
<div class="rightbar"><label class="ellipsis">' . t('Required') . '</label><select style="font-size:13px;height:26px" name="required"><option value="1">' . t('True') . '</option><option value="0">' . t('False') . '</option></select></div>
<div class="rightbar"><label class="ellipsis">' . t('Regex') . '</label><input type="text" name="regex"></div>
<div class="rightbar" style="padding:5px 10px 10px 10px;">
<div style="padding:3px 0px;">
' . t('In which form display the field ?') . '
</div>
<div class="visibilityform">
	<input data-form="form-display" checked="checked" type="checkbox" value="1">
	<span class="ellipsis" for="display" style="width:70px;display:inline-block">' . t('Display') . '</span>
	<input data-form="form-add" checked="checked" type="checkbox" value="2">
	<span class="ellipsis" for="add" style="width:70px;display:inline-block;">' . t('Add') . '</span>
	<input type="checkbox" checked="checked" value="4" data-form="form-update">
	<span class="ellipsis" for="update" style="width:70px;display:inline-block;">' . t('Update') . '</span>
	<input type="hidden" name="visibility">
</div>
</div>
</div>';
						if (is_file('modules/' . str_replace('\\', '/', \app::$aliasClasses[$class]) . '/admin.php')) {
							$html .= '<fieldset class="specialprop"><h3>' . t('Specials properties') . '</h3>';
							ob_start();
							include('modules/' . str_replace('\\', '/', \app::$aliasClasses[$class]) . '/admin.php');
							$html .= ob_get_clean();
							$html .= '</fieldset>';
						}
						$html .= '<input type="hidden" name="oldName"><input type="submit" class="save_field areaWrite" value="' . t('Save property') . '" style="width: 50%;margin: 5px 0 10px 25%;"></div>';
					}
				}
				?>
			</div>
		</div>
	</div>
	<div id="canvas">
		<?php
		$oldSchema = array();
		foreach ($moduleObj->getModel() as $entityName => $entity) {
			$oldSchema[$entityName] = array();
			$reflect = new ReflectionClass('\\' . $_POST['module'] . '\\model\\' . $entityName);
			$className = $reflect->getShortName();
			$modelInfos = \tools::getClassInfos($reflect);
			$tab = array('name' => $className, 'title' => $entity->getTitle(), 'oldName' => $className, 'behaviorTitle' => $entity->behaviorTitle, 'behaviorDescription' => $entity->behaviorDescription, 'behaviorKeywords' => $entity->behaviorKeywords, 'behaviorImage' => $entity->behaviorImage);
			echo '<div class="table" data-attributs=\'' . s(json_encode($tab)) . '\' id="table_' . $className . '" style="top:' . $modelInfos['top'] . ';left:' . $modelInfos['left'] . ';"><div class="title">' . $className . '</div>';
			$parameters = $entity->getFields();
			foreach ($parameters as $propertyName => $field) {
				$oldSchema[$entityName][$propertyName] = '';
				$class = get_class($field);
				if (isset($aliasClasses[$class])) {
					$class = $aliasClasses[$class];
				}
				$reflect = new ReflectionClass($field);
				$params = $reflect->getDefaultProperties();
				$args = array();
				foreach ($params as $name => $defaultValue) {
					$args[$name] = $field->$name;
				}
				unset($args['fieldPath']);
				unset($args['entity']);
				unset($args['value']);
				$args['required'] = (int) $args['required'];
				$args['oldName'] = $field->name;
				echo '<div class="property" id="property_' . $className . '_' . $propertyName . '" data-attributs=\'' . s(json_encode($args)) . '\' type_class="' . $class . '">' . $propertyName . '</div>';
			}
			echo '</div>';
		}
		?>
	</div>
	<div id="rightsidebar" style="z-index:999">
		<div id="update_table">
			<h2 class="hdb"><span class="closeformpreview ui-icon ui-icon-circle-close" style="display: inline-block;left: 15px;position: absolute;top: 11px;background-image: url(<?php echo BASE_PATH; ?>admin/img/icons.png);"></span><?php echo t('Table Settings') ?></h2>
			<div class="rightbar"><label class="ellipsis"><?php echo t('Name'); ?> </label><input type="text" name="name"><input type="hidden" name="oldName"></div>
			<div class="rightbar"><label class="ellipsis"><?php echo t('Title'); ?> </label><input type="text" name="title"></div>
			<div><h3><?php echo t('Fields Behaviour'); ?></h3>
				<div class="rightbar"><label class="ellipsis"><?php echo t('Title'); ?> </label><select class="behaviorProperty" name="behaviorTitle"></select></div>
				<div class="rightbar"><label class="ellipsis"><?php echo t('Description'); ?> </label><select class="behaviorProperty" name="behaviorDescription"></select></div>
				<div class="rightbar"><label class="ellipsis"><?php echo t('Keywords'); ?></label><select class="behaviorProperty" name="behaviorKeywords"></select></div>
				<div class="rightbar"><label class="ellipsis"><?php echo t('Image'); ?></label><select class="behaviorProperty" name="behaviorImage"></select></div>
				<input type="submit" class="save_table areaWrite" value="<?php echo t('Validate'); ?>" style="width: 50%;margin: 5px 0 10px 25%;">
			</div>
		</div>
		<div id="update_field">
			<h2 class="hdb"><span class="closeformpreview ui-icon ui-icon-circle-close" style="display: inline-block;left: 15px;position: absolute;top: 15px;background-image: url(img/icons.png);"></span><?php echo t('Field Settings') ?></h2>
			<?php echo $html; ?>
		</div>
	</div>
	<span id="deletator" class="ui-icon ui-icon-closethick"></span>
</div>
<script>
	var oldSchema = '<?php echo json_encode($oldSchema) ?>';
	function enc(str) {
		if(str != null){ /* for ex : behaviorTitle, etc.. */
			return str.toString().replace('"', '\\"');
		}else{
			return "";
		}
	}
	$(document).on("change", '.visibilityform input[type="checkbox"]', function(e) {
		var nb = 0;
		var parent = $(this).parent();
		$('input:checked', parent).each(function() {
			nb += parseInt($(this).val());
		});
		$('input[name="visibility"]', parent).val(nb);
	});

	var connectorchoice = $("#connectorchoice").val();
	var dbadmin = {
		marqueur: false,
		endpointOptions: {endpoint: ["Dot", {radius: 12}],
			paintStyle: {fillStyle: '#1b74a4'},
			isSource: true,
			reattach: true,
			maxConnections: 100,
			connector: [connectorchoice, (200)],
			dragAllowedWhenFull: true,
			connectorStyle: {strokeStyle: "#1b74a4", position: "absolute", lineWidth: 2},
			isTarget: false},
		endpointOptions2: {endpoint: ["Dot", {radius: 12}],
			paintStyle: {fillStyle: "transparent"},
			dropOptions: {activeClass: 'dragActive'},
			isSource: false,
			reattach: true,
			maxConnections: 100,
			dragAllowedWhenFull: true,
			isTarget: true},
		endpointOptions3: {endpoint: ["Dot", {radius: 8}],
			paintStyle: {fillStyle: '#44c5ec'},
			isSource: false,
			connectorStyle: {strokeStyle: "#44c5ec", position: "absolute", lineWidth: 2},
			isTarget: false},
		keywordsReserveds: ",this,__halt_compiler,abstract,and,array,as,break,callable,case,catch,class,clone,const,continue,declare,default,die,do,echo,else,elseif,empty,enddeclare,endfor,endforeach,endif,endswitch,endwhile,eval,exit,extends,final,for,foreach,function,global,goto,if,implements,include,include_once,instanceof,insteadof,interface,isset,list,namespace,new,or,print,private,protected,public,require,require_once,return,static,switch,throw,trait,try,unset,use,var,while,xor,Compile-time constants,__CLASS__,__DIR__,__FILE__,__FUNCTION__,__LINE__,__METHOD__,__NAMESPACE__,__TRAIT__,",
		buildLink: function(sourceModule, source, targetModule, target) {
			//var objSource = $("#table_" + source);
			var objTarget = $("#table_" + target);
			var champ = $("#field_list div[type_class='field_foreignkey']").clone();
			var predictedname = "id_" + source;
			var n = 0;
			while ($('#property_' + target + '_' + predictedname).length || predictedname == "id_" + target) { /* 2nd condition for link to external module, ex: user to core user */
				n++;
				if (n > 1) {
					predictedname = predictedname.substring(0, predictedname.length - 2) + '_' + n;
				} else {
					predictedname += '_' + n;
				}
			}
			var jsonproperties = jQuery.parseJSON(JSON.stringify($("#field_list div[type_class='field_foreignkey']").data("attributs")));
			jsonproperties.name = predictedname;
			jsonproperties.label = predictedname;
			jsonproperties.moduleLink = sourceModule;
			jsonproperties.link = source;
			var fieldString = $("#table_" + source + ' .property[type_class="field_string"]:first');
			if (fieldString.length > 0)
				jsonproperties.templatelink = '%' + fieldString.text() + '%';
			else
				jsonproperties.templatelink = '%id_' + source + '%';
			jsonproperties.entity = target;
			champ.removeAttr('class').data("attributs", jsonproperties).text(predictedname);
			champ.attr("name", source);
			champ.attr("id", 'property_' + target + '_' + predictedname).addClass("property");
			champ.appendTo(objTarget).show();
			dbadmin.createConnector(champ[0]);
			dbadmin.refreshUI();
		},
		createTable: function(tablename) {
			if (tablename.length > 0) {
				if (dbadmin.keywordsReserveds.indexOf("," + tablename + ",") == -1) {
					if (!$('#table_' + tablename).length) {
						$("#canvas").append('<div id="table_' + tablename + '" data-attributs=\'{"name":"' + tablename + '","oldName":"' + tablename + '","title":"' + tablename + '","behaviorTitle":"","behaviorDescription":"","behaviorKeywords":"","behaviorImage":""}\' class="table new" style="left:300px;top:50px;"><div class="title">' + tablename + '</div><div type_class="field_ident">' + t('ID') + '</div></div>');
						var myID_champ = "property_" + tablename + "_id_" + tablename;
						var table_name = tablename;
						var jsonproperties = jQuery.parseJSON(JSON.stringify($("#field_list div[type_class='field_ident']").data("attributs")));
						jsonproperties.entity = table_name;
						jsonproperties.name = "id_" + table_name;
						jsonproperties.label = "Id " + table_name;
						var champ = $('#table_' + tablename + ' div[type_class="field_ident"]');
						champ.attr("id", myID_champ).attr("type_class", "field_ident").addClass("property new").text("id_" + table_name);
						champ.data("attributs", jsonproperties);
						dbadmin.createAnchor(champ[0].id);
						dbadmin.createAnchorForeignKey("table_" + tablename);
						dbadmin.refreshUI();
					} else {
						ParsimonyAdmin.notify(t('The Entity') + ' ' + tablename + ' ' + t('already exists'), 'negative');
					}
				} else {
					ParsimonyAdmin.notify(t('This word') + ' ' + tablename + ' ' + t('belongs to a list of Reserved Words, Please Choose another'), 'negative') + '.';
				}
			} else {
				ParsimonyAdmin.notify(t('Enter a Name of Entity'), 'negative');
			}
		},
		init: function() {
			/* Tooltip */
			$(".tooltip").parsimonyTooltip({triangleWidth: 5});

			$(window).bind("beforeunload", function(event) {
				if ($("#save").hasClass("haveToSave"))
					return t("You have unsaved changes");
			});

			/* JsPlumb */
			jsPlumb.importDefaults({
				Container: $("#canvas"),
				DragOptions: {zIndex: 2000}
			});

			/* Save field settings */
			$("#update_field").on('click', '.save_field', function() {
				if ($('#update_' + current_update_field.attr('type_class') + ' input[name="name"]').val() != $('#update_' + current_update_field.attr('type_class') + ' input[name="oldName"]').val()) {
					if (!confirm(('Your Attention Please : If you change the name of the property, you will break all your database queries already done with the old name.'))) {
						return false;
					}
				}
				var json = '{';
				$("#update_" + current_update_field.attr('type_class') + " input[name],#update_" + current_update_field.attr('type_class') + " select[name]").each(function() {
					json += '"' + $(this).attr('name') + '":"' + $(this).val().replace(/"/g, '\\"').replace(/\\/g, '\\\\') + '",';
				});
				var obj = jQuery.parseJSON(json.substring(0, json.length - 1) + "}");
				if (current_update_field.hasClass("new"))
					obj.oldName = obj.name;
				current_update_field.data("attributs", obj);
				$("#deletator").prependTo($("body"));
				current_update_field.text(obj.name);
				$(this).parent().hide();
				$("#rightsidebar").hide();
				$("#save").addClass("haveToSave");
			});

			/* Save table Settings */
			$("#update_table").on('click', '.save_table', function() {
				var oldName = $('#update_table input[name="oldName"]').val();
				var newName = $('#update_table input[name="name"]').val();
				if (dbadmin.keywordsReserveds.indexOf("," + newName + ",") == -1) {
					if ($('#table_' + newName).length == 0 || newName == oldName) {
						if (newName != oldName) {
							if (!confirm(('Your Attention Please : If you change the name of the table, you will break all your database queries already done with the old name.'))) {
								return false;
							}
							/* we change the entity name of all his properties */
							$(".property", current_update_table).each(function() {
								var attrs = $(this).data("attributs");
								attrs.entity = newName;
								$(this).data("attributs", attrs);
							});
							/* we change the link entity name for all foreign keys that link to this table */
							$('.property[type_class="field_foreignkey"]').each(function() {
								var attrs = $(this).data("attributs");
								if (attrs.link == oldName)
									attrs.link = newName;
								$(this).data("attributs", attrs);
							});
						}
						var json = '{';
						$("#update_table input[name],#update_table select[name]").each(function() {
							json += '"' + $(this).attr('name') + '":"' + $(this).val().replace(/"/g, '\\"') + '",';
						});
						var obj = jQuery.parseJSON(json.substring(0, json.length - 1) + "}");
						if (current_update_table.hasClass("new"))
							obj.oldName = obj.name;
						current_update_table.data("attributs", obj);
						$("#deletator").prependTo($("body"));
						current_update_table.find(".title").text(obj.name);
						$(this).parent().parent().hide();
						$("#rightsidebar").hide();
						$("#save").addClass("haveToSave");
					} else {
						ParsimonyAdmin.notify(t('The Entity') + ' ' + newName + ' ' + t('already exists'), 'negative');
					}
				} else {
					ParsimonyAdmin.notify(t('This word') + ' ' + newName + ' ' + t('belongs to a list of Reserved Words, Please Choose another'), 'negative') + '.';
				}
			});

			/* Open Table Settings */
			$('#canvas').on('click', '.title', function() {
				$('#update_field, #update_field > div').hide();
				$('#rightsidebar, #update_table').show();
			})

			/* Delete Table */
			.on('click', '#deletator', function() {
				obj = $(this).parent();
				if (obj.hasClass('native')) {
					alert(t("This is a native object, you don't have the permissions to delete it."));
					return false;
				} else {
					if (obj.hasClass('table')) {
						if (confirm(t('Are you sure to delete this entity ?'))) {
							$(this).appendTo($('body'));
							jsPlumb.removeAllEndpoints(obj.attr('id'));
							$('.property', obj).each(function(index) {
								jsPlumb.removeAllEndpoints(this.id);
							});
							$('#canvas div[type_class="field_foreignkey"]').each(function(index) {
								var name = $(".title", obj).text();
								if ($(this).data("attributs").link == name) {
									jsPlumb.removeAllEndpoints(this.id);
									$(this).remove();
								}
							});
							obj.remove();
							dbadmin.refreshUI();
						}
					} else if (obj.hasClass('property')) {
						if (confirm(t('Are you sure to delete this property ?'))) {
							$(this).appendTo($('body'));
							jsPlumb.removeAllEndpoints(obj.attr('id'));
							obj.remove();
						}
					}
					$("#save").addClass("haveToSave");
				}
			})

			/* Show delete buttons on fields */
			.on('mouseover mouseout', '.property', function(event) {
				event.stopPropagation();
				if (this.classList.contains("native"))
					return false;
				var deletator = document.getElementById("deletator");
				if (event.type == 'mouseover') {
					if (this.getAttribute('type_class') != 'field_ident') {
						deletator.style.display = "block";
						this.insertBefore(deletator, this.firstChild);
					}
				} else {
					deletator.style.display = "none";
				}
			})

			/* Show delete buttons on tables */
			.on('mouseover mouseout', '.table', function(event) {
				if (this.classList.contains("native"))
					return false;
				var deletator = document.getElementById("deletator");
				if (event.type == 'mouseover') {
					deletator.style.display = "block";
					this.insertBefore(deletator, this.firstChild);
				} else {
					document.getElementById("deletator").style.display = "none";
				}
			});

			var current_update_field;
			var current_update_table;

			/* Shortcut : Save on CTRL+S */
			document.addEventListener("keydown", function(e) {
				if (e.keyCode == 83 && e.ctrlKey) {
					e.preventDefault();
					$("#save").trigger("click");
				}
			}, false);

			$(document).on('click', '.conf_box_close', function() {
				$(this).closest(".popup").hide();
				$('#conf_box_overlay').hide();
			})
			.on('click', '.closeformpreview', function() {
				$("#rightsidebar").hide();
				$(this).parent().parent().hide();
			})
			.on('mousedown', '._jsPlumb_endpoint', function() {
				document.getElementById("update_field").style.display = "none";
				document.getElementById("update_table").style.display = "none";
			})
			/* Filter Table Name */
			.on('keyup', "#table_name", function() {
				this.value = this.value.toLowerCase().replace(/[^a-z_]+/, "");
			})
			/* Open and load field Settings */
			.on('click', ".table .property", function() {
				$('#rightsidebar, #update_field').show();
				$('#update_table').hide();
				current_update_field = $(this);
				$(".current_property").removeClass("current_property");
				current_update_field.addClass("current_property");
				var parent = $('#update_' + current_update_field.attr('type_class'));
				$.each($(this).data("attributs"), function(i, item) {
					if (item === false)
						item = 0;
					$('[name=' + i + ']', parent).val(item);
					if (i == 'visibility') {
						if (item & 1)
							$('input[data-form="form-display"]', parent).attr('checked', 'checked');
						else
							$('input[data-form="form-display"]', parent).removeAttr('checked');
						if (item & 2)
							$('input[data-form="form-add"]', parent).attr('checked', 'checked');
						else
							$('input[data-form="form-add"]', parent).removeAttr('checked');
						if (item & 4)
							$('input[data-form="form-update"]', parent).attr('checked', 'checked');
						else
							$('input[data-form="form-update"]', parent).removeAttr('checked');
					}
				});
				if (this.classList.contains("native"))
					$('input[name="name"]', parent).attr('disabled', 'disabled');
				else
					$('input[name="name"]', parent).removeAttr('disabled');
				$('#update_field > div').hide();
				$('#update_' + current_update_field.attr('type_class')).show();
			})

			/* Open and load table Settings */
			.on('click', ".table", function() {
				current_update_table = $(this);
				$(".current_update_table").removeClass("current_update_table");
				current_update_table.addClass("current_update_table");
				/* Fill properties allowed for behavior properties */
				var select = '<option></option>';
				$.each($(".property", this), function() {
					select += "<option>" + this.textContent + "</option>";
				});
				$(".behaviorProperty").html(select);
				/* Fill form with current attributs */
				$.each($(this).data("attributs"), function(i, item) {
					$('#update_table [name=' + i + ']').val(item);
				});
				if (this.classList.contains("native"))
					$('#update_table input[name="name"]').attr('disabled', 'disabled');
				else
					$('#update_table input[name="name"]').removeAttr('disabled');
				thumb.draw();
			})
			/* Save all models */
			.on('click', '#save', function() {
				var propertylist = '[';
				$(".table").each(function() {
					var recupId = $(".title", this).text();
					var tableAttrs = $(this).data("attributs");
					propertylist += '{"name": "' + enc(recupId) + '","oldName": "' + enc(tableAttrs.oldName) + '","title":"' + enc(tableAttrs.title) + '","behaviorTitle":"' + enc(tableAttrs.behaviorTitle) + '","behaviorDescription":"' + enc(tableAttrs.behaviorDescription) + '","behaviorKeywords":"' + enc(tableAttrs.behaviorKeywords) + '","behaviorImage":"' + enc(tableAttrs.behaviorImage) + '","top": "' + $(this).css("top") + '","left": "' + $(this).css("left") + '","properties" : {';
					$(".property", $(this)).each(function() {
						var jsonproperties = $(this).data("attributs");
						propertylist += '"' + enc(jsonproperties.name) + ':' + $(this).attr("type_class") + '" :' + JSON.stringify(jsonproperties) + ' ,';
					});
					propertylist = propertylist.substring(0, propertylist.length - 1) + '}},';
				});
				propertylist = propertylist.substring(0, propertylist.length - 1) + ']';
				$.post('saveModel', {TOKEN: '<?php echo TOKEN; ?>', module: '<?php echo $_POST['module'] ?>', list: propertylist, oldSchema: oldSchema}, function(data) {
					ParsimonyAdmin.notify(t('New Data Model has been Saved') + data, "positive");
					$(".new").removeClass("new");
				});
				$("#save").removeClass("haveToSave");
			})
			/* Choose behavior of the link */
			.on('click', '#popup input', function() {
				var source1 = $("#" + $(this).data("sourceid"));
				var target1 = $("#" + $(this).data("targetid"));
				var entitySource = source1.parent().find(".title").text();
				var entityTarget = $(".title", target1).text();
				var module = $("#currentModule").val();
				if (this.id == "button3") {
					var t = entitySource + '_' + entityTarget;
					dbadmin.createTable(t);
					dbadmin.buildLink(module, entitySource, module, t);
					dbadmin.buildLink(module, entityTarget, module, t);
				} else {
					if (this.id == "button2") {
						source = source1;
						target = target1;
					} else {
						source = target1.find("div[type_class='field_ident']");
						target = source1.parent();
					}
					var entitySource = source.parent().find('.title').text();
					var entityTarget = $('.title', target).text();
					dbadmin.buildLink(module, entitySource, module, entityTarget);
				}
				$("#popup,#conf_box_overlay").hide();
				$("#save").addClass("haveToSave");
				dbadmin.refreshUI();
			})
			/* Choose behavior of the link */
			.on('click', '#btnLinkToExternal', function() {
				if ($("#linkToExternal").val()) {
					var module = $("#currentModule").val();
					var source1 = $("#" + $(this).data('sourceid'));
					var entitySource = source1.parent().find('.title').text();
					var ref = $("#linkToExternal").val().toString().split(" - ");
					dbadmin.buildLink(ref[0], ref[1], module, entitySource);
					$(this).closest(".popup").hide();
					$('#conf_box_overlay').hide();
					$("#save").addClass("haveToSave");
					dbadmin.refreshUI();
				} else {
					alert(t("Please choose the linked table"));
				}

			})
			/* Filter Table Name */
			.on('keyup', "#table_name", function() {
				this.value = this.value.toLowerCase().replace(/[^a-z_]+/g, "");
			});

			/* Sort properties */
			$("#canvas .table").sortable({items: ".property[type_class!='field_ident']"});
			$("#field_list > div").draggable({zIndex: 2700, revert: true, helper: "clone"});

			/* Add a Table */
			$("#toolbar").on('submit', '#add_table', function(e) {
				e.preventDefault();
				dbadmin.createTable($("#table_name").val());
				$("#save").addClass("haveToSave");
			});

			/* Draw Anchor on fields ident */
			$(".property[type_class='field_ident']").each(function(index) {
				dbadmin.createAnchor(this.id);
			});

			/* Draw Anchor on fields foreignKey */
			$(".table").each(function() {
				dbadmin.createAnchorForeignKey(this.id);
			});

			jsPlumb.makeTarget("extLink", {isTarget: true, paintStyle: {fillStyle: "transparent"}, dropOptions: {activeClass: 'dragActive2'}});

			/* Draw connectors between tables */
			$("#canvas div[type_class='field_foreignkey']").each(function(index) {
				dbadmin.createConnector(this);
			});

			/* When a connector is linked */
			jsPlumb.bind("beforeDrop", function(event, originalEvent) {
				if (event.targetId == "extLink") {
					jsPlumb.removeAllEndpoints("extLink");
					$("#popup2,#conf_box_overlay").show();
					$("#btnLinkToExternal").data('sourceid', event.sourceId);
					return true;
				}
				$("#popup input").data('sourceid', event.sourceId);
				$("#popup input").data('targetid', event.targetId);
				$("#popup .entity1").text(event.connection.source.parent().find('.title').text());
				$("#popup .entity2").text(event.connection.target.find('.title').text());
				if (event.connection.source.parent().attr('id') == event.targetId) {
					$('#button1').trigger('click');
				} else {
					$("#popup,#conf_box_overlay").show();
				}
			});

			/* When a connector is cliqued*/
			jsPlumb.bind("click", function(connection, originalEvent) {
				if (confirm(t('Delete connection from') + ' ' + connection.source.parent().find(".title").text() + ' ' + t('to') + ' ' + connection.target.parent().find(".title").text() + " ?")) {
					jsPlumb.detach(connection);
					jsPlumb.removeAllEndpoints(connection.sourceId);
					//$("#" + connection.sourceId).remove();
				}
			});

			dbadmin.refreshUI();
		},
		//	    updateFormPreview :   function(){
		//		$.post("action",'TOKEN=' + TOKEN + '&action=getPreviewAddForm&module=<?php //echo $_POST['module']  ?>&model=' + $(".current_property").closest(".table").find(".title").text() ,function(data){
		//		    $("#preview_form .content").html(data);                 
		//		});
		//	    },
		createAnchor: function(myID) {
			myEndpoint = jsPlumb.addEndpoint(myID, $.extend({anchor: ["LeftMiddle", "RightMiddle"], uuid: myID + "_uuid"}, dbadmin.endpointOptions));
		},
		createAnchorForeignKey: function(myID) {
			jsPlumb.addEndpoint(myID, $.extend({anchor: ["BottomRight", "TopRight"], uuid: myID + "_uuid"}, dbadmin.endpointOptions2));
		},
		createAnchorNewForeignKey: function(myID) {
			jsPlumb.addEndpoint(myID, $.extend({anchor: ["LeftMiddle", "RightMiddle"], uuid: myID + "_uuid"}, dbadmin.endpointOptions3));
		},
		createConnector: function(elmt) {
			var jsonproperties = $(elmt).data("attributs");
			dbadmin.createAnchorNewForeignKey(elmt.id);
			if ($("#table_" + jsonproperties.link).length > 0) {
				jsPlumb.connect({uuids: [elmt.id + "_uuid", $("#table_" + jsonproperties.link + " div[type_class='field_ident']").attr("id") + "_uuid"],
					paintStyle: {lineWidth: 3, strokeStyle: '#259BDB'},
					hoverPaintStyle: {lineWidth: 3, strokeStyle: '#259BDB'},
					detachable: false,
					deleteEndpointsOnDetach: false,
					overlays: [
						["Arrow", {location: 0.4, paintStyle: {fillStyle: '#259bdb', strokeStyle: "rgba(255,255,255,0)"}}],
						["Label", {cssClass: "component", font: "12px sans-serif", label: "<span class=\"connection\">" + $(elmt).parent().find('.title').text() + "</span>" + ' ' + t('to') + ' : ' + "<span class=\"connection\">" + $("#table_" + jsonproperties.link + " div[type_class='field_ident']").parent().find('.title').text() + "</span> "}]
					]
				});
			}
		},
		refreshUI: function() {

			/* Allows to drag tables */
			jsPlumb.draggable($(".table"), {
				cursor: 'move',
				handle: 'div.title',
				containment: '#canvas',
				drag: function(event, ui) {
					thumb.draw();
				}, stop: function() {
					jsPlumb.repaint($(".property", this).add(this).toArray());
				}
			});

			/* Allows to drop fields in table */
			$(".table").droppable({
				accept: '#field_list div',
				activeClass: 'ui-state-hover',
				hoverClass: 'ombre',
				drop: function(event, ui) {
					var champ = ui.draggable.clone();
					var nom_champ = prompt(t('Please enter a field name') + ' ?');
					if (nom_champ != "this" && nom_champ != null) {
						nom_champ = nom_champ.toLowerCase().replace(/[^a-z_]+/g, "");
						if (nom_champ != "") {
							var id = "property_" + event.target.id.substring(6) + "_" + nom_champ;
							if (!$('#' + id).length) {
								var champ = ui.draggable.clone();
								champ.removeAttr('class').attr("id", id).addClass("property new");
								jsonproperties = jQuery.parseJSON(JSON.stringify(ui.draggable.data("attributs")));
								jsonproperties.entity = $(this).find('.title').text();
								jsonproperties.name = nom_champ;
								jsonproperties.oldName = nom_champ;
								jsonproperties.label = nom_champ;
								champ.data("attributs", jsonproperties);
								champ.text(nom_champ);
								champ.appendTo(this);

								$("#canvas .table").sortable({items: ".property[type_class!='field_ident']"});
								$("#save").addClass("haveToSave");
							} else {
								ParsimonyAdmin.notify(t('The property') + ' ' + nom_champ + ' ' + t('already exists'), 'negative');
							}
						}
					} else {
						ParsimonyAdmin.notify(t('This word') + ' ' + nom_champ + ' ' + t('belongs to a list of Reserved Words, Please Choose another'), 'negative') + '.';
					}
				}
			});

		}
	};
	$(document).ready(function() {
		if (navigator.userAgent.toLowerCase().indexOf('firefox') > -1)
			$.extend($.ui.draggable.prototype.options, {scroll: false}); // firefox fix
		dbadmin.init();
		thumb.draw();
	});




	function Fraction(outlineID, settings, viewportID) {

		/* Init */
		this.ratio;
		this.settings = settings;
		this.canvas = document.getElementById(outlineID);
		this.viewport = document.getElementById(viewportID);
		this.container = document.body;
		this.dragging = false;
		this.ctx = this.canvas.getContext("2d");

		/* Dimmenssions */
		this.height = this.canvas.getAttribute("height");
		this.viewportCoords = this.viewport.getBoundingClientRect();
		this.ratio = this.height / this.viewportCoords.height;
		this.width = this.viewportCoords.width * this.ratio;
		this.canvas.setAttribute("width", this.width);

		/* Events */
		window.addEventListener("scroll", this.draw.bind(this), false);
		this.canvas.addEventListener("mousedown", this.mousedown.bind(this), false);
	}

	Fraction.prototype.draw = function() {

		this.viewportCoords = this.viewport.getBoundingClientRect();

		/* Prepare to draw : clear and set background */
		this.ctx.clearRect(0, 0, this.width, this.height);
		this.ctx.fillStyle = this.settings.containerStyle.fillStyle;
		this.ctx.fillRect(0, 0, this.width, this.height);

		/* Draw parts */
		for (var i = 0, len = this.settings.parts.length; i < len; i++) {
			var part = document.querySelectorAll(this.settings.parts[i].selector);
			this.ctx.fillStyle = this.settings.parts[i].fillStyle;
			for (var j = 0, lenT = part.length; j < lenT; j++) {
				var coords = part[j].getBoundingClientRect();
				this.ctx.fillRect((coords.left - this.viewportCoords.left) * this.ratio, (coords.top - this.viewportCoords.top) * this.ratio, coords.width * this.ratio, coords.height * this.ratio);
			}
		}

		/* Draw viewport scroll */
		if (this.dragging == true)
			this.ctx.fillStyle = this.settings.viewportDragStyle.fillStyle;
		else
			this.ctx.fillStyle = this.settings.viewportStyle.fillStyle;
		this.ctx.fillRect(this.container.scrollLeft * this.ratio, this.container.scrollTop * this.ratio, this.container.offsetWidth * this.ratio, this.container.offsetHeight * this.ratio);
	}

	Fraction.prototype.mousedown = function(e) {
		this.dragging = true;
		var canvasCoords = this.canvas.getBoundingClientRect();
		this.container.scrollTop = (e.clientY - canvasCoords.top - (this.container.offsetHeight * this.ratio / 2)) / this.ratio;
		this.container.scrollLeft = (e.clientX - canvasCoords.left - (this.container.offsetWidth * this.ratio / 2)) / this.ratio;

		this.Yscroll = this.container.scrollTop;
		this.Xscroll = this.container.scrollLeft;
		this.clientY = e.clientY;
		this.clientX = e.clientX;
		this.mousemoveCallBack = this.mousemove.bind(this);
		this.mouseupCallBack = this.mouseup.bind(this);
		this.container.addEventListener("mousemove", this.mousemoveCallBack, false);
		this.container.addEventListener("mouseup", this.mouseupCallBack, false);
	}

	Fraction.prototype.mousemove = function(e) {
		this.container.scrollTop = this.Yscroll - ((this.clientY - e.clientY) / this.ratio);
		this.container.scrollLeft = this.Xscroll - ((this.clientX - e.clientX) / this.ratio);
	}

	Fraction.prototype.mouseup = function(e) {
		this.dragging = false;
		this.container.removeEventListener("mousemove", this.mousemoveCallBack, false);
		this.container.removeEventListener("mouseup", this.mouseupCallBack, false);
		this.draw();
	}

	var thumb = new Fraction("outline", {parts: [{
				selector: ".table",
				fillStyle: "#2E63A5"
			}, {
				selector: ".current_update_table",
				fillStyle: "red"
			}],
		containerStyle: {fillStyle: "rgba(104,169,255,0.1)"},
		viewportStyle: {fillStyle: "rgba(104,169,255,0.4)"},
		viewportDragStyle: {fillStyle: "rgba(104,169,255,0.7)"}}, "container_bdd");
</script>
