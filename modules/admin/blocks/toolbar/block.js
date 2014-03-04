function blockAdminToolbar() {

	this.initBefore = function() {

		/* Tabs */
		$('.tabsContainer').on('click', 'a', function() { 
			var sidebar = this.parentNode.parentNode.parentNode.id.split("_")[0];
			var panel = this.getAttribute("href").substring(1);
			if(!document.body.classList.contains('close' + sidebar) && document.getElementById(panel).style.display == 'block'){
				document.body.classList.add('close' + sidebar);
				this.classList.remove("active");
			}else{
				ParsimonyAdmin.displayPanel(panel);
				document.body.classList.remove('close' + sidebar);
			}
			ParsimonyAdmin.setCookie(sidebar + "ToolbarPanel", panel, 999);
		});

	}

}

Parsimony.registerBlock("admin_toolbar", new blockAdminToolbar());