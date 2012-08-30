function blockAdminMenu() {

    this.initBefore = function () {
	
	/* Orientation and resolution */
	$("#toolbar").on('change','#changeres', function(e) {
	    var bodyIframe = ParsimonyAdmin.currentBody;
	    var res = this.value;
	    $("#currentRes").text(res);
	    if(res == 'max'){
		var height = bodyIframe.offsetHeight + 250;
		if(screen.height > height) height = screen.height - 28;
		ParsimonyAdmin.$iframe.css({
		    "width":  "100%",
		    "height": height + "px"
		});
		res = ["max","max"];
	    }else{
		res = res.split(/x/);
		if($("#changeorientation").length == 0 || ($("#changeorientation").val()=='portrait' && ParsimonyAdmin.getCookie("landscape") == 'portrait')){
		    ParsimonyAdmin.$iframe.css({
			"width": res[0] + "px",
			"height": res[1] + "px"
		    });
		}else{ 
		    ParsimonyAdmin.$iframe.css({
			"width": res[1] + "px",
			"height": res[0] + "px"
		    });
		}
	    }
	    ParsimonyAdmin.setCookie("screenX",res[0],999);
	    ParsimonyAdmin.setCookie("screenY",res[1],999);
	    ParsimonyAdmin.setCookie("landscape",$("#changeorientation").val(),999);
	    ParsimonyAdmin.$currentBody.removeClass("landscape portrait").addClass($("#changeorientation").val());
	    
	})
	.on('change','#changeorientation', function(e) {
	    ParsimonyAdmin.setCookie("landscape",$("#changeorientation").val(),999);
	    $("#changeres").trigger("change");
	});
	
    }
}

ParsimonyAdmin.setPlugin(new blockAdminMenu());