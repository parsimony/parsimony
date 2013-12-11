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
 *  @category  Editinline
 *  Requires: jQuery 
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* Manage edit tools */
var parsiEdit = {
	currentElmt: "",
	tools: {},
	registerTool: function(name, tool) {
		this.tools[name] = tool;
	},
	init: function() {
		for (var i in this.tools) {
			if (typeof this.tools[i]["init"] == 'function') {
				this.tools[i]["init"]();
			}
		}
	},
	destroy: function() {
		for (var i in this.tools) {
			if (typeof this.tools[i]["destroy"] == 'function') {
				this.tools[i]["destroy"]();
			}
		}
	}

}

$(document).ready(function() {
	
	
	parsiEdit.registerTool("default", {
		
		init: function(){
			
			$(document.body).append('<div id="editArea"><div id="toolbarEdit"><div id="editLabel"></div><div id="saveEdit">Save</div><div id="cancelEdit">✖</div></div><input type="text" id="inputEditMode"><iframe id="editForm"></iframe></div>');

			$(document).on("click.edit", ".parsieditinline", function() {
				if(parsiEdit.currentElmt && parsiEdit.currentElmt != this){
					$(parsiEdit.currentElmt).removeClass("editing");
					parsiEdit.tools[parsiEdit.currentElmt.dataset.mode].onCancel(this);
				}
				parsiEdit.currentElmt = this;
				$(this).addClass("editing");
				parsiEdit.tools[this.dataset.mode].onClick(this);
			})

			/* For default and form modes */	
			.on("click.edit", "#saveEdit", function() {
				parsiEdit.tools[parsiEdit.currentElmt.dataset.mode].onSave(this);
			})
			.on("click.edit", "#cancelEdit", function() {
				parsiEdit.tools[parsiEdit.currentElmt.dataset.mode].onCancel(this);
			})
			.on("input.edit", "#inputEditMode", function() {
				parsiEdit.tools[parsiEdit.currentElmt.dataset.mode].onInput(this);
			});


		},
		destroy: function(){
			$("#editArea").remove();
			$(document).off("click.edit");
		},
		onClick: function() {
			$("#editLabel").text($(parsiEdit.currentElmt).data("label"));
			$("#editForm").hide();
			$("#inputEditMode").show();
			this.adapt();
			this.oldValue = parsiEdit.currentElmt.textContent;
			document.getElementById("inputEditMode").value = parsiEdit.currentElmt.textContent;
		},
		onSave: function() {
			$.post(BASE_PATH + $(parsiEdit.currentElmt).data("module") + '/callField', {
				TOKEN:TOKEN,
				entity: $(parsiEdit.currentElmt).data("entity"),
				fieldName: $(parsiEdit.currentElmt).data("property"),
				method: 'saveEditInline',
				data: parsiEdit.currentElmt.textContent,
				id: $(parsiEdit.currentElmt).data("id")
			}, function(data) {
				if (data != "0") {
					this.oldValue = data;
					$("#editArea").hide();
				} else {
					$("#inputEditMode").addClass("error");
				}
			});
		},
		onCancel: function() {
			parsiEdit.currentElmt.textContent = this.oldValue;
			$("#editArea").hide();
		},
		onInput: function() {
			parsiEdit.currentElmt.textContent = document.getElementById("inputEditMode").value;
			this.adapt();
		},
		adapt: function() {
			var dimm = parsiEdit.currentElmt.getBoundingClientRect();
			$("#editArea").css({display: "block", top: dimm.top, left: dimm.left});
			$("#inputEditMode").removeClass("error").css({display: "block", width: dimm.width, height: dimm.height});
		}

	});
	
	parsiEdit.registerTool("form", {
		
		init: function() {
			
			$("#editForm").on("load.edit", function(e) {
				var body = $("#editForm").contents().find("body");
				if (body.find("form").length > 0) {
					var dimm = body[0].getBoundingClientRect();
					$("#editArea").css({height: dimm.height});
				}else{
					var result = body.html();
					if(result != 0){
						parsiEdit.currentElmt.innerHTML = result;
						$("#editArea").hide();
					}else{
						alert("Error");
					}
				}
			});
		},
		onClick: function() {
			$("#editLabel").text($(parsiEdit.currentElmt).data("label"));
			var src = BASE_PATH + $(parsiEdit.currentElmt).data("module") + '/callField';
			src += "?entity=" + $(parsiEdit.currentElmt).data("entity");
			src += "&fieldName=" + $(parsiEdit.currentElmt).data("property");
			src += "&method=" + 'editInlineForm&id=' + $(parsiEdit.currentElmt).data("id") + "&preview=ok";
			document.getElementById("editForm").src = src;
			
			var dimm = parsiEdit.currentElmt.getBoundingClientRect();
			$("#editArea").css({display: "block", top: dimm.top + "px", left: dimm.left, width: dimm.width + "px" });
			$("#editForm").show();
			$("#inputEditMode").hide();
		},
		onSave: function() {
			$("#editForm").contents().find("body form")[0].submit();
		},
		onCancel: function() {
			$("#editArea").hide();
		}

	});
});