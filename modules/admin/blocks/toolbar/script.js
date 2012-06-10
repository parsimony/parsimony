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
	
		
        $('#admin').on('click','.ssTab',function(){
            $(this).parent().parent().find("ul").hide();
            $(this).parent().parent().find("." + $(this).attr('rel')).show();
            $(this).parent().parent().find(".ssTab").removeClass('active');
            $(this).addClass('active');
        });
    }
    
}

ParsimonyAdmin.setPlugin(new blockAdminToolbar());