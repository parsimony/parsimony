$(document).ready(function() {
	/* Register tool to edit block wysiwyg */
	parsiEdit.registerTool("blockwysiwyg", {
		init: function() {

			if (typeof window['wysiwygy'] == "undefined") {
				window['wysiwygy'] = new wysiwyg();
				window['wysiwygy'].init(".core_wysiwyg, .field_wysiwyg", ["bold", "underline", "italic", "justifyLeft", "justifyCenter", "justifyRight", "strikeThrough", "subscript", "superscript", "orderedList", "unOrderedList", "outdent", "indent", "removeFormat", "createLink", "unlink", "formatBlock", "foreColor", "hiliteColor"]);
				$(".HTML5editorToolbar").hide();
			}

			window['wysiwygy']['widgets']['savewysiwygblock'] = function() {

				window['wysiwygy']['widgets']["btn"].call(this);
				this.name = "savewysiwygblock";
				this.command = "none"; // fix firefox
				this.category = "edit";
				this.position = "-416px -32px";
				this.onClick = function(e, editor, elmt) {
					window.parsiEdit.tools['blockwysiwyg'].onSave();
				}

			}

			window['wysiwygy']['widgets']['cancelwysiwygblock'] = function() {

				window['wysiwygy']['widgets']["btn"].call(this);
				this.name = "cancelwysiwygblock";
				this.command = "none"; // fix firefox
				this.category = "edit";
				this.position = "-448px -32px";
				this.onClick = function(e, editor, elmt) {
					window.parsiEdit.tools['blockwysiwyg'].onCancel();
				}

			}
				
		},
		onClick: function() {
			if(typeof parsiEdit.oldValue == "undefined" || parsiEdit.oldValue == null){
				parsiEdit.oldValue = parsiEdit.currentElmt.innerHTML;
				parsiEdit.currentElmt.setAttribute("contenteditable", "true");
				$(".HTML5editorToolbar").show();
			}
		},
		onSave: function() {
			var module = THEMEMODULE;
			var theme = THEME;
			var idPage = '';
			if ($(parsiEdit.currentElmt).closest(".core_page").length > 0) {
				theme = '';
				module = MODULE;
				idPage = $("#content").data('page');
			}
			$.post(BASE_PATH + module + '/callBlock', {
				TOKEN:TOKEN,
				idPage: idPage,
				theme: theme,
				method: 'saveWYSIWYG',
				id: parsiEdit.currentElmt.id,
				html: parsiEdit.currentElmt.innerHTML
			}, function(data) {
				if (data == 1) {
					parsiEdit.oldValue = null;
					$(".HTML5editorToolbar").hide();
					parsiEdit.currentElmt.setAttribute("contenteditable", "false");
				} else {
					$("#inputEditMode").addClass("error");
				}
			});
		},
		onCancel: function() {
			if(parsiEdit.oldValue != null) {
				parsiEdit.currentElmt.innerHTML = parsiEdit.oldValue;
				parsiEdit.oldValue = null;
			}
			$(".HTML5editorToolbar").hide();
			//parsiEdit.currentElmt.setAttribute("contenteditable", "false");
		}

	});
});