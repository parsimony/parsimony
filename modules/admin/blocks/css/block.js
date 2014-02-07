function blockAdminCSS() {

	this.currentIdStylesheet = 0;
	this.currentIdRule = 0;
	this.currentIdMedia = 0;
	this.currentMediaText = "";
	this.currentRule;
	this.currentFile;


	this.initPreview = function() {
		document.getElementById("changecsspath").value = CSSTHEMEPATH;
	}
	
	this.loadCreationMode = function() {
		
		var $this = this;
		
		/* Init visual tool (drag 'n drop/resize/move block) */

		/* Manage Visual tool for Deag 'n drop */
		$(".parsimonyMove").on("mousedown.creation", function(e) {
			e.stopImmediatePropagation();
			document.getElementById("overlays").style.pointerEvents = "all";
			var dndstart = {
				left: isNaN(parseFloat($this.currentRule.style.left)) ? 0 : $this.currentRule.style.left,
				top: isNaN(parseFloat($this.currentRule.style.top)) ? 0 : $this.currentRule.style.top,
				pageX: e.pageX,
				pageY: e.pageY,
				rule: $this.currentRule
			};

			$(document).add(ParsimonyAdmin.currentDocument).on("mousemove.parsimonyDND", dndstart, function(e) {
				dndstart.rule.style.left = parseFloat(dndstart.left) + e.pageX - dndstart.pageX + "px";
				dndstart.rule.style.top = parseFloat(dndstart.top) + e.pageY - dndstart.pageY + "px";
				document.getElementById("box_top").value = dndstart.rule.style.top || "";
				document.getElementById("box_left").value = dndstart.rule.style.left || "";
				$this.updatePosition(ParsimonyAdmin.currentDocument.getElementById(ParsimonyAdmin.inProgress).getBoundingClientRect());
			})
			.on("mouseup.parsimonyDND", dndstart, function(e) {
				document.getElementById("overlays").style.pointerEvents = "none";
				$("#box_top").val(dndstart.rule.style.top !== 'auto' ? dndstart.rule.style.top : '').trigger("change");
				$("#box_left").val(dndstart.rule.style.left !== 'auto' ? dndstart.rule.style.left : '').trigger("change");
				$(document).add(ParsimonyAdmin.currentDocument).off("mousemove").off("mouseup");
				// $this.checkChanges();
			});
		});

		/* Manage Visual tool for Resize */
		$(".parsimonyResize").on("mousedown.creation", function(e) {
			e.stopImmediatePropagation();
			document.getElementById("overlays").style.pointerEvents = "all";
			var DNDiframe = ParsimonyAdmin.currentDocument.getElementById(ParsimonyAdmin.inProgress);
			var bounds = DNDiframe.getBoundingClientRect();
			var dndstart = {
				DNDiframe: DNDiframe,
				width: bounds.width,
				height: bounds.height,
				left: isNaN(parseInt($this.currentRule.style.left)) ? 0 : $this.currentRule.style.left,
				top: isNaN(parseInt($this.currentRule.style.top)) ? 0 : $this.currentRule.style.top,
				pageX: e.pageX,
				pageY: e.pageY,
				dir: this.getAttribute('class').replace("parsimonyResize ", ""),
				rule: $this.currentRule
			};

			$(document).add(ParsimonyAdmin.currentDocument).on("mousemove.parsimonyDND", dndstart, function(e) {
				switch (dndstart.dir) {
					case "se":
						dndstart.rule.style.width = parseInt(dndstart.width) + (e.pageX - dndstart.pageX) + "px";
						dndstart.rule.style.height = parseInt(dndstart.height) + (e.pageY - dndstart.pageY) + "px";
						break;
					case "nw":
						dndstart.rule.style.top = parseInt(dndstart.top) + (e.pageY - dndstart.pageY) + "px";
						dndstart.rule.style.left = parseInt(dndstart.left) + (e.pageX - dndstart.pageX) + "px";
						dndstart.rule.style.width = parseInt(dndstart.width) - (e.pageX - dndstart.pageX) + "px";
						dndstart.rule.style.height = parseInt(dndstart.height) - (e.pageY - dndstart.pageY) + "px";
						break;
					case "ne":
						dndstart.rule.style.top = parseInt(dndstart.top) + (e.pageY - dndstart.pageY) + "px";
						dndstart.rule.style.width = parseInt(dndstart.width) + (e.pageX - dndstart.pageX) + "px";
						dndstart.rule.style.height = parseInt(dndstart.height) - (e.pageY - dndstart.pageY) + "px";
						break;
					case "sw":
						dndstart.rule.style.left = parseInt(dndstart.left) + (e.pageX - dndstart.pageX) + "px";
						dndstart.rule.style.width = parseInt(dndstart.width) - (e.pageX - dndstart.pageX) + "px";
						dndstart.rule.style.height = parseInt(dndstart.height) + (e.pageY - dndstart.pageY) + "px";
						break;
				}
				$this.updatePosition(dndstart.DNDiframe.getBoundingClientRect());
				document.getElementById("box_width").value = dndstart.rule.style.width;
				document.getElementById("box_height").value = dndstart.rule.style.height;
				document.getElementById("box_top").value = dndstart.rule.style.top;
				document.getElementById("box_left").value = dndstart.rule.style.left;
			})
			.on("mouseup", dndstart, function(e) {
				document.getElementById("overlays").style.pointerEvents = "none";
				$("#box_width").val(dndstart.rule.style.width !== 'auto' ? dndstart.rule.style.width : '').trigger("change");
				$("#box_height").val(dndstart.rule.style.height !== 'auto' ? dndstart.rule.style.height : '').trigger("change");
				$("#box_top").val(dndstart.rule.style.top !== 'auto' ? dndstart.rule.style.top : '').trigger("change");
				$("#box_left").val(dndstart.rule.style.left !== 'auto' ? dndstart.rule.style.left : '').trigger("change");
				$(document).add(ParsimonyAdmin.currentDocument).off("mousemove").off("mouseup");
				//$this.checkChanges();
			});
		});



		/* CSSLight */
		$("#panelcss").on("click.creation", ".CSSLighthider", function(e) {
			var line = this.parentNode.parentNode;
			var lineNb = line.dataset.line;
			var texta = line.parentNode.previousSibling;
			texta.focus(); //to init
			var id = texta.id;
			if (line.classList.contains("CSSLightbarre")) {
				line.classList.remove('CSSLightbarre');
				delete $this.codeEditors[id].rm[lineNb];
			} else {
				line.classList.add('CSSLightbarre');
				$this.codeEditors[id].rm[lineNb] = lineNb;
			}
			$this.codeEditors[id].draw();
		})

		/* Init current CSS rule editor focus */
		.on("focus.creation", ".csscode", function(e) {
			$this.setCurrentRule(this.dataset.nbstyle, this.dataset.nbrule, this.dataset.nbmedia);
		})

		/* Save changes */
		.on("click.creation", "#savemycss", function() {
			/* Save changes */
			$.post(BASE_PATH + "admin/saveCSS", {TOKEN: ParsimonyAdmin.currentWindow.TOKEN,
				changes: JSON.stringify(ParsimonyAdmin.CSSValuesChanges) /* encode to allow a [class="tt"] selectors */
			}, function(data) {
				ParsimonyAdmin.execResult(data);
				/* Update CSSValues */
				for (var file in ParsimonyAdmin.CSSValuesChanges) {
					for (var key in ParsimonyAdmin.CSSValuesChanges[file]) {
						if (ParsimonyAdmin.CSSValues[file] && ParsimonyAdmin.CSSValues[file][key]) {
							ParsimonyAdmin.CSSValues[file][key].s = ParsimonyAdmin.CSSValuesChanges[file][key].selector;
							ParsimonyAdmin.CSSValues[file][key].p = ParsimonyAdmin.CSSValuesChanges[file][key].value;
						}
					}
				}
				/* Clean changes */
				ParsimonyAdmin.CSSValuesChanges = {};
				/* Reinit UI */
				$this.checkChanges();
			});
		})

		/* Reinit changes */
		.on("click.creation", "#reinitcss", function() {
			for (var file in ParsimonyAdmin.CSSValuesChanges) {
				for (var key in ParsimonyAdmin.CSSValuesChanges[file]) {
					var selector = ParsimonyAdmin.CSSValuesChanges[file][key];
					var selectors = $this.findSelectorsByElement(selector.selector, selector.media);
					var selectorName = selector.media.replace(/\s+/g, "") + selector.selector;
					if (typeof selectors[file] !== "undefined") {
						var infos = selectors[file][selectorName];
						if (infos) {
							var oldValue = "";
							if (typeof ParsimonyAdmin.CSSValues[file][selectorName] !== "undefined") {
								oldValue = ParsimonyAdmin.CSSValues[file][selectorName].p
							}
							$this.setCurrentRule(infos.nbStylesheet, infos.nbRule, infos.nbMedia);
							$this.setCss($this.currentRule, oldValue);
							var editor = document.getElementById('idcss' + infos.nbStylesheet + "_" + infos.nbRule + "_" + infos.nbMedia);
							if (editor)
								editor.value = oldValue;
						}
					}
					delete ParsimonyAdmin.CSSValuesChanges[file][key];
				}
			}
			$this.checkChanges();
			document.getElementById('css_panel').style.display = 'none';
			$this.updatePosition(ParsimonyAdmin.currentDocument.getElementById(ParsimonyAdmin.inProgress).getBoundingClientRect());
		})

		/* Build media query syntax */
		.on("keyup.creation", "#mdqMinWidthValue, #mdqMaxWidthValue", function() {
			var minWidth = document.getElementById('mdqMinWidthValue').value;
			var maxWidth = document.getElementById('mdqMaxWidthValue').value;
			var media = "";
			var mediaText = [];
			if (minWidth.length > 0) {
				media += " and (min-width:" + minWidth + "px)";
				mediaText.push(minWidth);
			}
			if (maxWidth.length > 0) {
				media += " and (max-width:" + maxWidth + "px)";
				mediaText.push(maxWidth);
			}
			if (media.length > 0)
				media = "@media screen" + media;
			document.getElementById('currentMdq').value = media;
			document.getElementById("currentMdq").dataset.range = mediaText.join(",");
			document.getElementById("panelcss").classList.add("CSSSearch");
		})
		/* Build media query syntax */
		.on("change.creation", "#currentMdq", function() {
			var media = this.value.replace(/\s+/g, "");
			var min = media.match(/min-width:([0-9]*)px/);
			var max = media.match(/max-width:([0-9]*)px/);
			document.getElementById('mdqMinWidthValue').value = (min != null ? min[1] : "");
			document.getElementById('mdqMaxWidthValue').value = (max != null ? max[1] : "");
			if (this.value.length > 0) {
				document.getElementById('mediaqueries').classList.remove('none');
			} else {
				document.getElementById('mediaqueries').classList.add('none');
			}

		})

		/* Manage CSS updates with visual forms */
		.on("keyup.creation change.creation", ".liveconfig", function() {

			/* Get the CSS property in camelCase notation( usefull for firefox) */
			var jsProp = this.dataset.css.replace(/-([a-z])/g, function(s, t) {
				return t.toUpperCase();
			});
			/* Set style in current CSS rule */
			if (this.value.length > 0) {
				$this.currentRule.style[jsProp] = this.value;
			} else {
				$this.currentRule.style[jsProp] = "";
			}

			/* Update position of visual tool */
			$this.updatePosition(ParsimonyAdmin.currentDocument.getElementById(ParsimonyAdmin.inProgress).getBoundingClientRect());

			/* Save change, we update from our old CSS string ( from CSSValuesChanges obj ) because shorthand propeties are exploded in CSS rules ( background => background-color..etc */
			var currentSelector = document.getElementById("current_selector_update").value;
			var key = $this.currentMediaText.replace(/\s+/g, '') + currentSelector;
			/* Get last CSS code */
			var oldCssText = $this.getLastCSS($this.currentFile, key);

			$this.setCssChange($this.currentFile, currentSelector, $this.updateCSSText(oldCssText, this.dataset.css, this.value), $this.currentMediaText);
		})

		/* Explorer */
		.on('click.creation', ".explorer", function() {
			window.callbackExplorerID = this.getAttribute("rel");
			window.callbackExplorer = function(file) {
				$("#" + window.callbackExplorerID).val(BASE_PATH + file);
				$("#" + window.callbackExplorerID).trigger('change');
				window.callbackExplorer = function(file) {
					return false;
				};
				ParsimonyAdmin.explorer.close();
			}
			ParsimonyAdmin.displayExplorer();
		})

		/* Tabs of visual panel css */
		.on('click.creation', '#css_menu .cssTab', function() {
			document.querySelector("#css_menu .cssTab.active").classList.remove("active");
			this.classList.add("active");
			$(".panelcss_tab").addClass("hiddenTab");
			document.getElementById(this.getAttribute("rel")).classList.remove("hiddenTab");
		})

		/* Input selecto management : current_selector_update */
		.on("change.creation", '#changecsspath', function() {
			if (document.getElementById("current_selector_update").value.length > 2) {
				$this.displayCSSConf(document.getElementById("changecsspath").value, document.getElementById("current_selector_update").value);
			}
		})

		.on("keypress.creation", '#current_selector_update', function(e) {
			if (e.which === 13) { // if [enter]
				if (document.getElementById("panelcss").classList.contains('CSSCode')) {
					document.getElementById("switchtocode").click();
				} else {
					document.getElementById("switchtovisuel").click();
				}
				e.preventDefault(); // to avoid submit form and save css
			}
		})
		.on('keyup.creation', "#current_selector_update", function(event) {
			event.stopPropagation();
			if (event.which !== 13) { // if not [enter]
				if (event.type === 'keyup') {
					try { /* In case of bad selector */
						$('.cssPicker', ParsimonyAdmin.currentDocument).removeClass('cssPicker');
						$(this.value, ParsimonyAdmin.currentDocument).addClass('cssPicker');
					} catch (E) {}
					if (this.value.length > 0) {
						document.getElementById("panelcss").classList.add("CSSSearchBTNS");
					} else {
						document.getElementById("panelcss").classList.remove("CSSSearchBTNS");
					}
					document.getElementById("panelcss").classList.add("CSSSearch");
				}
			}
		})

		/* Opacity visual tool */
		.on('change.creation', '#slider-range-max', function( ) {
			var val = this.value;
			if(val == 1) val = '';
			document.getElementById("positioning_opacity").value = val;
			trigger(document.getElementById("positioning_opacity"), "change");
		})

		/* Color picker */
		.on('click.creation', '.colorpicker2, .colorpicker3', function() {
			if (this.classList.contains("colorpicker2")) {
				currentColorPicker = $(this);
			} else {
				currentColorPicker = $(this).prev().focus();
			}
			picker.el.style.display = "block";
			picker.el.style.top = ($(this).offset().top) + 20 + "px";
			picker.el.style.left = ($(this).offset().left - 200) + "px";
		})
		.on('blur.creation', '.colorpicker2', function() {
			picker.el.style.display = "none";
		})

		/* Representation schemas */
		.on('keyup.creation change.creation', '.representation input:not(".resultcss")', function(e) {
			obj = $(this).closest('.representation')[0];
			var top = obj.querySelector('.repr_top');
			var right = obj.querySelector('.repr_right');
			var bottom = obj.querySelector('.repr_bottom');
			var left = obj.querySelector('.repr_left');
			var init = obj.getAttribute("init");
			if (!top.value)
				top.value = init;
			if (!right.value)
				right.value = init;
			if (!bottom.value)
				bottom.value = init;
			if (!left.value)
				left.value = init;
			top = top.value;
			right = right.value;
			bottom = bottom.value;
			left = left.value;
			var result = "";

			if (top == bottom && top == right && top == left) {
				result = top;
			} else if (right == left && top == bottom) {
				result = top + ' ' + right;
			} else if (right == left & top != bottom) {
				result = top + ' ' + right + ' ' + bottom;
			} else {
				result = top + ' ' + right + ' ' + bottom + ' ' + left;
			}

			obj.querySelector('.resultcss').value = result;
			trigger(obj.querySelector('.resultcss'), e.type);
		})
		.on('keyup.creation change.creation init.creation', '.resultcss', function() {
			obj = $(this).closest('.representation')[0];
			var top = obj.querySelector('.repr_top');
			var right = obj.querySelector('.repr_right');
			var bottom = obj.querySelector('.repr_bottom');
			var left = obj.querySelector('.repr_left');
			var expl = obj.querySelector('.resultcss').value.trim();
			if (expl.length > 0) {
				expld = expl.split(' ');
				switch (expld.length) {
					case 1 :
						top.value = expl;
						right.value = expl;
						bottom.value = expl;
						left.value = expl;
						break;
					case 2 :
						top.value = expld[0];
						right.value = expld[1];
						bottom.value = expld[0];
						left.value = expld[1];
						break;
					case 3 :
						top.value = expld[0];
						right.value = expld[1];
						bottom.value = expld[2];
						left.value = expld[1];
						break;
					case 4 :
						top.value = expld[0];
						right.value = expld[1];
						bottom.value = expld[2];
						left.value = expld[3];
						break;
				}
			}
		})

		/* Spinner */
		.on("keydown", ".spinner", function(event) {
			if (event.which == 40 || event.which == 38) {
				event.preventDefault();
				var num = this.value;
				if (num != '')
					num = parseInt(num);
				else
					num = 0;
				var text = this.value.replace(num, '') || this.dataset.sufix || "";
				if (event.which == 40) {
					this.value = (num - 1) + text;
				} else if (event.which == 38) {
					this.value = (num + 1) + text;
				}
				trigger(this, "change");
			}
		})

		/* Autocomlete */
		.on("click.creation", ".autocomplete", function() {
			/* We clear datalist */
			document.getElementById("parsidatalist").innerHTML = "";
			this.setAttribute("list", "parsidatalist");
			if (this.id == "current_selector_update") {
				$.getJSON("admin/getCSSSelectors?filePath=" + document.getElementById("changecsspath").value, function(data) {
					$.each(data, function(i, value) {
						options += '<option value="' + value + '" />';
					});
					document.getElementById("parsidatalist").innerHTML = options;
				});
			} else {
				var options = "";
				$.each($.parseJSON(this.dataset.options), function(i, value) {
					options += '<option value="' + value + '" />';
				});
				document.getElementById("parsidatalist").innerHTML = options;
			}
		});

		/* Media queries UI */
		$("#mediaqueries").on("click.creation", ".mediaq", function(e) {
			var currentMdq = document.getElementById("currentMdq");
			currentMdq.value = this.dataset.media;
			trigger(currentMdq, "change");

			/* If a selector is already selected */
			var currentPath = document.getElementById("changecsspath").value;
			var currentSelector = document.getElementById("current_selector_update").value;
			if (currentPath.length > 0 && currentSelector.length > 0) {
				$this.displayCSSConf(currentPath, currentSelector, this.dataset.media);
			}

			var changeres = document.getElementById("changeres");
			if (changeres.value != "max") {
				var height = changeres.value.split("x")[1];
				var mediaQueryRange = currentMdq.dataset.range.split(",");
				var width = mediaQueryRange[mediaQueryRange.length - 1];
				changeres.value = width + "x" + height;
				trigger(changeres, "change");
			}
		})

		/* Button to remove media querie context */
		.on("click.creation", "#removeMDQ", function() {
			document.getElementById("mdqMinWidthValue").value = "";
			document.getElementById("mdqMaxWidthValue").value = "";
			document.getElementById("currentMdq").value = "";

			/* If a selector is already selected */
			var currentPath = document.getElementById("changecsspath").value;
			var currentSelector = document.getElementById("current_selector_update").value;
			if (currentPath.length > 0 && currentSelector.length > 0) {
				$this.displayCSSConf(currentPath, currentSelector, "");
			}
			document.getElementById('mediaqueries').classList.add('none');
		});

		/* Font text-decoration */
		$(".decoration").on("click.creation", ".optionDeco", function(e) {
			this.classList.toggle("active");
			var deco = '';
			$('.active', e.delegateTarget).each(function() {
				deco = deco + this.dataset.val + ' ';
			});
			deco = deco.replace(/[\s]{2,}/g, ' ');
			document.getElementById("css-decoration").value = deco;
			trigger(document.getElementById("css-decoration"), "change");
		})
		.on("change.creation", ".prop_text-decoration", function() {
			var container = this.parentNode;
			var value = this.value.replace(/[\s]{2,}/g, ' ');
			var values = value.split(" ");
			$('.active', container).each(function() {
				this.classList.remove("active");
			});
			for (var i = 0, len = values.length; i < len; i++) {
				container.querySelector('[data-val="' + values[i] + '"]').classList.add("active");
			}
		});

		/* Font style */
		$(".fontstyle").on("click.creation", ".optionFontStyle", function() {
			var input = this.querySelector("input");
			if (this.classList.contains("active")) {
				this.classList.remove("active");
				input.value = "";
			} else {
				this.classList.add("active");
				input.value = this.dataset.val;
			}
			trigger(input, "change");
		})
		.on("change.creation", ".prop_font-weight, .prop_font-style", function() {
			var container = this.parentNode;
			if (this.value == "") {
				container.classList.remove("active");
			} else {
				container.classList.add("active");
			}
		});

		/* Font alignement */
		$(".alignement").on("click.creation", ".optionAlign", function() {
			var input = document.getElementById("text_align");
			if (this.classList.contains("active")) {
				this.classList.remove("active");
				input.value = "";
			} else {
				var container = this.parentNode;
				if (container.querySelector(".active")) {
					container.querySelector(".active").classList.remove("active");
				}
				this.classList.add("active");
				input.value = this.dataset.val;
			}

			trigger(input, "change");
		})
		.on("change.creation", ".prop_text-align", function() {
			var container = this.parentNode;
			if (this.value == "") {
				if (container.querySelector(".active")) {
					container.querySelector(".active").classList.remove("active");
				}
			} else {
				container.querySelector('[data-val="' + this.value.toLowerCase() + '"]').classList.add("active");
			}
		});

		/* Background Tab */
		$("#panelcss_tab_background").on("change.creation", ".ruleBack", function() {
			var back_im = document.querySelector(".prop_background-image").value;
			var color = document.querySelector(".prop_background-color").value;
			if (back_im.length > 0) {
				back_im = "url(" + back_im + ") ";
			} else if (color.length == 0) {
				back_im = " url(admin/img/transparent.png) "
			}

			//var size = document.querySelector(".prop_background-size").value;
			var back = color + ' ' + back_im + ' ' +
					document.querySelector(".prop_background-repeat").value + ' ' +
					document.querySelector(".prop_background-position").value + ' ' +
					//(size.length > 0 && document.querySelector(".prop_background-position").value.length > 0 ? '/' + size + ' ' : ' ') + 
					document.querySelector(".prop_background-attachment").value + ' ' +
					document.querySelector(".prop_background-origin").value + ' ' +
					document.querySelector(".prop_background-clip").value;
			back = back.replace(/[\s]{2,}/g, ' ');
			document.querySelector(".prop_background").value = back.replace(" url(admin/img/transparent.png)", "");
			trigger(document.querySelector(".prop_background"), "change");
			document.getElementById("backTest").style.background = back;
		})
		.on("init.creation", ".prop_background", function() {

			var parse = document.getElementById("parseCSS").style;
			parse.background = this.value;
			document.querySelector(".prop_background-color").value = (parse.backgroundColor && parse.backgroundColor != "initial" ? rgbToHex(parse.backgroundColor) : "");
			document.querySelector(".prop_background-image").value = (parse.backgroundImage && parse.backgroundImage != "initial" && parse.backgroundImage != "none" ? parse.backgroundImage.replace("url(", "").replace(")", "").replace('"', "").replace(window.location.origin + window.location.pathname, "") : "");
			document.querySelector(".prop_background-position").value = (parse.backgroundAttachment && parse.backgroundPosition != "initial" ? parse.backgroundPosition : "");
			document.querySelector(".prop_background-attachment").value = (parse.backgroundAttachment && parse.backgroundAttachment != "initial" ? parse.backgroundAttachment : "");
			document.querySelector(".prop_background-repeat").value = (parse.backgroundRepeat && parse.backgroundRepeat != "initial" ? parse.backgroundRepeat : "");
			document.querySelector(".prop_background-origin").value = (parse.backgroundOrigin && parse.backgroundOrigin != "initial" ? parse.backgroundOrigin : "");
			document.querySelector(".prop_background-clip").value = (parse.backgroundClip && parse.backgroundClip != "initial" ? parse.backgroundClip : "");

			/* Preview */
			document.getElementById("backTest").style.background = this.value;

			/* Clean for further use */
			parse.background = "";
		});

		$("#backTest").on("mousedown.creation", function(e) {
			e.stopImmediatePropagation();
			document.getElementById("overlays").style.pointerEvents = "all";
			var dndstart = {left: isNaN(parseInt(this.style.left)) ? 0 : parseInt(this.style.left),
				top: isNaN(parseInt(this.style.top)) ? 0 : parseInt(this.style.top),
				pageX: e.pageX, pageY: e.pageY};

			$(document).on("mousemove.parsimonyDND", dndstart, function(e) {
				var left = e.pageX - dndstart.pageX;
				var top = e.pageY - dndstart.pageY;
				document.querySelector(".prop_background-position").value = left + "px " + top + "px";
				document.getElementById("backTest").style.backgroundPosition = left + "px " + top + "px";
			})
			.on("mouseup.parsimonyDND", dndstart, function(e) {
				trigger(document.querySelector(".prop_background-position"), "change");
				document.getElementById("overlays").style.pointerEvents = "none";
				$(document).off("mousemove").off("mouseup");
			});
		});

		/* shadowWidget init*/
		$('.shadowWidget').on("change.creation", ".rulePart", function(e) {
			var container = e.delegateTarget;
			var el = container.querySelector(".resultShadow");
			el.value = container.querySelector('.h-offset').value.trim() + ' ' + container.querySelector('.v-offset').value.trim() + ' ' + container.querySelector('.blurShadow').value.trim() + ' ' + container.querySelector('.colorShadow').value.trim();
			trigger(el, "change");
			container.querySelector(".pointer").style.left = parseInt(container.querySelector('.h-offset').value) + 16 + "px";
			container.querySelector(".pointer").style.top = parseInt(container.querySelector('.v-offset').value) + 16 + "px";
		})
		.on("change.creation", ".resultShadow", function(e) {
			var container = e.delegateTarget;
			var el = container.querySelector(".resultShadow");
			var res = el.value.split(" ");
			container.querySelector(".h-offset").value = res[0];
			container.querySelector(".v-offset").value = res[1];
			if (res[2])
				container.querySelector(".blurShadow").value = res[2];
			if (res[3])
				container.querySelector(".colorShadow").value = res[3];
		})
		.on("mousedown.creation", ".pointer", function(e) {
			e.stopImmediatePropagation();
			document.getElementById("overlays").style.pointerEvents = "all";
			var dndstart = {left: isNaN(parseInt(this.style.left)) ? 0 : parseInt(this.style.left) - 16,
				top: isNaN(parseInt(this.style.top)) ? 0 : parseInt(this.style.top) - 16,
				pointer: this,
				container: e.delegateTarget,
				pageX: e.pageX, pageY: e.pageY};

			$(document).on("mousemove.parsimonyDND", dndstart, function(e) {
				var left = e.pageX - dndstart.pageX;
				var top = e.pageY - dndstart.pageY;
				var left2 = 16 + left + dndstart.left;
				var top2 = 16 + top + dndstart.top;
				if (left2 > 32)
					left2 = 32;
				else if (left2 < 0)
					left2 = 0;
				if (top2 > 32)
					top2 = 32;
				else if (top2 < 0)
					top2 = 0;
				dndstart.container.querySelector('.h-offset').value = left2 - 16 + "px";
				dndstart.container.querySelector('.v-offset').value = top2 - 16 + "px";
				trigger(dndstart.container.querySelector('.v-offset'), "change");
			})
					.on("mouseup.parsimonyDND", dndstart, function(e) {
				document.getElementById("overlays").style.pointerEvents = "none";
				$(document).off("mousemove").off("mouseup");
			});
		});

		/* Borders */
		$('#panelcss_tab_border_general').on("click.creation", ".borderMarkers", function(e) {
			e.delegateTarget.querySelector('.active').classList.remove("active");
			this.classList.add("active");
			trigger(e.delegateTarget.querySelector('.prop_' + this.dataset.targetcss), "change");
		})
		.on("change.creation", ".rulePart", function(e) {
			var container = e.delegateTarget;
			var elmt = container.querySelector('.prop_' + container.querySelector('.active').dataset.targetcss);
			elmt.value = (container.querySelector('.borderWidth').value.trim() || "1px") + ' ' + (container.querySelector('.borderStyle').value.trim() || "solid") + ' ' + (container.querySelector('.borderColor').value.trim() || "#000000");
			trigger(elmt, "change");
		})
		.on("click.creation", ".clearBorder", function(e) {
			var container = e.delegateTarget;
			var elmt = container.querySelector('.prop_' + container.querySelector('.active').dataset.targetcss);
			elmt.value = "";
			trigger(elmt, "change");
		})
		.on("change.creation", ".liveconfig", function(e) {
			var container = e.delegateTarget;
			var value = this.value.trim();
			if (value.length > 0) {
				var cut = value.split(" ");
				for (var i = 0, len = cut.length; i < len; i++) {
					cut[i] = cut[i].trim();
					if (cut[i].substring(0, 1) == "#") {
						container.querySelector('.borderColor').value = cut[i];
					} else if (",none,solid,dashed,dotted,double,groove,ridge,inset,outset,".indexOf(cut[i]) != -1) {
						container.querySelector('.borderStyle').value = cut[i];
					} else {
						container.querySelector('.borderWidth').value = cut[i];
					}
				}
				container.querySelector('.borderMarkers[data-targetcss="' + this.dataset.css + '"]').classList.add("modifiedBorder");
			} else {
				container.querySelector('.borderWidth').value = "";
				container.querySelector('.borderColor').value = "";
				container.querySelector('.borderStyle').value = "";
				container.querySelector('.borderMarkers[data-targetcss="' + this.dataset.css + '"]').classList.remove("modifiedBorder");
			}
		});

		/* Manage CSSpicker */
		$("#right_sidebar").on('click.creation', ".cssPickerBTN", function(e) {
			e.preventDefault();
			e.stopPropagation();
			$("#threed").show();
			function destroyCSSpicker() {
				$('#container', ParsimonyAdmin.currentBody).off(".csspicker");
				$("#rotatex,#rotatey").val(0);
				$("#rotatez").val(300);
				$(".cssPickerBTN").removeClass("active");
				$('.cssPicker', ParsimonyAdmin.currentDocument).removeClass('cssPicker');
			}
			if ($(this).hasClass("active")) {
				destroyCSSpicker();
				return false;
			}
			ParsimonyAdmin.closeParsiadminMenu();
			$('#container', ParsimonyAdmin.currentBody).on('mouseover.csspicker', "*", function(event) {
				event.stopPropagation();
				var elmt = ParsimonyAdmin.currentDocument.querySelector('.cssPicker');
				if (elmt)
					elmt.classList.remove("cssPicker");
				this.classList.add("cssPicker");
			});
			$(".cssPickerBTN").addClass("active");
			$('#container', ParsimonyAdmin.currentBody).on('click.csspicker', "*", function(e) {
				e.preventDefault();
				e.stopPropagation();

				/* Init/clean panel */
				$("#threed").hide();
				document.getElementById("currentMdq").value = "";
				document.getElementById("current_selector_update").value = "";
				document.getElementById("changecsspath").value = "";
				document.getElementById("panelcss").classList.remove("CSSSearchBTNS");
				document.getElementById("panelcss").classList.remove("CSSSearch");

				/* Clean iframe */
				ParsimonyAdmin.$currentBody.css('transform', 'initial').css('-webkit-transform', 'initial').removeClass("threed");
				$('.cssPicker', ParsimonyAdmin.currentBody).removeClass("cssPicker");
				$(this).addClass("cssPicker");
				$this.openCSSCode();
				var proposals = [];
				if (this.id.length > 0 && $(".selectorcss[selector='#" + this.id + "']", $("#changecsscode")).length == 0)
					proposals.push("#" + this.id);
				var selectProp = "";
				var forbidClasses = ",selection-block,block,container,selection-container,parsieditinline,cssPicker,";
				$.each(this.classList, function(index, value) {
					if ($(".selectorcss[selector='." + value + "']").length == 0 && forbidClasses.indexOf("," + value + ",") == "-1") {
						proposals.push("." + value);
						selectProp = "." + value + " ";
					}
				});
				var good = false;
				if (this.id == "") {
					$(this).parentsUntil("body").each(function() {
						if (!good) {
							var selectid = "";
							var selectclass = "";
							if (this.getAttribute('id') != undefined)
								selectid = "#" + this.getAttribute('id');
							else {
								$.each(this.classList, function(index, value) {
									if (forbidClasses.indexOf("," + value + ",") == "-1")
										selectclass = "." + value;
								});
							}
							selectProp = selectid + selectclass + " " + selectProp;
							if (selectid != "")
								good = true;
						}
					});
					selectProp = selectProp.replace(/\s+/g, " ");
					if ($(".selectorcss[selector='" + selectProp + "']", $("#changecsscode")).length == 0)
						proposals.push(selectProp);
				}

				$this.getCSSSelectorForElement(this, proposals);
				destroyCSSpicker();
				return false;
			});
		});

		/* Manage change CSS mode */
		$("#changecssformcode").on('click.creation', '#switchtovisuel, #switchtocode', function() {
			var currentSelector = document.getElementById("current_selector_update").value;
			if (currentSelector != "") {
				if (this.id == 'switchtovisuel') { /* switch to viual form */
					$this.openCSSForm();
				} else { /* switch to code mode */
					$this.openCSSCode();
				}
				$this.displayCSSConf(document.getElementById("changecsspath").value, currentSelector);
			}
		});


		/* CSSpicker 3D */
		$("#threed").on('change.creation', '.ch', function() {
			var requestAnim = window.requestAnimationFrame || window.mozRequestAnimationFrame;
			requestAnim(function() {
				var x = document.getElementById("rotatex").value;
				var y = document.getElementById("rotatey").value;
				var z = document.getElementById("rotatez").value;
				ParsimonyAdmin.currentBody.classList.add("threed");
				if(x >= 0){
					ParsimonyAdmin.currentBody.classList.remove("threedTop");
					ParsimonyAdmin.currentBody.classList.add("threedBottom");
				}else{
					ParsimonyAdmin.currentBody.classList.remove("threedBottom");
					ParsimonyAdmin.currentBody.classList.add("threedTop");
				}
				if(y >= 0){
					ParsimonyAdmin.currentBody.classList.remove("threedRight");
					ParsimonyAdmin.currentBody.classList.add("threedLeft");
				}else{
					ParsimonyAdmin.currentBody.classList.remove("threedLeft");
					ParsimonyAdmin.currentBody.classList.add("threedRight");
				}
				var style = 'rotateX(' + (x / 10) + 'deg) rotateY(' + (y / 10) + 'deg) translateZ(' + z + 'px)';
				if (typeof ParsimonyAdmin.currentBody.style.transform != "undefined") {
					ParsimonyAdmin.currentBody.style.transform = 'rotateX(' + x + 'deg) rotateY(' + y + 'deg) scale(0.9)';
					$this.iframeStyleSheet.deleteRule("0");
					$this.iframeStyleSheet.insertRule('.threed * {transform:' + style + ';transform-style: preserve-3d;background-color:#f9f9f9;}', "0");
				} else {
					ParsimonyAdmin.currentBody.style.webkitTransform = 'rotateX(' + x + 'deg) rotateY(' + y + 'deg) scale(0.9)';
					$this.iframeStyleSheet.deleteRule("0");
					$this.iframeStyleSheet.insertRule('.threed * {-webkit-transform:' + style + ';-webkit-transform-style: preserve-3d;background-color:#f9f9f9}', "0");
				}
			});
		});

		$this.iframeStyleSheet = ParsimonyAdmin.currentDocument.styleSheets[ParsimonyAdmin.currentDocument.styleSheets.length - 1];

		/* Shortcut : Save on CTRL+S */
		$(document).on("keydown.creation", function(e) {
			if (e.which === 83 && e.ctrlKey && document.querySelector(".panelcss.active") != null) {
				e.preventDefault();
				document.getElementById("savemycss").click();
			}
		});

	}

	this.unloadCreationMode = function() {
		/* reInit panel, when change page or version */
		document.getElementById("panelcss").classList.add("CSSSearch");
		document.getElementById("current_selector_update").value = '';
		/* If CSS changed but not saved, we reinit */
		if (document.getElementById("toolChanges").classList.contains("toolactive")) {
			ParsimonyAdmin.CSSValuesChanges = {};
			document.getElementById("reinitcss").click(); // place before unload event
		}

		$("#panelcss").off('.creation', "**");
		$("#colorjack_square").hide();
		$(document).add(ParsimonyAdmin.currentDocument).off(".parsimonyDND");
		$('#container', ParsimonyAdmin.currentBody).off(".csspicker");
		$(".parsimonyMove").off(".creation");
		$(".parsimonyResize").off(".creation");
		$("#changecssformcode").off(".creation");
		$("#right_sidebar").off(".creation");
		$("#mediaqueriesdisplay").off(".creation");
		$(".decoration").off(".creation");
		$("#panelcss_tab_background").off(".creation");
		$("#backTest").off(".creation");
		$("#threed").off(".creation");
		$(document).off(".creation");
		$("#parsimonyDND").hide();
	}
}

blockAdminCSS.prototype.codeEditors = [];

blockAdminCSS.prototype.setCss = function(rule, code) {
	rule.style.cssText = code;
}

blockAdminCSS.prototype.setCurrentRule = function(idStyle, idRule, idMedia) {
	if (idMedia == "") {
		this.currentRule = ParsimonyAdmin.currentDocument.styleSheets[idStyle].cssRules[idRule];
	} else {
		this.currentRule = ParsimonyAdmin.currentDocument.styleSheets[idStyle].cssRules[idMedia].cssRules[idRule];
	}
}

blockAdminCSS.prototype.checkChanges = function() {
	var cpt = 0;
	var list = "";
	for (var file in ParsimonyAdmin.CSSValuesChanges) {
		for (var key in ParsimonyAdmin.CSSValuesChanges[file]) {
			cpt++;
			media = ParsimonyAdmin.CSSValuesChanges[file][key].media;
			list += '<div class="selector tooltip" data-tooltip="' + file + (media ? " : " + media : "") + '">' + ParsimonyAdmin.CSSValuesChanges[file][key].selector + "</div>";
		}
	}
	document.getElementById("nbChanges").textContent = " " + cpt + " changes";
	document.getElementById("listchanges").innerHTML = list;
	if (cpt > 0)
		document.getElementById("toolChanges").classList.add('toolactive');
	else
		document.getElementById("toolChanges").classList.remove('toolactive');
}

blockAdminCSS.prototype.updatePosition = function(bounds) {
	var DNDadmin = document.getElementById("parsimonyDND");
	DNDadmin.style.cssText = "display:block;top:" + bounds.top + "px;left:" + (bounds.left + ParsimonyAdmin.iframe.offsetLeft +  + (ParsimonyAdmin.iframe.classList.contains("sized") ? 40 : 0)) + "px;width:" + bounds.width + "px;height:" + bounds.height + "px";
}

/* Keep CSS changes in an object */
blockAdminCSS.prototype.setCssChange = function(path, selector, value, media) {

	if (typeof ParsimonyAdmin.CSSValuesChanges[path] == "undefined")
		ParsimonyAdmin.CSSValuesChanges[path] = {};
	var key = media.replace(/\s+/g, '') + selector;
	/* Add this selector in changed selector list */
	if (typeof ParsimonyAdmin.CSSValuesChanges[path][key] == "undefined") {
		ParsimonyAdmin.CSSValuesChanges[path][key] = {"value": value, "selector": selector, "media": media}; // fix for count changes
		this.checkChanges();
		/* If this is the first time we use this media queri */
		if (media && document.querySelectorAll('#mediaqueriesdisplay .mediaq[data-media="' + media + '"]').length == 0) {
			this.addMediaQueries(document.getElementById("mdqMinWidthValue").value, document.getElementById("mdqMaxWidthValue").value);
		}
	} else {
		ParsimonyAdmin.CSSValuesChanges[path][key] = {"value": value, "selector": selector, "media": media}; // fix for count changes
	}
}

blockAdminCSS.prototype.getLastCSS = function(filePath, ident) {
	var code = "";
	if (typeof ParsimonyAdmin.CSSValuesChanges[filePath] != "undefined") {
		if (typeof ParsimonyAdmin.CSSValuesChanges[filePath][ident] != "undefined") {
			code = ParsimonyAdmin.CSSValuesChanges[filePath][ident].value.trim();
			return code; //if we already cleaned up do not try to go further
		}
	}
	if (code.length == 0 && typeof ParsimonyAdmin.CSSValues[filePath] != "undefined") {
		if (typeof ParsimonyAdmin.CSSValues[filePath][ident] != "undefined") {
			code = ParsimonyAdmin.CSSValues[filePath][ident].p.trim();
		}
	}
	return code;
}

blockAdminCSS.prototype.displayCSSConf = function(filePath, selector, media) {

	/* Media querie or not */
	if (typeof media == "undefined") {
		var media = document.getElementById("currentMdq").value;
	} else {
		document.getElementById("currentMdq").value = media;
		trigger(document.getElementById("currentMdq"), "change");
	}

	/* Clean form */
	document.getElementById("form_css").reset(); // reset all input except inputs hidden
	$(".modifiedBorder").removeClass("modifiedBorder"); // clean borders visual tools
	document.getElementById("backTest").style.background = "url(admin/img/transparent.png)"; // clean background
	$('.cssPicker', ParsimonyAdmin.currentDocument).removeClass('cssPicker'); // clean cssPicker marker
	document.getElementById("panelcss").classList.remove("CSSSearchBTNS");
	document.getElementById("panelcss").classList.remove("CSSSearch");

	/* Get existing code, last changes in priority */
	var code = this.getLastCSS(filePath, media.replace(/\s+/g, '') + selector);

	/* Init form */
	document.getElementById("current_selector_update").value = selector;
	document.getElementById("changecsspath").value = filePath;

	/* Init vars to locate CSS rule : this.currentIdStylesheet, this.currentIdRule, this.currentIdMedia */
	this.mapSelectorWithStylesheet(filePath, selector);

	if (document.getElementById("panelcss").classList.contains('CSSCode')) {
		this.openCSSCode();
		/* If code mode is enable */
		$("#changecsscode").empty();
		this.addSelectorCSS(filePath, selector, this.formatCSS(code), this.currentIdStylesheet, this.currentIdRule, this.currentMediaText, this.currentIdMedia);

	} else {
		this.openCSSForm();
		/* If visual mode is enable, we fill the visual CSS Form  */
		if (code.length > 0) {
			var properties = code.split(";");
			if (Array.isArray(properties)) {
				properties.forEach(function(item) {
					var properties = item.split(":");
					if (Array.isArray(properties) && properties.length == 2) {
						var elmt = document.querySelector(".prop_" + properties[0].trim());
						if (elmt) {
							elmt.value = properties[1].trim();
							trigger(elmt, "init");
						}
					}
				});
			} else {
				var property = code.split(":");
				if (Array.isArray(property) && property.length == 2) {
					var elmt = document.querySelector(".prop_" + property[0].trim());
					if (elmt) {
						elmt.value = property[1].trim();
						trigger(elmt, "init");
					}
				}
			}
		}
	}
	document.getElementById("panelcss").classList.remove('CSSSearch');
}

blockAdminCSS.prototype.mapSelectorWithStylesheet = function(path, selector) {
	/* Search for an existing cssRule to update it in the future */
	var matches = this.findSelectorsByElement(selector);
	var file = matches[path];

	/* By default */
	var media = document.getElementById("currentMdq").value;
	this.currentFile = path;
	this.currentIdStylesheet = 0;
	this.currentIdRule = ParsimonyAdmin.currentDocument.styleSheets[0].cssRules.length;
	this.currentIdMedia = "";
	this.currentMediaText = "";

	if (typeof file != "undefined" && typeof file.nbStylesheet != "undefined") {
		/* By default if we found the stylesheet of theme */
		this.currentIdStylesheet = file.nbStylesheet;
		this.currentIdRule = file.nbRules;
		var rule = file[media.replace(/\s+/g, '') + selector];
		if (typeof rule != "undefined") {
			/* If we found the good rule */
			this.currentIdStylesheet = rule.nbStylesheet;
			this.currentIdRule = rule.nbRule;
			this.currentIdMedia = rule.nbMedia;
			this.currentMediaText = rule.media;
			this.setCurrentRule(this.currentIdStylesheet, this.currentIdRule, this.currentIdMedia);
			return true;
		}
	}
	/* If CSS rule doesn't exists we create it, If it's a media we wrap rule with media declaration */

	if (media.length > 0) {
		ParsimonyAdmin.currentDocument.styleSheets[this.currentIdStylesheet].insertRule(media + " { " + selector + " { } }", this.currentIdRule);
		this.currentIdMedia = this.currentIdRule;
		this.currentMediaText = media;
		this.currentIdRule = 0;
	} else {
		ParsimonyAdmin.currentDocument.styleSheets[this.currentIdStylesheet].insertRule(selector + "{}", this.currentIdRule);
	}

	this.setCurrentRule(this.currentIdStylesheet, this.currentIdRule, this.currentIdMedia);
}

blockAdminCSS.prototype.findSelectorsByElement = function(elmt, mediaFilter) {
	document.getElementById("toolChanges").style.display = "block";
	var matchesSelector = (document.documentElement.webkitMatchesSelector || document.documentElement.mozMatchesSelector || document.documentElement.matchesSelector);
	var result = {};
	var styleSheets = ParsimonyAdmin.currentDocument.styleSheets;
	var url = "";
	for (var i = 0; i < styleSheets.length; i++) {
		if (styleSheets[i].cssRules !== null && styleSheets[i].href != null && !!styleSheets[i].href && styleSheets[i].href.indexOf("iframe.css") == "-1") {
			url = styleSheets[i].href;
			/* If file is not the concat file we clean the path */
			if (url.indexOf("/cssconcat_") == "-1") {
				url = url.replace("http://" + window.location.host, "").substring(BASE_PATH.length);
				result[url] = {"nbStylesheet": i, "nbRules": styleSheets[i].cssRules.length};
			}
			var nbRules = styleSheets[i].cssRules.length;
			for (var j = 0; j < nbRules; j++) {
				var media = "";
				var rule = styleSheets[i].cssRules[j];
				/* If it's a media rule we check each style rule */
				if (typeof rule.media != "undefined") {
					media = "@media " + rule.media.mediaText;

					/* Add this media queries in shorthand list */
					media2 = media.replace(/\s+/g, "");
					this.addMediaQueries((media2.match(/\min-width:[0-9]+/) || "").toString().replace("min-width:", ""),
							(media2.match(/\max-width:[0-9]+/) || "").toString().replace("max-width:", ""));

					for (var h = 0; h < rule.cssRules.length; h++) {
						mediaRule = styleSheets[i].cssRules[j].cssRules[h];
						try {
							if (typeof elmt == 'string' ? (elmt == mediaRule.selectorText) : matchesSelector.call(elmt, mediaRule.selectorText)) {
								/* if we search for a media we check */
								if (mediaFilter == media || typeof mediaFilter == "undefined") {
									result[url][media2 + mediaRule.selectorText] = {"nbStylesheet": i, "nbRule": h, "nbMedia": j, "selector": mediaRule.selectorText, "media": media};
								}
							}
						} catch (Error) {
						}
					}
				} else { /* if it's a style rule */
					/* If source map tell us we parse another CSS file */
					if (rule.selectorText == '.parsimonyMarker') {
						/* We set last id for CSS file just before*/
						if (j > 0) {
							result[url]["nbStylesheet"] = i;
							result[url]["nbRules"] = j;
						}
						/* We init the next CSS file */
						url = rule.style['backgroundImage'].replace(/"/g, "").split('url(')[1].replace("http://" + window.location.host + BASE_PATH, "").split(')')[0];
						result[url] = {"nbStylesheet": i, "nbRules": styleSheets[i].cssRules.length - 1};
					} else if (typeof mediaFilter == "undefined" || mediaFilter == "") {
						try {
							if (typeof elmt == 'string' ? (elmt == rule.selectorText) : matchesSelector.call(elmt, rule.selectorText)) {
								result[url][rule.selectorText] = {"nbStylesheet": i, "nbRule": j, "nbMedia": "", "selector": rule.selectorText, "media": ""};
							}
						} catch (Error) {
						}
					}
				}
			}
		}
	}
	return result;
}

blockAdminCSS.prototype.getCSSSelectorForElement = function(elmt, proposals) {
	var matches = this.findSelectorsByElement(elmt);
	var found = false;
	var delta = 0;
	var $this = this;
	var nbStylesheet, nbRules;
	for (var file in matches) {
		/* We delete all selectors of iframe.css and lib/_*_.css */
		if (file.indexOf("iframe.css") == "-1" && file.indexOf("lib/") == "-1") {
			var selectors = matches[file];
			for (var key2 in selectors) {
				if (key2 != 'nbStylesheet' && key2 != 'nbRules') {
					selectors[key2].filePath = file;
					var selector = selectors[key2];
					/* If a proposal already exists we remove it from proposal list */
					if (file == CSSTHEMEPATH && proposals.indexOf(selector) > -1)
						delete proposals[proposals.indefOf(selector)];
					$this.addSelectorCSS(file, selector.selector, "", selector.nbStylesheet, (selector.nbRule + delta), selector.media, selector.nbMedia);
					found = true;
				}
			}
			if (file == CSSTHEMEPATH) {
				nbStylesheet = selectors['nbStylesheet'];
				nbRules = selectors['nbRules'];

				/* Add proposals */
				if (proposals.length > 0) {
					for (var key in proposals) {
						nbRules++;
						$this.addSelectorCSS(CSSTHEMEPATH, proposals[key], "", nbStylesheet, nbRules, "", "");
						ParsimonyAdmin.currentDocument.styleSheets[nbStylesheet].insertRule(proposals[key] + "{}", nbRules);
						delta++;
					}
				}
			}
			delete selectors['nbStylesheet'];
			delete selectors['nbRules'];
		} else {
			delete matches[file];
		}
	}

	/* If a selector has been found, we get his cssText from server */
	if (found) {
		$.post(BASE_PATH + "admin/getCSSSelectorsRules", {TOKEN: ParsimonyAdmin.currentWindow.TOKEN,
			matches: matches
		}, function(data) {
			$.each(data, function(i, item) {
				/* We set cssText value to selector's textarea */
				var id = 'idcss' + item.nbStylesheet + "_" + item.nbRule + "_" + item.nbMedia;
				document.getElementById(id).value = item.cssText;
				$this.codeEditors[id].draw(false);
				// Add new selectors to ParsimonyAdmin.CSSValues to save initial behavior
				if (typeof ParsimonyAdmin.CSSValues[item.filePath] == "undefined")
					ParsimonyAdmin.CSSValues[item.filePath] = {};
				ParsimonyAdmin.CSSValues[item.filePath][item.media.replace(/\s+/g, "") + item.selector] = item.CSSValues;
			});
		});
	}
}

blockAdminCSS.prototype.addNewSelectorCSS = function(path, selector) {
	/* Get rule id, if not exist we create one */
	this.mapSelectorWithStylesheet(path, selector);

	var media = this.currentMediaText;
	if (media.length > 0) {
		var media = document.getElementById("currentMdq").value;
	}
	/* Get code */
	var code = this.currentRule.cssText.split("{")[1].split("}")[0];
	this.addSelectorCSS(path, selector, code, this.currentIdStylesheet, this.currentIdRule, media, this.currentIdMedia);
	this.setCss(this.currentRule, code);
}

blockAdminCSS.prototype.addSelectorCSS = function(url, selector, styleCSS, nbstyle, nbrule, media, nbmedia) {
	var id = 'idcss' + nbstyle + "_" + nbrule + "_" + nbmedia;
	var code = '<div class="selectorcss" title="' + url + '" selector="' + selector + '"><div class="selectorTitle"><b>' + selector + '</b> <small>in ' + url.replace(/^.*[\/\\]/g, '') + '</small></div><div class="gotoform" onclick="$(\'#panelcss\').removeClass(\'CSSCode\');Parsimony.blocks[\'admin_css\'].displayCSSConf(\'' + url + '\',\'' + selector + '\',\'' + (media || "") + '\')"> ' + t('Visual') + ' </div></div>';
	if (typeof media != "undefined" && media.toString().length > 0)
		code += '<div class="mediaQueriesTitle">' + media + '</div>';
	code += '<textarea class="csscode CSSLighttexta" id="' + id + '" spellcheck="false" name="selectors[' + id + '][code]" data-nbstyle="' + nbstyle + '" data-nbrule="' + nbrule + '" data-media="' + media + '" data-nbmedia="' + nbmedia + '" data-path="' + url + '" data-selector="' + encodeURIComponent(selector) + '">' + Parsimony.blocks['admin_css'].formatCSS(styleCSS) + '</textarea>';
	$("#changecsscode").append(code);
	this.codeEditors[id] = new CSSlight(document.getElementById(id));
}

blockAdminCSS.prototype.addMediaQueries = function(minWidth, maxWidth) {
	var media = "@media screen";
	if (minWidth.length > 0) {
		media += " and (min-width:" + minWidth + "px)";
	}
	if (maxWidth.length > 0) {
		media += " and (max-width:" + maxWidth + "px)";
	}
	if (media != "@media screen" && document.querySelectorAll('#mediaqueriesdisplay .mediaq[data-media="' + media + '"]').length == 0) {
		var doc = document.createElement("div");
		doc.dataset.min = minWidth || 0;
		doc.dataset.max = maxWidth || 9999;
		doc.dataset.media = media;
		doc.classList.add("mediaq");
		if (minWidth && maxWidth) {
			doc.style.left = minWidth + "px";
			doc.style.width = maxWidth - minWidth + "px";
		} else if (minWidth) {
			doc.style.left = minWidth + "px";
		} else if (maxWidth) {
			doc.style.left = "0";
			doc.style.width = maxWidth + "px";
		} else {
			doc.classList.add("active");
		}
		document.getElementById("mediaqueriesdisplay").insertBefore(doc, document.getElementById("globalcssscope"));
		this.drawMediaQueries();
	}
	return media;
}

blockAdminCSS.prototype.drawMediaQueries = function() {
	var size = document.getElementById("preview").getBoundingClientRect();
	document.getElementById("mediaqueriesdisplay").style.paddingLeft = size.left + 40 + "px";
	document.getElementById("scopeMediaQueries").style.width = size.width - 80 + "px";
	var width = size.width - 40;
	var arrow = width + size.left - 10;
	document.getElementById("arrow-down").style.left = arrow + "px";
	var mediaqs = document.querySelectorAll(".mediaq");
	width = width - 40;
	for (var i = 0, len = mediaqs.length; i < len; i++) {
		if (width >= mediaqs[i].dataset.min && width <= mediaqs[i].dataset.max) {
			mediaqs[i].classList.add("active");
		} else {
			mediaqs[i].classList.remove("active");
		}
	}
}

blockAdminCSS.prototype.formatCSS = function(css) {
	return css.replace(/\/\*.*\*\//g, "").replace(/;[^a-z-]*/g, ";\n").replace(/(^|\n)([^:]+:)[^a-z0-9-#'"]*/g, "$1$2 ");
}

blockAdminCSS.prototype.updateCSSText = function(oldCssText, property, newValue) {
	var newCSSText = "";
	
	if (oldCssText.length == 0)
		oldCssText = property + ":" + newValue + ";";

	/* We check if property is already set; must manage with : min-width and width props like  */
	if (oldCssText.match(new RegExp("(^|[^a-z-])" + property + "[^:]*:")) != null) {
		if (newValue.length > 0) {
			newCSSText = oldCssText.replace(new RegExp("(^|[^a-z-])(" + property + "[^;]*)"), "$1" + property + ": " + newValue);
		} else { /* if there is no value we delete property */
			newCSSText = oldCssText.replace(new RegExp("(^|[^a-z-])" + property + "[^;]*[;]?"), "$1");
		}
	} else {
		if (newValue.length > 0) {
			newCSSText = oldCssText + (oldCssText.substring(oldCssText.length - 1) == ";" ? "" : ";") + property + ": " + newValue + ";";
		} else {
			newCSSText = oldCssText;
		}
	}
	return newCSSText;
}

blockAdminCSS.prototype.openCSSForm = function() {
	var panel = document.getElementById("panelcss");
	panel.classList.remove('CSSCode');
	panel.classList.add('CSSForm');
	ParsimonyAdmin.setCookie("cssMode", "visual", 999);
}

blockAdminCSS.prototype.openCSSCode = function() {
	var panel = document.getElementById("panelcss");
	panel.classList.remove('CSSForm');
	panel.classList.add('CSSCode');
	$("#changecsscode").empty();
	ParsimonyAdmin.setCookie("cssMode", "code", 999);
}

blockAdminCSS.prototype.openCSSPanel = function() {
	ParsimonyAdmin.displayPanel("panelcss");
}


Parsimony.registerBlock("admin_css", new blockAdminCSS());


function CSSlight(elmt) {

	this.textarea = elmt;
	this.rm = [];

	var obj = document.createElement("div");
	obj.classList.add("CSSLightcontainer");
	var gutter = document.createElement("div");
	gutter.classList.add("CSSLightguttercss");
	this.hilight = document.createElement("div");
	this.hilight.classList.add("CSSLighthilight");

	obj = this.textarea.parentNode.insertBefore(obj, this.textarea);
	obj.appendChild(gutter);
	obj.appendChild(this.textarea);
	obj.appendChild(this.hilight);

	this.draw = function(change) {

		var content = this.textarea.value;
		var highlighted = '';
		var gutter = '<span class="CSSLightgutter"><span class="CSSLighthider"></span></span>';
		var line = 2;
		var search = "[:\n]";
		var error = false;
		while (content.length > 0) {
			var regex = new RegExp(search);
			var char = content.match(regex);
			if (char !== null) {
				switch (char[0]) {
					case "\n":
						var search = "[:\n]";
						var cont = content.substring(0, char.index).trim();
						if (cont.length > 0) {
							error = true;
							highlighted += '<span class="CSSLightother">' + content.substring(0, char.index) + '</span>';
						}
						highlighted += '</div><div class="CSSLightline' + (this.rm.indexOf(line.toString()) == "-1" ? "" : " CSSLightbarre") + '" data-line="' + line + '">' + gutter;
						line++;
						break;
					case ":":
						var search = "[;\n]";
						highlighted += '<span class="' + (error ? "CSSLightother" : "CSSLightproperty") + '">' + content.substring(0, char.index) + '</span><span class="CSSLighttwopoints">:</span>';
						break;
					case ";":
						var search = "[\n]";
						highlighted += '<span class="' + (error ? "CSSLightother" : "CSSLightvalue") + '">' + content.substring(0, char.index) + '</span><span class="CSSLightcoma">;</span>';
						break;
				}
				content = content.substring(char.index + 1);
			} else {
				highlighted += '<span class="CSSLightother">' + content.substring(0) + '</span>';
				content = "";
			}
		}
		line++;
		this.hilight.innerHTML = '<div class="CSSLightline' + (this.rm.indexOf("1") == "-1" ? "" : " CSSLightbarre") + '" data-line="1">' + gutter + highlighted + '</div>';
		this.textarea.style.height = this.hilight.getBoundingClientRect().height + "px";
		if (change != false) {
			/* Preview changes on CSS code mode && save changes in a temp object */
			var value = this.textarea.value;
			if (this.rm.length > 0) {
				var lines = value.split("\n");
				for (var key in this.rm) {
					if (typeof lines[key - 1] != "undefined")
						lines[key - 1] = "";
				}
				value = lines.join("\n");
			}

			Parsimony.blocks['admin_css'].setCss(Parsimony.blocks['admin_css'].currentRule, value);
			Parsimony.blocks['admin_css'].setCssChange(this.textarea.dataset.path, decodeURIComponent(this.textarea.dataset.selector), this.textarea.value, this.textarea.dataset.media);
		}
	}

	this.rePos = function() {
		this.hilight.scrollLeft = this.textarea.scrollLeft;
		this.hilight.scrollTop = this.textarea.scrollTop;
	}

	this.format = function(e) {
		e.preventDefault();
		var data = e.clipboardData.getData('text/plain') || "";
		document.execCommand('insertText', false, Parsimony.blocks['admin_css'].formatCSS(data));
	}

	this.draw(false);
	elmt.addEventListener("paste", this.format, false);
	elmt.addEventListener("input", this.draw.bind(this), true);
	elmt.addEventListener("scroll", this.rePos.bind(this), false);

};
