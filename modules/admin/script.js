var ParsimonyAdmin = {
    isInit : false,
    currentWindow : "",
    currentDocument : "",
    currentBody : "",
    inProgress :  "",
    typeProgress : "",
    startTypeCont : "theme",
    startIdParentBlock : "",
    stopIdParentBlock : "",
    idNextBlock : "",
    idBlock : "",
    blockType : "",
    moveBlock : false,
    dragLastDomId : "",
    dragMiddle : 0,
    csseditors: [],
	
    initBefore :   function(){
        $("#toolbars").live('change',function(){
            var style = $('#toolbars option:selected').attr('style');
            $("#toolbars").attr('style',style);
        });

        $('.sidebar').draggable({
            handle: ".handle",
            zIndex: 999998,
            containment: 'body',
            start:function(){
                ParsimonyAdmin.showOverlay(0);
                $(this).addClass('notransition')
            }, 
            stop:function(){
                ParsimonyAdmin.hideOverlay();
                $(this).removeClass('notransition');
                ParsimonyAdmin.setCookie("leftToolbarCoordX",$("#left_sidebar").css('left'),999);
                ParsimonyAdmin.setCookie("leftToolbarCoordY",$("#left_sidebar").css('top'),999);
                ParsimonyAdmin.setCookie("rightToolbarCoordX",$("#right_sidebar").css('left'),999);
                ParsimonyAdmin.setCookie("rightToolbarCoordY",$("#right_sidebar").css('top'),999);
            }
        });
        $( '.sidebar' ).resizable({
            start:function(event, ui){
                ParsimonyAdmin.showOverlay(0);
                $(this).addClass('notransition');
            },
            helper: "ui-resizable-helper",
            handles: {
                'e': '.ui-icon-arrowthick-2-e-w',
                'w': '.ui-icon-arrowthick-2-e-w'
            }, 
            stop:function(){
                ParsimonyAdmin.hideOverlay();
                $(this).removeClass('notransition');
                $( '#right_sidebar').css('height','auto');
                $( '#right_sidebar').css('position','fixed');
                ParsimonyAdmin.setCookie("leftToolbarX",$("#left_sidebar").css('width'),999);
                ParsimonyAdmin.setCookie("rightToolbarX",$("#right_sidebar").css('width'),999);
            }
        });
        
        $('#admin').on('click',".revert",function () {
            var id = $( this).parent().parent().get(0).id;
            $("#" + id).attr("style","");
            if(id=="left_sidebar"){
                ParsimonyAdmin.setCookie("leftToolbarCoordX","0",999);
                ParsimonyAdmin.setCookie("leftToolbarCoordY","0",999);
                ParsimonyAdmin.setCookie("leftToolbarX","209px",999);
            }else{
                ParsimonyAdmin.setCookie("rightToolbarCoordX","0",999);
                ParsimonyAdmin.setCookie("rightToolbarCoordY","0",999);
                ParsimonyAdmin.setCookie("rightToolbarX","230px",999);
            }
        });
			
        $(".modeleajout").click(function(e){
            e.preventDefault();
            e.stopPropagation();
            ParsimonyAdmin.displayConfBox(BASE_PATH + "admin/action",t("Data") + " : "+ $(this).attr('rel').split(" - ")[1],"TOKEN=" + TOKEN + "&model=" + $(this).attr('rel') + "&action=getViewAdminModel");
        });

        $("#ajaxhack").load(function() {
            var elmt = $(this).contents().find('body').text();
            ParsimonyAdmin.execResult(elmt);
        });

        $("#conf_box_content_iframe").load(function() {
            if($(this).get(0).contentDocument.location.href != "about:blank"){
                var bodyIframe = $(this).contents().find('body');
                $( this ).add("#conf_box_content").css({
                    "width": bodyIframe.outerWidth() + "px",
                    "height": bodyIframe.outerHeight() + "px"
                });
                $( "#conf_box" ).css({
                    "width": bodyIframe.outerWidth() + "px"
                });
                $("#conf_box").show();
            }
        });
		
        $(document).on('mouseover',"#admin", function(e) {
            if(ParsimonyAdmin.moveBlock) ParsimonyAdmin.returnToShelter();
        });
		
        /* Orientation and resolution */
        $("#toolbar").on('change','#changeres', function(e) {
	    var bodyIframe = ParsimonyAdmin.currentDocument.body;
            var res = $(this).val();
	    $("#currentRes").text(res);
            if(res=='max'){
                var height = bodyIframe.offsetHeight + 250;
                if(screen.height > height) height = screen.height - 28;
                $("#parsiframe").css({
                    "width":  "100%",
                    "height": height + "px"
                });
                return true;
            }
            res = res.split(/x/);
            if($("#changeorientation").length == 0 || ($("#changeorientation").val()=='portrait' && ParsimonyAdmin.getCookie("landscape") == 'portrait')){
                $("#parsiframe").css({
                    "width": res[0] + "px",
                    "height": res[1] + "px"
                });
               /* $("body").css({
                    "max-height": res[1] + "px"
                });*/
            }else{ 
                $("#parsiframe").css({
                    "width": res[1] + "px",
                    "height": res[0] + "px"
                });
                /*$("body").css({
                    "max-height": res[0] + "px"
                });*/
            }
            ParsimonyAdmin.setCookie("screenX",res[0],999);
            ParsimonyAdmin.setCookie("screenY",res[1],999);
            ParsimonyAdmin.setCookie("landscape",$("#changeorientation").val(),999);
	    $(bodyIframe).removeClass("landscape").removeClass("portrait");
	    $(bodyIframe).addClass($("#changeorientation").val());
	    
        });
        
		
        $("#toolbar").on('change','#changeorientation', function(e) {
            ParsimonyAdmin.setCookie("landscape",$("#changeorientation").val(),999);
            $("#changeres").trigger("change");
        });
		
        //Open & Close Panels 
        $("#right_sidebar").on('click',"#openrightslide",function(){
            if(!$(this).parent().parent().hasClass("close")) ParsimonyAdmin.closeRightPanel();
            else ParsimonyAdmin.openRightPanel();
        });
        $("#left_sidebar").on('click',"#openleftslide",function(){
            if(!$(this).parent().parent().hasClass("close")) ParsimonyAdmin.closeLeftPanel();
            else ParsimonyAdmin.openLeftPanel();
        });
			
        /* Tabs */
        $('.admdesign,.admmodules').addClass('active');

        $('#left_sidebar').on('click','.panelmodules', function(){
            ParsimonyAdmin.openLeftModulesPanel();
        });
        $('#left_sidebar').on('click','.panelblocks',function(){
            ParsimonyAdmin.openLeftBlocksPanel();
        });
        $('#right_sidebar').on('click','.panelcss',function(){
            ParsimonyAdmin.openRightCSSPanel();
        });
        $('#right_sidebar').on('click','.paneltree',function(){
            ParsimonyAdmin.openRightTreePanel();
        });

        $("#dialog-id").keyup(function(){
            $(this).val($(this).val().replace(/[^a-z_]+/,"").replace(" ",""));
        });
		
        $("#conf_box").draggable();
        
        /* Help on Tree*/
        $('#right_sidebar').on('click','#treelegend',function(){
            $('#treelegend2').slideToggle();
        });
        $('#right_sidebar').on('click','.arrow_tree',function(event){
            event.stopPropagation();
            $(this).toggleClass('down');
            $(this).nextAll('ul,li').toggleClass('none');
        });
        
        /* Open CSS Filepath*/
        $('#panelcss').on('click','#opencssfilepath',function(){
            $('#opencssfilepath2').slideToggle();
        }); 
		
        $('#admin').on('click','.ssTab',function(){
            $(this).parent().parent().find("ul").hide();
            $(this).parent().parent().find("." + $(this).attr('rel')).show();
            $(this).parent().parent().find(".ssTab").removeClass('active');
            $(this).addClass('active');
        });
        
        document.getElementById('admintoolbar').onselectstart = new Function ("return false");
        
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
	
        $('#treedom_container').attr('title','Site structure');
        $('#treedom_content').attr('title','Dynamic content');
        $(".tooltip").parsimonyTooltip({
            triangleWidth:5
        });
       
       var initialMode = ParsimonyAdmin.getCookie("mode");
        if(initialMode == 'creation'){
            $("#switchCreationMode").trigger('click');
        }else if(initialMode == 'edit'){
            $("#switchEditMode").trigger('click');
        }else if(initialMode == 'preview'){
            $("#switchPreviewMode").trigger('click');
        }
        
        var styleSheets = ParsimonyAdmin.currentDocument.styleSheets;
        $("#changecsspath").html('');
        for (var i = 0; i < styleSheets.length; i++){
            if(styleSheets[i].href && styleSheets[i].href.match(new RegExp("/" + window.location.host + "/")) && !styleSheets[i].href.match(new RegExp("/" + window.location.host + BASE_PATH + "lib")) && styleSheets[i].href != "http://" + window.location.host + BASE_PATH + 'admin/iframe.css' )
                $("#changecsspath").append("<option>" + styleSheets[i].href.replace("http://" + window.location.host,"").substring(BASE_PATH.length) + "</option>");
        }

        //override jQuery ready function to exec them with ajax portions
        $.fn.ready = function(a) {
            document.getElementById("parsiframe").contentWindow.eval(" exec = " + a.toString()+";exec.call(window)");
        }
    //document.getElementById("parsiframe").contentWindow.$.fn.ready = function(a) {a.call(document.getElementById("parsiframe").contentWindow);}
	
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
		
        /*$(ParsimonyAdmin.currentBody).on('click',function(e){ 
      var elem = document.getElementById("parsiframe").contentWindow.document.elementFromPoint ( e.pageX , e.pageY );
    //console.log ( $(elem).closest(".block") );
$this = $(elem).closest(".block").get(0);*/
        $(ParsimonyAdmin.currentBody).on('click.creation','.block', function(e){
            e.stopPropagation();
            ParsimonyAdmin.selectBlock(this.id);
            
            if(e.trad != true && e.link != true) ParsimonyAdmin.closeParsiadminMenu();
            ParsimonyAdmin.addTitleParsiadminMenu('#' + ParsimonyAdmin.inProgress);
            ParsimonyAdmin.addOptionParsiadminMenu('<a href="#" class="action" rel="getViewConfigBlock" title="Configuration"><span class="ui-icon ui-icon-wrench floatleft"></span>'+ t('Configure') +'</a>');
            ParsimonyAdmin.addOptionParsiadminMenu('<a href="#" class="cssblock"><span class="ui-icon ui-icon-pencil floatleft"></span>'+ t('Design') +'</a>');
            if(this.id != "container") ParsimonyAdmin.addOptionParsiadminMenu('<a href="#" draggable="true" class="move_block" style="cursor:move"><span class="ui-icon ui-icon-arrow-4 floatleft"></span>'+ t('Move') +'</a>');
            if(this.id != "container" && this.id != "content") ParsimonyAdmin.addOptionParsiadminMenu('<a href="#" class="config_destroy"><span class="ui-icon ui-icon-closethick floatleft"></span>'+ t('Delete') +'</a>');
            ParsimonyAdmin.openParsiadminMenu(e.pageX || ($(window).width()/2),e.pageY || ($(window).height()/2));
        });
        
        $(ParsimonyAdmin.currentBody).add('#paneltree').on('dragover.creation dragenter.creation','.marqueurdragndrop', function(e) {
            e.stopImmediatePropagation();
            return false;
        });
	
        $(ParsimonyAdmin.currentBody).on('mouseover.creation',".block", function(event) {
            event.stopImmediatePropagation();
	    var offset = $(this).offset();
	    var offsetFrame = $("#parsiframe").offset();
	    if(ParsimonyAdmin.inProgress != this.id) $("#blockOverlay").css({"display":"block","top":offset.top + offsetFrame.top + "px","left":offset.left + offsetFrame.left + "px","width":$(this).outerWidth() + "px","height":$(this).outerHeight() + "px" })
	    else $("#blockOverlay").hide();
        });
	
	$(document).on('mouseover.creation',"body", function(event) {
	    $("#blockOverlay").hide();
        });
	
        $(ParsimonyAdmin.currentBody).add('#paneltree').on('drop.creation','.container,.tree_selector',function( event ){
            ParsimonyAdmin.stopDragging();
            event.stopPropagation();
            var evt = event.originalEvent;
            evt.stopPropagation();
            var toto = $( "#dropInPage" ,ParsimonyAdmin.currentBody);
            if(toto.is(':visible')){
                if(toto.closest(".container").hasClass("container_page")) ParsimonyAdmin.stopIdParentBlock = toto.closest(".container").data('page');
                else ParsimonyAdmin.stopIdParentBlock = toto.closest(".container").attr('id');
                ParsimonyAdmin.idNextBlock = toto.next(".block").attr('id');
                if(evt.dataTransfer.getData('text/plain') == 'addBlock'){
                    ParsimonyAdmin.displayConfBox("#dialog","Entrez un identifiant pour ce nouveau bloc");
		    $("#dialog-id").val('').trigger("focus");
                }
                else if(evt.dataTransfer.getData('text/plain') == 'moveBlock'){
                    if(ParsimonyAdmin.idBlock == '' || ParsimonyAdmin.idNextBlock == '' || ParsimonyAdmin.startIdParentBlock == '' || ParsimonyAdmin.stopIdParentBlock == '' || ParsimonyAdmin.startTypeCont == '' || ParsimonyAdmin.whereIAm("dropInTree") == '') alert("stop");
                    ParsimonyAdmin.changeBlockPosition('',ParsimonyAdmin.idBlock,ParsimonyAdmin.idNextBlock,ParsimonyAdmin.startIdParentBlock,ParsimonyAdmin.stopIdParentBlock,ParsimonyAdmin.startTypeCont,ParsimonyAdmin.whereIAm("dropInTree"),"moveBlock");
                }
            }
        });
        
        $(document).on("keypress.creation",'#dialog-id',function(e){
            var code = e.keyCode || e.which; 
            if(code == 13) {
                $("#dialog-ok").trigger("click");
            }
        });
            
        $(ParsimonyAdmin.currentBody).on('mouseover.creation mouseout.creation','#menu div',function(e){ /* todo fixer */
            if (e.type == 'mouseover') {
                $($(this).text()).not("#admin " + $(this).text()).addClass("cssselectorviewer");
            } else {
                $($(this).text()).not("#admin " + $(this).text()).removeClass("cssselectorviewer");
            }
        });
	
        //highlight link on list page
        var src = document.getElementById("parsiframe").contentWindow.location.toLocaleString().replace("http://","");
        var src = src.substring(src.indexOf(BASE_PATH)).replace("?parsiframe=ok","").replace("parsiframe=ok","");
        var itemLink = $('.sublist[data-url="' + src + '"]');
        if(itemLink.length > 0){
            $(".sublist.selected").removeClass('selected');
            itemLink.addClass('selected');
        }

    }, 

    unloadCreationMode :   function(){
        $(".selection-block",ParsimonyAdmin.currentBody).removeClass("selection-block");
        $(".selection-container",ParsimonyAdmin.currentBody).removeClass("selection-container");
        ParsimonyAdmin.closeParsiadminMenu();
        $(ParsimonyAdmin.currentBody).off('.creation');
        $('.parsimonyDND',ParsimonyAdmin.currentBody).parsimonyDND('destroy');
    },
    
    loadEditMode :   function(){
        HTML5editor.init(".wysiwyg",["bold","underline","italic","justifyLeft","justifyCenter","justifyRight","strikeThrough","subscript","superscript","orderedList","unOrderedList","undo","redo","copy","paste","cut","outdent","indent","removeFormat","createLink","unlink","formatBlock","foreColor","hiliteColor"], document, ParsimonyAdmin.currentDocument);

        $(ParsimonyAdmin.currentBody).on('click.edit','a', function(e){
            if($(this).attr("href").substring(0,1) != '#' && $(this).attr("href").substring(0,7) != 'http://'){
                e.preventDefault();
                ParsimonyAdmin.goToPage( $.trim($(this).text().replace("'","\\'")) , $(this).attr('href') );
            }
        });
	isGood = true; // yeah
	$("#HTML5editorToolbar").on('mousedown.edit',function(e){
	     window['isGood'] = false;
	});
	$("#HTML5editorToolbar").on('mouseup.edit',function(e){
	     window['isGood'] = true;
	});
	
	$(".wysiwyg",ParsimonyAdmin.currentBody).on('blur.edit',function(e){
	    console.log("2");
	    if (window['isGood']) {
		var module = MODULE;
		var theme = THEME;
		var idPage = '';
		if(ParsimonyAdmin.whereIAm(this.id) == 'page'){
		    theme = '';
		    module = THEMEMODULE;
		    idPage = $(".container_page",ParsimonyAdmin.currentBody).data('page');
		}
		$.post(BASE_PATH + 'core/callBlock',{module:module, idPage:idPage,theme: theme, id:this.id, method:'saveWYSIWYG', args:"html=" + $(this).html()},function(data){
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
    }, 
    
    unloadEditMode :   function(){
        HTML5editor.disable();
        $(ParsimonyAdmin.currentBody).off('.edit');
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
    },
    unloadPreviewMode :   function(){
        $(ParsimonyAdmin.currentBody).off('.preview');
    },
    init :   function(){
        
        ParsimonyAdmin.isInit = true;

        $(document).on('click','#menu a',function(e){
            ParsimonyAdmin.closeParsiadminMenu();
        });

        $(document).add('#config_tree_selector').on('click',".action", function(e){
            if($("#treedom_" + ParsimonyAdmin.inProgress).parent().closest(".container").attr("id")=="treedom_content") var parentId = $("#treedom_" + ParsimonyAdmin.inProgress).parent().closest("#treedom_content").data('page');
                else var parentId = $("#treedom_" + ParsimonyAdmin.inProgress).parent().closest(".container").attr('id').replace("treedom_","");
            ParsimonyAdmin.displayConfBox(BASE_PATH + "admin/action",$(this).attr('title'),"TOKEN=" + TOKEN + "&idBlock=" + ParsimonyAdmin.inProgress + "&parentBlock=" + parentId + "&typeProgress=" + ParsimonyAdmin.typeProgress + "&action=" + $(this).attr('rel') +"&IDPage=" + $(".container_page",ParsimonyAdmin.currentBody).data('page') +"&" + $(this).attr('params'));
            e.preventDefault();
        });

        $('#right_sidebar').on('click','.tree_selector', function(event){
            event.stopPropagation(); 
            ParsimonyAdmin.selectBlock(this.id.split("treedom_")[1]);
            if($("#" + this.id.split("treedom_")[1],ParsimonyAdmin.currentBody).length > 0){
                $("body").animate({
                    scrollTop : $("#" + this.id.split("treedom_")[1],ParsimonyAdmin.currentBody).offset().top -50
                },"fast");
            }
        });
	
        $('#right_sidebar').on('mouseenter','.tree_selector', function(event){
            event.stopPropagation();
            var ids = this.id.split("treedom_")[1];
            $(".selection-block:not(#" + ParsimonyAdmin.inProgress + ")",ParsimonyAdmin.currentBody).removeClass("selection-block");
            $("#" + ids,ParsimonyAdmin.currentBody).trigger('mouseover');
        });
	
        //ui update
        $("#panelcss").on("keyup change",".liveconfig", function(event){
            var nbstyle = document.getElementById("current_stylesheet_nb").value;
            var nbrule = document.getElementById("current_stylesheet_nb_rule").value;
            var stylesh = ParsimonyAdmin.currentDocument.styleSheets[nbstyle].cssRules[nbrule];
            if(typeof stylesh != "undefined") var rules = stylesh.style.cssText + this.getAttribute("name") + ": " + this.value + ";";
            else rules = this.getAttribute("name") + ": " + this.value + ";";
            ParsimonyAdmin.setCss(nbstyle, nbrule, document.getElementById("current_selector_update").value + "{" + rules + "}");
        });

        //destruction of a block
        $(document).add('#config_tree_selector').on('click',".config_destroy",function(e){
            ParsimonyAdmin.destroyBlock();
        });

        /* HTML5 drag n drop*/
        $("#modulespages").on('dragstart',".admin_core_block", function( event ){
            var evt = event.originalEvent;
            evt.dataTransfer.setDragImage(document.getElementById($(this).attr("id")),15,15);
            evt.dataTransfer.setData("Text", $(this).attr("id"));
            evt.dataTransfer.setData('text/plain', "addBlock");
            evt.dataTransfer.effectAllowed = 'copy';
            ParsimonyAdmin.startDragging();
            ParsimonyAdmin.blockType = this.id;
        });
        $("#menu").add('#paneltree').on('dragstart',".move_block",function( event ){  
            var evt = event.originalEvent;
            var toto = $("#" + ParsimonyAdmin.inProgress,ParsimonyAdmin.currentBody);
            var InProgressId = toto.attr("id");
            evt.dataTransfer.setDragImage($("#" + InProgressId,ParsimonyAdmin.currentBody).get(0),15,15);
            evt.dataTransfer.setData("Text", InProgressId);
            evt.dataTransfer.setData('text/plain', "moveBlock");
            evt.dataTransfer.effectAllowed = 'copyMove';
            ParsimonyAdmin.startDragging();
            ParsimonyAdmin.startTypeCont = ParsimonyAdmin.whereIAm(ParsimonyAdmin.inProgress);
            if(toto.parent().closest(".container").hasClass("container_page")) ParsimonyAdmin.startIdParentBlock = toto.parent().closest(".container").data('page');
            else ParsimonyAdmin.startIdParentBlock = toto.parent().closest(".container").attr('id');
            if(ParsimonyAdmin.startIdParentBlock == 'content') ParsimonyAdmin.startIdParentBlock = $(".container_page",ParsimonyAdmin.currentBody).data('page');
            ParsimonyAdmin.idBlock = InProgressId;
        });
        
        $('#conf_box_content').on('click',"#dialog-ok",function(e){
            e.preventDefault();
            var toto = $( "#dropInPage" ,ParsimonyAdmin.currentBody);
            ParsimonyAdmin.idBlock = $("#dialog-id").val();
            if(ParsimonyAdmin.idBlock != ''){
                //if($("#" + ParsimonyAdmin.idBlock,ParsimonyAdmin.currentBody).length == 0){           <-- server do this check
                    if( ParsimonyAdmin.idNextBlock == '' || ParsimonyAdmin.stopIdParentBlock == '' || ParsimonyAdmin.whereIAm("dropInTree") == '') alert("stop");
                    ParsimonyAdmin.changeBlockPosition(ParsimonyAdmin.blockType,ParsimonyAdmin.idBlock,ParsimonyAdmin.idNextBlock,'',ParsimonyAdmin.stopIdParentBlock,'',ParsimonyAdmin.whereIAm("dropInTree"),"addBlock");
                //}
            }else{
                alert(t('Please enter your ID'));
            }
            ParsimonyAdmin.closeConfBox();
        });

        $("#toolbar li.subMenu,#toolbar li.subSubMenu").hover(function(){
            $("ul:first",this).show();
            if($(this).hasClass("subSubMenu")) $("ul:first",this).css("left",$(this).width()-5 + "px");
        },function(){
            $("ul:first",this).hide();
        });

        $(".subSidebar").on('click',"#csspicker", function(e){
            e.preventDefault();
            e.stopPropagation();
	    ParsimonyAdmin.closeParsiadminMenu();
	    $("#blockOverlay").addClass("csspicker");
            $('#container',ParsimonyAdmin.currentBody).on('mouseover.csspicker',"*", function(event) {
                event.stopPropagation();
		var offset = $(this).offset();
		var offsetFrame = $("#parsiframe").offset();
		$("#blockOverlay").css({"display":"block","top":offset.top + offsetFrame.top + "px","left":offset.left + offsetFrame.left +  "px","width":$(this).outerWidth() + "px","height":$(this).outerHeight() + "px" });
            });
            $("#csspicker").addClass("active");
            $('#container',ParsimonyAdmin.currentBody).on('click.csspicker',"*",function(e){
                e.preventDefault();
                e.stopPropagation();
                $(".cssPicker").removeClass("cssPicker");
                $(this).addClass("cssPicker");
                ParsimonyAdmin.getCSSForCSSpicker();
                var title = CSSTHEMEPATH;
                if(this.id != "" && $(".selectorcss[selector='#" + this.id + "']").length == 0) ParsimonyAdmin.addNewSelectorCSS( title, "#" + this.id)
                $.each($(this).attr('class').replace('  ',' ').split(' '), function(index, value) {
                    if(value.length > 0 && $(".selectorcss[selector='." + value + "']").length == 0 && value != "selection-block") ParsimonyAdmin.addNewSelectorCSS( title, "." + value);
                });
                var good = false;
                var selectProp = this.tagName.toLowerCase();
                if(this.id == ""){
                    if($(this).attr('class') != undefined && $(this).attr('class') != "") selectProp = selectProp + ("." + $(this).attr("class").replace(" ",".")).replace(".cssPicker","");
                    $(this).parentsUntil("body").each(function(){
                        if(!good){
                            var selectid = "";
                            var selectclass = "";
                            if($(this).attr('id') != undefined) selectid = "#" + $(this).attr('id');
                            if($(this).attr('class') != undefined && $(this).attr('class') != "") selectclass = "." + $(this).attr("class").replace(" ","");
                            selectProp = selectid + selectclass.replace("  ","") + " " + selectProp;
                            if(selectid != "") good = true;
                        }
                    });
                    ParsimonyAdmin.addNewSelectorCSS( title, selectProp);
                }
                
                $('#container',ParsimonyAdmin.currentBody).off(".csspicker");
                $("#csspicker").removeClass("active");
		$("#blockOverlay").removeClass("csspicker");
                return false;
            });
        });
        
        $(document).on('click',"#menu div.selectorcss", function(){
            ParsimonyAdmin.closeParsiadminMenu();
            $(".cssselectorviewer").removeClass("cssselectorviewer");
            ParsimonyAdmin.displayCSSConf($(this).attr('title'), $(this).attr('selector'));
        });
	
        $(document).add('#config_tree_selector').on('click',".cssblock",function(e){ 
            e.preventDefault();
            var filePath = CSSTHEMEPATH;
            if(ParsimonyAdmin.whereIAm(ParsimonyAdmin.inProgress)=='page') filePath = CSSPAGEPATH;
            ParsimonyAdmin.displayCSSConf(filePath, "#" + ParsimonyAdmin.inProgress);
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

        $("#left_sidebar").on('click','div.titleTab', function(){
            var next = $(this).next();
            if(next.is('div')) $(this).next().slideToggle('fast');
        });
        
        $("#treedom_content").on('mouseover mouseout',function(event){
            var dom = $(".container_page",ParsimonyAdmin.currentBody).get(0);
            if(typeof dom.style != "undefined"){
                if (event.type == 'mouseover') {
                    dom.style.outline = '5px #c8007a solid';
                } else {
                    dom.style.outline = 'none';
                }
            }
        });

        $(".explorer").on('click',function(event){
            ParsimonyAdmin.displayConfBox(BASE_PATH + "admin/explorer","Explorer","idelmt=" + $(this).attr('rel'));
        });
	
        ParsimonyAdmin.removeEmptyTextNodes(document.body);
        ParsimonyAdmin.hideOverlay();
	
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
    startDragging :   function (){
        ParsimonyAdmin.moveBlock = true;
        $(ParsimonyAdmin.currentBody).append($("#dropInPage" ));
        ParsimonyAdmin.openRightTreePanel(); 
        $(ParsimonyAdmin.currentBody).add('#paneltree').on('dragenter.drag','.block,#dropInTree,.tree_selector', function(e) {
            e.stopImmediatePropagation();
            //if(e.type == 'dragenter' || Math.floor ( Math.random() * 12 ) == 3) {
            var isContainer = false;
            if((this.classList.contains("container") && !this.classList.contains("tree_selector")) || this.id =='treedom_container') isContainer = true;
            if(e.type == 'dragenter' || (ParsimonyAdmin.dragLastDomId != this.id ||
                ( ParsimonyAdmin.dragMiddlePos == 1 && (e.originalEvent.pageY > ParsimonyAdmin.dragMiddle)) ||
                ( ParsimonyAdmin.dragMiddlePos == 0 && (e.originalEvent.pageY < ParsimonyAdmin.dragMiddle)))){
                var theBlock = this;
                if(this.classList.contains("tree_selector")) theBlock = $("#" + this.id.split("treedom_")[1],ParsimonyAdmin.currentBody).get(0);
                var theBlockTree = document.getElementById("treedom_" + theBlock.id);
                var dropInPage = $( "#dropInPage",ParsimonyAdmin.currentBody).get(0);
                ParsimonyAdmin.dragLastDomId = this.id;
                ParsimonyAdmin.dragMiddle = $(this).offset().top + this.offsetHeight/2;
                if(e.originalEvent.pageY < ParsimonyAdmin.dragMiddle && !isContainer){
                    ParsimonyAdmin.dragMiddlePos = 1;
                    $(theBlock).before(dropInPage);
                    theBlockTree.parentNode.insertBefore(document.getElementById( "dropInTree" ),theBlockTree);
                }else{
                    ParsimonyAdmin.dragMiddlePos = 0;
                    if(theBlock.classList.contains("container") && $(theBlock).children(".dropInContainer").length > 0){
                        $(".dropInContainerChild:first",theBlock).append(dropInPage);
                        theBlockTree.appendChild(document.getElementById( "dropInTree" ),theBlockTree);
                    }else if(theBlock.classList.contains("container") && !isContainer){
                        theBlock.parentNode.insertBefore(dropInPage,theBlock);
                        theBlockTree.parentNode.insertBefore(document.getElementById( "dropInTree" ),theBlockTree);
                    }else if(theBlock.parentNode.classList.contains("container") && !isContainer){
                        theBlock.parentNode.insertBefore(dropInPage,theBlock.nextSibling);
                        theBlockTree.parentNode.insertBefore(document.getElementById( "dropInTree" ),theBlockTree.nextSibling);
                    }
                }
            }
            dropInPage = theBlock = theBlockTree = null;
            return false;
        });
	
        $(ParsimonyAdmin.currentBody).add('#paneltree').on('dragover.drag','.block,#dropInTree,.tree_selector', function(e) {
            e.stopImmediatePropagation();
            return false;
        });


    },
    stopDragging :   function (){
        $(ParsimonyAdmin.currentBody).add('#paneltree').off('.drag');
        ParsimonyAdmin.moveBlock = false;
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
        setConfBoxTitle :   function (title){
            $("#conf_box_title").html(title);
            
        },
        returnToShelter :   function (){
            $("#dropInPage",ParsimonyAdmin.currentBody).prependTo($("#shelter"));
            $("#dropInTree").prependTo($("#shelter"));
        },
        structure_tree :   function (elmt){
            if(typeof html == "undefined") {
                var html = ""
            }
            elmt.children(".block",ParsimonyAdmin.currentBody).each(function(i){
                var id = this.id;
                if($(this).hasClass('container')) html += "<ul class=\"tree_selector container parsicontainer\" style=\"clear:both\" id=\"treedom_" + this.id + "\"><span class=\"arrow_tree\"></span>" + id + "" ;
                else html += "<li class=\"tree_selector parsiblock\" id=\"treedom_" + this.id + "\"> " + id + "</li>";
                html += ParsimonyAdmin.structure_tree($(this));
                if(($(this).hasClass('container'))) html += "</ul>";
            });
            return html;
        },
        /*ajax :   function (action,title,params){
            ParsimonyAdmin.postData(BASE_PATH + "admin/" + action,"idBlock=" + ParsimonyAdmin.inProgress + "&parentBlock=" + $("#" + ParsimonyAdmin.inProgress,ParsimonyAdmin.currentBody).parent().attr("id") + "&typeProgress=" + ParsimonyAdmin.typeProgress + "&" + params,function(data){
                ParsimonyAdmin.displayConfBox(data,title);
            });
        },*/
        changeBlockPosition : function (blockType,idBlock,idNextBlock,startIdParentBlock,stopIdParentBlock,startTypeCont,stopTypeCont,action){
            if(typeof startIdParentBlock == "undefined" || typeof stopIdParentBlock == "undefined"){alert('Error in your DOM, perhaps an HTML tag isn\'t closed.');return false};
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
        reloadIframe : function (selector,evt){
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
                //$("#tree").html(ParsimonyAdmin.structure_tree($(ParsimonyAdmin.currentBody)));
                ParsimonyAdmin.loadBlock('tree',function(){if(ParsimonyAdmin.inProgress != "container") $("#treedom_" + ParsimonyAdmin.inProgress).trigger("click");});
                
            }
            $(".container",ParsimonyAdmin.currentBody).each(function(){
                if($(this).find('.block:not("#content")').length==0) {
                    $(this).prepend('<div class="dropInContainer"><div class="dropInContainerChild">Id #' + $(this).get(0).id + ". " + t("Drop the blocks in this space") + '</div></div>');
                }else $(".dropInContainerChild:first",this).remove();
            });
        },
        updateCSSUI :   function (cssprop){
            $("#current_selector_update,#current_selector_update_prev").val(cssprop.selector);
            $("#changecsspath").val(cssprop.filePath);
            $("#panelcss *[data-initial]").val('');
            $.each(cssprop.values, function(i,item){
                $("#panelcss [css=" + i + "]").val(item);
            });
        },
        setCreationMode :   function (){
            ParsimonyAdmin.loadCreationMode();
            ParsimonyAdmin.unloadEditMode();
            ParsimonyAdmin.unloadPreviewMode();
            $('.creation,.panelblocks').show();
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
            $("#left_sidebar #panelmodules").show();
            $("#left_sidebar #panelblocks").hide();
            $(".panelmodules").addClass('active');
            ParsimonyAdmin.setCookie("mode","edit",999);
        },
        setPreviewMode :   function (){
            ParsimonyAdmin.unloadCreationMode();
            ParsimonyAdmin.unloadEditMode();
            ParsimonyAdmin.loadPreviewMode();
            $('.creation,.panelblocks').hide();
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
        setCss: function(nbstyle, nbrule, rule){
            if(nbrule == null){
                nbrule = ParsimonyAdmin.currentDocument.styleSheets[nbstyle].cssRules.length - 1;
            }else{
                ParsimonyAdmin.currentDocument.styleSheets[nbstyle].removeRule(nbrule);
            }
            ParsimonyAdmin.currentDocument.styleSheets[nbstyle].insertRule(rule,nbrule);
        },
        displayCSSConf :  function (filePath,selector){
            ParsimonyAdmin.openCSSForm();
            ParsimonyAdmin.postData(BASE_PATH + "admin/getCSSSelectorRules" ,{
                TOKEN:TOKEN,
                selector: selector,
                filePath: filePath
            } ,function(data){
                $("#css_panel input[type!=\"hidden\"]").val("");
                ParsimonyAdmin.updateCSSUI($.parseJSON(data));
            });
            var selectorPrev = $("#current_selector_update_prev").val();
            if(selectorPrev.length > 0){
                var nbstyle = $("#current_stylesheet_nb").val();
                var nbrule = $("#current_stylesheet_nb_rule").val();
                ParsimonyAdmin.setCss(nbstyle, nbrule, selectorPrev + "{" + ($("#current_stylesheet_rules").val() || " ") + "}");
            }
            document.getElementById("typeofinput").value = "form";
            document.getElementById("current_stylesheet_rules").value = "";
            document.getElementById("current_stylesheet_nb_rule").value = "";
            $("#changecssformcode").removeClass("none");
            
            var styleSheets = ParsimonyAdmin.currentDocument.styleSheets;
            for (var i = 0; i < styleSheets.length; i++){
                 if(styleSheets[i].href != null && !!styleSheets[i].href && styleSheets[i].href.match(new RegExp(filePath))){
                   $("#current_stylesheet_nb").val(i);
                   $.each(styleSheets[i].cssRules, function(nbrule) {
                        if(this.selectorText == selector){
                            $("#current_stylesheet_nb_rule").val(nbrule);
                            $("#current_stylesheet_rules").val(styleSheets[i].cssRules[nbrule].style.cssText);
                        }
                    });
                    if($("#current_stylesheet_nb_rule").val().length == 0){
                        $("#current_stylesheet_nb_rule").val(null);
                    }
                }
            }

            if($(selector,ParsimonyAdmin.currentBody).length == 1){
                $(selector,ParsimonyAdmin.currentBody).parsimonyDND('destroy');
                $(selector,ParsimonyAdmin.currentBody).parsimonyDND({
                    stopResizable : function(event, ui) {
                        $("#form_css input[css=width]").val(ui.width() + "px");
                        $("#form_css input[css=height]").val(ui.height() + "px");
                        $("#form_css input[css=left]").val(ui.css('left'));
                        $("#form_css input[css=top]").val(ui.css('top'));
                    },
                    stopDraggable: function(event, ui) {
                        $("#form_css input[css=left]").val(ui.css('left'));
                        $("#form_css input[css=top]").val(ui.css('top'));
                    }
                });
            }
        },
        CSSeditor :  function (id){
            editor = CodeMirror(function(node) {
                document.getElementById(id).parentNode.insertBefore(node, document.getElementById(id).nextSibling);
                }, {
                mode:"text/css",
                value: document.getElementById(id).value,
                lineNumbers: false,
                gutter:true,
                autoClearEmptyLines:true,
                onGutterClick: function(c, n) {
                    var info = c.lineInfo(n);
                    if (info.lineClass == "barre"){
			c.setLineClass(n, "");
			c.clearMarker(n);
		    }else{
			c.setMarker(n, '<span class="activebtn">×</span>');
			c.setLineClass(n, "barre");
		    }
                    this.onChange(c);
                },
                onChange: function(c) {
                    var textarea = document.getElementById(c.id);
                    var nbstyle = textarea.getAttribute('data-nbstyle');
                    var nbrule = textarea.getAttribute('data-nbrule');
                    var selector = textarea.getAttribute('data-selector');
                    var code = selector + '{';
                    for(var i = 0;i < c.lineCount(); i++){
                        if(c.lineInfo(i).lineClass != "barre") code += c.getLine(i);
                    }
                    ParsimonyAdmin.setCss(nbstyle, nbrule, code + "}");
                    textarea.value = c.getValue();
                },
                onCursorActivity: function(c) {
                    var cursorLine = c.getCursor().line;
                    for(var i = 0;i < c.lineCount() - 1; i++){
                        if(c.getLine(i) == "" && cursorLine != i) c.removeLine(i);
                        //editor.setLine(i,editor.getLine(i).replace(/([a-z\-]);([a-z\-])/g, "$1;"));
                        //editor.indentLine(i);
                    } 
                }
            });
            editor.id = id;
            ParsimonyAdmin.csseditors.push(editor);
        },
        getCSSForCSSpicker :  function (){
            var json = '[';
            ParsimonyAdmin.openCSSCode();
            var elmt = $('.cssPicker',ParsimonyAdmin.currentBody).removeClass('cssPicker').get(0);
            var styleSheets = ParsimonyAdmin.currentDocument.styleSheets;
            for (var i = 0; i < styleSheets.length; i++){
                if(styleSheets[i].href != null && !!styleSheets[i].href && !styleSheets[i].href.match(new RegExp("/" + window.location.host + BASE_PATH + "lib"))){
                    $.each(styleSheets[i].cssRules, function(nbrule) {
                        if(elmt.webkitMatchesSelector(this.selectorText)){
                            var url = styleSheets[i].href.replace("http://" + window.location.host,"").substring(BASE_PATH.length);
                            ParsimonyAdmin.addSelectorCSS(url, this.selectorText, this.style.cssText.replace(/;[^a-z\-]/g, ";\n"), i , nbrule);
                            json += '{"nbstyle":"' + i + '","nbrule":"' + nbrule + '","url":"' + url + '","selector":"' + this.selectorText + '"},';
                        }
                    });
                }
            }
            json = json.substring(0, json.length - 1) + ']';
            $.post(BASE_PATH + "admin/getCSSSelectorsRules", { json: json },
            function(data) {
                $.each(data, function(i,item) {
                    var id = 'idcss' + item.nbstyle + item.nbrule;
                    document.getElementById(id).value = item.cssText;
                });
                $.each(ParsimonyAdmin.csseditors,function(i, el){
                    el.setValue(document.getElementById(el.id).value);
                });
            });
            
        },
        addNewSelectorCSS :  function ( path, selector){
            var code = '';
            var nbstyle = '';
            var nbrule = '';
            var styleSheets = ParsimonyAdmin.currentDocument.styleSheets;
            for (var i = 0; i < styleSheets.length; i++){
                if(styleSheets[i].href != null && !!styleSheets[i].href && styleSheets[i].href.match(new RegExp(path))){
                    nbstyle = i;
                    $.each(styleSheets[i].cssRules, function(indexRule) {
                        if(this.selectorText == selector){
                            nbrule = indexRule;
                            code = styleSheets[i].cssRules[nbrule].style.cssText;
                        }
                    });
                    if(nbrule.length == 0){
                        nbrule = null;
                    }
                }
            }
            ParsimonyAdmin.addSelectorCSS( path, selector, code, nbstyle, nbrule);
            ParsimonyAdmin.setCss(nbstyle, nbrule, selector + "{" + code + "}");
        },
        addSelectorCSS :  function ( url, selector, styleCSS, nbstyle, nbrule){
            var id = 'idcss' + nbstyle + nbrule;
            var code = '<div class="selectorcss" title="' + url + '" selector="' + selector + '"><div style="text-shadow: 0px 1px 0px white;width:160px;word-break: break-all;"><b>' + selector + '</b> <small>in ' + url.replace(/^.*[\/\\]/g, '') + '</small></div><div class="gotoform" onclick="ParsimonyAdmin.displayCSSConf(\'' + url + '\',\'' + selector + '\')">'+ t('Go to form') +'</div></div>'
            + '<input type="hidden" name="selectors[' + id + '][file]" value="' + url + '"><input type="hidden" name="selectors[' + id + '][selector]" value="' + selector + '">'
            + '<textarea  class="csscode" id="' + id + '" name="selectors[' + id + '][code]" data-nbstyle="' + nbstyle + '" data-nbrule="' + nbrule + '" data-selector="' + selector + '">' + styleCSS.replace(/;/,";\n").replace("\n\n","\n") + '</textarea>';
            $("#changecsscode").prepend(code);
            ParsimonyAdmin.CSSeditor(id);
        },
        openCSSForm :  function (){
            ParsimonyAdmin.openRightCSSPanel();
            $("#changecssform").removeClass('none');
            $("#changecsscode").addClass('none');
            $("#switchtocode").removeClass('active');
            $("#switchtovisuel").addClass('active');
            $("#typeofinput").val("form");
            $('#css_panel').show();
            $("#goeditcss").hide();
        },
        openCSSCode :  function (){
            ParsimonyAdmin.openRightCSSPanel();
            $("#changecsscode").removeClass('none');
            $("#changecssform").addClass('none');
            $("#switchtovisuel").removeClass('active');
            $("#switchtocode").addClass('active');
            $("#typeofinput").val("code");
            $('#css_panel').show();
            $("#goeditcss").hide();
            $.each(ParsimonyAdmin.csseditors,function(i, el){
                ParsimonyAdmin.csseditors.splice(i,i+1);
            });
            $("#changecsscode").empty();
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
