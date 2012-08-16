function blockAdminToolbar() {

    this.initBefore = function () {
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
        
        $(".sidebar").on('click',".openclose",function(){
            var sidebar = $(this).closest(".sidebar");
            sidebar.toggleClass("close");
            ParsimonyAdmin.setCookie(sidebar.data("side") + "ToolbarOpen",( sidebar.hasClass("close") ? "0" : "1"),999);
        });
			
        /* Tabs */
        $('.sidebar').on('click','.mainTab', function(){
            var rel = $(this).attr("rel");
            var parent = $(this).closest(".contenttab");
	    $(".block",parent).hide();
            $("#" + rel).show();
            $(".mainTab",parent).removeClass('active');
	    $(this).addClass('active');
	    ParsimonyAdmin.setCookie($(this).closest(".sidebar").data("side") + "ToolbarPanel",rel,999);
        });
	
		
        $('#admin').on('click','.ssTab',function(){
            $(this).parent().parent().find("ul").hide();
            $(this).parent().parent().find("." + $(this).attr('rel')).show();
            $(this).parent().parent().find(".ssTab").removeClass('active');
            $(this).addClass('active');
        });
    }
    
}

ParsimonyAdmin.setPlugin(new blockAdminToolbar());