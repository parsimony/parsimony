
$(document).ready(function(){
    $(document).on('click','a', function(e){
	if($(this).attr("href").substring(0,1) != '#' && $(this).attr("href").substring(0,7) != 'http://'){
	    e.preventDefault();
	    loadPage($(this).attr('href'));
	}
    });
    if((window.history && history.pushState)) history.replaceState({url:document.location.href}, document.title, document.location.href);
    
});

function loadPage(url, isHistory){
    $("#content").removeClass("flip");
    $.get(url + "?nostructure=yes", function(data) {
	/*$("#content").fadeOut("speed",function(){
	    $(this).html(data).fadeIn("speed")
	});*/
	$("#content").html(data).addClass("flip");
	if(typeof isHistory == "undefined"){
	    var hist = new Object() ;
	    hist.url = url;
	    if((window.history && history.pushState)) history.pushState(hist, "", url);
	}
	
    });
}

window.onpopstate = function( event ){
    var data = event.state;
    if(data && data.url){
	loadPage( data.url, false );
    }
}