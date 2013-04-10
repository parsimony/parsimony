function block_categories() {
    
    block.call(this);  
    
    this.name = "categories";

    this.stylableElements = {
	"categories list":"ul",
	"categories items":"li",
	"links":"a"
    }
    
}