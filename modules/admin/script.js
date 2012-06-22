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
	    ParsimonyAdmin.execResult(elmt);
	});

	$("#conf_box_content_iframe").on("load",function() {
	    ParsimonyAdmin.resizeConfBox();
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
	
	/*var x;
	var y;
	$(ParsimonyAdmin.currentBody).on('dragstart',function(event){
	var img = document.createElement("img");
	event.originalEvent.dataTransfer.setDragImage(img, 128,128);
	x =  event.originalEvent.clientX;
	y =  event.originalEvent.clientY;
	});
	$(ParsimonyAdmin.currentBody).parent().on('dragover',function(event){
	$(ParsimonyAdmin.currentBody).css( '-webkit-transform', 'perspective(' + $("#perspective").val() + 'px) rotate(' + $("#rotate").val() + 'deg) rotateX(' + ((event.originalEvent.clientY - y) + parseInt($("#rotatex").val())) + 'deg) rotateY(' + ((event.originalEvent.clientX - x) + parseInt($("#rotatey").val())) + 'deg) translateZ(' + $("#translatez").val() + 'px)' );
	return false;
	});
	$(ParsimonyAdmin.currentBody).parent().on('drop',function(event){
	$("#rotatey").val(((event.originalEvent.clientX - x) + parseInt($("#rotatey").val())));
	$("#rotatex").val(((event.originalEvent.clientY - y) + parseInt($("#rotatex").val())));
	return false;
	});*/
	
	/* Init tooltip */
	$(".tooltip").parsimonyTooltip({
	    triangleWidth:5
	});
       
	/* Set initial mode */
	var initialMode = ParsimonyAdmin.getCookie("mode");
	if(initialMode == 'creation'){
	    $("#switchCreationMode").trigger('click');
	}else if(initialMode == 'edit'){
	    $("#switchEditMode").trigger('click');
	}else if(initialMode == 'preview'){
	    $("#switchPreviewMode").trigger('click');
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
	    ParsimonyAdmin.addTitleParsiadminMenu("Traduction");
	    ParsimonyAdmin.addOptionParsiadminMenu('<span class="ui-icon ui-icon-pencil floatleft"></span><a href="#" class="action" rel="getViewTranslation" params="key=' + $(this).data("key") + '" title="Gestion des Traductions">Traduct</a>');
	});
	
	$(ParsimonyAdmin.currentBody).on('click.creation','a', function(e){
	    e.link = true;
	    e.preventDefault();
	    if(e.trad != true) ParsimonyAdmin.closeParsiadminMenu();
	    ParsimonyAdmin.addTitleParsiadminMenu("Link");
	    ParsimonyAdmin.addOptionParsiadminMenu('<span class="ui-icon ui-icon-extlink floatleft"></span><a href="javascript:ParsimonyAdmin.goToPage(\'' + $.trim($(this).text().replace("'","\\'")) + '\',\'' + $(this).attr('href') + '\');return false;">Go to the link</a>');
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
	if(typeof parsimonyDND == "object") $('.parsimonyDND',ParsimonyAdmin.currentBody).parsimonyDND('destroy');
	this.pluginDispatch("unloadCreationMode");
    },
    
    loadEditMode :   function(){
	if(typeof ParsimonyAdmin.wysiwyg == "string") ParsimonyAdmin.wysiwyg= new wysiwyg();
	ParsimonyAdmin.wysiwyg.init(".wysiwyg",["bold","underline","italic","justifyLeft","justifyCenter","justifyRight","strikeThrough","subscript","superscript","orderedList","unOrderedList","undo","redo","copy","paste","cut","outdent","indent","removeFormat","createLink","unlink","formatBlock","foreColor","hiliteColor"], document, ParsimonyAdmin.currentDocument);

	$(ParsimonyAdmin.currentBody).on('click.edit','a', function(e){
	    if($(this).attr("href").substring(0,1) != '#' && $(this).attr("href").substring(0,7) != 'http://'){
		e.preventDefault();
		ParsimonyAdmin.goToPage( $.trim($(this).text().replace("'","\\'")) , $(this).attr('href') );
	    }
	});
	isGood = true; // yeah
	$(".HTML5editorToolbar").on('mousedown.edit',function(e){
	    window['isGood'] = false;
	});
	$(".HTML5editorToolbar").on('mouseup.edit',function(e){
	    window['isGood'] = true;
	});
	
	$(".wysiwyg",ParsimonyAdmin.currentBody).on('blur.edit',function(e){
	    if (window['isGood']) {
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
		window['isGood'] = true;
	    }
	});
	
	$(ParsimonyAdmin.currentBody).on('click.edit',".editinline",function(){
	    $(this).attr('contentEditable', true);
	});
        
	$(".editinline",ParsimonyAdmin.currentBody).on('blur.edit',function(){
	    $(this).attr('contentEditable', false);
	    ParsimonyAdmin.postData(BASE_PATH + "admin/editInLine",{
		TOKEN: TOKEN,
		module: $(this).data('module'),
		model: $(this).data('model'),
		property: $(this).data('property'),
		id: $(this).data('id'),
		value: $(this).html()
	    },function(data){
		ParsimonyAdmin.execResult(data);
	    });
	});
	
	this.pluginDispatch("loadEditMode");
    }, 
    
    unloadEditMode :   function(){
	if(typeof ParsimonyAdmin.wysiwyg == "object") ParsimonyAdmin.wysiwyg.disable();
	$(ParsimonyAdmin.currentBody).off('.edit');
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
	    ParsimonyAdmin.displayConfBox(BASE_PATH + "admin/action",$(this).attr('title'),"TOKEN=" + TOKEN + "&idBlock=" + ParsimonyAdmin.inProgress + "&parentBlock=" + parentId + "&typeProgress=" + ParsimonyAdmin.typeProgress + "&action=" + $(this).attr('rel') +"&IDPage=" + $(".container_page",ParsimonyAdmin.currentBody).data('page') +"&" + $(this).attr('params'));
	    e.preventDefault();
	});

	$("#toolbar li.subMenu,#toolbar li.subSubMenu").hover(function(){
	    $("ul:first",this).show();
	    if($(this).hasClass("subSubMenu")) $("ul:first",this).css("left",$(this).width()-5 + "px");
	},function(){
	    $("ul:first",this).hide();
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
	eval(obj.eval);
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
	    if(confirm('Etes-vous sûr de vouloir supprimer le bloc ' + ParsimonyAdmin.inProgress + ' ?')==true){
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
	$("#conf_box").hide();
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
		$("#conf_box_form").append('<input type="hidden" name="popup" value="yes">');
		$("#conf_box_form").trigger("submit");
		$("#conf_box_content_iframe").show();
		$("#conf_box_content_inline").hide();
	    }else{
		$("#shelter").append($("#conf_box_content_inline").html());
		$("#conf_box_content_inline").show().append($(url));
		$("#conf_box_content_iframe").hide();
		$(url).show();
		$("#conf_box" ).css({
		    "display" : "block",
		    "width" : $(url).outerWidth() + "px"
		});
		$("#conf_box_content" ).css({
		    "height" : $(url).outerHeight() + "px"
		});
	    }
	},
	closeConfBox :   function (){
	    $("#conf_box").hide();
	    ParsimonyAdmin.hideOverlay();
	    $("#conf_box_title").empty();
	    $("#conf_box_content_iframe").attr("src","about:blank");
            
	},
	resizeConfBox : function(){
	    var doc = document.getElementById("conf_box_content_iframe").contentDocument;
	    if(doc.location.href != "about:blank"){
		var bodyIframe = $(doc);
		$( "#conf_box_content_iframe" ).add("#conf_box_content").css({
		    "width": bodyIframe.outerWidth() + "px",
		    "height": bodyIframe.outerHeight() + "px"
		});
		$( "#conf_box" ).css({
		    "width": bodyIframe.outerWidth() + "px"
		});
		$("#conf_box").show();
	    }
	},
	setConfBoxTitle :   function (title){
	    $("#conf_box_title").html(title);
	},
	changeBlockPosition : function (blockType,idBlock,idNextBlock,startIdParentBlock,stopIdParentBlock,startTypeCont,stopTypeCont,action){
	    if(typeof startIdParentBlock == "undefined" || typeof stopIdParentBlock == "undefined"){
		alert('Error in your DOM, perhaps an HTML tag isn\'t closed.');
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
	    ParsimonyAdmin.changeDeviceUpdate(device);
	    $("#info_themetype").text("Version " + device);
	    $('#parsiframe').attr("src", $('#parsiframe').attr("src"));
	},
	changeDeviceUpdate : function () {
	    var select = '';
	    var nb = 0;
	    $.each($.parseJSON(resultions[THEMETYPE]), function(i,item){
		if(nb==0) $('#changeres').val(i).trigger('change');
		select += '<li><a href="#" onclick="$(\'#changeres\').val(\'' + i + '\').trigger(\'change\');">' + item + ' (' + i + ')</a></li>';
		nb++;
	    });
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
		ParsimonyAdmin.loadBlock('tree',function(){
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
	loadBlock: function(id,func){
	    $.get(window.location.toLocaleString(), function(data) {
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
