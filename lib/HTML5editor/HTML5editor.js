function wysiwyg() {

	this.currentElmt;
	this.toolbar;
	this.toolbarWidgets;
    this.widgetsInst = {};
	this.selector;
	this.toolbarDocument;
	this.currentDocument;
	this.enable;
	this.isMultiple = false;
	this.textarea;
	this.allowedTags = {};
	this.allowedStyles = {};
			
	var $this = this;
	
	/* Build and init toolbar */
	this.init = function(selector, toolbarWidgets, toolbarDocument, currentDocument) {

		/* Init WYSIWYG vars*/
		this.selector = selector;
		this.toolbarWidgets = toolbarWidgets;
		if (typeof toolbarDocument == "undefined")
			this.toolbarDocument = document;
		else
			this.toolbarDocument = toolbarDocument;
		if (typeof currentDocument == "undefined")
			this.currentDocument = document;
		else
			this.currentDocument = currentDocument;

		/* Select element to convert into WYSIWYG */
		var el = $(this.selector, this.currentDocument);

		/* If it's not already done */
		if ($('.HTML5editorToolbar[data-selector="' + this.selector + '"]', this.toolbarDocument).length == 0) {

			/* We build a toolbar which is associated with the WYSIWYG */
			this.toolbar = document.createElement("div");
			this.toolbar.className = "HTML5editorToolbar";
			this.toolbar.setAttribute("data-selector", this.selector);

			$(this.toolbar).on("mousedown", "*", function(e) {
				e.preventDefault();
				return setTimeout('', 300);
			})
			/* Listen click action on BTN && Listen change action on selects*/
			.on("click", ".HTML5editorAction", function(e) {
				$this.widgetsInst[this.getAttribute("data-name")].onClick(e, $this, this);
				e.preventDefault();
			});

			/* If textarea mode : simple WYSIWYG conneted with a textarea */
			if (el[0] && el[0].tagName == "TEXTAREA") {
				this.textarea = el;
				this.textarea.before(this.toolbar);
				el[0].style.margin = "0";
				var singleEditor = $("<div>", this.toolbarDocument);
				singleEditor.attr("id", "editor" + ($(".HTML5editorToolbar", this.currentDocument).length + 1))
						.attr("contenteditable", "true").attr("spellcheck", "false").attr("data-textarea", this.selector)
						.addClass("HTML5Editor")
						.html(this.textarea.val())
						.get(0).style = el[0].style;
				this.currentElmt = singleEditor[0];
				this.textarea.hide()
				.before(singleEditor);
				this.updateToolbar(this.toolbarWidgets);
				this.selector = "#" + singleEditor.attr("id");
			} else {
				/* If multiple mode: one toolbar for severals contenteditable divs */
				$("body", this.toolbarDocument).append(this.toolbar);
				$(this.toolbar).addClass("multiple");
				this.isMultiple = true;
			}
		}

		$(this.currentDocument).on("mouseup", this.selector, function(e) {
			$this.checkCommands();
			document.querySelector(".popover").style.display = "none";
		})
		.on("paste drop", this.selector, function(e) {
			var eltm = this;
			setTimeout(function() {
				$this.sanitize(eltm);
			}, 20);
		})
		.on('keydown', this.selector, function(e) {
			var keyCode = e.keyCode || e.which;
			if (keyCode == 9) {
				e.preventDefault();
				if (e.shiftKey) {
					$this.currentDocument.execCommand("outdent", false, null);
				} else {
					$this.currentDocument.execCommand("indent", false, null);
				}
			}
		});

		$(this.selector, this.currentDocument).each(function() {
			if ($this.isMultiple) {
				this.setAttribute("spellcheck", "false");
				this.setAttribute("contenteditable", "true");

				/* Listen focus event on all div that matches selector in order to position and resize wysiwyg */
				$(this).on("focus", function(e) {
					if ($this.enable) {
						$this.setDIV(e.target);
					}
				})
				.on("input", function(e) {
					$this.checkCommands();
				});

			} else {
				/* Copy the content of WYSIWYG to the textarea */
				$(this).on("input", function(e) {
					$this.textarea[0].value = $this.currentElmt.innerHTML;
					$this.checkCommands();
					e.preventDefault();
				})
				.on("blur", function(e) {
					this.innerHTML = $this.format(this.innerHTML);
				});

				/* Copy the content of textarea to the WYSIWYG */
				$this.textarea.on("keyup blur", function(e) {
					$this.currentElmt.innerHTML = $this.textarea[0].value;
					e.preventDefault();
				});
			}
		});

		/* Init for first use */
		this.setCommand("styleWithCSS");

		this.enable = true;
	}

	this.updateToolbar = function(toolbarWidgets) {
		
		/* Init values for new editable area */
		var groups = [];
		var groupsIds = [];
		this.widgetsInst = [];
		this.allowedTags = {
			"div": ["style", "dir", "lang", "title"],
			"span": ["style", "dir", "lang", "title"],
			"p": ["style", "dir", "lang", "title"],
			"pre": ["style", "dir", "lang", "title"],
			"blockquote": ["style", "dir", "lang", "title"],
			"br": ["style"]
		};
		this.allowedStyles = {};
		
		/* Build toolbar */
		for (var i = 0; i < toolbarWidgets.length; i++) {
			var widget = new this.widgets[toolbarWidgets[i]];
			if (typeof widget.allowedTags != "undefined")
				jQuery.extend(this.allowedTags, widget.allowedTags);
			if (typeof widget.allowedStyles != "undefined")
				jQuery.extend(this.allowedStyles, widget.allowedStyles);
			if (typeof groupsIds[widget.category] == "undefined"){
				groupsIds[widget.category] = groups.length;
				groups[groups.length] = '<div class="toolbar_' + widget.category + '">';
			}
			groups[groupsIds[widget.category]] += widget.getAdmin();
			this.widgetsInst[toolbarWidgets[i]] = widget;
		}
		this.toolbar.innerHTML = '<iframe src="javascript:void(0)" class="popover"></iframe>' + groups.join("</div>") + "</div>";
	}

	this.format = function(code) {
		var html = '';
		var pad = 0;
		code = code.replace(/(>)\s*(<)(\/*)/g, '$1\r\n$2$3');
		$.each(code.split('\r\n'), function(index, node) {
			var indent = 0;
			if (node.match(/.+<\/\w[^>]*>$/)) {
				indent = 0;
			} else if (node.match(/^<\/\w/)) {
				if (pad != 0)
					pad -= 1;
			} else if (node.match(/^<\w[^>]*[^\/]>.*$/) && !node.match(/^<(br|img).*>\s?$/)) {
				indent = 1;
			}
			var padding = '';
			for (var i = 0; i < pad; i++)
				padding += '    ';
			html += padding + node + '\r\n';
			pad += indent;
		});
		return html;
	}
	
	/* position and resize wysiwyg */
	this.setDIV = function(currentElmt) {
		this.currentElmt = currentElmt;
		var dim = this.currentElmt.getBoundingClientRect();
		if (this.isMultiple){
			var widgets = currentElmt.getAttribute("data-wysiwygplugins");
			if(widgets){
				this.updateToolbar(widgets.split(","));
			}else{
				this.updateToolbar(this.toolbarWidgets);
			}
			this.toolbar.style.width = dim.width + "px";
		}
		var top = dim.top - 30;
		if (top < 0) {
			top = dim.top + dim.height + 30;
		}
		this.toolbar.style.top = top + "px";
		this.toolbar.style.left = dim.left + "px";
		document.querySelector(".HTML5editorToolbar").style.display = "block";
	}

	/* Exec a command on current active contenteditable div */
	this.setCommand = function(command, value) {
		if (navigator.userAgent.toLowerCase().indexOf('firefox') > -1)
			this.currentDocument.execCommand("styleWithCSS", false, value); // fix firefox
		/* if range is collaspsed we select the entire focused node */
		var selection = this.currentDocument.getSelection();
		if(selection && selection.focusNode != null && this.currentDocument.getSelection().isCollapsed && command != "insertImage"){
			var range = document.createRange();
			range.selectNode(selection.focusNode);
			selection.addRange(range);
		}
		this.currentDocument.execCommand(command, false, value);
		this.checkCommands();
	}

	/* Check wich command could be exec or not */
	this.checkCommands = function() {
		var commands = this.toolbar.querySelectorAll("[data-command]");
		if(commands){
			for(var i = 0, len = commands.length; i < len; i++){
				var command = commands[i].getAttribute("data-command");
				if(command != "none"){
					var el = $(commands[i]);
					var enabled = this.currentDocument.queryCommandEnabled(command);
					if (enabled) {
						el.removeClass("inactive");
						this.widgetsInst[commands[i].getAttribute("data-name")].setCurrentValue(el, this.currentDocument.queryCommandValue(command), this);
					} else {
						el.removeClass("active");
						if (!el.hasClass("inactive"))
							el.addClass("inactive");
					}
				}
			}
		}
	}

	this.sanitize = function(elmt) {
		for (var a = 0; a < elmt.childNodes.length; a++) {
			node = elmt.childNodes[a];
			if (node.nodeType == 1) {
				if (typeof this.allowedTags[node.tagName.toLowerCase()] == "undefined") {
					if (window.getComputedStyle(node, null).getPropertyValue("display") == 'block')
						var span = document.createElement("div");// todo ie x.currentStyle[styleProp];
					else
						var span = document.createElement("span");
					var attrs = node.attributes;
					for (var i = 0; i < attrs.length; i++)
						span.setAttribute(attrs[i].nodeName, attrs[i].nodeValue);
					span.innerHTML = node.innerHTML;// do with dom, not innerHTML
					var newNode = node.parentNode.insertBefore(span, node);
					node.parentNode.removeChild(node);
					node = newNode;
				}
				var attrs = node.attributes;
				for (var i = 0; i < attrs.length; i++) {
					if (this.allowedTags[node.tagName.toLowerCase()].indexOf(attrs[i].nodeName) == -1) { //indexOf not for ie8
						node.removeAttribute(attrs[i].nodeName);
						i--;
					} else if (attrs[i].nodeName == "style") {
						var styles = node.style;
						for (var y = 0; y < styles.length; y++) {
							if (typeof this.allowedStyles[styles[y]] == "undefined") {
								styles.removeProperty(styles[y]); //removeProperty not for ie8 : removeAttribute
								y--;
							}
						}
						if (styles.length == 0)
							node.removeAttribute("style");
						else
							node.setAttribute("style", styles.cssText);
					}
				}
				/* if the node is empty and useless */
				if (node.tagName != "BR" && node.getBoundingClientRect().height == 0) {
					node.parentNode.removeChild(node);
					a--;
				}
				/* if the node is useless */
				else if ((node.tagName == "SPAN" && !node.hasAttribute("style")) || (!node.hasAttribute("style") && node.parentNode.childNodes.length == 1)) {
					for (var z = 0; z < node.childNodes.length; z++) {
						var childNode = node.childNodes[z];
						node.parentNode.insertBefore(childNode, node);
						z--
					}
					node.parentNode.removeChild(node);
					a--;
				}

				if (node.hasChildNodes())
					this.sanitize(node);
			}
		}
	},

	this.disable = function() {
		this.enable = false;
		if (typeof this.toolbar != "undefined") {
			this.toolbar.hide();
			$(this.selector, this.currentDocument).attr("contenteditable", "false");
		}
	}
	
	this.widgets = {
		btn : function () {

			this.category = "format";

			this.getAdmin = function() {
				return '<div style="background:url(' + BASE_PATH + 'lib/HTML5editor/sprites777.png) no-repeat ' + this.position + ';" class="HTML5editorAction btn" data-name="' + this.name + '" data-command="' + this.command + '"></div>';
			}

			this.onClick = function(e, editor) {
				editor.setCommand(this.command);
			}

			this.setCurrentValue = function(elmt, value, obj) {
				if (obj.currentDocument.queryCommandState(elmt[0].getAttribute("data-command"))) {
					if (!elmt.hasClass("active"))
						elmt.addClass("active");
				}else{
					elmt.removeClass("active");
				}
			}

		},
		bold: function () {

			$this.widgets["btn"].call(this);

			this.name = this.command = "bold";
			this.category = "fontstyle";
			this.position = "-480px 0";
			this.allowedStyles = {"font-weight": /.*/};

		},
		underline: function () {

			$this.widgets["btn"].call(this);

			this.name = this.command = "underline";
			this.category = "fontstyle";
			this.position = "0 -32px";
			this.allowedStyles = {"text-decoration": /.*/};

		},
		italic:function () {

			$this.widgets["btn"].call(this);

			this.name = this.command = "italic";
			this.category = "fontstyle";
			this.position = "-32px -32px";
			this.allowedStyles = {"font-style": /.*/};

		},
		justifyLeft: function () {

			$this.widgets["btn"].call(this);

			this.name = this.command = "justifyLeft";
			this.category = "textalign";
			this.position = "-128px -32px";
			this.allowedStyles = {"text-align": /.*/};

		},
		justifyCenter:function () {

			$this.widgets["btn"].call(this);

			this.name = this.command = "justifyCenter";
			this.category = "textalign";
			this.position = "-160px -32px";
			this.allowedStyles = {"text-align": /.*/};

		},
		justifyRight:function () {

			$this.widgets["btn"].call(this);

			this.name = this.command = "justifyRight";
			this.category = "textalign";
			this.position = "-192px -32px";
			this.allowedStyles = {"text-align": /.*/};

		},
		justifyFull:function () {

			$this.widgets["btn"].call(this);

			this.name = this.command = "justifyFull";
			this.category = "textalign";
			this.position = "-224px -32px";
			this.allowedStyles = {"text-align": /.*/};

		},
		strikeThrough:function () {

			$this.widgets["btn"].call(this);

			this.name = this.command = "strikeThrough";
			this.category = "fontstyle";
			this.position = "-64px -32px";
			this.allowedStyles = {"text-decoration": /.*/};

		},
		subscript:function () {

			$this.widgets["btn"].call(this);

			this.name = this.command = "subscript";
			this.category = "fontstyle";
			this.position = "-128px 0";
			this.allowedStyles = {"vertical-align": /.*/};

		},
		superscript:function () {

			$this.widgets["btn"].call(this);

			this.name = this.command = "superscript";
			this.category = "fontstyle";
			this.position = "-96px 0";
			this.allowedStyles = {"vertical-align": /.*/};

		},
		orderedList:function () {

			$this.widgets["btn"].call(this);

			this.name = "orderedList";
			this.command = "insertOrderedList";
			this.category = "list";
			this.position = "-416px 0";
			this.allowedTags = {
				"ol": ["id", "class", "style", "dir", "lang", "title"],
				"li": ["id", "class", "style", "dir", "lang", "title"]
			};
			this.allowedStyles = {
				"list-style": /.*/,
				"list-style-image": /.*/,
				"list-style-position": /.*/,
				"list-style-type": /.*/
			};
		},
		unOrderedList:function () {

			$this.widgets["btn"].call(this);

			this.name = "unOrderedList";
			this.command = "insertUnorderedList";
			this.category = "list";
			this.position = "-384px 0";
			this.allowedTags = {
				"ul": ["id", "class", "style", "dir", "lang", "title"],
				"li": ["id", "class", "style", "dir", "lang", "title"]
			};
			this.allowedStyles = {
				"list-style": /.*/,
				"list-style-image": /.*/,
				"list-style-position": /.*/,
				"list-style-type": /.*/
			};
		},
		undo:function () {

			$this.widgets["btn"].call(this);

			this.name = this.command = "undo";
			this.category = "history";
			this.position = "-320px 0";

		},
		redo:function () {

			$this.widgets["btn"].call(this);

			this.name = this.command = "redo";
			this.category = "history";
			this.position = "-352px 0";

		},
		copy:function () {

			$this.widgets["btn"].call(this);

			this.name = this.command = "copy";
			this.category = "copypaste";
			this.position = "-256px 0";

			this.onClick = function(e, editor) {
				if (typeof(window.clipboardData) == "undefined") {
					alert("Your navigateur preferences don't allow this action. Please use CTRL + C");
				} else {
					editor.setCommand("copy");
				}
			}
		},
		paste:function () {

			$this.widgets["btn"].call(this);

			this.name = this.command = "paste";
			this.category = "copypaste";
			this.position = "-288px 0";

			this.onClick = function(e, editor) {
				if (typeof(window.clipboardData) == "undefined") {
					alert("Your navigateur preferences don't allow this action. Please use CTRL + V");
				} else {
					editor.setCommand("copy");
				}
			}
		},
		cut:function () {

			$this.widgets["btn"].call(this);

			this.name = this.command = "cut";
			this.category = "copypaste";
			this.position = "-448px 0";

			this.onClick = function(e, editor) {
				if (typeof(window.clipboardData) == "undefined") {
					alert("Your navigateur preferences don't allow this action. Please use CTRL + X");
				} else {
					editor.setCommand("copy");
				}
			}
		},
		outdent:function () {

			$this.widgets["btn"].call(this);

			this.name = this.command = "outdent";
			this.category = "indent";
			this.position = "-288px -32px";
			this.allowedTags = {"blockquote": ["id", "class", "style", "dir", "lang", "title"]};
			this.allowedStyles = {"margin": /.*/,"border": /.*/,"padding": /.*/};

		},
		indent:function () {

			$this.widgets["btn"].call(this);

			this.name = this.command = "indent";
			this.category = "indent";
			this.position = "-256px -32px";
			this.allowedTags = {"blockquote": ["id", "class", "style", "dir", "lang", "title"]};
			this.allowedStyles = {"margin": /.*/,"border": /.*/,"padding": /.*/};
		},
		removeFormat:function () {

			$this.widgets["btn"].call(this);

			this.name = this.command = "removeFormat";
			this.category = "removeFormat";
			this.position = "0 0";

			this.onClick = function(e, editor) {
				editor.setCommand("removeFormat");
				editor.setCommand("backColor", "transparent");
			}
		},
		createLink:function () {

			$this.widgets["btn"].call(this);

			this.name = this.command = "createLink";
			this.position = "-32px 0";
			this.category = "link";
			this.allowedTags = {"a": ["id", "class", "style", "dir", "lang", "title", "accesskey", "tabindex", "charset", "coords", "href", "hreflang", "name", "rel", "rev", "shape", "target"]};

			this.onClick = function(e, editor) {
				var link = prompt('URL', 'http:\/\/');
				if (link && link.length > 0) {
					editor.setCommand("createLink", link);
				}
			}
		},
		unlink:function () {

			$this.widgets["btn"].call(this);

			this.name = this.command = "unlink";
			this.category = "link";
			this.position = "-64px 0";

		},
		selectSquel:function () {

			this.name = this.command = "formatBlock";
			this.category = "format";

			this.getAdmin = function() {
				return '<div style="height:25px;border:0;" class="HTML5editorAction select temoin" data-name="' + this.name + '" data-command="' + this.command + '">' + this.defaultText + '</div>';
			}

			this.onClick = function(e, editor) {
				var html = "<style>.option{cursor:pointer;padding:3px;}.option:hover{background:#ddd}</style>";
				for (i in this.values) {
					html += this.template(i, this.values[i]);
				}
				e.target.insertBefore( $('.popover', editor.toolbar)[0], e.target.firstChild);
				var prop = $('.popover', editor.toolbar).css({
					width: "190px",
					height: "200px"
				}).show().contents().find("body").append(html);

				var $this = this;
				$(".option", prop).on("mousedown", function(e) {
					$('.popover', editor.toolbar).appendTo(editor.toolbar).hide();
					$(".option", prop).off("mousedown");
					editor.setCommand($this.command, $(this).data("option"));
				});
			}

			this.setCurrentValue = function(elmt, type) {
				$(elmt).text(this.defaultText).text(this.values[type]);
			}
		},
		formatBlock:function () {

			$this.widgets["selectSquel"].call(this);

			this.name = this.command = "formatBlock";
			this.category = "format";
			this.values = {
				"p": "Paragraph",
				"h1": "Heading 1",
				"h2": "Heading 2",
				"h3": "Heading 3",
				"h4": "Heading 4",
				"h5": "Heading 5",
				"h6": "Heading 6",
				"pre": "Preformatted",
				"blockquote": "Blockquote"
			};
			this.allowedTags = {
				"h1": ["id", "class", "style", "dir", "lang", "title"],
				"h2": ["id", "class", "style", "dir", "lang", "title"],
				"h3": ["id", "class", "style", "dir", "lang", "title"],
				"h4": ["id", "class", "style", "dir", "lang", "title"],
				"h5": ["id", "class", "style", "dir", "lang", "title"],
				"h6": ["id", "class", "style", "dir", "lang", "title"]
			};

			this.template = function(i, val) {
				return '<' + i + ' class="option" data-option="' + i + '">' + val + '</' + i + '>';
			};
			this.defaultText = 'Format';
		},
		fontName:function () {

			$this.widgets["selectSquel"].call(this);

			this.name = this.command = "fontName";
			this.category = "format";
			this.values = {
				"arial, helvetica, sans-serif": "Arial",
				"'arial black', avant garde;": "Arial Black",
				"'book antiqua', palatino": "Book Antiqua",
				"'comic sans ms', sans-serif": "Comic Sans MS",
				"courier new, courier": "Courier New",
				"georgia, palatino": "Georgia",
				"helvetica": "Helvetica",
				"impact, chicago": "Impact",
				"symbol": "Symbol",
				"tahoma, arial, helvetica, sans-serif": "Tahoma",
				"terminal, monaco": "Terminal",
				"'times new roman', times": "Times New Roman",
				"'trebuchet ms', geneva": "Trebuchet MS",
				"verdana, geneva": "Verdana",
				"webdings": "Webdings",
				"wingdings, 'zapf dingbats'": "Wingdings"
			};
			this.template = function(i, val) {
				return '<div class="option" data-option="' + i + '" style="font-family:' + i + '">' + val + '</div>';
			};
			this.defaultText = 'Font Family';
			this.allowedStyles = {"font-family": /.*/};
		},
		fontSize:function () {

			$this.widgets["selectSquel"].call(this);

			this.name = this.command = "fontSize";
			this.category = "format";
			this.values = {
				"1": "8",
				"2": "10",
				"3": "12",
				"4": "14",
				"5": "18",
				"6": "24",
				"7": "36"
			};
			this.template = function(i, val) {
				return '<div class="option" data-option="' + i + '" style="font-size:' + val + 'pt">' + val + '</div>';
			};
			this.defaultText = 'Font Size';
			this.allowedStyles = {"font-size": /.*/};
		},
		colorPicker:function () {

			this.palette = ["#000000", "#434343", "#666666", "#999999", "#B7B7B7", "#cccccc", "#D9D9D9", "#EFEFEF", "#F3F3F3", "#ffffff",
				"#980000", "red", "#F90", "yellow", "lime", "cyan", "#4A86E8", "blue", "#90F", "magenta",
				"#E6B8AF", "#F4CCCC", "#FCE5CD", "#FFF2CC", "#D9EAD3", "#D0E0E3", "#C9DAF8", "#CFE2F3", "#D9D2E9", "#EAD1DC",
				"#DD7E6B", "#EA9999", "#F9CB9C", "#FFE599", "#B6D7A8", "#A2C4C9", "#A4C2F4", "#9FC5E8", "#B4A7D6", "#D5A6BD",
				"#CC4125", "#E06666", "#F6B26B", "#FFD966", "#93C47D", "#76A5AF", "#6D9EEB", "#6FA8DC", "#8E7CC3", "#C27BA0",
				"#A61C00", "#C00", "#E69138", "#F1C232", "#6AA84F", "#45818E", "#3C78D8", "#3D85C6", "#674EA7", "#A64D79",
				"#85200C", "#900", " #B45F06", " #BF9000", "#38761D", "#134F5C", "#15C", "#0B5394", "#351C75", "#741B47",
				"#5B0F00", "#600", "#783F04", "#7F6000", "#274E13", "#0C343D", "#1C4587", "#073763", "#20124D", "#4C1130"];

			this.getAdmin = function() {
				return '<div class="HTML5editorAction btn" style="background:url(' + BASE_PATH + 'lib/HTML5editor/sprites777.png) no-repeat ' + this.position + ';" data-name="' + this.name + '"data-command="' + this.command + '"><div class="temoin" style="position:absolute;top:22px;height:3px;left:2px;right:3px;"></div></div>';
			}

			this.onClick = function(e, editor) {
				var html = '<style>.pick{float:left;width:16px;height:16px;margin:1px;cursor:pointer}.pick:hover{outline:1px solid #777}</style><div style="width: 180px;"><div class="pick" style="height:16px;width:auto;float:none;color:#ddd;font-size:11px;font-family:arial;padding:0 2px;background:transparent">Transparent</div>';
				for (var i = 0; i < this.palette.length; i++) {
					html += '<div class="pick" style="background:' + this.palette[i] + '"></div>';
				}
				var prop = $('.popover', editor.toolbar).css({
					width: "200px",
					height: "185px"
				}).appendTo($(e.target).closest(".HTML5editorAction")).show().contents().find("body").append(html);
				var temoin = $(".temoin", e.target);
				var $this = this;
				$(prop).on("mousedown.Wcolorpicker", ".pick", function(event) {
					editor.setCommand($this.command, $(this).css("background-color"));
					temoin.css("background-color", $(this).css("background-color"));
					$('.popover', editor.toolbar).hide();
					$(prop).off(".Wcolorpicker");
					event.preventDefault();
				});
			}

			this.setCurrentValue = function(elmt, color) {
				$(".temoin", elmt).css("background-color", color);
			}

			this.rgbToHex = function(r, g, b) {
				return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
			}
		},
		foreColor:function () {

			$this.widgets["colorPicker"].call(this);

			this.name = this.command = "foreColor";
			this.category = "color";
			this.position = "-384px -32px";
			this.allowedStyles = {"color": /.*/};
		},
		hiliteColor:function () {

			$this.widgets["colorPicker"].call(this);

			this.name = "hiliteColor";
			this.command = "backColor";
			this.category = "color";
			this.position = "-160px 0";
			this.allowedStyles = {"background-color": /.*/};
		},
		insertImage:function () {

			$this.widgets["btn"].call(this);

			this.name = this.command = "insertImage";
			this.category = "insert";
			this.position = "-192px 0";

			this.allowedStyles = {"padding": /.*/, "float": /.*/};

			this.allowedTags = {"img": ["id", "src", "alt", "title", "class", "style", "dir", "lang", "title"]};

			this.onClick = function(e, editor) {
				var html = '<style>.panel{padding:7px 0;display:none}#url{display:block}.tab,.tabBtn{display:inline-block;vertical-align:top;padding:5px 15px;font-size:12px;cursor:pointer}.tab.active,.tab:hover,.tabBtn:hover{background:#ddd}input[type="button"]{background:#ddd;border:1px #ddd solid;cursor:pointer}input[type="button"]:hover{background:#ccc;border:1px #ddd solid}</style>';
				html += '<div><div class="tab active" data-tab="url">From URL</div>';
				//html += '<div class="tab" data-tab="pc">From PC</div>';
				if(typeof ParsimonyAdmin != "undefined") html += '<div class="tabBtn" id="addImgExplorer">From Explorer</div></div>';
				html += '<div id="url" class="panel"><input type="text" placeholder="http://..." id="URLimg"><input type="button" value="Add" class="addImgURL"></div>';
				//html += '<div id="pc" class="panel"><input type="file" value="Add" id="addImgPC"></div>';
				var prop = $('.popover', editor.toolbar).css({
					width: "350px",
					height: "100px"
				}).appendTo(e.target.parentNode).show().contents().find("body").empty().append(html);
				var $this = this;
				$(".tab", prop).on("mousedown", function() {
					$(".active", prop).removeClass("active");
					$(".panel", prop).hide();
					$(this).addClass("active");
					$("#" + this.getAttribute("data-tab"), prop).show();
				});
				$("#addImgURL", prop).on("mousedown", function() {
					editor.setCommand($this.command, $(".URLimg", prop).val());
					$('.popover', editor.toolbar).hide();
					$(".addImgURL", prop).off("mousedown");
				});
				$("#addImgPC", prop).on("mousedown", function() {
					editor.setCommand($this.command, $(".URLimg", prop).val());
					$('.popover', editor.toolbar).hide();
					$(".addImgPC", prop).off("mousedown");
				});
				$("#addImgExplorer", prop).on("mousedown", function(e) {
					e.preventDefault();
					window.callbackExplorer = function (file){
						editor.setCommand($this.command, file);
						window.callbackExplorer = function(file){return false;};
						ParsimonyAdmin.explorer.close();
					}
					ParsimonyAdmin.displayExplorer();
					$('.popover', editor.toolbar).hide();
					$(".addImgExplorer", prop).off("mousedown");
				});
			}
		},
		code:function () {

			this.name = "code";
			this.command = "none"; // fix firefox
			this.category = "code";
			this.position = "-320px -32px";
			this.allowedTags = {"code": ["id", "src", "alt", "title", "class", "style", "dir", "lang", "title", "data-language"]};
			
			this.getAdmin = function() {
				if(!$this.isMultiple){
					return '<div style="background:url(' + BASE_PATH + 'lib/HTML5editor/sprites777.png) no-repeat ' + this.position + ';" class="HTML5editorAction btn" data-name="' + this.name + '" data-command="' + this.command + '"></div>';
				}
				return '';
			}
			
			this.onClick = function(e, editor, elmt) {
				if($(elmt).hasClass("active")){
					$(elmt).removeClass("active");
					$this.toolbar.nextSibling.nextSibling.style.display = "none";
					$this.toolbar.nextSibling.style.display = "block";
					$this.toolbar.nextSibling.focus();
					$this.checkCommands();
				}else{
					$(elmt).addClass("active");
					$this.toolbar.nextSibling.nextSibling.style.display = "block";
					$this.toolbar.nextSibling.nextSibling.focus();
					$this.checkCommands();
					$this.toolbar.nextSibling.style.display = "none";
				}
			}
			
			this.setCurrentValue = function(elmt, value, obj) {}
		}
	}
}