function block_query() {
    
    block.call(this);  
    
    this.name = "query";

    this.stylableElements = {
	"each line":".itemscope",
	"each property":".itemprop",
	"pagination links":".pagination a",
	"active pagination links":".pagination a.active",
	"pagination links hover":".pagination a:hover",
	"no results message":".noResults"
    }
    
}