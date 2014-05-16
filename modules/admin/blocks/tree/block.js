function blockAdminTree() {

	this.initBefore = function() {

		/* Help on Tree*/
		$('#right_sidebar').on('click', '.arrow_tree', function(event) {
			event.stopPropagation();
			this.classList.toggle('down');
			$(this).nextAll('ul,li').toggleClass('none');
		});

	}

	this.init = function() {

		$('#right_sidebar').on('click', '.tree_selector', function(event) {
			event.stopPropagation();
			var idBlock = this.id.split("treedom_")[1];
			Parsimony.blocks['admin_blocks'].selectBlock(idBlock);
			var blockPreview = $("#" + idBlock, ParsimonyAdmin.currentBody);
			if (blockPreview.length > 0) {
				ParsimonyAdmin.$currentBody.animate({
					scrollTop: blockPreview.offset().top - 50
				}, "fast");
			}
		}).on('mouseenter', '.tree_selector', function(event) {
			event.stopPropagation();
			$("#" + this.id.split("treedom_")[1], ParsimonyAdmin.currentBody).trigger('mouseover');
		})
		.on("mouseenter mouseleave", "#treedom_content", function(event) {
			var dom = ParsimonyAdmin.currentBody.querySelector(".core_page");
			if (dom) {
				if (event.type == 'mouseenter') {
					dom.style.outline = '4px #c8007a solid';
				} else {
					dom.style.outline = 'none';
				}
			}
		});

	}

}

Parsimony.registerBlock("admin_tree", new blockAdminTree());