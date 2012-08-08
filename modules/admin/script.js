var ParsimonyAdmin = {
    isInit : false,
    currentWindow : "",
    currentDocument : "",
    currentBody : "",
    inProgress :  "",
    typeProgress : "",
    wysiwyg : "",
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
	    $(this).val($(this).val().replace(/[^a-z_]+/,"").replace(" ",""));
	});
		
	$("#conf_box").draggable();

	$(document).on('click','#conf_box_wpopup', function(e){
	    var action = $("#conf_box_form input[name=action]").val();
	    $("#conf_box_form").attr('target','conf_box_content_popup' + action);
	    ParsimonyAdmin.closeConfBox();
	    window.open ($("#conf_box_content_iframe").attr('src'),'conf_box_content_popup' + action,"width=" + $("#conf_box_content_iframe").width() + ",height=" + $("#conf_box_content_iframe").height());
	    $("#conf_box_form").trigger("submit");
	    $("#conf_box_form").attr('target','conf_box_content_iframe');
	});
	
	var timer=setInterval(function resizeIframe() {
	    if(document.getElementById("changeres").value == "max"){
		var height = ParsimonyAdmin.currentDocument.body.offsetHeight;
		if(screen.height > height) height = screen.height - 28;
		document.getElementById("parsiframe").style.height = height + "px"
	    }
	}, 1000);
	
	this.pluginDispatch("initBefore");
    },

    initIframe :   function(){
	ParsimonyAdmin.currentWindow = document.getElementById("parsiframe").contentWindow;
	ParsimonyAdmin.currentDocument = ParsimonyAdmin.currentWindow.document;
	ParsimonyAdmin.currentBody = ParsimonyAdmin.currentDocument.body;
	ParsimonyAdmin.inProgress = "container";
	ParsimonyAdmin.updateUI();
	$(ParsimonyAdmin.currentBody).append('<link rel="stylesheet" type="text/css" href="' + BASE_PATH + 'admin/iframe.css">');
	ParsimonyAdmin.changeDeviceUpdate();
	
	/* Init tooltip */
	$(".tooltip").parsimonyTooltip({
	    triangleWidth:5
	});
       
	/* Set initial mode */
	var initialMode = ParsimonyAdmin.getCookie("mode");
	if(initialMode == 'edit'){
	    $("#switchEditMode").trigger('click');
	}else if(initialMode == 'preview'){
	    $("#switchPreviewMode").trigger('click');
	}else{
	    $("#switchCreationMode").trigger('click');
	}

	//override jQuery ready function to exec them with ajax portions
	$.fn.ready = function(a) {
	    ParsimonyAdmin.currentWindow.eval(" exec = " + a.toString()+";exec.call(window)");
	}
    //document.getElementById("parsiframe").contentWindow.$.fn.ready = function(a) {a.call(document.getElementById("parsiframe").contentWindow);}
    
	this.pluginDispatch("initIframe");
    }
    ,
    loadCreationMode :   function(){
	ParsimonyAdmin.unloadCreationMode();
	$(ParsimonyAdmin.currentBody).on('click.creation','.traduction', function(e){
	    e.trad = true;
	    ParsimonyAdmin.closeParsiadminMenu();
	    ParsimonyAdmin.addTitleParsiadminMenu(t('Translation'));
	    ParsimonyAdmin.addOptionParsiadminMenu('<span class="ui-icon ui-icon-pencil floatleft"></span><a href="#" class="action" rel="getViewTranslation" params="key=' + $(this).data("key") + '" title="Gestion des Traductions">'+ t('Translate') +'</a>');
	});
	
	$(ParsimonyAdmin.currentBody).on('click.creation','a', function(e){
	    e.link = true;
	    e.preventDefault();
	    if(e.trad != true) ParsimonyAdmin.closeParsiadminMenu();
	    ParsimonyAdmin.addTitleParsiadminMenu("Link");
	    ParsimonyAdmin.addOptionParsiadminMenu('<span class="ui-icon ui-icon-extlink floatleft"></span><a href="javascript:ParsimonyAdmin.goToPage(\'' + $.trim($(this).text().replace("'","\\'")) + '\',\'' + $(this).attr('href') + '\');return false;">'+ t('Go to the link') +'</a>');
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
	$(ParsimonyAdmin.currentBody).off('.creation');
	$('.parsimonyDND').parsimonyDND('destroy');
	$("#colorjack_square").hide();
	this.pluginDispatch("unloadCreationMode");
    },
    
    loadEditMode :   function(){
        $(".parsieditinline",ParsimonyAdmin.currentBody).addClass('usereditinline').attr("contenteditable", "true");
        $(".wysiwyg",ParsimonyAdmin.currentBody).addClass('activeEdit').attr("contenteditable", "true");
	if(typeof ParsimonyAdmin.wysiwyg == "string"){
            ParsimonyAdmin.wysiwyg= new wysiwyg();
            ParsimonyAdmin.wysiwyg.init(".wysiwyg",["bold","underline","italic","justifyLeft","justifyCenter","justifyRight","strikeThrough","subscript","superscript","orderedList","unOrderedList","undo","redo","copy","paste","cut","outdent","indent","removeFormat","createLink","unlink","formatBlock","foreColor","hiliteColor"], document, ParsimonyAdmin.currentDocument);
        }
        $(".HTML5editorToolbar").hide();
        
	$(ParsimonyAdmin.currentDocument).on('click.edit','a', function(e){
            e.preventDefault();
	    if($(this).attr("href").substring(0,1) != '#' && $(this).attr("href").substring(0,7) != 'http://' && $(".usereditinline",this).length == 0){
		ParsimonyAdmin.goToPage( $.trim($(this).text().replace("'","\\'")) , $(this).attr('href') );
	    }
	});
	
	$(".wysiwyg.activeEdit",ParsimonyAdmin.currentBody).on('blur.edit',function(e){
            $(".HTML5editorToolbar").hide();
            var module = ParsimonyAdmin.currentWindow.THEMEMODULE;
            var theme = ParsimonyAdmin.currentWindow.THEME;
            var idPage = '';
            if(ParsimonyAdmin.whereIAm(this.id) == 'page'){
                theme = '';
                module = ParsimonyAdmin.currentWindow.MODULE;
                idPage = $(".container_page",ParsimonyAdmin.currentBody).data('page');
            }
            $.post(BASE_PATH + module + '/callBlock',{
                module:module, 
                idPage:idPage,
                theme: theme, 
                id:this.id, 
                method:'saveWYSIWYG', 
                args:"html=" + $(this).html()
                },function(data){
                ParsimonyAdmin.execResult(data);
            });
	});
	
	this.pluginDispatch("loadEditMode");
    }, 
    
    unloadEditMode :   function(){
	//if(typeof ParsimonyAdmin.wysiwyg == "object") ParsimonyAdmin.wysiwyg.disable();
	$(ParsimonyAdmin.currentDocument).off('.edit');
        $(ParsimonyAdmin.currentBody).off('.edit');
        $(".wysiwyg.activeEdit",ParsimonyAdmin.currentBody).off();
        $(".parsieditinline",ParsimonyAdmin.currentBody).removeClass('usereditinline').attr("contenteditable", "false");
        $(".wysiwyg",ParsimonyAdmin.currentBody).removeClass('activeEdit').attr("contenteditable", "false");
        $(".HTML5editorToolbar").hide();
	this.pluginDispatch("unloadEditMode");
    },
    loadPreviewMode :   function(){
	ParsimonyAdmin.closeLeftPanel();
	ParsimonyAdmin.closeRightPanel();
	$(ParsimonyAdmin.currentBody).on('click.preview','a', function(e){
	    if($(this).attr("href").substring(0,1) != '#' && $(this).attr("href").substring(0,7) != 'http://'){
		e.preventDefault();
		ParsimonyAdmin.goToPage( $.trim($(this).text().replace("'","\\'")) , $(this).attr('href') );
	    }
	});
	this.pluginDispatch("loadPreviewMode");
    },
    unloadPreviewMode :   function(){
	$(ParsimonyAdmin.currentBody).off('.preview');
	this.pluginDispatch("unloadPreviewMode");
    },
    init :   function(){
        
	ParsimonyAdmin.isInit = true;

	$(document).on('click','#menu a',function(e){
	    ParsimonyAdmin.closeParsiadminMenu();
	});

	$(document).add('#config_tree_selector').on('click',".action", function(e){
	    var parentId = '';
	    var inProgress = $("#treedom_" + ParsimonyAdmin.inProgress);
	    if(inProgress.length > 0){
		if(inProgress.parent().closest(".container").attr("id") == "treedom_content") parentId = inProgress.parent().closest("#treedom_content").data('page');
		else parentId = inProgress.parent().closest(".container").attr('id').replace("treedom_","");
	    }
	    ParsimonyAdmin.displayConfBox(BASE_PATH + "admin/action",($(this).data('title') || $(this).attr('title') || $(this).data('tooltip')),"TOKEN=" + TOKEN + "&idBlock=" + ParsimonyAdmin.inProgress + "&parentBlock=" + parentId + "&typeProgress=" + ParsimonyAdmin.typeProgress + "&action=" + $(this).attr('rel') +"&IDPage=" + $(".container_page",ParsimonyAdmin.currentBody).data('page') +"&" + $(this).attr('params'));
	    e.preventDefault();
	});

	/* CTRL-S*/
	var isCtrl = false;
	$(window).keydown(function(e) {
	    if(e.ctrlKey) isCtrl = true;     
	    if(e.keyCode == 83 && isCtrl) {
		$("form",$('#conf_box_content_iframe').contents().find("body")).trigger("submit");
		return false;
	    }
	}).keyup(function(e) {
	    if(e.ctrlKey) isCtrl = false;
	});

	$(document).on('click',".explorer",function(event){
	    ParsimonyAdmin.displayConfBox(BASE_PATH + "admin/explorer","Explorer","idelmt=" + $(this).attr('rel'));
	});
	
	ParsimonyAdmin.hideOverlay();
	ParsimonyAdmin.removeEmptyTextNodes(document.body);
	this.pluginDispatch("init");
    }
    ,
    goToPage :   function (pageTitle,pageUrl, isHistory){
	ParsimonyAdmin.unloadCreationMode();
	ParsimonyAdmin.unloadPreviewMode();
	if(pageUrl.substring(0,BASE_PATH.length) != BASE_PATH) pageUrl = BASE_PATH + pageUrl;
	pageUrl = $.trim(pageUrl);
	if(pageUrl.indexOf('?parsiframe=ok') == -1) pageUrl += '?parsiframe=ok';
	$('#parsiframe').attr('src', pageUrl);
	return false;
    },
    execResult :   function (obj){
	if (obj.notification == null) 
	    var obj = jQuery.parseJSON(obj);
	if(obj.eval != null) eval(obj.eval);
	var headParsiFrame = $('#parsiframe').contents().find("head");
	if(obj.jsFiles){
	    obj.jsFiles = jQuery.parseJSON(obj.jsFiles);
	    $.each(obj.jsFiles, function(index, url) { 
		if (!$("script[scr='" + url + "']",headParsiFrame).length) {
		    $(ParsimonyAdmin.currentBody).append('<script type="text/javascript" src="' + url + '"></script>');
		}
	    });
	}
	if(obj.CSSFiles){
	    obj.CSSFiles = jQuery.parseJSON(obj.CSSFiles);
	    $.each(obj.CSSFiles, function(index, url) {
		if (!$('link[href="' + url + '"]',headParsiFrame).length) {
		    $(ParsimonyAdmin.currentBody).append('<link rel="stylesheet" type="text/css" href="' + url + '">');
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
	    var block = $( "#" + idBlockAfter ,ParsimonyAdmin.currentBody).parent().parent().parent();
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
	var blockObj = $("#" + idBlock,ParsimonyAdmin.currentBody);
	var blockTreeObj = $("#treedom_" + idBlock);
        
	$(".selection-block",ParsimonyAdmin.currentBody).removeClass("selection-block");
	$(".selection-container",ParsimonyAdmin.currentBody).removeClass("selection-container");
	ParsimonyAdmin.inProgress = idBlock;
	ParsimonyAdmin.typeProgress = ParsimonyAdmin.whereIAm(ParsimonyAdmin.inProgress);
	$("#tree .tree_selector,#tree .container").css('background','transparent');
	$("#" + ParsimonyAdmin.inProgress,ParsimonyAdmin.currentBody).addClass("selection-block").parent(".container").addClass("selection-container");

	blockTreeObj.css('background','#999');
	if(idBlock=="container" || blockObj.hasClass('container_page')) $("#right_sidebar #config_tree_selector").addClass("restrict");
	else $("#right_sidebar #config_tree_selector").removeClass("restrict");
	blockTreeObj.prepend($("#right_sidebar #config_tree_selector").show());
        
    },
    whereIAm :   function (idBlock){
	var where = "theme";
	if($("#" + idBlock,ParsimonyAdmin.currentBody).length > 0){
	    if($("#" + idBlock,ParsimonyAdmin.currentBody).parent().closest("#content").length > 0 ){
		where = "page";
	    }
	}else{
	    if(idBlock == 'dropInTree') var obj = $("#dropInTree");
	    else var obj = $("#treedom_" + idBlock);
	    if(obj.parent().closest("#treedom_content").length > 0){
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
	$( "#conf_box_overlay").hide();
    },
    displayConfBox :   function (url,title,params,modal){
        $("#conf_box_load").show();
	if(typeof modal == "undefined" || modal == true) ParsimonyAdmin.showOverlay();
	ParsimonyAdmin.setConfBoxTitle(title);
        $("#conf_box,#conf_box_content" ).removeAttr("style");
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
                $("#conf_box_form").append('<input type="hidden" name="popup" value="yes">');
                $("#conf_box_form").trigger("submit");
                $("#conf_box_content_iframe").show();
                $("#conf_box_content_inline").hide();
            }else{
                $("#shelter").append($("#conf_box_content_inline").html());
                $("#conf_box_content_inline").show().append($(url));
                $("#conf_box_content_iframe").hide();
                $(url).show();
            }
	},
	closeConfBox :   function (){
	    $("#conf_box").hide();
	    ParsimonyAdmin.hideOverlay();
	    $("#conf_box_title").empty();
	    $("#conf_box_content_iframe").attr("src","about:blank");
            
	},
	resizeConfBox : function(){
            $("#conf_box_load").hide();
            $( "#conf_box_content_iframe" ).removeAttr("style");
	    var doc = document.getElementById("conf_box_content_iframe").contentDocument;
	    if(doc.location.href != "about:blank"){
		$( "#conf_box_content_iframe" ).css({
		    "width": ($(".adminzonecontent",doc).outerWidth() + $(".adminzonemenu",doc).outerWidth()) + "px",
		    "height": $("body",doc).outerHeight() + "px"
		});
                $("#conf_box").show();
	    }
	},
	setConfBoxTitle :   function (title){
	    $("#conf_box_title").html(title);
	},
	changeBlockPosition : function (blockType,idBlock,idNextBlock,startIdParentBlock,stopIdParentBlock,startTypeCont,stopTypeCont,action){
	    if(typeof startIdParentBlock == "undefined" || typeof stopIdParentBlock == "undefined"){
		alert(t('Error in your DOM, perhaps an HTML tag isn\'t closed.'));
		return false
		};
	    if(idNextBlock == undefined || idNextBlock==idBlock) idNextBlock = "last";
	    ParsimonyAdmin.postData(BASE_PATH + "admin/" + action,{
		TOKEN: TOKEN ,
		popBlock: blockType ,
		idBlock: idBlock,
		id_next_block:idNextBlock ,
		startParentBlock: startIdParentBlock ,
		parentBlock:stopIdParentBlock ,
		start_typecont:startTypeCont ,
		stop_typecont:stopTypeCont ,
		IDPage: $(".container_page",ParsimonyAdmin.currentBody).data('page')
	    },function(data){
		ParsimonyAdmin.execResult(data);
		ParsimonyAdmin.returnToShelter();
		ParsimonyAdmin.updateUI();
	    });
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
	    $("#info_themetype").text("Version " + device);
	    $('#parsiframe').attr("src", $('#parsiframe').attr("src"));
	    ParsimonyAdmin.loadBlock('panelblocks');
	},
	changeDeviceUpdate : function () {
	    var select = '';
	    var nb = 0;
	    var changeres = $('#changeres');
	    $.each($.parseJSON(resultions[THEMETYPE]), function(i,item){
		if(changeres.get(0).value == "" && nb == 0) changeres.val(i);
		select += '<li><a href="#" onclick="$(\'#changeres\').val(\'' + i + '\').trigger(\'change\');">' + item + ' (' + i + ')</a></li>';
		nb++;
	    });
	    changeres.trigger('change');
	    $('#listres').empty().html(select).trigger('change');
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
	openLeftPanel : function () {
	    $("#left_sidebar").removeClass('close');
	    $("#left_sidebar .contenttab").show();
	    $("#openleftslide span").removeClass('ui-icon-circle-arrow-e').addClass('ui-icon-circle-arrow-w');
	    ParsimonyAdmin.setCookie("leftToolbarOpen",1,999);
	},
	closeLeftPanel : function () {
	    $("#left_sidebar").addClass('close');
	    $("#left_sidebar .contenttab").hide();
	    $("#openleftslide span").removeClass('ui-icon-circle-arrow-w').addClass('ui-icon-circle-arrow-e');
	    ParsimonyAdmin.setCookie("leftToolbarOpen",0,999);
	},
	openRightPanel : function () {
	    $("#right_sidebar").removeClass('close');
	    $("#right_sidebar .contenttab").show();
	    $("#openrightslide span").removeClass('ui-icon-circle-arrow-w').addClass('ui-icon-circle-arrow-e');
	    ParsimonyAdmin.setCookie("rightToolbarOpen",1,999);
	},
	closeRightPanel : function () {
	    $("#right_sidebar").addClass('close');
	    $("#right_sidebar .contenttab").hide();
	    $("#openrightslide span").removeClass('ui-icon-circle-arrow-e').addClass('ui-icon-circle-arrow-w');
	    ParsimonyAdmin.setCookie("rightToolbarOpen",0,999);
	},
	openRightTreePanel : function () {
	    ParsimonyAdmin.openRightPanel();
	    $("#panelcss").hide();
	    $("#paneltree").show();
	    $(".paneltree").addClass('active');
	    $(".panelcss").removeClass('active');
	    ParsimonyAdmin.setCookie("rightToolbarPanel","paneltree",999);
	},
	openRightCSSPanel : function () {
	    ParsimonyAdmin.openRightPanel();
	    $(".panelcss").addClass('active');
	    $(".paneltree").removeClass('active');
	    $("#paneltree").hide();
	    $("#panelcss").show();
	    ParsimonyAdmin.setCookie("rightToolbarPanel","panelcss",999);
	},
	openLeftModulesPanel : function () {
	    ParsimonyAdmin.openLeftPanel();
	    $("#left_sidebar #panelmodules").show();
	    $("#left_sidebar #panelblocks").hide();
	    $(".panelmodules").addClass('active');
	    $(".panelblocks").removeClass('active');
	    ParsimonyAdmin.setCookie("leftToolbarPanel","panelmodules",999);
	},
	openLeftBlocksPanel : function () {
	    ParsimonyAdmin.openLeftPanel();
	    $(".panelmodules").removeClass('active');
	    $(".panelblocks").addClass('active');
	    $("#left_sidebar #panelmodules").hide();
	    $("#left_sidebar #panelblocks").show();
	    ParsimonyAdmin.setCookie("leftToolbarPanel","panelblocks",999);
	},
	openParsiadminMenu : function (x,y) {
	    var off = $("#parsiframe").offset();
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
	trigger : function (selector,evt){
	    $(selector).trigger(evt);
	},
	reloadIframe : function (){
	    $.get("index.html?parsiframe=ok",
		function(data){
		    $('#parsiframe').contents().find("html").replaceWith(data);
		});
	},
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
	    ParsimonyAdmin.loadCreationMode();
	    ParsimonyAdmin.unloadEditMode();
	    ParsimonyAdmin.unloadPreviewMode();
	    $('.creation,.panelblocks,#left_sidebar').show();
	    $('#switchCreationMode').addClass("selected");
	    $('#switchPreviewMode').removeClass("selected");
	    $('#switchEditMode').removeClass("selected");
	    ParsimonyAdmin.setCookie("mode","creation",999);
	},
	setEditMode :   function (){
	    ParsimonyAdmin.unloadCreationMode();
	    ParsimonyAdmin.unloadPreviewMode();
	    ParsimonyAdmin.loadEditMode();
	    $('.creation,.panelblocks').hide();
	    $('#switchEditMode').addClass("selected");
	    $('#switchCreationMode').removeClass("selected");
	    $('#switchPreviewMode').removeClass("selected");
	    $("#left_sidebar #panelmodules,#left_sidebar").show();
	    $("#left_sidebar #panelblocks").hide();
	    $(".panelmodules").addClass('active');
	    ParsimonyAdmin.setCookie("mode","edit",999);
	},
	setPreviewMode :   function (){
	    ParsimonyAdmin.unloadCreationMode();
	    ParsimonyAdmin.unloadEditMode();
	    ParsimonyAdmin.loadPreviewMode();
	    $('.creation,.panelblocks,#left_sidebar').hide();
	    $('#switchPreviewMode').addClass("selected");
	    $('#switchEditMode').removeClass("selected");
	    $('#switchCreationMode').removeClass("selected");
	    ParsimonyAdmin.setCookie("mode","preview",999);
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
	    var lent = children.length;
	    var i = 0;
	    var whitespace = /^\s*$/;
	    for(; i < lent; i++){
		child = children[i];
		if(child.nodeType == 3){
		    if(whitespace.test(child.nodeValue)){
			elem.removeChild(child);
			i--;
			lent--;
		    }
		}else if(child.nodeType == 1){
		    ParsimonyAdmin.removeEmptyTextNodes(child);
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
