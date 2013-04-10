/**
 * Parsimony
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@parsimony-cms.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Parsimony to newer
 * versions in the future. If you wish to customize Parsimony for your
 * needs please refer to http://www.parsimony.mobi for more information.
 *
 *  @authors Julien Gras et Benoît Lorillot
 *  @copyright  Julien Gras et Benoît Lorillot
 *  @version  Release: 1.0
 *  @category  cms.js
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
function loadBlock(id, params, callback){
    if(!params) params = {};
    if(!callback) window['callback'] = '';
    else window['callback'] = callback;
    $.get(window.location.href.toLocaleString(), params, function(data) {
	$('#' + id).html($("<div>").append(data.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, "")).find("#" + id).html());
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

});