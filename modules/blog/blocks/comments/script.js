function block_comments() {
    
    block.call(this);  
    
    this.name = "comments";

    this.stylableElements = {
	"comments list":"ul",
	"comments items":"li",
	"links":"a"
    }
    
}