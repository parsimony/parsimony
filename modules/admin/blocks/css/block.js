function blockAdminCSS() {

	this.currentIdStylesheet = 0;
	this.currentIdRule = 0;
	this.currentIdMedia = 0;
	this.currentMediaText = "";
	this.currentRule;
	this.currentProperties = {};
	this.currentFile;


	this.initPreview = function() {
		document.getElementById("changecsspath").value = CSSTHEMEPATH;
	}
	
	this.loadCreationMode = function() {
		
		var $this = this;
		
		document.getElementById("panelcss").classList.add("CSSSearch");
		
		/* Init visual tool (drag 'n drop/resize/move block) */

		/* Manage Visual tool for Deag 'n drop */
		$(".parsimonyMove").on("mousedown.creation", function(e) {
			e.stopImmediatePropagation();
			ParsimonyAdmin.iframe.style.pointerEvents = "none";
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
				ParsimonyAdmin.iframe.style.pointerEvents = "all";
				$("#box_top").val(dndstart.rule.style.top !== 'auto' ? dndstart.rule.style.top : '').trigger("change");
				$("#box_left").val(dndstart.rule.style.left !== 'auto' ? dndstart.rule.style.left : '').trigger("change");
				$(document).add(ParsimonyAdmin.currentDocument).off("mousemove").off("mouseup");
				// $this.checkChanges();
			});
		});

		/* Manage Visual tool for Resize */
		$(".parsimonyResize").on("mousedown.creation", function(e) {
			e.stopImmediatePropagation();
			ParsimonyAdmin.iframe.style.pointerEvents = "none";
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

			$(document).on("mousemove.parsimonyDND", dndstart, function(e) {
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
				ParsimonyAdmin.iframe.style.pointerEvents = "all";
				$("#box_width").val(dndstart.rule.style.width !== 'auto' ? dndstart.rule.style.width : '').trigger("change");
				$("#box_height").val(dndstart.rule.style.height !== 'auto' ? dndstart.rule.style.height : '').trigger("change");
				$("#box_top").val(dndstart.rule.style.top !== 'auto' ? dndstart.rule.style.top : '').trigger("change");
				$("#box_left").val(dndstart.rule.style.left !== 'auto' ? dndstart.rule.style.left : '').trigger("change");
				$(document).off("mousemove").off("mouseup");
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
				line.classList.remove("CSSLightbarre");
				delete $this.codeEditors[id].rm[lineNb];
			} else {
				line.classList.add("CSSLightbarre");
				$this.codeEditors[id].rm[lineNb] = lineNb;
			}
			$this.codeEditors[id].draw();
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
						if (typeof ParsimonyAdmin.CSSValues[file] == "undefined") {
							ParsimonyAdmin.CSSValues[file] = {};
						}
						ParsimonyAdmin.CSSValues[file][key] = {s:ParsimonyAdmin.CSSValuesChanges[file][key].selector,p:ParsimonyAdmin.CSSValuesChanges[file][key].value};
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
					var selectorName = selector.media + selector.selector;
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
			$this.updatePosition(ParsimonyAdmin.currentDocument.getElementById(ParsimonyAdmin.inProgress).getBoundingClientRect());
			$this.displayCSSConf(document.getElementById("changecsspath").value, document.getElementById("current_selector_update").value);
			
		})

		/* Build media query syntax */
		.on("change.creation", "#currentMdq", function() {
			var option = document.querySelector('.mdqOption[data-media="' + this.value + '"]');
			if(option){
				option.click();
			}
			ParsimonyAdmin.setCookie("currentMdq", this.value, 999);
		})

		/* Manage CSS updates with visual forms */
		.on("keyup.creation change.creation", ".liveconfig", function() {

			/* Set style in current CSS rule */
			$this.currentRule.style[this.dataset.js] = this.value;

			/* Update position of visual tool */
			$this.updatePosition(ParsimonyAdmin.currentDocument.getElementById(ParsimonyAdmin.inProgress).getBoundingClientRect());

			if (this.value.length > 0) {
				$this.currentProperties[this.dataset.css] = this.value;
			} else {
				delete $this.currentProperties[this.dataset.css];
			}
			var updatedCode = $this.getCSSRulesUpdated();
			/* Save changes, we update from our old CSS string ( from CSSValuesChanges obj ) because shorthand propeties are exploded in CSS rules ( background => background-color..etc */
			$this.setCssChange($this.currentFile, document.getElementById("current_selector_update").value, updatedCode, $this.currentMediaText);
			
			/* Update tab code*/
			document.querySelector("textarea").value = updatedCode;
		})

		/* Explorer */
		.on("click.creation", ".explorer", function() {
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
		.on("click.creation", "#css_menu .cssTab", function() {
			document.querySelector("#css_menu .cssTab.active").classList.remove("active");
			this.classList.add("active");
			$(".panelcss_tab").addClass("hiddenTab");
			var rel = this.getAttribute("rel");
			document.getElementById(rel).classList.remove("hiddenTab");
			if (rel == "panelcss_tab_code") {
				document.getElementById("form_css").classList.remove("listOnly");
				for (var id in $this.codeEditors) {
					$this.codeEditors[id].draw(false);
				}
			} else {
				document.getElementById("form_css").classList.add("listOnly");
			}
		})

		.on("keypress.creation", "#current_selector_update", function(e) {
			if (e.which === 13 && this.value.length > 1) { // if [enter]
				$this.displayCSSConf(document.getElementById("changecsspath").value, this.value, document.getElementById("currentMdq").value);
				e.preventDefault(); // to avoid submit form and save css
			}
		})
		.on("click.creation", "#goWithThisSelector", function(e) {
			$this.displayCSSConf(document.getElementById("changecsspath").value, document.getElementById("current_selector_update").value, document.getElementById("currentMdq").value);
		})

		.on('keyup.creation', "#current_selector_update", function(event) {
			event.stopPropagation();
			if (event.which !== 13 && event.type === "keyup") { // if not [enter]
				try { /* In case of bad selector */
					$(".cssPicker", ParsimonyAdmin.currentDocument).removeClass("cssPicker");
					$(this.value, ParsimonyAdmin.currentDocument).addClass("cssPicker");
					$this.highlightBlock();
				} catch (E) {}
				document.getElementById("panelcss").classList.add("CSSSearch");
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
			var offset = currentColorPicker[0].getBoundingClientRect()
			picker.el.style.top = (offset.top) + 20 + "px";
			picker.el.style.left = (offset.left - 150) + "px";
		})
		.on('blur.creation', '.colorpicker2', function() {
			picker.el.style.display = "none";
		})

		/* Representation schemas */
		.on('keyup.creation change.creation', '.representation input:not(".resultcss")', function(e) {
			obj = this.parentNode;
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
			obj = this.parentNode;
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
			var options = "";
			if (this.id == "current_selector_update") {
				var data = ParsimonyAdmin.CSSValues[CSSTHEMEPATH];
				for(var i in data) {
					options += '<option>' + data[i].s + '</option>';
				}
			} else {
				var data = JSON.parse(this.dataset.options);
				for(var i in data) {
					options += '<option>' + data[i] + '</option>';
				}
			}
			
			document.getElementById("parsidatalist").innerHTML = options;
			this.setAttribute("list", "parsidatalist");
		})

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

		.on("click.creation", "#addMDQ", function() {
			var minWidth = document.getElementById("mdqMinWidthValue").value;
			var maxWidth = document.getElementById("mdqMaxWidthValue").value;
			var mediaComp = [];
			if(maxWidth.length > 0) {
				  mediaComp.push("(max-width: " +  maxWidth + "px)");
			}
			if(minWidth.length > 0) {
				  mediaComp.push("(min-width: " +  minWidth + "px)");
			}
			$this.addMediaQueries("@media " + mediaComp.join(" and "));
			document.getElementById("formAddMedia").style.display = 'none';
			document.getElementById("mdqMinWidthValue").value = "";
			document.getElementById("mdqMaxWidthValue").value = "";
		})
		.on("click.creation", "#rmvMDQ", function() {
			document.getElementById("formAddMedia").style.display = 'none';
		})
		.on("click.creation", "#selectmedias .mdqOption", function() {
			this.parentNode.classList.add("none");
			document.getElementById("mediaselected").innerHTML = "Media query: " + this.innerHTML;
			document.getElementById("currentMdq").value = this.dataset.media;
			trigger(document.getElementById("currentMdq"), "change");
			if(document.getElementById("current_selector_update").value.length > 0){
				$this.displayCSSConf( document.getElementById("changecsspath").value, document.getElementById("current_selector_update").value, this.dataset.media);
			}
		})
			
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
		.on("change.creation  init.creation", ".prop_text-decoration", function() {
			var container = this.parentNode;
			var value = this.value.replace(/[\s]{2,}/g, ' ');
			var values = value.trim().split(" ");
			$(".active", container).each(function() {
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
		.on("change.creation init.creation", ".prop_font-weight, .prop_font-style", function() {
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
		.on("change.creation init.creation", ".prop_text-align", function() {
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
				back_im = " url(admin/img/transparent.png) ";
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
			var backColorElmt = document.querySelector(".prop_background-color");
			backColorElmt.value = (parse.backgroundColor && parse.backgroundColor != "initial" ? rgbToHex(parse.backgroundColor) : "");
			backColorElmt.nextElementSibling.style.background = backColorElmt.value;
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
			document.getElementById("previewContainer").style.pointerEvents = "all";
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
				document.getElementById("previewContainer").style.pointerEvents = "none";
				$(document).off("mousemove").off("mouseup");
			});
		});

		/* shadowWidget init*/
		$(".shadowWidget").on("change.creation", ".rulePart", function(e) {
			var container = e.delegateTarget;
			var el = container.querySelector(".resultShadow");
			el.value = container.querySelector('.h-offset').value.trim() + ' ' + container.querySelector('.v-offset').value.trim() + ' ' + container.querySelector('.blurShadow').value.trim() + ' ' + container.querySelector('.colorShadow').value.trim();
			trigger(el, "change");
			container.querySelector(".pointer").style.left = parseInt(container.querySelector('.h-offset').value) + 16 + "px";
			container.querySelector(".pointer").style.top = parseInt(container.querySelector('.v-offset').value) + 16 + "px";
		})
		.on("change.creation  init.creation", ".resultShadow", function(e) {
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
			document.getElementById("previewContainer").style.pointerEvents = "all";
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
				document.getElementById("previewContainer").style.pointerEvents = "none";
				$(document).off("mousemove").off("mouseup");
			});
		});

		/* Borders */
		$('#panelcss_tab_border_general').on("click.creation", ".borderMarkers", function(e) {
			$(".active", e.delegateTarget).removeClass("active");
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
		.on("change.creation init.creation", ".liveconfig", function(e) {
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
		$("#right_sidebar").on("click.creation", ".cssPickerBTN", function(e) {
			e.stopPropagation();
			/* init */
			document.getElementById("threed").style.display = "block";
			document.getElementById("linkedRules").innerHTML = "";
			document.getElementById("current_selector_update").value = "";
			document.getElementById("blockDetect").style.display = "none";
			document.getElementById("panelcss").classList.add("CSSSearch");
			ParsimonyAdmin.closeParsiadminMenu();
			
			function destroyCSSpicker() {
				$("#container", ParsimonyAdmin.currentBody).off(".csspicker");
				$("#rotatex,#rotatey").val(0);
				$("#rotatez").val(300);
				$(".cssPickerBTN").removeClass("active");
				$(".cssPicker", ParsimonyAdmin.currentDocument).removeClass("cssPicker");
				ParsimonyAdmin.$currentBody.css('transform', 'initial').css('-webkit-transform', 'initial').removeClass("threed");
			}
			if (this.classList.contains("active")) {
				destroyCSSpicker();
				return false;
			}
			
			$(".cssPickerBTN").addClass("active");
			
			$("#container", ParsimonyAdmin.currentBody).on("mouseover.csspicker", "*", function(event) {
				event.stopPropagation();
				var elmt = ParsimonyAdmin.currentDocument.querySelector('.cssPicker');
				if (elmt)
					elmt.classList.remove("cssPicker");
				this.classList.add("cssPicker");
			});

			$("#container", ParsimonyAdmin.currentBody).on("click.csspicker", "*", function(e) {
				e.preventDefault();
				e.stopPropagation();

				/* Init */
				document.getElementById("threed").style.display = "none";
				document.getElementById("panelcss").classList.remove("CSSSearch");
				destroyCSSpicker();
				
				/* search for selectors and sort them by specificity */
				var proposals = [];

				/* If element has an ID we choose it immediatly */
				if (this.id.length > 0) {
					proposals[1000] = "#" + this.id;
				}

				/* classes */
				var forbidClasses = ",selection-block,parsiblock,parsieditinline,cssPicker,";
				for (var i = 0, len = this.classList.length; i < len; i++) {
					if (proposals.indexOf(this.classList[i]) == "-1" && forbidClasses.indexOf("," + this.classList[i] + ",") == "-1") {
						proposals[$this.getSpecificity("." + this.classList[i]) + proposals.length] = "." + this.classList[i];
					}
				}

				/* search for parent block */
				var block = this;
				while (!block.classList.contains("parsiblock")) {
					block = block.parentNode;
				}

				/* search for predefined block selectors */
				var matchesSelector = (document.documentElement.webkitMatchesSelector || document.documentElement.mozMatchesSelector || document.documentElement.matchesSelector);
				var stylableElements = ParsimonyAdmin.stylableElements[block.classList[1]];
				for(var i in stylableElements) {
					var selector = "#" + block.id + " " + stylableElements[i];
					if (matchesSelector.call(this, selector)) {
						proposals[$this.getSpecificity(selector) + proposals.length] = selector;
					}
				}
				
				/* if there is no propasol we add the element selector */
				if(proposals.length == 0){
					proposals[101] = "#" + block.id + " " + this.tagName.toLowerCase();
				}
				
				$this.getCSSSelectorForElement(this, null, proposals);

			});
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

		$this.iframeStyleSheet = ParsimonyAdmin.currentDocument.styleSheets[0];

		/* Shortcut : Save on CTRL+S */
		$(document).on("keydown.creation", function(e) {
			if (e.which === 83 && e.ctrlKey && document.querySelector(".panelcss.active") != null) {
				e.preventDefault();
				document.getElementById("savemycss").click();
			}
		});

		var usedMedia = ParsimonyAdmin.getCookie("currentMdq");
		if(usedMedia) {
			this.addMediaQueries(usedMedia);
			document.querySelector('.mdqOption[data-media="' + usedMedia + '"]').click();
		}

	}

	this.unloadCreationMode = function() {
		/* reInit panel, when change page or version */
		/*document.getElementById("panelcss").classList.add("CSSSearch");
		document.getElementById("current_selector_update").value = '';*/
		
		/* If CSS changed but not saved, we reinit */
		/*if (document.getElementById("toolChanges").classList.contains("toolactive")) {
			ParsimonyAdmin.CSSValuesChanges = {};
			document.getElementById("reinitcss").click(); // place before unload event
			ParsimonyAdmin.inProgress = '';
		}*/
		
		$("#panelcss").off(".creation", "**");
		$("#colorjack_square").hide();
		$(document).add(ParsimonyAdmin.currentDocument).off(".parsimonyDND");
		$("#container", ParsimonyAdmin.currentBody).off(".csspicker");
		$(".parsimonyMove").off(".creation");
		$(".parsimonyResize").off(".creation");
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

blockAdminCSS.prototype.highlightBlock = function() {
	var selector = document.getElementById("current_selector_update").value;
	if(selector[0] == "#") { /* test if the selector begin by a block */
		var block = ParsimonyAdmin.currentDocument.querySelector(selector.split(" ")[0] + ".parsiblock");
		if(block) {
			document.getElementById("blockDetectId").textContent = selector.split(" ")[0];
			document.getElementById("blockDetect").style.display = "block";
			/* Provide selectors proposals */
			var CSSProps = '<div>Selectors for block ' + block.getAttribute("is").split("-")[1] + '</div>';
			var stylableElements = ParsimonyAdmin.stylableElements[block.classList[1]];
			if (typeof stylableElements == "object") {
				for(var title in stylableElements) {
					var value = stylableElements[title];
					CSSProps += '<a href="#" data-selector="#' + block.id + ' ' + value + '">' + ' ' + t(title) + '<div>' + value + '</div></a>';
				}
			}
			document.getElementById("blockDetectSelectors").innerHTML = CSSProps;
		} else {
			document.getElementById("blockDetect").style.display = "none";
		}
	} else {
		document.getElementById("blockDetect").style.display = "none";
	}
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
	document.getElementById("nbChanges").textContent = " " + cpt + " change" + (cpt > 1 ? "s" : "");
	document.getElementById("listchanges").innerHTML = list;
	if (cpt > 0)
		document.getElementById("toolChanges").classList.add('toolactive');
	else
		document.getElementById("toolChanges").classList.remove('toolactive');
}

blockAdminCSS.prototype.updatePosition = function(bounds) {
	document.getElementById("parsimonyDND").style.cssText = "display:block;top:" + bounds.top + "px;left:" + bounds.left + "px;width:" + bounds.width + "px;height:" + bounds.height + "px";
}

/* Save CSS changes in an object */
blockAdminCSS.prototype.setCssChange = function(path, selector, value, media) {

	if (typeof ParsimonyAdmin.CSSValuesChanges[path] == "undefined")
		ParsimonyAdmin.CSSValuesChanges[path] = {};
	var key = media + selector;
	/* Add this selector in changed selector list */
	if (typeof ParsimonyAdmin.CSSValuesChanges[path][key] == "undefined") {
		ParsimonyAdmin.CSSValuesChanges[path][key] = {"value": value, "selector": selector, "media": media}; // fix for count changes
		this.checkChanges();
		/* If this is the first time we use this media query */
		if (media && document.querySelectorAll('#mediaqueriesdisplay .mediaq[data-media="' + media + '"]').length == 0) {
			this.addMediaQueries(media);
		}
	} else {
		ParsimonyAdmin.CSSValuesChanges[path][key] = {"value": value, "selector": selector, "media": media}; // fix for count changes
	}
}

/* Get lastests rules of a selector with real source as origin( not browser which remove css rules that doesn't recognize ) */
blockAdminCSS.prototype.getLastCSS = function(filePath, ident) {
	var code = "";
	
	/* If selector has already been updated */
	if (typeof ParsimonyAdmin.CSSValuesChanges[filePath] != "undefined") {
		if (typeof ParsimonyAdmin.CSSValuesChanges[filePath][ident] != "undefined") {
			code = ParsimonyAdmin.CSSValuesChanges[filePath][ident].value.trim();
			return code; //if we already cleaned up do not try to go further
		}
	}
	
	/* If not, we take css rules from the origin */
	if (typeof ParsimonyAdmin.CSSValues[filePath] != "undefined") {
		if (typeof ParsimonyAdmin.CSSValues[filePath][ident] != "undefined") {
			code = ParsimonyAdmin.CSSValues[filePath][ident].p.trim();
		}
	}
	return code;
}

blockAdminCSS.prototype.displayCSSConf = function(filePath, selector, media) {
	
	/* Clean form */
	document.getElementById("form_css").reset(); // reset all input except inputs hidden
	$(".panelcss_tab .modifiedBorder").removeClass("modifiedBorder"); // clean borders visual tools
	$(".panelcss_tab .active").removeClass("active"); // clean borders visual tools
	$(".colorpicker3").removeAttr("style"); // clean colorpicker feedback
	document.getElementById("backTest").style.background = "url(admin/img/transparent.png)"; // clean background
	$(".cssPicker", ParsimonyAdmin.currentDocument).removeClass("cssPicker"); // clean cssPicker marker
	document.getElementById("panelcss").classList.remove("CSSSearch");

	/* Init vars to locate CSS rule : this.currentIdStylesheet, this.currentIdRule, this.currentIdMedia,.. */
	var proposal = new Array();
	proposal[10000] = selector;
	this.getCSSSelectorForElement(selector, media, proposal);

}

blockAdminCSS.prototype.fillVisualCssForm = function(properties) {
	for(var property in properties){
		var elmt = document.querySelector(".prop_" + property);
		if (elmt) {
			elmt.value = properties[property].trim();
			trigger(elmt, "init");
		}
	}
}

blockAdminCSS.prototype.extractCSSRules = function(code) {
	var properties = {};
	var cutProperties = code.split(";");
	for( var i = 0, len = cutProperties.length; i < len; i++) {
		var cutProperty = cutProperties[i].split(":");
		var property = cutProperty[0].trim();
		if(property.length > 0 && typeof cutProperty[1] != "undefined") {
			properties[property] = cutProperty[1].trim();
		}
	}
	return properties;
}

blockAdminCSS.prototype.getCSSRulesUpdated = function() {
	var code = "";
	for(var property in this.currentProperties){
		code += property + ": " + this.currentProperties[property] + ";\n";
	}
	return code;
}

blockAdminCSS.prototype.findSelectorsByElement = function(elmt, mediaFilter) {
	
	var matchesSelector = (document.documentElement.webkitMatchesSelector || document.documentElement.mozMatchesSelector || document.documentElement.matchesSelector);
	var result = {};
	var styleSheets = ParsimonyAdmin.currentDocument.styleSheets;

	for (var i = 0, len = styleSheets.length; i < len; i++) {
		if (styleSheets[i].cssRules !== null && styleSheets[i].href != null && !!styleSheets[i].href && styleSheets[i].href.indexOf("iframe.css") == "-1") {
			var url = styleSheets[i].href;
			
			/* If file is not the concat file we clean the path */
			if (url.indexOf("/cssconcat_") == "-1") {
				url = url.replace("http://" + window.location.host, "").substring(BASE_PATH.length);
				result[url] = {"nbStylesheet": i, "nbRules": styleSheets[i].cssRules.length};
			}

			for (var j = 0, nbRules = styleSheets[i].cssRules.length; j < nbRules; j++) {
				
				var media = "";
				var rule = styleSheets[i].cssRules[j];
				switch (rule.type) {
					
					case 1: //CSSStyleRule
						/* If source map tell us we parse another CSS file */
						if (rule.selectorText == ".parsimonyMarker") {
							/* We set last id for CSS file just before */
							if (j > 0) {
								result[url]["nbStylesheet"] = i;
								result[url]["nbRules"] = j;
							}
							/* We init the next CSS file */
							url = rule.style["backgroundImage"].replace(/"/g, "").split("url(")[1].replace("http://" + window.location.host + BASE_PATH, "").split(")")[0];
							result[url] = {"nbStylesheet": i, "nbRules": styleSheets[i].cssRules.length - 1};
						} else if (typeof mediaFilter == "undefined" || mediaFilter == "") {
							try {
								if (typeof elmt == "string" ? (elmt == rule.selectorText) : matchesSelector.call(elmt, rule.selectorText)) {
									result[url][rule.selectorText] = {"nbStylesheet": i, "nbRule": j, "nbMedia": "", "selector": rule.selectorText, "media": ""};
								}
							} catch (Error) { }
						}
						break;
						
					case 4: //CSSMediaRule
						media = "@media " + rule.media.mediaText;

						/* Add this media queries in shorthand list */
						this.addMediaQueries(media);

						for (var h = 0, nbMediaRules = rule.cssRules.length; h < nbMediaRules; h++) {
							mediaRule = styleSheets[i].cssRules[j].cssRules[h];
							try {
								if (typeof elmt == "string" ? (elmt == mediaRule.selectorText) : matchesSelector.call(elmt, mediaRule.selectorText)) {
									/* if we search for a media we check */
									if (mediaFilter == media || typeof mediaFilter == "undefined") {
										result[url][media + mediaRule.selectorText] = {"nbStylesheet": i, "nbRule": h, "nbMedia": j, "selector": mediaRule.selectorText, "media": media};
									}
								}
							} catch (Error) { }
						}
						break;
				}
			}
		}
	}
	/* Always have to return the CSSTHEMEPATH properties, create it if it doesn't exists */
	if(typeof result[CSSTHEMEPATH] == "undefined") {
		ParsimonyAdmin.currentDocument.styleSheets[0].insertRule(".parsimonyMarker{background-image: url(" + CSSTHEMEPATH + ") }", 0);
		result[CSSTHEMEPATH] = {"nbStylesheet": 0, "nbRules": 1};
	}
	return result;
}

blockAdminCSS.prototype.getCSSSelectorForElement = function(node, media, proposals) {
	if(typeof proposals == "undefined"){
		proposals = [];
	}

	/* Media querie given or not */
	if (typeof media == "undefined" || media == null) {
		var media = document.getElementById("currentMdq").value;
	} else {
		document.getElementById("currentMdq").value = media;
		trigger(document.getElementById("currentMdq"), "change");
	}
	var linkedRules = [];
	var linkedRulesNb = 1;
	var nbStylesheet, nbRules;
	
	/* If at least one element is visible we will search for selectors with it to also have selectors that match this element */
	var search = node;
	if(typeof node == "string") {
		var visibleElmts = ParsimonyAdmin.currentDocument.querySelectorAll(node);
		if(visibleElmts.length > 0) {
			search = visibleElmts[0];
		}
	}
	var matches = this.findSelectorsByElement(search);

	for (var file in matches) {
		/* We delete all selectors of iframe.css and lib/_*_.css */
		if (file.indexOf("iframe.css") == "-1" && file.indexOf("lib/") == "-1") {
			var selectors = matches[file];
			for (var key2 in selectors) {
				if (key2 != "nbStylesheet" && key2 != "nbRules") {
					var rule = selectors[key2];
					/* If a proposal already exists we remove it from proposal list */
					if (file == CSSTHEMEPATH && proposals.indexOf(rule.selector) > -1 && rule.media === media){
						delete proposals[proposals.indexOf(rule.selector)];
					}
					if(typeof node == "string" && rule.selector == node && rule.media == media) {
						var nb = 20000 + linkedRulesNb;
						linkedRules[nb] = [file, rule.selector, this.getLastCSS(file, rule.media + rule.selector), rule.nbStylesheet, rule.nbRule, rule.media, rule.nbMedia];
					} else {
						linkedRules[this.getSpecificity(rule.selector) + linkedRulesNb] = [file, rule.selector, this.getLastCSS(file, rule.media + rule.selector), rule.nbStylesheet, rule.nbRule, rule.media, rule.nbMedia];
					}
					linkedRulesNb++;
				}
			}
		}
		if (file == CSSTHEMEPATH) {
			nbStylesheet = selectors["nbStylesheet"]; /* init and fill values in case we ahve to create a selector in this file */ 
			nbRules = selectors["nbRules"];
		}
	}
	
	/* Add proposals */
	if(proposals.length > 0) {
		for (var key in proposals) {
			if (media.length > 0) {
				ParsimonyAdmin.currentDocument.styleSheets[nbStylesheet].insertRule(media + " { " + proposals[key] + " { } }", nbRules);
				linkedRules[this.getSpecificity(proposals[key]) + linkedRulesNb] = [CSSTHEMEPATH, proposals[key], "", nbStylesheet, 0, media, nbRules];
			} else {
				ParsimonyAdmin.currentDocument.styleSheets[nbStylesheet].insertRule(proposals[key] + "{}", nbRules);
				linkedRules[this.getSpecificity(proposals[key]) + linkedRulesNb] = [CSSTHEMEPATH, proposals[key], "", nbStylesheet, nbRules, "", ""];
			}
			nbRules++;
			linkedRulesNb++;
		}
	}

	/* ------ Add main selector in CSS panel ----- */
	var bestSpecificity = linkedRules[linkedRules.length -1];
	this.currentFile = bestSpecificity[0];
	this.currentIdStylesheet = bestSpecificity[3];
	this.currentIdRule = bestSpecificity[4];
	this.currentIdMedia = bestSpecificity[6];
	this.currentMediaText = bestSpecificity[5];
	document.getElementById("current_selector_update").value = bestSpecificity[1];

	this.setCurrentRule(this.currentIdStylesheet, this.currentIdRule, this.currentIdMedia);
	
	/* ------ Init form ----- */
	document.getElementById("changecsspath").value = this.currentFile;
	document.getElementById("panelcss_tab_code").innerHTML = "";
	document.getElementById("linkedRules").innerHTML = "";
	this.addSelectorCSS(this.currentFile, document.getElementById("current_selector_update").value, this.getLastCSS(this.currentFile, this.currentMediaText + document.getElementById("current_selector_update").value), this.currentIdStylesheet, this.currentIdRule, this.currentMediaText, this.currentIdMedia);

	/* Get existing code, last changes in priority */
	var code = this.getLastCSS(this.currentFile, media + document.getElementById("current_selector_update").value);
	this.currentProperties = this.extractCSSRules(code);

	/* We fill the visual CSS Form  */
	this.fillVisualCssForm(this.currentProperties); 
		
	this.highlightBlock();
	delete linkedRules[linkedRules.length-1];
	

	/* ------- Add linked selectors in CSS panel ----- */
	for (var i in linkedRules) {
		this.addSelectorCSS.apply(this, linkedRules[i]);
	}
}

blockAdminCSS.prototype.addSelectorCSS = function(url, selector, styleCSS, nbstyle, nbrule, media, nbmedia) {
	var id = 'idcss' + nbstyle + "_" + nbrule + "_" + nbmedia;
	var code = '<div class="selectorcss" title="' + url + '" selector="' + selector + '"><div class="selectorTitle"><b>' + selector + '</b> <small>in ' + url.replace(/^.*[\/\\]/g, '') + '</small></div><div class="gotoform" onclick="Parsimony.blocks[\'admin_css\'].displayCSSConf(\'' + url + '\',\'' + selector + '\',\'' + (media || "") + '\')"> ' + t('Edit') + ' </div></div>';
	if (typeof media != "undefined" && media.toString().length > 0)
		code += '<div class="mediaQueriesTitle">' + media + '</div>';
	code += '<textarea class="csscode CSSLighttexta" id="' + id + '" spellcheck="false" name="selectors[' + id + '][code]" data-nbstyle="' + nbstyle + '" data-nbrule="' + nbrule + '" data-media="' + media + '" data-nbmedia="' + nbmedia + '" data-path="' + url + '" data-selector="' + encodeURIComponent(selector) + '">' + Parsimony.blocks['admin_css'].formatCSS(styleCSS) + '</textarea>';
	if (document.getElementById("current_selector_update").value == selector && media == this.currentMediaText) {
		$("#panelcss_tab_code").prepend(code);
	} else {
		$("#linkedRules").prepend(code);
	}

	this.codeEditors[id] = new CSSlight(document.getElementById(id));
}

blockAdminCSS.prototype.getSpecificity = function(selector) {
	var count = 0;
	
	/* Clean */
	selector = selector.replace("+", " ");
	selector = selector.replace("~", " ");
	selector = selector.replace(/\s+/g, " ");
	
	/* Count IDs */
	count += (selector.split("#").length - 1) * 100;
	
	/* Count classes */
	count += (selector.split(".").length - 1) * 10;
	
	selector = selector.replace("::", " ");
	
	/* Count pseudo-classes */
	count += (selector.split(":").length - 1) * 10;
	
	/* Count attributs */
	if(selector.indexOf("[")) {
		count += (selector.split("[").length - 1) * 10;
		selector = selector.replace(/\[(.*?)\]/g, "");
	}
	/* Count elements and pseudo-element */
	var elmts = (" " + selector).match(/\s[-\w]+/g);
	if(elmts){
		count += elmts.length;
	}
	
	return count * 10;
}

blockAdminCSS.prototype.addMediaQueries = function(media) {	
	if (document.querySelectorAll('#mediaqueriesdisplay .mediaq[data-media="' + media + '"]').length == 0) {

		/* get media type */
		var cutMediaType = media.match(/@media\s+([^\s\(]+)/);
		if(cutMediaType != null) {
			var mediaType = cutMediaType[1];
		} else {
			var mediaType = "all";
		}
		
		/* get media properties */
		var properties = {};
		var titleMedia = "";
		var cutMedia = media.match(/\([^\)]+/g);
		if(cutMedia != null) { /* ie: @media not all */
			for( var i = 0, len = cutMedia.length; i < len; i++){
				var cutProperty = cutMedia[i].split(":");
				var key = cutProperty[0].trim().substring(1);
				var value = cutProperty[1].trim();
				properties[key] = value;
				titleMedia += "<br><span>&#746;</span>" + key + ": " + value;
			}
		}
		

		/* For mediaqueries toolbar */
		var doc = document.createElement("div"); 
		doc.dataset.min = properties["min-width"] || 0;
		doc.dataset.max = properties["max-width"] || 9999;
		doc.dataset.media = media;
		doc.classList.add("mediaq");
		if (properties["min-width"] && properties["max-width"]) {
			doc.style.left = properties["min-width"];
			doc.style.width = parseInt(properties["max-width"]) - parseInt(properties["min-width"]) + "px";
		} else if (properties["min-width"]) {
			doc.style.left = properties["min-width"];
		} else if (properties["max-width"]) {
			doc.style.left = "0";
			doc.style.width = properties["max-width"];
		} else {
			doc.classList.add("active");
		}
		document.getElementById("mediaqueriesdisplay").insertBefore(doc, document.getElementById("globalcssscope"));
		
		/* For mediaqueries select */
		var option = document.createElement("div"); 
		option.classList.add("mdqOption");
		option.dataset.media = media;
		option.innerHTML = mediaType + titleMedia;
		document.getElementById("selectmedias").insertBefore(option, document.getElementById("selectmedias").lastChild);
		this.drawMediaQueries();
	}
	return media;
}

blockAdminCSS.prototype.drawMediaQueries = function() {
	var size = document.getElementById("preview").getBoundingClientRect();
	document.getElementById("mediaqueriesdisplay").style.paddingLeft = size.left + "px";
	document.getElementById("scopeMediaQueries").style.width = size.width + "px";
	var width = size.width;
	var arrow = width + size.left - 10;
	document.getElementById("arrow-down").style.left = arrow + "px";
	var mediaqs = document.querySelectorAll(".mediaq");
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
		if(line < 4) {
			for (var i = line; i < 4; i++) {
				highlighted += '</div><div class="CSSLightline" data-line="' + i + '"><span class="CSSLightgutter"></span>';
			}
		}
		line++;
		this.hilight.innerHTML = '<div class="CSSLightline' + (this.rm.indexOf("1") == "-1" ? "" : " CSSLightbarre") + '" data-line="1">' + gutter + highlighted + '</div>';
		this.textarea.style.height = this.hilight.getBoundingClientRect().height + "px";
		if (change != false) {
			/* Preview changes on CSS code mode without commented rules && save changes in a temp object */
			var value = this.textarea.value;
			if (this.rm.length > 0) {
				var lines = value.split("\n");
				for (var key in this.rm) {
					if (typeof lines[key - 1] != "undefined")
						lines[key - 1] = "";
				}
				value = lines.join("\n");
			}
			var admin_css = Parsimony.blocks['admin_css'];
			var selector = decodeURIComponent(this.textarea.dataset.selector);
			if (this.textarea.dataset.media == "") {
				admin_css.setCss(ParsimonyAdmin.currentDocument.styleSheets[this.textarea.dataset.nbstyle].cssRules[this.textarea.dataset.nbrule], value);
			} else {
				admin_css.setCss(ParsimonyAdmin.currentDocument.styleSheets[this.textarea.dataset.nbstyle].cssRules[this.textarea.dataset.nbmedia].cssRules[this.textarea.dataset.nbrule], value);
			}
			admin_css.setCssChange(this.textarea.dataset.path, selector, this.textarea.value, this.textarea.dataset.media);
			
			/*  */
			if (selector == document.getElementById("current_selector_update").value && this.textarea.dataset.nbmedia == admin_css.currentIdMedia) {
				admin_css.setCss(admin_css.currentRule, value);
				admin_css.currentProperties = admin_css.extractCSSRules(this.textarea.value);
				admin_css.fillVisualCssForm(admin_css.currentProperties);
			}
			
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
