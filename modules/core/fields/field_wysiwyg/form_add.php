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
 * @package core/fields
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>
<div class="placeholder">
    <label for="<?php echo $this->name ?>">
	<?php echo $this->label ?>
	<?php if (!empty($this->text_help)): ?>
    	<span class="tooltip ui-icon ui-icon-info" data-tooltip="<?php echo $this->text_help ?>"></span>
	<?php endif; ?>
    </label>
    <div style="padding-top: 24px;">
	<textarea cols="50" rows="8" class="<?php echo $this->name ?>" name="<?php echo $this->name ?>" id="<?php echo $this->name ?>" <?php if (!empty($this->regex)) echo 'pattern="' . $this->regex . '"' ?> ><?php echo $this->default ?></textarea>
    </div>
</div>
 <script>window.tinyMCE || document.write('<script type="text/javascript" src="<?php echo BASE_PATH; ?>lib/tinymce/tiny_mce.js"><\/script>')</script>
<script>
    tinyMCE.init({
	// Location of TinyMCE script
        mode : "textareas",
	script_url : BASE_PATH + 'lib/tinymce/tiny_mce.js',
        editor_selector : "<?php echo $this->name; ?>",

	// General options
	theme : "advanced",
	plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
	skin : "o2k7",
	skin_variant : "silver",
	// Theme options
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,image,cleanup,help,code,|forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,charmap,emotions,iespell,media,|,ltr,rtl,|",
	theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,|,visualchars",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : false,

	// Example content CSS (should be your site CSS)
	content_css : "<?php echo BASE_PATH . THEMEMODULE ?>/themes/<?php echo THEME ?>/<?php echo THEMETYPE ?>.css",

	// Drop lists for link/image/media/template dialogs
	template_external_list_url : "lists/template_list.js",
	external_link_list_url : "lists/link_list.js",
	external_image_list_url : "lists/image_list.js",
	media_external_list_url : "lists/media_list.js",
	width:"100%",
	height:"570px",
	file_browser_callback : "tinyBrowser"
    });</script>