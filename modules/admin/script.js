var ParsimonyAdmin = {
    isInit    :   false,
    currentWindow    :   "",
    inProgress    :   "",
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
	    var bodyIframe = document.getElementById("parsiframe").contentWindow.document.body;
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
            $(this).parent().find("ul").hide();
            $(this).parent().find("." + $(this).attr('rel')).show();
            $(this).parent().find(".ssTab").removeClass('active');
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
                var height = document.getElementById("parsiframe").contentWindow.document.body.offsetHeight;
                if(screen.height > height) height = screen.height - 28;
                    document.getElementById("parsiframe").style.height = height + "px"
            }
        }, 1000);
		
    },

    initIframe :   function(){
        ParsimonyAdmin.currentWindow = $('#parsiframe').contents().find("body");
        ParsimonyAdmin.inProgress = "container";
        ParsimonyAdmin.updateUI();
        $(ParsimonyAdmin.currentWindow).append('<link rel="stylesheet" type="text/css" href="' + BASE_PATH + 'admin/iframe.css">');
        ParsimonyAdmin.changeDeviceUpdate();
        ParsimonyAdmin.loadCreationMode();
	
        /*var x;
	var y;
	$(ParsimonyAdmin.currentWindow).on('dragstart',function(event){
	var img = document.createElement("img");
	event.originalEvent.dataTransfer.setDragImage(img, 128,128);
	x =  event.originalEvent.clientX;
	y =  event.originalEvent.clientY;
	});
	$(ParsimonyAdmin.currentWindow).parent().on('dragover',function(event){
	$(ParsimonyAdmin.currentWindow).css( '-webkit-transform', 'perspective(' + $("#perspective").val() + 'px) rotate(' + $("#rotate").val() + 'deg) rotateX(' + ((event.originalEvent.clientY - y) + parseInt($("#rotatex").val())) + 'deg) rotateY(' + ((event.originalEvent.clientX - x) + parseInt($("#rotatey").val())) + 'deg) translateZ(' + $("#translatez").val() + 'px)' );
	return false;
	});
	$(ParsimonyAdmin.currentWindow).parent().on('drop',function(event){
	$("#rotatey").val(((event.originalEvent.clientX - x) + parseInt($("#rotatey").val())));
	$("#rotatex").val(((event.originalEvent.clientY - y) + parseInt($("#rotatex").val())));
	return false;
	});*/
	
        $('#treedom_container').attr('title','Site structure');
        $('#treedom_content').attr('title','Dynamic content');
        $(".tooltip").parsimonyTooltip({
            triangleWidth:5
        });

        if(ParsimonyAdmin.getCookie("mode") == 'preview'){
            $("#switchPreviewMode").trigger('click');
        }
        
        var styleSheets = document.getElementById("parsiframe").contentWindow.document.styleSheets;
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
        $(ParsimonyAdmin.currentWindow).on('click.creation','.traduction', function(e){
            e.trad = true;
            ParsimonyAdmin.closeParsiadminMenu();
            ParsimonyAdmin.addTitleParsiadminMenu("Traduction");
            ParsimonyAdmin.addOptionParsiadminMenu('<span class="ui-icon ui-icon-pencil floatleft"></span><a href="#" class="action" rel="getViewTranslation" params="key=' + $(this).data("key") + '" title="Gestion des Traductions">Traduct</a>');
        });
	
        $(ParsimonyAdmin.currentWindow).on('click.creation','a', function(e){
            e.link = true;
            e.preventDefault();
            if(e.trad != true) ParsimonyAdmin.closeParsiadminMenu();
            ParsimonyAdmin.addTitleParsiadminMenu("Link");
            ParsimonyAdmin.addOptionParsiadminMenu('<span class="ui-icon ui-icon-extlink floatleft"></span><a href="javascript:ParsimonyAdmin.goToPage(\'' + $.trim($(this).text().replace("'","\\'")) + '\',\'' + $(this).attr('href') + '\');">Go to the link</a>');
        });	
		
        /*$(ParsimonyAdmin.currentWindow).on('click',function(e){ 
      var elem = document.getElementById("parsiframe").contentWindow.document.elementFromPoint ( e.pageX , e.pageY );
    //console.log ( $(elem).closest(".block") );
$this = $(elem).closest(".block").get(0);*/
        $(ParsimonyAdmin.currentWindow).on('click.creation','.block', function(e){
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
        
        $(ParsimonyAdmin.currentWindow).add('#paneltree').on('dragover.creation dragenter.creation','.marqueurdragndrop', function(e) {
            e.stopImmediatePropagation();
            return false;
        });
	
        $(ParsimonyAdmin.currentWindow).on('mouseover.creation mouseout.creation',".block", function(event) {
            event.stopImmediatePropagation();
            if (event.type == 'mouseover') {
                $(this).addClass("selection-block");
            } else {
                if(this.id != ParsimonyAdmin.inProgress) $(this).removeClass("selection-block");
            }
        });
	
        /*$(document).on('mouseover mouseout',"#timer", function(event) {
            if (event.type == 'mouseover') {
                $("#infodev").show();
            } else {
                $("#infodev").hide();
            }
        });*/
        
        $(ParsimonyAdmin.currentWindow).add('#paneltree').on('drop.creation','.container,.tree_selector',function( event ){
            ParsimonyAdmin.stopDragging();
            event.stopPropagation();
            var evt = event.originalEvent;
            evt.stopPropagation();
            var toto = $( "#dropInPage" ,ParsimonyAdmin.currentWindow);
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
            
        $(ParsimonyAdmin.currentWindow).on('mouseover.creation mouseout.creation','#menu div',function(e){ /* todo fixer */
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
        $(".selection-block",ParsimonyAdmin.currentWindow).removeClass("selection-block");
        $(".selection-container",ParsimonyAdmin.currentWindow).removeClass("selection-container");
        ParsimonyAdmin.closeParsiadminMenu();
        $(ParsimonyAdmin.currentWindow).off('.creation');
    },
    
    loadPreviewMode :   function(){
        $(ParsimonyAdmin.currentWindow).on('click.preview','a', function(e){
            if($(this).attr("href").substring(0,1) != '#' && $(this).attr("href").substring(0,7) != 'http://'){
                e.preventDefault();
                ParsimonyAdmin.goToPage( $.trim($(this).text().replace("'","\\'")) , $(this).attr('href') );
            }
        });
	
        $(ParsimonyAdmin.currentWindow).on('dblclick.preview',".editinline",function(){
            $(this).attr('contentEditable', true);
        });
        
        $(".editinline",ParsimonyAdmin.currentWindow).on('blur.preview',function(){
            ParsimonyAdmin.postData(BASE_PATH + "admin/editInLine",{
                TOKEN: TOKEN,
                module: $(this).data('module'),
                model: $(this).data('model'),
                property: $(this).data('property'),
                id: $(this).data('id'),
                value: $(this).html()
            },function(data){
                $("#conf_box_content_iframe").contents().find('body').html(data);
                data = $("#conf_box_content_iframe").contents().find('body').html();
                ParsimonyAdmin.execResult(data);
            });
        });
    }, 
    
    unloadPreviewMode :   function(){
        $(ParsimonyAdmin.currentWindow).off('.preview');
    }, 
    init :   function(){
        
        ParsimonyAdmin.isInit = true;

        $(document).on('click','#menu a',function(e){
            ParsimonyAdmin.closeParsiadminMenu();
        });

        $(document).add('#config_tree_selector').on('click',".action", function(e){
            if($("#treedom_" + ParsimonyAdmin.inProgress).parent().closest(".container").attr("id")=="treedom_content") var parentId = $("#treedom_" + ParsimonyAdmin.inProgress).parent().closest("#treedom_content").data('page');
                else var parentId = $("#treedom_" + ParsimonyAdmin.inProgress).parent().closest(".container").attr('id').replace("treedom_","");
            ParsimonyAdmin.displayConfBox(BASE_PATH + "admin/action",$(this).attr('title'),"TOKEN=" + TOKEN + "&idBlock=" + ParsimonyAdmin.inProgress + "&parentBlock=" + parentId + "&typeProgress=" + ParsimonyAdmin.typeProgress + "&action=" + $(this).attr('rel') +"&IDPage=" + $(".container_page",ParsimonyAdmin.currentWindow).data('page') +"&" + $(this).attr('params'));
            e.preventDefault();
        });

        $('#right_sidebar').on('click','.tree_selector', function(event){
            event.stopPropagation(); 
            ParsimonyAdmin.selectBlock(this.id.split("treedom_")[1]);
            if($("#" + this.id.split("treedom_")[1],ParsimonyAdmin.currentWindow).length > 0){
                $("body").animate({
                    scrollTop : $("#" + this.id.split("treedom_")[1],ParsimonyAdmin.currentWindow).offset().top -50
                },"fast");
            }
        });
	
        $('#right_sidebar').on('mouseenter','.tree_selector', function(event){
            event.stopPropagation();
            var ids = this.id.split("treedom_")[1];
            $(".selection-block:not(#" + ParsimonyAdmin.inProgress + ")",ParsimonyAdmin.currentWindow).removeClass("selection-block");
            $("#" + ids,ParsimonyAdmin.currentWindow).trigger('mouseover');
        });
	
        //ui update
        $("#panelcss").on("keyup change",".liveconfig", function(event){
            if(typeof $(this).attr("name") != 'undefined') $($("#current_selector_update").val(),ParsimonyAdmin.currentWindow).css($(this).attr("name"), $(this).val());
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
            var toto = $("#" + ParsimonyAdmin.inProgress,ParsimonyAdmin.currentWindow);
            var InProgressId = toto.attr("id");
            evt.dataTransfer.setDragImage($("#" + InProgressId,ParsimonyAdmin.currentWindow).get(0),15,15);
            evt.dataTransfer.setData("Text", InProgressId);
            evt.dataTransfer.setData('text/plain', "moveBlock");
            evt.dataTransfer.effectAllowed = 'copyMove';
            ParsimonyAdmin.startDragging();
            ParsimonyAdmin.startTypeCont = ParsimonyAdmin.whereIAm(ParsimonyAdmin.inProgress);
            if(toto.parent().closest(".container").hasClass("container_page")) ParsimonyAdmin.startIdParentBlock = toto.parent().closest(".container").data('page');
            else ParsimonyAdmin.startIdParentBlock = toto.parent().closest(".container").attr('id');
            if(ParsimonyAdmin.startIdParentBlock == 'content') ParsimonyAdmin.startIdParentBlock = $(".container_page",ParsimonyAdmin.currentWindow).data('page');
            ParsimonyAdmin.idBlock = InProgressId;
        });
        
        $('#conf_box_content').on('click',"#dialog-ok",function(e){
            e.preventDefault();
            var toto = $( "#dropInPage" ,ParsimonyAdmin.currentWindow);
            ParsimonyAdmin.idBlock = $("#dialog-id").val();
            if(ParsimonyAdmin.idBlock != ''){
                //if($("#" + ParsimonyAdmin.idBlock,ParsimonyAdmin.currentWindow).length == 0){           <-- server do this check
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
            $('#container',ParsimonyAdmin.currentWindow).on('mouseover mouseout',"*", function(e) {
                e.stopPropagation();
                if (e.type == 'mouseover') {
                    $(this).addClass('cssPicker');
                } else {
                    $(this).removeClass('cssPicker');
                }
            });
            $('#container',ParsimonyAdmin.currentWindow).on('click',"*",function(e){
                e.preventDefault();
                e.stopPropagation();
                $(".cssPicker").removeClass("cssPicker");
                $(this).addClass("cssPicker");
                ParsimonyAdmin.closeParsiadminMenu();
                ParsimonyAdmin.addTitleParsiadminMenu("Existing Selectors :");
                ParsimonyAdmin.getCSSForCSSpicker();
		ParsimonyAdmin.getCSSForCSSpicker2();
                var title = CSSTHEMEPATH;
                //if(ParsimonyAdmin.whereIAm($(this))=='page') title = CSSPAGEPATH;
			
                if(this.id != "") ParsimonyAdmin.addOptionParsiadminMenu("<div class=\"selectorcss\" title=\"" + title + "\" selector=\"#" + this.id + "\"><b>#" + this.id + "</b> <small>dans " + title + "</small></div>");
                $.each($(this).attr('class').replace('  ',' ').split(' '), function(index, value) {
                    if(value != 'cssPicker') ParsimonyAdmin.addOptionParsiadminMenu("<div class=\"selectorcss\" title=\"" + title + "\" selector=\"." + value + "\"><b>." + value + "</b> <small>in " + title + "</small></div>");
                });
                var good = false;
                var selectProp = this.tagName.toLowerCase();
                if(this.id == ""){
                    ParsimonyAdmin.addTitleParsiadminMenu("Proposals of New Selectors  :");
                    if($(this).attr('class') != undefined) selectProp = selectProp + ("." + $(this).attr("class").replace(" ",".")).replace(".cssPicker","");
                    $(this).parentsUntil("body").each(function(){
                        if(!good){
                            var selectid = "";
                            var selectclass = "";
                            if($(this).attr('id') != undefined) selectid = "#" + $(this).attr('id');
                            if($(this).attr('class') != undefined) selectclass = "." + $(this).attr("class").replace(" ",".");
                            selectProp =  selectid + selectclass + " " + selectProp;
                            if(selectid != "") good = true;
                        }
                    });
                    //if(ParsimonyAdmin.whereIAm($(selectProp))=='page') title = CSSPAGEPATH;
                    ParsimonyAdmin.addOptionParsiadminMenu("<div class=\"selectorcss\" title=\"" + title + "\" selector=\"" + selectProp + "\">" + selectProp + "</div>");
                }
                ParsimonyAdmin.openParsiadminMenu(e.pageX,e.pageY);
                $('#container',ParsimonyAdmin.currentWindow).off('click',"*");
                $('#container',ParsimonyAdmin.currentWindow).off('mouseover mouseout',"*");
                this.style.outline = "none";
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
            var dom = $(".container_page",ParsimonyAdmin.currentWindow).get(0);
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
    goToPage :   function (pageTitle,pageUrl){
        ParsimonyAdmin.unloadCreationMode();
        ParsimonyAdmin.unloadPreviewMode();
        if(pageUrl.substring(0,BASE_PATH.length) != BASE_PATH) pageUrl = BASE_PATH + pageUrl;
        pageUrl = $.trim(pageUrl);
        history.pushState({}, pageTitle, pageUrl.replace("?parsiframe=ok","").replace("parsiframe=ok",""));
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
                    $(ParsimonyAdmin.currentWindow).append('<script type="text/javascript" src="' + url + '"></script>');
                }
            });
        }
        if(obj.CSSFiles){
            obj.CSSFiles = jQuery.parseJSON(obj.CSSFiles);
            $.each(obj.CSSFiles, function(index, url) {
                if (!$('link[href="' + url + '"]',headParsiFrame).length) {
                    $(ParsimonyAdmin.currentWindow).append('<link rel="stylesheet" type="text/css" href="' + url + '">');
                }
            });
             
        }
        if (obj.notification) 
            ParsimonyAdmin.notify(obj.notification,obj.notificationType);
        if(obj.css)
            ParsimonyAdmin.updateCSSUI(obj.css);
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
                    IDPage:($(".container_page",ParsimonyAdmin.currentWindow).data('page') || $(".sublist.selected").attr("id").replace("page_",""))
                    } ,function(data){
                    ParsimonyAdmin.execResult(data);
                    ParsimonyAdmin.returnToShelter();
                    ParsimonyAdmin.updateUI();
                });
            }
        }
    },
    addBlock :   function (idBlock, contentBlock, idBlockAfter){
        if($( "#" + idBlockAfter ,ParsimonyAdmin.currentWindow).parent().hasClass("container")){
            $( "#" + idBlockAfter ,ParsimonyAdmin.currentWindow).after(contentBlock);
            ParsimonyAdmin.returnToShelter();
        }else {
            var block = $( "#" + idBlockAfter ,ParsimonyAdmin.currentWindow).parent().parent().parent();
            ParsimonyAdmin.returnToShelter();
            $(".dropInContainer:first",block).remove();
            if(block.get(0).id == 'container' && block.children(".block").length==1) block.prepend(contentBlock);
            else block.append(contentBlock);
        }
        $("#" + idBlock,ParsimonyAdmin.currentWindow ).trigger("click");
    },
    moveMyBlock :   function (idBlock, idBlockAfter){
        if($( "#" + idBlockAfter ,ParsimonyAdmin.currentWindow).parent().hasClass("container")){
            $( "#" + idBlockAfter ,ParsimonyAdmin.currentWindow).after( $("#" + idBlock,ParsimonyAdmin.currentWindow) );
            ParsimonyAdmin.returnToShelter();
        }else {
            var block = $( "#" + idBlockAfter ,ParsimonyAdmin.currentWindow).parent().parent().parent();
            ParsimonyAdmin.returnToShelter();
            block.html($("#" + idBlock,ParsimonyAdmin.currentWindow ) );
        }
    },
    startDragging :   function (){
        ParsimonyAdmin.moveBlock = true;
        $(ParsimonyAdmin.currentWindow).append($("#dropInPage" ));
        ParsimonyAdmin.openRightTreePanel(); 
        $(ParsimonyAdmin.currentWindow).add('#paneltree').on('dragenter','.block,#dropInTree,.tree_selector', function(e) {
            
            e.stopImmediatePropagation();
            //if(e.type == 'dragenter' || Math.floor ( Math.random() * 12 ) == 3) {
            var isContainer = false;
            if(this.id =='container'  || this.id =='treedom_container') isContainer = true;
            if(e.type == 'dragenter' || (ParsimonyAdmin.dragLastDomId != this.id ||
                ( ParsimonyAdmin.dragMiddlePos == 1 && (e.originalEvent.pageY > ParsimonyAdmin.dragMiddle)) ||
                ( ParsimonyAdmin.dragMiddlePos == 0 && (e.originalEvent.pageY < ParsimonyAdmin.dragMiddle)))){
                var theBlock = this;
                if((" " + this.className + " ").replace(/[\n\t]/g, " ").indexOf(" tree_selector ") > -1) theBlock = $("#" + this.id.split("treedom_")[1],ParsimonyAdmin.currentWindow).get(0);
                var theBlockTree = document.getElementById("treedom_" + theBlock.id);
                var dropInPage = $( "#dropInPage",ParsimonyAdmin.currentWindow).get(0);
                ParsimonyAdmin.dragLastDomId = this.id;
                ParsimonyAdmin.dragMiddle = $(this).offset().top + this.offsetHeight/2;
                if(e.originalEvent.pageY < ParsimonyAdmin.dragMiddle && !isContainer){
                    ParsimonyAdmin.dragMiddlePos = 1;
                    $(theBlock).before(dropInPage);
                    theBlockTree.parentNode.insertBefore(document.getElementById( "dropInTree" ),theBlockTree);
                }else{
                    ParsimonyAdmin.dragMiddlePos = 0;
                    if(((" " + theBlock.className + " ").replace(/[\n\t]/g, " ").indexOf(" container ") > -1) && $(theBlock).children(".dropInContainer").length > 0){
                        $(".dropInContainerChild:first",theBlock).append(dropInPage);
                        theBlockTree.appendChild(document.getElementById( "dropInTree" ),theBlockTree);
                    }else if((" " + theBlock.className + " ").replace(/[\n\t]/g, " ").indexOf(" container ") > -1 && !isContainer){
                        theBlock.parentNode.insertBefore(dropInPage,theBlock);
                        theBlockTree.parentNode.insertBefore(document.getElementById( "dropInTree" ),theBlockTree);
                    }else if((" " + theBlock.parentNode.className + " ").replace(/[\n\t]/g, " ").indexOf(" container ") > -1 && !isContainer){
                        theBlock.parentNode.insertBefore(dropInPage,theBlock.nextSibling);
                        theBlockTree.parentNode.insertBefore(document.getElementById( "dropInTree" ),theBlockTree.nextSibling);
                    }
                }
            }
            dropInPage = theBlock = theBlockTree = null;
            return false;
        });
	
        $(ParsimonyAdmin.currentWindow).add('#paneltree').on('dragover','.block,#dropInTree,.tree_selector', function(e) {
            e.stopImmediatePropagation();
            return false;
        });


    },
    stopDragging :   function (){
        $(ParsimonyAdmin.currentWindow).add('#paneltree').off('dragenter','.block,#dropInTree,.tree_selector');
        $(ParsimonyAdmin.currentWindow).add('#paneltree').off('dragover','.block,#dropInTree,.tree_selector');
        ParsimonyAdmin.moveBlock = false;
    },
    selectBlock :   function (idBlock){
        var blockObj = $("#" + idBlock,ParsimonyAdmin.currentWindow);
        var blockTreeObj = $("#treedom_" + idBlock);
        
        $(".selection-block",ParsimonyAdmin.currentWindow).removeClass("selection-block");
        $(".selection-container",ParsimonyAdmin.currentWindow).removeClass("selection-container");
        ParsimonyAdmin.inProgress = idBlock;
        ParsimonyAdmin.typeProgress = ParsimonyAdmin.whereIAm(ParsimonyAdmin.inProgress);
        $("#tree .tree_selector,#tree .container").css('background','transparent');
        $("#" + ParsimonyAdmin.inProgress,ParsimonyAdmin.currentWindow).addClass("selection-block").parent(".container").addClass("selection-container");

        blockTreeObj.css('background','#999');
        if(idBlock=="container" || blockObj.hasClass('container_page')) $("#right_sidebar #config_tree_selector").addClass("restrict");
        else $("#right_sidebar #config_tree_selector").removeClass("restrict");
        blockTreeObj.prepend($("#right_sidebar #config_tree_selector").show());
        
    },
    whereIAm :   function (idBlock){
        var where = "theme";
        if($("#" + idBlock,ParsimonyAdmin.currentWindow).length > 0){
            if($("#" + idBlock,ParsimonyAdmin.currentWindow).parent().closest("#content").length > 0 ){
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
        $("#conf_box_title").html(title);
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
        returnToShelter :   function (){
            $("#dropInPage",ParsimonyAdmin.currentWindow).prependTo($("#shelter"));
            $("#dropInTree").prependTo($("#shelter"));
        },
        structure_tree :   function (elmt){
            if(typeof html == "undefined") {
                var html = ""
            }
            elmt.children(".block",ParsimonyAdmin.currentWindow).each(function(i){
                var id = this.id;
                if($(this).hasClass('container')) html += "<ul class=\"tree_selector container parsicontainer\" style=\"clear:both\" id=\"treedom_" + this.id + "\"><span class=\"arrow_tree\"></span>" + id + "" ;
                else html += "<li class=\"tree_selector parsiblock\" id=\"treedom_" + this.id + "\"> " + id + "</li>";
                html += ParsimonyAdmin.structure_tree($(this));
                if(($(this).hasClass('container'))) html += "</ul>";
            });
            return html;
        },
        /*ajax :   function (action,title,params){
            ParsimonyAdmin.postData(BASE_PATH + "admin/" + action,"idBlock=" + ParsimonyAdmin.inProgress + "&parentBlock=" + $("#" + ParsimonyAdmin.inProgress,ParsimonyAdmin.currentWindow).parent().attr("id") + "&typeProgress=" + ParsimonyAdmin.typeProgress + "&" + params,function(data){
                ParsimonyAdmin.displayConfBox(data,title);
            });
        },*/
        changeBlockPosition : function (blockType,idBlock,idNextBlock,startIdParentBlock,stopIdParentBlock,startTypeCont,stopTypeCont,action){
            if(typeof startIdParentBlock == "undefined" || typeof stopIdParentBlock == "undefined"){ alert('Error in your DOM, perhaps an HTML tag isn\'t closed.');return false};
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
                IDPage: $(".container_page",ParsimonyAdmin.currentWindow).data('page')
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
            $(".dropInContainer",ParsimonyAdmin.currentWindow).remove();
            if(tree!=false) {
                $("#config_tree_selector").hide().prependTo("#right_sidebar");
                ParsimonyAdmin.loadBlock('tree');
                //$("#tree").html(ParsimonyAdmin.structure_tree($(ParsimonyAdmin.currentWindow)));
                ParsimonyAdmin.loadBlock('tree',function(){if(ParsimonyAdmin.inProgress != "container") $("#treedom_" + ParsimonyAdmin.inProgress).trigger("click");});
                
            }
            $(".container",ParsimonyAdmin.currentWindow).each(function(){
                if($(this).find('.block:not("#content")').length==0) {
                    $(this).prepend('<div class="dropInContainer"><div class="dropInContainerChild">Id #' + $(this).get(0).id + ". " + t("Drop the blocks in this space") + '</div></div>');
                }else $(".dropInContainerChild:first",this).remove();
            });
        },
        updateCSSUI :   function (cssprop){
            $("#current_selector_update,#current_selector_update_prev").val(cssprop.selector);
            $("#changecsspath").val(cssprop.filePath);
            $("#changecsscodetextarea").val(cssprop.code);
            $("#changecsscodeinitial").html(cssprop.code);
            $("#panelcss *[data-initial]").val($(this).data('initial'));
            $.each(cssprop.values, function(i,item){
                $("#panelcss [css=" + i + "]").val(item).trigger("change");
            });
        },
        setCreationMode :   function (){
            ParsimonyAdmin.loadCreationMode();
            ParsimonyAdmin.unloadPreviewMode();
            $('.creation,.panelblocks').show();
            $('#switchCreationMode').addClass("selected");
            $('#switchPreviewMode').removeClass("selected");
            ParsimonyAdmin.setCookie("mode","creation",999);
        },
        setPreviewMode :   function (){
            ParsimonyAdmin.unloadCreationMode();
            ParsimonyAdmin.loadPreviewMode();
            $('.creation,.panelblocks').hide();
            $('#switchPreviewMode').addClass("selected");
            $('#switchCreationMode').removeClass("selected");
            $("#left_sidebar #panelmodules").show();
            $("#left_sidebar #panelblocks").hide();
            $(".panelmodules").addClass('active');
            ParsimonyAdmin.setCookie("mode","preview",999);
        },
        loadBlock: function(id,func){
            $.get(window.location.toLocaleString(), function(data) {
                $('#' + id).html($("<div>").append(data.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, "")).find("#" + id).html());
            },func);
            //$('#' + id).load(window.location.toLocaleString() + " #" + id + " > div");
        },
        displayCSSConf :  function (filePath,selector){
            $("#container " + $('#current_selector_update').val(),ParsimonyAdmin.currentWindow).removeClass('cssPicker');
            ParsimonyAdmin.postData(BASE_PATH + "admin/getCSSSelectorRules" ,{
                TOKEN:TOKEN,
                selector: selector,
                filePath: filePath
            } ,function(data){
                var selectorPrev = $("#current_selector_update_prev").val();
                if(selectorPrev.length > 0){
                    var selectorPrevObj = $(selectorPrev,ParsimonyAdmin.currentWindow);
                    $('.liveconfig').each(function(){
                        selectorPrevObj.css($(this).attr('css'),$(this).attr('data-initial'));
                    });
                    selectorPrevObj.css('cssText', $("#changecsscodeinitial").html());
                }
                $("#css_panel input[type!=\"hidden\"]").val("");
                ParsimonyAdmin.updateCSSUI($.parseJSON(data));
                $('.liveconfig').each(function(){
                    $(this).attr('data-initial', $(this).val());
                });
            });
            ParsimonyAdmin.openRightPanel();
            ParsimonyAdmin.openRightCSSPanel();
            $('#css_panel').show();
            $("#goeditcss").hide();
            if($(selector,ParsimonyAdmin.currentWindow).length == 1){
                $(selector,ParsimonyAdmin.currentWindow).parsimonyDND('destroy');
                $(selector,ParsimonyAdmin.currentWindow).parsimonyDND({
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
        getCSSForCSSpicker :  function (){
            var styleSheets = document.getElementById("parsiframe").contentWindow.document.styleSheets;
            for (var i = 0; i < styleSheets.length; i++){
                var fileRules = styleSheets[i].cssRules || styleSheets[i].rules;
                if(fileRules != null && !!styleSheets[i].href && !styleSheets[i].href.match(new RegExp("/" + window.location.host + BASE_PATH + "lib"))){
                    $.each(fileRules, function() {
                        if(styleSheets[i].href != null){
                            if(this.selectorText!='.cssPicker' && this.selectorText.substring(0,1)!='-' && !this.selectorText.match(/,|::/) && $(this.selectorText + ".cssPicker",ParsimonyAdmin.currentWindow).length > 0){
                                var url = styleSheets[i].href.replace("http://" + window.location.host,"").substring(BASE_PATH.length);
                                ParsimonyAdmin.addOptionParsiadminMenu('<div class=\"selectorcss\" title="' + url + '" selector="' + this.selectorText + '"><b>' + this.selectorText + '</b> <small>in ' + url + '</small></div>');
                            }
                        }
                    });
                }
            }
        },
        getCSSForCSSpicker2 :  function (){
	    var test = '';
            var styleSheets = document.getElementById("parsiframe").contentWindow.document.styleSheets;
            for (var i = 0; i < styleSheets.length; i++){
                var fileRules = styleSheets[i].cssRules || styleSheets[i].rules;
                if(fileRules != null && !!styleSheets[i].href && !styleSheets[i].href.match(new RegExp("/" + window.location.host + BASE_PATH + "lib"))){
                    $.each(fileRules, function(num) {
                        if(styleSheets[i].href != null){
                            if(this.selectorText!='.cssPicker' && this.selectorText.substring(0,1)!='-' && !this.selectorText.match(/,|::/) && $(this.selectorText + ".cssPicker",ParsimonyAdmin.currentWindow).length > 0){
                                var url = styleSheets[i].href.replace("http://" + window.location.host,"").substring(BASE_PATH.length);
				test += '{'+num+':' + url + ',' + this.selectorText + '</div>';
                            }
                        }
                    });
                }
            }
	    console.log(test);
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