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
 * to contact@parsimony.mobi so we can send you a copy immediately.
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
 *  @category  Editinline
 *  Requires: jQuery 
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* EDIT INLINE */ 

/* Manage edit tools */
var parsiEdit = {
    currentElmt : "",
    tools: [],
	
    init :   function(plugin){
        this.tools.push(plugin);
    },
    registerTool :   function(name, func){
        this.tools[name] = func;
    },
    onClick : function(elmt, methodName){
	alert('toto');
        this.currentElmt = elmt;
        if(typeof this.tools[methodName]  != 'undefined'){
            this.tools[methodName]();
        }else if(typeof this.tools['default']  != 'undefined'){
            this.tools['default']();
        }
    }
    
}
 
$(document).ready(function() {
    /* Active editmode if user is'nt an admin */
    if(typeof top.ParsimonyAdmin == "undefined"){
	$(".parsieditinline").addClass('usereditinline').attr("contenteditable", "true");
    }
    $(".usereditinline").on('keyup.edit',function(e){
	$(this).attr("data-modified","1");
    });
    parsiEdit.registerTool("default",function(){
        $(".usereditinline").on('blur.edit',function(e){
            $(".usereditinline").off('blur');
            $(this).html($(this).text());
            $.post(BASE_PATH + $(this).data("module") + '/callField',{
                module: $(this).data("module"), 
                entity:$(this).data("entity"),
                fieldName: $(this).data("property"), 
                method:'saveEditInline',
                args:"html=" + encodeURIComponent($(this).text()) + "&id=" + $(this).data("id")
            },function(data){
                console.log(data);
            });
        });
    });
});

$("body").on("click.edit",".usereditinline",function(e){
    if(parsiEdit.currentElmt != this) parsiEdit.onClick(this, $(this).data("click"));
    else alert(22);
});