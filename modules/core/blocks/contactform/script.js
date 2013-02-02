function block_contactform() {
    
    block.call(this);  
    
    this.name = "contactform";

    this.stylableElements = {
	"form":"form",
	"form parts":"form div",
	"labels":"label",
	"inputs":"input",
	"textareas":"textarea",
	"selects":"select",
	"submit button":".submit",
	"notification":".notify",
	"positive notification":".positive",
	"negative notification":".negative"
    }
    
}