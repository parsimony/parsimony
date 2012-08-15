<!DOCTYPE html>
<html>
    <head>
        <!--[if ie]>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <script type="text/javascript">
        document.createElement('header');
        document.createElement('hgroup');
        document.createElement('nav');
        document.createElement('menu');
        document.createElement('section');
        document.createElement('article');
        document.createElement('aside');
        document.createElement('footer');
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
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="' + BASE_PATH + 'lib/jquery/jquery-1.8.0.min.js"><\/script>')</script>
        <meta name="generator" content="Parsimony">
	<?php echo app::$request->page->printMetas() ?>
        <?php echo app::$request->page->printInclusions() ?>
	<?php echo app::$request->page->head ?>
    </head>
    <body>
	<?php echo $this->body; ?>
    </body>
</html>