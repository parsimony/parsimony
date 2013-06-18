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

app::$request->page->addCSSFile('lib/colorpicker/colorpicker.css');
app::$request->page->addJSFile('lib/colorpicker/colorpicker.js');
app::$request->page->addJSFile('admin/blocks/css/script.js','footer');
if(strlen(strstr($_SERVER['HTTP_USER_AGENT'],"Firefox")) > 0 ){ 
    app::$request->page->addJSFile('lib/firefoxCompatibility/html5slider.js');
}

/* We get and store client side all CSS selectors from theme style */
$pathTheme =  THEMEMODULE . '/themes/' . THEME . '/' . THEMETYPE . '.css';
$css = new css(PROFILE_PATH . $pathTheme);
$CSSValues = $css->getCSSValues();
?>
<script>
ParsimonyAdmin.CSSValues = <?php echo json_encode(array($pathTheme => $CSSValues)); ?>;
</script>
<?php /* We create a form in order to reset easily all values by .reset()  */ ?>
<form method="POST" id="form_css" action="javascript:void(0);" target="formResult">
    <div style="min-width:230px;position:relative">
        <div id="toolChanges" style="display:none;margin: 0 10px 10px;background:#EBEBEB;border-radius:5px;border: 1px solid #CCC;line-height: 30px;color: #919191;;font-size: 11px;">
            <button id="savemycss" class="tooltip" data-tooltip="<?php echo t('Save'); ?>" data-pos="n" style="border:0;border-radius: 5px 0 0 5px;">
                <span class="sprite sprite-savecss" style="margin:0px auto;vertical-align: middle;"></span>
            </button>
	    <button id="reinitcss" style="height: 30px;border:0;border-left: 1px #FCFCFC solid;position: relative;left: -1px;" class="tooltip" data-tooltip="<?php echo t('Reinit'); ?>" data-pos="n">
                <span class="ui-icon ui-icon-arrowrefresh-1-w"></span>
            </button>
            <span id="nbChanges"> 0 changes</span>
	</div>

	<div id="selectorcontainer">
	    <div id="csspicker" class="cssPickerBTN tooltip" data-tooltip="<?php echo t('CSS Picker', FALSE); ?>"><span class="sprite sprite-picker"></span></div>
            <input type="text" placeholder="e.g. #selector" data-optionsurl="" class="autocomplete" id="current_selector_update" spellcheck="false" />
            <div id="goeditcss"></div>
        </div>
        <div style="color: #444;font-size: 10px;position: relative;">
            <div style="position:absolute;width: 213px;right:0;top: -1px;">
                <a href="#" onclick="$('#formAddMedia').toggle();return false;" style="background: rgb(236, 236, 236);
width: 20px;height: 20px;border-radius: 0 0 0 15px;text-align: center;border: 1px solid rgb(204, 204, 204);
line-height: 16px;position:absolute;margin-left:5px;text-decoration: none;color: #555;font-size: 13px;"> + </a>
                
                <select id="currentMdq" style="color: #8D8D8D;background-color: #F7F7F7;position: absolute;border-left: 0 !important;outline:0;width: 193px;left:20px;line-height: 15px;height: 20px;display: block;border-radius: 0;">
                    <option value="">No media query</option>
                </select>
            </div>
	</div>
	<input type="hidden" id="changecsspath" name="filePath" value="<?php echo THEMEMODULE.'/themes/'.THEME.'/'.THEMETYPE; ?>.css" />
        
    </div>
    <br><br>
    <div id="formAddMedia" style="color: #444;font-size: 10px;line-height: 30px;padding-left:7px;background: #EEE;box-shadow: inset 0px 0px 1px #B3B3B3;" class="none">
        <span style="font-weight:bold">Width</span> : Min <input type="text" style="width:27px" id="mdqMinWidthValue" /> px 
        &nbsp; Max <input type="text" style="width:27px" id="mdqMaxWidthValue" /> px
        <input type="button" value="Add" id="addMdq">
    </div>
    <div id="changecssformcode" class="subTabsContainer">
        <div id="switchtovisuel" class="ssTabCSS">Visuel</div>
        <div id="switchtocode" class="ssTabCSS">Code</div>
    </div>
    
     <div id="threed" class="none">
         <div class="align_center">3D</div>
        X <input type="range" class="ch" id="rotatex" min="-40" max="40" value="0">
        <div class="clearboth"></div>
        Y <input type="range" class="ch" id="rotatey" min="-40" max="40" value="0">
	<div class="clearboth"></div>
        Z <input type="range" class="ch" id="rotatez" min="0" max="1000" value="300">
    </div>
    <div id="css_panel" class="none">
        <div id="changecssform" class="clearboth none swicthcsscode">
            <div id="css_menu" class="clearboth">
                <div class="active" rel="panelcss_tab_general"><div>General</div></div>
                <div rel="panelcss_tab_border"><div>Borders</div></div>
                <div rel="panelcss_tab_background"><div>Back.</div></div>
                <div rel="panelcss_tab_type"><div>Type</div></div>
                <div rel="panelcss_tab_lists"><div>List</div></div>
            </div>
            <div class="panelcss_tab" id="panelcss_tab_general">
                <div class="leftpart"  style="display:inline-block;vertical-align:top;width: 95px;">
                    <label for="box_width">Width</label>
                    <input class="liveconfig spinner align_center prop_width" id="box_width" data-css="width" type="text" value="">
                    <label for="box_height">Height</label>
                    <input class="liveconfig spinner align_center prop_height" id="box_height" data-css="height" type="text" value="">
                    <label for="box_top">Top</label>
                    <input class="liveconfig spinner align_center prop_top" id="box_top" data-css="top" type="text" value="">
                    <label for="box_bottom">Bottom</label>
                    <input class="liveconfig spinner align_center prop_bottom" id="box_bottom" data-css="bottom" type="text" value="">
                    <label for="box_right">Right</label>
                    <input class="liveconfig spinner align_center prop_right" id="box_right" data-css="right" type="text" value="">
                    <label for="box_left">Left</label>
                    <input class="liveconfig spinner align_center prop_left" id="box_left" data-css="left" type="text" value="">
                    <label for="positioning_opacity">Opacity</label>
                    <input class="liveconfig align_center prop_opacity" type="text" id="positioning_opacity" data-css="opacity" value="">
                    <br><input type="range" id="slider-range-max" style="width: 88px;" min="0" max="1" step="0.05" data-css="opacity" value="1">
                </div>
                <div class="rightpart" style="display: inline-block;border-left: solid #ccc 1px;margin-left: 2px;width: 123px;padding-left: 5px;">
                    <label for="positioning_type">Position</label>
                    <select class="select liveconfig prop_position" id="positioning_type" onchange="if(this.value == 'static' || this.value == ''){$('#parsimonyDND').removeClass('positionOK')}else{$('#parsimonyDND').addClass('positionOK')}" data-css="position"><option value=""></option><option value="absolute">absolute</option><option value="relative">relative</option><option value="fixed">fixed</option><option value="static">static</option></select>
                    <label for="float">Float</label>
                    <select class="select liveconfig prop_float" id="box_float" data-css="float"><option value=""></option><option value="left">left</option><option value="right">right</option><option value="none">none</option></select>
                    <label for="clear">Clear</label>
                    <select class="select liveconfig prop_clear" id="box_clear" data-css="clear"><option value=""></option><option value="left">left</option><option value="right">right</option><option value="both">both</option><option value="none">none</option></select>

                    <label for="positioning_visibility">Visibility</label>
                    <select class="select liveconfig prop_visibility" id="positioning_visibility" data-css="visibility"><option value=""></option><option value="inherit">inherit</option><option value="visible">visible</option><option value="hidden">hidden</option></select>
                    <label for="display">Display</label>
                    <select class="select liveconfig prop_display" id="display" data-css="display"><option value=""></option><option value="block">block</option><option value="compact">compact</option><option value="inline">inline</option><option value="inline-block">inline-block</option><option value="inline-table">inline-table</option><option value="list-item">list-item</option><option value="list-item">list-item</option><option value="none">none</option><option value="run-in">run-in</option><option value="table">table</option><option value="table-caption">table-caption</option><option value="table-cell">table-cell</option>
                        <option value="table-column-group ">table-column-group </option>
                        <option value="table-footer-group">table-footer-group</option>
                        <option value="table-header-group">table-header-group </option>
                        <option value="table-row">table-row </option>
                        <option value="table-row-group">table-row-group</option>
                    </select>
                    <label for="positioning_overflow">Overflow</label>
                    <select class="select liveconfig prop_overflow spinner" id="positioning_overflow" data-css="overflow"><option value=""></option><option value="visible">visible</option><option value="hidden">hidden</option><option value="scroll">scroll</option><option value="auto">auto</option></select>
                    <label for="positioning_zindex">Z-index</label>
                    <input class="liveconfig align_center prop_z-index" type="text" id="positioning_zindex" data-css="z-index" value="">
                </div>                
                <div id="metrics">
                    <div>
                        <label style="float:none;display:block;text-align: left;padding-left: 4px;">Margin</label>
                        <label style="float:none;display:block;text-align: left;padding-left: 4px;">Padding</label>
                    </div>
                    <div class="graph" style="position:relative">
                        <div class="margin representation border" init="0" style="position:relative;width:197px;height:138px;margin-top:5px">
                            <label>Margin</label>
                            <div style="position: absolute;left: 75px;top: 7px;"><input class="spinner repr_top prop_margin-top" data-css="margin-top" type="text"></div>
                            <div style="position: absolute;left: 8px;top: 61px;"><input class="spinner repr_left prop_margin-left" data-css="margin-left" type="text"></div>
                            <div style="position: absolute;left: 149px;top: 60px;"><input class="spinner repr_right prop_margin-right" data-css="margin-right" type="text"></div>
                            <div style="position: absolute;left: 76px;top: 113px;"><input class="spinner repr_bottom prop_margin-bottom" data-css="margin-bottom" type="text"></div>
                            <input class="resultcss liveconfig prop_margin" data-css="margin" style="position:absolute;left: 50px;top: -51px;width: 140px;" onload="$(this).trigger('change')" type="text" value="">
                        </div>
                        <div class="padding representation border" init="0" style="position:absolute;top: 36px;left: 54px;width: 96px;height: 75px;">
                            <label>Padding</label>
                            <div style="position: absolute;left: 30px;top: 6px;"><input class="spinner repr_top prop_padding-top" data-css="padding-top" type="text"></div>
                            <div style="position: absolute;left: 4px;top: 28px;"><input class="spinner repr_left prop_padding-left" data-css="padding-left" type="text"></div>
                            <div style="position: absolute;left: 56px;top: 28px;"><input class="spinner repr_right prop_padding-right" data-css="padding-right" type="text"></div>
                            <div style="position: absolute;left: 30px;top: 50px;"><input class="spinner repr_bottom prop_padding-bottom" data-css="padding-bottom" type="text"></div>
                            <input class="resultcss liveconfig prop_padding" data-css="padding" style="position:absolute;left: 4px;top: -68px;width: 140px;" onload="$(this).trigger('change')" type="text" value="">
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
                    <input class="resultcss liveconfig prop_border-width spinner" data-css="border-width" onload="$(this).trigger('change')" type="text" value="">
                    <input class="repr_top spinner prop_border-top-width" type="text" data-css="border-top-width"/>
                    <input class="repr_left spinner prop_border-left-width" type="text" data-css="border-left-width"/>
                    <input class="repr_right spinner prop_border-right-width" type="text" data-css="border-right-width"/>
                    <input class="repr_bottom spinner prop_border-bottom-width" type="text" data-css="border-bottom-width"/>
                </div>
                <div class="width representation" init="#000" style="width:35px;display:inline-block">
                    <div>Color</div>
                    <input class="resultcss liveconfig colorpicker2 prop_border-color" data-css="border-color" onload="$(this).trigger('change')" type="text" value="">
                    <input class="repr_top colorpicker2 prop_border-top-color" data-css="border-top-color" type="text" />
                    <input class="repr_left colorpicker2 prop_border-left-color" data-css="border-left-color" type="text" />
                    <input class="repr_right colorpicker2 prop_border-right-color" data-css="border-right-color" type="text" />
                    <input class="repr_bottom colorpicker2 prop_border-bottom-color" data-css="border-bottom-color" type="text" />
                </div>
                <div class="style representation" init="solid" style="width:45px;display:inline-block">
                    <div>Style</div>
                    <input type="text" class="resultcss liveconfig autocomplete prop_border-style" data-css="border-style" id="border_style" onload="$(this).trigger('change')"  data-options='["none","solid","dashed","dotted","double","groove","ridge", "inset", "outset"]'>
                    <input type="text" class="repr_top autocomplete prop_border-top-style" data-options='["none","solid","dashed","dotted","double","groove","ridge", "inset", "outset"]' data-css="border-top-style" id="border_style_top" />
                    <input type="text" class="repr_left autocomplete prop_border-left-style" data-options='["none","solid","dashed","dotted","double","groove","ridge", "inset", "outset"]' data-css="border-left-style" id="border_style_left" />
                    <input type="text" class="repr_right autocomplete prop_border-right-style" data-options='["none","solid","dashed","dotted","double","groove","ridge", "inset", "outset"]' data-css="border-right-style" id="border_style_right" />
                    <input type="text" class="repr_bottom autocomplete prop_border-bottom-style" data-options='["none","solid","dashed","dotted","double","groove","ridge", "inset", "outset"]' data-css="border-bottom-style" id="border_style_bottom" />
                </div>
                <div class="radius representation" init="0" style="width:35px;display:inline-block">
                    <div>Radius</div>
                    <input class="resultcss liveconfig prop_border-radius" data-css="border-radius" onload="$(this).trigger('change')" type="text" value="">
                    <input class="repr_top prop_border-top-radius" type="text" data-css="border-top-radius"/>
                    <input class="repr_left prop_border-left-radius" type="text" data-css="border-left-radius"/>
                    <input class="repr_right prop_border-right-radius" type="text" data-css="border-right-radius"/>
                    <input class="repr_bottom prop_border-bottom-radius" type="text" data-css="border-bottom-radius"/>
                </div>  
                <div>
                    <div style="margin-top:20px; color: #444;">Box Shadow :</div>
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
                    <div class="box-shadow" style="display:inline-block;vertical-align:top;border-left: solid #ccc 1px;margin-left: 5px;padding-left: 5px;">
                        <div style="margin-top:10px">
                            <label style="padding-top: 5px;" for="fd-slider-handle-blur1" id="blur1_label">Blur</label>
                            <input type="text" id="blurbox" class="spinner">
                        </div>
                        <div>
                            <label style="padding-top: 5px;" for="shadow-color1">Color</label>
                            <input class="colorpicker2" type="text" id="shadow-colorbox">
                        </div>
                    </div>
                    <input class="liveconfig prop_box-shadow" data-css="box-shadow" onload="$(this).trigger('change')" id="box-shadow" type="hidden">
                </div>
            </div>

            <div class="panelcss_tab hiddenTab" id="panelcss_tab_background">

                <label for="background">Background</label>
                <input class="liveconfig prop_background input" style="margin-left:10px;width: 201px;position: relative;height: 17px;" id="background" type="text" data-css="background" value="">

                <label for="background_image">Image</label>
                <div>
                    <span class="ui-icon ui-icon-folder-open explorer" rel="background_image" style="float:left;margin-right:5px;"></span>
                    <input class="liveconfig prop_background_image input" style="width: 190px;float:left;height: 17px;margin-bottom: 5px;" id="background_image" type="text" data-css="background-image" value="">
                </div>

                <label for="background_color">Color</label>
                <input class="liveconfig prop_background-color colorpicker2" id="background_color" data-css="background-color" type="text" value="">

                <label for="background_size">Size</label>
                <input type="text" class="liveconfig prop_background-size autocomplete" id="background_size" data-options='["cover","contain"]' data-css="background-size" />

                <label for="background_hpos">Position X. Y.</label>
                <input type="text" class="liveconfig prop_background-position spinner" data-css="background-position" value="">

                <label for="background_attachment">Attachment</label>
                <select class="liveconfig prop_background-attachment" id="background_attachment" data-css="background-attachment"><option></option><option value="fixed">fixed</option><option value="scroll">scroll</option></select>

                <label for="background_repeat">Repeat</label>
                <select class="liveconfig prop_background-repeat" id="background_repeat" data-css="background-repeat"><option></option><option value="no-repeat">no-repeat</option><option value="repeat">repeat</option><option value="repeat-x">repeat-x</option><option value="repeat-y">repeat-y</option></select>

                <label for="background_clip">Clip</label>
                <select class="liveconfig prop_background-clip" id="background_clip" data-css="background-clip"><option></option><option value="padding-box">padding-box</option><option value="border-box">border-box</option><option value="content-box">content-box</option></select>

                <label for="background_origin">Origin</label>
                <input type="text" class="liveconfig prop_background-origin" id="background-origin" data-options='["fixed","scroll"]' data-css="background-origin"><option></option><option value="padding-box">padding-box</option><option value="border-box">border-box</option><option value="content-box">content-box</option></select>

            </div>
            <div class="panelcss_tab hiddenTab" id="panelcss_tab_type">

                <label for="text_font">Font</label>
                <input type="text" style="margin-left:10px;width: 201px;position: relative;height: 17px;margin-bottom: 5px;" class="liveconfig autocomplete" id="text_font" data-options='["Arial, Helvetica, sans-serif","Times New Roman, Times, serif","Courier New, Courier, mono","Times New Roman, Times, serif","Georgia, Times New Roman, Times, serif","Verdana, Arial, Helvetica, sans-serif","Geneva, Arial, Helvetica, sans-serif"]' data-css="font-family" />
		<div class="leftpart"  style="display:inline-block;vertical-align:top;width: 95px;">
		    <label for="text_size">Size</label>
		    <input class="liveconfig prop_font-size spinner" type="text" data-css="font-size">
		    <label for="text_color">Color</label>
		    <input class="liveconfig prop_color colorpicker2" id="text_color" data-css="color" type="text">
		    <label for="text_lineheight" title="Line-Height">Height</label>
		    <input class="liveconfig prop_line-height spinner" type="text" data-css="line-height">
		    <label for="text_lineheight" title="Letter-Space">Space</label>
		    <input class="liveconfig prop_letter-spacing spinner" type="text" data-css="letter-spacing">
		    <label for="text_case">Overflow</label>
		    <input type="text" class="liveconfig prop_text-overflow autocomplete" id="text_overflow" data-options='["ellipsis","clip","ellipsis-word"]' data-css="text-overflow">
		    <label for="text_lineheight" title="Text-Indent">Indent</label>
		    <input class="liveconfig prop_text-indent spinner" type="text" data-css="text-indent">
		</div>
		
		<div class="rightpart" style="display: inline-block;border-left: solid #ccc 1px;margin-left: 2px;width: 123px;padding-left: 5px;">
		    <label for="text_align">Align</label>
		    <select class="liveconfig prop_text-align" id="text_align" data-css="text-align"><option></option><option value="center">Center</option><option value="right">Right</option><option value="left">Left</option><option value="justify">Justify</option></select>
		    <label for="text_weight">Weight</label>
		    <select class="select liveconfig prop_font-weight" id="text_weight" data-css="font-weight"><option></option><option value="normal">normal</option><option value="bold">bold</option><option value="bolder">bolder</option><option value="lighter">lighter</option><option value="100">100</option><option value="200">200</option><option value="300">300</option><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option><option value="800">800</option><option value="900">900</option></select>

		    <label for="text_case">Case</label>
		    <select class="select liveconfig prop_text-transform" id="text_case" data-css="text-transform"><option></option><option value="capitalize">capitalize</option><option value="uppercase">uppercase</option><option value="lowercase">lowercase</option></select>

		    <label for="word-wrap" title="Word Wrap">W. wrap</label>
		    <select class="liveconfig prop_word-wrap select" id="word-wrap" data-css="word-wrap"><option></option><option value="normal">normal</option><option value="break-word">break-word</option></select>

		    <label for="text-wrap" title="Text Wrap">T. wrap</label>
		    <select class="liveconfig prop_text-wrap" id="text-wrap" data-css="text-wrap"><option></option><option value="normal">normal</option><option value="unrestricted">unrestricted</option><option value="suppress">suppress</option></select>

		    <label for="text_style">Style</label>
		    <select class="liveconfig prop_font-style" type="text" data-css="font-style"><option></option><option value="normal">normal</option><option value="italic">italic</option><option value="oblique">oblique</option><option value="inherit">inherit</option></select>

		</div>
                <input class="liveconfig prop_text-decoration" data-css="text-decoration" id="css-decoration" type="hidden">
                <div class="decoration">
		   
                    <div style="display: inline-block;margin-left: 2px;">
                        <input style="margin-left: 6px;" id="text_underline" data-data-css="underline" class="option" type="checkbox"><label style="padding-top: 5px;margin: 3px 0px;" for="text_underline">Underline</label>
                        <input id="text_overline" data-data-css="overline" class="option" type="checkbox"><label style="padding-top: 5px;margin: 3px 0px;" for="text_overline">Overline</label>
                    </div>
                    <div class="box-shadow" style="display:inline-block;margin-left: 2px">
                        <input style="margin-left: 6px;" id="text_linethrough" data-data-css="line-through" class="option" type="checkbox"><label style="padding-top: 5px;margin: 3px 0px;" for="text_linethrough">Line-through</label>
                        <input data-data-css="none" class="option" type="checkbox"><label style="padding-top: 5px;margin: 3px 0px;" for="text_linethrough">None</label>
                    </div>
                </div>
		    
		<div style="margin-top:20px; color: #444;">Text Shadow :</div>
                <div class="text-shadow" style="display: inline-block;vertical-align: top;margin-left: 5px;" class="clearboth text-shadow" >             
                    <label for="fd-slider-handle-h-offset1">X</label>
                    <input type="text" id="h-offsettext" class="spinner">
                    <label for="fd-slider-handle-v-offset1">Y</label>
                    <input type="text" id="v-offsettext" class="spinner">
                </div>
                <div class="text-shadow" style="display: inline-block;vertical-align: top;margin-left: 5px;">        
                    <label for="fd-slider-handle-blur1">Blur</label>
                    <input type="text" id="blurtext" class="spinner">
                    <label for="shadow-color1">Color</label> 
                    <input class="colorpicker2" id="shadow-colortext" type="text">
                </div>
                <input class="liveconfig prop_text-shadow align_center" data-css="text-shadow" id="text-shadow" onload="$(this).trigger('change')" type="hidden" value="">
            </div>

            <div class="panelcss_tab hiddenTab" id="panelcss_tab_lists">
                <label for="list-style-image">Image</label>
                <input class="liveconfig prop_list-style-image spinner" type="text" data-css="list-style-image" value=""><br>
                <label for="position">Position</label>
                <select class="select prop_list-style-position liveconfig" id="list-style-position" data-css="list-style-position">
                    <option value=""></option><option value="inside">inside</option><option value="outside">outside</option>
                </select><br>
                <label for="list-style-type">Type</label>
                <input type="text" class="liveconfig prop_list-style-type autocomplete" id="list-style-type" data-options='["none","armenian","circle","cjk-ideographic","decimal","decimal-leading-zero","disc","georgian","hebrew","hiragana","hiragana-iroha","katakana","katakana-iroha","lower-alpha","lower-greek","lower-latin","lower-roman","square","upper-alpha","upper-latin","upper-roman"]' data-css="list-style-type">
            </div>
        </div>
        <div id="changecsscode" class="clearboth swicthcsscode"></div>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function(){
        
        var code = function () {return false;};
        window.document.getElementById("form_css").addEventListener("submit", code, false);
        
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
                $.each( $.parseJSON(this.dataset.options), function(i, value){
                     options += '<option value="' + value + '" />';
                });
                $("#parsidatalist").html(options);
            }
        });
    });

    /* Color Picker */
    var currentColorPicker = $(".colorpicker2");
    var picker = new Color.Picker({
        callback: function(hex) {
            currentColorPicker.val("#" + hex).trigger("change");
        }
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

    /*box shadows init*/
    $('.box-shadow').on("change", "input",function(){
        var box = '';
        box = $('#h-offsetbox').val() +' '+ $('#v-offsetbox').val() +' '+ $('#blurbox').val() +' '+ $('#shadow-colorbox').val();
        box = box.replace(/[\s]{2,}/g,' ');
        $('#box-shadow').val(box).trigger("change");
    });

    /*text shadows init*/
    $('.text-shadow').on("change", "input", function(){
        var text = '';
        text = $('#h-offsettext').val() + ' ' + $('#v-offsettext').val() + ' ' + $('#blurtext').val() + ' ' + $('#shadow-colortext').val();
        text = text.replace(/[\s]{2,}/g,' ');
        $('#text-shadow').val(text).trigger("change");
    });

    /* Spinner */
    $(".spinner").keydown(function (event) {
        if (event.keyCode == 40 || event.keyCode == 38) {
            event.preventDefault();
            var num = this.value;
            if(num!='') num = parseInt(num);
            else num = 0;
            var text = this.value.replace(num,'');
            if (event.keyCode == 40) {
                this.value = (num - 1) + text;
            } else if (event.keyCode == 38) {
                this.value = (num + 1) + text;
            }
            $(this).trigger("change");
        }
    });

</script>