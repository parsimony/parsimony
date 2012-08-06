function block_tags() {
    
    block.call(this);  
    
    this.name = "tags";

    this.stylableElements = {
	"tags list":"ul",
	"tags items":"li",
	"links":"a"
    }
    
}