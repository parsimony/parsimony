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

app::$request->page->addJSFile(BASE_PATH . 'lib/dnd/parsimonyDND.js');
app::$request->page->addJSFile(BASE_PATH . 'lib/CodeMirror/lib/codemirror.js');
app::$request->page->addCSSFile(BASE_PATH . 'lib/CodeMirror/theme/default.css');
app::$request->page->addCSSFile(BASE_PATH . 'lib/CodeMirror/lib/codemirror.css');
app::$request->page->addJSFile(BASE_PATH . 'lib/CodeMirror/mode/xml/xml.js');
app::$request->page->addJSFile(BASE_PATH . 'lib/CodeMirror/mode/css/css.js');
app::$request->page->addCSSFile(BASE_PATH . 'lib/colorpicker/colorpicker.css');
app::$request->page->addJSFile(BASE_PATH . 'lib/colorpicker/colorpicker.js');
app::$request->page->addJSFile(BASE_PATH . 'admin/blocks/css/script.js');

if (isset($_POST['typeProgress']) && $_POST['typeProgress'] == 'Theme') {
    $filePath = PROFILE_PATH . THEMEMODULE . '/themes/' . THEME . '/' . THEMETYPE . '.css';
    $filePath2 = PROFILE_PATH . MODULE . '/' . THEMETYPE . '.css';
} else {
    $filePath = PROFILE_PATH . MODULE . '/' . THEMETYPE . '.css';
    $filePath2 = PROFILE_PATH . THEMEMODULE . '/themes/' . THEME . '/' . THEMETYPE . '.css';
}
if (!isset($filePath2) && $filePath == PROFILE_PATH . MODULE . '/' . THEMETYPE . '.css')
    $filePath2 = PROFILE_PATH . THEMEMODULE . '/themes/' . THEME . '/' . THEMETYPE . '.css';
elseif (!isset($filePath2) && $filePath == PROFILE_PATH . THEMEMODULE . '/themes/' . THEME . '/' . THEMETYPE . '.css')
    $filePath2 = PROFILE_PATH . MODULE . '/' . THEMETYPE . '.css';

$css = new css($filePath);

if (isset($_POST['selector']) || isset($_POST['idBlock'])) {
    if (isset($_POST['idBlock']))
        $selector = '#' . $_POST['idBlock'];
    else
        $selector = $_POST['selector'];
}else {
    $selector = false;
}

$selectors = $css->getAllSselectors();
?>

<form method="POST" id="form_css" action="<?php echo BASE_PATH; ?>admin/saveCSS" target="ajaxhack">
    <div style="min-width:230px;position:relative">
        <input type="hidden" name="typeofinput" id="typeofinput" value="code" />
        <div id="changecssformcode" class="subTabsContainer">
            <div id="switchtovisuel" class="ssTab">Visuel</div>
            <div id="switchtocode" class="ssTab">Code</div>
        </div>
	<div id="selectorcontainer">
	    <div id="csspicker" class="tooltip" data-tooltip="<?php echo t('CSS Picker', FALSE); ?>"></div>
	    <input type="text" placeholder="CSS Selector e.g. #selector" name="selector" data-optionsurl="" class="autocomplete" id="current_selector_update" />
	</div>
        <input type="hidden" id="current_selector_update_prev" />
        <input type="hidden" id="current_stylesheet_nb" />
        <input type="hidden" id="current_stylesheet_nb_rule" />
        <input type="hidden" id="current_stylesheet_rules" />
	<input type="hidden" id="changecsspath" name="filePath" value="<?php echo THEMEMODULE.'/themes/'.THEME.'/'.THEMETYPE; ?>.css" />
        <div id="goeditcss"></div>

    </div>
     <div id="threed" class="none">
         <div class="align_center">3D</div>
        X <input type="range" class="ch" id="rotatex" min="-40" max="40" value="0">
        <div class="clearboth"></div>
        Y <input type="range" class="ch" id="rotatey" min="-40" max="40" value="0">
	<div class="clearboth"></div>
        Z <input type="range" class="ch" id="rotatez" min="0" max="1000" value="300">
    </div>
    <div id="css_panel" style="" class="none">
        <div>
            <div id="savemycss" onclick="$(this).closest('form').trigger('submit')" class="adminbtnrightslide"><img src="<?php echo BASE_PATH; ?>admin/img/savecss.png" style="margin:0px auto;vertical-align: middle;">  <?php echo t('Save'); ?></div>
        </div>
        <div id="changecssform" class="clearboth none swicthcsscode">
            <div id="css_menu" class="clearboth">
                <div class="active" rel="panelcss_tab_general">General</div>
                <div rel="panelcss_tab_border">Borders</div>
                <div rel="panelcss_tab_background">Back.</div>
                <div rel="panelcss_tab_type">Type</div>
                <div rel="panelcss_tab_lists">List</div>
                <?php /*        <div class="button" rel="panelcss_tab_transition">Transition</div>
                  <div class="button" rel="panelcss_tab_transform">Transform</div>
                  <div class="button" rel="panelcss_tab_animation">Animation</div> */ ?>
            </div>
            <div class="panelcss_tab" id="panelcss_tab_general">
                <div class="leftpart"  style="display:inline-block;vertical-align:top;width: 86px;">
                    <label for="box_width">Width</label>
                    <input class="liveconfig spinner align_center" name="width" css="width" type="text" value="">
                    <label for="box_height">Height</label>
                    <input class="liveconfig spinner align_center" name="height" css="height" type="text" value="">
                    <label for="box_width">Top</label>
                    <input class="liveconfig spinner align_center" name="top" css="top" type="text" value="">
                    <label for="box_width">Bottom</label>
                    <input class="liveconfig spinner align_center" name="bottom" css="bottom" type="text" value="">
                    <label for="box_width">Right</label>
                    <input class="liveconfig spinner align_center" name="right" css="right" type="text" value="">
                    <label for="box_width">Left</label>
                    <input class="liveconfig spinner align_center" name="left" css="left" type="text" value="">
                    <label for="positioning_opacity">Opacity</label>
                    <input class="liveconfig align_center" type="text" id="positioning_opacity" name="opacity" css="opacity" value="">
                    <br><input type="range" id="slider-range-max" style="width: 88px;" min="0" max="1" step="0.05" css="opacity" value="1">
                </div>
                <div class="rightpart" style="display: inline-block;border-left: solid #999 1px;margin-left: 2px;width: 123px;padding-left: 5px;">
                    <label for="positioning_type">Position</label>
                    <select class="" id="positioning_type" name="position" css="position"><option value=""></option><option value="absolute">absolute</option><option value="relative">relative</option><option value="fixed">fixed</option><option value="static">static</option></select>
                    <label for="float">Float</label>
                    <select class="select liveconfig" id="box_float" name="float" css="float"><option value=""></option><option value="left">left</option><option value="right">right</option><option value="none">none</option></select>
                    <label for="clear">Clear</label>
                    <select class="select liveconfig" id="box_clear" name="clear" css="clear"><option value=""></option><option value="left">left</option><option value="right">right</option><option value="both">both</option><option value="none">none</option></select>

                    <label for="positioning_visibility">Visibility</label>
                    <select class="select liveconfig" id="positioning_visibility" name="visibility" css="visibility"><option value=""></option><option value="inherit">inherit</option><option value="visible">visible</option><option value="hidden">hidden</option></select>
                    <label for="display">Display</label>
                    <select class="select liveconfig" id="display" name="display" css="display"><option value=""></option><option value="block">block</option><option value="compact">compact</option><option value="inline">inline</option><option value="inline-block">inline-block</option><option value="inline-table">inline-table</option><option value="list-item">list-item</option><option value="list-item">list-item</option><option value="none">none</option><option value="run-in">run-in</option><option value="table">table</option><option value="table-caption">table-caption</option><option value="table-cell">table-cell</option>
                        <option value="table-column-group ">table-column-group </option>
                        <option value="table-footer-group">table-footer-group</option>
                        <option value="table-header-group">table-header-group </option>
                        <option value="table-row">table-row </option>
                        <option value="table-row-group">table-row-group</option>
                    </select>
                    <label for="positioning_overflow">Overflow</label>
                    <select class="select liveconfig spinner" id="positioning_overflow" name="overflow" css="overflow"><option value=""></option><option value="visible">visible</option><option value="hidden">hidden</option><option value="scroll">scroll</option><option value="auto">auto</option></select>
                    <label for="positioning_zindex">Z-index</label>
                    <input class="liveconfig align_center" type="text" id="positioning_zindex" name="z-index" css="z-index" value="">
                </div>                
                <div id="metrics">
                    <div>
                        <label style="float:none;display:block;text-align: left;padding-left: 4px;">Margin</label>
                        <label style="float:none;display:block;text-align: left;padding-left: 4px;">Padding</label>
                    </div>
                    <div class="graph" style="position:relative">
                        <div class="margin representation border" init="0" style="position:relative;width:180px;height:130px;">
                            <label>Margin</label>
                            <div style="position: absolute;left: 75px;top: 7px;"><input class="spinner repr_top" type="text"></div>
                            <div style="position: absolute;left: 8px;top: 61px;"><input class="spinner repr_left" css="margin-left" type="text"></div>
                            <div style="position: absolute;left: 149px;top: 60px;"><input class="spinner repr_right" css="margin-right" type="text"></div>
                            <div style="position: absolute;left: 76px;top: 113px;"><input class="spinner repr_bottom" css="margin-bottom" type="text"></div>
                            <input class="resultcss liveconfig" css="margin" style="position:absolute;left: 50px;top: -62px;width: 140px;" onload="$(this).trigger('change')" name="margin" type="text" value="">
                        </div>
                        <div class="padding representation border" init="0" style="position:absolute;top:40px;left:54px;width:90px;height:70px;">
                            <label>Padding</label>
                            <div style="position: absolute;left: 30px;top: 6px;"><input class="spinner repr_top" css="padding-top" type="text"></div>
                            <div style="position: absolute;left: 4px;top: 28px;"><input class="spinner repr_left" css="padding-left" type="text"></div>
                            <div style="position: absolute;left: 56px;top: 28px;"><input class="spinner repr_right" css="padding-right" type="text"></div>
                            <div style="position: absolute;left: 30px;top: 50px;"><input class="spinner repr_bottom" css="padding-bottom" type="text"></div>
                            <input class="resultcss liveconfig" css="padding" style="position:absolute;left: 4px;top: -74px;width: 140px;" onload="$(this).trigger('change')" type="text" name="padding" value="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="panelcss_tab hiddenTab" id="panelcss_tab_border">                   
                <div class="labels" style="width:38px;display:inline-block">
                    <div>All</div>
                    <div>Top</div>
                    <div>Left</div>
                    <div>Right</div>
                    <div>Bottom</div>
                </div>
                <div class="color representation" init="0" style="width:35px;display:inline-block">
                    <div>Width</div>
                    <div><input class="resultcss liveconfig spinner" name="border-width" css="border-width" onload="$(this).trigger('change')" type="text" value=""></div>
                    <div><input class="repr_top spinner" type="text" css="border-top-width"/></div>
                    <div><input class="repr_left spinner" type="text" css="border-left-width"/></div>
                    <div><input class="repr_right spinner" type="text" css="border-right-width"/></div>
                    <div><input class="repr_bottom spinner" type="text" css="border-bottom-width"/></div>
                </div>
                <div class="width representation" init="#000" style="width:35px;display:inline-block">
                    <div>Color</div>
                    <div><input class="resultcss liveconfig colorpicker2" name="border-color" css="border-color" onload="$(this).trigger('change')" type="text" value=""></div>
                    <div><input class="repr_top colorpicker2" css="border-top-color" type="text" /></div>
                    <div><input class="repr_left colorpicker2" css="border-left-color" type="text" /></div>
                    <div><input class="repr_right colorpicker2" css="border-right-color" type="text" /></div>
                    <div><input class="repr_bottom colorpicker2" css="border-bottom-color" type="text" /></div>
                </div>
                <div class="style representation" init="solid" style="width:45px;display:inline-block">
                    <div>Style</div>
                    <div><input type="text" class="resultcss liveconfig autocomplete" css="border-style" id="border_style" onload="$(this).trigger('change')"  data-options='["none","solid","dashed","dotted","double","groove","ridge", "inset", "outset"]' name="border-style"></div>
                    <div><input type="text" class="repr_top autocomplete" data-options='["none","solid","dashed","dotted","double","groove","ridge", "inset", "outset"]' css="border-top-style" id="border_style_top" /></div>
                    <div><input type="text" class="repr_left autocomplete" data-options='["none","solid","dashed","dotted","double","groove","ridge", "inset", "outset"]' css="border-left-style" id="border_style_left" /></div>
                    <div><input type="text" class="repr_right autocomplete" data-options='["none","solid","dashed","dotted","double","groove","ridge", "inset", "outset"]' css="border-right-style" id="border_style_right" /></div>
                    <div><input type="text" class="repr_bottom autocomplete" data-options='["none","solid","dashed","dotted","double","groove","ridge", "inset", "outset"]' css="border-bottom-style" id="border_style_bottom" /></div>
                </div>
                <div class="radius representation" init="0" style="width:35px;display:inline-block">
                    <div>Radius</div>
                    <div><input class="resultcss liveconfig" name="border-radius" css="border-radius" onload="$(this).trigger('change')" type="text" value=""></div>
                    <div><input class="repr_top" type="text" css="border-top-radius"/></div>
                    <div><input class="repr_left" type="text" css="border-left-radius"/></div>
                    <div><input class="repr_right" type="text" css="border-right-radius"/></div>
                    <div><input class="repr_bottom" type="text" css="border-bottom-radius"/></div>
                </div>  
                <div>
                    <div style="margin-top:20px; color: white;">Box Shadow :</div>
                    <div class="box-shadow" style="display:inline-block;vertical-align:top;margin-left: 5px;">
                        <div style="margin-top:10px">
                            <label style="padding-top: 5px;" for="fd-slider-handle-h-offset1" id="h-offset1_label">Angle</label>
                            <input type="text" id="h-offsetbox" class="spinner">
                        </div>
                        <div>
                            <label style="padding-top: 5px;" for="fd-slider-handle-v-offset1" id="v-offset1_label">Distance</label>
                            <input type="text" id="v-offsetbox" class="spinner">
                        </div>
                    </div>
                    <div class="box-shadow" style="display:inline-block;vertical-align:top;border-left: solid #999 1px;margin-left: 5px;padding-left: 5px;">
                        <div style="margin-top:10px">
                            <label style="padding-top: 5px;" for="fd-slider-handle-blur1" id="blur1_label">Blur</label>
                            <input type="text" id="blurbox" class="spinner">
                        </div>
                        <div>
                            <label style="padding-top: 5px;" for="shadow-color1">Color</label>
                            <input class="colorpicker2" type="text" id="shadow-colorbox">
                        </div>
                    </div>
                    <input class="liveconfig" name="box-shadow" css="box-shadow" onload="$(this).trigger('change')" id="box-shadow" type="hidden">
                </div>
            </div>

            <div class="panelcss_tab hiddenTab" id="panelcss_tab_background">

                <label for="background">Background</label>
                <input class="liveconfig input" style="margin-left:10px;width: 185px;position: relative;height: 20px;" id="background" type="text" name="background" css="background" value="">

                <label for="background_image">Image</label>
                <div padding-left: 13px;>
                    <span class="ui-icon ui-icon-folder-open explorer" rel="background_image" style="float:left;margin-right:5px;"></span>
                    <input class="liveconfig input" style="width: 160px;float:left;height: 20px;" id="background_image" type="text" name="background-image" css="background-image" value="">
                </div>

                <label for="background_color">Color</label>
                <input class="liveconfig colorpicker2" id="background_color" css="background-color" name="background-color" type="text" value="">

                <label for="background_size">Size</label>
                <input type="text" class="liveconfig autocomplete" id="background_size" data-options='["cover","contain"]' css="background-size" name="background-size" />

                <label for="background_hpos">Position X. Y.</label>
                <input type="text" class="liveconfig spinner" name="background-position" css="background-position" value="">

                <label for="background_attachment">Attachment</label>
                <select class="liveconfig" id="background_attachment" css="background-attachment" name="background-attachment"><option></option><option value="fixed">fixed</option><option value="scroll">scroll</option></select>

                <label for="background_repeat">Repeat</label>
                <select class="liveconfig" id="background_repeat" name="background-repeat" css="background-repeat"><option></option><option value="no-repeat">no-repeat</option><option value="repeat">repeat</option><option value="repeat-x">repeat-x</option><option value="repeat-y">repeat-y</option></select>

                <label for="background_clip">Clip</label>
                <select class="liveconfig" id="background_clip" name="background-clip" css="background-clip"><option></option><option value="padding-box">padding-box</option><option value="border-box">border-box</option><option value="content-box">content-box</option></select>

                <label for="background_origin">Origin</label>
                <input type="text" class="liveconfig" id="background-origin" name="background-origin" data-options='["fixed","scroll"]' css="background-origin"><option></option><option value="padding-box">padding-box</option><option value="border-box">border-box</option><option value="content-box">content-box</option></select>

            </div>
            <div class="panelcss_tab hiddenTab" id="panelcss_tab_type">

                <label for="text_font">Font</label>
                <input type="text" class="liveconfig autocomplete" id="text_font" name="font-family" data-options='[ "Arial, Helvetica, sans-serif","Times New Roman, Times, serif",Courier New, Courier, mono","Times New Roman, Times, serif","Georgia, Times New Roman, Times, serif","Verdana, Arial, Helvetica, sans-serif","Geneva, Arial, Helvetica, sans-serif"]' css="font-family" />

                <label for="text_size">Size</label>
                <input class="liveconfig spinner" type="text" name="font-size" css="font-size">
                <label for="text_color">Color</label>
                <input class="liveconfig colorpicker2" id="text_color" name="color" css="color" type="text">
                <label for="text_lineheight">LineHeight</label>
                <input class="liveconfig spinner" type="text" name="line-height" css="line-height">
                <label for="text_lineheight">LetterSpace</label>
                <input class="liveconfig spinner" type="text" name="letter-spacing" css="letter-spacing">
                <label for="text_lineheight">TextIndent</label>
                <input class="liveconfig spinner" type="text" name="text-indent" css="text-indent">

                <label for="text_weight">Weight</label>
                <select class="select liveconfig" id="text_weight" name="font-weight" css="font-weight"><option></option><option value="<?php echo $css->getPropertyValue($selector, 'font-weight') ?>"><?php echo ucfirst($css->getPropertyValue($selector, 'font-weight')) ?></option><option value="normal">normal</option><option value="bold">bold</option><option value="bolder">bolder</option><option value="lighter">lighter</option><option value="100">100</option><option value="200">200</option><option value="300">300</option><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option><option value="800">800</option><option value="900">900</option></select>

                <input class="liveconfig" name="text-decoration" css="text-decoration" id="css-decoration" type="hidden">

                <label for="text_case">Case</label>
                <select class="select liveconfig" id="text_case" name="text-transform" css="text-transform"><option></option><option value="<?php echo $css->getPropertyValue($selector, 'text-transform') ?>"><?php echo ucfirst($css->getPropertyValue($selector, 'text-transform')) ?></option><option value="capitalize">capitalize</option><option value="uppercase">uppercase</option><option value="lowercase">lowercase</option></select>

                <label for="">Word wrap</label>
                <select class="liveconfig select" id="word-wrap" name="word-wrap" css="word-wrap"><option></option><option value="normal">normal</option><option value="break-word">break-word</option></select>

                <label for="text_align">Align</label>
                <select class="liveconfig" id="text_align" name="text-align" css="text-align"><option value="<?php echo $css->getPropertyValue($selector, 'text-align') ?>"><?php echo ucfirst($css->getPropertyValue($selector, 'text-align')) ?></option><option value="center">Center</option><option value="right">Right</option><option value="left">Left</option><option value="justify">Justify</option></select>

                <label for="text_style">Style</label>
                <select class="liveconfig" type="text" name="font-style" css="font-style"><option value="<?php echo $css->getPropertyValue($selector, 'font-style') ?>"><?php echo ucfirst($css->getPropertyValue($selector, 'font-style')) ?></option><option value="normal">normal</option><option value="italic">italic</option><option value="oblique">oblique</option><option value="inherit">inherit</option></select>

                <label for="text_case">Over flow</label>
                <input type="text" class="liveconfig autocomplete" id="text_overflow" name="text-overflow" data-options='["ellipsis","clip","ellipsis-word"]' css="text-overflow">

                <label for="fd-slider-handle-v-offset1">Text wrap</label>
                <select class="liveconfig" id="text-wrap" name="text-wrap" css="text-wrap"><option value="<?php echo $css->getPropertyValue($selector, 'text-wrap') ?>"><?php echo ucfirst($css->getPropertyValue($selector, 'text-wrap')) ?></option><option value="normal">normal</option><option value="unrestricted">unrestricted</option><option value="suppress">suppress</option></select>

                <div class="decoration">
                    <div style="display: inline-block;margin-left: 2px;">
                        <input style="margin-left: 6px;" id="text_underline" data-css="underline" class="option" type="checkbox"><label style="padding-top: 5px;margin: 3px 0px;" for="text_underline">Underline</label>
                        <input id="text_overline" data-css="overline" class="option" type="checkbox"><label style="padding-top: 5px;margin: 3px 0px;" for="text_overline">Overline</label>
                    </div>
                    <div class="box-shadow" style="display:inline-block;margin-left: 2px">
                        <input style="margin-left: 6px;" id="text_linethrough" data-css="line-through" class="option" type="checkbox"><label style="padding-top: 5px;margin: 3px 0px;" for="text_linethrough">Line-through</label>
                        <input data-css="none" class="option" type="checkbox"><label style="padding-top: 5px;margin: 3px 0px;" for="text_linethrough">None</label>
                    </div>
                </div>

                <div style="padding-top: 5px;" class="clearboth text-shadow" >
                    <div style="color:white;padding-left: 6px;">Shadow</div>               
                    <label for="fd-slider-handle-h-offset1">X</label>
                    <input type="text" id="h-offsettext" class="spinner">
                    <label for="fd-slider-handle-v-offset1">Y</label>
                    <input type="text" id="v-offsettext" class="spinner">
                </div>
                <div class="text-shadow">        
                    <label for="fd-slider-handle-blur1">Blur</label>
                    <input type="text" id="blurtext" class="spinner">
                    <label for="shadow-color1">Color</label> 
                    <input class="colorpicker2" id="shadow-colortext" type="text">
                </div>
                <input class="liveconfig align_center" name="text-shadow" css="text-shadow" id="text-shadow" onload="$(this).trigger('change')" type="hidden" value="">
            </div>

            <div class="panelcss_tab hiddenTab" id="panelcss_tab_lists">
                <label for="list-style-image">Image</label>
                <input class="liveconfig spinner" type="text" name="list-style-image" css="list-style-image" value=""><br>
                <label for="position">Position</label>
                <select class="select liveconfig" id="list-style-position" name="list-style-position" css="list-style-position">
                    <option value=""></option><option value="inside">inside</option><option value="outside">outside</option>
                </select><br>
                <label for="list-style-type">Type</label>
                <input type="text" class="liveconfig autocomplete" id="list-style-type" name="list-style-type" data-options='["none","armenian","circle","cjk-ideographic","decimal","decimal-leading-zero","disc","georgian","hebrew","hiragana","hiragana-iroha","katakana","katakana-iroha","lower-alpha","lower-greek","lower-latin","lower-roman","square","upper-alpha","upper-latin","upper-roman"]' css="list-style-type">
            </div>
            <?php /*
              <div class="panelcss_tab hiddenTab" id="panelcss_tab_transition">
              <label for="transition">Transition (property duration timing-function delay)</label>
              <input class="liveconfig spinner" type="text" name="transition" value="<?php echo $css->getPropertyValue($selector, 'transition') ?>"><br>
              <label for="transition-property">Transition Property</label>
              <input class="liveconfig spinner" type="text" name="transition-property" value="<?php echo $css->getPropertyValue($selector, 'transition-property') ?>"><br>
              <label for="transition-duration">Transition Duration</label>
              <input class="liveconfig spinner" type="text" name="transition-duration" value="<?php echo $css->getPropertyValue($selector, 'transition-duration') ?>"><br>
              <label for="position">Transition Timing Function</label>
              <select class="select liveconfig autocomplete" id="transition-timing-function" name="transition-timing-function">
              <option value="<?php echo $css->getPropertyValue($selector, 'transition-timing-function') ?>"><?php echo ucfirst($css->getPropertyValue($selector, 'transition-timing-function')) ?></option>
              <option value="linear">linear</option>
              <option value="ease">ease</option>
              <option value="ease-in">ease-in</option>
              <option value="ease-out">ease-out</option>
              <option value="ease-in-out">ease-in-out</option>
              <option value="cubic-bezier(n,n,n,n)">cubic-bezier(n,n,n,n)</option>
              </select><br>
              <label for="transition-delay">Transition Delay</label>
              <input class="liveconfig spinner" type="text" name="transition-delay" value="<?php echo $css->getPropertyValue($selector, 'transition-delay') ?>"><br>
              </div>
              <div class="panelcss_tab hiddenTab" id="panelcss_tab_transform">
              <select class="select liveconfig autocomplete" id="transform" name="transform">
              <option value="<?php echo $css->getPropertyValue($selector, 'transition-timing-function') ?>"><?php echo ucfirst($css->getPropertyValue($selector, 'transition-timing-function')) ?></option>
              <option value="none">none</option>
              <option value="matrix(n,n,n,n,n,n)">matrix(n,n,n,n,n,n)</option>
              <option value="matrix3d(n,n,n,n,n,n,n,n,n,n,n,n,n,n,n,n)">matrix3d(n,n,n,n,n,n,n,n,n,n,n,n,n,n,n,n)</option>
              <option value="translate(x,y)">translate(x,y)</option>
              <option value="translate3d(x,y,z)">translate3d(x,y,z)</option>
              <option value="translateX(x)">translateX(x)</option>
              <option value="translateY(y)">translateY(y)</option>
              <option value="translate3d(x,y,z)">translate3d(x,y,z)</option>
              <option value="translateZ(z)">translateZ(z)</option>
              <option value="scale(x,y)">scale(x,y)</option>
              <option value="scale3d(x,y,z)">scale3d(x,y,z)</option>
              <option value="scaleX(x)">scaleX(x)</option>
              <option value="scaleY(y)">scaleY(y)</option>
              <option value="scaleZ(z)">scaleZ(z)</option>
              <option value="rotate(angle)">rotate(angle)</option>
              <option value="rotate3d(x,y,z,angle)">rotate3d(x,y,z,angle)</option>
              <option value="rotateX(angle)">rotateX(angle)</option>
              <option value="rotateY(angle)">rotateY(angle)</option>
              <option value="rotateZ(angle)">rotateZ(angle)</option>
              <option value="skew(x-angle,y-angle)">skew(x-angle,y-angle)</option>
              <option value="skewX(angle)">skewX(angle)</option>
              <option value="skewY(angle)">skewY(angle)</option>

              </select><br>
              <label for="transform-origin">Transform Origin(x-axis y-axis z-axis)</label>
              <input class="liveconfig spinner" type="text" name="transform-origin" value="<?php echo $css->getPropertyValue($selector, 'transform-origin') ?>"><br>
              <label for="perspective(n)">Perspective(n)</label>
              <input class="liveconfig spinner" type="text" name="perspective" value="<?php echo $css->getPropertyValue($selector, 'perspective') ?>"><br>
              3D Transforms
              <label for="transform-style">Transform Style(x-axis y-axis z-axis)</label>
              <select class="select liveconfig autocomplete" id="transform-style" name="transform-style">
              <option value="<?php echo $css->getPropertyValue($selector, 'transform-style') ?>"><?php echo ucfirst($css->getPropertyValue($selector, 'transform-style')) ?></option>
              <option value="flat">flat</option>
              <option value="preserve-3d">preserve-3d</option>
              </select><br>
              <label for="perspective-origin">Perspective-origin (x-axis y-axis)</label>
              <input class="liveconfig spinner" type="text" name="perspective-origin" value="<?php echo $css->getPropertyValue($selector, 'perspective-origin') ?>"><br>
              <label for="backface-visibility">Backface-visibility</label>
              <select class="select liveconfig autocomplete" id="backface-visibility" name="backface-visibility">
              <option value="<?php echo $css->getPropertyValue($selector, 'backface-visibility') ?>"><?php echo ucfirst($css->getPropertyValue($selector, 'backface-visibility')) ?></option>
              <option value="visible">visible</option>
              <option value="hidden">hidden</option>
              </select><br>
              </div>
              <div class="panelcss_tab hiddenTab" id="panelcss_tab_animation">

              <label for="@keyframes">@keyframes { animationname keyframes-selector {css-styles;} }</label>
              <label for="Start">Start</label>
              <div>
              <label></label>
              <input class="liveconfig spinner" type="text" name="animationname" value="<?php echo $css->getPropertyValue($selector, 'animationname') ?>"><br>
              <input class="liveconfig spinner" type="text" name="keyframes-selector" value="<?php echo $css->getPropertyValue($selector, 'keyframes-selector') ?>"><br>
              <input class="liveconfig spinner" type="text" name="css-styles" value="<?php echo $css->getPropertyValue($selector, 'css-styles') ?>"><br>
              </div>
              A REVOIR
              <label for="More Keyframes">More Keyframes</label><a href="#" onClick="$(this).next().clone().show();return false;">One more</a>
              <div id="newkeyframe" style="visibility:hidden;">
              <input class="liveconfig spinner" type="text" name="animationname" value="<?php echo $css->getPropertyValue($selector, 'animationname') ?>"><br>
              <input class="liveconfig spinner" type="text" name="keyframes-selector" value="<?php echo $css->getPropertyValue($selector, 'keyframes-selector') ?>"><br>
              <input class="liveconfig spinner" type="text" name="css-styles" value="<?php echo $css->getPropertyValue($selector, 'css-styles') ?>"><br>
              </div>

              <label for="End">End</label>
              <div>
              <input class="liveconfig spinner" type="text" name="animationname" value="<?php echo $css->getPropertyValue($selector, 'animationname') ?>"><br>
              <input class="liveconfig spinner" type="text" name="keyframes-selector" value="<?php echo $css->getPropertyValue($selector, 'keyframes-selector') ?>"><br>
              <input class="liveconfig spinner" type="text" name="css-styles" value="<?php echo $css->getPropertyValue($selector, 'css-styles') ?>"><br>
              </div>

              <label for="animation">animation(name duration timing-function delay iteration-count direction)</label>
              <input class="liveconfig spinner" type="text" name="animation" value="<?php echo $css->getPropertyValue($selector, 'animation') ?>"><br>

              <label for="ananimation-name">animation-name</label>
              <select class="select liveconfig autocomplete" id="animation-name" name="animation-name">
              <option value="<?php echo $css->getPropertyValue($selector, 'keyframename') ?>"><?php echo ucfirst($css->getPropertyValue($selector, 'keyframename')) ?></option>
              <option value="keyframename">keyframename</option>
              <option value="none">none</option>
              </select><br>

              <label for="animation-duration">animation-duration</label>
              <input class="liveconfig spinner" type="text" name="animation-duration" value="<?php echo $css->getPropertyValue($selector, 'animation-duration') ?>"><br>

              <label for="animation-timing-function">animation-timing-function</label>
              <select class="select liveconfig autocomplete" id="animation-timing-function" name="animation-timing-function">
              <option value="<?php echo $css->getPropertyValue($selector, 'animation-timing-function') ?>"><?php echo ucfirst($css->getPropertyValue($selector, 'animation-timing-function')) ?></option>
              <option value="linear">linear</option>
              <option value="ease">ease</option>
              <option value="ease-in">ease-in</option>
              <option value="ease-out">ease-out</option>
              <option value="ease-in-out">ease-in-out</option>
              <option value="cubic-bezier(n,n,n,n)">cubic-bezier(n,n,n,n)</option>
              </select><br>

              <label for="animation-delay">animation-delay</label>
              <input class="liveconfig spinner" type="text" name="animation-delay" value="<?php echo $css->getPropertyValue($selector, 'animation-delay') ?>"><br>

              <label for="animation-iteration-count">animation-iteration-count (Number of repetitions of the animation or Infinite) Default value: 1</label>
              <input class="liveconfig spinner" type="text" name="animation-iteration-count" value="<?php echo $css->getPropertyValue($selector, 'animation-iteration-count') ?>"><br>

              <label for="animation-direction">animation-direction</label>
              <select class="select liveconfig autocomplete" id="animation-direction" name="animation-direction">
              <option value="<?php echo $css->getPropertyValue($selector, 'animation-direction') ?>"><?php echo ucfirst($css->getPropertyValue($selector, 'animation-direction')) ?></option>
              <option value="normal">normal</option>
              <option value="alternate">alternate</option>
              </select><br>
              <label for="animation-play-state">animation-play-state</label>
              <select class="select liveconfig autocomplete" id="animation-play-state" name="animation-play-state">
              <option value="<?php echo $css->getPropertyValue($selector, 'animation-play-state') ?>"><?php echo ucfirst($css->getPropertyValue($selector, 'animation-play-state')) ?></option>
              <option value="paused">paused</option>
              <option value="running">running</option>
              </select><br>
              </div>
             */ ?>

        </div>
        <div id="changecsscode" class="clearboth swicthcsscode"></div>
        <input type="hidden" name="action" value="saveCSS" />
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function(){
        $("select option").each(function(){
            if(!$(this).val().length){
                $(this).text('Default');
                $(this).val('');
            };
        });

        $("#changecssformcode").on('click','#switchtovisuel,#switchtocode',function(){
            if(this.id == 'switchtovisuel'){ // go to form
                if($("#current_selector_update").val() == ""){
                    $(".gotoform:first").trigger("click");
                }else{
                    blockAdminCSS.displayCSSConf($("#changecsspath").val(),$("#current_selector_update").val());
                }
            }else{ // go to code
                var elmt = $($("#current_selector_update").val().replace(/:[hover|focus|active|visited|link|target]/,"") + ":first",ParsimonyAdmin.currentBody)
                if(elmt.length > 0){
                    elmt.addClass("cssPicker");
                    blockAdminCSS.getCSSForCSSpicker();
                }else{
                    blockAdminCSS.addNewSelectorCSS( $("#changecsspath").val(), $("#current_selector_update").val());
                    blockAdminCSS.openCSSCode();
                }
            }
        });

        $("#panelcss").on('click','#css_menu > div',function(){
            $("#css_menu > .active").removeClass("active");
            $(this).addClass("active");
	    $(".panelcss_tab").addClass("hiddenTab");
            $("#" + $(this).attr("rel")).removeClass("hiddenTab");
        });
        
        $("#panelcss").on('click','#savemycss',function(){
            if($("#typeofinput").val()=='form') { // update prev styles
                var nbstyle = $("#current_stylesheet_nb").val();
                var nbrule = $("#current_stylesheet_nb_rule").val();
                $("#current_stylesheet_rules").val(ParsimonyAdmin.currentDocument.styleSheets[nbstyle].cssRules[nbrule].style.cssText);
            }
            $(this).closest('form').trigger('submit');
        });
        
        $(document).on("click", ".autocomplete",function(){
            /* We clear datalist */
            $("#parsidatalist").empty();
            $(this).attr("list","parsidatalist");
            if(this.id == "current_selector_update"){
                $.getJSON( "admin/getCSSSelectors?filePath=" + $("#changecsspath").val(), function(data){
                    $.each( data, function(i, value){
                         options += '<option value="' + value + '" />';
                    });
                    $("#parsidatalist").html(options);
                });
            }else{
                var options = "";
                $.each( $(this).data('options'), function(i, value){
                     options += '<option value="' + value + '" />';
                });
                $("#parsidatalist").html(options);
            }
        });
    });
    
    /* CSSpicker 3D */
    $("#threed").on('change','.ch',function(){
	if(!ParsimonyAdmin.currentBody.classList.contains("threed")) ParsimonyAdmin.currentBody.classList.add("threed");
        $(ParsimonyAdmin.currentBody).add("#blockOverlay").css('-webkit-transform','rotateX(' + $("#rotatex").val() + 'deg) rotateY(' + $("#rotatey").val() + 'deg)');
	blockAdminCSS.iframeStyleSheet.removeRule("0");
	blockAdminCSS.iframeStyleSheet.insertRule('.threed * {-webkit-transform:rotateX(' + $("#rotatex").val()/10 + 'deg) rotateY(' + $("#rotatey").val()/10 + 'deg) translateZ(' + $("#rotatez").val() + 'px);box-shadow: '+ (-($("#rotatey").val()/10)) + 'px ' + ($("#rotatex").val()/10) + 'px 3px #aaa;background-color:#fff}',"0");
    });

    /* Color Picker */
    var currentColorPicker = $(".colorpicker2");
    var picker = new Color.Picker({
        callback: function(hex) {
            currentColorPicker.val("#" + hex).trigger("change");
        }
    });
    $("#panelcss").on('click','.colorpicker2',function(){
        currentColorPicker = $(this);
        picker.el.style.display = "block";
        picker.el.style.top = ($(this).offset().top) + 20 + "px";
        picker.el.style.left = ($(this).offset().left - 200) + "px";
    });
    $("#panelcss").on('blur','.colorpicker2',function(){
        picker.el.style.display = "none";
    });

    $("#panelcss").on('change','.representation input:not(".resultcss"),.representation select:not(".resultcss")',function(){
        obj = $(this).closest('.representation');
        reprToInput(obj);
    });

    function reprToInput(obj){
        if($('.repr_top',obj).val() == '') $('.repr_top',obj).val($(obj).attr("init"))
        if($('.repr_right',obj).val() == '') $('.repr_right',obj).val($(obj).attr("init"));
        if($('.repr_bottom',obj).val() == '') $('.repr_bottom',obj).val($(obj).attr("init"));
        if($('.repr_left',obj).val() == '') $('.repr_left',obj).val($(obj).attr("init"));
        var top = $('.repr_top',obj).val();
        var right = $('.repr_right',obj).val();
        var bottom = $('.repr_bottom',obj).val();
        var left = $('.repr_left',obj).val();
        var result = '';

        if(top == bottom && top == right && top == left){
            result = top;
        }else if(right == left && top == bottom ){
            result = top + ' ' + right;
        }else if(right == left & top != bottom){
            result = top + ' ' + right + ' ' + bottom;
        } else{
            result = top + ' ' + right + ' ' + bottom + ' ' + left;
        }
        var event = new $.Event('change');
        event.preventDefault();
        $('.resultcss',obj).val(result).trigger(event);
    }

    $("#panelcss").on('change','.resultcss',function(event){
        obj = $(this).closest('.representation');
        reprToInput2(obj);
    });
    function reprToInput2(obj){
        var expl = $('.resultcss',obj).val();
        if(expl.length > 0){
            expld = expl.split(' ');
            switch(expld.length){
                case 1 : $('.repr_top',obj).val(expl);$('.repr_right',obj).val(expl);$('.repr_bottom',obj).val(expl);$('.repr_left',obj).val(expl);
                    break;
                case 2 : $('.repr_top',obj).val(expld[0]);$('.repr_right',obj).val(expld[1]);$('.repr_bottom',obj).val(expld[0]);$('.repr_left',obj).val(expld[1]);
                    break;
                case 3 : $('.repr_top',obj).val(expld[0]);$('.repr_right',obj).val(expld[1]);$('.repr_bottom',obj).val(expld[2]);$('.repr_left',obj).val(expld[1]);
                    break;
                case 4 : $('.repr_top',obj).val(expld[0]);$('.repr_right',obj).val(expld[1]);$('.repr_bottom',obj).val(expld[2]);$('.repr_left',obj).val(expld[3]);
                    break;
            }
        }
    }
    /*$('#background_image').live("change",function(){
        alert("");
    });*/

    /*$('.background input,.background select').live("change",function(){
        var back = '';
        var image = $('#background_image').val();    
        if(image != ''){
            var back_im =  'url('+ "'"+ $('#background_image').val()+"') ";
        }else{
            var back_im = ' ';
        }      
        back = $('#background_color').val() + ' ' + back_im + ' ' + $('#background_repeat').val() + ' ' + $('#background_attachment').val() + ' ' + $('#background_hpos').val() + ' ' + $('#background_vpos').val();
        back = back.replace(/[\s]{2,}/g,' ');
        $('#css-background').val(back).trigger("change");
    });*/
    $("#panelcss").on("change",'.decoration input',function(){
        var deco='';
        $('.decoration .option:checked').each(function(){
            deco = deco+ $(this).attr('data-css') +' ';
        });
        deco = deco.replace(/[\s]{2,}/g,' ');
        $('#css-decoration').val(deco).trigger("change");
    });

    /*box shadows*/ /*init*/
    if($('#box-shadow').length > 0){
        var params = $('#box-shadow').val().split(/ /);
        $("#h-offsetbox").val(params[0]);
        $("#v-offsetbox").val(params[1]);
        $("#blurbox").val(params[2]);
        $("#shadow-colorbox").val(params[3]);
        $('.box-shadow').off('change', "input");
        $('.box-shadow').on("change", "input",function(){
            var box = '';
            box = $('#h-offsetbox').val() +' '+ $('#v-offsetbox').val() +' '+ $('#blurbox').val() +' '+ $('#shadow-colorbox').val();
            box = box.replace(/[\s]{2,}/g,' ');
            $('#box-shadow').val(box).trigger("change");
        });
    }

    /*text shadows*/ /*init*/
    if($('#text-shadow').length > 0){
        var params = $('#text-shadow').val().split(/ /);
        $("#h-offsettext").val(params[0]);
        $("#v-offsettext").val(params[1]);
        $("#blurtext").val(params[2]);
        $("#shadow-colortext").val(params[3]);
        $('.text-shadow').on("change", "input", function(){
            var text = '';
            text = $('#h-offsettext').val() + ' ' + $('#v-offsettext').val() + ' ' + $('#blurtext').val() + ' ' + $('#shadow-colortext').val();
            text = text.replace(/[\s]{2,}/g,' ');
            $('#text-shadow').val(text).trigger("change");
        });
    }

    $("#panelcss").on("change",'#changecsspath',function(){
        if($('#current_selector_update').val().length > 2) blockAdminCSS.displayCSSConf($('#changecsspath').val(),$('#current_selector_update').val());
    });
    
    $("#panelcss").on("click",'#goeditcss',function(){
        var selector = $('#current_selector_update').val();
        var path = $('#changecsspath').val();
        if($("#typeofinput").val() == 'code') {
	    blockAdminCSS.openCSSCode();
            blockAdminCSS.addNewSelectorCSS( path, selector);
        }else{
            blockAdminCSS.displayCSSConf(path,selector);
        }
    });

    $("#panelcss").on("keypress",'#current_selector_update',function(e){
        var code = e.keyCode || e.which; 
        if(code == 13) {
            $("#goeditcss").trigger("click");
        }
    });
    
    $("#panelcss").on('keyup keydown',"#current_selector_update", function(event) {
        event.stopPropagation();
        var code = event.keyCode || event.which; 
        if(code != 13) {
            if (event.type == 'keyup') {
                $('.cssPicker',ParsimonyAdmin.currentBody).removeClass('cssPicker');
                $("#container " + $('#current_selector_update').val(),ParsimonyAdmin.currentBody).addClass('cssPicker');
            }
	    $("#panelcss").addClass("CSSSearch");
        }
    });
    $( "#css_panel" ).on('change','#slider-range-max', function( event, ui ) {
        var val = $(this).val() ;
        if(val==1) val = '';
        $( "#positioning_opacity" ).val( val ).trigger("change").trigger("keyup");
    });    
    $('div.titleTab').off('click');
    $('div.titleTab').on('click',function(){
        var next = $(this).next();	
        if(next.is('ul')) $(this).next().slideToggle();       
    });

    /* Spinner */
    $(".spinner").keydown(function (event) {
        if (event.keyCode == 40 || event.keyCode == 38) {
            event.preventDefault();
            var num = $(this).val();
            if(num!='') num = parseInt(num);
            else num = 0;
            var text = $(this).val().replace(num,'');
            if (event.keyCode == 40) {
                $(this).val((num - 1) + text);
            } else if (event.keyCode == 38) {
                $(this).val((num + 1) + text);
            }
            $(this).trigger("change");
        }
    });

</script>