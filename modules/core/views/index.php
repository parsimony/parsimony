<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?php echo $this->page->getTitle() ?></title>
		<script type="text/javascript">
			var BASE_PATH = '<?php echo BASE_PATH ?>';
			var MODULE = '<?php echo MODULE ?>';
			var THEME = '<?php echo THEME ?>';
			var DEVICE = '<?php echo DEVICE ?>';
			var THEMEMODULE = '<?php echo THEMEMODULE ?>';
			var TOKEN = '<?php echo TOKEN ?>';

			document.cookie = "resMax=" + ((screen.height > screen.width ? screen.height : screen.width) * (window.devicePixelRatio ? window.devicePixelRatio : 1)) + "; expires=999; path=/";
		</script>
		<link rel="shortcut icon" href="<?php echo BASE_PATH . (isset(\app::$config['favicon']) ? \app::$config['favicon'] : 'core/img/favicon.png');  ?>" />
		<meta name="generator" content="Parsimony">
		<?php if(DEVICE === 'desktop'): ?>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<!--[if lt IE 9]>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script type="text/javascript">
		'article aside footer header nav section time'.replace(/\w+/g,function(n){document.createElement(n)})
		</script>
		<![endif]-->
		<!--[if gte IE 9]><!-->
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="' + BASE_PATH + 'lib/jquery/jquery-2.1.0.min.js"><\/script>')</script>
		<!--<![endif]-->
		<?php else: ?>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="' + BASE_PATH + 'lib/jquery/jquery-2.1.min.js"><\/script>')</script>
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