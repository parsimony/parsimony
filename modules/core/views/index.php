<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?php echo $this->page->getTitle() ?></title>
		<script type="text/javascript">
			var BASE_PATH = '<?php echo BASE_PATH ?>';
			var MODULE = '<?php echo MODULE ?>';
			var THEME = '<?php echo THEME ?>';
			var THEMETYPE = '<?php echo THEMETYPE ?>';
			var THEMEMODULE = '<?php echo THEMEMODULE ?>';
			var TOKEN = '<?php echo TOKEN ?>';

			/* Get res infos for RWD images */
			document.cookie = "resMax=" + ((screen.height > screen.width ? screen.height : screen.width) * (window.devicePixelRatio ? window.devicePixelRatio : 1)) + "; expires=999; path=/";

		</script>
		<meta name="generator" content="Parsimony">
		<?php if(THEMETYPE === 'desktop'): ?>
		<!--[if ie]>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<![endif]-->
		<!--[if lt IE 9]>
		<script type="text/javascript">
		'article aside footer header nav section time'.replace(/\w+/g,function(n){document.createElement(n)})
		</script>
		<![endif]-->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="' + BASE_PATH + 'lib/jquery/jquery-1.10.1.min.js"><\/script>')</script>
		<?php else: ?>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="' + BASE_PATH + 'lib/jquery/jquery-2.0.2.min.js"><\/script>')</script>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<script type="text/javascript"> !location.hash && setTimeout(function () { window.scrollTo(0, 0);}, 1000);</script>
		<?php endif;
		echo $this->page->printMetas();
		echo $this->printInclusions();
		echo $this->head;
		?>
	</head>
	<body class="module-<?php echo MODULE; ?> page-<?php echo MODULE; ?>-<?php echo $this->page->getId(); ?>">
		<?php echo $body; ?>
		<?php echo $this->printInclusions('footer') ?>
	</body>
</html>