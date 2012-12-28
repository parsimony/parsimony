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
 * @copyright  Julien Gras et Benoit Lorillot
 * @version  Release: 1.0
 * @category  Parsimony
 * @package admin
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>
<style>
    #themes{width: 230px;color:#444;height: 100%;}
    .placeholder {position: relative;clear: both;width: 200px;margin:10px;}
    .themelist h4{line-height: 25px;text-align: left;text-shadow: 0 1px 0 white;margin: 0;}
    
    #themeFormAdd{color:#444;border-right: 1px solid #CCC;}
    #themeFormAdd h4{margin: 0px 5px;line-height: 20px;text-transform: capitalize}
    #duplicatepattern{display:none}
    #themes span.ui-icon { background-image: url(admin/img/icons.png);}
    .contimg{position:relative;width:97px;height:97px;display: inline-block;}
    .contimg:hover .preview{display:block}
    .preview{position:absolute;width:100%;height:100%;background:rgba(0,0,0,.75);display:none;font-size:21px;padding-top:40%;cursor:pointer;font-family:sans-serif;color: rgb(240, 240, 240);text-align: center;}
    .themeOptions{position: absolute;padding-left:10px;top: 0;width: 120px;height: 125px;left: 110px;z-index: 999;padding-top: 30px;opacity: 0.4;}
    .themeItem:hover .themeOptions{opacity: 1;}
     #patternName{float: left;line-height: 27px;}
    .themeItem{position: relative;text-align:left;padding:5px 0 ;border-top: 1px solid white;border-bottom: 1px solid #D3D5DB;padding-left:7px;}
    .themeItem:first-child{border-top: 0;}
    .themeItem.active,.themeItem:hover{background: #e5e5e5;}
    .tabPanel{display:none}
    .themes .content{height: 100%}
    #admin_themes{height: 100%;}
    #admin_themes .subTabsContainer{z-index: 99;position: relative;}
    .themesTabs{height: 100%;position: absolute;width: 100%;top: 0;padding-top: 100px;}
</style>
<script type="text/javascript">
    $("#admin_themes").on("click",".duplicate",function(){
	$('#duplicatepattern').show();
	$('#admin_themes .secondpanel a').trigger('click');
	$('#patternName').text($(this).data("themename").split(";")[1]);
	$('#themeFormAdd input[name="template"]').val($(this).data("themename"));
	$('#patternIMG').attr("src",($(this).data("imgurl")));
	$('input[value="template"]').attr('checked', true)
    });
</script>
<div id="admin_themes">
    <div class="subTabsContainer">
        <div class="ssTab ellipsis switchtodata active" rel="themelist"><?php echo t('Existing', FALSE); ?></div>
        <div class="ssTab ellipsis switchtodata" rel="themenew"><?php echo t('New', FALSE); ?></div>
    </div>
    <div class="themesTabs">
        <div class="themelist tabPanel" style="display:block;height: 100%;">
            <ul style="height: 100%;overflow-y: auto;overflow-x: hidden;">
		<?php
                $modules = \app::$activeModules;
                unset($modules['admin']);
                foreach ($modules as $moduleName => $mode) {
		    $module = \app::getModule($moduleName);
		    foreach ($module->getThemes() as $themeName) {
			$imgURL = stream_resolve_include_path($moduleName . '/themes/' . s($themeName) . '/thumb.png');
			if($imgURL)  $imgURL = BASE_PATH.  strstr(str_replace('\\','/',$imgURL),'modules/');
			else $imgURL = BASE_PATH.'admin/img/defaulttheme.png';
			?>
			<li id="theme_<?php echo s($themeName); ?>" class="themeItem<?php if($themeName == THEME) echo ' active'; ?>">
			    <h4 class="ellipsis"><?php echo ucfirst(s($themeName)); ?></h4>
			    <div class="contimg" style="background:url(<?php echo $imgURL; ?>) center" class="floatleft">
				<div class="preview ellipsis" onclick="$('#themelist li.active').removeClass('active');$(this).closest('li').addClass('active');top.ParsimonyAdmin.setCookie('THEMEMODULE','<?php echo $moduleName; ?>',999);top.ParsimonyAdmin.setCookie('THEME','<?php echo s($themeName); ?>',999);document.getElementById('parsiframe').contentWindow.location.reload();" /><?php echo t('Preview', FALSE) ?></div>
			    </div>
			    <div class="themeOptions">
				<input class="button duplicate" data-themename="<?php echo s($moduleName.';'.$themeName); ?>" data-imgurl="<?php echo $imgURL; ?>" type="button" value="<?php echo t('Duplicate', FALSE) ?>" />
				<?php if($themeName != app::$config['THEME']): ?>
                                <form method="POST" style="" action="<?php echo BASE_PATH; ?>admin/changeTheme" target="ajaxhack">
				    <input type="hidden" name="THEMEMODULE" value="<?php echo $moduleName; ?>" />
				    <input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />
				    <input type="hidden" name="name" value="<?php echo s($themeName); ?>" />
				    <input class="input" type="submit" value="<?php echo t('Choose', FALSE) ?>" />
				</form>
				<form method="POST" style="" action="admin/deleteTheme" target="ajaxhack">
				    <input type="hidden" name="THEMEMODULE" value="<?php echo $moduleName; ?>" />
				    <input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />
				    <input type="hidden" name="name" value="<?php echo s($themeName); ?>" />
				    <input class="input" type="submit" value="<?php echo t('Delete', FALSE) ?>" />
				</form>
                                <?php endif; ?>
			    </div>
			</li>
			<?php
		    }
		}
		?>
            </ul>	
        </div> 
        <div class="themenew tabPanel">
            <form method="POST" id="themeFormAdd" target="ajaxhack" action="<?php echo BASE_PATH; ?>admin/addTheme">
		<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>"/>
		<div class="placeholder">
		    <label for="name"><?php echo t('Theme Name', FALSE); ?></label>
		    <input type="text" style="width: 190px;" name="name" required="required"/>                       
		</div>
		<div class="placeholder">
		    <label><?php echo t('Module', FALSE); ?>: </label>
		    <select name="thememodule">
			<?php
			$modules = \app::$activeModules;
			unset($modules['admin']);
			foreach ($modules as $moduleName => $mode) {
			    echo '<option value="' . $moduleName . '">' . $moduleName . '</option>';
			}
			?>
		    </select>
		</div>
		<div class="placeholder" style="width: 190px;">
		    <label><?php echo t('Pattern', FALSE) . ' : ' ?></label>
		    <div><input type="radio" name="patterntype" value="blank" checked="checked" /> <?php echo t('Blank', FALSE) ?></div>
		    <?php /*<div><input type="radio" name="patterntype" value="url" />  <?php echo t('An URL', FALSE) ?> : <input type="text" name="url" style="width:150px;" ></div>*/ ?>
		    <div id="duplicatepattern">
			<div><input type="radio" name="patterntype" value="template" style="float:left;margin:0" /><h4 id="patternName"></h4></div>
			<img id="patternIMG" src="" />
			<input type="hidden" name="template" value=""  />
		    </div>
		</div>
		<input type="submit" style="width: 90%;margin:15px;" value="<?php echo t('Create Theme', FALSE); ?>"/>
            </form>
        </div>
    </div>
</div>
