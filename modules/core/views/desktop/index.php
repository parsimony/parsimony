<!DOCTYPE html>
<html>
    <head>
        <!--[if ie]>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <script type="text/javascript">
        'article aside footer header nav section time'.replace(/\w+/g,function(n){document.createElement(n)})
        </script>
        <![endif]-->
        <meta charset="utf-8">
        <title><?php echo app::$request->page->getTitle() ?></title>
        <script type="text/javascript">
	    var BASE_PATH = '<?php echo BASE_PATH ?>';
	    var MODULE = '<?php echo MODULE ?>';
	    var THEME = '<?php echo THEME ?>';
	    var THEMETYPE = '<?php echo THEMETYPE ?>';
	    var THEMEMODULE = '<?php echo THEMEMODULE ?>';
	    var TOKEN = '<?php echo TOKEN ?>';
	</script>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="' + BASE_PATH + 'lib/jquery/jquery-1.9.0.min.js"><\/script>')</script>
        <meta name="generator" content="Parsimony">
	<?php echo app::$request->page->printMetas() ?>
        <?php echo app::$request->page->printInclusions() ?>
	<?php echo app::$request->page->head ?>
    </head>
    <body class="module-<?php echo MODULE; ?> page-<?php echo MODULE; ?>-<?php echo app::$request->page->getId(); ?>">
	<?php echo $this->body; ?>
	<?php echo app::$request->page->printInclusions('footer') ?>
    </body>
</html>