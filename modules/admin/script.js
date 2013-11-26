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
	wysiwyg: "",
	unsavedChanges: false,
	plugins: [],

	initBefore: function() {

		ParsimonyAdmin.iframe = document.getElementById("parsiframe");
		ParsimonyAdmin.$iframe = $(ParsimonyAdmin.iframe);

		$("#formResult").on("load", function() {
			var elmt = $(this).contents().find('body').text();
			if (elmt != "") ParsimonyAdmin.execResult(elmt); /* Firefox fix */
		});

		/* make sure that all url passed in iframe are suffixed by '?parsiframe=ok', to don't load the admin in iframe */
		var observer = new MutationObserver(function(mutations) {
			if (mutations[0].attributeName == "src") {
				var newSRC = ParsimonyAdmin.iframe.getAttribute("src");
				if (newSRC.indexOf("parsiframe=ok") == -1) {
					ParsimonyAdmin.iframe.setAttribute("src", newSRC + '?parsiframe=ok');
				}
			}
		});
		observer.observe(ParsimonyAdmin.iframe, {attributes: true});
		
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

	initIframe: function() {

		ParsimonyAdmin.currentWindow = ParsimonyAdmin.iframe.contentWindow;
		ParsimonyAdmin.currentDocument = ParsimonyAdmin.currentWindow.document;
		ParsimonyAdmin.$currentDocument = $(ParsimonyAdmin.currentDocument);
		ParsimonyAdmin.currentBody = ParsimonyAdmin.currentDocument.body;
		ParsimonyAdmin.$currentBody = $(ParsimonyAdmin.currentBody);
		Parsimony.popin = document.getElementById("conf_box_content_iframe");
		ParsimonyAdmin.inProgress = "container";
		ParsimonyAdmin.updateUI();
		ParsimonyAdmin.CSSValuesChanges = {},
		ParsimonyAdmin.changeDeviceUpdate();

		/* Add Iframe style */
		var iframeStyle = document.createElement("link");
		iframeStyle.setAttribute("rel", "stylesheet");
		iframeStyle.setAttribute("type", "text/css");
		iframeStyle.setAttribute("href", BASE_PATH + "admin/css/iframe.css");
		ParsimonyAdmin.currentDocument.getElementsByTagName('head')[0].appendChild(iframeStyle);

		/* Init mode */
		var initialMode = ParsimonyAdmin.getCookie("mode");
		if (initialMode == 'edit') {
			document.getElementById("editMode").click();
		} else if (initialMode == 'preview') {
			document.getElementById("previewMode").click();
		} else {
			document.querySelector(".switchMode:last-child").click();
		}

		//override jQuery ready function to exec them with ajax portions
		setTimeout('$.fn.ready = function(a) {ParsimonyAdmin.currentWindow.eval(" exec = " + a.toString()+";exec.call(window)");}', 4000);
		//document.getElementById("parsiframe").contentWindow.$.fn.ready = function(a) {a.call(document.getElementById("parsiframe").contentWindow);}

		Parsimony.blocksDispatch("initIframe");

	},
	loadCreationMode: function() {
		ParsimonyAdmin.$currentBody.on('click.creation', '.translation', function(e) {
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
		$(".selection-block", ParsimonyAdmin.currentBody).removeClass("selection-block");
		$(".selection-container", ParsimonyAdmin.currentBody).removeClass("selection-container");
		ParsimonyAdmin.closeParsiadminMenu();
		ParsimonyAdmin.$currentBody.off('.creation');
		$("#dialog-id").off('.creation');
		$(document).add('#config_tree_selector').off('.creation');
		$("#CSSProps").off('.creation');
		Parsimony.blocksDispatch("unloadCreationMode");
	},

	loadEditMode: function() {

		/* Enable contenteditable */
		$(".core_wysiwyg, .parsieditinline", ParsimonyAdmin.currentBody).attr("contenteditable", "true");

		/* Enable edit toolbar */
		document.getElementById("toolbarEditMode").style.display = "block";

		/* Listen all changes of all edit-inlines */
		ParsimonyAdmin.editObserver = new MutationObserver(function(mutations) {
			var node = mutations[0].target.parentNode;
			while (node != ParsimonyAdmin.currentBody) {
				if (node.classList.contains("parsieditinline") || node.classList.contains("core_wysiwyg")) {
					if (!node.hasAttribute("data-modified")) node.setAttribute("data-modified", "1");

					/* Manage save toolbar : show/hide for WYSISYG blocks or contenteditable fields */
					var undo = ParsimonyAdmin.currentDocument.queryCommandEnabled("undo");
					var redo = ParsimonyAdmin.currentDocument.queryCommandEnabled("redo");
					if (undo || redo) {
						document.getElementById("toolbarEditMode").classList.add("open");
						if (undo) document.getElementById("toolbarEditModeUndo").style.display = "inline-block";
						else document.getElementById("toolbarEditModeUndo").style.display = "none";
						if (redo) document.getElementById("toolbarEditModeRedo").style.display = "inline-block";
						else document.getElementById("toolbarEditModeRedo").style.display = "none";
					} else {
						document.getElementById("toolbarEditMode").classList.remove("open");
					}
					ParsimonyAdmin.unsavedChanges = true;

					break;
				}
				node = (node.parentNode || ParsimonyAdmin.currentBody);
			}
		});
		ParsimonyAdmin.editObserver.observe(ParsimonyAdmin.currentBody, {subtree: true, characterData: true});

		/* Init WYSIWYG editor */
		if (typeof ParsimonyAdmin.wysiwyg == "string") {
			ParsimonyAdmin.wysiwyg = new wysiwyg();
			ParsimonyAdmin.wysiwyg.init(".core_wysiwyg, .parsieditinline", ["bold", "underline", "italic", "justifyLeft", "justifyCenter", "justifyRight", "justifyFull", "strikeThrough", "subscript", "superscript", "orderedList", "unOrderedList", "outdent", "indent", "removeFormat", "createLink", "unlink", "formatBlock", "fontName", "fontSize", "foreColor", "hiliteColor", "insertImage"], document, ParsimonyAdmin.currentDocument);
		}
		document.querySelector(".HTML5editorToolbar").style.display = 'none';

		/* Hide WYSIWYG editor if focused element isn't a WYSIWYG block/field */
		ParsimonyAdmin.$currentDocument.on('click.edit', ".parsiblock", function(e) {
			if (!ParsimonyAdmin.currentDocument.activeElement.hasAttribute("contenteditable"))
				document.querySelector(".HTML5editorToolbar").style.display = 'none';
		});

		/* Manage undo/redo on save toolbar */
		$("#toolbarEditMode").on('click.edit', ".toolbarEditModeCommands", function(e) {
			ParsimonyAdmin.currentDocument.execCommand(this.dataset.command, false, null);
		})

		/* Save all WYSISYG blocks or contenteditable fields */
		.on('click.edit', "#toolbarEditModeSave", function(e) {

			/* We collect fresh data for WYSIWYG blocks */
			var changes = {};
			$(".core_wysiwyg", ParsimonyAdmin.currentBody).each(function() {
				if (this.dataset.modified) {
					var module = ParsimonyAdmin.currentWindow.THEMEMODULE;
					var theme = ParsimonyAdmin.currentWindow.THEME;
					var idPage = '';
					if (ParsimonyAdmin.whereIAm(this.id) == 'page') {
						theme = '';
						module = ParsimonyAdmin.currentWindow.MODULE;
						idPage = $(".core_page", ParsimonyAdmin.currentBody).data('page');
					}
					changes[this.id] = {idPage: idPage, theme: theme, module: module, html: this.innerHTML};
				}
			});

			/* We collect fresh data for contenteditable fields */
			$(".parsieditinline", ParsimonyAdmin.currentBody).each(function() {
				if (this.dataset.modified) {
					changes[this.dataset.entity + this.dataset.property + this.dataset.id] = {module: this.dataset.module, entity: this.dataset.entity, fieldName: this.dataset.property, id: this.dataset.id, html: this.innerHTML};
				}
			});

			/* We send fresh data to the server to save it */
			$.post(BASE_PATH + 'admin/saveWYSIWYGS', {changes: JSON.stringify(changes)}, function(data) {
				ParsimonyAdmin.unsavedChanges = false;
				document.getElementById("toolbarEditMode").classList.remove("open");
				$(".core_wysiwyg, .parsieditinline").attr("data-modified", "0");
				ParsimonyAdmin.execResult(data);
			});
		});

		Parsimony.blocksDispatch("loadEditMode");
	},
	unloadEditMode: function() {
		/* Disable wysiwyg */
		$(".HTML5editorToolbar").hide();
		$(".core_wysiwyg, .parsieditinline", ParsimonyAdmin.currentBody).attr("contenteditable", "false");

		/* Disable observer */
		if (ParsimonyAdmin.editObserver != undefined) ParsimonyAdmin.editObserver.disconnect();

		/* Disable events */
		ParsimonyAdmin.$currentDocument.off('.edit');

		/* Disable toolbar */
		$("#toolbarEditMode").off('.edit').hide();

		Parsimony.blocksDispatch("unloadEditMode");
	},
	loadPreviewMode: function() {
		ParsimonyAdmin.$currentBody.on('click.preview', 'a', function(e) {
			var href = this.getAttribute("href");
			if (href.substring(0, 1) != '#' && href.substring(0, 7) != 'http://') {
				e.preventDefault();
				ParsimonyAdmin.goToPage(this.textContent.replace("'", "\\'").trim(), href);
			}
		});
		ParsimonyAdmin.closeConfBox();
		Parsimony.blocksDispatch("loadPreviewMode");
	},
	unloadPreviewMode: function() {
		ParsimonyAdmin.$currentBody.off('.preview');
		Parsimony.blocksDispatch("unloadPreviewMode");
	},
	init: function() {

		ParsimonyAdmin.isInit = true;

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

		ParsimonyAdmin.hideOverlay();
		ParsimonyAdmin.removeEmptyTextNodes(document.body);
		Parsimony.blocksDispatch("init");

	},
	goToPage: function(pageTitle, pageUrl) {
		/* Unload current Mode to clean events */
		var captitalizeOldMode = ParsimonyAdmin.currentMode[0].toUpperCase() + ParsimonyAdmin.currentMode.substring(1);
		ParsimonyAdmin["unload" + captitalizeOldMode + "Mode"]();
		ParsimonyAdmin.currentMode = '';

		if (pageUrl.substring(0, BASE_PATH.length) != BASE_PATH && pageUrl.substring(0, 7) != "http://")
			pageUrl = BASE_PATH + pageUrl;
		pageUrl = pageUrl.trim();
		if (pageUrl.indexOf('?') > -1 && pageUrl.indexOf('?parsiframe=ok') == -1) pageUrl += '&parsiframe=ok';
		else pageUrl += '?parsiframe=ok';
		ParsimonyAdmin.currentDocument.title = pageTitle;
		document.getElementById("parsiframe").setAttribute('src', pageUrl);

		return false;
	},
	execResult: function(obj) {
		if (obj.notification == null)
			var obj = JSON.parse(obj);
		if (obj.eval != null) eval(obj.eval);
		var headParsiFrame = ParsimonyAdmin.$iframe.contents().find("head");
		if (obj.jsFiles) {
			obj.jsFiles = JSON.parse(obj.jsFiles);
			$.each(obj.jsFiles, function(index, url) {
				if (!$("script[scr='" + url + "']", headParsiFrame).length) {
					ParsimonyAdmin.$currentBody.append('<script type="text/javascript" src="' + BASE_PATH + url + '"></script>');
				}
			});
		}
		if (obj.CSSFiles) {
			obj.CSSFiles = JSON.parse(obj.CSSFiles);
			$.each(obj.CSSFiles, function(index, url) {
				if (!$('link[href="' + url + '"]', headParsiFrame).length) {
					ParsimonyAdmin.$currentBody.append('<link rel="stylesheet" type="text/css" href="' + BASE_PATH + url + '">');
				}
			});
		}
		if (obj.notification)
			ParsimonyAdmin.notify(obj.notification, obj.notificationType);
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
		if(idNextBlock == "last"){
			/* empty container */
			var testDropIncontainer = $("#" + parentBlock + " >  .dropInContainer", ParsimonyAdmin.currentBody);
			if(testDropIncontainer.length > 0){
				testDropIncontainer.remove();
			}
			$("#" + parentBlock, ParsimonyAdmin.currentBody).append(contentBlock);
		}else {
			$("#" + idNextBlock, ParsimonyAdmin.currentBody).before(contentBlock);
		}
		$("#" + idBlock, ParsimonyAdmin.currentBody).trigger("click");
	},
	moveMyBlock: function(idBlock) {
		var idNextBlock = Parsimony.blocks['admin_blocks'].lastIdNextBlock;
		var parentBlock = Parsimony.blocks['admin_blocks'].lastIdParent;
		if(idNextBlock == "last"){
			/* empty container */
			var testDropIncontainer = $("#" + parentBlock + " >  .dropInContainer", ParsimonyAdmin.currentBody);
			if(testDropIncontainer.length > 0){
				testDropIncontainer.remove();
			}
			$("#" + parentBlock, ParsimonyAdmin.currentBody).append($("#" + idBlock, ParsimonyAdmin.currentBody));
		}else {
			$("#" + idNextBlock, ParsimonyAdmin.currentBody).before($("#" + idBlock, ParsimonyAdmin.currentBody));
		}
	},
	selectBlock: function(idBlock) {
		var blockTreeObj = document.getElementById("treedom_" + idBlock);
		var block = ParsimonyAdmin.currentDocument.getElementById(idBlock);
		var oldSelection = ParsimonyAdmin.currentDocument.querySelector(".selection-block");
		var oldSelectionTree = document.querySelector(".currentDOM");
		var config_tree_selector = document.getElementById("config_tree_selector");

		oldSelection && oldSelection.classList.remove("selection-block");
		oldSelectionTree && oldSelectionTree.classList.remove("currentDOM");
		ParsimonyAdmin.inProgress = idBlock;
		ParsimonyAdmin.typeProgress = ParsimonyAdmin.whereIAm(ParsimonyAdmin.inProgress);
		block && block.classList.add("selection-block");
		if (blockTreeObj)
			blockTreeObj.classList.add("currentDOM");

		if (idBlock == "container") {
			$(".move_block, .config_destroy").hide();
		} else if (idBlock == "content") {
			$(".config_destroy").hide();
		} else {
			$(".move_block, .config_destroy").show();
		}
		if (blockTreeObj && blockTreeObj.classList.contains("container") && blockTreeObj.querySelector("#treedom_content")) {
			$(".config_destroy").hide();
		}
		config_tree_selector.style.display = "block";
		if (blockTreeObj)
			blockTreeObj.insertBefore(config_tree_selector, blockTreeObj.firstChild);

	},
	unSelectBlock: function() {
		$('#parsimonyDND, #config_tree_selector').hide();
		ParsimonyAdmin.inProgress = '';
	},
	whereIAm: function(idBlock) {
		var where = "theme";
		var elmt = ParsimonyAdmin.currentDocument.getElementById(idBlock);
		if (elmt) {
			if (elmt.compareDocumentPosition(ParsimonyAdmin.currentDocument.getElementById("content")) == 10) {
				where = "page";
			}
		} else {
			if (idBlock == 'dropInTree')
				var obj = document.getElementById("dropInTree");
			else
				var obj = document.getElementById("treedom_" + idBlock);
			if (obj.compareDocumentPosition(document.getElementById("treedom_content")) == 10) {
				where = "page";
			}
		}
		return where;
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
		ParsimonyAdmin.showOverlay();
		if (url.substring(0, 1) != "#") {
			Parsimony.popin.classList.remove("open");
			ParsimonyAdmin.returnToShelter();
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
			Parsimony.popin.style.display = "block";
		} else {
			document.getElementById("conf_box_content_inline").classList.remove("open");
			$("#conf_box_content_inline").show().append($(url));
			Parsimony.popin.style.display = "none";
			$(url).show();
			document.getElementById("conf_box_content_inline").classList.add("open");
			document.getElementById("conf_box_load").style.display = "none";
		}

	},
	displayExplorer: function() {
		ParsimonyAdmin.explorer = window.open(BASE_PATH + 'admin/explorer?parsiframe=ok', 'Explorer', 'top=200,left=200,width=1000,height=600');
	},
	closeConfBox: function() {
		Parsimony.popin.classList.remove("open");
		document.body.classList.remove("popleft");
		Parsimony.popin.removeAttribute("style");
		ParsimonyAdmin.hideOverlay();
		Parsimony.popin.setAttribute("src", "about:blank");
		/* Remove hash from URL to allow reopen the same popup */
		var urlWithoutHash = document.location.href.substring(0,(document.location.href.length - document.location.hash.length));
		history.replaceState({url:urlWithoutHash}, document.title, urlWithoutHash);
	},
	resizeConfBox: function() {
		Parsimony.popin.removeAttribute("style");
		var doc = Parsimony.popin.contentDocument;
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
			Parsimony.popin.style.cssText = "width:" + width + "px;height:" + height + "px;";
			Parsimony.popin.classList.add("open");
		}
	},
	setConfBoxTitle: function(title) {
		ParsimonyAdmin.currentDocument.getElementById("conf_box_title").textContent = title;
	},
	returnToShelter: function() {
		$("#dropInPage", ParsimonyAdmin.currentBody).prependTo($("#shelter"));
		$("#dropInTree").prependTo($("#shelter"));
	},
	changeDevice: function(device) {
		ParsimonyAdmin.setCookie("device", device, 999);
		THEMETYPE = device;
		document.getElementById("changeres").value = "";// to change res.
		ParsimonyAdmin.changeDeviceUpdate(device);
		document.getElementById("info_themetype").textContent = device;
		ParsimonyAdmin.iframe.setAttribute("src", ParsimonyAdmin.iframe.getAttribute("src"));
		ParsimonyAdmin.loadBlock('panelblocks');
	},
	changeDeviceUpdate: function() {
		var select = '<div id="customReso"><input id="customWidth" type="number" placeholder="Width" min="100" max="5000"> X <input id="customHeight" type="number" placeholder="Height" min="100" max="5000"></div>';
		var firstRes = "";
		var nb = 0;
		var changeres = $('#changeres');
		$.each(JSON.parse(ParsimonyAdmin.resolutions[THEMETYPE]), function(i, item) {
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
		ParsimonyAdmin.setCookie("locale", locale, 999);
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
		$(".dropInContainer",ParsimonyAdmin.currentBody).remove();
		if(tree!=false) {
		$("#config_tree_selector").hide().prependTo("#right_sidebar");
		ParsimonyAdmin.loadBlock('tree', {IDPage: top.document.getElementById("infodev_page").textContent});
		}
		$(".core_container",ParsimonyAdmin.currentBody).each(function(){
		if($(this).find('.parsiblock:not("#content")').length == 0) {
			$(this).prepend('<div class="dropInContainer"><div class="dropInContainerChild">Id #' + $(this).get(0).id + ". " + t("Drop the blocks in this space") + '</div></div>');
		}else $(".dropInContainerChild:first",this).remove();
		});
	},
	setCreationMode: function() {
		ParsimonyAdmin.setMode("creation");
	},
	setEditMode: function() {
		$('#panelblocks').hide();
		$('#modules').show();
		ParsimonyAdmin.setMode("edit");
	},
	setPreviewMode : function (){
		ParsimonyAdmin.setMode("preview");
	},
	setMode: function(mode) {
		$("body").add(ParsimonyAdmin.currentBody).removeClass("previewMode editMode creationMode").addClass(mode + "Mode");
		/* Unload current mode if exists */
		if (ParsimonyAdmin.currentMode.length > 0) {
			var captitalizeOldMode = ParsimonyAdmin.currentMode[0].toUpperCase() + ParsimonyAdmin.currentMode.substring(1);
			ParsimonyAdmin["unload" + captitalizeOldMode + "Mode"]();
		}
		ParsimonyAdmin.currentMode = mode;
		$(".switchMode").removeClass("selected");
		$("#" + mode + "Mode").addClass("selected");
		var captitalizeNewMode = mode[0].toUpperCase() + mode.substring(1);
		/* Load new mode */
		ParsimonyAdmin["load" + captitalizeNewMode + "Mode"]();
		ParsimonyAdmin.setCookie("mode", mode, 999);
	},
	loadBlock: function(id, params, func) {
		if (!params) params = {};
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
