function blockAdminMenu() {

	this.init = function() {

		/* Orientation and resolution */
		$("#toolbar").on('change', '#changeres', function() {

			var res = this.value;
			$("#currentRes").text(res);
			if (res === 'max') {
				ParsimonyAdmin.$previewContainer.css({
					"width": "100%",
					"height": "100%"
				});
				document.body.classList.remove("sizedPreview");
				res = ["max", "max"];
			} else {
				res = res.split(/x/);
				document.body.classList.add("sizedPreview");
				if ($("#changeorientation").length === 0 || ($("#changeorientation").val() === 'portrait' && ParsimonyAdmin.getCookie("landscape") === 'portrait')) {
					ParsimonyAdmin.$previewContainer.css({
						"width": res[0] + "px",
						"height": res[1] + "px"
					});
				} else {
					ParsimonyAdmin.$previewContainer.css({
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
			ParsimonyAdmin.changeDevice(this.dataset.version);
		});

	}
}

Parsimony.registerBlock("admin_menu", new blockAdminMenu());