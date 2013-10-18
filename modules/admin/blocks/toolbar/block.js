function blockAdminToolbar() {

	this.initBefore = function() {

		/* Tabs */
		$('#admin').on('click', '.icons', function() {
			$(".active", this.parentNode).removeClass("active");
			if(!document.body.classList.contains('close' + this.dataset.sidebar) && document.getElementById(this.dataset.panel).style.display == 'block'){
				document.body.classList.add('close' + this.dataset.sidebar);
			}else{
				this.classList.add("active");
				$("#" + this.dataset.sidebar + "_sidebar .parsiblock").hide();
				$("#" + this.dataset.panel).show();
				document.body.classList.remove('close' + this.dataset.sidebar);
			}
			ParsimonyAdmin.setCookie(this.dataset.sidebar + "ToolbarPanel", this.dataset.panel, 999);
		});

	}

}

Parsimony.registerBlock("admin_toolbar", new blockAdminToolbar());