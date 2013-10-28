$(document).ready(function() {
	parsiEdit.registerTool("fieldwysiwyg",function(elmt){

		if(typeof window['wysiwygy'] == "undefined"){
			window['wysiwygy'] = new wysiwyg();
			window['wysiwygy'].init(".fieldwysiwyg",["bold","underline","italic","justifyLeft","justifyCenter","justifyRight","strikeThrough","subscript","superscript","orderedList","unOrderedList","outdent","indent","removeFormat","createLink","unlink","formatBlock","foreColor","hiliteColor"]);
		}
		elmt.setAttribute("contenteditable", "true");
		if(typeof top.ParsimonyAdmin != "undefined") $(".HTML5editorToolbar", top.document).hide();
		$(".HTML5editorToolbar").show();
		$(document).on('focusout.edit',".fieldwysiwyg",function(e){
			parsiEdit.currentElmt = "";
			$(".HTML5editorToolbar").hide();
			if(typeof top.ParsimonyAdmin != "undefined") $(".HTML5editorToolbar", top.document).hide();
			$(document).off('focusout.edit',".fieldwysiwyg");
			if(typeof top.ParsimonyAdmin == "undefined"){
				$.post(BASE_PATH + $(elmt).data("module") + '/callField',{
					module: $(elmt).data("module"), 
					entity:$(elmt).data("entity"),
					fieldName: $(elmt).data("property"), 
					method:'saveEditInline',
					args:"html=" + encodeURIComponent($(elmt).html()) + "&id=" + $(elmt).data("id")
				},function(data){
					console.log(data);
				});
			}
		});
	});
});