function block_recentposts() {
    
    block.call(this);  
    
    this.name = "recentposts";

    this.stylableElements = {
	"posts list":"ul",
	"posts items":"li",
	"links":"a"
    }
    
}