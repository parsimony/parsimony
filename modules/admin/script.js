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
 * to contact@parsimony.mobi so we can send you a copy immediately.
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
    isInit : false,
    currentWindow : "",
    currentDocument : "",
    currentBody : "",
    currentMode : "",
    inProgress :  "",
    typeProgress : "",
    wysiwyg : "",
    unsavedChanges : false,
    plugins: [],
	
    setPlugin :   function(plugin){
	this.plugins.push(plugin);
    },
    pluginDispatch : function(methodName){
	
	/* Call this method for all plugins */
	for (var i=0; i < this.plugins.length; i++) {
	    var plugin = this.plugins[i];
	    eval("if(typeof plugin." + methodName + " != 'undefined') plugin." + methodName + "();");
	}
    },
    initBefore :   function(){

	$("#ajaxhack").on("load",function() {
	    var elmt = $(this).contents().find('body').text();
	    if(elmt != "") ParsimonyAdmin.execResult(elmt); /* Firefox fix */
	});

	$("#dialog-id").keyup(function(){
	    this.value = this.value.toLowerCase().replace(/[^a-z_]+/,"");
	});
		
	$("#conf_box").on('click','#conf_box_wpopup', function(e){
	    var action = $("#conf_box_form input[name=action]").val();
	    $("#conf_box_form").attr('target','conf_box_content_popup' + action);
	    ParsimonyAdmin.closeConfBox();
	    window.open ($("#conf_box_content_iframe").attr('src'),'conf_box_content_popup' + action,"width=" + $("#conf_box_content_iframe").width() + ",height=" + $("#conf_box_content_iframe").height());
	    $("#conf_box_form").trigger("submit").attr('target','conf_box_content_iframe');
	});
	
	this.pluginDispatch("initBefore");
    },

    initIframe :   function(){
        
        ParsimonyAdmin.iframe = document.getElementById("parsiframe");
        ParsimonyAdmin.$iframe = $(ParsimonyAdmin.iframe);
	ParsimonyAdmin.currentWindow = ParsimonyAdmin.iframe.contentWindow;
	ParsimonyAdmin.currentDocument = ParsimonyAdmin.currentWindow.document;
        ParsimonyAdmin.$currentDocument = $(ParsimonyAdmin.currentDocument);
	ParsimonyAdmin.currentBody = ParsimonyAdmin.currentDocument.body;
        ParsimonyAdmin.$currentBody = $(ParsimonyAdmin.currentBody);
	ParsimonyAdmin.inProgress = "container";
	ParsimonyAdmin.updateUI();
        ParsimonyAdmin.changeDeviceUpdate();
        
        /* Add Iframe style */
	var iframeStyle = document.createElement("link");
	iframeStyle.setAttribute("rel", "stylesheet");
	iframeStyle.setAttribute("type", "text/css");
	iframeStyle.setAttribute("href", BASE_PATH + "admin/iframe.css");
	ParsimonyAdmin.currentBody.insertBefore(iframeStyle, ParsimonyAdmin.currentBody.firstChild);
        
	/* Init mode */
	var initialMode = ParsimonyAdmin.getCookie("mode");
	if(initialMode == 'edit'){
	    $("#editMode").trigger('click');
	}else if(initialMode == 'preview'){
	    $("#previewMode").trigger('click');
	}else{
	    $("#creationMode").trigger('click');
	}

	//override jQuery ready function to exec them with ajax portions
	setTimeout('$.fn.ready = function(a) {ParsimonyAdmin.currentWindow.eval(" exec = " + a.toString()+";exec.call(window)");}',4000);
        //document.getElementById("parsiframe").contentWindow.$.fn.ready = function(a) {a.call(document.getElementById("parsiframe").contentWindow);}
    
	this.pluginDispatch("initIframe");
	
    }
    ,
    loadCreationMode :   function(){
	ParsimonyAdmin.unloadCreationMode();
	ParsimonyAdmin.$currentBody.on('click.creation','.traduction', function(e){
	    e.trad = true;
	    ParsimonyAdmin.closeParsiadminMenu();
	    ParsimonyAdmin.addTitleParsiadminMenu(t('Translation'));
	    ParsimonyAdmin.addOptionParsiadminMenu('<span class="ui-icon ui-icon-pencil floatleft"></span><a href="#" class="action" rel="getViewTranslation" params="key=' + $(this).data("key") + '" title="'+ t('Translation') +'">'+ t('Translate') +'</a>');
	})
	.on('click.creation','a', function(e){
	    e.link = true;
	    e.preventDefault();
	    if(e.trad != true) ParsimonyAdmin.closeParsiadminMenu();
	    ParsimonyAdmin.addTitleParsiadminMenu("Link");
	    ParsimonyAdmin.addOptionParsiadminMenu('<a href="#" onclick="ParsimonyAdmin.goToPage(\'' + $.trim($(this).text().replace("'","\\'")) + '\',\'' + $(this).attr('href') + '\');return false;"><span class="ui-icon ui-icon-extlink floatleft"></span>'+ t('Go to the link') +'</a>');
	});	

	$(document).on("keypress.creation",'#dialog-id',function(e){
	    var code = e.keyCode || e.which; 
	    if(code == 13) {
		$("#dialog-ok").trigger("click");
	    }
	});
	
	this.pluginDispatch("loadCreationMode");
    }, 

    unloadCreationMode :   function(){
	$(".selection-block",ParsimonyAdmin.currentBody).removeClass("selection-block");
	$(".selection-container",ParsimonyAdmin.currentBody).removeClass("selection-container");
	ParsimonyAdmin.closeParsiadminMenu();
	ParsimonyAdmin.$currentBody.off('.creation');
	this.pluginDispatch("unloadCreationMode");
    },
    
    loadEditMode :   function(){
	$(".parsieditinline",ParsimonyAdmin.currentBody).addClass('usereditinline').attr("contenteditable", "true");
        
	/* Active edit behavior on WYSIWYG blocks */
        $(".wysiwyg",ParsimonyAdmin.currentBody).addClass('activeEdit').attr("contenteditable", "true")
	
	/* Shortcut : Save on CTRL+S */
	.on("keydown.edit", function(e) {
	    if (e.keyCode == 83 && e.ctrlKey) {
	      e.preventDefault();
	      ParsimonyAdmin.haveToSave = true;
	    }
	});
	
	/* Init WYSIWYG editor */
	if(typeof ParsimonyAdmin.wysiwyg == "string"){
            ParsimonyAdmin.wysiwyg = new wysiwyg();
            ParsimonyAdmin.wysiwyg.init(".wysiwyg",["bold","underline","italic","justifyLeft","justifyCenter","justifyRight","justifyFull","strikeThrough","subscript","superscript","orderedList","unOrderedList","outdent","indent","removeFormat","createLink","unlink","formatBlock","fontName","fontSize","foreColor","hiliteColor","insertImage"], document, ParsimonyAdmin.currentDocument);
        }
            $(".HTML5editorToolbar").hide();
        
	/* Manage clicks on <a> in edit mode */
	ParsimonyAdmin.$currentDocument.on('click.edit','a', function(e){
            e.preventDefault();
	    /*if($(this).attr("href").substring(0,1) != '#' && $(this).attr("href").substring(0,7) != 'http://' && $(".usereditinline",this).length == 0){
		ParsimonyAdmin.goToPage( $.trim($(this).text().replace("'","\\'")) , $(this).attr('href') );
	    }*/
	})
	
	/* Hide WYSIWYG editor if focused element isn't a WYSIWYG block */
	.on('click.edit','.block',function(e){
	    if(!$(this).hasClass("wysiwyg")) $(".HTML5editorToolbar").hide();
            else $(".HTML5editorToolbar").show();
	});
	
	/* Manage undo/redo on save toolbar */
	$("#toolbarEditMode").on('click.edit',".toolbarEditModeCommands",function(e){
	    ParsimonyAdmin.currentDocument.execCommand(this.dataset.command, false, null);
	})
	
	/* Save all WYSISYG blocks or contenteditable fields */
	.on('click.edit',"#toolbarEditModeSave",function(e){
	    $(this).trigger("focus");
	    /* We collect fresh data for WYSIWYG blocks */
	    var changes = {};
	     $(".wysiwyg.activeEdit",ParsimonyAdmin.currentBody).each(function(){
		if(this.dataset.modified) {
		    var module = ParsimonyAdmin.currentWindow.THEMEMODULE;
		    var theme = ParsimonyAdmin.currentWindow.THEME;
		    var idPage = '';
		    if(ParsimonyAdmin.whereIAm(this.id) == 'page'){
			theme = '';
			module = ParsimonyAdmin.currentWindow.MODULE;
			idPage = $(".container_page",ParsimonyAdmin.currentBody).data('page');
		    }
		    changes[this.id] = {idPage:idPage,theme:theme,module:module,html:this.innerHTML};
		}
	    });

	    /* We collect fresh data for contenteditable fields */
	    $(".usereditinline",ParsimonyAdmin.currentBody).each(function(){
		if(this.dataset.modified) {
		    changes[this.dataset.property+this.dataset.id] = {module:this.dataset.module,entity:this.dataset.entity,fieldName:this.dataset.property,id:this.dataset.id,html:this.innerHTML};
		}
	    });
	    
	    /* We send fresh data to the serve to save it */
	    $.post(BASE_PATH + 'admin/saveWYSIWYGS',{changes:JSON.stringify(changes)},function(data){
			ParsimonyAdmin.unsavedChanges = false
			$("#toolbarEditMode").slideUp();
			$(".wysiwyg.activeEdit, .usereditinline").attr("data-modified","0");
			ParsimonyAdmin.execResult(data);
		});
	});
	
	/* Manage save toolbar : show/hide for WYSISYG blocks or contenteditable fields */
	$(ParsimonyAdmin.currentBody).on('keyup.edit',".wysiwyg.activeEdit, .usereditinline",function(e){
	    var undo = ParsimonyAdmin.currentDocument.queryCommandEnabled("undo");
	    var redo = ParsimonyAdmin.currentDocument.queryCommandEnabled("redo");
	    if(undo || redo){
		$("#toolbarEditMode").slideDown();
		if(undo) document.getElementById("toolbarEditModeUndo").style.display = "inline-block";
		else document.getElementById("toolbarEditModeUndo").style.display = "none";
		if(redo) document.getElementById("toolbarEditModeRedo").style.display = "inline-block";
		else document.getElementById("toolbarEditModeRedo").style.display = "none";
	    }else{
		$("#toolbarEditMode").slideUp();
	    }
	    if(ParsimonyAdmin.haveToSave){
		ParsimonyAdmin.haveToSave = false;
		$("#toolbarEditModeSave").trigger("click");
	    }
	    ParsimonyAdmin.unsavedChanges = true;
	});
	
	this.pluginDispatch("loadEditMode");
    }, 
    
    unloadEditMode :   function(){
	//if(typeof ParsimonyAdmin.wysiwyg == "object") ParsimonyAdmin.wysiwyg.disable();
        delete ParsimonyAdmin.wysiwyg;
        ParsimonyAdmin.wysiwyg = "";
	ParsimonyAdmin.$currentDocument.add(ParsimonyAdmin.currentBody).off('.edit');
        $(".wysiwyg.activeEdit",ParsimonyAdmin.currentBody).off();
        $(".parsieditinline",ParsimonyAdmin.currentBody).removeClass('usereditinline').attr("contenteditable", "false");
        $(".wysiwyg",ParsimonyAdmin.currentBody).removeClass('activeEdit').attr("contenteditable", "false");
        $(".HTML5editorToolbar").hide();
	$("#toolbarEditMode").hide();
	this.pluginDispatch("unloadEditMode");
    },
    loadPreviewMode :   function(){
	ParsimonyAdmin.$currentBody.on('click.preview','a', function(e){
	    if($(this).attr("href").substring(0,1) != '#' && $(this).attr("href").substring(0,7) != 'http://'){
		e.preventDefault();
		ParsimonyAdmin.goToPage( $.trim($(this).text().replace("'","\\'")) , $(this).attr('href') );
	    }
	});
	ParsimonyAdmin.closeConfBox();
	this.pluginDispatch("loadPreviewMode");
    },
    unloadPreviewMode :   function(){
	ParsimonyAdmin.$currentBody.off('.preview');
	this.pluginDispatch("unloadPreviewMode");
    },
    init :   function(){
        
	ParsimonyAdmin.isInit = true;

	$(document).add('#config_tree_selector').on('click',".action", function(e){
	    var parentId = '';
	    var inProgress = $("#treedom_" + ParsimonyAdmin.inProgress);
	    if(inProgress.length > 0){
		if(inProgress.parent().closest(".container").attr("id") == "treedom_content") parentId = inProgress.parent().closest("#treedom_content").data('page');
		else parentId = inProgress.parent().closest(".container").attr('id').replace("treedom_","");
	    }
	    ParsimonyAdmin.displayConfBox(BASE_PATH + "admin/action",($(this).data('title') || $(this).attr('title') || $(this).data('tooltip')),"TOKEN=" + TOKEN + "&idBlock=" + ParsimonyAdmin.inProgress + "&parentBlock=" + parentId + "&typeProgress=" + ParsimonyAdmin.typeProgress + "&action=" + $(this).attr('rel') +"&IDPage=" + $(".container_page",ParsimonyAdmin.currentBody).data('page') +"&" + $(this).attr('params'));
	    e.preventDefault();
	}).on('click','#menu a',function(e){
	    ParsimonyAdmin.closeParsiadminMenu();
	     $('.cssPicker',ParsimonyAdmin.currentDocument).removeClass('cssPicker');
	});
	
	$("#menu").on("mouseenter",".CSSProps a",function(){
	    $("#" + ParsimonyAdmin.inProgress  + " " + this.dataset.css,ParsimonyAdmin.currentDocument).addClass('cssPicker');
	}).on("mouseout",".CSSProps a",function(){
	    $('.cssPicker',ParsimonyAdmin.currentDocument).removeClass('cssPicker');
	});
        
        /* Init tooltip */
	$(".tooltip").parsimonyTooltip({
	    triangleWidth:5
	});
	
	var timer = setInterval(function resizeIframe() {
	    if(document.getElementById("changeres").value == "max"){
		var height = ParsimonyAdmin.currentBody.getBoundingClientRect().bottom;
		if(screen.height > height) height = screen.height - 35;
                if(ParsimonyAdmin.iframe.style.height != height + "px"){
                    ParsimonyAdmin.iframe.style.height = height + "px";
                    document.getElementById("overlays").style.height = height + "px";
                }
	    }
	}, 1000);
	
	/* Shortcut : Save on CTRL+S */
	document.addEventListener("keydown", function(e) {
	    if (e.keyCode == 83 && e.ctrlKey) {
	      e.preventDefault();
		$("form",$('#conf_box_content_iframe').contents().find("body")).trigger("submit");
	    }
	}, false);
	
	$(window).bind("beforeunload",function(event) {
	    if(ParsimonyAdmin.unsavedChanges) return t("You have unsaved changes");
	});
	
	ParsimonyAdmin.hideOverlay();
	ParsimonyAdmin.removeEmptyTextNodes(document.body);
	this.pluginDispatch("init");
	
	
    }
    ,
    goToPage :   function (pageTitle,pageUrl, isHistory){
	ParsimonyAdmin.unloadCreationMode();
	ParsimonyAdmin.unloadEditMode();
	ParsimonyAdmin.unloadPreviewMode();
	if(pageUrl.substring(0,BASE_PATH.length) != BASE_PATH && pageUrl.substring(0,7) != "http://") pageUrl = BASE_PATH + pageUrl;
	pageUrl = $.trim(pageUrl);
        if(pageUrl.indexOf('?') > -1 && pageUrl.indexOf('?parsiframe=ok') == -1) pageUrl += '&parsiframe=ok';
	else pageUrl += '?parsiframe=ok';
        ParsimonyAdmin.currentDocument.title = pageTitle;
	$('#parsiframe').attr('src', pageUrl);
	return false;
    },
    execResult :   function (obj){
	if (obj.notification == null) 
	    var obj = jQuery.parseJSON(obj);
	if(obj.eval != null) eval(obj.eval);
	var headParsiFrame = ParsimonyAdmin.$iframe.contents().find("head");
	if(obj.jsFiles){
	    obj.jsFiles = jQuery.parseJSON(obj.jsFiles);
	    $.each(obj.jsFiles, function(index, url) { 
		if (!$("script[scr='" + url + "']",headParsiFrame).length) {
		    ParsimonyAdmin.$currentBody.append('<script type="text/javascript" src="' + url + '"></script>');
		}
	    });
	}
	if(obj.CSSFiles){
	    obj.CSSFiles = jQuery.parseJSON(obj.CSSFiles);
	    $.each(obj.CSSFiles, function(index, url) {
		if (!$('link[href="' + url + '"]',headParsiFrame).length) {
		    ParsimonyAdmin.$currentBody.append('<link rel="stylesheet" type="text/css" href="' + url + '">');
		}
	    });
             
	}
	if (obj.notification) 
	    ParsimonyAdmin.notify(obj.notification,obj.notificationType);
    },
    postData :   function (url,params,callBack){
	$.post(url,params,function(data){
	    callBack(data);
	});
    },
    destroyBlock :   function (){
	if(ParsimonyAdmin.inProgress != "container"){
	    if(confirm(t('Do you really want to remove the block ') + ParsimonyAdmin.inProgress + ' ?')==true){
		ParsimonyAdmin.returnToShelter();
		if($("#treedom_" + ParsimonyAdmin.inProgress).parent().closest(".container").attr("id")=="treedom_content") var parentId = $("#treedom_" + ParsimonyAdmin.inProgress).parent().closest("#treedom_content").data('page');
		else var parentId = $("#treedom_" + ParsimonyAdmin.inProgress).parent().closest(".container").attr('id').replace("treedom_","");
		ParsimonyAdmin.postData(BASE_PATH + "admin/removeBlock",{
		    TOKEN: TOKEN ,
		    idBlock:ParsimonyAdmin.inProgress,
		    parentBlock: parentId ,
		    typeProgress:ParsimonyAdmin.typeProgress,
		    IDPage:($(".container_page",ParsimonyAdmin.currentBody).data('page') || $(".sublist.selected").attr("id").replace("page_",""))
		} ,function(data){
		    ParsimonyAdmin.execResult(data);
		    ParsimonyAdmin.returnToShelter();
		    ParsimonyAdmin.updateUI();
		});
	    }
	}
    },
    addBlock :   function (idBlock, contentBlock, idBlockAfter){
	if($( "#" + idBlockAfter ,ParsimonyAdmin.currentBody).parent().hasClass("container")){
	    $( "#" + idBlockAfter ,ParsimonyAdmin.currentBody).after(contentBlock);
	    ParsimonyAdmin.returnToShelter();
	}else {
	    var block = $( "#" + idBlockAfter ,ParsimonyAdmin.currentBody).closest(".container");
	    ParsimonyAdmin.returnToShelter();
	    $(".dropInContainer:first",block).remove();
	    if(block.get(0).id == 'container' && block.children(".block").length==1) block.prepend(contentBlock);
	    else block.append(contentBlock);
	}
	
	$("#" + idBlock,ParsimonyAdmin.currentBody ).trigger("click");
	var offset = $("#" + idBlock,ParsimonyAdmin.currentBody ).offset();
	ParsimonyAdmin.openParsiadminMenu(offset.left + 30,offset.top + 30);
    },
    moveMyBlock :   function (idBlock, idBlockAfter){
	if($( "#" + idBlockAfter ,ParsimonyAdmin.currentBody).parent().hasClass("container")){
	    $( "#" + idBlockAfter ,ParsimonyAdmin.currentBody).after( $("#" + idBlock,ParsimonyAdmin.currentBody) );
	    ParsimonyAdmin.returnToShelter();
	}else {
	    var block = $( "#" + idBlockAfter ,ParsimonyAdmin.currentBody).parent().parent().parent();
	    ParsimonyAdmin.returnToShelter();
	    block.html($("#" + idBlock,ParsimonyAdmin.currentBody ) );
	}
    },
    selectBlock :   function (idBlock){
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
	if(blockTreeObj) blockTreeObj.classList.add("currentDOM");
	if(idBlock == "container" || (block && block.classList.contains("container_page"))) config_tree_selector.classList.add("restrict");
	else document.getElementById("config_tree_selector").classList.remove("restrict");
	config_tree_selector.style.display = "block";
	if(blockTreeObj) blockTreeObj.insertBefore(config_tree_selector, blockTreeObj.firstChild);
        
    },
    whereIAm :   function (idBlock){
	var where = "theme";
	var elmt = ParsimonyAdmin.currentDocument.getElementById(idBlock);
	if(elmt){
	    if(elmt.compareDocumentPosition(ParsimonyAdmin.currentDocument.getElementById("content")) == 10){
		where = "page";
	    }
	}else{
	    if(idBlock == 'dropInTree') var obj = document.getElementById("dropInTree");
	    else var obj = document.getElementById("treedom_" + idBlock);
	    if(obj.compareDocumentPosition(document.getElementById("treedom_content")) == 10){
		where = "page";
	    }
	}
	return where;
    },
    showOverlay :   function (opacity){
	if(typeof opacity== "undefined") opacity=1;
	$( "#conf_box_overlay").css('opacity',opacity).show();
    },
    hideOverlay :   function (){
	document.getElementById("conf_box_overlay").style.display = "none";
    },
    displayConfBox :   function (url,title,params,modal){
        $("#conf_box_load").show();
        $("#conf_box,#conf_box_content" ).removeAttr("style");
        $("#conf_box").css("visibility","hidden").show();
	if(typeof modal == "undefined" || modal == true) ParsimonyAdmin.showOverlay();
	ParsimonyAdmin.setConfBoxTitle(title);
        if(url.substring(0,1) != "#"){
            ParsimonyAdmin.returnToShelter();
            $("#conf_box_form").attr("action",url).empty();
            if(typeof params != "undefined"){
                var vars = params.split(/&/);
                for (var i=0; i< vars.length; i++) {
                    var myvar = vars[i].split(/=/);
                        $("#conf_box_form").append('<input type="hidden" name="' + myvar[0]  + '" value="' + myvar[1] + '">');
                    }
                }
                $("#conf_box_form").append('<input type="hidden" name="popup" value="yes">').trigger("submit");
                $("#conf_box_content_iframe").show();
                $("#conf_box_content_inline").hide();
            }else{
                $("#shelter").append($("#conf_box_content_inline").html());
                $("#conf_box_content_inline").show().append($(url));
                $("#conf_box_content_iframe").hide();
                $(url).show();
                document.getElementById("conf_box").style.visibility = "visible";
            }
            
	},
	closeConfBox :   function (){
	    $("#conf_box").hide();
	    ParsimonyAdmin.hideOverlay();
	    $("#conf_box_title").empty();
	    $("#conf_box_content_iframe").attr("src","about:blank");
            
	},
	resizeConfBox : function(){
	    var iframe = document.getElementById("conf_box_content_iframe");
            iframe.removeAttribute("style");
	    var doc = iframe.contentDocument;
            document.getElementById("conf_box_load").style.display = "none";
	    if(doc.location.href != "about:blank"){
                var elmt = $(".adminzone",doc)[0] || $("body",doc)[0] ;
		var height = $(".adminzonefooter",doc).length > 0 ? ( elmt.scrollHeight + 40) : elmt.scrollHeight ;
		iframe.style.cssText = "width:" + elmt.scrollWidth + "px;height:" + height + "px";
	    }
            document.getElementById("conf_box").style.visibility = "visible";
	},
	setConfBoxTitle :   function (title){
	    $("#conf_box_title").html(title);
	},
	returnToShelter : function () {
	    $("#dropInPage",ParsimonyAdmin.currentBody).prependTo($("#shelter"));
	    $("#dropInTree").prependTo($("#shelter"));
	},
	changeDevice : function (device) {
	    ParsimonyAdmin.setCookie("device",device,999);
	    THEMETYPE = device;
	    $('#changeres').val('');// to change res.
	    ParsimonyAdmin.changeDeviceUpdate(device);
	    $("#info_themetype").text(device);
	    ParsimonyAdmin.$iframe.attr("src", ParsimonyAdmin.$iframe.attr("src"));
	    ParsimonyAdmin.loadBlock('panelblocks');
	},
	changeDeviceUpdate : function () {
	    var select = '';
	    var nb = 0;
	    var changeres = $('#changeres');
	    $.each($.parseJSON(resultions[THEMETYPE]), function(i,item){
		if(changeres[0].value == "" && nb == 0) changeres.val(i).trigger('change');
		select += '<li><a href="#" onclick="$(\'#changeres\').val(\'' + i + '\').trigger(\'change\');">' + item + ' (' + i + ')</a></li>';
		nb++;
	    });
	    $("#currentRes").text(changeres[0].value);
	    $('#listres').html(select);$('#currentRes').css("position","relative");
	},
	changeLocale : function (locale) {
	    ParsimonyAdmin.setCookie("locale",locale,999);
	    window.location.reload();
	},
	setCookie : function (name,value,days) {
	    if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	    }
	    else var expires = "";
	    document.cookie = name+"="+value+expires+"; path=/";
	},
	getCookie: function (name){
	    var i, x, y, cookies = document.cookie.split(";");
	    for (i=0; i < cookies.length;i++){
		x = cookies[i].substr(0,cookies[i].indexOf("="));
		y = cookies[i].substr(cookies[i].indexOf("=") + 1);
		x = x.replace(/^\s+|\s+$/g,"");
		if (x==name) return unescape(y);
	    }
	},
	notify : function (message,type) {
	    $("#notify").appendTo("body").attr("class","").addClass(type).html(message).fadeIn("normal").delay(4000).fadeOut("slow");
	},
	openParsiadminMenu : function (x,y) {
	    var off = ParsimonyAdmin.$iframe.offset();
	    $("#menu").appendTo("body").css({
		"top":(y + off.top),
		"left":(x + off.left)
	    });
	},
	closeParsiadminMenu : function () {
	    $("#menu").appendTo($("#shelter")).find(".options").empty();
	},
	addTitleParsiadminMenu : function (title) {
	    $("#menu .options").append('<h5>' + title + '</h5>');
	},
	addOptionParsiadminMenu : function (option) {
	    $("#menu .options").append('<div class="option">' + option + '</div>');
	},
	/*reloadIframe : function (){
	    $.get("index.html?parsiframe=ok",
		function(data){
		    ParsimonyAdmin.$iframe.contents().find("html").replaceWith(data);
		});
	},*/
	updateUI :   function (tree){
	    $(".dropInContainer",ParsimonyAdmin.currentBody).remove();
	    if(tree!=false) {
		$("#config_tree_selector").hide().prependTo("#right_sidebar");
		ParsimonyAdmin.loadBlock('tree');
		ParsimonyAdmin.loadBlock('tree',{}, function(){
		    if(ParsimonyAdmin.inProgress != "container") $("#treedom_" + ParsimonyAdmin.inProgress).trigger("click");
		});   
	    }
	    $(".container",ParsimonyAdmin.currentBody).each(function(){
		if($(this).find('.block:not("#content")').length==0) {
		    $(this).prepend('<div class="dropInContainer"><div class="dropInContainerChild">Id #' + $(this).get(0).id + ". " + t("Drop the blocks in this space") + '</div></div>');
		}else $(".dropInContainerChild:first",this).remove();
	    });
	},
	setCreationMode :   function (){
	    $('.sidebar,.panelblocks,.creation').show();
            $(".panelblocks").removeClass("active");
            $(".panelmodules").addClass("active");
	    ParsimonyAdmin.setMode("creation");  
	},
	setEditMode :   function (){
	    $('#right_sidebar,.panelblocks,.creation').hide();
            $('#left_sidebar').show();
	    $(".panelmodules").addClass("active");
	    ParsimonyAdmin.setMode("edit");
	},
	setPreviewMode :   function (){
	    $('.sidebar').hide();
	    ParsimonyAdmin.setMode("preview");
	},
        setMode :   function (mode){
            $("body").removeClass("previewMode modeMode creationMode").addClass(mode + "Mode");
            $(".switchMode").removeClass("selected");
            $("#" + mode + "Mode").addClass("selected");
	    /* Unload current mode if exists */
	    if(ParsimonyAdmin.currentMode.length > 0){
		var captitalizeOldMode = ParsimonyAdmin.currentMode[0].toUpperCase() + ParsimonyAdmin.currentMode.substring(1);
		ParsimonyAdmin["unload" + captitalizeOldMode + "Mode"]();
	    }
	    ParsimonyAdmin.currentMode = mode;
	    var captitalizeNewMode = mode[0].toUpperCase() + mode.substring(1);
	    /* Load new mode */
	    ParsimonyAdmin["load" + captitalizeNewMode + "Mode"]();
            ParsimonyAdmin.setCookie("mode",mode,999);
	},
	loadBlock: function(id, params, func){
            if(!params) params = {};
	    $.get(window.location.href.toLocaleString(),params , function(data) {
		$('#' + id).html($("<div>").append(data.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, "")).find("#" + id).html());
	    },func);
	//$('#' + id).load(window.location.toLocaleString() + " #" + id + " > div");
	},
	removeEmptyTextNodes: function(elem){
	    var children = elem.childNodes;
	    var child;
	    var len = children.length;
	    var whitespace = /^\s*$/;
	    for(var i = 0; i < len; i++){
		child = children[i];
		if(child.nodeType == 3){
		    if(whitespace.test(child.nodeValue)){
			elem.removeChild(child);
			i--;
			len--;
		    }
		}else if(child.nodeType == 1){
		    this.removeEmptyTextNodes(child);
		}
	    }
	}
    }
    var $lang = new Array;
    function t(val){
	if($lang[val]){
	    return $lang[val];
	}else{
	    return val;
	}  
    }
