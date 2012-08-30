function blockAdminTree() {

    this.initBefore = function () {
	
	/* Help on Tree*/
	$('#right_sidebar').on('click','#treelegend',function(){
	    $('#treelegend2').slideToggle();
	}).on('click','.arrow_tree',function(event){
	    event.stopPropagation();
	    $(this).toggleClass('down');
	    $(this).nextAll('ul,li').toggleClass('none');
	});
	
    }
    
    this.initIframe = function () {
	
	$('#treedom_container').attr('title','Site structure');
	$('#treedom_content').attr('title','Dynamic content');
	
    }
    
    this.init = function () {
	
	$('#right_sidebar').on('click','.tree_selector', function(event){
	    event.stopPropagation(); 
	    ParsimonyAdmin.selectBlock(this.id.split("treedom_")[1]);
	    if($("#" + this.id.split("treedom_")[1],ParsimonyAdmin.currentBody).length > 0){
		$("body").animate({
		    scrollTop : $("#" + this.id.split("treedom_")[1],ParsimonyAdmin.currentBody).offset().top -50
		},"fast");
	    }
	}).on('mouseenter','.tree_selector', function(event){
	    event.stopPropagation();
	    var ids = this.id.split("treedom_")[1];
	    $(".selection-block:not(#" + ParsimonyAdmin.inProgress + ")",ParsimonyAdmin.currentBody).removeClass("selection-block");
	    $("#" + ids,ParsimonyAdmin.currentBody).trigger('mouseover');
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
	
    }

}

ParsimonyAdmin.setPlugin(new blockAdminTree());