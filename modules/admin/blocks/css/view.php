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

app::$response->page->addCSSFile('lib/colorpicker/colorpicker.css');
app::$response->page->addJSFile('lib/colorpicker/colorpicker.js');
app::$response->page->addJSFile('admin/blocks/css/block.js','footer');

?>
<div id="toolChanges">
	<button id="savemycss" class="tooltip" data-tooltip="Sauvegarder" data-pos="n"></button>
	<button id="reinitcss" class="tooltip" data-tooltip="Reinit" data-pos="n"></button>
	<span id="nbChanges" onclick="document.getElementById('listchanges').classList.toggle('none');"> 0 changes</span>
	<div id="listchanges" class="none"></div>
</div>
<div id="mediaquerieslabel">Media queries</div>
<input type="checkbox" name="slide" id="checkmedia" onclick="Parsimony.blocks['admin_css'].findSelectorsByElement(document.body);document.getElementById('mediaqueries').classList.remove('none');" />
<label for="checkmedia" id="labelmedia">
  <div id="btnmedia">
	<span></span>
  </div>  
</label>
<div id="mediaqueries" style="text-align: right;font-size: 11px;" class="none">
	<div id="formAddMedia" style="line-height: 30px;text-align: left;padding-left:7px;border-top:1px solid #F3F3F3;">
		Screen width From <input type="text" style="width:27px;text-align:right" id="mdqMinWidthValue" placeholder="&infin;" />px 
		&nbsp; To <input type="text" style="width:27px;text-align:right" id="mdqMaxWidthValue" placeholder="&infin;" />px
		<div id="removeMDQ">X</div>
	</div>
	<div id="mediaqueriesdisplay">
		<div id="mdqlabel" onclick="document.getElementById('mediaqueriesdisplay').classList.toggle('hide');">Media queries</div>
		<div id="scopeMediaQueries"></div>
		<div id="arrow-down"></div>
		<div id="globalcssscope" data-min="0" data-max="9999" data-media="" class="mediaq active"></div>
	</div>
	<input type="hidden" id="currentMdq" data-range="" />
</div>
<?php /* We create a form in order to reset easily all values by .reset(), but not media queries inputs  */ ?>
<form method="POST" id="form_css" action="javascript:void(0);" target="formResult">
	<div id="selectorcontainer">
	   <div id="csspicker" class="cssPickerBTN tooltip" data-tooltip="<?php echo t('CSS Picker'); ?>"><span class="sprite sprite-picker"></span></div>
		<input type="text" placeholder="e.g. #selector" data-optionsurl="" class="autocomplete" id="current_selector_update" spellcheck="false" />
	</div>
	<input type="hidden" id="changecsspath" name="filePath" />
	<div id="changecssformcode" class="subTabsContainer">
		<div id="switchtovisuel" class="ssTabCSS"><?php echo t('Visual'); ?></div>
		<div id="switchtocode" class="ssTabCSS"><?php echo t('Code'); ?></div>
	</div>

	 <div id="threed" class="none">
		 <div class="align_center">3D</div>
		X <input type="range" class="ch" id="rotatex" min="-40" max="40" value="0">
		<div class="clearboth"></div>
		Y <input type="range" class="ch" id="rotatey" min="-40" max="40" value="0">
	<div class="clearboth"></div>
		Z <input type="range" class="ch" id="rotatez" min="60" max="400" value="100">
	</div>
	<div id="css_panel" class="none">
		<div id="changecssform" class="clearboth none swicthcsscode">
			<div id="css_menu" class="clearboth">
				<div class="cssTab active" rel="panelcss_tab_general" data-title="General"></div>
				<div class="cssTab" rel="panelcss_tab_border" data-title="Borders"></div>
				<div class="cssTab" rel="panelcss_tab_background" data-title="Back."></div>
				<div class="cssTab" rel="panelcss_tab_type" data-title="Type"></div>
				<div class="cssTab" rel="panelcss_tab_lists" data-title="List"></div>
			</div>
			<div class="panelcss_tab" id="panelcss_tab_general">
				<div class="leftpart" style="display:inline-block;vertical-align:top;width: 95px;">
					<label for="box_width">Width</label>
					<input placeholder="auto" class="liveconfig spinner prop_width" id="box_width" data-css="width" type="text">
					<label for="box_height">Height</label>
					<input placeholder="auto" class="liveconfig spinner prop_height" id="box_height" data-css="height" type="text">
					<label for="box_top">Top</label>
					<input placeholder="auto" class="liveconfig spinner prop_top" id="box_top" data-css="top" type="text">
					<label for="box_bottom">Bottom</label>
					<input placeholder="auto" class="liveconfig spinner prop_bottom" id="box_bottom" data-css="bottom" type="text">
					<label for="box_right">Right</label>
					<input placeholder="auto" class="liveconfig spinner prop_right" id="box_right" data-css="right" type="text">
					<label for="box_left">Left</label>
					<input placeholder="auto" class="liveconfig spinner prop_left" id="box_left" data-css="left" type="text">
					<label for="positioning_opacity">Opacity</label>
					<input placeholder="1" class="liveconfig prop_opacity" type="text" id="positioning_opacity" data-css="opacity" value="">
					<br><input type="range" id="slider-range-max" style="width: 88px;" min="0" max="1" step="0.05" data-css="opacity" value="1">
				</div>
				<div class="rightpart" style="display: inline-block;border-left: solid #D3D3D3 1px;width: 118px;padding-left: 7px;">
					<label for="positioning_type">Position</label>
					<select class="liveconfig prop_position" id="positioning_type" onchange="if(this.value == 'static' || this.value == ''){$('#parsimonyDND').removeClass('positionOK')}else{$('#parsimonyDND').addClass('positionOK')}" data-css="position"><option value=""></option><option value="absolute">absolute</option><option value="relative">relative</option><option value="fixed">fixed</option><option value="static">static</option></select>
					<label for="float">Float</label>
					<select class="liveconfig prop_float" id="box_float" data-css="float"><option value=""></option><option value="left">left</option><option value="right">right</option><option value="none">none</option></select>
					<label for="clear">Clear</label>
					<select class="liveconfig prop_clear" id="box_clear" data-css="clear"><option value=""></option><option value="left">left</option><option value="right">right</option><option value="both">both</option><option value="none">none</option></select>

					<label for="positioning_visibility">Visibility</label>
					<select class="liveconfig prop_visibility" id="positioning_visibility" data-css="visibility"><option value=""></option><option value="inherit">inherit</option><option value="visible">visible</option><option value="hidden">hidden</option></select>
					<label for="display">Display</label>
					<select class="liveconfig prop_display" id="display" data-css="display"><option value=""></option><option value="block">block</option><option value="compact">compact</option><option value="inline">inline</option><option value="inline-block">inline-block</option><option value="inline-table">inline-table</option><option value="list-item">list-item</option><option value="list-item">list-item</option><option value="none">none</option><option value="run-in">run-in</option><option value="table">table</option><option value="table-cell">table-cell</option></select>
					<label for="positioning_overflow">Overflow</label>
					<select class="liveconfig prop_overflow spinner" id="positioning_overflow" data-css="overflow"><option value=""></option><option value="visible">visible</option><option value="hidden">hidden</option><option value="scroll">scroll</option><option value="auto">auto</option></select>
					<label for="positioning_zindex">Z-index</label>
					<input placeholder="auto" class="liveconfig prop_z-index spinner" type="text" id="positioning_zindex" data-css="z-index">
				</div>
				<div id="metrics">
					<div>
						<label style="display:block;">Margin</label>
						<label style="display:block;">Padding</label>
					</div>
					<div class="graph" style="position:relative">
						<div class="margin representation border" init="0" style="position:relative;width:214px;height:138px;margin-top:5px">
							<label>Margin</label>
							<input style="left: 89px;top: 7px;" class="spinner repr_top prop_margin-top" data-css="margin-top" data-sufix="px" type="text">
							<input style="left: 5px;top: 61px;" class="spinner repr_left prop_margin-left" data-css="margin-left" data-sufix="px" type="text">
							<input style="left: 173px;top: 61px;" class="spinner repr_right prop_margin-right" data-css="margin-right" data-sufix="px" type="text">
							<input style="left: 89px;top: 113px;" class="spinner repr_bottom prop_margin-bottom" data-css="margin-bottom" data-sufix="px" type="text">
							<input class="resultcss liveconfig prop_margin" data-css="margin" style="left: 49px;top: -52px;width: 140px;" type="text">
						</div>
						<div class="padding representation border" init="0" style="position: absolute;top: 36px;left: 46px;width: 125px;height: 75px;">
							<label>Padding</label>
							<input style="left: 44px;top: 10px;" class="spinner repr_top prop_padding-top" data-css="padding-top" data-sufix="px" type="text">
							<input style="left: 4px;top: 30px;" class="spinner repr_left prop_padding-left" data-css="padding-left" data-sufix="px" type="text">
							<input style="left: 83px;top: 30px;" class="spinner repr_right prop_padding-right" data-css="padding-right" data-sufix="px" type="text">
							<input style="left: 44px;top: 50px;" class="spinner repr_bottom prop_padding-bottom" data-css="padding-bottom" data-sufix="px" type="text">
							<input class="resultcss liveconfig prop_padding" data-css="padding" style="left: 4px;top: -59px;width: 140px;" type="text">
						</div>
					</div>
				</div>
			</div>
			<div class="panelcss_tab hiddenTab" id="panelcss_tab_border">
				<div id="panelcss_tab_border_general">
					<div class="labels" style="position: relative;float: left;width:70px;height:70px;">
						<div data-targetcss="border-top" class="borderMarkers" style="top: 0px;left: 25px;"><div style="border-top: 4px solid rgb(179, 179, 179);"></div></div>
						<div data-targetcss="border-left" class="borderMarkers" style="top: 25px;left:0px;"><div style="border-left: 4px solid rgb(179, 179, 179);"></div></div>
						<div data-targetcss="border" class="borderMarkers active" style="top: 25px;left: 25px;" class="active"><div style="border: 4px solid rgb(179, 179, 179);"></div></div>
						<div data-targetcss="border-right" class="borderMarkers" style="top: 25px;left: 50px;"><div style="border-right: 4px solid rgb(179, 179, 179);"></div></div>
						<div data-targetcss="border-bottom" class="borderMarkers" style="top: 50px;left: 25px;"><div style="border-bottom: 4px solid rgb(179, 179, 179);"></div></div>
					</div>
					<div class="labelborders" style="float:left;width: 140px;padding-left: 16px;">
						<label>Width</label>
						<input class="rulePart spinner borderWidth" type="text" data-sufix="px" /><div class="tooltip clearBorder" data-tooltip="Clear this rule" data-pos="n" style="display:inline-block;margin-left:14px">X</div>
						<label>Color</label>
						<input class="rulePart borderColor colorpicker2" style="margin-bottom: 0px;margin-left: 0px;" type="text" /><span class="colorpicker3"></span>
						<label>Style</label>
						<select class="rulePart borderStyle">
							<option></option><option>none</option><option>solid</option><option>dashed</option><option>dotted</option><option>double</option><option>groove</option><option>ridge</option><option>inset</option><option>outset</option>
						</select>
					</div>
					<input class="liveconfig prop_border none" data-css="border" type="text">
					<input class="liveconfig prop_border-top none" data-css="border-top" type="text">
					<input class="liveconfig prop_border-left none" data-css="border-left" type="text">
					<input class="liveconfig prop_border-right none" data-css="border-right" type="text">
					<input class="liveconfig prop_border-bottom none" data-css="border-bottom" type="text">
				</div>
				<div class="radius representation" init="0" style="clear: both;text-align: center;">
					<div style="text-align: left;">Radius</div>
					<div class="divrad" style="border-radius: 5px;margin-right:7px"></div>
					<input class="resultcss liveconfig prop_border-radius" data-css="border-radius" type="text" style="width: 160px;">
					<div>
						<div class="divrad" style="border-radius: 20px 0 0 0;margin-right: 10px;"></div>
						<input class="repr_top prop_border-top-radius spinner" type="text" data-css="border-top-radius" data-sufix="px" style="margin-right: 53px;" />
						<input class="repr_right prop_border-right-radius spinner" type="text" data-css="border-right-radius" data-sufix="px">
						<div class="divrad" style="border-radius: 0 20px 0 0;display: inline-block;margin-left: 10px;vertical-align: middle;"></div>
					</div>
					<div>
						<div class="divrad" style="border-radius: 0 0 0 20px;margin-right: 10px;"></div>
						<input class="repr_left prop_border-left-radius spinner" type="text" data-css="border-left-radius" data-sufix="px" style="margin-right: 53px;">
						<input class="repr_bottom prop_border-bottom-radius spinner" type="text" data-css="border-bottom-radius" data-sufix="px">
						<div class="divrad" style="border-radius: 0 0 20px 0;margin-left: 10px;"></div>
					</div>
				</div>
				<div class="shadowWidget box-shadow">
					<div style="margin: 10px 0;">Box Shadow</div>
					<div style="width: 47%;display:inline-block;vertical-align:top;">
						<div class="panelPointer" style="float:left;margin-right: 7px;margin-top: 6px;">
							<div class="cross1"></div>
							<div class="cross2"></div>
							<div class="pointer"></div>
						</div>
						<label style="min-width:15px">X</label>
						<input type="text" class="spinner rulePart h-offset" data-sufix="px">
						<label style="min-width:15px">Y</label>
						<input type="text" class="spinner rulePart v-offset" data-sufix="px">
					</div>
					<div style="width: 53%;display:inline-block;vertical-align:top;border-left: solid rgb(219, 219, 219) 1px;padding-left: 5px;">
						<label>Blur</label>
						<input type="text" class="spinner rulePart blurShadow" data-sufix="px">
						<label>Color</label>
						<input type="text" class="colorpicker2 rulePart colorShadow"><span class="colorpicker3"></span>
					</div>
					<input class="liveconfig prop_box-shadow resultShadow none" data-css="box-shadow" id="box-shadow" type="text">
				</div>
			</div>

			<div class="panelcss_tab hiddenTab" id="panelcss_tab_background">
				<input class="liveconfig prop_background none" type="text" data-css="background">
				<label for="background_color">Color</label>
				<input class="prop_background-color colorpicker2 ruleBack" data-css="background-color" type="text"><span class="colorpicker3"></span>

				<label for="background_image">Image</label>
				<div style="position: relative;overflow: hidden;">
					<span class="ui-icon ui-icon-folder-open explorer" rel="background_image" style="position: absolute;top:4px;left:5px;"></span>
					<input class="prop_background-image ruleBack" id="background_image" style="width: 100%;padding-left: 21px;" type="text" data-css="background-image">
				</div>

				<div id="backTest" style="cursor:move;height:100px;padding:10px;border: 2px solid #DDD;background: url(<?php echo BASE_PATH; ?>admin/img/transparent.png)"></div>

				<label for="background_size">Size</label>
				<input type="text" class="liveconfig prop_background-size autocomplete ruleBack" data-options='["cover","contain"]' data-css="background-size" />

				<label for="background_pos">Position X. Y.</label>
				<input type="text" class="prop_background-position spinner autocomplete ruleBack" data-css="background-position" data-options='["left top","left center","left bottom","left center","right top","right center","right bottom","center top","center center","center bottom"]'>

				<label for="background_attachment">Attachment</label>
				<select class="prop_background-attachment ruleBack" data-css="background-attachment"><option></option><option value="fixed">fixed</option><option value="scroll">scroll</option></select>

				<label for="background_repeat">Repeat</label>
				<select class="prop_background-repeat ruleBack" data-css="background-repeat"><option></option><option value="no-repeat">no-repeat</option><option value="repeat">repeat</option><option value="repeat-x">repeat-x</option><option value="repeat-y">repeat-y</option></select>

				<label for="background_clip">Clip</label>
				<select class="prop_background-clip ruleBack" data-css="background-clip"><option></option><option value="padding-box">padding-box</option><option value="border-box">border-box</option><option value="content-box">content-box</option></select>

				<label for="background_origin">Origin</label>
				<select class="prop_background-origin ruleBack" data-css="background-clip"><option></option><option value="padding-box">padding-box</option><option value="border-box">border-box</option><option value="content-box">content-box</option></select>

			</div>
			<div class="panelcss_tab hiddenTab" id="panelcss_tab_type">
				<label for="text_font">Family</label>
				<input type="text" style="width: 168px;" class="liveconfig prop_font-family autocomplete" id="text_font" data-options='["Arial, Helvetica, sans-serif","Times New Roman, Times, serif","Courier New, Courier, mono","Times New Roman, Times, serif","Georgia, Times New Roman, Times, serif","Verdana, Arial, Helvetica, sans-serif","Geneva, Arial, Helvetica, sans-serif"]' data-css="font-family" />
				<div class="leftpart" style="display:inline-block;vertical-align:top;width: 95px;">
					<label for="text_size">Size</label>
					<input class="liveconfig prop_font-size spinner" type="text" placeholder="normal" data-css="font-size" data-sufix="px">

					<label for="text_lineheight" title="Line-Height">L. Height</label>
					<input class="liveconfig prop_line-height spinner" placeholder="normal" type="text" data-css="line-height" data-sufix="px">

					<label for="text_lineheight" title="Letter-Space">Spacing</label>
					<input class="liveconfig prop_letter-spacing spinner" placeholder="normal" type="text" data-css="letter-spacing" data-sufix="px">

					<label for="text_lineheight" title="Text-Indent">Indent</label>
					<input class="liveconfig prop_text-indent spinner" type="text" placeholder="0" data-css="text-indent" data-sufix="px">
				</div>

				<div class="rightpart" style="display: inline-block;border-left: solid #D3D3D3 1px;width: 118px;padding-left: 7px;">
					<label for="text_color">Color</label>
					<input class="liveconfig prop_color colorpicker2" id="text_color" data-css="color" type="text"><span class="colorpicker3"></span>

					<label for="text_case">Case</label>
					<select class="liveconfig prop_text-transform" id="text_case" data-css="text-transform"><option></option><option value="capitalize">capitalize</option><option value="uppercase">uppercase</option><option value="lowercase">lowercase</option></select>

					<label for="word-wrap" title="Word Wrap">W. wrap</label>
					<select class="liveconfig prop_word-wrap" id="word-wrap" data-css="word-wrap"><option></option><option value="normal">normal</option><option value="break-word">break-word</option></select>

					<label for="text_case">Overflow</label>
					<input type="text" class="liveconfig prop_text-overflow autocomplete" placeholder="clip" id="text_overflow" data-options='["ellipsis","clip","ellipsis-word"]' data-css="text-overflow">

				</div>
				<div class="decoration" style="padding: 7px 0;padding-top:15px">
					<label style="width:60px">Decoration</label>
					<div data-val="underline" class="optionDeco" style="text-decoration:underline">U</div>
					<div data-val="overline" class="optionDeco" style="text-decoration:overline">O</div>
					<div data-val="line-through" class="optionDeco" style="text-decoration:line-through">S</div>
					<div data-val="none" class="optionDeco" style="text-decoration:none">N</div>
					<input class="liveconfig prop_text-decoration none" data-css="text-decoration" id="css-decoration" type="text">
				</div>
				<div class="fontstyle" style="padding: 7px 0;">
					<label style="width:60px">Style</label>
					<div data-val="bold" class="optionFontStyle" style="font-weight:bold">Bold
						<input class="liveconfig prop_font-weight none" data-css="font-weight" type="text">
					</div>
					<div data-val="italic" class="optionFontStyle" style="font-style:italic">Italic
						<input class="liveconfig prop_font-style none" data-css="font-style" type="text">
					</div>
				</div>
				<div class="alignement" style="padding: 7px 0;">
					<label style="width:60px">Text Align</label>
					<div data-val="left" class="optionAlign" style="background: url(<?php echo BASE_PATH;?>lib/HTML5editor/sprites777.png) no-repeat -126px -29px;"></div>
					<div data-val="center" class="optionAlign" style="background: url(<?php echo BASE_PATH;?>lib/HTML5editor/sprites777.png) no-repeat -157px -29px;"></div>
					<div data-val="right" class="optionAlign" style="background: url(<?php echo BASE_PATH;?>lib/HTML5editor/sprites777.png) no-repeat -189px -29px;"></div>
					<div data-val="justify" class="optionAlign" style="background: url(<?php echo BASE_PATH;?>lib/HTML5editor/sprites777.png) no-repeat -221px -29px;"></div>
					<input class="liveconfig prop_text-align none" data-css="text-align" id="text_align" type="text">
				</div>
				<div class="shadowWidget text-shadow">
					<div style="margin-top:10px;">Text Shadow :</div>
					<div style="width: 47%;display: inline-block;vertical-align: top;">             
						<div class="panelPointer" style="float:left;margin-right: 7px;margin-top: 6px;">
							<div class="cross1"></div>
							<div class="cross2"></div>
							<div class="pointer"></div>
						</div>
						<label style="min-width:15px">X</label>
						<input type="text" class="spinner rulePart h-offset" data-sufix="px">
						<label style="min-width:15px">Y</label>
						<input type="text" class="spinner rulePart v-offset" data-sufix="px">
					</div>
					<div style="width: 53%;display: inline-block;border-left: solid rgb(219, 219, 219) 1px;vertical-align: top;padding-left: 5px;">        
						<label>Blur</label>
						<input type="text" class="spinner rulePart blurShadow" data-sufix="px">
						<label>Color</label> 
						<input type="text" class="colorpicker2 rulePart colorShadow"><span class="colorpicker3"></span>
					</div>
					<input class="liveconfig prop_text-shadow resultShadow none" data-css="text-shadow" id="text-shadow" type="text">
				</div>
			</div>

			<div class="panelcss_tab hiddenTab" id="panelcss_tab_lists">
				<label for="list-style-image">Image</label>
				<input class="liveconfig prop_list-style-image spinner" type="text" data-css="list-style-image"><br>
				<label for="position">Position</label>
				<select class="select prop_list-style-position liveconfig" id="list-style-position" data-css="list-style-position">
					<option value=""></option><option value="inside">inside</option><option value="outside">outside</option>
				</select><br>
				<label for="list-style-type">Type</label>
				<input type="text" class="liveconfig prop_list-style-type autocomplete" id="list-style-type" data-options='["none","armenian","circle","cjk-ideographic","decimal","decimal-leading-zero","disc","georgian","hebrew","hiragana","hiragana-iroha","katakana","katakana-iroha","lower-alpha","lower-greek","lower-latin","lower-roman","square","upper-alpha","upper-latin","upper-roman"]' data-css="list-style-type">
			</div>
		</div>
		<div id="changecsscode" class="clearboth swicthcsscode"></div>
		<div id="parseCSS" class="none"></div>
	</div>
</form>
<script type="text/javascript">
	$(document).ready(function(){

		/* Disable form submit */
		var code = function () {return false;};
		window.document.getElementById("form_css").addEventListener("submit", code, false);

		/* Init Color Picker */
		window.currentColorPicker = $(".colorpicker2");
		window.picker = new Color.Picker({
			callback: function(hex) {
				currentColorPicker.val("#" + hex).trigger("change");
				currentColorPicker.next('span').css('background', "#" + hex);
			}
		});
	});


	function trigger(el, event){
		ev = document.createEvent('Event');
		ev.initEvent(event, true, false);
		el.dispatchEvent(ev);
	}
	function rgbToHex(color) {
		if (color.substring(0, 1) === '#') {
			return color;
		}
		var part = /(.*?)rgb\((\d+), (\d+), (\d+)\)/.exec(color);
		return "#" + ((1 << 24) + (parseInt(part[2]) << 16) + (parseInt(part[3]) << 8) + parseInt(part[4])).toString(16).slice(1);
	};

</script>