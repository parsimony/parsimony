var onorientationchange = function(){
    $("body").removeClass("landscape portrait");
    $("body").addClass(Math.abs(window.orientation == 90)? "landscape" : "portrait");
    
}
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
$(document).ready(function(){
    
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
    
    // Device orientation
    if(typeof window.orientation != "undefined"){
	$("body").addClass(Math.abs(window.orientation == 90)? "landscape" : "portrait");
	window.onorientationchange = onorientationchange;
    }

});