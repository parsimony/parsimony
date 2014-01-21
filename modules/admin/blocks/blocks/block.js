function blockAdminBlocks() {

	this.isAddBlock = false;
	this.lastIdParent = "";
	this.lastIdNextBlock = "";
	this.lastDragTimestamp = 0;
	
	var $this = this;

	this.startDragging = function() {
		ParsimonyAdmin.$currentBody.append(document.getElementById("dropInPage"));
		ParsimonyAdmin.displayPanel("paneltree");
		ParsimonyAdmin.currentBody.addEventListener("dragover", this.dragndroping);
		document.getElementById("tree").addEventListener("dragover", this.dragndroping);
	}
	
	this.stopDragging = function() {
		ParsimonyAdmin.returnToShelter();
		ParsimonyAdmin.currentBody.removeEventListener("dragover", this.dragndroping);
		document.getElementById("tree").removeEventListener("dragover", this.dragndroping);
	}

	this.changeBlockPosition = function(blockType, idBlock, idNextBlock, startIdParentBlock, stopIdParentBlock, startTypeCont, stopTypeCont, action, content) {
		if (typeof startIdParentBlock == "undefined" || typeof stopIdParentBlock == "undefined") {
			//alert(t('Error in your DOM, perhaps an HTML tag isn\'t closed.'));
			return false;
		};
		if(idNextBlock == undefined || idNextBlock == idBlock) idNextBlock = "last";
		var contentToAdd = '';
		if (typeof content != "undefined") contentToAdd = content;
		ParsimonyAdmin.postData(BASE_PATH + "admin/" + action, {
			TOKEN: TOKEN,
			MODULE: ParsimonyAdmin.currentWindow.MODULE, THEMEMODULE: ParsimonyAdmin.currentWindow.THEMEMODULE, THEME: ParsimonyAdmin.currentWindow.THEME, THEMETYPE: ParsimonyAdmin.currentWindow.THEMETYPE,
			popBlock: blockType,
			idBlock: idBlock,
			id_next_block: idNextBlock,
			startParentBlock: startIdParentBlock,
			parentBlock: stopIdParentBlock,
			start_typecont: startTypeCont,
			stop_typecont: stopTypeCont,
			IDPage: $(".core_page", ParsimonyAdmin.currentBody).data('page'),
			content: contentToAdd
		}, function(data) {
			ParsimonyAdmin.execResult(data);
			ParsimonyAdmin.returnToShelter();
			ParsimonyAdmin.updateUI();
		});
	}
	
	/* Method to display visual placeholder during drag 'n drop, in vanilla js for perfs */
	this.dragndroping = function(e) {
		e.stopImmediatePropagation();
		if (e.dataTransfer.types != null) {
			var now  = new Date().getTime();
			if(now - $this.lastDragTimestamp > 40){
				var node = e.target;
				var matchesSelector = (document.documentElement.webkitMatchesSelector || document.documentElement.mozMatchesSelector || document.documentElement.matchesSelector);
				while (node != this){
					if (matchesSelector.call(node, '.parsiblock,.tree_selector')) {
						if (node.classList.contains("tree_selector")){
							var theBlock = ParsimonyAdmin.currentDocument.getElementById(node.id.split("treedom_")[1]);
							var theBlockTree = node;
						}else{
							var theBlock = node;
							var theBlockTree = document.getElementById("treedom_" + node.id);
						}
						if(!$this.isAddBlock){
							/* Check if block is trying to put in itself in move mode */
							if (theBlock.compareDocumentPosition(ParsimonyAdmin.currentDocument.getElementById(ParsimonyAdmin.inProgress)) == 10) {
								return true;
							}
							
							/* Can't move block around him */
							if (ParsimonyAdmin.inProgress == theBlock.id) {
								return true;
							}
						}
						e.preventDefault();
						

						var dropInPage = ParsimonyAdmin.currentDocument.getElementById("dropInPage");
						var offset = node.getBoundingClientRect();

						/* if it's a container and mouse doesn't point boundaries */
						if (theBlockTree.classList.contains("container") && e.clientY - offset.top > 4 && offset.bottom - e.clientY > 4 ) {
							if (theBlock.querySelectorAll(".dropInContainer").length > 0) {
								theBlock.querySelector(".dropInContainerChild").appendChild(dropInPage);
								theBlockTree.appendChild(document.getElementById("dropInTree"), theBlockTree);

							} else {
								theBlock.appendChild(dropInPage);
								theBlockTree.appendChild(document.getElementById("dropInTree"));
							}
						}else{
							var middle = offset.top + (offset.height / 2);
							if (e.clientY < middle) { /* Before */
								theBlock.parentNode.insertBefore(dropInPage, theBlock);
								theBlockTree.parentNode.insertBefore(document.getElementById("dropInTree"), theBlockTree);
							} else { /* after */
								theBlock.parentNode.insertBefore(dropInPage, theBlock.nextElementSibling);
								theBlockTree.parentNode.insertBefore(document.getElementById("dropInTree"), theBlockTree.nextElementSibling);
							}
						}
						$this.lastDragTimestamp = now;
						e.preventDefault();
						return false;
					}
					node = (node.parentNode || this);
				}
			}
		}
		e.preventDefault();
		return true;
	}

	this.loadEditMode = function() {
		
		ParsimonyAdmin.$currentDocument.on('click.edit', '.parsiblock', function(e) {
			var blockInst = ParsimonyAdmin.currentWindow.Parsimony.blocks[this.classList[1]];
			(typeof blockInst == "object" && blockInst["onClickEdit"] == "function") ? blockInst.onClickEdit.apply(this, [e]) : Parsimony.blocks['blockAdmin'].onClickEdit.apply(this, [e]);
		});
	}

	this.loadCreationMode = function() {

		//Dispatch menu action event : configure / design / delete
		$(document).add('#config_tree_selector').on('click.creation', ".config_destroy, .cssblock, .configure_block", function(e) {
			var blockInst = ParsimonyAdmin.currentWindow.Parsimony.blocks[this.classList[1]];
			(typeof blockInst == "object" && blockInst[this.dataset.action] == "function") ? blockInst[this.dataset.action].apply(this, [e]) : Parsimony.blocks['blockAdmin'][this.dataset.action].apply(this, [e]);
		})
		/* Hide overlay when user don't pick a block */
		.on('mouseover.creation', "body", function() {
			document.getElementById("blockOverlay").style.display = "none";
		});

		/* HTML5 drag n drop*/
		$("#panelblocks").on('dragstart.creation', ".admin_core_block", function(event) {
			$this.isAddBlock = true;
			var evt = event.originalEvent;
			evt.dataTransfer.setDragImage(this, 15, 15);
			evt.dataTransfer.setData("parsimony/addblock", JSON.stringify({blockType: this.dataset.block}));
			evt.dataTransfer.effectAllowed = 'copy';
			$this.startDragging();
		});
		$("#parsimonyDND").add('#paneltree').on('dragstart.creation', ".move_block", function(event) {
			$this.isAddBlock = false;
			var evt = event.originalEvent;
			var elmt = ParsimonyAdmin.currentDocument.getElementById(ParsimonyAdmin.inProgress);
			evt.dataTransfer.setDragImage(elmt, 15, 15);
			var startTypeCont = elmt.compareDocumentPosition(ParsimonyAdmin.currentDocument.getElementById("content")) == 10 ? "page" : "theme" ;
			var parentContainer = $(elmt).parent().closest(".core_container"); // parent in case it's a container himself
			if(parentContainer.hasClass("core_page")) startIdParentBlock = parentContainer.data('page');
			else startIdParentBlock = parentContainer.attr('id');
			evt.dataTransfer.setData("parsimony/moveblock", JSON.stringify({idBlock:ParsimonyAdmin.inProgress,startIdParentBlock:startIdParentBlock,startTypeCont:startTypeCont}));
			evt.dataTransfer.effectAllowed = 'copyMove';
			$this.startDragging();
		});

		$('#conf_box_content_inline').on('click.creation', "#dialog-ok", function(e) { 
			e.preventDefault();
			var idBlock = document.getElementById("dialog-id").value;
			var obj = JSON.parse(document.getElementById("dialog-id-options").value);
			if (idBlock != '') {
				var content = '';
				if (typeof obj.content != "undefined")
					content = obj.content;
				var typecont = $("#" + obj.stopIdParentBlock, ParsimonyAdmin.currentDocument).closest(".core_page").length > 0 || obj.stopIdParentBlock == parseInt(obj.stopIdParentBlock) ? 'page' : 'theme'; /* "content" block has a parentId numeric */
				$this.changeBlockPosition(obj.blockType, idBlock, obj.idNextBlock, '', obj.stopIdParentBlock, '', typecont, "addBlock", content);
			} else {
				alert(t('Please enter your ID'));
			}
			ParsimonyAdmin.closeConfBox();
		});

		ParsimonyAdmin.$currentBody.on('click.creation', '.parsiblock', function(e) {
			var blockInst = ParsimonyAdmin.currentWindow.Parsimony.blocks[this.classList[1]];
			(typeof blockInst == "object" && blockInst["onClickCreation"] == "function") ? blockInst.onClickCreation.apply(this, [e]) : Parsimony.blocks['blockAdmin'].onClickCreation.apply(this, [e]);
		})
		.on('mouseover.creation', ".parsiblock", function(event) {
			event.stopImmediatePropagation();
			var offset = this.getBoundingClientRect();
			var leftOffsetFrame = ParsimonyAdmin.iframe.offsetLeft + (ParsimonyAdmin.iframe.classList.contains("sized") ? 40 : 0);
			if (ParsimonyAdmin.inProgress != this.id)
				document.getElementById("blockOverlay").style.cssText = "display:block;top:" + offset.top + "px;left:" + (offset.left + leftOffsetFrame) + "px;width:" + offset.width + "px;height:" + offset.height + "px";
			else
				document.getElementById("blockOverlay").style.display = "none";
		});

		ParsimonyAdmin.$currentBody.add('#paneltree')
		.on('drop.creation', '.core_container,.tree_selector', function(event) {
			event.stopPropagation();
			var evt = event.originalEvent;
			evt.preventDefault(); /* Firefox fix */
			evt.stopPropagation();
			var elmt = $("#dropInPage", ParsimonyAdmin.currentBody);
			/* Hide position of visual tool */
			document.getElementById("parsimonyDND").style.display = "none";
			ParsimonyAdmin.inProgress = "";
			if (elmt.length > 0) {
				var parentContainer = elmt.closest(".core_container");
				$this.lastIdParent = parentContainer.attr("id");
				var stopIdParentBlock = "";
				if(elmt.closest(".core_container").hasClass("core_page")) stopIdParentBlock = parentContainer.data('page');
				else stopIdParentBlock = parentContainer.attr('id');
				var idNextBlock = elmt.next(".parsiblock").attr('id');
				$this.lastIdNextBlock = idNextBlock;
				
				/* Move block action */
				if (evt.dataTransfer.getData("parsimony/moveblock").length > 0) {
					var obj = JSON.parse(evt.dataTransfer.getData("parsimony/moveblock"));

					/* in case move container in container, in case dnd api doesn't do the job */
					if(obj.idBlock == parentContainer.attr('id') || parentContainer[0].compareDocumentPosition(ParsimonyAdmin.currentDocument.getElementById(obj.idBlock)) == 10){
						alert("Can't move container in container");
						return false;
					}
					$this.changeBlockPosition('', obj.idBlock, idNextBlock, obj.startIdParentBlock, stopIdParentBlock, obj.startTypeCont, (elmt.closest(".core_page").length > 0 ? 'page' : 'theme'), "moveBlock");
				} else {
					
					/* Add a block or other types of things */
					var obj;
					if (evt.dataTransfer.getData("parsimony/addblock").length > 0) {
						obj = JSON.stringify({blockType: JSON.parse(evt.dataTransfer.getData("parsimony/addblock")).blockType, stopIdParentBlock: stopIdParentBlock, idNextBlock: idNextBlock});
					} else if (evt.dataTransfer.getData("text/plain").length > 0) {
						obj = JSON.stringify({blockType: "core\\blocks\\wysiwyg", stopIdParentBlock: stopIdParentBlock, idNextBlock: idNextBlock, content: evt.dataTransfer.getData("text/plain")});
					}
					else if (evt.dataTransfer.files != null) {
						/* todo, img upload for all modules/profiles */
						files = event.originalEvent.dataTransfer.files;
						var count = files.length;
						var maxFileSize = 1000000000;
						for (var i = 0; i < count; i++) {
							var file = files[i];
							if ((files[i].type.match(new RegExp("image.*", "g")))) {
								if (maxFileSize > file.size) {
									var fd = new FormData();
									fd.append("fileField", files[0]);
									$.each({action: "upload", path: "profiles/www/modules/core/files", MODULE: MODULE, THEME: THEME, THEMETYPE: THEMETYPE, THEMEMODULE: THEMEMODULE}, function(i, val) {
										fd.append(i, val);
									});
									var xhr = new XMLHttpRequest();
									xhr.open("POST", BASE_PATH + "admin/action");
									xhr.upload.file = xhr.file = file;
									xhr.upload.addEventListener("progress", function(event) {
										if (event.lengthComputable) {
											var progress = (event.loaded / event.total) * 100;
										}
									}, false);
									xhr.addEventListener("load", function(event) {
										var response = jQuery.parseJSON(event.target.response)
										obj = JSON.stringify({blockType: "core\\blocks\\image", stopIdParentBlock: stopIdParentBlock, idNextBlock: idNextBlock, content: "core/files/" + response.name});
										$("#dialog-id-options").val(obj);
									}, false);
									xhr.send(fd);
								}
							}
						}
					}

					ParsimonyAdmin.displayConfBox("#dialog");
					$("#dialog-id-options").val(obj);
					$("#dialog-id").val('').trigger("focus");
				}
			}
			$this.stopDragging();
		});
		
		document.addEventListener("dragend", function( event ) {
			$this.stopDragging();
		 }, false);
		
	}

	this.unloadCreationMode = function() {
		$(document).add('#config_tree_selector').off('.creation');
		$("#panelblocks").off('.creation');
		$("#parsimonyDND").add('#paneltree').off('.creation');
		$("#parsimonyDND").hide();
		$('#conf_box_content_inline').off('.creation');
		ParsimonyAdmin.$currentBody.off('.creation');
		ParsimonyAdmin.$currentBody.add('#paneltree').off('.creation');
	}

}


function blockAdmin() {
	
	this.onClickEdit = function(e) {
		e.stopPropagation();
	}

	this.onClickCreation = function(e) {
		e.stopPropagation();
		if (ParsimonyAdmin.inProgress != this.id) {
			ParsimonyAdmin.selectBlock(this.id);
		}
		if (e.trad != true && e.link != true)
			ParsimonyAdmin.closeParsiadminMenu();
		else
			ParsimonyAdmin.openParsiadminMenu(e.pageX || ($(window).width() / 2), e.pageY || ($(window).height() / 2));
	}

	this.onConfigure = function() {
		var parentId = '';
		var inProgress = $("#treedom_" + ParsimonyAdmin.inProgress);
		if (inProgress.length > 0) {
			if (inProgress.parent().closest(".core_container").attr("id") == "treedom_content")
				parentId = inProgress.parent().closest("#treedom_content").data('page');
			else
				parentId = inProgress.parent().closest(".core_container").attr('id').replace("treedom_", "");
		}
		ParsimonyAdmin.displayConfBox(BASE_PATH + "admin/action", "TOKEN=" + TOKEN + "&MODULE=" + ParsimonyAdmin.currentWindow.MODULE + "&THEMEMODULE=" + ParsimonyAdmin.currentWindow.THEMEMODULE + "&THEME=" + ParsimonyAdmin.currentWindow.THEME + "&THEMETYPE=" + ParsimonyAdmin.currentWindow.THEMETYPE + "&idBlock=" + ParsimonyAdmin.inProgress + "&parentBlock=" + parentId + "&typeProgress=" + ParsimonyAdmin.typeProgress + "&action=" + this.getAttribute('rel') + "&IDPage=" + $(".core_page", ParsimonyAdmin.currentBody).data('page'));
	}

	this.onDesign = function(e) {
		e.preventDefault();
		Parsimony.blocks['admin_css'].displayCSSConf(CSSTHEMEPATH, "#" + ParsimonyAdmin.inProgress);
		ParsimonyAdmin.displayPanel("panelcss");
	}

	this.onCreate = function() {

	}

	this.onDelete = function(e) {
		ParsimonyAdmin.destroyBlock();
	}

	this.onSaveConfig = function() {

	}

}

Parsimony.registerBlock("blockAdmin", new blockAdmin());
Parsimony.registerBlock("admin_blocks", new blockAdminBlocks());