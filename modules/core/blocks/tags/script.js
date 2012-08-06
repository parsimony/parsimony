function block_tags() {
    
    block.call(this);  
    
    this.name = "tags";

    this.stylableElements = {
	"tags list":"ul",
	"tags items":"li",
	"links":"a",
        "x small tags":".xsmall",
        "small tags":".small",
        "medium tags":".medium",
        "large tags":".large",
        "x large tags":".xlarge"
    }
    
}