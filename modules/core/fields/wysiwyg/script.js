$(document).ready(function() {
	parsiEdit.registerTool("fieldwysiwyg",{
		init: function() {
			if (typeof window['wysiwygy'] == "undefined") {
				window['wysiwygy'] = new wysiwyg();
				window['wysiwygy'].init(".core_wysiwyg, .field_wysiwyg", ["bold", "underline", "italic", "justifyLeft", "justifyCenter", "justifyRight", "strikeThrough", "subscript", "superscript", "orderedList", "unOrderedList", "outdent", "indent", "removeFormat", "createLink", "unlink", "formatBlock", "foreColor", "hiliteColor"]);
				$(".HTML5editorToolbar").hide();
			}
			window['wysiwygy']['widgets']['saveedit'] = function(){

				window['wysiwygy']['widgets']["btn"].call(this);
				
				this.name = "saveedit";
				this.command = "none"; // fix firefox
				this.category = "edit";
				this.position = "-416px -32px";

				this.onClick = function(e, editor, elmt) {
					parsiEdit.tools['fieldwysiwyg'].onSave();
				}

			}
			
			window['wysiwygy']['widgets']['canceledit'] = function(){

				window['wysiwygy']['widgets']["btn"].call(this);
				
				this.name = "canceledit";
				this.command = "none"; // fix firefox
				this.category = "edit";
				this.position = "-448px -32px";

				this.onClick = function(e, editor, elmt) {
					parsiEdit.tools['fieldwysiwyg'].onCancel();
				}

			}

		},
		onClick: function() {
			if(typeof parsiEdit.oldValue == "undefined" || parsiEdit.oldValue == null){
				$(".HTML5editorToolbar").show();
				parsiEdit.oldValue = parsiEdit.currentElmt.innerHTML;
				parsiEdit.currentElmt.setAttribute("contenteditable", "true");
				$(".HTML5editorToolbar").show();
			}
		},
		onSave: function() {
			$.post(BASE_PATH + $(parsiEdit.currentElmt).data("module") + '/callField', {
				TOKEN:TOKEN,
				entity: $(parsiEdit.currentElmt).data("entity"),
				fieldName: $(parsiEdit.currentElmt).data("property"),
				method: 'saveEditInline',
				id: $(parsiEdit.currentElmt).data("id"),
				data: parsiEdit.currentElmt.innerHTML
			}, function(data) {
				if (data != "0") {
					parsiEdit.currentElmt.innerHTML = data;
					parsiEdit.oldValue = null;
					$(".HTML5editorToolbar").hide();
					parsiEdit.currentElmt.setAttribute("contenteditable", "false");
				} else {
					$("#inputEditMode").addClass("error");
				}
			});
		},
		onCancel: function() {
			parsiEdit.currentElmt.innerHTML = parsiEdit.oldValue;
			parsiEdit.oldValue = null;
			$(".HTML5editorToolbar").hide();
			//parsiEdit.currentElmt.setAttribute("contenteditable", "false");
		}

	});
});