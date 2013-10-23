function blockAdminToolbar() {

	this.initBefore = function() {

		/* Tabs */
		$('#admin').on('click', '.icons', function() {
			var sidebar = this.parentNode.dataset.sidebar;
			if(!document.body.classList.contains('close' + sidebar) && document.getElementById(this.dataset.panel).style.display == 'block'){
				document.body.classList.add('close' + sidebar);
				this.classList.remove("active");
			}else{
				ParsimonyAdmin.displayPanel(this.dataset.panel);
				document.body.classList.remove('close' + sidebar);
			}
			ParsimonyAdmin.setCookie(sidebar + "ToolbarPanel", this.dataset.panel, 999);
		});

	}

}

Parsimony.registerBlock("admin_toolbar", new blockAdminToolbar());