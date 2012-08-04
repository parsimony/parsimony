function block_recentposts() {
    
    block.call(this);  
    
    this.name = "recentposts";

    this.stylableElements = {
	"form":"form",
	"form parts":"form div",
	"labels":"label",
	"inputs":"input",
	"selects":"select",
	"submit button":".submit",
	"notification":".notify",
	"positive notification":".positive",
	"negative notification":".negative"
    }
    
}