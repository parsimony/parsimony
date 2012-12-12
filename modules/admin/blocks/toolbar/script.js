function blockAdminToolbar() {

    this.initBefore = function () {
		
        /* Tabs */
        $('.sidebar').on('click','.icons', function(){
            var elmt = this.parentNode;
            var rel = elmt.getAttribute("rel");
            var parent = $(elmt).closest(".contenttab");
	    $(".block",parent).hide();
            if( elmt.classList.contains('active')) {
                elmt.classList.remove('active');
		rel = '';
            }else {
                $(".mainTab",parent).removeClass('active');
                elmt.classList.add('active');
            }
	    ParsimonyAdmin.setCookie($(elmt).closest(".sidebar").data("side") + "ToolbarPanel",rel,999);
        });

        $('#admin').on('click','.ssTab',function(){
            var parent = $(this).parent().parent();
            parent.find(".tabPanel").hide();
            parent.find("." + this.getAttribute('rel')).show();
            parent.find(".ssTab").removeClass('active');
            this.classList.add('active');
        });

    }
    
}

ParsimonyAdmin.setPlugin(new blockAdminToolbar());