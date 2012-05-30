
function wysiwyg() {
		
    this.currentElmt;
    this.toolbar;
    this.toolbarWidgets;
    this.widgets = [];
    this.selector;
    this.toolbarDocument;
    this.currentDocument;
		
    /* Build and init toolbar */
    this.init = function (selector, toolbarWidgets, toolbarDocument, currentDocument) {
		    
	this.selector = selector;
	this.toolbarWidgets = toolbarWidgets;
	if(typeof toolbarDocument == "undefined") this.toolbarDocument = document;
	else this.toolbarDocument = toolbarDocument;
	if(typeof currentDocument == "undefined") this.currentDocument = document;
	else this.currentDocument = currentDocument;
	console.dir(toolbarDocument);
	console.dir(currentDocument);
		    
	/* If it's not already done */
	if(!this.toolbarDocument.getElementById("HTML5editorToolbar")){
	    var toolbar = this.toolbarDocument.createElement("div");
	    toolbar.id = "HTML5editorToolbar";
	    toolbar.innerHTML = '<ul class="tabs"></ul><div class="commands"></div>';
	    this.toolbarDocument.querySelector("body").appendChild(toolbar);
	    this.toolbar = toolbar;
	    for (var i=0; i < this.toolbarWidgets.length; i++) {
		var widget = this.widgets[this.toolbarWidgets[i]];
		var tag = this.toolbarDocument.createElement("div");
		tag.classList.add('btn');
		tag.innerHTML = widget.getAdmin();
		if(!this.toolbarDocument.getElementById("toolbar_" + widget.category)){
		    var tabTitle = this.toolbarDocument.createElement("a");
		    tabTitle.setAttribute("href", "#" + "toolbar_" + widget.category);
		    tabTitle.innerHTML = widget.category;
		    var item = this.toolbarDocument.createElement("li");
		    item.appendChild(tabTitle);
		    this.toolbar.querySelector(".tabs").appendChild(item);
		    var tab = this.toolbarDocument.createElement("div");
		    tab.id = "toolbar_" + widget.category;
		    this.toolbar.querySelector(".commands").appendChild(tab);
		}
		this.toolbarDocument.getElementById("toolbar_" + widget.category).appendChild(tag); 
			
	    }
			
	    $this = this;
			
	    /* Listen click action on BTN */
	    [].forEach.call( this.toolbar.querySelectorAll(".HTML5editorAction"), function(el) {
		el.addEventListener('click', function(e) {
		    $this.widgets[this.getAttribute("data-name")].onClick(e);
		    e.preventDefault();
		}, false);
	    });
			
	    /* Listen change action on selects*/
	    [].forEach.call( this.toolbar.querySelectorAll(".HTML5editorAction"), function(el) {
		el.addEventListener('change', function(e) {
		    $this.widgets[this.getAttribute("data-name")].onClick(e);
		    e.preventDefault();
		}, false);
	    });
			
	    /* Manage Tabs */
	    [].forEach.call( this.toolbar.querySelectorAll(".tabs a"), function(el) {
		el.addEventListener('click', function(e) {
		    [].forEach.call( $this.toolbar.querySelectorAll(".commands > div"), function(elmt) {
			elmt.style.display = "none";
		    });
		    [].forEach.call( $this.toolbar.querySelectorAll(".tabs a"), function(elmt) {
			elmt.classList.remove("current");
		    });
		    el.classList.add("current");
		    $this.toolbarDocument.querySelector(this.getAttribute("href")).style.display = "block";
		    e.preventDefault();
		}, false);
	    });
			
	    /* Listen focus event on all div that matches selector in order to position and resize wysiwyg */
	    [].forEach.call( this.currentDocument.querySelectorAll(this.selector), function(el) {
		el.setAttribute("spellcheck", "false");
		el.setAttribute("contenteditable", "true");
		el.addEventListener('focus', function(e) {
		    $this.setDIV(this.id);
		    e.preventDefault();
		}, false);
	    });
			
	    /* Listen click event on all div that matches selector to check wich command could be exec or not */
	    [].forEach.call( this.currentDocument.querySelectorAll(this.selector), function(el) {
		el.addEventListener('click', function(e) {
		    $this.checkCommands();
		    e.preventDefault();
		}, false);
	    });
			
	    /* Init for first use */
	    this.setCommand("styleWithCSS");
	    this.toolbar.querySelector(".tabs a").click();
	    $this.setDIV(this.currentDocument.querySelector(this.selector).id);
			
	}
		    
    }
		
    /* position and resize wysiwyg */
    this.setDIV = function (currentElmt) {
	this.currentElmt = this.currentDocument.getElementById( currentElmt );
	this.toolbar.style.width = this.currentElmt.offsetWidth + "px";
	this.toolbar.style.top = (this.currentElmt.offsetTop - 60) + "px";
	this.toolbar.style.left = this.currentElmt.offsetLeft + "px";
    }

    /* Exec a command on current active contenteditable div */
    this.setCommand = function (command, value) { 
	this.currentDocument.execCommand(command, false, value);
	this.checkCommands();
    }
		
    /* Add new widget */
    this.setWidget = function (obj) {
	this.widgets[obj.name] = obj;
    }
		
    /* Check wich command could be exec or not */
    this.checkCommands = function () {
	[].forEach.call( this.toolbar.querySelectorAll("[data-command]"), function(elmt) {
	    var el = elmt.parentNode;
	    if($this.currentDocument.queryCommandEnabled(elmt.getAttribute("data-command"))){
		el.classList.remove("inactive"); 
	    }else{
		if(!el.classList.contains("inactive")) el.classList.add("inactive");
	    }
	    if($this.currentDocument.queryCommandState(elmt.getAttribute("data-command"))){
		if(!el.classList.contains("active")) el.classList.add("active");
	    }else{
		el.classList.remove("active");
	    }
	});
    }
		
}
	    
var HTML5editor = new wysiwyg();

function wysiwyg_btn() {
		
    this.category = "format";
		
    this.getAdmin = function(){
	return '<button style="background:url(' + this.icon + ');width:22px;height:22px;border:0;" class="HTML5editorAction" data-name="' + this.name + '" data-command="' + this.command + '"></button>';
    }
		
    this.onClick = function(){
	HTML5editor.setCommand(this.command);
    }
		
}
	    
function wysiwyg_bold() {
		
    wysiwyg_btn.call(this);  
		
    this.name = this.command = "bold";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAID/AMDAwAAAACH5BAEAAAAALAAAAAAWABYAQAInhI+pa+H9mJy0LhdgtrxzDG5WGFVk6aXqyk6Y9kXvKKNuLbb6zgMFADs=";

}
	    
function wysiwyg_underline() {
		
    wysiwyg_btn.call(this);
		
    this.name = this.command = "underline";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAKECAAAAAF9vj////////yH5BAEAAAIALAAAAAAWABYAAAIrlI+py+0Po5zUgAsEzvEeL4Ea15EiJJ5PSqJmuwKBEKgxVuXWtun+DwxCCgA7";

}
	    
function wysiwyg_italic() {
		
    wysiwyg_btn.call(this);
		
    this.name = this.command = "italic";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAKEDAAAAAF9vj5WIbf///yH5BAEAAAMALAAAAAAWABYAAAIjnI+py+0Po5x0gXvruEKHrF2BB1YiCWgbMFIYpsbyTNd2UwAAOw==";

}
	    
function wysiwyg_justifyLeft() {
		
    wysiwyg_btn.call(this); 
		
    this.name = this.command = "justifyLeft";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAID/AMDAwAAAACH5BAEAAAAALAAAAAAWABYAQAIghI+py+0Po5y02ouz3jL4D4JMGELkGYxo+qzl4nKyXAAAOw==";

}
	    
function wysiwyg_justifyCenter() {
		
    wysiwyg_btn.call(this);
		
    this.name = this.command = "justifyCenter";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAID/AMDAwAAAACH5BAEAAAAALAAAAAAWABYAQAIfhI+py+0Po5y02ouz3jL4D4JOGI7kaZ5Bqn4sycVbAQA7";

}
	    
function wysiwyg_justifyRight() {
		
    wysiwyg_btn.call(this);

    this.name = this.command = "justifyRight";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAID/AMDAwAAAACH5BAEAAAAALAAAAAAWABYAQAIghI+py+0Po5y02ouz3jL4D4JQGDLkGYxouqzl43JyVgAAOw==";

}
	    
function wysiwyg_strikeThrough() {
		
    wysiwyg_btn.call(this);
		
    this.name = this.command = "strikeThrough";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAID/AMDAwAAAACH5BAEAAAAALAAAAAAWABYAQAInhI+pa+H9mJy0LhdgtrxzDG5WGFVk6aXqyk6Y9kXvKKNuLbb6zgMFADs=";

}
	    
function wysiwyg_subscript() {
		
    wysiwyg_btn.call(this); 
		
    this.name = this.command = "subscript";
    this.icon = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAARhJREFUeNrsU4FtgzAQfHcDM4I9AiuYEegIMAKMACOYEfAI8QjOCHgEPIL7b+SoIUoaJW2kSjnpxfsRp/vzAfDGvwXbD4ZhiPtZXdcwTROEENIZe/YT8cd+oJQC730qznkqKSUTQgBV13WPKSY45+I4jmeKrbV3Kb2q+M88ztBaR2PM6TzPMxRF8bxiWv876PJ+RXHTNLEsy9Rnv3ELqKqKLeuakmPslhK8X2gryW4Sr/gRqaM0tG3LcgSzYrLEer5tpTjYYwCP/OPnOfGFFX3fp6hRZikdVJSIHDd6r0RIJdFzH16ciqegD0ts9BLdssa7L+8WDm4jsuhDhz4fPYDYbMe/dIvkQ8Skkp4ne7ExvWTwxkvxJcAAeyp5PYg93M0AAAAASUVORK5CYII=";
		 
}
	    
function wysiwyg_superscript() {
		
    wysiwyg_btn.call(this);
		
    this.name = this.command = "superscript";
    this.icon = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAARVJREFUeNrsU4FtwyAQfEddAI9gRrBHsEdgBToCHsEeAY9gRggjOCPACDDClyeKFSdK1Fpqqko56cSDxPPc3wO88W9R7LnkQkBajY15zxjAZ8c3uT72JL4kFC0De4rg4y98Wc0Oibfnh5dpPAzD3etCCJimCWI8/znF+Z4+Ojx5AJkkaXhZPK24bVvw3mey1BUi57yoqgqISik4LgGJpO0gGNB7zgUkPnXFsiw4juOmYmvtWqnUZ03XnqXA9FtXPLSb1hqNMet+nmcoy/Lb9nzYPKryGqTxT3B4+eRJKbGu6xxf9E7yQNd1u6YVQhpXshxpvA6BUpgckUmN3VUxVUorWYusR+j7/tbreW2apoA3/gxfAgwA01J5qh+9fJUAAAAASUVORK5CYII=";
		
}
	    
function wysiwyg_orderedList() {
		
    wysiwyg_btn.call(this);
		
    this.name = "orderedList";
    this.command = "insertOrderedList";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAMIGAAAAADljwliE35GjuaezxtHa7P///////yH5BAEAAAcALAAAAAAWABYAAAM2eLrc/jDKSespwjoRFvggCBUBoTFBeq6QIAysQnRHaEOzyaZ07Lu9lUBnC0UGQU1K52s6n5oEADs=";
		
}
	    
function wysiwyg_unOrderedList() {
		
    wysiwyg_btn.call(this);
		
    this.name = "unOrderedList";
    this.command = "insertUnorderedList";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAMIGAAAAAB1ChF9vj1iE33mOrqezxv///////yH5BAEAAAcALAAAAAAWABYAAAMyeLrc/jDKSesppNhGRlBAKIZRERBbqm6YtnbfMY7lud64UwiuKnigGQliQuWOyKQykgAAOw==";
				
}

function wysiwyg_undo() {
		
    wysiwyg_btn.call(this);
		
    this.name = this.command = "undo";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAOMKADljwliE33mOrpGjuYKl8aezxqPD+7/I19DV3NHa7P///////////////////////yH5BAEKAA8ALAAAAAAWABYAAARR8MlJq7046807TkaYeJJBnES4EeUJvIGapWYAC0CsocQ7SDlWJkAkCA6ToMYWIARGQF3mRQVIEjkkSVLIbSfEwhdRIH4fh/DZMICe3/C4nBQBADs=";
				
}
	    
function wysiwyg_redo() {
		
    wysiwyg_btn.call(this);  
		
    this.name = this.command = "redo";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAMIHAB1ChDljwl9vj1iE34Kl8aPD+7/I1////yH5BAEKAAcALAAAAAAWABYAAANKeLrc/jDKSesyphi7SiEgsVXZEATDICqBVJjpqWZt9NaEDNbQK1wCQsxlYnxMAImhyDoFAElJasRRvAZVRqqQXUy7Cgx4TC6bswkAOw==";

}
	    
function wysiwyg_copy() {
		
    wysiwyg_btn.call(this);
		
    this.name = this.command = "copy";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAIQcAB1ChBFNsTRLYyJYwjljwl9vj1iE31iGzF6MnHWX9HOdz5GjuYCl2YKl8ZOt4qezxqK63aK/9KPD+7DI3b/I17LM/MrL1MLY9NHa7OPs++bx/Pv8/f///////////////yH5BAEAAB8ALAAAAAAWABYAAAWG4CeOZGmeaKqubOum1SQ/kPVOW749BeVSus2CgrCxHptLBbOQxCSNCCaF1GUqwQbBd0JGJAyGJJiobE+LnCaDcXAaEoxhQACgNw0FQx9kP+wmaRgYFBQNeAoGihCAJQsCkJAKOhgXEw8BLQYciooHf5o7EA+kC40qBKkAAAGrpy+wsbKzIiEAOw==";

    this.onClick = function(){
	if(typeof(window.clipboardData)=="undefined") {
	    alert("Your navigateur preferences don't allow this action. Please use CTRL + C");
	}else{
	    HTML5editor.setCommand("copy");
	}
    }
}
	    
function wysiwyg_paste() {
		
    wysiwyg_btn.call(this);
		
    this.name = this.command = "paste";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAIQUAD04KTRLY2tXQF9vj414WZWIbXmOrpqbmpGjudClFaezxsa0cb/I1+3YitHa7PrkIPHvbuPs+/fvrvv8/f///////////////////////////////////////////////yH5BAEAAB8ALAAAAAAWABYAAAWN4CeOZGmeaKqubGsusPvBSyFJjVDs6nJLB0khR4AkBCmfsCGBQAoCwjF5gwquVykSFbwZE+AwIBV0GhFog2EwIDchjwRiQo9E2Fx4XD5R+B0DDAEnBXBhBhN2DgwDAQFjJYVhCQYRfgoIDGiQJAWTCQMRiwwMfgicnVcAAAMOaK+bLAOrtLUyt7i5uiUhADs=";

    this.onClick = function(){
	if(typeof(window.clipboardData)=="undefined") {
	    alert("Your navigateur preferences don't allow this action. Please use CTRL + V");
	}else{
	    HTML5editor.setCommand("copy");
	}
    }
}
	    
function wysiwyg_cut() {
		
    wysiwyg_btn.call(this);
		
    this.name = this.command = "cut";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAIQSAB1ChBFNsRJTySJYwjljwkxwl19vj1dusYODhl6MnHmOrpqbmpGjuaezxrCztcDCxL/I18rL1P///////////////////////////////////////////////////////yH5BAEAAB8ALAAAAAAWABYAAAVu4CeOZGmeaKqubDs6TNnEbGNApNG0kbGMi5trwcA9GArXh+FAfBAw5UexUDAQESkRsfhJPwaH4YsEGAAJGisRGAQY7UCC9ZAXBB+74LGCRxIEHwAHdWooDgGJcwpxDisQBQRjIgkDCVlfmZqbmiEAOw==";
		
    this.onClick = function(){
	if(typeof(window.clipboardData)=="undefined") {
	    alert("Your navigateur preferences don't allow this action. Please use CTRL + X");
	}else{
	    HTML5editor.setCommand("copy");
	}
    }
}
	    
function wysiwyg_outdent() {
		
    wysiwyg_btn.call(this);
		
    this.name = this.command = "outdent";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAMIHAAAAADljwliE35GjuaezxtDV3NHa7P///yH5BAEAAAcALAAAAAAWABYAAAM2eLrc/jDKCQG9F2i7u8agQgyK1z2EIBil+TWqEMxhMczsYVJ3e4ahk+sFnAgtxSQDqWw6n5cEADs=";
		
		  
}
	    
function wysiwyg_indent() {
		
    wysiwyg_btn.call(this); 
		
    this.name = this.command = "indent";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAOMIAAAAADljwl9vj1iE35GjuaezxtDV3NHa7P///////////////////////////////yH5BAEAAAgALAAAAAAWABYAAAQ7EMlJq704650B/x8gemMpgugwHJNZXodKsO5oqUOgo5KhBwWESyMQsCRDHu9VOyk5TM9zSpFSr9gsJwIAOw==";
		
		 
}
	    
function wysiwyg_removeFormat() {
		
    wysiwyg_btn.call(this);
		
    this.name = this.command = "removeFormat";
    this.icon = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAABGdBTUEAALGPC/xhBQAAAAZiS0dEAP8A/wD/oL2nkwAAAAlwSFlzAAAOxAAADsQBlSsOGwAAAAd0SU1FB9oECQMCKPI8CIIAAAAIdEVYdENvbW1lbnQA9syWvwAAAuhJREFUOMtjYBgFxAB501ZWBvVaL2nHnlmk6mXCJbF69zU+Hz/9fB5O1lx+bg45qhl8/fYr5it3XrP/YWTUvvvk3VeqGXz70TvbJy8+Wv39+2/Hz19/mGwjZzuTYjALuoBv9jImaXHeyD3H7kU8fPj2ICML8z92dlbtMzdeiG3fco7J08foH1kurkm3E9iw54YvKwuTuom+LPt/BgbWf3//sf37/1/c02cCG1lB8f//f95DZx74MTMzshhoSm6szrQ/a6Ir/Z2RkfEjBxuLYFpDiDi6Af///2ckaHBp7+7wmavP5n76+P2ClrLIYl8H9W36auJCbCxM4szMTJac7Kza////R3H1w2cfWAgafPbqs5g7D95++/P1B4+ECK8tAwMDw/1H7159+/7r7ZcvPz4fOHbzEwMDwx8GBgaGnNatfHZx8zqrJ+4VJBh5CQEGOySEua/v3n7hXmqI8WUGBgYGL3vVG7fuPK3i5GD9/fja7ZsMDAzMG/Ze52mZeSj4yu1XEq/ff7W5dvfVAS1lsXc4Db7z8C3r8p7Qjf///2dnZGxlqJuyr3rPqQd/Hhyu7oSpYWScylDQsd3kzvnH738wMDzj5GBN1VIWW4c3KDon7VOvm7S3paB9u5qsU5/x5KUnlY+eexQbkLNsErK61+++VnAJcfkyMTIwffj0QwZbJDKjcETs1Y8evyd48toz8y/ffzv//vPP4veffxpX77z6l5JewHPu8MqTDAwMDLzyrjb/mZm0JcT5Lj+89+Ybm6zz95oMh7s4XbygN3Sluq4Mj5K8iKMgP4f0////fv77//8nLy+7MCcXmyYDAwODS9jM9tcvPypd35pne3ljdjvj26+H2dhYpuENikgfvQeXNmSl3tqepxXsqhXPyc666s+fv1fMdKR3TK72zpix8nTc7bdfhfkEeVbC9KhbK/9iYWHiErbu6MWbY/7//8/4//9/pgOnH6jGVazvFDRtq2VgiBIZrUTIBgCk+ivHvuEKwAAAAABJRU5ErkJggg==";
		
}
	    
function wysiwyg_createLink() {
		
    wysiwyg_btn.call(this);
		
    this.name = this.command = "createLink";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAOMKAB1ChDRLY19vj3mOrpGjuaezxrCztb/I19Ha7Pv8/f///////////////////////yH5BAEKAA8ALAAAAAAWABYAAARY8MlJq7046827/2BYIQVhHg9pEgVGIklyDEUBy/RlE4FQF4dCj2AQXAiJQDCWQCAEBwIioEMQBgSAFhDAGghGi9XgHAhMNoSZgJkJei33UESv2+/4vD4TAQA7";
    this.category = "insert";
		
    this.onClick = function(){
	var link = prompt('URL','http:\/\/');
	if(link && link.length > 0){
	    HTML5editor.setCommand("createLink", link);
	}
    }
}
	    
function wysiwyg_unlink() {
		
    this.name = this.command = "unlink";
    this.category = "insert";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAOMKAB1ChDRLY19vj3mOrpGjuaezxrCztb/I19Ha7Pv8/f///////////////////////yH5BAEKAA8ALAAAAAAWABYAAARY8MlJq7046827/2BYIQVhHg9pEgVGIklyDEUBy/RlE4FQF4dCj2AQXAiJQDCWQCAEBwIioEMQBgSAFhDAGghGi9XgHAhMNoSZgJkJei33UESv2+/4vD4TAQA7";
		
    wysiwyg_btn.call(this);  
}
	    
function wysiwyg_formatBlock() {
		
    this.name = this.command = "formatBlock";
    this.category = "format";
    this.values = ["h1","h2","h3","h4","h5","h6"];
		
    this.getAdmin = function(){
	var html = '<select class="actionSelect" data-command="' + this.command + '">';
	console.dir(this.values);
	for (var i = 0; i < this.values.length; i++) {
	    html += "<option>" + this.values[i] + "</option>";
	}
	html += "</select>"
	return html;
    }
		
    this.onClick = function(e){
	HTML5editor.setCommand(this.command,e.srcElement.value);
    }
		
}
	    
HTML5editor.setWidget(new wysiwyg_bold());
HTML5editor.setWidget(new wysiwyg_underline());
HTML5editor.setWidget(new wysiwyg_italic());
HTML5editor.setWidget(new wysiwyg_justifyLeft());
HTML5editor.setWidget(new wysiwyg_justifyCenter());
HTML5editor.setWidget(new wysiwyg_justifyRight());
HTML5editor.setWidget(new wysiwyg_strikeThrough());
HTML5editor.setWidget(new wysiwyg_subscript());
HTML5editor.setWidget(new wysiwyg_superscript());
HTML5editor.setWidget(new wysiwyg_orderedList());
HTML5editor.setWidget(new wysiwyg_unOrderedList());
HTML5editor.setWidget(new wysiwyg_undo());
HTML5editor.setWidget(new wysiwyg_redo());
HTML5editor.setWidget(new wysiwyg_copy());
HTML5editor.setWidget(new wysiwyg_paste());
HTML5editor.setWidget(new wysiwyg_cut());
HTML5editor.setWidget(new wysiwyg_outdent());
HTML5editor.setWidget(new wysiwyg_indent());
HTML5editor.setWidget(new wysiwyg_removeFormat());
HTML5editor.setWidget(new wysiwyg_createLink());
HTML5editor.setWidget(new wysiwyg_unlink());
HTML5editor.setWidget(new wysiwyg_formatBlock());