Parsimony.registerBlock("core-tabs", {
  prototype: {
    createdCallback :  function() {
		  var firstTab = this.querySelector(".tabsContainer li");
		  if(firstTab) {
			  firstTab.classList.add("active");
		  }	  
	}
  },
  events:{
	  click:{
		  ".tabsContainer a": function(e, block, target){
			   var lastActiveTab = block.querySelector("li.active");
			   if(lastActiveTab) {
					lastActiveTab.classList.remove("active");
				}
				target.parentNode.classList.add("active");
				var elmts = block.querySelectorAll("#" + block.id + " > .parsiblock");
				Array.prototype.forEach.call(elmts, function(el, i){
					el.style.display = "none";
				});
				document.getElementById(target.getAttribute("href").substring(1)).style.display = "block";
		  }
	  }
  }
 });
