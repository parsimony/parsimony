function blockAdminMenu() {

	this.initBefore = function() {

		/* Orientation and resolution */
		$("#toolbar").on('change', '#changeres', function() {
			if (window.performance.timing.domComplete && ((new Date).getTime() - window.performance.timing.domComplete) > 2000)
				ParsimonyAdmin.$iframe.css('transition', 'all 0.4s');
			var res = this.value;
			$("#currentRes").text(res);
			if (res === 'max') {
				var height = ParsimonyAdmin.currentBody.offsetHeight + 250;
				if (screen.height > height)
					height = screen.height - 28;
				ParsimonyAdmin.$iframe.css({
					"width": "100%",
					"height": height + "px"
				});
				res = ["max", "max"];
			} else {
				res = res.split(/x/);
				if ($("#changeorientation").length === 0 || ($("#changeorientation").val() === 'portrait' && ParsimonyAdmin.getCookie("landscape") === 'portrait')) {
					ParsimonyAdmin.$iframe.css({
						"width": res[0] + "px",
						"height": res[1] + "px"
					});
				} else {
					ParsimonyAdmin.$iframe.css({
						"width": res[1] + "px",
						"height": res[0] + "px"
					});
				}
			}
			document.getElementById("customWidth").value = res[0];
			document.getElementById("customHeight").value = res[1];
			ParsimonyAdmin.setCookie("screenX", res[0], 999);
			ParsimonyAdmin.setCookie("screenY", res[1], 999);
			ParsimonyAdmin.setCookie("landscape", $("#changeorientation").val(), 999);
			setTimeout("ParsimonyAdmin.$iframe.css('transition','none');", 2000);
			setTimeout("Parsimony.blocks['admin_css'].drawMediaQueries();", 500);
		})
				.on('change', '#changeorientation', function(e) {
			ParsimonyAdmin.setCookie("landscape", $("#changeorientation").val(), 999);
			$("#changeres").trigger("change");
		});

		$('#listres').on('click', 'li', function() {
			$('#changeres').val(this.dataset.res).trigger('change');
		})
				.on('change', 'input', function() {
			var width = document.getElementById("customWidth").value;
			var height = document.getElementById("customHeight").value;
			if (width && height) {
				$('#changeres').val(width + "x" + height).trigger('change');
			}
		});

		$('#changeDevice').on('click', 'li', function() {
			ParsimonyAdmin.changeDevice(this.dataset.device);
		});

	}
}

Parsimony.registerBlock("admin_menu", new blockAdminMenu());