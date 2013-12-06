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
		app::$request->page->addCSSFile('lib/cms.css');
		app::$request->page->addCSSFile('admin/css/ui.css');
		app::$request->page->addCSSFile('admin/css/popin.css');
		app::$request->page->addCSSFile('lib/tooltip/parsimonyTooltip.css');
		app::$request->page->addJSFile('lib/cms.js');
		app::$request->page->addJSFile('lib/tooltip/parsimonyTooltip.js');
		echo app::$request->page->printInclusions()
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