<div id="toolbar">
    <div class="rightpart floatright">
	<?php if (ID_ROLE == 1): ?>
    	<a class="floatleft" href="#"><span class="ui-icon ui-icon-clipboard floatleft tooltip" data-tooltip="#infodev" data-pos="s"></span></a>
	<?php endif; ?>
	<a href="#" class="floatleft action tooltip" rel="getViewAdminLanguage" data-tooltip="<?php echo t('Current Language', FALSE); ?>" data-pos="s">
	    <span class="ui-icon ui-icon-flag floatleft"></span> <?php echo \request::$locales[\app::$request->getLocale()] ?>
	</a>
	<a href="#" class="floatleft action tooltip" rel="getViewUserProfile" data-tooltip="<?php echo t('My Profile',false); ?>" data-pos="s">
	    <span class="ui-icon ui-icon-locked floatleft"></span><?php echo ucfirst(htmlentities($_SESSION['login'], ENT_QUOTES, "UTF-8")); ?>        
	</a>
	<a  href="<?php echo BASE_PATH; ?>logout" class="floatleft tooltip" data-tooltip="<?php echo t('Logout',false); ?>" data-pos="se">
	    <span class="ui-icon ui-icon-circle-close floatleft"></span>
	</a>
    </div>
    <ul>
	<li style="border:0;width:208px"><a href="http://parsimony.mobi" target="_blank" style="padding:0;display: block;height:28px;">
		<img src="<?php echo BASE_PATH; ?>admin/img/parsimony.png">
	    </a>
	</li>
	<?php if (\app::$config['domain']['multisite']): ?>
	<li style="border-left:0;" class="subMenu" >
    	    <a href="#" style="background: none" title="<?php echo t('My domains', FALSE); ?>"><img src="<?php echo BASE_PATH . 'admin/img/multi.png'; ?>" style="position: relative;top: 5px;left: 5px;" /> </a>
    	    <ul>
		<?php foreach(glob('profiles/*', GLOB_ONLYDIR) AS $domainPath):
		    $basen = basename($domainPath);
		?>
    		<li>
                    <a href="http://<?php if($basen != 'www') echo $basen.'.'.DOMAIN.'/connect'; else echo DOMAIN.'/connect'; ?>"><?php echo ucfirst($basen); ?></a>
    		</li>
		<?php endforeach; ?>
    	    </ul>
    	</li>
	<?php endif; ?>
	<?php if (ID_ROLE == 1): ?>
    	<li style="border-left:0;"><a href="#"  class="action" rel="getViewModuleAdmin" params="module=admin" title="<?php echo t('Settings', FALSE); ?>"><?php echo t('Settings', FALSE); ?></a></li>
    	<li class="subMenu" >
    	    <a href="#" style="background: none" title="<?php echo t('Accounts', FALSE); ?>"><?php echo t('Accounts', FALSE); ?></a>
    	    <ul>
    		<li>
    		    <a href="#" class="action" rel="getViewAdminRights" title="Gestion des Droits"><?php echo t('Permissions', FALSE); ?></a>
    		</li>
    		<li>
    		    <a href="#" class="modeleajout ellipsis" rel="core - role" title=""><?php echo t('Manage Rights', FALSE); ?></a>
    		</li>
    		<li>
    		    <a href="#" class="modeleajout ellipsis" rel="core - user" title=""><?php echo t('Manage Roles', FALSE); ?></a>
    		</li>
    	    </ul>
    	</li>              
    	<li><a href="#" onclick="$('#themes').slideToggle();return false;" title="<?php echo t('Manage Themes', FALSE); ?>"><?php echo t('Themes', FALSE); ?></a>
    	</li>   
    	<li><a href="#" onclick="$(this).next('form').trigger('submit');return false;" title="<?php echo t('Db Modeling', FALSE); ?>"><?php echo t('DB', FALSE); ?></a>        
    	    <form method="POST" class="none" action="<?php echo BASE_PATH; ?>admin/dbDesigner" target="_blank"></form>
    	</li>

	<?php endif; ?>
	<li class="subMenu"><a href="#" id="info_themetype" title="<?php echo t('Version', FALSE); ?>" style="text-transform: capitalize"><?php echo t('Version', FALSE); ?> <?php echo str_replace('theme', '', THEMETYPE); ?></a>
	    <ul>
		<?php foreach (\app::$devices AS $device): ?>
    		<li>
    		    <a href="#" onclick="ParsimonyAdmin.changeDevice('<?php echo $device['name']; ?>'); return false;">
			    <?php echo ucfirst($device['name']); ?>
    		    </a>
    		</li>
		<?php endforeach; ?>
	    </ul>
	</li>
	<li class="subMenu"><a href="#" id="info_themetype" style="text-transform: capitalize"><img src="<?php echo BASE_PATH . 'admin/img/resolution.png'; ?>" style="position: relative;top: 4px;left: -5px;"/><span id="currentRes"></span></a>
	    <ul id="listres"></ul>
	</li>
    </ul>

    <div class="toolbarbonus floatleft"> 
	<input id="changeres" type="hidden">
	<script>
	    var resultions = new Array();
<?php
foreach (\app::$devices AS $device) {
    echo 'resultions["' . $device['name'] . '"] = \'' . json_encode($device['resolution']) . '\';' . PHP_EOL;
}
?>
	</script>
	<img src="<?php echo BASE_PATH . 'admin/img/portrait.png'; ?>" onclick="$('#changeorientation').val('portrait').trigger('change');$('#toolbar .toolbarbonus img').removeClass('active');$(this).addClass('active');" />
	<img src="<?php echo BASE_PATH . 'admin/img/landscape.png'; ?>" onclick="$('#changeorientation').val('landscape').trigger('change');$('#toolbar .toolbarbonus img').removeClass('active');$(this).addClass('active');" />
	<select id="changeorientation" class="none">
	    <option value="portrait"><?php echo t('Portrait', FALSE); ?></option><option value="landscape"<?php if(isset($_COOKIE['landscape']) &&  $_COOKIE['landscape'] == 'landscape') echo 'selected="selected"'; ?>><?php echo t('Landscape', FALSE); ?></option>
	</select>
	<?php if (ID_ROLE == 1): ?>
            <a href="" id="switchPreviewMode" <?php if(isset($_COOKIE['mode']) && $_COOKIE['mode'] == 'preview') echo 'class="selected"'; ?>onclick="ParsimonyAdmin.setPreviewMode();return false;">Preview</a>
            <a href="" id="switchCreationMode" <?php if(!isset($_COOKIE['mode']) || (isset($_COOKIE['mode']) &&  $_COOKIE['mode'] == 'creation')) echo 'class="selected"'; ?> onclick="ParsimonyAdmin.setCreationMode();return false;">Creation</a>
	<?php endif; ?>
    </div>      
</div>
<div id="infodev">
    <div>Time : <span id="infodev_timer"></span></div>
    <div>Current Theme : <span id="infodev_theme"></span></div>
    <div>Current Module : <span id="infodev_module"></span></div>
    <div>Current Page : <span id="infodev_page"></span></div>
    <div>PHP version : <span><?php echo phpversion(); ?></span></div>
</div>