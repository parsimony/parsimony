<?php
app::$response->addCSSFile('admin/css/main.css');
app::$response->addCSSFile('admin/css/ui.css');
app::$response->addCSSFile('lib/tooltip/parsimonyTooltip.css', 'footer');
app::$response->addJSFile('lib/tooltip/parsimonyTooltip.js');
app::$response->addJSFile('admin/script.js');
app::$response->addJSFile('lib/HTML5sortable/jquery.sortable.js', 'footer');
app::$response->addJSFile('admin/blocks/toolbar/block.js', 'footer');
?>
<script type="text/javascript">

	$(document).ready(function() {

<?php
    if(isset($_COOKIE['takeATour']) && $_COOKIE['takeATour'] == 'yes'):
	if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'fr_FR') 	app::$response->addJSFile('lib/takeATour/take-a-tour-fr.js');
    app::$response->addJSFile('lib/takeATour/take-a-tour.js', 'footer');
    app::$response->addCSSFile('lib/takeATour/style.css', 'footer');
?>

    // Configuration du Take A Tour
	
    window.takeATour = new TakeATour('tat-parsimony',
    [
        {
            content:
                "<div class='mark'>1/18</div><h2>"
				+ t('Welcome to Parsimony') 
				+"</h2><br>"
				+ "<div class='alignLeft'><br><p>"
				+ t('We will present some key features of Parsimony. If you are a project manager, developer, designer or webmaster, Parsimony offers incredible opportunities you\'ve always dreamed of!') 
				+"</p>"    
				+ "<br><p>"
				+ t('Are you ready to begin your new life as a developer?') 	
				+"<br>"
				+ "<br><br><p>"
				+  t('Start with a short tour of the interface!') 
				+"</p></div>"
				+ "<br><br><br><br><br><br><div><button onclick='takeATour.nextStep()'>"
				+  t('Go!') 
				+"</button>"
				+ "<br><br><small><button onclick='takeATour.stop()'>"
				+  t('No, thanks') 	
				+"</button></small>"
	
        },
        {
					target : document.getElementById('modesSwitcher'),
            content:
                "<div class='mark'>2/18</div><h2>"
				+  t('Three workspaces') 
				+"</h2>"
                + "<br><div class='alignLeft'><p>"
		+  t('You can access different types of workspace.') 
		+"</p><div>"
                + "<div style='margin: 20px 0;line-height: 25px;'><span class='modesSwitcher'>Preview </span>"
		+  t('This is the site as the user knows it.') 
		+"</div>"
                + "<div style='margin: 20px 0;line-height: 25px;'><span class='modesSwitcher'>Edit </span>"
		+  t('You can access the list of pages, dynamic content and file explorer.') 
		+"</div>"
                + "<div style='margin: 20px 0;line-height: 25px;'><span class='modesSwitcher'>Design </span>"
		+  t('This is your space Website Design: Data modeling, blocks, CSS, theme and tree structure of pages.') 
		+"</div></div>"
				+ "<div class='alignLeft'><p>"
		+  t('These 3 workspaces are defined by default during installation of Parsimony and are configurable by granting or restricting roles permissions.') 
		+"</p></div>"
				+ "<div class='alignLeft'><p>"
		+  t('The super administrator can define interface rights for each role.') 
		+"</p></div>"
          
        },
        {
            target: document.getElementById('left_sidebar'),
            content:
                "<div class='mark'>3/18</div><h2>"
		+  t('Edit bar') 
		+"</h2>"
                + "<br><div class='alignLeft'><p>"
		+  t('It corresponds to ') 
		+"<span class='modesSwitcher'>Edit</span>. "
		+  t('It is used to edit the content and manage modules.') 
		+"</p></div>"
				+ "<div class='alignLeft'><p>"
		+  t('This is where you access the configurations of your modules (default or newly created), the web pages and content management.') 
		+"</p></div>"
				+ "<div class='alignLeft'>"
		+  t('We\'ll see each of the following items:') 
		+"</div>"
				+ "<br><div class='alignLeft'><ul><strong>"
		+  t('Default modules') 
		+"</strong>"
                + "<li>"
		+  t('Pages Management') 
		+"</li>"
                + "<li>"
		+  t('Content Management') 
		+"</li></ul></div>"
				+"<br><div class='alignLeft'><ul><strong>"
		+  t('Administration') 
		+"</strong>"
                + "<ul><li>"
		+  t('Website configuration') 
		+"</li>"
                + "<li>"
		+  t('Permission Management') 
		+"</li>"
                + "<li>"
		+  t('Role Management') 
		+"</li>"
                + "<li>"
		+  t('User Management') 
		+"</li></ul></div>"
                
		
        },
		{
            target: document.querySelector('div[data-module="blog"]'), /*div[data-module="blog"]  ---  > .titleTab*/
            content:
                "<div class='mark'>4/18</div><h2>"
		+  t('The current module') 
		+"</h2>"
                + "<br><div class='alignLeft'>"
		+  t('This is your content and pages management space.') 
		+"<br><br>"
                + "<ul><strong>"
		+  t('Pages') 
		+"</strong>"
				+ "<li class='marg'>"
		+  t('You see the list of pages and their configurations.') 
		+"</li>"
				+ "<li class='marg'>"
		+  t('You can add new pages.') 
		+"<span class='subli'></li></ul>"
				+ "<div class='subli addp'><span class='ui-icon ui-icon-plus'></span>Add A Page</div>"
				+ "<br><br><br><ul><strong>"
		+  t('Database content') 
		+"</strong>"
				+ "<li class='marg'>"
		+  t('You can search, add, update and delete content.') 
		+"</li>"
				+ "<li class='marg'>"
		+  t('A modeling workspace is available to build or modify your data model.') 
		+"</li></ul>"
					 + "<div><span class='dbDesigner subli'>Database Designer</span></div></div>",
					 callback: function() {
                document.querySelector('#left_sidebar > div[data-module="blog"]').classList.add("active");
		}
        },
		{
            target: document.querySelector('div[data-module="core"]'),
            content:
                "<div class='mark'>5/18</div><h2>"
		+  t('Parsimony Administration') 
		+"</h2>"
				+ "<br><br><br><div class='alignLeft'><span class='taKeAdmin'>General</span>"
		+  t('In this tab, you configure your Parsimony. ') 
		+"</div>"
				+ "<br><br><div class='alignLeft'><span class='taKeAdmin'>Permissions</span>"
		+  t('You define the rights to the pages and data modules (CRUD).') 
		+"</div>"
				+ "<br><br><div class='alignLeft'><span class='taKeAdmin'>"
		+  t('Roles') 
		+ "</span>"
		+  t('You specify the rights to user groups.') 
		+"</div>"
				+ "<br><br><div class='alignLeft'><span class='taKeAdmin'>Users</span>"
		+  t('You determine the users rights.') 
		+"</div>"
				+ "<br><br><div class='alignLeft'><span id='add-module-take' style='margin-right : 10px;'>+ Add a Module</span>"
		+  t(' is used to create a new data model or a new theme.') 
		+"</div>",
				callback: function() {
			document.querySelector('#left_sidebar > div[data-module="blog"]').classList.remove("active");
                document.querySelector('div[data-module="core"]').classList.add("active");
        }
        },	
		{
            target: document.querySelector('#left_sidebar a[href="#left_sidebar/settings/admin"]'),
            content:
                "<div class='mark'>6/18</div><h2>General</h2>"
				+ "<div class='alignLeft'><p>"
		+  t('Parsimony Configuration: Site information, Cache, DB, Devices, Localization, Modules, Security, Development, Mailing, Sessions, Version.') 
		+"</p></div>"
				+ "<div class='alignLeft'><img style='max-width: 600px;' src='http://parsimony.mobi/images-take-a-tour/takeATour-imgs-administration.png'></div>",
            callback: function() {
             
        }    
        },	
		{
            target: document.querySelector('#left_sidebar a[href="#left_sidebar/permissions"]'),
            content:
                "<div class='mark'>7/18</div><h2>Permissions</h2>"
				+ "<div class='alignLeft'><p>"
		+  t('For each role, you can change the permissions on pages and data modules (CRUD).') 
		+"</p></div>"
		+ "<div class='alignLeft'><img style='width: 615px;' src='http://parsimony.mobi/images-take-a-tour/takeATour-imgs-permissions.png'></div>",
        },
		{
            target: document.querySelector('#left_sidebar a[href="#left_sidebar/model/core/role"]'),
            content:
                "<div class='mark'>8/18</div><h2>"
		+  t('Roles') 
		+"</h2>"
				+ "<div class='alignLeft'><p>"
		+  t('The Roles tab helps you set or change roles.') 
		+"</p></div>"
				+ "<div class='alignLeft'><p>"
		+  t('For each role, rights in the Parsimony interface can be defined.') 
		+"</p></div>"
				+"<br><div class='alignLeft'><ul><strong>"
		+  t('Examples') 
		+"</strong>"
                + "<ul><li>"
		+  t('A designer can only have rights to the CSS, the tree of pages and blocks management.') 
		+"</li>"
				+ "<li>"
		+  t('An editor may be able to manage the content or change the SEO pages.') 
		+"</li></ul>"
				+ "</div><br>"
				+ "<div class='alignLeft'><img src='http://parsimony.mobi/images-take-a-tour/takeATour-imgs-roles-editor-example.png'></div>",            
        },
		{
            target: document.querySelector('#left_sidebar a[href="#left_sidebar/model/core/user"]'),
            content:
                "<div class='mark'>9/18</div><h2>"
		+  t('Users') 
		+"</h2>"
				+ "<div class='alignLeft'><p>"
		+  t('Users tab lists your users and allows you to manage: edit, add, modify, delete.') 
		+"</p></div>"
		+ "<div class='alignLeft'><img src='http://parsimony.mobi/images-take-a-tour/takeATour-imgs-users.png'></div>"
        },
        {
            target: document.getElementById('right_sidebar'),
            content:
                "<div class='mark'>10/18</div><h2>"
		+  t('Design Bar') 
		+"</h2>"
                + "<div class='alignLeft'><p>"
		+"<span class='modesSwitcher'>Design</span>"
		+  t('This is your website design area, you design, structure and style web pages.') 
		+"</p></div>"
                + "<div class='alignLeft'>"
		+  t('You will access the following features:') 
		+"<br><ul><li class='marg'>"
		+  t('Editing CSS') 
		+"</li>"
                + "<li class='marg'>"
		+  t('Adding and dropping blocks') 
		+"</li>"
                + "<li class='marg'>"
		+  t('Organization and structure of the page (container and content)') 
		+"</li>"
                + "<li class='marg'>"
		+  t('Theme Management') 
		+".</li></ul></div>",
				callback: function() {
              	document.querySelector('.tabsContainer').style.border = '3px solid #ff0000';
				document.querySelector('.tabsContainer').classList.add("animColors");
				document.querySelector("a[href='#paneltree']").click();
        }
        },
        {
            target: document.getElementById('right_sidebar'),
            content:
                "<div class='mark'>11/18</div><br><br><br><br><div class='alignLeft'>"
		+  t('Parsimony offers impressive features to style your web pages.') 

		+"</div><br><h3 class='alignLeft'>"
		+  t('CSS inspector') 
		+"</h3>"
                + "<div class='alignLeft'><p>"
		+"<span style='border : 3px solid #ff0000;border-radius :20px;padding: 5px;'><a href='#' style='background-position: 0 -1275px;width: 16px;height: 16px;background-image: url(admin/img/defaultsprite.png);background-repeat: no-repeat;display: inline-block;top: 3px;position: relative;'></a></span>"
		+  t('CSS inspector: a visual search of a CSS selector as the element inspector in your browser.') 
		+"</p></div>"
				+"<h3 class='alignLeft'>"
		+  t('Editing CSS properties') 
		+"</h3>"
				+"<div class='alignLeft'><p>"
		+  t('Here you apply new rules to a CSS selector, you edit, add, delete CSS properties.') 
		+"<br>"
		+  t('Example') 
		+": body{font-size : 25px}"
		+"</p></div>"
				+ "<h3 class='alignLeft'>"
		+  t('Media queries management') 
		+"</h3>"
				+"<div class='alignLeft'>"
		+  t('You can easily add or edit media queries.') 
		+"<br><br>"
		+  t('Example to target iPads (portrait and landscape) in media query.') 
		+"<br><br>@media only screen and (min-device-width : 768px) and (max-device-width : 1024px) {}<br>"
		  + "<br><br>"
		+  t('For each selector, you can choose the appropriate media query.') 
		+"</p></div>",
 
            callback: function() {
		document.querySelector('.tabsContainer').classList.remove("animColors");
		document.querySelector('.tabsContainer').style.border = 'none';
				document.querySelector("a[href='#panelcss']").style.border = '3px solid #ff0000';
				document.querySelector("a[href='#panelcss']").classList.add("animColors");
                document.querySelector("a[href='#panelcss']").click();
				document.querySelector('#csspicker').style.border = '3px solid #ff0000';
				document.querySelector('#csspicker').style.top = '8px';
				document.querySelector('#csspicker').style.borderRadius = '20px';
				document.querySelector("#csspicker").classList.add("animColors");
				
				
				document.querySelector('.sprite-picker').style.border = 'none';
				document.querySelector('.sprite-picker').style.top = '11px';
				document.querySelector('.sprite-picker').style.borderRadius = '0px'; 
				document.querySelector("#selectorcontainer #current_selector_update").value = 'body';
				document.getElementById('goWithThisSelector').click();
				document.querySelector('div[rel="panelcss_tab_type"]').click();
				document.querySelector('input[data-css="font-size"]').value = '25px';
				/* MQ */
				document.querySelector("a[href='#panelcss']").style.border = 'none';
				document.getElementById('mediaselected').style.border = '3px solid rgb(45, 193, 238)';
				document.getElementById('mediaselected').classList.add("animColors");
				document.getElementById('mediaselected').click();
				document.querySelector("#selectmedias div:last-child").click();
				document.querySelector("#mdqMinWidthValue").value = '768';
				document.querySelector("#mdqMaxWidthValue").value = '1024';
			}
        }
		,
        {
            target: document.getElementById('right_sidebar'),
            content:
                "<div class='mark'>12/18</div><h2>"
				+  t('Page Tree') 
		+"</h2>"
		+ "<div class='alignLeft'>"
                + "<div>"
				+  t('The page tree is the organization and structure of your web page (container and content).') 
				+"<div class='marg' style='border: 3px solid #ff0000;padding: 5px;margin: 10px 5px;'>"
				+  t('Container: The theme corresponds to the structure of the web page which remains throughout the web application.') 
		+"</div>"
				+"<div class='marg' style='border: 3px solid rgb(45, 238, 99);padding: 5px;margin: 10px 5px;'>"
				+  t('Content: The content that changes depending on the url of pages.') 
		+"</div></div>"
		+ "</div>"
				+"<div class='alignLeft'><div style='position : relative;-ms-transform:scale(0.8);-webkit-transform:scale(0.8);transform:scale(0.8); top: -35px;left : 30px;'><div id='thpbrowser'><div id='thptop' style='border-bottom: 1px solid #ccc;'><span id='thpbuttons'><span class='button'></span><span class='button'></span><span class='button'></span></span><span id='thpbar'><span id='thpmess1' class='messAnim'> http://mysite.com/page1</span><span id='thpmess2' class='messAnim'> http://mysite.com/page2</span></span></div><div id='thppageAnim' style='height:89%;overflow: hidden;' class=''>	<div id='thpcontAnim'>	<div id='thpheaderAnim'></div>	<div id='thpmiddleAnim'><div id='thpcontentAnim'><div id='thpslidepageAnim1' class='slidepageAnim'><div id='thpcontentAnim1'></div><div id='thpcontentAnim2'></div><div id='thpcontentAnim3'></div></div><div id='thpslidepageAnim2' class='slidepageAnim'><div id='thpcontentAnim4'></div>	<div id='thpcontentAnim5'></div></div></div><div id='thpmenuAnim'> </div></div><div id='thpfooterAnim'></div></div></div></div><div id='thpshowtheme'><div onmouseover='document.getElementById('thppageAnim').classList.add('themeeffect');' onmouseout='document.getElementById('thppageAnim').classList.remove('themeeffect');' style='display: inline-block;'>Theme</div></div><div id='thpshowpage'><div onmouseover='document.getElementById('thppageAnim').classList.add('pageeffect');' onmouseout='document.getElementById('thppageAnim').classList.remove('pageeffect');'>Content</div></div></div></div>" ,
            callback: function() {
		document.querySelector("a[href='#panelcss']").classList.remove("animColors");
		document.querySelector("#csspicker").classList.remove("animColors");
		document.getElementById('mediaselected').classList.remove("animColors");
		document.getElementById('mediaselected').style.border = 'none';
		document.querySelector('#csspicker').style.border = 'none';
		document.querySelector('.sprite-picker').style.top = '0px';
		document.querySelector('#csspicker').style.top = '';
				document.querySelector('#csspicker').style.borderRadius = '0px';
				document.querySelector('.sprite-picker').style.borderRadius = '0px'; 
		document.getElementById('reinitcss').click();
		document.getElementById('rmvMDQ').click();
		document.getElementById('current_selector_update').value = '';
                document.querySelector("a[href='#paneltree']").click();
				
				
				document.getElementById('treedom_container').style.border = '3px solid #ff0000';
				document.getElementById('treedom_container').style.top = '8px';
				document.getElementById('treedom_container').classList.add("animColors");
				
				document.getElementById('treedom_content').style.border = '3px solid rgb(45, 238, 99)';
				document.getElementById('treedom_content').style.top = '8px';
				document.getElementById('treedom_content').classList.add("animColors");
				
            }
        },
        {
            target: document.getElementById('right_sidebar'),
            content:
                "<div class='mark'>13/18</div><h2>"
				+  t('Theme management') 
		+"</h2>"
		+ "<br><div class='alignLeft'><p>"
				+  t('You can manage your themes preview a theme, select, duplicate, or delete it.') 
			+ "</p></div><br><br>"
	+ "<div class='alignLeft'><p>"
				+  t('You can add new themes:') 
		+"<br><br><a href='#' style='border-bottom: 2px solid rgb(45, 193, 238);text-decoration: none;line-height: 28px;clear: both;font-size: 13px;margin-left: 13px;padding-bottom: 3px;color: #777;text-transform: uppercase;font-weight: bold;'> + New theme</a>",
callback: function() {
	document.getElementById('treedom_content').classList.remove("animColors");
	document.getElementById('treedom_container').classList.remove("animColors");
	document.getElementById('treedom_container').style.border = 'none';
				document.getElementById('treedom_content').style.border = 'none';
                document.querySelector("a[href='#themes']").click();
				document.querySelector('.contimg .themeOptions').style.right='0';
            }
        },
        {target : document.querySelector('#toolbar ul:nth-child(2) .subMenu:nth-child(2)'),
            content: "<div class='mark'>14/18</div><h2>"
				+  t('Change views') 
		+"</h2><div class='alignLeft'><p>"
				+  t('You can choose the view corresponding to a mobile, tablet, TV and desktop device.') 
		+"</p></div>"
			+ "<div class='alignLeft'><p>"
				+  t('You change the resolution of your screen and view your website in different screen configurations.') 
		+"</p></div>"
	+"<div class='alignLeft'><img src='http://parsimony.mobi/images-take-a-tour/takeATour-imgs-resolution.gif'></div>",
		callback: function() {
			document.querySelector('#parsimonyDND .ui-icon-closethick').click();
            }
        },

        {
	target : document.querySelector('#toolbar .sprite-bdd'),
            targetName: "Infor",
            content: "<div class='mark'>15/18</div><h2>"
				+  t('Access data modeling') 
		+"</h2>"
	+ "<div class='alignLeft'><p>"
				+  t('DB Designer is used to build  your data modeling: structure and data relationships.') 
		+"</p></div>"
	+"<br><div class='alignLeft'><iframe width='560' height='315' src='//www.youtube.com/embed/a7YYd3UZtws?autohide=1&showinfo=0&rel=0&controls=0' frameborder='0' allowfullscreen></iframe></div>",
        },
			{
	target : document.querySelector('#toolbar .sprite-dir'),
            targetName: "file-explorer",
            content: "<div class='mark'>16/18</div><h2>"
				+  t('Access the file explorer') 
		+"</h2>"
	+ "<div class='alignLeft'><p>"
				+  t('Here you can browse through your files: php, css, html, images (crop, resize etc).') 
		+"</p></div>"
	+"<br><div class='alignLeft'><iframe width='560' height='315' src='//www.youtube.com/embed/XLMfDzYINdo?autohide=1&showinfo=0&rel=0&controls=0' frameborder='0' allowfullscreen></iframe></div>",
        },	
			{
	target : document.querySelector('#toolbar > div ul.menu'),
            targetName: "info",
            content: "<br><br><br><div class='mark'>17/18</div><h2>"
			+  t('Information about your site configuration') 	
		+" </h2><br><div class='alignLeft'>"
				
		+  t('This is the state of your current configuration.') 
		+"<br><br>"
				
		+  t('You can access important information: server loading time, theme, module, page, and PHP version.') 
		+"</div>"
			+"<br><br><br><h2>"
	+  t('Configure the language and user profile') 
		+"</h2><div class='alignLeft'><br>"
				+  t('You can log out or choose a different language for the administration of Parsimony.') 
		+" </div>"
 
        },		
        {
            content:
                "<div class='mark'>18/18</div><h2>"
		+  t('End of presentation') 
		+"</h2><br><br><div class='alignLeft'><p style='font-weight: bold;'>"
				+  t('You can now create stunning web applications with all these features and discover very unique others.') 
		+"</p></div>"
		+"<br><br><div style='margin : 20px auto'><img src='<?php echo BASE_PATH ?>core/img/logo-parsimony-big.png'></div>",
            callback: function() {
                document.querySelector("a[href='#panelblocks']").click();
            }
        }
    ]);

    // Démarrage du Take a Tour
    takeATour.start();


<?php endif ?>


		ParsimonyAdmin.init();
		<?php
		/* Define active panels */
		if (isset($_COOKIE['rightToolbarPanel'])) {
			echo '$(\'a[href="#' . $_COOKIE['rightToolbarPanel'] . '"]\')[0].click();';
		}
		?>
	
	});
</script>

<?php
$admin = new \core\blocks\container("admin");

/* Menu */
$menutop = new \admin\blocks\menu("toolbar");
$admin->addBlock($menutop);

/* Sidebar Left:  Modules */
$leftSidebar = new \admin\blocks\modules("left_sidebar");
$leftSidebar->setConfig('cssClasses', 'sidebar');
$admin->addBlock($leftSidebar);

/* Sidebar Right */
$rightSidebar = new \core\blocks\tabs("right_sidebar");

/* CSS , perm 16 = design CSS */
if ($_SESSION['permissions'] & 16) {
	$block = new \admin\blocks\css("panelcss");
	$block->setConfig('headerTitle', 'CSS');
	$rightSidebar->addBlock($block);
	$admin->addBlock($rightSidebar);
}

/* Blocks , perm 128 = configure blocks */
if ($_SESSION['permissions'] & 128) {
	$block = new \admin\blocks\tree("paneltree");
	$block->setConfig('headerTitle', 'Tree');
	$rightSidebar->addBlock($block);
	
	/* Blocks , perm 256 = manage blocks */
	if ($_SESSION['permissions'] & 256) {
		$block = new \admin\blocks\blocks("panelblocks");
		$block->setConfig('headerTitle', 'Blocks');
		$rightSidebar->addBlock($block);
	}
}

/* Theme , perm 32 = choose a theme */
if ($_SESSION['permissions'] & 32) {
	$block = new \admin\blocks\themes("themes");
	$block->setConfig('headerTitle', 'Themes');
	$rightSidebar->addBlock($block);
}

$blocks = $rightSidebar->getBlocks();
if (!empty($blocks)) {
	$rightSidebar->setConfig('cssClasses', 'sidebar');
	$admin->addBlock($rightSidebar);
}

echo $admin->display();
?>
<div id="conf_box_overlay" class="none">
	<div id="conf_box_load">
		<div id="followingBalls_1" class="followingBalls"></div>
		<div id="followingBalls_2" class="followingBalls"></div>
		<div id="followingBalls_3" class="followingBalls"></div>
		<div id="followingBalls_4" class="followingBalls"></div>
	</div>
	<iframe name="conf_box_content_iframe" id="conf_box_content_iframe" src="" class="conf_box"></iframe>
	<div id="conf_box_content_inline" class="conf_box"></div>
</div>
<div id="dialog" style="display:none;width: 450px;">
	<span id="conf_box_close" onclick="top.ParsimonyAdmin.closeConfBox()" class="floatright ui-icon ui-icon-closethick"></span>
	<div id="conf_box_title"><?php echo t('Enter an ID for the new block') ?></div>
	<div id="dialog-input"><input type="text" id="dialog-id" /><input type="hidden" id="dialog-id-options" /></div>
	<div id="dialog-ac">
		<input type="button" id="dialog-ok" value="<?php echo t("Add", FALSE) ?>" class="highlight" />
		<input type="button" id="dialog-cancel" onclick="ParsimonyAdmin.closeConfBox();ParsimonyAdmin.returnToShelter();" value="<?php echo t("Cancel", FALSE) ?>" />
	</div>
</div>
<iframe name="formResult" id="formResult" src="" class="none"></iframe>
<div id="shelter">
	<div id="dropInPage" class="marqueurdragndrop">
		<div id="dropInPageChild"></div>
	</div>
	<div id="dropInTree" class="marqueurdragndrop"></div>
	<div id="notify"></div>
	<div id="menu">
		<span id="closemenu" onclick="ParsimonyAdmin.closeParsiadminMenu()" class="floatright ui-icon ui-icon-closethick"></span>
		<div class="options"></div>
	</div>
	<datalist id="parsidatalist"></datalist>
</div>

<?php
if (strstr($_SERVER['REQUEST_URI'], '?') != FALSE)
	$frameUrl = $_SERVER['REQUEST_URI'];
else
	$frameUrl = $_SERVER['REQUEST_URI'] . '?preview=ok';
$style = 'width: 100%; height: 100%;';
if (isset($_COOKIE['screenX']) && isset($_COOKIE['screenY']) && is_numeric($_COOKIE['screenX']) && is_numeric($_COOKIE['screenY'])) {
	echo '<script> document.body.classList.add("sizedPreview"); </script>';
	if (isset($_COOKIE['landscape']) && $_COOKIE['landscape'] == 'landscape') {
		$style = 'width: ' . $_COOKIE['screenY'] . 'px; height: ' . $_COOKIE['screenX'] . 'px;';
	} else {
		$style = 'width: ' . $_COOKIE['screenX'] . 'px; height: ' . $_COOKIE['screenY'] . 'px;';
	}
}
?>
<div id="previewContainer" style="<?php echo $style; ?>">
	<iframe id="preview" src="<?php echo $frameUrl; ?>"></iframe>
	<div id="blockOverlay"></div>
	<div id="parsimonyDND">
		<div class="parsimonyResizeInfo">
			<span class="parsimonyResizeClose" id="idName"></span>
			<div href="#" id="stylableElements" class="toolbarButton">
				<a href="#" style="display:block;width: 100%;height: 100%" class="cssblock" data-action="onDesign">
					<span class="spanDND sprite sprite-csspickerlittle"></span>
				</a>
				<div id="CSSProps" class="none"></div>
			</div>
			<?php if ($_SESSION['permissions'] & 128) : ?>
			<a href="#" class="toolbarButton configure_block" rel="getViewConfigBlock" data-action="onConfigure" title="Configuration">
				<span class="spanDND ui-icon-wrench"></span>
			</a>
				<?php if ($_SESSION['permissions'] & 256) : ?>
				<a href="#" draggable="true" class="toolbarButton move_block" style="cursor:move">
					<span class="spanDND ui-icon-arrow-4"></span>
				</a>
				<a href="#" class="toolbarButton config_destroy" data-action="onDelete">
					<span class="spanDND ui-icon-trash"></span>
				</a>
				<?php endif; ?>
			<?php endif; ?>
			<a href="#" style="border-right:0;border-radius: 0 3px 3px 0;" class="toolbarButton" onclick="Parsimony.blocks['admin_blocks'].unSelectBlock();return false;">
				<span class="spanDND ui-icon-closethick"></span>
			</a>
			<div class="arrow" style="left: 20px; border-color: #f9f9f9 transparent transparent;bottom: -14px;margin-left: -7px;width: 0;height: 0;position: absolute;border-width: 7px;border-style: solid;"></div>
		</div>
		<div class="parsimonyResize se"></div>
		<div class="parsimonyResize nw"></div>
		<div class="parsimonyResize ne"></div>
		<div class="parsimonyResize sw"></div>
		<div class="parsimonyMove"></div>
	</div>
</div>