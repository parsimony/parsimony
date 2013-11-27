function blockAdminModules() {

	this.init = function() {

		$("#left_sidebar").on('click', 'div.titleTab', function() {
			var next = $(this).next();
			if(next.is('div')) $(this).next().slideToggle('fast');
		});

	}
	
	this.model = function(module, entity) {
		$(".models .sublist").removeClass("active");
		$('[href="#modules/model/' + module + '/' + entity + '"]').addClass("active");
		ParsimonyAdmin.displayConfBox(BASE_PATH + "admin/action", "TOKEN=" + TOKEN + "&model=" + module + " - " + entity + "&action=getViewAdminModel", true);
	}
	
	this.page = function(module, idPage) {
		ParsimonyAdmin.displayConfBox(BASE_PATH + "admin/action", "TOKEN=" + TOKEN + "&module=" + module + "&page=" + idPage + "&action=getViewUpdatePage");
	}

	this.settings = function(module) {
		ParsimonyAdmin.displayConfBox(BASE_PATH + "admin/action", "TOKEN=" + TOKEN + "&module=" + module + "&action=getViewModuleAdmin");
	}
	
	this.permissions = function() {
		ParsimonyAdmin.displayConfBox(BASE_PATH + "admin/action", "TOKEN=" + TOKEN + "&action=getViewAdminRights");
	}
	
	this.profile = function() {
		ParsimonyAdmin.displayConfBox(BASE_PATH + "admin/action", "TOKEN=" + TOKEN + "&action=getViewUserProfile");
	}
	
	this.language = function() {
		ParsimonyAdmin.displayConfBox(BASE_PATH + "admin/action", "TOKEN=" + TOKEN + "&action=getViewAdminLanguage");
	}
	
	this.add = function() {
		ParsimonyAdmin.displayConfBox(BASE_PATH + "admin/action", "TOKEN=" + TOKEN + "&action=getViewAddModule");
	}

	this.loadCreationAndEditMode = function() {
		//highlight link on list page
		var src = ParsimonyAdmin.currentWindow.location.href.toLocaleString().replace("http://", "");
		var src = src.substring(src.indexOf(BASE_PATH)).replace("?preview=ok", "").replace("preview=ok", "");
		var itemLink = $('.sublist[data-url="' + src + '"]');
		if (itemLink.length > 0) {
			$(".sublist.selected").removeClass('selected');
			itemLink.addClass('selected');
		}

		/* Manage navigation */
		$("#modules").on("click.edit", ".gotopage", function(e) {
			if (!e.target.classList.contains("ui-icon")) {
				ParsimonyAdmin.goToPage(this.dataset.title, this.dataset.url);
				return false;
			}
		})

		/* Sort pages */
		.on("dragstart.sortPages", ".gotopage", function(e) {
			$("#conf_box_overlay").css({"opacity": 0, "z-index": 0, "display": "block"});
			var evt = e.originalEvent;
			evt.dataTransfer.effectAllowed = 'move';
			evt.dataTransfer.setData("Parsimony/dragSidebar", "drag page"); /* Firefox fix */
			var dragInfos = {elmt: this, list: this.parentNode.parentNode};
			dragInfos.width = isNaN(parseFloat(dragInfos.elmt.style.width)) ? 0 : dragInfos.elmt.style.width;
			$('li',dragInfos.list).on('dragover.sortPages',dragInfos,function(e){
				if(dragInfos.elmt != this) this.parentNode.insertBefore(dragInfos.elmt, this);
					return false;
			})
			$(document).on('dragend.sortPages', dragInfos, function() {
				$("#conf_box_overlay").css({"z-index": 999, "display": "none"});
				$(document).add('li', dragInfos.list).off('.sortPages');
				var list = Array();
				$('li', dragInfos.list).each(function() {
					if (this.id)
						list.push(this.id);
				});
				$.post(BASE_PATH + "admin/reorderPages", {module: $(dragInfos.list).data("module"), order: list}, function(data) {
					ParsimonyAdmin.notify(t("The changes have been saved"), "positive");
				});
			});
		});


	}

	this.unloadCreationAndEditMode = function() {
		$("#modules").off('.edit');
	}

	this.loadCreationMode = function() {
		this.loadCreationAndEditMode();

		/* Link to dbdesigner */
		$("#modules").on('click.edit', '.gotoDBDesigner', function() {
			this.querySelector("form").submit();
			return false;
		});

	}

	this.unloadCreationMode = function() {
		this.unloadCreationAndEditMode();
		$("#modules").off('.edit');

	}

	this.loadEditMode = function() {
		this.loadCreationAndEditMode();
	}

	this.unloadEditMode = function() {
		this.unloadCreationAndEditMode();
	}

}
Parsimony.registerBlock("admin_modules", new blockAdminModules());
