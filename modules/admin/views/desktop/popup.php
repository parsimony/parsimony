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
        <script src="<?php echo BASE_PATH; ?>lib/jquery/jquery-2.0.2.min.js"></script>
        <?php
        app::$request->page->addCSSFile('lib/cms.css');
        app::$request->page->addCSSFile('admin/style.css');
        app::$request->page->addCSSFile('lib/tooltip/parsimonyTooltip.css');
        app::$request->page->addJSFile('lib/cms.js');
        app::$request->page->addJSFile('lib/tooltip/parsimonyTooltip.js');
        echo app::$request->page->printInclusions()
        ?>
        <style>body{overflow: hidden;background: white;}</style>
        <script>
            $(document).ready(function() {
                $(".tooltip").parsimonyTooltip({triangleWidth:5});
                if( $(".firstpanel").length > 0) $(".firstpanel a").trigger("click");
                else ( typeof top.ParsimonyAdmin != "undefined" ? top.ParsimonyAdmin.resizeConfBox() : opener.top.ParsimonyAdmin.resizeConfBox())
            }).on('click',".adminzonetab a",function(event){
                event.preventDefault();
                $(".adminzonecontent .admintabs").hide();
                $(".adminzonetab a").removeClass("active");
                $(this).addClass("active");
                $($(this).attr("href")).show();
		typeof top.ParsimonyAdmin != "undefined" ? top.ParsimonyAdmin.resizeConfBox() : opener.top.ParsimonyAdmin.resizeConfBox();
            });            
            /* Shortcut : Save on CTRL+S */
	    document.addEventListener("keydown", function(e) {
		if (e.keyCode == 83 && e.ctrlKey) {
		    e.preventDefault();
		    /* Ask for native notifications if it's enable : an event is required for notifications */
                    if (window.Notification){
                        window.Notification.requestPermission(function(permission){ 
                            return true;
                        });
                    }
                    $("form").trigger("submit")
		}
	    }, false);
        </script>
    </head>
    <body>
<?php echo $content; ?>
    </body>

</html>