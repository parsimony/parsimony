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

$(document).on("click.edit",".usereditinline",function(e){
    if(parsiEdit.currentElmt != this) parsiEdit.onClick(this, $(this).data("click"));
});