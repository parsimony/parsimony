
function wysiwyg() {
		
    this.currentElmt;
    this.toolbar;
    this.toolbarWidgets;
    this.widgets = [];
    this.selector;
    this.toolbarDocument;
    this.currentDocument;
    this.enable;
		
    /* Build and init toolbar */
    this.init = function (selector, toolbarWidgets, toolbarDocument, currentDocument) {
		    
        this.selector = selector;
        this.toolbarWidgets = toolbarWidgets;
        if(typeof toolbarDocument == "undefined") this.toolbarDocument = document;
        else this.toolbarDocument = toolbarDocument;
        if(typeof currentDocument == "undefined") this.currentDocument = document;
        else this.currentDocument = currentDocument;
		    
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
        
        /* Listen focus event on all div that matches selector in order to position and resize wysiwyg */
        [].forEach.call( this.currentDocument.querySelectorAll(this.selector), function(el) {
            el.setAttribute("spellcheck", "false");
            el.setAttribute("contenteditable", "true");
            el.addEventListener('focus', $this.prepareDIV, false);
        });
        this.toolbar.style.display = "block";
        this.enable = true;
		    
    }
    
    /* enable contenteditable */
    this.prepareDIV = function (e) {
        if(HTML5editor.enable){
            HTML5editor.setDIV(this.id);
            e.preventDefault();
        }
    }
		
    /* position and resize wysiwyg */
    this.setDIV = function (currentElmt) {
        this.currentElmt = this.currentDocument.getElementById( currentElmt );
        this.currentElmt.setAttribute("contenteditable", "true");
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
    
    this.disable = function () {
        this.enable = false;
        if(typeof this.toolbar != "undefined") {
            this.toolbar.style.display = "none";
            [].forEach.call( this.currentDocument.querySelectorAll(this.selector), function(el) {
                el.setAttribute("contenteditable", "false");
            });
        }
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
        var html = '<div class="HTML5editorAction formatBlock" data-name="' + this.name + '" data-command="' + this.command + '"><span class="value">H1</span><div style="position:absolute;box-shadow: 1px 1px 4px #888;border: 1px solid #BBB;display:none;padding:5px;background:#f9f9f9;" class="propositions">';
        for (var i = 0; i < this.values.length; i++) {
            html += '<div class="option"><' + this.values[i] + '>' + this.values[i] + '</' + this.values[i] + '></div>';
        }
        html += "</div></div>"
        return html;
    }
		
    this.onClick = function(e){
        var select = e.target.parentNode.querySelector('.propositions');
        select.style.display = "block";
        var $this = this;
        [].forEach.call( select.querySelectorAll(".option"), function(el) {
            el.addEventListener('click', function(e) {
		console.dir( $this.command+ " - " + this.innerText);
                HTML5editor.setCommand($this.command,this.innerText);
                select.style.display = "none";
                e.stopPropagation();
            }, false);
        });
    }
		
}


function wysiwyg_colorPicker() {
		
    this.pickerColorImg = "data:image/png;base64,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";
    this.pickerColorImgObj;
    
    this.getAdmin = function(){
	this.pickerColorImgObj = new Image();
        this.pickerColorImgObj.src = this.pickerColorImg;
        var html = '<div class="HTML5editorAction" style="position:relative;width:22px;height:22px;background:url(' + this.icon + ') no-repeat;" data-name="' + this.name + '"data-command="' + this.command + '">';
        html += '<div style="position:absolute;top:22px;box-shadow: 1px 1px 4px #888;border: 1px solid #BBB;display:none;padding:5px;background:#f9f9f9;" class="foreColorPicker">';
        html += '<div style="float:left;width:22px;height:22px;margin: 1px;border: 1px solid #999;" class="picked_color_preview"></div>';
        html += '<div style="float:left;width:50px;height:22px;margin-left: 7px;" class="picked_color_rgb"></div><div style="clear:both"></div>';
        html += '<canvas style="width:256px;height:256px;" class="mycolorpicker"></canvas>';
        html += "</div>"
        return html;
    }
		
    this.onClick = function(e){
        var myPicker = e.target.querySelector('.foreColorPicker');
        myPicker.style.display = "block";
        var c = e.target.querySelector('.mycolorpicker');
        var ctx = c.getContext('2d');
        c.width  = this.pickerColorImgObj.width;
        c.height = this.pickerColorImgObj.height;
        ctx.drawImage(this.pickerColorImgObj,0,0);
        var $this = this;
        c.addEventListener('mousemove', function(event){
            var x = event.layerX - c.offsetLeft;
            var y = event.layerY - c.offsetTop;
            var img_data = ctx.getImageData(x, y, 1, 1).data;
            var rgb = $this.rgbToHex(img_data[0],img_data[1],img_data[2]);
            myPicker.querySelector('.picked_color_preview').style.backgroundColor = rgb;
            myPicker.querySelector('.picked_color_rgb').innerHTML = rgb;
        });
        c.addEventListener('click', function(event){
            event.stopPropagation();
            HTML5editor.setCommand($this.command,myPicker.querySelector('.picked_color_rgb').innerHTML);
            myPicker.style.display = "none";
        });
    }
    
    this.rgbToHex = function(r, g, b){
	return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
    }
		
}

function wysiwyg_foreColor() {
    
    wysiwyg_colorPicker.call(this);
		
    this.name = this.command = "foreColor";
    this.category = "format";
    this.icon = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH1gEEDyMK75EyNwAAAi5JREFUOMut0k9I02EYB/Dv+74/98fy56opYymUa61RQ1I6denfpRIMDLzoTcmDHYTA6NhFtB0qFEHsFOQxgyFUsA4aqbHTmC1xiX+alPsna7r22/u8HbzoXF3sOX+fz/s8vA9DmWruemnjnG0SKQ0AOGdFIlUTGu/MlGa1cgBj6L5xqYF3tfgAAOOBMH8/H+sG8ORAtszrXBN8x3//umk6/F0BQKOrhj1+MV0oSrKGxjtpb56XAiTlNZezWuhHTZgMLqjJ4IIC5zihWwRJebU0fwDQBJ7eueIRs5ENKCVXlJIrc5E4zrsdQhN49k/A1zFWb9KY1113HK+DEZnPF/ry+ULfTCgm9WodhlH0+jrG6v8KcODR7cvn2JfVNOI/MiBJUyRpKpHMYjO1A6fDzqDwsCzgbR8xS5LdF9wO9vbToiKSo5GJHiMy0WNIkqPRpXVlr6llUhbvedtHzAe+kaRsazzrZKmcxPJ6koyC0eu5+7wXAIqGgcxWVtoddcJisSL3K9sG4NU+QHA27DlTx5fi22i+6BMWs0ClWcMRs4BurUCVVROzX9NYP1Yrctns8D7A1er3mSq4TsKKdx8+y2QiKcodWGWVTZ5s8AoFpbta/b7Ymwfh3VNlGHI4nWJxNYXkZgJEZF4O9Bf2Np9uGTTlttLbxu88bHaHSG6sDQG4yXf3p6ZoJIq5mY9QRAOlzQCwHOgvQKnBWHgeP9e+QRE14X8UO3VrQB0G4Ied4A+X1/MuJnM+zQAAAABJRU5ErkJggg==";
		
}

function wysiwyg_hiliteColor() {
    
    wysiwyg_colorPicker.call(this);
		
    this.name = this.command = "hiliteColor";
    this.category = "format";
    this.icon = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAe5JREFUeNqMUz1LI1EUPe9FRJGIsqAgmDjuBKZKoYuCilqk0EYrG3+AYiUWIhayKUQQwWA5hUHFzir+ghijRjEiFpIwJCAWwoZMxM6YzOydcSabHaPxwuFy7nvncO/7YKoK6Po/pNMdkOWx4ObmUVDT3mvOXB2cECXoNny+P3pnZ/l3dc2BcafBWCwm4fBwBKenklksl8vY2prG+fk7TyQk7OxM4+rK5B8MMDqaQnd3DtGoWDFYXo4gHhfx+PgDZ2ciFhcjGBhImeu5HHB/D1xeWgbh8CC83hxKpZK5oTobM9vciGIReH1FV3UHJ4riRibT/p8wGAxgeFiBx5PH0JCCjY0AdfITLy9oIpNjMpZtg3FN05ggqMzIxM1swOtVTe7xqMzvzxDPTJJBoK8P/b29mKFbmWtAnXh+JgdWgN9fmKDW1w3x0xMKNzfIUqNJ/pnw9vaB5i+YBtRuLfE8bUt+6GB7m2NvL1bhX4mN9YrB7m7REvJvi41gq6ucHk3NSX4JgrCWSChTTnE6DUQiQCpVQ7WwwOFyuXwEORQK6aIoXhwc4DocRv/KCiBJdU5dlhlsMef8mDG2RMKWukIr3PE4rvN52OJZqrURWgkthGZCo3V2xtzMeQtumhc0b7KnR9vPZnFnGbzVgPlt/gowAIULFFoZgfsOAAAAAElFTkSuQmCC";
		
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
HTML5editor.setWidget(new wysiwyg_foreColor());
HTML5editor.setWidget(new wysiwyg_hiliteColor());