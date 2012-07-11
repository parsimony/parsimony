var onorientationchange = function(){
    alert(Math.abs(window.orientation == 90)? "landscape" : "portrait");
    $("body").removeClass("landscape").removeClass("portrait");
    $("body").addClass(Math.abs(window.orientation == 90)? "landscape" : "portrait");
    
}
$(document).ready(function(){
    $("a[href^='http://']").attr('target','_blank');
    $("embed").attr("wmode", "transparent");
    var embedTag;
    $("embed").each(function(i) {
	embedTag = $(this).attr("outerHTML");
	if ((embedTag != null) && (embedTag.length > 0)) {
	    embedTag = embedTag.replace(/embed /gi, "embed wmode=\"transparent\" ");
	    $(this).attr("outerHTML", embedTag);
	}
    });
    $('iframe[src*="youtube"]').each(function(){
	var url = $(this).attr("src");
	if(typeof $(this).attr("src") != "undefined" && $(this).attr("src").indexOf("?") == 0) $(this).attr("src",url + "?wmode=transparent");
	else $(this).attr("src",url + "&wmode=transparent");
    });
    //autocomplete
    $("select.autocomplete").each(function(){
	$(this).attr('type','text');
	var obj = $("option", this);
	var arr = $.makeArray(obj);
	$(this).autocomplete({
	    source: arr
	});
    });
    //datepicker
    /*$(function() {
	$( ".datepicker" ).datepicker($.datepicker.regional[ "fr" ]);
    });*/
    /* for the placeholder */
    $(".placeholder label,.placeholder input,.placeholder textarea").live('click',function(){
	$(this).parent().addClass('active');
    });
    $(".placeholder input,.placeholder textarea").live('blur',function(){
	if($(this).val().length==0) $(this).parent().removeClass('active');
    });
    $(".placeholder input,.placeholder textarea").each(function(){
	if($(this).val().length > 0) $(this).parent().addClass('active');
    });
    // Fancybox
    if(jQuery().fancybox) { 
	$("a.fancybox").fancybox({
	    'hideOnContentClick': true
	});
    }
    // Device orientation
    if(typeof window.orientation != "undefined"){
	$("body").addClass(Math.abs(window.orientation == 90)? "landscape" : "portrait");
	window.onorientationchange = onorientationchange;
    }

});
function loadBlock(id, params, callback){
    if(!params) params = {};
    if(!callback) window['callback'] = '';
    else window['callback'] = callback;
    $.get(window.location.toLocaleString(), params, function(data) {
	$('#' + id).html($("<div>").append(data).find("#" + id).html());
        if(typeof window['callback'] == 'function') window['callback'].call();
    });
}
var $lang = new Array;
function t(val){
    if($lang[val]){
	return $lang[val];
    }else{
	return val;
    }  
}