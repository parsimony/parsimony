function block_tabs() {
    
    block.call(this);  
    
    this.name = "tabs";

    this.stylableElements = {
	"tabs container":".tabsContainer",
	"tab":".tabsContainer li",
	"link tab":".tabsContainer a",
	"link tab hover":".tabsContainer a:hover",
	"first link tab":".tabsContainer li:first-child a",
	"last link tab":".tabsContainer li:last-child a"}
}