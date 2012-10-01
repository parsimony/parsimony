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
if (!\app::getClass('user')->VerifyConnexion())
    exit;
if (!isset($_POST['module']))
    $_POST['module'] = 'core';

include_once('modules/core/classes/field.php');
?>
<link rel="stylesheet" href="<?php echo BASE_PATH; ?>lib/cms.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo BASE_PATH; ?>admin/style.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo BASE_PATH; ?>lib/tooltip/parsimonyTooltip.css" type="text/css" media="all" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js" type="text/javascript"></script>
<script>window.jQuery || document.write('<script src="' + BASE_PATH + 'lib/jquery/jquery-1.8.1.min.js"><\/script>')</script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.js" type="text/javascript"></script>
<script>typeof jQuery.ui != 'undefined' || document.write('<script src="' + BASE_PATH + 'lib/jquery-ui/jquery-ui-1.8.23.min.js"><\/script>')</script>
<script type="text/javascript">
    var BASE_PATH = '<?php echo BASE_PATH ?>';
    var MODULE = '<?php echo MODULE ?>';
    var THEME = '<?php echo THEME ?>';
    var THEMETYPE = '<?php echo THEMETYPE ?>';
    var THEMEMODULE = '<?php echo THEMEMODULE ?>';
    var TOKEN = '<?php echo TOKEN ?>';
</script>
<script type="text/javascript" src="<?php echo BASE_PATH; ?>lib/jsPlumb/jquery.jsPlumb-1.3.12-all-min.js"></script>
<script type="text/javascript" src="<?php echo BASE_PATH; ?>lib/fracs/jquery.fracs-0.11.min.js"></script>
<script type="text/javascript" src="<?php echo BASE_PATH; ?>lib/fracs/jquery.outline-0.11.min.js"></script>
<script type="text/javascript" src="<?php echo BASE_PATH; ?>lib/tooltip/parsimonyTooltip.js"></script>
<script type="text/javascript" src="<?php echo BASE_PATH; ?>admin/script.js"></script>
<script type="text/javascript" src="<?php echo BASE_PATH; ?>cache/<?php echo app::$request->getLocale(); ?>-lang.js"></script>
<style>.ui-state-disabled, .ui-widget-content .ui-state-disabled { opacity: .85; filter:Alpha(Opacity=85); background-image: none; }
</style>
<style type="text/css">
    .ui-icon { width: 16px; height: 16px;background-color:transparent; background-image: url(<?php echo BASE_PATH; ?>admin/img/icons.png);display: block;overflow: hidden;}
    body{margin:0;padding:0;font-family:verdana;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;}
    select {background-image: url("<?php echo BASE_PATH; ?>admin/img/select.png"), -webkit-linear-gradient(#FEFEFE, #F8F8F8 40%, #E9E9E9);}
    select:enabled:hover {background-image: url("<?php echo BASE_PATH; ?>admin/img/select.png"), -webkit-linear-gradient(#FEFEFE, #F8F8F8 40%, #E9E9E9);}
    #container_bdd{margin:0;padding:0;
		   background:  url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAMAAAC67D+PAAAADFBMVEXx9vnw9fj+/v7///+vmeNIAAAAKklEQVQIHQXBAQEAAAjDoHn6dxaqrqpqAAWwMrZRs8EKAzWAshkUDIoZPCvPAOPf77MtAAAAAElFTkSuQmCC');position:absolute;width: 2500px;height: 2500px;}
    ._jsPlumb_endpoint{z-index: 50}
    /*._jsPlumb_connector{z-index: 1}*/
    #field_list{margin:0;padding:0;border-radius: 8px;}
    #field_list .myfield{position:relative;font-size: 12px;color: #222;width: 196px;margin: 2px;cursor: move;text-align: left;padding:5px;background-color: #F1F5F9;background-repeat:no-repeat;padding-left:23px;background-position: 2px 3px ;border: 1px solid #97B2D2;}
    #field_list .myfield:hover{background-color: #CBD8E8;}
    #field_list .myfield span{display:none;position: absolute;right: 5px;top: 5px;}
    #field_list .myfield:hover span{display:block}
    #update_table{display: none;}
    #update_field > div{font-size: 14px;display: none;}
    .table {z-index:60; float: left; margin: 10px;border:1px solid gray;
            position:absolute; color:#484848;line-height:18px;font-family:serif;cursor:pointer;
            font-size:15px;background-color:white;font-weight:bold;
            border-radius: 5px;
            box-shadow: #666 0px 2px 3px;background: #FFFFFF;background: -webkit-gradient(linear, 0 0, 0 bottom, from(#FFFFFF), to(#ddd));background: -moz-linear-gradient(#FFFFFF, #ddd);
            background: linear-gradient(#FFFFFF, #ddd);}
    .table:hover{box-shadow: 0px 0px 9px #777;}
    .ui-draggable-dragging:hover{box-shadow: none} /*perf enhancement on drag table */
    .property{position:relative;cursor: pointer;border-bottom: dashed #CCC 1px;padding: 2px 10px;padding-right:15px;padding-left:20px;background-repeat:no-repeat;background-position: 2px 3px ;font-family: arial;font-size: 12px;font-weight: normal;}
    .property.current_property,.table .property:hover{background-color: #CBD8E8;}
    .property[type_class=field_ident]{cursor: pointer;text-decoration:underline}
    .property[type_class=field_foreignkey]::before{ content:"#"; }
    .table .property:last-child{ border-radius: 0 0 3px 3px; }
    .ombre{box-shadow: 0px 0px 20px #34afb6;}
    .dragActive { border:4px dotted #b634af; border-radius:50px;}
    label{font-size: 13px;line-height: 26px;width: 140px;display: block;float: left;padding-left: 10px;}
    h2,.title{text-align:center;font-family: arial;font-size: 12px;padding:7px;color: white;
              border-color: #2E63A5;
              background: #5E9AE2;
              background: -webkit-gradient(linear, left top, left bottom, from(#5E9AE2), to(#3570B8));
              background: -moz-linear-gradient(top, #5E9AE2, #3570B8);
              filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#5E9AE2', endColorstr='#3570B8');}
    .title{border-top-left-radius: 2px;border-top-right-radius: 2px;text-align: center;/*text-decoration: underline;*/}
    #leftsidebar{box-shadow: 1px 1px 5px #444;z-index:999 ; text-align: center;width:200px;position:fixed;left:0px;top:28px;background: #EEE;/*border:1px solid #000000;*/}
    #rightsidebar{box-shadow: -2px 1px 8px #444;position:fixed;width:320px;background:#F1F5F9;right:0;top:28px;}
    #deletator{cursor: pointer;position:absolute;top:2px;right:0px;color:#fff;background-image: url(/parsimony_cms/admin/img/icons_white.png);}
    .property #deletator{padding: 0px 2px 0px 0px;color: #FF4D4D;background-image: url(/parsimony_cms/admin/img/icons.png);}
    #outline{position:fixed;right:20px;bottom: 20px;border: 1px solid #97B2D2;z-index: 999998;}
    h3{margin:10px 0;font-size:16px;padding-left: 5px;}
    .component{font-size: 11px;cursor:help;padding:4px 2px;background-color: #F1F5F9;border: 1px solid #97B2D2;opacity:0.6}
    .component:hover{opacity:1}
    .rightbar{padding: 3px 0}
    #editor:hover{display:table}
    .connection{color:#2E63A5;text-transform: capitalize;}
    #popup{font-family: Arial, Verdana;text-align: left;border-radius:10px;padding: 5px 10px;width:700px;position:relative;margin:0 auto;top:110px;z-index:999998;display: none;border: 2px solid #2E63A5;background-color: #EEE;}
    .question{font-size: 14px;color: #333;padding: 5px;border: 1px solid #97B2D2;margin: 11px 0px;background-color: #F1F5F9;line-height: 20px;}
    .question input{margin-right: 10px;}
    #conf_box_close{background-image: url(<?php echo BASE_PATH; ?>admin/img/icons.png);margin: 2px 5px;position: absolute;top: 2px;right: 0px;color: white;cursor: pointer;}
    .entity2,.entity1{font-weight:bold}
    #cardinality{border-radius: 3px 3px 0 0; position: relative;background: #5E9AE2;background: -webkit-gradient(linear, left top, left bottom, from(#5E9AE2), to(#3570B8));background: -moz-linear-gradient(top, #5E9AE2, #3570B8);
                 text-align: center;color: white;border-color: #2E63A5;font-size: 18px;line-height: 30px;}
    input[type='checkbox']:checked::before {content: url("../admin/img/checkmark.png");}
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
    #toolbar{font-weight: normal;line-height: 25px;}
    .specialprop{border: none;border-radius: 0;padding: 5px;background: none;}
    #save.haveToSave{
	color: white;
	font-weight: bold;
	background-image: -webkit-linear-gradient(top, #44C5EC, #259BDB);
	background-image: -moz-linear-gradient(top, #44C5EC, #259BDB);
	background-image: -ms-linear-gradient(top, #44C5EC, #259BDB);
	background-image: -o-linear-gradient(top, #44C5EC, #259BDB);
	background-image: linear-gradient(top, #44C5EC, #259BDB);
    }
</style> 

<div id="tooltip-new-fields" class="none toolfield">
    <p class="tooldef ellipsis"><?php echo t('Create an entity and drag n\'drop fields in order to develop your DB model !'); ?></p>
</div>

<div id="tooltip-field_string" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/field_string/icon.png"><span class="tooltitle"><?php echo t('String Field') ?></span></div>
    <p class="tooldef ellipsis"><?php echo t('A String Field manages any finite sequence of characters (i.e., letters, numerals, symbols and punctuation marks.)'); ?></p>
    <div class="tooltype"> SQL Type : VARCHAR 255 by default</div>
    <table class="tooltab">
        <thead>
            <tr>
                <td class="inline-block tooldua">Display</td>
                <td class="inline-block tooldua">Add</td>
                <td class="inline-block tooldua">Update</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="clearboth inline-block tooldua">eF(_5</td>
                <td class="inline-block tooldua"><input type="text"></td>
                <td class="inline-block tooldua"><input type="text" value="eF(_5"></td>
            </tr>
        </tbody>
    </table>
</div>
<div id="tooltip-field_numeric" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/field_numeric/icon.png"><span class="tooltitle"><?php echo t('Numeric Field') ?></span></div>
    <p class="tooldef ellipsis"><?php echo t('A Numeric Field is a data field that holds only numbers to be calculated (without any decimal places).'); ?></p>
    <div class="tooltype"> SQL Type : INT 2 by default</div>
    <table class="tooltab">
        <thead>
            <tr>
                <td class="inline-block tooldua">Display</td>
                <td class="inline-block tooldua">Add</td>
                <td class="inline-block tooldua">Update</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="clearboth inline-block tooldua">45</td>
                <td class="inline-block tooldua"><input type="text"></td>
                <td class="inline-block tooldua"><input type="text" value="45"></td>
            </tr>
        </tbody>
    </table>
</div> 

<div id="tooltip-field_decimal" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/field_decimal/icon.png"><span class="tooltitle"><?php echo t('Decimal Field') ?></span></div>    
    <p class="tooldef ellipsis"> <?php echo t('A Decimal Field is a data field that holds fixed-precision decimal numbers.') ?></p>
    <div class="tooltype"> SQL Type : DECIMAL 20,6 by default</div>
    <table class="tooltab">
        <thead>
            <tr>
                <td class="inline-block tooldua">Display</td>
                <td class="inline-block tooldua">Add</td>
                <td class="inline-block tooldua">Update</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="clearboth inline-block tooldua">45,12</td>
                <td class="inline-block tooldua"><input type="text"></td>
                <td class="inline-block tooldua"><input type="text" value="45,12"></td>
            </tr>
        </tbody>
    </table>
</div> 

<div id="tooltip-field_price" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/field_price/icon.png"><span class="tooltitle"><?php echo t('Price Field') ?></span></div>
    <p class="tooldef ellipsis"> <?php echo 'A Price Field stores a money value in your entity. ' ?></p>
    <div class="tooltype"> SQL Type : DECIMAL 20,6 by default</div>
    <table class="tooltab">
        <thead>
            <tr>
                <td class="inline-block tooldua">Display</td>
                <td class="inline-block tooldua">Add</td>
                <td class="inline-block tooldua">Update</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="clearboth inline-block tooldua">12,34</td>
                <td class="inline-block tooldua"><input type="text"></td>
                <td class="inline-block tooldua"><input type="text" value="12,34"></td>
            </tr>
        </tbody>
    </table>
</div>

<div id="tooltip-field_percent" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/field_percent/icon.png"><span class="tooltitle"><?php echo t('Percent Field') ?></span></div>
    <p class="tooldef ellipsis"><?php echo 'A Percent Field specializes in handling percentage data and displays a value between 0 and 100. ' ?></p>
    <div class="tooltype"> SQL Type : DECIMAL 3 by default</div>
    <table class="tooltab">
        <thead>
            <tr>
                <td class="inline-block tooldua">Display</td>
                <td class="inline-block tooldua">Add</td>
                <td class="inline-block tooldua">Update</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="clearboth inline-block tooldua">100</td>
                <td class="inline-block tooldua"><input type="text"></td>
                <td class="inline-block tooldua"><input type="text" value="100"></td>
            </tr>
        </tbody>
    </table>
</div>

<div id="tooltip-field_mail" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/field_mail/icon.png"><span class="tooltitle"><?php echo t('Mail Field') ?></span></div>
    <p class="tooldef ellipsis"><?php echo 'A Mail Field is used when the data entered by the user has to be an email. ' ?></p>
    <div class="tooltype"> SQL Type : VARCHAR 255 by default</div>
    <table class="tooltab">
        <thead>
            <tr>
                <td class="inline-block tooldua">Display</td>
                <td class="inline-block tooldua">Add</td>
                <td class="inline-block tooldua">Update</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="clearboth inline-block tooldua">abcdef@ghi.jk</td>
                <td class="inline-block tooldua"><input type="text"></td>
                <td class="inline-block tooldua"><input type="text" value="abcdef@ghi.jk"></td>
            </tr>
        </tbody>
    </table>
</div>

<div id="tooltip-field_password" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/field_password/icon.png"><span class="tooltitle"><?php echo t('Password Field') ?></span></div>
    <p class="tooldef ellipsis"><?php echo 'A password field stores in sha-1 hash the password + a salt. It displays a password input type.'; ?></p>
    <div class="tooltype"> SQL Type : VARCHAR 255 by default</div>
    <table class="tooltab">
        <thead>
            <tr>
                <td class="inline-block tooldua">Display</td>
                <td class="inline-block tooldua">Add</td>
                <td class="inline-block tooldua">Update</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="clearboth inline-block tooldua">******</td>
                <td class="inline-block tooldua"><input type="password"></td>
                <td class="inline-block tooldua"><input type="password" value="abcdef@ghi"></td>
            </tr>
        </tbody>
    </table>
</div>

<div id="tooltip-field_state" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/field_state/icon.png"><span class="tooltitle"><?php echo t('State Field') ?></span></div>
    <p class="tooldef ellipsis"><?php echo 'A State field manages the status of an Entity. The state can be used as a Boolean (True / False) or can contain several values (Yes,Perhaps,No) separated by a comma (CSV). '; ?></p>
    <div class="tooltype"> SQL Type : INT 2 by default</div>
    <table class="tooltab">
        <thead>
            <tr>
                <td class="inline-block tooldua">Display</td>
                <td class="inline-block tooldua">Add</td>
                <td class="inline-block tooldua">Update</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="clearboth inline-block tooldua"><select type="text"> 
			<option value="0">Yes</option>';
			<option value="1" selected="selected">No</option>
			<option value="2">Perhaps</option>';
		    </select></td>
                <td class="inline-block tooldua"><select type="text"> 
			<option value="0" selected="selected">Yes</option>';
			<option value="1">No</option>
			<option value="2">Perhaps</option>';
		    </select></td>
                <td class="inline-block tooldua"><select type="text" name="ping_status"> 
			<option value="0">Yes</option>';
			<option value="1">No</option>
			<option value="2" selected="selected">Perhaps</option>';
		    </select></td>
            </tr>
        </tbody>
    </table>
</div>

<div id="tooltip-field_date" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/field_date/icon.png"><span class="tooltitle"><?php echo t('Date Field') ?></span></div>
    <p class="tooldef ellipsis"><?php echo 'A date field is a component for presenting date and time.'; ?></p>
    <div class="tooltype"> SQL Type : DATETIME by default</div>
    <table class="tooltab">
        <thead>
            <tr>
                <td class="inline-block tooldua">Display</td>
                <td class="inline-block tooldua">Add</td>
                <td class="inline-block tooldua">Update</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="clearboth inline-block tooldua">2012-07-06 09:42:30</td>
                <td class="inline-block tooldua"><input type="text"></td>
                <td class="inline-block tooldua"><input type="text" value="2012-07-06 09:42:30"></td>
            </tr>
        </tbody>
    </table>
</div>

<div id="tooltip-field_publication" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/field_date/icon.png"><span class="tooltitle"><?php echo t('Publication Field') ?></span></div>
    <p class="tooldef ellipsis"><?php echo 'A Publication Field contains the published or scheduled date. It provides a Visibility Mode (public, private, protected by password) and also a workflow with different status like Pending, Draft and Published. '; ?></p>
    <div class="tooltype"> SQL Type : DATETIME by default</div>
    <table class="tooltab">
        <thead>
            <tr>
                <td class="inline-block tooldua">Display</td>
                <td class="inline-block tooldua">Add</td>
                <td class="inline-block tooldua">Update</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="clearboth inline-block tooldua">2012-07-07 11:15:52</td>
                <td class="inline-block tooldua"><input type="text"></td>
                <td class="inline-block tooldua"><input type="text" value="2012-07-07 11:15:52"></td>
            </tr>
        </tbody>
    </table>
</div>

<div id="tooltip-field_progress" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/field_progress/icon.png"><span class="tooltitle"><?php echo t('Progress Field') ?></span></div>
    <p class="tooldef ellipsis"><?php echo 'A Progress Field creates a progress bar. '; ?></p>
    <div class="tooltype"> SQL Type : INT 3 by default</div>
    <table class="tooltab">
        <thead>
            <tr>
                <td class="inline-block tooldua">Display</td>
                <td class="inline-block tooldua">Add</td>
                <td class="inline-block tooldua">Update</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="clearboth inline-block tooldua"><progress value="22" max="100"></progress></td>
                <td class="inline-block tooldua"><progress value="" max=""></progress></td>
                <td class="inline-block tooldua"><progress value="50" max="100"></progress></td>
            </tr>
        </tbody>
    </table>
</div>

<div id="tooltip-field_image" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/field_image/icon.png"><span class="tooltitle"><?php echo t('Image Field') ?></span></div>
    <p class="tooldef ellipsis"><?php echo 'This field is used to store the path and display a configurable image in drag n drop.'; ?></p>
    <div class="tooltype"> SQL Type : VARCHAR 255 by default</div>
    <table class="tooltab">
        <thead>
            <tr>
                <td class="inline-block tooldua">Display</td>
                <td class="inline-block tooldua">Add</td>
                <td class="inline-block tooldua">Update</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="clearboth inline-block tooldua"><img title="" src="<?php echo BASE_PATH ?>core/fields/field_image/icon.png"></td>
                <td class="inline-block tooldua">     
                    <div class="boxDropImage">
                        <input style="height: 25px;width: 120px;" type="file">
                        <label style="font-size: 10px;line-height: 15px;width: 140px;display: block;float: none;padding-left: 0px;">Drag n' Drop your New Image In this Window</label>      
                    </div>
                </td>
                <td class="inline-block tooldua" style="width: 145px;">       
                    <div class="boxDropImage" style="margin-top: 20px">
                        <input style="height: 25px;width: 120px;" type="file">
                        <label style="font-size: 10px;line-height: 15px;width: 140px;display: block;float: none;padding-left: 0px;">Drag n' Drop your New Image In this Window</label>      
                    </div>
                    <img title="" style="padding: 0 5px" src="<?php echo BASE_PATH ?>core/fields/field_image/icon.png">
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div id="tooltip-field_flash" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/field_flash/icon.png"><span class="tooltitle"><?php echo t('Flash Field') ?></span></div>
    <p class="tooldef ellipsis"> <?php echo 'This field allows to add easily flash content (SWF).'; ?></p>
    <div class="tooltype"> SQL Type : VARCHAR 255 by default</div>
    <table class="tooltab">
        <thead>
            <tr>
                <td class="inline-block tooldua">Display</td>
                <td class="inline-block tooldua">Add</td>
                <td class="inline-block tooldua">Update</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="clearboth inline-block tooldua">
                    <img title="" src="<?php echo BASE_PATH ?>core/fields/field_flash/icon.png">
                </td>
                <td class="inline-block tooldua" style="padding: 0 5px">
		    <input style="height: 25px;width: 120px;" type="file">    

                </td>
                <td class="inline-block tooldua" style="width: 155px;padding: 0 5px"> 
		    <img title="" src="<?php echo BASE_PATH ?>core/fields/field_flash/icon.png">
		    <input style="height: 25px;width: 120px;" type="file">    
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div id="tooltip-field_url" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/field_url/icon.png"><span class="tooltitle"><?php echo t('URL Field') ?></span></div>
    <p class="tooldef ellipsis"><?php echo 'This field is used to specify a url.'; ?></p>
    <div class="tooltype"> SQL Type : VARCHAR 255 by default</div>
    <table class="tooltab">
        <thead>
            <tr>
                <td class="inline-block tooldua">Display</td>
                <td class="inline-block tooldua">Add</td>
                <td class="inline-block tooldua">Update</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="clearboth inline-block tooldua" style="margin: 0 3px;width: 70px;">abc.def/ghijk</td>
                <td class="inline-block tooldua"><input type="text"></td>
                <td class="inline-block tooldua" style=""><input style="margin: 0 5px;width: 70px;" type="text" value="bcdef.gh/ijkl"></td>
            </tr>
        </tbody>
    </table>
</div>

<div id="tooltip-field_url_rewriting" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/field_url_rewriting/icon.png"><span class="tooltitle"><?php echo t('Url rewriting Field') ?></span></div>
    <p class="tooldef ellipsis"><?php echo 'This field is a unique identifier of a record for the SEO generated by the title of your record (i.e. This is my article, /this-is-my-article).<br> URL rewriting allows to provide a better search engine optimization.<br> URL\'s appearance is modified to have more relevant links to web pages.'; ?></p>
    <div class="tooltype"> SQL Type : VARCHAR 255 by default</div>
    <table class="tooltab">
        <thead>
            <tr>
                <td class="inline-block tooldua">Display</td>
                <td class="inline-block tooldua">Add</td>
                <td class="inline-block tooldua">Update</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="clearboth inline-block tooldua">url.rew/riting</td>
                <td class="inline-block tooldua"><input type="text"></td>
                <td class="inline-block tooldua"><input type="text" value="url.rew/riting"></td>
            </tr>
        </tbody>
    </table>
</div>

<div id="tooltip-field_wysiwyg" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/field_wysiwyg/icon.png"><span class="tooltitle"><?php echo t('WYSIWIG Field') ?></span></div>
    <p class="tooldef ellipsis"><?php echo 'This field is used to display a rich content such as text, images or videos.'; ?></p>
    <div class="tooltype"> SQL Type : LONGTEXT by default</div>
    <table class="tooltab">
        <thead>
            <tr>
                <td class="inline-block tooldua">Display</td>
                <td class="inline-block tooldua">Add</td>
                <td class="inline-block tooldua">Update</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="clearboth inline-block tooldua">Responsive Design Server Side</td>
                <td class="inline-block tooldua"><img style="margin:5px" title="" src="<?php echo BASE_PATH ?>core/files/wysiwyg.png"></td>
                <td class="inline-block tooldua"><img style="margin:5px" title="" src="<?php echo BASE_PATH ?>core/files/wysiwyg-update.png"></td>
            </tr>
        </tbody>
    </table>
</div>

<div id="tooltip-field_textarea" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/field_textarea/icon.png"><span class="tooltitle"><?php echo t('Text Field') ?></span></div>
    <p class="tooldef ellipsis"><?php echo 'This field holds any type of character with a maximum length of 4,294,967,295.'; ?></p>
    <div class="tooltype"> SQL Type : LONGTEXT by default</div>
    <table class="tooltab">
        <thead>
            <tr>
                <td class="inline-block tooldua">Display</td>
                <td class="inline-block tooldua">Add</td>
                <td class="inline-block tooldua">Update</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="clearboth inline-block tooldua">Development on the fly</td>
                <td class="inline-block tooldua"><textarea></textarea>
                <td class="inline-block tooldua"><textarea>Development on the fly</textarea>
            </tr>
        </tbody>
    </table>
</div>

<div id="tooltip-field_user" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/field_user/icon.png"><span class="tooltitle"><?php echo t('User Field') ?></span></div>
    <p class="tooldef ellipsis"><?php echo 'A User Field manages the relationship with user entity. It contains a registered user in Parsimony.'; ?></p>
    <div class="tooltype"> SQL Type : INT 11 by default</div>
    <table class="tooltab">
        <thead>
            <tr>
                <td class="inline-block tooldua">Display</td>
                <td class="inline-block tooldua">Add</td>
                <td class="inline-block tooldua">Update</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="clearboth inline-block tooldua">1</td>
                <td class="inline-block tooldua"><input type="text"></td>
                <td class="inline-block tooldua"><input type="text" value="15"></td>
            </tr>
        </tbody>
    </table>
</div>

<div id="tooltip-field_ip" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/field_ip/icon.png"><span class="tooltitle"><?php echo t('IP Field') ?></span></div>
    <p class="tooldef ellipsis"><?php echo 'This field allows to store an IP address.'; ?></p>
    <div class="tooltype"> SQL Type : VARCHAR 45 by default</div>
    <table class="tooltab">
        <thead>
            <tr>
                <td class="inline-block tooldua">Display</td>
                <td class="inline-block tooldua">Add</td>
                <td class="inline-block tooldua">Update</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="clearboth inline-block tooldua">172.16.254.1</td>
                <td class="inline-block tooldua"><input type="text"></td>
                <td class="inline-block tooldua"><input type="text" value="172.16.254.1"></td>
            </tr>
        </tbody>
    </table>
</div>

<div id="tooltip-field_vote" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/field_vote/icon.png"><span class="tooltitle"><?php echo t('Vote Field') ?></span></div>
    <p class="tooldef ellipsis"><?php echo 'This field is under construction.'; ?></p>
    <div class="tooltype"> SQL Type : FLOAT 20 by default</div>
    <table class="tooltab">
        <thead>
            <tr>
                <td class="inline-block tooldua">Display</td>
                <td class="inline-block tooldua">Add</td>
                <td class="inline-block tooldua">Update</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="clearboth inline-block tooldua">0,12</td>
                <td class="inline-block tooldua"><input type="text"></td>
                <td class="inline-block tooldua"><input type="text" value="2,34"></td>
            </tr>
        </tbody>
    </table>
</div>

<div id="tooltip-field_formasso" class="none toolfield"><div><img class="inline toolimg" title="" src="<?php echo BASE_PATH ?>core/fields/field_formasso/icon.png"><span class="tooltitle"><?php echo t('N:N Association Form') ?></span></div>
    <p class="tooldef ellipsis"><?php echo 'N:N Association Form manages the display in the same form of two different entities connected with a N:N relationship.'; ?></p>
    <div class="tooltype"> SQL Type : VARCHAR by default</div>
    <table class="tooltab">
        <thead>
            <tr>
                <td class="inline-block tooldua">Display</td>
                <td class="inline-block tooldua">Add</td>
                <td class="inline-block tooldua">Update</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="clearboth inline-block tooldua">My TAGS e.g.</td>
                <td class="inline-block tooldua"><img style="margin:5px" title="" src="<?php echo BASE_PATH ?>core/files/n-n-relation-add.png"></td>
                <td class="inline-block tooldua"><img style="margin:5px" title="" src="<?php echo BASE_PATH ?>core/files/n-n-relation.png"></td>
            </tr>
        </tbody>
    </table>
</div>

<div id="toolbar">
    <a href="http://parsimony.mobi" target="_blank" style="padding:0;height:28px;">
        <img src="<?php echo BASE_PATH; ?>admin/img/parsimony.png">
    </a>
    <div class="toolbarbonus inline-block">
        <div class="floatleft" style="border-right: 1px solid #D3D5DB;padding-right: 10px">	
	    <?php echo t('Current Module', FALSE); ?>
            <form action="" method="POST" style="display:inline-block">
                <select style="font-weight: bold;" name="module" onchange="$(this).parent().trigger('submit');">
		    <?php
		    foreach (\app::$activeModules as $moduleName => $module) {
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

        <div class="floatleft" style="border-left: 1px solid white;border-right: 1px solid #D3D5DB;padding-left: 10px;padding-right: 10px">
	    <?php echo t('Add an Entity', FALSE); ?>
            <form id="add_table" class="inline-block">
                <input type="text" id="table_name" style="padding:1px;">
                <input type="submit" style="height: 24px;" value="<?php echo t('Add', FALSE); ?>"> 
            </form>
        </div>
        <div class="floatleft inline-block" style="border-left: 1px solid white;padding-left: 10px">
            <input type="button" id="save" value="<?php echo t('Save', FALSE); ?>" style="height: 24px;margin-top: 1px;" />
        </div>

    </div>
</div>
<div id="notify"></div>
<div id="container_bdd">
    <canvas id="outline" width="150" height="100"></canvas>
    <div id="conf_box_overlay" style="z-index: 99;" class="none">
        <div id="popup">   
            <div id="cardinality"><?php echo t('Cardinality', FALSE); ?>
                <span id="conf_box_close" class="ui-icon ui-icon-closethick right"></span>
            </div>
            <div class="question"><input type="button" id="button1" value="✔">(1 <span class="entity2"></span> - &infin; <span class="entity1"></span>) -- <?php echo t('For 1', FALSE); ?> " <span class="entity2"></span>",<?php echo ' ' . t('are there several', FALSE); ?> " <span class="entity1"></span> " ?</div>
            <div class="question"><input type="button" id="button2" value="✔">(1 <span class="entity1"></span> - &infin; <span class="entity2"></span>) -- <?php echo t('For 1', FALSE); ?> " <span class="entity1"></span>",<?php echo ' ' . t('are there several', FALSE); ?> " <span class="entity2"></span> " ?</div>
            <div class="question"><input type="button" id="button3" value="✔">(&infin; <span class="entity1"></span> - &infin; <span class="entity2"></span>) -- <?php echo t('For several', FALSE); ?> " <span class="entity1"></span> " ,<?php echo ' ' . t('are there several', FALSE); ?> " <span class="entity2"></span> " ?</div>
        </div>
    </div>
    <div id="leftsidebar">
        <div>
            <h2 data-tooltip="#tooltip-new-fields" class="tooltip"><?php echo t('New Fields', FALSE); ?></h2>
            <div id="field_list">
		<?php
		$aliasClasses = array_flip(\app::$aliasClasses);
		foreach ($aliasClasses AS $class => $alias) {
		    if (preg_match('#field#', $alias))
			if (!class_exists($alias))
			    class_alias($class, $alias);
		}
		$classes = get_declared_classes();
		$html = '';
		$classes = array_unique($classes);
		foreach ($classes as $class) {
		    if (is_subclass_of($class, 'field')) {
			if (isset($aliasClasses[$class])) {
			    $class = $aliasClasses[$class];
			}
			$field = new $class($_POST['module'], '', '');
                        $fieldInfos = \tools::getClassInfos($field);
			$args = array();
			$ssmethod = new ReflectionMethod($class, '__construct');
			$params = $ssmethod->getParameters();
			foreach ($params as $ssparam) {
			    $args[$ssparam->name] = $field->{$ssparam->name};
			}
			$args['oldName'] = $field->name;
			if ($class == 'field_ident' || $class == 'field_foreignkey')
			    $none = ' style="display:none"';
			else
			    $none = '';
			echo '<style>.property[type_class=' . $class . '],.myfield[type_class=' . $class . ']{background-image:url(' . BASE_PATH . str_replace('\\', '/', \app::$aliasClasses[$class]) . '/icon.png); }</style>';
			echo '<div type_class="' . $class . '" data-attributs=\'' . s(json_encode($args)) . '\' class="myfield ellipsis" ' . $none . '>' . t(ucfirst(s($fieldInfos['title'])), FALSE) . '<span class="tooltip ui-icon ui-icon-info" data-tooltip="#tooltip-' . $class . '"></span></div>';
			$html .= '<div id="update_' . $class . '">
<input type="hidden" name="module">
<input type="hidden" name="entity">
<h2 style="margin-top:0;padding: 10px;"><span class="closeformpreview ui-icon ui-icon-circle-close" style="display: inline-block;left: 15px;position: absolute;top: 8px;background-image: url(/parsimony_cms/admin/img/icons_white.png);"></span>' . t('Field Settings', FALSE) . '</h2>
<div class="rightbar"><label class="ellipsis">' . t('Name', FALSE) . ' </label><input type="text" name="name">
<label class="ellipsis">' . t('Field', FALSE) . ' </label><div class="inline-block" style="position:relative;top:3px">' . ucfirst(substr(strstr(strrchr(get_class($field), '\\'), '_'), 1)) . '</div>    
</div>
<div><h3>' . t('SQL Properties', FALSE) . '</h3>
	<div class="rightbar"><label class="ellipsis">' . t('Type', FALSE) . ' </label><div class="inline-block" style="position:relative;top:3px"><input type="hidden" name="type">' . $field->type . '</div></div>
	<div class="rightbar" style="clear: both;"><label class="ellipsis">' . t('Max Characters', FALSE) . ' </label><input type="text" name="characters_max"></div>
	<div class="rightbar"><label class="ellipsis">' . t('Min Characters', FALSE) . ' </label><input type="text" name="characters_min"></div>
</div>
<div><h3>' . t('Form View', FALSE) . '</h3>
<div  class="rightbar"><label class="ellipsis">' . t('Label', FALSE) . ' </label><input type="text" name="label"></div>
<div class="rightbar"><label class="ellipsis">' . t('Text help', FALSE) . ' </label><input type="text" name="text_help"></div>
<div class="rightbar"><label class="ellipsis">' . t('Error Message', FALSE) . '</label><input type="text" name="msg_error"></div>
<div class="rightbar"><label class="ellipsis">' . t('Default Values', FALSE) . '</label><input type="text" name="default"></div>
<div class="rightbar"><label class="ellipsis">' . t('Required', FALSE) . '</label><select style="font-size:13px;height:26px" name="required"><option value="1">' . t('True') . '</option><option value="0">' . t('False') . '</option></select></div>
<div class="rightbar"><label class="ellipsis">' . t('Regex', FALSE) . '</label><input type="text" name="regex"></div>
<div class="rightbar" style="padding:5px 10px 10px 10px;">
<div style="padding:3px 0px;">
' . t('In which form display the field ?', FALSE) . '
</div>
<div class="visibilityform">
    <input data-form="form-display" checked="checked" type="checkbox" value="1">
    <span class="ellipsis" for="display" style="width:70px;display:inline-block">' . t('Display', FALSE) . '</span>
    <input data-form="form-add" checked="checked" type="checkbox" value="2">
    <span class="ellipsis" for="add" style="width:70px;display:inline-block;">' . t('Add', FALSE) . '</span>
    <input type="checkbox" checked="checked" value="4" data-form="form-update">
    <span class="ellipsis" for="update" style="width:70px;display:inline-block;">' . t('Update', FALSE) . '</span>
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
			$html .= '<input type="hidden" name="oldName"><input type="submit" class="save_field" value="' . t('Validate', FALSE) . '" style="width: 50%;margin: 5px 0 10px 25%;"></div>';
		    }
		}
		?>
            </div>
        </div>
    </div>
    <?php
    foreach (\app::getModule($_POST['module'])->getModel() as $entityName => $entity) {
	$reflect = new ReflectionClass('\\' . $_POST['module'] . '\\model\\' . $entityName);
	$className = $reflect->getShortName();
        $modelInfos = \tools::getClassInfos($reflect);
	$tab = array('name' => $className, 'title' => $entity->getTitle(), 'oldName' => $className, 'behaviorTitle' => $entity->behaviorTitle, 'behaviorDescription' => $entity->behaviorDescription, 'behaviorKeywords' => $entity->behaviorKeywords, 'behaviorImage' => $entity->behaviorImage);
	echo '<div class="table" data-attributs=\'' . s(json_encode($tab)) . '\' id="table_' . $className . '" style="top:' . $modelInfos['top'] . ';left:' . $modelInfos['left'] . ';"><div class="title">' . $className . '</div>';
	$parameters = $entity->getFields();
	foreach ($parameters as $propertyName => $field) {
	    $class = get_class($field);
	    if (isset($aliasClasses[$class])) {
		$class = $aliasClasses[$class];
	    }
	    $ssmethod = new ReflectionMethod($class, '__construct');
	    $params = $ssmethod->getParameters();
	    $args = array();
	    foreach ($params as $ssparam) {
		$args [$ssparam->name] = $field->{$ssparam->name};
	    }
	    $args['oldName'] = $field->name;
	    echo '<div class="property" id="table_' . $className . '_' . $propertyName . '" data-attributs=\'' . s(json_encode($args)) . '\' type_class="' . $class . '">' . $propertyName . '</div>';
	}
	echo '</div>';
    }
    ?>
    <div id="rightsidebar" style="z-index:999">
        <div id="update_table">
            <h2 style="margin-top:0;padding: 10px;"><span class="closeformpreview ui-icon ui-icon-circle-close" style="display: inline-block;left: 15px;position: absolute;top: 8px;background-image: url(/parsimony_cms/admin/img/icons_white.png);"></span><?php echo t('Table Settings', FALSE) ?></h2>
            <div class="rightbar"><label class="ellipsis"><?php echo t('Name', FALSE); ?> </label><input type="text" name="name"><input type="hidden" name="oldName"></div>
            <div class="rightbar"><label class="ellipsis"><?php echo t('Title', FALSE); ?> </label><input type="text" name="title"></div>
            <div><h3><?php echo t('Fields Behaviour', FALSE); ?></h3>
                <div class="rightbar"><label class="ellipsis"><?php echo t('Title', FALSE); ?> </label><input type="text" name="behaviorTitle"></div>
                <div class="rightbar"><label class="ellipsis"><?php echo t('Description', FALSE); ?> </label><input type="text" name="behaviorDescription"></div>
                <div class="rightbar"><label class="ellipsis"><?php echo t('Keywords', FALSE); ?></label><input type="text" name="behaviorKeywords"></div>
                <div class="rightbar"><label class="ellipsis"><?php echo t('Image', FALSE); ?></label><input type="text" name="behaviorImage"></div>
                <input type="submit" class="save_table" value="<?php echo t('Validate', FALSE); ?>" style="width: 50%;margin: 5px 0 10px 25%;">
            </div>
        </div>
        <div id="update_field">
	    <?php echo $html; ?>
        </div>
    </div>
    <span id="deletator" class="ui-icon ui-icon-closethick"></span>
    <script>
        function enc(str){
            return str.toString().replace('"','\\"');
        }
        $(document).on("change",'.visibilityform input[type="checkbox"]',function(e){
            var nb = 0;
            var parent = $(this).parent();
            $('input:checked',parent).each(function(){
                nb += parseInt($(this).val());
            });
            $('input[name="visibility"]',parent).val(nb);
        });

        var dbadmin = {
            marqueur : false,
            endpointOptions : {endpoint:[ "Dot", { radius:12 } ],
                paintStyle:{ fillStyle:'#346db5'},
                isSource:true,
                reattach:true,
                maxConnections:100,
                connector:[ "Bezier", (200) ],
                dragAllowedWhenFull:true,
                connectorStyle : { strokeStyle:"#34afb6",  position:"absolute", lineWidth:2 },
                isTarget:false },
            endpointOptions2 : {endpoint:[ "Dot", { radius:12 } ],
                paintStyle:{ fillStyle: "transparent" },
                isSource:false,
                reattach:true,
                maxConnections:100,
                dragAllowedWhenFull:true, 
                isTarget:true },
            endpointOptions3 : {endpoint:[ "Dot", { radius:8 } ],
                paintStyle:{ fillStyle:'#b634af'},
                isSource:false,
                connectorStyle : {strokeStyle:"#2E63A5", position:"absolute", lineWidth:3},
                isTarget:false },
            /*todo décider si on peut faire un lien récursif ou pas
             */
            buildLink : function(source,target){
                var objSource = $("#table_" + source);
                var objTarget = $("#table_" + target);
                var champ = $("#field_list div[type_class='field_foreignkey']").clone();         
                var predictedname = objSource.find(".property[type_class='field_ident']" ).text();
                var n = 0;
                while($('#table_'+ target + '_'+predictedname).length){            
                    n++;
                    if(n>1){
                        predictedname = predictedname.substring(0,predictedname.length-2)+'_'+n;  
                    }else{
                        predictedname += '_'+n;
                    }           
                }
                var jsonproperties = jQuery.parseJSON(JSON.stringify($("#field_list div[type_class='field_foreignkey']").data("attributs")));
                jsonproperties.name = predictedname;
                jsonproperties.label = predictedname;
                var linkName = objSource.find(".title").text();
                jsonproperties.link = linkName;
                var fieldString = $("#table_" + linkName + ' .property[type_class="field_string"]:first');
                if(fieldString.length > 0) jsonproperties.templatelink = '%' + fieldString.text() + '%';
                else jsonproperties.templatelink = '%id_' + linkName + '%';
                jsonproperties.entity = objTarget.find(".title").text();
                champ.removeAttr('class').data("attributs",jsonproperties).text(predictedname);
                champ.attr("name",objSource.find('.title').text());
                champ.attr("id",'table_' + target + '_' + predictedname).addClass("property");
                champ.appendTo(objTarget).show();
                dbadmin.reDraw();
            },
            createTable : function(tablename){
                if(tablename.length>0){
                    var keywordsReserveds = ',include,require,include_once,require_once,for,foreach,as,if,elseif,else,while,do,endwhile,endif,switch,case,endswitch,endfor,endforeach,return,break,continue,self,static,parent,a,abort,abs,absolute,access,action,ada,add,admin,after,aggregate,alias,all,allocate,also,alter,always,analyse,analyze,and,any,are,array,as,asc,asensitive,assertion,assignment,asymmetric,at,atomic,attribute,attributes,audit,authorization,auto_increment,avg,avg_row_length,backup,backward,before,begin,bernoulli,between,bigint,binary,bit,bit_length,bitvar,blob,bool,boolean,both,breadth,break,browse,bulk,by,c,cache,call,called,cardinality,cascade,cascaded,case,cast,catalog,catalog_name,ceil,ceiling,chain,change,char,char_length,character,character_length,character_set_catalog,character_set_name,character_set_schema,characteristics,characters,check,checked,checkpoint,checksum,class,class_origin,clob,close,cluster,clustered,coalesce,cobol,collate,collation,collation_catalog,collation_name,collation_schema,collect,column,column_name,columns,command_function,command_function_code,comment,commit,committed,completion,compress,compute,condition,condition_number,connect,connection,connection_name,constraint,constraint_catalog,constraint_name,constraint_schema,constraints,constructor,contains,containstable,continue,conversion,convert,copy,corr,corresponding,count,covar_pop,covar_samp,create,createdb,createrole,createuser,cross,csv,cube,cume_dist,current,current_date,current_default_transform_group,current_path,current_role,current_time,current_timestamp,current_transform_group_for_type,current_user,cursor,cursor_name,cycle,data,database,databases,date,datetime,datetime_interval_code,datetime_interval_precision,day,day_hour,day_microsecond,day_minute,day_second,dayofmonth,dayofweek,dayofyear,dbcc,deallocate,dec,decimal,declare,default,defaults,deferrable,deferred,defined,definer,degree,delay_key_write,delayed,delete,delimiter,delimiters,dense_rank,deny,depth,deref,derived,desc,describe,descriptor,destroy,destructor,deterministic,diagnostics,dictionary,disable,disconnect,disk,dispatch,distinct,distinctrow,distributed,div,do,domain,double,drop,dual,dummy,dump,dynamic,dynamic_function,dynamic_function_code,each,element,else,elseif,enable,enclosed,encoding,encrypted,end,end-exec,enum,equals,errlvl,escape,escaped,every,except,exception,exclude,excluding,exclusive,exec,execute,existing,exists,exit,exp,explain,external,extract,false,fetch,fields,file,fillfactor,filter,final,first,float,float4,float8,floor,flush,following,for,force,foreign,fortran,forward,found,free,freetext,freetexttable,freeze,from,full,fulltext,function,fusion,g,general,generated,get,global,go,goto,grant,granted,grants,greatest,group,grouping,handler,having,header,heap,hierarchy,high_priority,hold,holdlock,host,hosts,hour,hour_microsecond,hour_minute,hour_second,identified,identity,identity_insert,identitycol,if,ignore,ilike,immediate,immutable,implementation,implicit,in,include,including,increment,index,indicator,infile,infix,inherit,inherits,initial,initialize,initially,inner,inout,input,insensitive,insert,insert_id,instance,instantiable,instead,int,int1,int2,int3,int4,int8,integer,intersect,intersection,interval,into,invoker,is,isam,isnull,isolation,iterate,join,k,key,key_member,key_type,keys,kill,lancompiler,language,large,last,last_insert_id,lateral,leading,least,leave,left,length,less,level,like,limit,lineno,lines,listen,ln,load,local,localtime,localtimestamp,location,locator,lock,login,logs,long,longblob,longtext,loop,low_priority,lower,m,map,match,matched,max,max_rows,maxextents,maxvalue,mediumblob,mediumint,mediumtext,member,merge,message_length,message_octet_length,message_text,method,middleint,min,min_rows,minus,minute,minute_microsecond,minute_second,minvalue,mlslabel,mod,mode,modifies,modify,module,month,monthname,more,move,multiset,mumps,myisam,name,names,national,natural,nchar,nclob,nesting,new,next,no,no_write_to_binlog,noaudit,nocheck,nocompress,nocreatedb,nocreaterole,nocreateuser,noinherit,nologin,nonclustered,none,normalize,normalized,nosuperuser,not,nothing,notify,notnull,nowait,null,nullable,nullif,nulls,number,numeric,object,octet_length,octets,of,off,offline,offset,offsets,oids,old,on,online,only,open,opendatasource,openquery,openrowset,openxml,operation,operator,optimize,option,optionally,options,or,order,ordering,ordinality,others,out,outer,outfile,output,over,overlaps,overlay,overriding,owner,pack_keys,pad,parameter,parameter_mode,parameter_name,parameter_ordinal_position,parameter_specific_catalog,parameter_specific_name,parameter_specific_schema,parameters,partial,partition,pascal,password,path,pctfree,percent,percent_rank,percentile_cont,percentile_disc,placing,plan,pli,position,postfix,power,preceding,precision,prefix,preorder,prepare,prepared,preserve,primary,print,prior,privileges,proc,procedural,procedure,process,processlist,public,purge,quote,raid0,raiserror,range,rank,raw,read,reads,readtext,real,recheck,reconfigure,recursive,ref,references,referencing,regexp,regr_avgx,regr_avgy,regr_count,regr_intercept,regr_r2,regr_slope,regr_sxx,regr_sxy,regr_syy,reindex,relative,release,reload,rename,repeat,repeatable,replace,replication,require,reset,resignal,resource,restart,restore,restrict,result,return,returned_cardinality,returned_length,returned_octet_length,returned_sqlstate,returns,revoke,right,rlike,role,rollback,rollup,routine,routine_catalog,routine_name,routine_schema,row,row_count,row_number,rowcount,rowguidcol,rowid,rownum,rows,rule,save,savepoint,scale,schema,schema_name,schemas,scope,scope_catalog,scope_name,scope_schema,scroll,search,second,second_microsecond,section,security,select,self,sensitive,separator,sequence,serializable,server_name,session,session_user,set,setof,sets,setuser,share,show,shutdown,signal,similar,simple,size,smallint,some,soname,source,space,spatial,specific,specific_name,specifictype,sql,sql_big_result,sql_big_selects,sql_big_tables,sql_calc_found_rows,sql_log_off,sql_log_update,sql_low_priority_updates,sql_select_limit,sql_small_result,sql_warnings,sqlca,sqlcode,sqlerror,sqlexception,sqlstate,sqlwarning,sqrt,ssl,stable,start,starting,state,statement,static,statistics,status,stddev_pop,stddev_samp,stdin,stdout,storage,straight_join,strict,string,structure,style,subclass_origin,sublist,submultiset,substring,successful,sum,superuser,symmetric,synonym,sysdate,sysid,system,system_user,table,table_name,tables,tablesample,tablespace,temp,template,temporary,terminate,terminated,text,textsize,than,then,ties,time,timestamp,timezone_hour,timezone_minute,tinyblob,tinyint,tinytext,to,toast,top,top_level_count,trailing,tran,transaction,transaction_active,transactions_committed,transactions_rolled_back,transform,transforms,translate,translation,treat,trigger,trigger_catalog,trigger_name,trigger_schema,trim,true,truncate,trusted,tsequal,type,uescape,uid,unbounded,uncommitted,under,undo,unencrypted,union,unique,unknown,unlisten,unlock,unnamed,unnest,unsigned,until,update,updatetext,upper,usage,use,user,user_defined_type_catalog,user_defined_type_code,user_defined_type_name,user_defined_type_schema,using,utc_date,utc_time,utc_timestamp,vacuum,valid,validate,validator,value,values,var_pop,var_samp,varbinary,varchar,varchar2,varcharacter,variable,variables,varying,verbose,view,volatile,waitfor,when,whenever,where,while,width_bucket,window,with,within,without,work,write,writetext,x509,xor,year,year_month,zerofill,zone,';
                    if(keywordsReserveds.indexOf("," + tablename + ",") == -1){
                        if(!$('#table_' + tablename).length){
                            $("#container_bdd").prepend('<div id="table_' + tablename + '"data-attributs=\'{"name":"' + tablename + '","oldName":"' + tablename + '","title":"' + tablename + '","behaviorTitle":"","behaviorDescription":"","behaviorKeywords":"","behaviorImage":""}\' class="table" style="left:300px;top:50px;"><div class="title">' + tablename + '</div><div type_class="field_ident">'+ t('ID') +'</div></div>');
                            var monid_champ = "table_" + tablename +  "_id_" + tablename;
                            var table_name = tablename;
                            var jsonproperties = jQuery.parseJSON(JSON.stringify($("#field_list div[type_class='field_ident']").data("attributs")));
                            jsonproperties.entity = table_name;
                            jsonproperties.name = "id_" + table_name;
                            jsonproperties.label = "Id " + table_name;
                            var champ = $('#table_' + tablename + ' div[type_class="field_ident"]');
                            champ.attr("id",monid_champ).attr("type_class","field_ident").addClass("property").text("id_" + table_name);
                            champ.data("attributs",jsonproperties);
                            dbadmin.reDraw();
                        }else{
                            ParsimonyAdmin.notify(t('The Entity') + ' ' +tablename + ' ' + t('already exists'),'negative');
                        }
                    }else{
                        ParsimonyAdmin.notify(t('This word')+ ' '  + tablename + ' ' + t('belongs to a list of Reserved Words, Please Choose another'),'negative') + '.';
                    }
                }else{
                    ParsimonyAdmin.notify(t('Enter a Name of Entity'),'negative');
                }
            },
            init :   function(){
                /* Tooltip */
                $(".tooltip").parsimonyTooltip({triangleWidth:5});
                /* Fracs preview */
                $("#outline").fracs("outline", {
                    crop: true,
                    styles: [{
                            selector: ".table",
                            strokeWidth: "auto",
                            strokeStyle: "auto",
                            fillStyle: "#2E63A5"
                        },{
                            selector: ".current_update_table",
                            strokeWidth: "auto",
                            strokeStyle: "auto",
                            fillStyle: "red"
                        }],
                    viewportStyle:{fillStyle:"rgba(104,169,255,0.2)"},
                    viewportDragStyle:{fillStyle:"rgba(104,169,255,0.5)"}
                });
		
		$(window).bind("beforeunload",function(event) {
		    if($("#save").hasClass("haveToSave")) return t("You have unsaved changes");
		});
		
                /* JsPlumb */
                jsPlumb.importDefaults({     
                    Container : $("body"),
                    DragOptions : { cursor: 'pointer', zIndex:2000 },                  
                    DropOptions : { activeClass:'dragActive' } 
                });
                		
                /* Save field settings */
                $("#update_field").on('click','.save_field',function(){
                    if($('#update_' + current_update_field.attr('type_class') + ' input[name="name"]').val() != $('#update_' + current_update_field.attr('type_class') + ' input[name="oldName"]').val()){
                        if(!confirm(('Your Attention Please : If you change the name of the property, you will break all your database queries already done with the old name.'))){
                            return false;
                        }
                    }
                    var json = '{';
                    $("#update_" + current_update_field.attr('type_class') + " input[name],#update_" + current_update_field.attr('type_class') + " select[name]").each(function(){
                        json +=  '"' + $(this).attr('name') + '":"' +  $(this).val().replace(/"/g,'\\"').replace(/\\/g,'\\\\') + '",';
                    });
                    var obj = jQuery.parseJSON(json.substring(0, json.length-1) + "}");
                    current_update_field.data("attributs",obj);
                    $("#deletator").prependTo($("body"));
                    current_update_field.text(obj.name);
                    $(this).parent().hide('slow');
		    $("#save").addClass("haveToSave");
                });
                
                /* Save table Settings */
                $("#update_table").on('click','.save_table',function(){
                    var oldName = $('#update_table input[name="oldName"]').val();
                    var newName = $('#update_table input[name="name"]').val();
                    if(newName != oldName){
                        if(!confirm(('Your Attention Please : If you change the name of the table, you will break all your database queries already done with the old name.'))){
                            return false;
                        }
                        /* we change the entity name of all his properties */
                        $(".property", current_update_table).each(function(){
                            var attrs = $(this).data("attributs");
                            attrs.entity = newName;
                            $(this).data("attributs",attrs);
                        });
                         /* we change the link entity name for all foreign keys that link to this table */
                        $('.property[type_class="field_foreignkey"]').each(function(){
                            var attrs = $(this).data("attributs");
                            if(attrs.link == oldName)
                                attrs.link = newName;
                            $(this).data("attributs",attrs);
                        });
                    }
                    var json = '{';
                    $("#update_table input[name],#update_table select[name]").each(function(){
                        json +=  '"' + $(this).attr('name') + '":"' +  $(this).val().replace(/"/g,'\\"') + '",';
                    });
                    var obj = jQuery.parseJSON(json.substring(0, json.length-1) + "}");
                    current_update_table.data("attributs",obj);
                    $("#deletator").prependTo($("body"));
                    current_update_table.find(".title").text(obj.name);
                    $(this).parent().parent().hide('slow');
                    $("#save").addClass("haveToSave");
                }); 
		
                /* Open Table Settings */
                $('#container_bdd').on('click','.title',function(){
                    $('#update_field > div').hide();
                    $('#update_table').show();
                })
		
                /* Delete Table */
                .on('click','#deletator',function(){
                    obj = $(this).parent();
                    if(obj.hasClass('table')){
                        if(confirm(t('Are you sure to delete this entity ?'))){
                            $(this).appendTo($('body'));
                            $('#container_bdd div[type_class="field_foreignkey"]').each(function(index){
                                var name = $(".title",obj).text();
                                if($(this).text()=='id_' + name) $(this).remove();
                            });
                            obj.remove();
                            dbadmin.reDraw();
                        }
                    }else if(obj.hasClass('property')){
                        if(confirm(t('Are you sure to delete this property ?'))){
                            $(this).appendTo($('body'));
                            jsPlumb.removeAllEndpoints(obj.attr('id'));
                            obj.remove();
                        }
                    }
		    $("#save").addClass("haveToSave");
                })
		
                /* Show delete buttons on fields */
                .on('mouseover mouseout','.property',function(event) {
                    event.stopPropagation();
                    if (event.type == 'mouseover') {
                        if($(this).attr('type_class') != 'field_ident') $("#deletator").show().prependTo($(this));
                    } else {
                        $("#deletator").hide();
                    }
                })
		
                /* Show delete buttons on tables */
                .on('mouseover mouseout','.table',function(event) {
                    if (event.type == 'mouseover') {
                        $("#deletator").show().prependTo($(this));
                    } else {
                        $("#deletator").hide();
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
                
                $(document).on('click','#conf_box_close',function(){
                    $('#popup').hide();
                    $('#conf_box_overlay').hide();
                })
                .on('click','.closeformpreview',function(){
                    $(this).parent().parent().hide();
                })
                /* Filter Table Name */
                .on('keyup',"#table_name",function(){
                    $(this).val($(this).val().toLowerCase().replace(/[^a-z_]+/,""));
                })
                /* Open and load field Settings */
                .on('click',".table .property",function(){ 
                    $('#update_field').show();
                    $('#update_table').hide();
                    current_update_field = $(this);
                    $(".current_property").removeClass("current_property");
                    current_update_field.addClass("current_property");
                    $.each($(this).data("attributs"), function(i,item){
                        var parent = $('#update_'+ current_update_field.attr('type_class'));
                        if(item === false) item = 0;
                        $('[name=' + i + ']',parent).val(item);
                        if(i == 'visibility'){
                            if(item & 1) $('input[data-form="form-display"]',parent).attr('checked','checked');
                            else $('input[data-form="form-display"]',parent).removeAttr('checked');
                            if(item & 2) $('input[data-form="form-add"]',parent).attr('checked','checked');
                            else $('input[data-form="form-add"]',parent).removeAttr('checked');
                            if(item & 4) $('input[data-form="form-update"]',parent).attr('checked','checked');
                            else $('input[data-form="form-update"]',parent).removeAttr('checked');
                        }
                    });
                    $('#update_field > div').hide();
                    $('#update_'+ current_update_field.attr('type_class')).show();          
                })

                
                /* Open and load table Settings */
                .on('click',".table",function(){ 
                    current_update_table = $(this);         
                    $(".current_update_table").removeClass("current_update_table");
                    current_update_table.addClass("current_update_table");
                    $.each($(this).data("attributs"), function(i,item){
                        $('#update_table input[name=' + i + ']').val(item);
                    });
                    $("#outline").fracs('outline', 'redraw');
                })
                /* Save all models */
                .on('click','#save',function(){
                    var propertylist = '[' ;
                    $(".table").each(function(){
                        var recupId = $(".title",this).text();
                        var tableAttrs = $(this).data("attributs");
                        propertylist += '{"name": "' + enc(recupId) + '","oldName": "' + enc(tableAttrs.oldName) + '","title":"' + enc(tableAttrs.title) + '","behaviorTitle":"' + enc(tableAttrs.behaviorTitle) + '","behaviorDescription":"' + enc(tableAttrs.behaviorDescription) + '","behaviorKeywords":"' + enc(tableAttrs.behaviorKeywords) + '","behaviorImage":"' + enc(tableAttrs.behaviorImage) + '","top": "'+ $(this).css("top")+'","left": "'+ $(this).css("left")+'","properties" : {';
                        $(".property",$(this)).each(function(){
                            var jsonproperties = $(this).data("attributs");
                            propertylist += '"' + enc(jsonproperties.name) + ':' + $(this).attr("type_class") + '" :' + JSON.stringify(jsonproperties) +' ,';
                        });
                        propertylist = propertylist.substring(0, propertylist.length-1) + '}},';
                    });
                    propertylist = propertylist.substring(0, propertylist.length-1) + ']';
                    $.post('saveModel', {  module : '<?php echo $_POST['module'] ?>', list : propertylist },function(data){
                        ParsimonyAdmin.notify(t('New Data Model has been Saved') + data,"positive");
                    });
		    $("#save").removeClass("haveToSave");
                })
                /* Choose behavior of the link */
                .on('click','#popup input',function(){
                    var source1 = $("#" + $(this).data('sourceid'));
                    var target1 = $("#" + $(this).data('targetid'));
                    var entitySource = source1.parent().find('.title').text();
                    var entityTarget = $('.title',target1).text();
                    if($(this).attr('id')=='button3'){
                        var t = entitySource +'_'+entityTarget;
                        dbadmin.createTable(t);
                        dbadmin.buildLink(entitySource,t);
                        dbadmin.buildLink(entityTarget,t);
                    }else{
                        if($(this).attr('id')=='button2'){
                            source = source1;
                            target = target1;
                        }else{
                            source = target1.find("div[type_class='field_ident']");
                            target = source1.parent();
                        }
                        var entitySource = source.parent().find('.title').text();
                        var entityTarget = $('.title',target).text();
                        dbadmin.buildLink(entitySource,entityTarget);
                    }
                    $("#popup").hide();
                    $('#conf_box_overlay').hide();
                    dbadmin.reDraw();
                })
                /* Filter Table Name */
                .on('keyup',"#table_name",function(){
                    $(this).val($(this).val().toLowerCase().replace(/[^a-z_]+/g,""));
                });
		
                /* Sort properties */
                $("#container_bdd .table").sortable({ items: ".property[type_class!='field_ident']" });
                $("#field_list > div").draggable({zIndex: 2700 ,revert:true,helper: "clone"});
		
                /* Add a Table */
                $("#toolbar").on('submit','#add_table',function(e){  
                    e.preventDefault();
                    dbadmin.createTable($("#table_name").val());
		    $("#save").addClass("haveToSave");
                });
                
                dbadmin.reDraw();
            },
            //	    updateFormPreview :   function(){
            //		$.post("action",'TOKEN=' + TOKEN + '&action=getPreviewAddForm&module=<?php echo $_POST['module'] ?>&model=' + $(".current_property").closest(".table").find(".title").text() ,function(data){
            //		    $("#preview_form .content").html(data);                 
            //		});
            //	    },
            createAnchor :   function(monid){
                myEndpoint = jsPlumb.addEndpoint(monid, $.extend({ anchor:["LeftMiddle","RightMiddle"], uuid:monid+"_uuid" }, dbadmin.endpointOptions));
                jsPlumb.setDraggable(monid, false);          
            },
            createAnchorForeignKey :   function(monid){
                jsPlumb.addEndpoint(monid, $.extend({ anchor:["BottomRight","TopRight"], uuid:monid+"_uuid" }, dbadmin.endpointOptions2));
            },
            createAnchorNewForeignKey :   function(monid){
                jsPlumb.addEndpoint(monid, $.extend({ anchor:["LeftMiddle","RightMiddle"], uuid:monid+"_uuid" }, dbadmin.endpointOptions3));
                jsPlumb.setDraggable(monid, false);
            },
            reDraw :   function(){
                jsPlumb.reset();
		
                /* Draw Anchor on fields ident */
                $(".property").each(function(index) {
                    if($(this).attr('type_class') == 'field_ident') dbadmin.createAnchor($(this).attr('id') );
                });

                /* Draw Anchor on fields foreignKey */
                $(".table").each(function(){
                    dbadmin.createAnchorForeignKey($(this).attr('id'));
                });
		
                /* Draw connectors between tables */
                $("#container_bdd div[type_class='field_foreignkey']").not("#field_list div[type_class='field_foreignkey']").each(function(index) {
                    var jsonproperties = $(this).data("attributs");
                    dbadmin.createAnchorNewForeignKey($(this).attr("id"));
                    dbadmin.marqueur = true;
                    jsPlumb.connect({ uuids:[$(this).attr("id")+"_uuid", $("#table_" + jsonproperties.link + " div[type_class='field_ident']" ).attr("id")+"_uuid"] ,
                        paintStyle:{lineWidth:3,strokeStyle:'#6fb735'},
                        hoverPaintStyle:{lineWidth:3,strokeStyle:'#8fdb00'},
                        overlays: [
                            [ "Arrow", {  location:0.4,paintStyle:{ fillStyle:'#222', strokeStyle:"rgba(255,255,255,0)" }} ],
                            [ "Label", { cssClass:"component",font:"12px sans-serif",label: ' ' + t('Primary key') +" : <span class=\"connection\">" + $(this).parent().find('.title').text() + "</span>"+ ' ' + t('to Foreign Key')+ ' : '+ "<span class=\"connection\">" + $("#table_" + jsonproperties.link + " div[type_class='field_ident']").parent().find('.title').text() + "</span> " }]	
                        ]
                    });
                    dbadmin.marqueur = false;
                });
		
		/* Allows to drag tables */
		$(".table").draggable("destroy").draggable({
		    cursor: 'move',
                    scroll: false ,
		    handle : 'div.title',
		    containment: '#container_bdd',drag: function(event, ui) {
			jsPlumb.repaint( $(".property",this).add(this).toArray());
			$("#outline").fracs('outline', 'redraw');
		    }
		    ,stop:function(){
			jsPlumb.repaint( $(".property",this).add(this).toArray());
		    }
		});

                /* Allows to drop fields in table */
                $(".table").droppable("destroy").droppable({
                    accept: '#field_list div',
                    activeClass: 'ui-state-hover',
                    hoverClass: 'ombre',
                    drop: function(event, ui) {
                        var nom_champ = prompt(t('Please enter a field name') + ' ?');
                        nom_champ = nom_champ.toLowerCase().replace(/[^a-z_]+/g,"");
                        if(nom_champ){
                            var champ = ui.draggable.clone();
                            champ.removeAttr('class').attr("id",$(event.target).attr("id") + "_" + nom_champ).addClass("property");
                            jsonproperties = jQuery.parseJSON(JSON.stringify(ui.draggable.data("attributs")));
                            jsonproperties.entity = $(this).find('.title').text();
                            jsonproperties.name = nom_champ;
                            jsonproperties.oldName = nom_champ;
                            jsonproperties.label = nom_champ;
                            champ.data("attributs",jsonproperties);
                            champ.text(nom_champ);
                            champ.appendTo(this);
			    
                            $("#container_bdd .table").sortable('destroy').sortable({ items: ".property[type_class!='field_ident']" });
			    $("#save").addClass("haveToSave");
                        }
                    }
                });
		
                /* When a connector is linked */
                jsPlumb.bind("jsPlumbConnection", function(event, originalEvent) {
                    if(  !dbadmin.marqueur){                       
                        jsPlumb.detach(event);
                        $("#popup input").data('sourceid',event.sourceId);
                        $("#popup input").data('targetid',event.targetId);
                        $("#popup .entity1").text(event.source.parent().find('.title').text());
                        $("#popup .entity2").text(event.target.find('.title').text());
                        if(event.source.parent().attr('id') == event.targetId){
                            $('#button1').trigger('click');
                        }else{
                            $("#popup").show();
                            $('#conf_box_overlay').show();
                        }
                    }
                });
		
                /* When a connector is cliqued*/
                jsPlumb.bind("click", function(connection, originalEvent) {
                    if (confirm( t('Delete connection from') + ' ' + connection.source.parent().find(".title").text()+ ' ' + t('to') + ' ' + connection.target.parent().find(".title").text() + " ?")){
                        jsPlumb.detach(connection);
                        jsPlumb.removeAllEndpoints(connection.sourceId);
                        $("#" + connection.sourceId).remove();
                    }
                });
            }
        };
        $(document).ready(function() {
            dbadmin.init();
        });
    </script>
