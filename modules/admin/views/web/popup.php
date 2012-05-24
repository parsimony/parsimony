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
        <SCRIPT LANGUAGE="Javascript" SRC="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"> </SCRIPT>
        <?php
        app::$request->page->addCSSFile(BASE_PATH . 'lib/cms.css');
        app::$request->page->addCSSFile(BASE_PATH . 'admin/style.css');
        app::$request->page->addCSSFile(BASE_PATH . 'lib/tooltip/parsimonyTooltip.css');
        app::$request->page->addJSFile(BASE_PATH . 'lib/cms.js');
        app::$request->page->addJSFile(BASE_PATH . 'lib/tooltip/parsimonyTooltip.js');
        echo app::$request->page->printInclusions()
        ?>
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
	    
        </script>
    </head>
    <body>
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