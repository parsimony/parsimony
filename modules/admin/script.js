/**
 * Parsimony
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@parsimony-cms.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Parsimony to newer
 * versions in the future. If you wish to customize Parsimony for your
 * needs please refer to http://www.parsimony.mobi for more information.
 *
 *  @authors Julien Gras et Benoît Lorillot
 *  @copyright  Julien Gras et Benoît Lorillot
 *  @version  Release: 1.0
 *  @category  Parsimony
 *  @package admin
 *  Requires: jQuery 
 *  @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

var ParsimonyAdmin = {
	isInit: false,
	currentWindow: "",
	currentDocument: "",
	currentBody: "",
	currentMode: "",
	inProgress: "",
	typeProgress: "",
	unsavedChanges: false,

	initBefore: function() {

		this.iframe = document.getElementById("preview");
		this.$iframe = $(this.iframe);
		this.currentWindow = this.iframe.contentWindow;

		$("#formResult").on("load", function() {
			var elmt = $(this).contents().find('body').text();
			if (elmt != "") ParsimonyAdmin.execResult(elmt); /* Firefox fix */
		});

		/* make sure that all url passed in iframe are suffixed by '?preview=ok', to don't load the admin in iframe */
		var observer = new MutationObserver(function(mutations) {
			if (mutations[0].attributeName == "src") {
				var newSRC = ParsimonyAdmin.iframe.getAttribute("src");
				if (newSRC.indexOf("preview=ok") == -1) {
					ParsimonyAdmin.iframe.setAttribute("src", newSRC + '?preview=ok');
				}
			}
		});
		observer.observe(this.iframe, {attributes: true});
		
		/* Client side controller / for now, only for admin blocks */
		window.onhashchange = function() {
			if (document.location.hash.length > 0 && document.location.hash.indexOf("/") > -1) {
				var hashParts = document.location.hash.split("/", 2);
				var block = document.querySelector(".parsiblock" + hashParts[0]);
				if (block && hashParts.length > 1) {
					/* If first part is an existing block ID */
					if (typeof Parsimony.blocks[block.classList[1]] == "object" && typeof Parsimony.blocks[block.classList[1]][hashParts[1]] == "function") {
						Parsimony.blocks[block.classList[1]][hashParts[1]].apply(this, document.location.hash.substring(hashParts.toString().length + 1).split("/"));
					}
				}
			}
		};
		Parsimony.blocksDispatch("initBefore");
	},

	initPreview: function() {

		this.currentDocument = this.currentWindow.document;
		this.$currentDocument = $(this.currentDocument);
		this.currentBody = this.currentDocument.body;
		this.$currentBody = $(this.currentBody);
		this.popin = document.getElementById("conf_box_content_iframe");
		this.inProgress = "container";
		this.updateUI();
		this.CSSValuesChanges = {},
		this.changeDeviceUpdate();

		/* Add Iframe style */
		var iframeStyle = document.createElement("link");
		iframeStyle.setAttribute("rel", "stylesheet");
		iframeStyle.setAttribute("type", "text/css");
		iframeStyle.setAttribute("href", BASE_PATH + "admin/css/iframe.css");
		this.currentDocument.getElementsByTagName('head')[0].appendChild(iframeStyle);

		/* Init mode */
		var initialMode = this.getCookie("mode");
		if (initialMode == 'edit') {
			document.getElementById("editMode").click();
		} else if (initialMode == 'preview') {
			document.getElementById("previewMode").click();
		} else {
			document.querySelector(".switchMode:last-child").click();
		}

		//override jQuery ready function to exec them with ajax portions
		setTimeout('$.fn.ready = function(a) {ParsimonyAdmin.currentWindow.eval(" exec = " + a.toString()+";exec.call(window)");}', 4000);
		//document.getElementById("preview").contentWindow.$.fn.ready = function(a) {a.call(document.getElementById("preview").contentWindow);}

		Parsimony.blocksDispatch("initPreview");

	},
	loadCreationMode: function() {
		this.$currentBody.on('click.creation', '.translation', function(e) {
			e.trad = true;
			ParsimonyAdmin.closeParsiadminMenu();
			ParsimonyAdmin.addTitleParsiadminMenu(t('Translation'));
			ParsimonyAdmin.addOptionParsiadminMenu('<span class="ui-icon ui-icon-pencil floatleft"></span><a href="#" class="action" rel="getViewTranslation" params="key=' + $(this).data("key") + '" title="' + t('Translation') + '">' + t('Translate') + '</a>');
		})
		.on('click.creation', 'a', function(e) {
			e.link = true;
			e.preventDefault();
			if(e.trad != true) ParsimonyAdmin.closeParsiadminMenu();
			ParsimonyAdmin.addTitleParsiadminMenu("Link");
			ParsimonyAdmin.addOptionParsiadminMenu('<a href="#" onclick="ParsimonyAdmin.goToPage(\'' + $(this).text().replace("'", "\\'").trim() + '\',\'' + $(this).attr('href') + '\');return false;"><span class="ui-icon ui-icon-extlink floatleft"></span>' + t('Go to the link') + '</a>');
		});

		$("#dialog-id").on("keyup.creation", function(e) {
			this.value = this.value.toLowerCase().replace(/[^a-z_]+/g, "");
			var code = e.keyCode || e.which;
			if (code == 13) {
				document.getElementById("dialog-ok").click();
			}
		});

		$(document).on('click', '#menu a', function(e) {
			ParsimonyAdmin.closeParsiadminMenu();
			$('.cssPicker', ParsimonyAdmin.currentDocument).removeClass('cssPicker');
		});

		$("#CSSProps").on("mouseenter.creation mouseleave.creation", "a", function(event) {
			if (event.type == 'mouseenter') {
				$("#" + ParsimonyAdmin.inProgress + " " + this.dataset.css, ParsimonyAdmin.currentDocument).addClass('cssPicker');
			} else {
				$('.cssPicker', ParsimonyAdmin.currentDocument).removeClass('cssPicker');
			}
		});

		Parsimony.blocksDispatch("loadCreationMode");
	},
	unloadCreationMode: function() {
		$(".selection-block", this.currentBody).removeClass("selection-block");
		$(".selection-container", this.currentBody).removeClass("selection-container");
		this.closeParsiadminMenu();
		this.$currentBody.off('.creation');
		$("#dialog-id").off('.creation');
		$(document).add('#config_tree_selector').off('.creation');
		$("#CSSProps").off('.creation');
		Parsimony.blocksDispatch("unloadCreationMode");
	},

	loadEditMode: function() {
		
		/* Enable edit on block wysiwyg */
		$(".core_wysiwyg", this.currentBody).attr("data-mode", "blockwysiwyg").addClass("parsieditinline");
		this.currentWindow.parsiEdit.init();
		Parsimony.blocksDispatch("loadEditMode");
	},
	unloadEditMode: function() {
		$(".HTML5editorToolbar", this.currentBody).hide();
		$(".core_wysiwyg", this.currentBody).removeClass("parsieditinline");

		/* Disable events */
		this.currentWindow.parsiEdit.destroy();
		
		Parsimony.blocksDispatch("unloadEditMode");
	},
	loadPreviewMode: function() {
		this.$currentBody.on('click.preview', 'a', function(e) {
			var href = this.getAttribute("href");
			if (href.substring(0, 1) != '#' && href.substring(0, 7) != 'http://') {
				e.preventDefault();
				ParsimonyAdmin.goToPage(this.textContent.replace("'", "\\'").trim(), href);
			}
		});
		this.closeConfBox();
		Parsimony.blocksDispatch("loadPreviewMode");
	},
	unloadPreviewMode: function() {
		this.$currentBody.off('.preview');
		Parsimony.blocksDispatch("unloadPreviewMode");
	},
	init: function() {

		this.isInit = true;

		/* Init tooltip */
		$(".tooltip").parsimonyTooltip({
			triangleWidth: 5
		});

		var timer = setInterval(function resizeIframe() {
			if (document.getElementById("changeres").value == "max") {
				var height = ParsimonyAdmin.currentBody.getBoundingClientRect().bottom;
				if (screen.height > height)
					height = screen.height - 35;
				if (ParsimonyAdmin.iframe.style.height != height + "px") {
					ParsimonyAdmin.iframe.style.height = height + "px";
					document.getElementById("overlays").style.height = height + "px";
				}
			}
		}, 1000);

		/* Shortcut : Save on CTRL+S */
		document.addEventListener("keydown", function(e) {
			if (e.keyCode == 83 && e.ctrlKey) {
				e.preventDefault();
				$("form", $('#conf_box_content_iframe').contents().find("body")).trigger("submit");
			}
		}, false);

		$(window).on("beforeunload", function(event) {
			if (ParsimonyAdmin.unsavedChanges == true) return t("You have unsaved changes");
		});

		this.hideOverlay();
		this.removeEmptyTextNodes(document.body);
		Parsimony.blocksDispatch("init");

	},
	goToPage: function(pageTitle, pageUrl) {
		/* Unload current Mode to clean events */
		var captitalizeOldMode = this.currentMode[0].toUpperCase() + this.currentMode.substring(1);
		this["unload" + captitalizeOldMode + "Mode"]();
		this.currentMode = '';

		if (pageUrl.substring(0, BASE_PATH.length) != BASE_PATH && pageUrl.substring(0, 7) != "http://")
			pageUrl = BASE_PATH + pageUrl;
		pageUrl = pageUrl.trim();
		if (pageUrl.indexOf('?') > -1 && pageUrl.indexOf('?preview=ok') == -1) pageUrl += '&preview=ok';
		else pageUrl += '?preview=ok';
		this.currentDocument.title = pageTitle;
		document.getElementById("preview").setAttribute('src', pageUrl);

		return false;
	},
	execResult: function(obj) {
		if (obj.notification == null)
			var obj = JSON.parse(obj);
		if (obj.eval != null) eval(obj.eval);
		var headpreview = this.$iframe.contents().find("head");
		if (obj.jsFiles) {
			obj.jsFiles = JSON.parse(obj.jsFiles);
			$.each(obj.jsFiles, function(index, url) {
				if (!$("script[scr='" + url + "']", headpreview).length) {
					ParsimonyAdmin.$currentBody.append('<script type="text/javascript" src="' + BASE_PATH + url + '"></script>');
				}
			});
		}
		if (obj.CSSFiles) {
			obj.CSSFiles = JSON.parse(obj.CSSFiles);
			$.each(obj.CSSFiles, function(index, url) {
				if (!$('link[href="' + url + '"]', headpreview).length) {
					ParsimonyAdmin.$currentBody.append('<link rel="stylesheet" type="text/css" href="' + BASE_PATH + url + '">');
				}
			});
		}
		if (obj.notification)
			this.notify(obj.notification, obj.notificationType);
	},
	postData: function(url, params, callBack) {
		$.post(url, params, function(data) {
			callBack(data);
		});
	},
	destroyBlock: function() {
		if (ParsimonyAdmin.inProgress != "container") {
			if (confirm(t('Do you really want to remove the block ') + ParsimonyAdmin.inProgress + ' ?') == true) {
				ParsimonyAdmin.returnToShelter();
				if ($("#treedom_" + ParsimonyAdmin.inProgress).parent().closest(".parsicontainer").attr("id") == "treedom_content")
					var parentId = $("#treedom_" + ParsimonyAdmin.inProgress).parent().closest("#treedom_content").data('page');
				else
					var parentId = $("#treedom_" + ParsimonyAdmin.inProgress).parent().closest(".parsicontainer").attr('id').replace("treedom_", "");
				ParsimonyAdmin.postData(BASE_PATH + "admin/removeBlock", {
					TOKEN: TOKEN,
					MODULE: ParsimonyAdmin.currentWindow.MODULE, THEMEMODULE: ParsimonyAdmin.currentWindow.THEMEMODULE, THEME: ParsimonyAdmin.currentWindow.THEME, THEMETYPE: ParsimonyAdmin.currentWindow.THEMETYPE,
					idBlock: ParsimonyAdmin.inProgress,
					parentBlock: parentId,
					typeProgress: ParsimonyAdmin.typeProgress,
					IDPage: ($(".core_page", ParsimonyAdmin.currentBody).data('page') || $(".sublist.selected").attr("id").replace("page_", ""))
				}, function(data) {
					ParsimonyAdmin.execResult(data);
					ParsimonyAdmin.returnToShelter();
					ParsimonyAdmin.updateUI();
					document.getElementById("parsimonyDND").style.display = "none";
				});
			}
		}
	},
	addBlock: function(idBlock, contentBlock) {
		var idNextBlock = Parsimony.blocks['admin_blocks'].lastIdNextBlock;
		var parentBlock = Parsimony.blocks['admin_blocks'].lastIdParent;
		if(idNextBlock == null || idNextBlock == "last" ){
			/* empty container */
			var testDropIncontainer = $("#" + parentBlock + " >  .dropInContainer", this.currentBody);
			if(testDropIncontainer.length > 0){
				testDropIncontainer.remove();
			}
			$("#" + parentBlock, this.currentBody).append(contentBlock);
		}else {
			$("#" + idNextBlock, this.currentBody).before(contentBlock);
		}
		$("#" + idBlock, this.currentBody).trigger("click");
	},
	moveBlock: function(idBlock, changeType) {
		var elmt = $("#" + idBlock, this.currentBody);
		this.addBlock(idBlock, elmt);
		if(changeType && changeType == 'pageToTheme'){
			this.inProgress = idBlock.toLowerCase();
			elmt.attr("id", this.inProgress)
		} else if(changeType && changeType == 'themeToPage'){
			this.inProgress = idBlock[0].toUpperCase() + idBlock.substring(1);
			elmt.attr("id", this.inProgress);
		}
	},
	selectBlock: function(idBlock) {
		
		this.inProgress = idBlock;
		
		var block = this.currentDocument.getElementById(idBlock);
		if (block) { /* in case we could'ont find the block id in preview but in tree */
			var blockTreeObj = document.getElementById("treedom_" + block.id);
		} else {
			var blockTreeObj = document.getElementById("treedom_" + idBlock);
		}
		
		this.typeProgress = blockTreeObj.compareDocumentPosition(document.getElementById("treedom_content")) == 10 ? "page" : "theme"; /* check on tree to avoid non-ended tag in preview */
		var oldSelection = this.currentDocument.querySelector(".selection-block");
		var oldSelectionTree = document.querySelector(".currentDOM");
		var config_tree_selector = document.getElementById("config_tree_selector");

		oldSelection && oldSelection.classList.remove("selection-block");
		oldSelectionTree && oldSelectionTree.classList.remove("currentDOM");
		
		if(block){
			block.classList.add("selection-block");

			/* Prepare DND UI */
			if (window.getComputedStyle(block, null).position !== "static") {
				document.getElementById("parsimonyDND").classList.add('positionOK');
			} else {
				document.getElementById("parsimonyDND").classList.remove('positionOK');
			}
			Parsimony.blocks['admin_css'].updatePosition(block.getBoundingClientRect());
			Parsimony.blocks['admin_css'].displayCSSConf(CSSTHEMEPATH, "#" + ParsimonyAdmin.inProgress);

			/* Provide selectors proposals */
			var CSSProps = '';
			var stylableElements = ParsimonyAdmin.stylableElements[block.classList[1]];
			if (typeof stylableElements == "object") {
				$.each(stylableElements, function(index, value) {
					CSSProps += '<a href="#" onclick="Parsimony.blocks[\'admin_css\'].displayCSSConf(CSSTHEMEPATH, \'#\' + ParsimonyAdmin.inProgress + \' ' + value + '\');return false;" data-css="' + value + '">' + ' ' + t(index) + '</a>';
				});

				if (CSSProps.length > 0) {
					document.getElementById("stylableElements").style.display = "inline-block";
				} else {
					document.getElementById("stylableElements").style.display = "none";
				}
			}
			document.getElementById("CSSProps").innerHTML = CSSProps;
		}
		
		if (idBlock == "container") {
			$(".move_block, .config_destroy").hide();
		} else if (idBlock == "content") {
			$(".config_destroy").hide();
		} else {
			$(".move_block, .config_destroy").show();
		}
		
		if (blockTreeObj){
			blockTreeObj.classList.add("currentDOM");
			
			/* can't detroy a container that contain #content */
			if (blockTreeObj.classList.contains("core_container") && blockTreeObj.querySelector("#treedom_content")) {
				$(".config_destroy").hide();
			}
			
			config_tree_selector.style.display = "block";
			blockTreeObj.insertBefore(config_tree_selector, blockTreeObj.firstChild);
		}
			

		

	},
	unSelectBlock: function() {
		$('#parsimonyDND, #config_tree_selector').hide();
		this.inProgress = '';
	},
	showOverlay: function() {
		document.getElementById("conf_box_overlay").style.display = "block";
	},
	hideOverlay: function() {
		document.getElementById("conf_box_overlay").style.display = "none";
	},
	displayConfBox: function(url, params, popleft) {
		document.getElementById("parsimonyDND").style.display = "none";
		document.getElementById("conf_box_overlay").style.display = "block";
		if (popleft == true) document.body.classList.add("popleft");
		else document.body.classList.remove("popleft");
		this.showOverlay();
		if (url.substring(0, 1) != "#") {
			this.popin.classList.remove("open");
			this.returnToShelter();
			var form = document.createElement("form");
			form.setAttribute("action", url);
			form.setAttribute("method", "POST");
			form.setAttribute("target", "conf_box_content_iframe");
			if (typeof params == "string") {
				var vars = (params + "&popup=yes").split(/&/);
				for (var i = 0, len = vars.length; i < len; i++) {
					var myvar = vars[i].split(/=/);
					var field = document.createElement("input");
					field.setAttribute("name", myvar[0]);
					field.setAttribute("value", myvar[1]);
					form.appendChild(field);
				}
			}
			if (navigator.userAgent.toLowerCase().indexOf('firefox') > -1)
				document.body.appendChild(form);
			form.submit();
			form.remove();
			document.getElementById("conf_box_content_inline").style.display = "none";
			this.popin.style.display = "block";
		} else {
			document.getElementById("conf_box_content_inline").classList.remove("open");
			$("#conf_box_content_inline").show().append($(url));
			this.popin.style.display = "none";
			$(url).show();
			document.getElementById("conf_box_content_inline").classList.add("open");
			document.getElementById("conf_box_load").style.display = "none";
		}

	},
	displayExplorer: function() {
		this.explorer = window.open(BASE_PATH + 'admin/explorer?preview=ok', 'Explorer', 'top=200,left=200,width=1000,height=600');
	},
	closeConfBox: function() {
		this.popin.classList.remove("open");
		document.body.classList.remove("popleft");
		this.popin.removeAttribute("style");
		this.hideOverlay();
		this.popin.setAttribute("src", "about:blank");
		/* Remove hash from URL to allow reopen the same popup */
		var urlWithoutHash = document.location.href.substring(0,(document.location.href.length - document.location.hash.length));
		history.replaceState({url:urlWithoutHash}, document.title, urlWithoutHash);
	},
	resizeConfBox: function() {
		this.popin.removeAttribute("style");
		var doc = this.popin.contentDocument;
		document.getElementById("conf_box_load").style.display = "none";
		if (doc.location.href != "about:blank") {
			var adminzonecontent = doc.querySelector(".adminzonecontent");
			var width, height;
			if (adminzonecontent) {
				width = adminzonecontent.scrollWidth + 150;
				height = adminzonecontent.scrollHeight;
				if (doc.querySelector("#conf_box_title")) {
					height += 40;
				}
				if (doc.querySelector(".adminzonefooter")) {
					height += 40;
				}
			} else {
				var elmt = doc.body.querySelector(".content");
				width = elmt.offsetWidth;
				height = elmt.scrollHeight;
			}
			this.popin.style.cssText = "width:" + width + "px;height:" + height + "px;";
			this.popin.classList.add("open");
		}
	},
	setConfBoxTitle: function(title) {
		this.currentDocument.getElementById("conf_box_title").textContent = title;
	},
	returnToShelter: function() {
		$("#dropInPage", this.currentBody).prependTo($("#shelter"));
		$("#dropInTree").prependTo($("#shelter"));
	},
	changeDevice: function(device) {
		this.setCookie("device", device, 999);
		THEMETYPE = device;
		document.getElementById("changeres").value = "";// to change res.
		this.changeDeviceUpdate(device);
		document.getElementById("info_themetype").textContent = device;
		this.iframe.setAttribute("src", this.iframe.getAttribute("src"));
		this.loadBlock('panelblocks');
	},
	changeDeviceUpdate: function() {
		var select = '<div id="customReso"><input id="customWidth" type="number" placeholder="Width" min="100" max="5000"> X <input id="customHeight" type="number" placeholder="Height" min="100" max="5000"></div>';
		var firstRes = "";
		var nb = 0;
		var changeres = $('#changeres');
		$.each(JSON.parse(this.resolutions[THEMETYPE]), function(i, item) {
			if (nb == 0) firstRes = i;
			select += '<li data-res="' + i + '">' + item + ' (' + i + ')</li>';
			nb++;
		});
		document.getElementById("currentRes").textContent = changeres[0].value;
		document.getElementById("listres").innerHTML = select;
		if (changeres[0].value == "") changeres.val(firstRes).trigger('change');
		$('#currentRes').css("position", "relative"); //fix
	},
	changeLocale: function(locale) {
		this.setCookie("locale", locale, 999);
		/* kill cache language */
		$.post(BASE_PATH + 'admin/changeLocale', {locale: locale}, function(data) {window.location.reload();});
		
	},
	setCookie: function(name, value, days) {
		if (days) {
		var date = new Date();
			date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
			value += "; expires=" + date.toGMTString();
		}
		document.cookie = name + "=" + value + "; path=/";
	},
	getCookie: function(name) {
		var i, x, y, cookies = document.cookie.split(";");
		for (i = 0; i < cookies.length; i++) {
			x = cookies[i].substr(0, cookies[i].indexOf("="));
			y = cookies[i].substr(cookies[i].indexOf("=") + 1);
			x = x.replace(/^\s+|\s+$/g, "");
			if (x == name) return unescape(y);
		}
	},
	notify: function(message, type) {
		if (window.Notification) {
			var notif = new Notification(message, {
				icon: BASE_PATH + "admin/img/" + type + ".png",
				tag: type
			});
			if (notif.permission === "granted" || window.Notification.permission === "granted") {
				notif.ondisplay = function(event) {
					setTimeout(function() {
						event.currentTarget.cancel();
					}, 4000);
				}
				return true;
			}
		}
		$("#notify").appendTo("body").attr("class", "").addClass(type).html(message).fadeIn("normal").delay(4000).fadeOut("slow");
	},
	openParsiadminMenu: function(x, y) {
		$("#menu").appendTo("body").css({
			"top": (y + ParsimonyAdmin.iframe.offsetTop),
			"left": (x + ParsimonyAdmin.iframe.offsetLeft)
		});
	},
	closeParsiadminMenu: function() {
		$("#menu").appendTo($("#shelter")).find(".options").empty();
	},
	addTitleParsiadminMenu: function(title) {
		$("#menu .options").append('<h5>' + title + '</h5>');
	},
	addOptionParsiadminMenu: function(option) {
		$("#menu .options").append('<div class="option">' + option + '</div>');
	},
	updateUI : function (tree){
		$(".dropInContainer",this.currentBody).remove();
		if(tree != false) {
			$("#config_tree_selector").hide().prependTo("#right_sidebar");
			this.loadBlock('tree', {MODULE: this.currentWindow.MODULE, THEMEMODULE: this.currentWindow.THEMEMODULE, THEME: this.currentWindow.THEME, THEMETYPE: this.currentWindow.THEMETYPE, IDPage: top.document.getElementById("infodev_page").textContent});
		}
		$(".core_container",this.currentBody).each(function(){
		if($(this).find('.parsiblock:not("#content")').length == 0) {
			$(this).prepend('<div class="dropInContainer"><div class="dropInContainerChild">Id #' + $(this).get(0).id + ". " + t("Drop the blocks in this space") + '</div></div>');
		}else $(".dropInContainerChild:first",this).remove();
		});
	},
	setCreationMode: function() {
		this.setMode("creation");
	},
	setEditMode: function() {
		$('#panelblocks,#manage').hide();
		$('#modules').show();
		this.setMode("edit");
	},
	setPreviewMode : function (){
		this.setMode("preview");
	},
	setMode: function(mode) {
		$("body").add(this.currentBody).removeClass("previewMode editMode creationMode").addClass(mode + "Mode");
		/* Unload current mode if exists */
		if (this.currentMode.length > 0) {
			var captitalizeOldMode = this.currentMode[0].toUpperCase() + this.currentMode.substring(1);
			this["unload" + captitalizeOldMode + "Mode"]();
		}
		this.currentMode = mode;
		$(".switchMode").removeClass("selected");
		$("#" + mode + "Mode").addClass("selected");
		var captitalizeNewMode = mode[0].toUpperCase() + mode.substring(1);
		/* Load new mode */
		this["load" + captitalizeNewMode + "Mode"]();
		this.setCookie("mode", mode, 999);
	},
	loadBlock: function(id, params, func) {
		if (!params) params = {};
		params["getBlockAdmin"] = '1';
		$.get(window.location.href.toLocaleString(), params, function(data) {
			$('#' + id).html($("<div>").append(data.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, "")).find("#" + id).html());
		}, func);
	},
	displayPanel: function(id) {
		var panel = document.getElementById(id);
		var sidebar = panel.parentNode;
		$(".parsiblock",sidebar).hide();
		panel.style.display = "block";
		if(sidebar.id == "left_sidebar"){
			$(".leftSidebarMenu .active").removeClass("active");
		}else{
			$(".rightSidebarMenu .active").removeClass("active");
		}
		$("." + panel.id).addClass("active");
	},	
	removeEmptyTextNodes: function(elem) {
		var children = elem.childNodes;
		var child;
		var len = children.length;
		var whitespace = /^\s*$/;
		for (var i = 0; i < len; i++) {
			child = children[i];
			if (child.nodeType == 3) {
				if (whitespace.test(child.nodeValue)) {
					elem.removeChild(child);
					i--;
					len--;
				}
			} else if (child.nodeType == 1) {
				this.removeEmptyTextNodes(child);
			}
		}
	}
}
var $lang = new Array;
function t(val) {
	if ($lang[val]) {
		return $lang[val];
	} else {
		return val;
	}
}

if(top.window != self){ /* to be sure not reload admin in preview */
	var pageUrl = window.location.href;
	if (pageUrl.indexOf('?') > -1 && pageUrl.indexOf('?preview=ok') == -1) pageUrl += '&preview=ok';
	else pageUrl += '?preview=ok';
	window.location = pageUrl;
}