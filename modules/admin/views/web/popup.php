<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <script type="text/javascript">
            var BASE_PATH = '<?php echo BASE_PATH ?>';
            var MODULE = '<?php echo MODULE ?>';
            var THEME = '<?php echo THEME ?>';
            var THEMETYPE = '<?php echo THEMETYPE ?>';
            var THEMEMODULE = '<?php echo THEMEMODULE ?>';
            var TOKEN = '<?php echo TOKEN ?>';
        </script>
        <link rel="stylesheet" type="text/css" href="<?php echo BASE_PATH ?>concat?format=css&files=<?php echo BASE_PATH ?>lib/cms.css,<?php echo BASE_PATH ?>admin/style.css,<?php echo BASE_PATH ?>lib/tooltip/parsimonyTooltip.css" />
        <SCRIPT LANGUAGE="Javascript" SRC="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"> </SCRIPT>
        <SCRIPT LANGUAGE="Javascript" SRC="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.js"> </SCRIPT>
        <SCRIPT LANGUAGE="Javascript" SRC="<?php echo BASE_PATH ?>concat?format=js&files=<?php echo BASE_PATH ?>lib/cms.js,<?php echo BASE_PATH ?>lib/tooltip/parsimonyTooltip.js"> </SCRIPT>
        <style>body{overflow: hidden;}</style>
        <script>
	    $(window).load(function() {
		$(".firstpanel a").trigger("click");
	    });
	    $(document).ready(function() {
		$(".tooltip").parsimonyTooltip({triangleWidth:5});
                
	    });
	    $(".adminzonetab a").live('click',function(event){
		event.preventDefault();
		$(".adminzonecontent .admintabs").hide();
		$(".adminzonetab a").removeClass("active");
		$(this).addClass("active");
		$($(this).attr("href")).show();
	    });            
	    /* CTRL-S*/
	    var isCtrl = false;
	    $(window).keydown(function(e) {
		if(e.ctrlKey) isCtrl = true;     
		if(e.keyCode == 83 && isCtrl) {
		    $("form").trigger("submit");
		    return false;
		}
	    }).keyup(function(e) {
                isCtrl = false;
	    });
	    
        </script>    </head>
    <body><div id="parsimonyTooltip">
    <div class="tri"></div>
    <div class="parsimonyTooltipContent"></div>
</div>
	<?php echo $content; ?>
    </body>
    
</html><script>
       /* var body = $(document);
		$("#conf_box_content_iframe,#conf_box_content",window.parent.document).css({
		    "width": body.outerWidth() + "px",
		    "height": body.outerHeight() + "px"
		});
                $("#conf_box",window.parent.document).css({
		    "width": body.outerWidth() + "px"
		}).show();*/
        </script>