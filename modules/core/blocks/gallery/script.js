function block_gallery() {
    
    block.call(this);  
    
    this.name = "gallery";

    this.stylableElements = {
	"slider":".slider",
	"sldies container":".slides_container",
	"each slide":".slide",
	"slide links":".slide a",
	"images":".slide img",
	"captions container":".caption",
	"caption":".caption p",
	"previous button":".prev",
	"next button":".next",
	"pagination container":".paginationSlides",
	"pagination buttons":".paginationSlides li",
	"pagination links":".paginationSlides a",
	"pagination current slide":".paginationSlides li.current a"}
    
}