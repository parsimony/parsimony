function blockAdminTree() {

	this.initBefore = function() {

		/* Help on Tree*/
		$('#right_sidebar').on('click', '#treelegend', function() {
			$('#treelegend2').slideToggle();
		}).on('click', '.arrow_tree', function(event) {
			event.stopPropagation();
			this.classList.toggle('down');
			$(this).nextAll('ul,li').toggleClass('none');
		});

	}

	this.init = function() {

		$('#right_sidebar').on('click', '.tree_selector', function(event) {
			event.stopPropagation();
			ParsimonyAdmin.selectBlock(this);
			if ($("#" + this.id.split("treedom_")[1], ParsimonyAdmin.currentBody).length > 0) {
				$("body").animate({
					scrollTop: $("#" + this.id.split("treedom_")[1], ParsimonyAdmin.currentBody).offset().top - 50
				}, "fast");
			}
		}).on('mouseenter', '.tree_selector', function(event) {
			event.stopPropagation();
			var ids = this.id.split("treedom_")[1];
			$(".selection-block:not(#" + ParsimonyAdmin.inProgress + ")", ParsimonyAdmin.currentBody).removeClass("selection-block");
			$("#" + ids, ParsimonyAdmin.currentBody).trigger('mouseover');
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