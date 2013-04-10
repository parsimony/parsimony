<!DOCTYPE html>
<html>
    <head>
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
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="' + BASE_PATH + 'lib/jquery/jquery-1.9.1.min.js"><\/script>')</script>
        <meta name="generator" content="Parsimony">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <script type="text/javascript"> !location.hash && setTimeout(function () { window.scrollTo(0, 0);}, 1000);</script>
        <?php \app::$request->page->addCSSFile(BASE_PATH . 'lib/mobile.css'); ?>
	<?php echo app::$request->page->printMetas() ?>
        <?php echo app::$request->page->printInclusions() ?>
	<?php echo app::$request->page->head ?>
    </head>
    <body class="module-<?php echo MODULE; ?> page-<?php echo MODULE; ?>-<?php echo app::$request->page->getId(); ?>">
	<?php echo $this->body; ?>
	<?php echo app::$request->page->printInclusions('footer') ?>
    </body>
</html>