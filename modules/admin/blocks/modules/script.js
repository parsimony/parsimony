function blockAdminModules() {

    this.initBefore = function () {

	$(".modeleajout").click(function(e){
	    e.preventDefault();
	    e.stopPropagation();
	    ParsimonyAdmin.displayConfBox(BASE_PATH + "admin/action",t($(this).attr('title')),"TOKEN=" + TOKEN + "&model=" + $(this).attr('rel') + "&action=getViewAdminModel");
	});
	
    }
    
    this.init = function () {
	
        $("#left_sidebar").on('click','div.titleTab', function(){
            var next = $(this).next();
            if(next.is('div')) $(this).next().slideToggle('fast');
        });
	       
	
    }
    this.loadCreationMode = function () {
	//highlight link on list page
	var src = ParsimonyAdmin.currentWindow.location.toLocaleString().replace("http://","");
	var src = src.substring(src.indexOf(BASE_PATH)).replace("?parsiframe=ok","").replace("parsiframe=ok","");
	var itemLink = $('.sublist[data-url="' + src + '"]');
	if(itemLink.length > 0){
	    $(".sublist.selected").removeClass('selected');
	    itemLink.addClass('selected');
	}
    }

}

ParsimonyAdmin.setPlugin(new blockAdminModules());