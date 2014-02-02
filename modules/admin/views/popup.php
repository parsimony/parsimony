<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title></title>
		<script type="text/javascript">
			var BASE_PATH = '<?php echo BASE_PATH ?>';
			var MODULE = '<?php echo MODULE ?>';
			var THEMETYPE = '<?php echo THEMETYPE ?>';
			var TOKEN = '<?php echo TOKEN ?>';
		</script>
		<script src="<?php echo BASE_PATH; ?>lib/jquery/jquery-2.0.2.min.js"></script>
		<?php
		app::$response->page->addCSSFile('core/css/parsimony.css');
		app::$response->page->addCSSFile('admin/css/ui.css');
		app::$response->page->addCSSFile('admin/css/popin.css');
		app::$response->page->addCSSFile('lib/tooltip/parsimonyTooltip.css');
		app::$response->page->addJSFile('core/js/parsimony.js');
		app::$response->page->addJSFile('lib/tooltip/parsimonyTooltip.js');
		echo app::$response->page->printInclusions()
		?>
		<script>
			$(document).ready(function() {
				$(".tooltip").parsimonyTooltip({triangleWidth: 5});
				if ($(".firstpanel").length > 0)
					$(".firstpanel a").trigger("click");
				else
					(typeof top.ParsimonyAdmin != "undefined" ? top.ParsimonyAdmin.resizeConfBox() : opener.top.ParsimonyAdmin.resizeConfBox());

				/* Notifications */
				var form = document.querySelector("form");
				if (form) {
					form.addEventListener("submit", function(e) {
						if (window.Notification.permission != "granted" && window.Notification.permission != "denied") {
							e.preventDefault();
							Notification.requestPermission(function() {
								$("form").trigger("submit");
							});
						}
					}, false);
				}

			}).on('click', ".adminzonetab a", function(event) {
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
					$("form").trigger("submit")
				}
			}, false);
		</script>
	</head>
	<body>
		<span id="conf_box_close" onclick="top.ParsimonyAdmin.closeConfBox()" class="floatright ui-icon ui-icon-closethick"></span>
		<?php echo $content; ?>
	</body>
</html>