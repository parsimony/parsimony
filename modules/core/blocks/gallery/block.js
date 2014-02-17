Parsimony.registerBlock("core-gallery", {
  prototype: {
    createdCallback :  function() {
		this.nbSlides = this.querySelectorAll(".slide").length;
	},
	currentSlide : 1,
	nbSlides : 0
  },
  events:{
	  click:{
		  ".prev": function(e, block){
			  if(block.currentSlide > 1){
				  block.querySelector(".slides").style.left = (-100 * (block.currentSlide - 2)) + "%";
				  block.currentSlide--;
			  }
		  },
		  ".next": function(e, block){
				if(block.currentSlide < block.nbSlides){
					block.querySelector(".slides").style.left = "-" + (block.currentSlide) + "00%";
					block.currentSlide++;
				}
		  }
	  }
  }
 });
