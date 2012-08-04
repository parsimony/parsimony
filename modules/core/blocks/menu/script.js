function block_menu() {
    
    block.call(this);  
    
    this.name = "menu";

    this.stylableElements = {"Menu container":".parsimenu",
			    "Menu Items":".parsimenu li",
			    "Menu Links Items":".parsimenu a",
			    "Menu Active Items":".parsimenu .current a",
			    "Menu Items Hover":".parsimenu li:hover > a",
			    "Sub Menu Links Hover":".parsimenu ul a:hover",
			    "Sub Menu":".parsimenu ul",
			    "Sub Menu Items":".parsimenu ul li",
			    "Sub Menu links":".parsimenu ul a",
			    "Sub Sub Menu Items":".parsimenu ul ul"}
    
}