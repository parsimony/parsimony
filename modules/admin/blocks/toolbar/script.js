function blockAdminToolbar() {

    this.initBefore = function () {
	
	$('.subSidebar').on('dragstart',".handle",function(e){
	    $("#conf_box_overlay").css({"opacity":0,"z-index":0, "display":"block"});
	    var evt = e.originalEvent;
	    var img = document.createElement('img');
	    img.src = "data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==";
	    evt.dataTransfer.setDragImage(img,0,0);
	    evt.dataTransfer.setData("Parsimony/dragSidebar", "drag sidebar"); /* Firefox fix */
	    var dragInfos = {elmt : this.parentNode.parentNode, pageX : evt.pageX, pageY : evt.pageY};
	    dragInfos.side = dragInfos.elmt.getAttribute("data-side");
	    dragInfos.elmt.classList.add('notransition'); /* Firefox fix */
	    dragInfos.left = isNaN(parseFloat(dragInfos.elmt.style.left)) ? 0 : dragInfos.elmt.style.left,
	    dragInfos.right = isNaN(parseFloat(dragInfos.elmt.style.right)) ? 0 : dragInfos.elmt.style.right,
	    dragInfos.top = isNaN(parseFloat(dragInfos.elmt.style.top)) ? 0 : dragInfos.elmt.style.top;
	    ParsimonyAdmin.showOverlay(0);
	    
	    $(document).on('dragover.dragSidebar',dragInfos,function(e){
		e.preventDefault(); /* Firefox fix */
		var evt = e.originalEvent;
		var side;
		var top = parseFloat(dragInfos.top) + evt.pageY - dragInfos.pageY;
		if(dragInfos.side == "left") side = parseFloat(dragInfos.left) + evt.pageX - dragInfos.pageX;
		else side = parseFloat(dragInfos.right) - (evt.pageX - dragInfos.pageX);
		dragInfos.elmt.style.top = (top < 28) ? "28px" : top + "px";
		dragInfos.elmt.style[dragInfos.side] = (side < 0) ? "0px" : side + "px";
		return false;
	    }).on('dragend.dragSidebar',dragInfos,function(){
		$("#conf_box_overlay").css({"z-index":999, "display":"none"});
		$(document).add(ParsimonyAdmin.currentDocument).off('.dragSidebar');
		dragInfos.elmt.classList.remove('notransition');
		ParsimonyAdmin.setCookie("leftToolbarCoordX",$("#left_sidebar").css('left'),999);
                ParsimonyAdmin.setCookie("leftToolbarCoordY",$("#left_sidebar").css('top'),999);
                ParsimonyAdmin.setCookie("rightToolbarCoordX",$("#right_sidebar").css('right'),999);
                ParsimonyAdmin.setCookie("rightToolbarCoordY",$("#right_sidebar").css('top'),999);
	    });
	});
	
	$('.subSidebar').on('dragstart',".resizeHandle",function(e){
	    $("#conf_box_overlay").css({"opacity":0,"z-index":0, "display":"block"});
	    var evt = e.originalEvent;
	    var img = document.createElement('img');
	    img.src = "data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==";
	    evt.dataTransfer.setDragImage(img,0,0);
	    evt.dataTransfer.setData("Parsimony/dragSidebar", "drag sidebar"); /* Firefox fix */
	    var dragInfos = {elmt : this.parentNode.parentNode, pageX : evt.pageX, pageY : evt.pageY};
	    dragInfos.side = dragInfos.elmt.getAttribute("data-side");
	    dragInfos.elmt.classList.add('notransition');
	    dragInfos.width = isNaN(parseFloat(dragInfos.elmt.style.width)) ? 0 : dragInfos.elmt.style.width;
	    $(document).on('dragover.dragSidebar',dragInfos,function(e){
		var evt = e.originalEvent;
		var width;
		if(dragInfos.side == "left") width = parseFloat(dragInfos.width) + evt.pageX - dragInfos.pageX;
		else width = parseFloat(dragInfos.width) - (evt.pageX - dragInfos.pageX);
		dragInfos.elmt.style.width = (width < 150) ? "150px" : width + "px";
		return false;
	    }).on('dragend.dragSidebar',dragInfos,function(){
		$("#conf_box_overlay").css({"z-index":999, "display":"none"});
		$(document).add(ParsimonyAdmin.currentDocument).off('.dragSidebar');
		dragInfos.elmt.classList.remove('notransition');
		ParsimonyAdmin.setCookie("leftToolbarX",$("#left_sidebar").css('width'),999);
                ParsimonyAdmin.setCookie("rightToolbarX",$("#right_sidebar").css('width'),999);
	    });
	});

        $('.sidebar').on('click',".openclose",function(){
            var sidebar = $(this).closest(".sidebar");
            sidebar.toggleClass("close");
            ParsimonyAdmin.setCookie(sidebar.data("side") + "ToolbarOpen",( sidebar.hasClass("close") ? "0" : "1"),999);
        })
		
        /* Tabs */
        .on('click','.mainTab', function(){
            var rel = $(this).attr("rel");
            var parent = $(this).closest(".contenttab");
	    $(".block",parent).hide();
            $("#" + rel).show();
            $(".mainTab",parent).removeClass('active');
	    $(this).addClass('active');
	    ParsimonyAdmin.setCookie($(this).closest(".sidebar").data("side") + "ToolbarPanel",rel,999);
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
        })
        .on('click','.ssTab',function(){
            $(this).parent().parent().find("ul").hide();
            $(this).parent().parent().find("." + $(this).attr('rel')).show();
            $(this).parent().parent().find(".ssTab").removeClass('active');
            $(this).addClass('active');
        });

    }
    
}

ParsimonyAdmin.setPlugin(new blockAdminToolbar());