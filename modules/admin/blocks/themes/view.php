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
 * @copyright  Julien Gras et Benoit Lorillot
 * @version  Release: 1.0
 * @category  Parsimony
 * @package admin
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>
<style>
    .themeOptions form, .themeOptions .button{margin: 6px 0}
    #themes .placeholder {position: relative;clear: both;width: 200px;margin:10px;}
    #themes h4{line-height: 25px;margin: 0;font-weight: normal;}
    .themeOptions input {width: 100%;}
    #themeFormAdd{color:#444;border-right: 1px solid #CCC;}
    #themeFormAdd h4{padding: 0px 5px;line-height: 20px;text-transform: capitalize}
    #duplicatepattern{display:none}
    .contimg{position:relative;width:100%;height:150px;background-repeat: no-repeat;background-position: center}
    .contimg:hover .themeOptions{right:0}
    .themeOptions{position: absolute;transition: right 0.2s;width:50%;background:rgba(0,0,0,.6);padding-left:10px;top: 0;height: 100%;right: -50%;z-index: 999;padding-right: 15px;}
     #patternName{float: left;line-height: 27px;}
    .themeItem{position: relative;text-align:left;border-top: 1px solid white;border-bottom: 1px solid #D3D5DB;padding-left:7px;}
    .themeItem:first-child{border-top: 0;}
    .themeItem.active{background: #e5e5e5;}
	.add-theme:hover {border-bottom: 2px solid rgb(45, 193, 238);}
	.add-theme{text-decoration: none;line-height: 28px;clear: both;font-size: 13px;
	margin-left: 13px;padding-bottom: 3px;color: #777;text-transform: uppercase;font-weight: bold;}
	#themes #themelist{display:block;}
	#themes #themenew{display:none;}
	#themes.add #themelist{display:none;}
	#themes.add #themenew{display:block;}
	
	
	.creationMode #toolbar:before{background: transparent}
	.creationMode #toolbar:after {background: transparent}
	.rightSidebarMenu2{display:flex !important}
	.rightSidebarMenu2 li{flex:1}
	.rightSidebarMenu2 li.active{flex: 3;}
	.rightSidebarMenu2 li.active::after {
content: attr(data-title);position: absolute;left: 0;right: 0;
text-align: center;color: #FFF;padding-left: 25px;font-size: 15px;}
</style>
<ul class="creation rightSidebarMenu2" data-sidebar="right" style="width:230px;background: #2DC1EE;overflow: hidden;">
	<li class="icons sprite paneltree floatright" data-title="Themes" data-panel="paneltree"></li>
	<li class="icons sprite panelcss floatright" data-title="Themes"  data-panel="panelcss"></li>
	<li class="icons sprite themes floatright active" data-title="Themes"  data-panel="themes"></li>
</ul>


<div id="themelist">
	<ul>
<?php
$modules = \app::$config['modules']['active'];
unset($modules['admin']);
foreach ($modules as $moduleName => $mode) {
	$module = \app::getModule($moduleName);
	foreach ($module->getThemes() as $themeName) {
		$imgURL = stream_resolve_include_path($moduleName . '/themes/' . s($themeName) . '/thumb.png');
		if ($imgURL !== FALSE)
			$imgURL = BASE_PATH . $moduleName . '/themes/' . s($themeName) . '/thumb.png';
		else
			$imgURL = BASE_PATH . 'admin/img/defaulttheme.png';
?>
	<li id="theme_<?php echo s($themeName); ?>" class="themeItem">
		<h4 class="ellipsis"><?php echo ucfirst(s($themeName)); ?></h4>
		<div class="contimg" style="background-image:url(<?php echo $imgURL; ?>)" class="floatleft">
			<div class="themeOptions">
				<input class="button preview"  onclick="$('#themelist li.active').removeClass('active');$(this).closest('li').addClass('active');top.ParsimonyAdmin.setCookie('THEMEMODULE','<?php echo $moduleName; ?>',999);top.ParsimonyAdmin.setCookie('THEME','<?php echo s($themeName); ?>',999);document.getElementById('preview').contentWindow.location.reload();" type="button" value="<?php echo t('Preview') ?>" />
				<?php if($themeName !== app::$config['THEME']): ?>
					<form method="POST" action="<?php echo BASE_PATH; ?>admin/changeTheme" target="formResult">
						<input type="hidden" name="THEMEMODULE" value="<?php echo $moduleName; ?>" />
						<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />
						<input type="hidden" name="name" value="<?php echo s($themeName); ?>" />
						<input class="input" type="submit" value="<?php echo t('Choose') ?>" />
					</form>
				<?php endif; ?>
				<input class="button duplicate" data-themename="<?php echo s($moduleName.';'.$themeName); ?>" data-imgurl="<?php echo $imgURL; ?>" type="button" value="<?php echo t('Duplicate') ?>" />
				<?php if($themeName !== app::$config['THEME']): ?>
					<form method="POST" action="<?php echo BASE_PATH; ?>admin/deleteTheme" target="formResult">
						<input type="hidden" name="THEMEMODULE" value="<?php echo $moduleName; ?>" />
						<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />
						<input type="hidden" name="name" value="<?php echo s($themeName); ?>" />
						<input class="input" type="submit" value="<?php echo t('Delete') ?>" />
					</form>
				<?php endif; ?>
			</div>
		</div>
	</li>
	<?php
	}
}
?>
	<a href="#" class="ellipsis add-theme" onclick="document.getElementById('themes').classList.toggle('add')"> + <?php echo t('New'); ?> Theme</a>
	</ul>	
</div> 
<div id="themenew">
	<form method="POST" id="themeFormAdd" target="formResult" action="<?php echo BASE_PATH; ?>admin/addTheme">
		<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>"/>
		<div class="placeholder">
			<label for="name"><?php echo t('Theme Name'); ?></label>
			<input type="text" style="width: 190px;" name="name" required="required"/>                       
		</div>
		<div class="placeholder">
			<label><?php echo t('Module'); ?></label>
			<select name="thememodule">
				<?php
				$modules = \app::$config['modules']['active'];
				unset($modules['admin']);
					unset($modules['core']);
				foreach ($modules as $moduleName => $mode) {
					echo '<option value="' . $moduleName . '">' . $moduleName . '</option>';
				}
				?>
			</select>
		</div>
		<div class="placeholder" style="width: 190px;">
			<label style="display: inline-block;position: relative;"><?php echo t('Pattern') ;?></label>
			<div style="margin: 8px 0;"><input type="radio" name="patterntype" value="blank" checked="checked" /> <?php echo t('Blank') ?></div>
			<?php /*<div><input type="radio" name="patterntype" value="url" />  <?php echo t('An URL') ?> : <input type="text" name="url" style="width:150px;" ></div>*/ ?>
			<div id="duplicatepattern">
				<div><input type="radio" name="patterntype" value="template" style="float:left;margin:0" /><h4 id="patternName"></h4></div>
				<img id="patternIMG" src="" />
				<input type="hidden" name="template" value=""  />
			</div>
		</div>
		<input type="submit" style="width: 90%;margin:15px;" value="<?php echo t('Create Theme'); ?>" />
	</form>
	<a href="#" class="ellipsis add-theme" onclick="document.getElementById('themes').classList.toggle('add')"> < <?php echo t('Cancel'); ?></a>
</div>


<script type="text/javascript">
	function setActiveTheme(themeName){
		var oldActiveTheme = document.querySelector(".themeItem.active");
		if(oldActiveTheme){
			oldActiveTheme.classList.remove("active");
		}
		document.getElementById("theme_" + themeName).classList.add("active");
	}
	
    $("#themes").on("click",".duplicate",function(){
		$('#themes div[rel="themenew"]').trigger('click');
		$('#duplicatepattern').show();
		$('#patternName').text($(this).data("themename").split(";")[1]);
		$('#themeFormAdd input[name="template"]').val($(this).data("themename"));
		$('#patternIMG').attr("src",($(this).data("imgurl")));
		$('input[value="template"]').attr('checked', true);
    });
</script>
