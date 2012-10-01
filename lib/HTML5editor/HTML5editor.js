function wysiwyg() {
		
    this.currentElmt;
    this.toolbar;
    this.toolbarWidgets;
    this.widgets = [];
    this.selector;
    this.toolbarDocument;
    this.currentDocument;
    this.enable;
    this.isMultiple = false;
    this.textarea;
    this.allowedTags = {"div":["id","class","style","dir","lang","title"],
                    "span":["id","class","style","dir","lang","title"],
                    "p":["id","class","style","dir","lang","title"],
                    "pre":["id","class","style","dir","lang","title"],
                    "br":["id","class","style","title"]};
    this.allowedStyles = {},
		
    /* Build and init toolbar */
    this.init = function (selector, toolbarWidgets, toolbarDocument, currentDocument) {
	
        /* Init WYSIWYG vars*/
        this.selector = selector;
        this.toolbarWidgets = toolbarWidgets;
        if(typeof toolbarDocument == "undefined") this.toolbarDocument = document;
        else this.toolbarDocument = toolbarDocument;
        if(typeof currentDocument == "undefined") this.currentDocument = document;
        else this.currentDocument = currentDocument;
        var $this = this;
	
        /* Select element to convert into WYSIWYG */
        var el = $(this.selector,this.currentDocument);
        
        /* If it's not already done */
        if($('.HTML5editorToolbar[data-selector="' + this.selector + '"]',this.toolbarDocument).length == 0){

            /* We build a toolbar which is associated with the WYSIWYG */
            this.toolbar = this.buildToolbar();
	
            /* If textarea mode : simple WYSIWYG conneted with a textarea */
            if(el.get(0).tagName == "TEXTAREA") {
                this.textarea = el;
                this.textarea.before(this.toolbar);
                var singleEditor = $("<div>",this.toolbarDocument);
                singleEditor.attr("id", "editor" + ($(".HTML5editorToolbar",this.currentDocument).length + 1));
                singleEditor.attr("contenteditable","true");
                singleEditor.attr("spellcheck", "false");
                singleEditor.attr("data-textarea",this.selector);
                singleEditor.get(0).style = el.get(0).style;
                singleEditor.addClass("HTML5Editor");
                singleEditor.html(this.textarea.val());
                this.currentElmt = singleEditor.get(0);
                this.textarea.hide();
                this.textarea.before(singleEditor);
                this.selector = "#" + singleEditor.attr("id");
            }else{
                /* If multiple mode: one toolbar for severals contenteditable divs */
                $("body",this.toolbarDocument).append(this.toolbar);
                this.toolbar.addClass("multiple");
                this.isMultiple = true;
            }
        }
	
        this.toolbar.show();
        
        $(this.currentDocument).on("mouseup",this.selector,function(e){
            $this.checkCommands();
        })
	.on("paste drop",this.selector,function(e){
            $this.sanitize(this);
        });
	
        $(this.selector,this.currentDocument).each(function(){
            if($this.isMultiple){
                $(this).attr("spellcheck", "false");
                $(this).attr("contenteditable", "true");
                /* Listen focus event on all div that matches selector in order to position and resize wysiwyg */
                $(this).on("focus",function (e) {
                    if($this.enable){
                        $this.setDIV(e.target);
			console.log($this.format($(this).html()));
			this.innerHTML = $this.format(this.innerHTML);
                    }
                });
		$(this).on("keyup drop paste",function(e) {
                   $(this).attr("data-changed","1");
		   $this.checkCommands();
                });
            }else{
                /* Copy the content of WYSIWYG to the textarea */
                $(this).on("keyup blur",function(e) {
                    $this.textarea.val($this.currentElmt.innerHTML);
		    $this.checkCommands();
                    e.preventDefault();
                });
            }
        });
        
        /* Init for first use */
        this.setCommand("styleWithCSS");
        $(".tabs a:first",this.toolbar).trigger("mousedown");
        if(this.isMultiple) $this.setDIV($(this.selector, this.currentDocument).get(0));	


        this.enable = true;  
    }
 
    this.buildToolbar = function(){
        var toolbar = $("<div>");
        toolbar.addClass("HTML5editorToolbar");
        toolbar.data("selector",this.selector);
        toolbar.html('<iframe class="popover"></iframe><ul class="tabs"></ul><div class="commands"></div>');
        for (var i=0; i < this.toolbarWidgets.length; i++) {
            eval("var widget = new wysiwyg_" + this.toolbarWidgets[i] + "();");
            var tag = $("<div>",this.toolbarDocument);
            tag.addClass('btn');
            tag.html(widget.getAdmin());
            if(typeof widget.allowedTags != "undefined") jQuery.extend(this.allowedTags, widget.allowedTags);
            if(typeof widget.allowedStyles != "undefined") jQuery.extend(this.allowedStyles, widget.allowedStyles);
            this.widgets[widget.name] = widget;
            if($(".toolbar_" + widget.category,toolbar).length == 0){
                var tabTitle = $("<a>");
                tabTitle.attr("href", "#" + "toolbar_" + widget.category);
                tabTitle.html(widget.category);
                var item = $("<li>");
                item.append(tabTitle);
                $(".tabs",toolbar).append(item);
                var tab = $("<div>");
                tab.addClass("toolbar_" + widget.category);
                $(".commands",toolbar).append(tab);
            }
            $(".toolbar_" + widget.category,toolbar).append(tag); 	
        }
	
	this.format = function( code ) {
		var html = '';
		var pad = 0;
		code = code.replace(/(>)\s*(<)(\/*)/g, '$1\r\n$2$3');
		jQuery.each(code.split('\r\n'), function(index, node) {
			var indent = 0;
			if (node.match( /.+<\/\w[^>]*>$/ )) {
				indent = 0;
			} else if (node.match( /^<\/\w/ )) {
				if (pad != 0) pad -= 1;
			} else if (node.match( /^<\w[^>]*[^\/]>.*$/ ) && !node.match( /^<(br|img).*>\s?$/ ) ) {
				indent = 1;
			}
			var padding = '';
			for (var i = 0; i < pad; i++) padding += '    ';
			html += padding + node + '\r\n';
			pad += indent;
		});
		return html;
	}

        var $this = this;
	
        /* Manage Tabs */
        $(toolbar).on("mousedown",".tabs a",function(e){
            $(this).addClass("current");
            $(".commands > div",$this.toolbar).hide();
            $(".tabs a",$this.toolbar).removeClass("current");
            $($(e.target).attr("href").replace("#","."),$this.toolbar).show();
            e.preventDefault();
        });
	
	$(toolbar).on("mousedown","*",function(e){
	    //$(this).triggerHandler("click");
            e.preventDefault();
        });

        /* Listen click action on BTN && Listen change action on selects*/
        $(toolbar).on("mousedown change",".HTML5editorAction",function(e){
            $this.widgets[$(this).attr("data-name")].onClick(e, $this);
	    e.preventDefault();
        });
        
        return toolbar;
    }
		
    /* position and resize wysiwyg */
    this.setDIV = function (currentElmt) {
        this.currentElmt = currentElmt;
        if(this.isMultiple) this.toolbar.css("width",$(this.currentElmt).width());
        var offset = $(this.currentElmt).offset();
        var top = offset.top - 55;
        if(top < 0){
            top = offset.top + $(this.currentElmt).height() + 30;
        }
        this.toolbar.css("top",top + "px");
        this.toolbar.css("left", offset.left + "px");
        $(".HTML5editorToolbar").show();
    }

    /* Exec a command on current active contenteditable div */
    this.setCommand = function (command, value) { 
        this.currentDocument.execCommand(command, false, value);
	$(this.currentElmt).attr("data-changed","1");
	this.checkCommands();
    }
		
    /* Check wich command could be exec or not */
    this.checkCommands = function () {
        var $this = this;
        $("[data-command]",this.toolbar).each(function(){
            var el = $(this).parent();
            var enabled = $this.currentDocument.queryCommandEnabled($(this).attr("data-command"));
            if(enabled){
                el.removeClass("inactive"); 
            }else{
		if(!el.hasClass("inactive")) el.addClass("inactive");
            }
	    $this.widgets[$(this).attr("data-name")].setCurrentValue(el, $this.currentDocument.queryCommandValue($(this).attr("data-command")));
            if(enabled && $this.currentDocument.queryCommandState($(this).attr("data-command"))){
                if(!el.hasClass("active")) el.addClass("active");
            }else{
                el.removeClass("active");
            }
        });
    }
    
    this.sanitize = function(elmt) {
        var html = "";
        var tab = $(elmt).html().replace(/\s+/g, " ").split(/(<[^>]*>)/); //.split(/(<\/|<\!--|<[!?]|[&<>])/g); todo manage script, cdata etc..
        for(t in tab){
            var innerTag = "";
            var val = tab[t];
            var tag = val.split(/^(<\/?)([^\s]+)[^a-z]/); // /(<[^>]*[^\/]>)/i ?
            if(typeof tag[1] == "string" && typeof tag[2] == "string"){
                currentTag = tag[2];
                if(!(currentTag in this.allowedTags)){
                    currentTag = "span";
                }
                innerTag = tag[1] + currentTag;
                var attrs = tag[3].split(/([\w\-.:]+)\s*=\s*"([^"]*)"/g);
                for(var i=1; i<attrs.length -1;i = i + 3){
                    var name = attrs[i];
                    var value = attrs[i + 1];
                    if($.inArray(name,this.allowedTags[currentTag])){
                        var newValue = "";
                        if(name == "style"){
                            var styles = value.split(";");
                            for(s in styles){
                                var cutStyle = styles[s].split(":");
                                if(typeof this.allowedStyles[$.trim(cutStyle[0])] != "undefined") { // todo check regex
                                    newValue += $.trim(styles[s]) + ";";
                                }
                            }
                            value = newValue;
                        }
                        innerTag += " " + name + '="' + value + '"';
                    }
                }
                html += " " + innerTag + ">";
            }else{
                html += val;
            }

        }
        $(elmt).html(html);
    },
    
    this.disable = function () {
        this.enable = false;
        if(typeof this.toolbar != "undefined") {
            this.toolbar.hide();
            $(this.selector,this.currentDocument).attr("contenteditable", "false");
        }
    }
		
}

function wysiwyg_btn() {
		
    this.category = "format";
		
    this.getAdmin = function(){
        return '<div style="background:url(' + this.icon + ') no-repeat center;width:25px;height:25px;border:0;" class="HTML5editorAction" data-name="' + this.name + '" data-command="' + this.command + '"></div>';
    }
		
    this.onClick = function(e, editor){
        editor.setCommand(this.command);
    }
    
    this.setCurrentValue = function(elmt, value){}
		
}

function wysiwyg_bold() {
		
    wysiwyg_btn.call(this);  
		
    this.name = this.command = "bold";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAID/AMDAwAAAACH5BAEAAAAALAAAAAAWABYAQAInhI+pa+H9mJy0LhdgtrxzDG5WGFVk6aXqyk6Y9kXvKKNuLbb6zgMFADs=";
    this.allowedStyles = {"font-weight": /.*/};
        
}
	    
function wysiwyg_underline() {
		
    wysiwyg_btn.call(this);
		
    this.name = this.command = "underline";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAKECAAAAAF9vj////////yH5BAEAAAIALAAAAAAWABYAAAIrlI+py+0Po5zUgAsEzvEeL4Ea15EiJJ5PSqJmuwKBEKgxVuXWtun+DwxCCgA7";
    this.allowedStyles = {"text-decoration": /.*/};
    
}
	    
function wysiwyg_italic() {
		
    wysiwyg_btn.call(this);
		
    this.name = this.command = "italic";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAKEDAAAAAF9vj5WIbf///yH5BAEAAAMALAAAAAAWABYAAAIjnI+py+0Po5x0gXvruEKHrF2BB1YiCWgbMFIYpsbyTNd2UwAAOw==";
    this.allowedStyles = {"font-style": /.*/};
    
}
	    
function wysiwyg_justifyLeft() {
		
    wysiwyg_btn.call(this); 
		
    this.name = this.command = "justifyLeft";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAID/AMDAwAAAACH5BAEAAAAALAAAAAAWABYAQAIghI+py+0Po5y02ouz3jL4D4JMGELkGYxo+qzl4nKyXAAAOw==";
    this.allowedStyles = {"text-align": /.*/};
    
}
	    
function wysiwyg_justifyCenter() {
		
    wysiwyg_btn.call(this);
		
    this.name = this.command = "justifyCenter";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAID/AMDAwAAAACH5BAEAAAAALAAAAAAWABYAQAIfhI+py+0Po5y02ouz3jL4D4JOGI7kaZ5Bqn4sycVbAQA7";
    this.allowedStyles = {"text-align": /.*/};
    
}
	    
function wysiwyg_justifyRight() {
		
    wysiwyg_btn.call(this);

    this.name = this.command = "justifyRight";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAID/AMDAwAAAACH5BAEAAAAALAAAAAAWABYAQAIghI+py+0Po5y02ouz3jL4D4JQGDLkGYxouqzl43JyVgAAOw==";
    this.allowedStyles = {"text-align": /.*/};
    
}

function wysiwyg_justifyFull() {
		
    wysiwyg_btn.call(this);

    this.name = this.command = "justifyFull";
    this.icon = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAALBJREFUeNpi/P//PwMlgImBQsACNoUJbE4jEGsBMSMUM2FhM0HxZSAu/ffvHwMjyAtQA0gGIANYkPhrsdiGbCsyG4QdQZqo6oKNBGxEx9ZUd8E2ImyFYWYgNqHIBSB9IIzsgt1E2MwG1MQOpDmAWIFkF6Ane7gLgAxmRkbGyUCmLhZbWYHyHFBbQbazAvEFIPYF4u9gFwA1gwKFF4g5oQFJCPwB4s9AvRADBjQ3AgQYAIOVSAdZa5U/AAAAAElFTkSuQmCC";
    this.allowedStyles = {"text-align": /.*/};
    
}
	    
function wysiwyg_strikeThrough() {
		
    wysiwyg_btn.call(this);
		
    this.name = this.command = "strikeThrough";
    this.icon = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAQAAAC1+jfqAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAACfSURBVCjPY/jPgB8yUFNBiWDBzOy01PKEmZG7sSrIe5dVDqIjygP/Y1GQm5b2P7kDwvbAZkK6S8L/6P8hM32N/zPYu2C1InJ36P/A/x7/bc+YoSooLy3/D4Px/23+SyC5G8kEf0EIbZSmfdfov9wZDCvc0uzLYWyZ/2J3MRTYppn/14eaIvKOvxxDgUma7ju1M/LlkmnC5bwdNIoL7BAAWzr8P9A5d4gAAAAASUVORK5CYII=";
    this.allowedStyles = {"text-decoration": /.*/};
    
}
	    
function wysiwyg_subscript() {
		
    wysiwyg_btn.call(this); 
		
    this.name = this.command = "subscript";
    this.icon = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAARhJREFUeNrsU4FtgzAQfHcDM4I9AiuYEegIMAKMACOYEfAI8QjOCHgEPIL7b+SoIUoaJW2kSjnpxfsRp/vzAfDGvwXbD4ZhiPtZXdcwTROEENIZe/YT8cd+oJQC730qznkqKSUTQgBV13WPKSY45+I4jmeKrbV3Kb2q+M88ztBaR2PM6TzPMxRF8bxiWv876PJ+RXHTNLEsy9Rnv3ELqKqKLeuakmPslhK8X2gryW4Sr/gRqaM0tG3LcgSzYrLEer5tpTjYYwCP/OPnOfGFFX3fp6hRZikdVJSIHDd6r0RIJdFzH16ciqegD0ts9BLdssa7L+8WDm4jsuhDhz4fPYDYbMe/dIvkQ8Skkp4ne7ExvWTwxkvxJcAAeyp5PYg93M0AAAAASUVORK5CYII=";
    this.allowedStyles = {"vertical-align": /.*/};
    
}
	    
function wysiwyg_superscript() {
		
    wysiwyg_btn.call(this);
		
    this.name = this.command = "superscript";
    this.icon = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAARVJREFUeNrsU4FtwyAQfEddAI9gRrBHsEdgBToCHsEeAY9gRggjOCPACDDClyeKFSdK1Fpqqko56cSDxPPc3wO88W9R7LnkQkBajY15zxjAZ8c3uT72JL4kFC0De4rg4y98Wc0Oibfnh5dpPAzD3etCCJimCWI8/znF+Z4+Ojx5AJkkaXhZPK24bVvw3mey1BUi57yoqgqISik4LgGJpO0gGNB7zgUkPnXFsiw4juOmYmvtWqnUZ03XnqXA9FtXPLSb1hqNMet+nmcoy/Lb9nzYPKryGqTxT3B4+eRJKbGu6xxf9E7yQNd1u6YVQhpXshxpvA6BUpgckUmN3VUxVUorWYusR+j7/tbreW2apoA3/gxfAgwA01J5qh+9fJUAAAAASUVORK5CYII=";
    this.allowedStyles = {"vertical-align": /.*/};
    
}
	    
function wysiwyg_orderedList() {
		
    wysiwyg_btn.call(this);
		
    this.name = "orderedList";
    this.command = "insertOrderedList";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAMIGAAAAADljwliE35GjuaezxtHa7P///////yH5BAEAAAcALAAAAAAWABYAAAM2eLrc/jDKSespwjoRFvggCBUBoTFBeq6QIAysQnRHaEOzyaZ07Lu9lUBnC0UGQU1K52s6n5oEADs=";
    this.allowedTags = {"ol":["id","class","style","dir","lang","title"],
                    "li":["id","class","style","dir","lang","title"]};
    this.allowedStyles = {"list-style": /.*/,"list-style-image": /.*/,"list-style-position": /.*/,"list-style-type": /.*/};
}
	    
function wysiwyg_unOrderedList() {
		
    wysiwyg_btn.call(this);
		
    this.name = "unOrderedList";
    this.command = "insertUnorderedList";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAMIGAAAAAB1ChF9vj1iE33mOrqezxv///////yH5BAEAAAcALAAAAAAWABYAAAMyeLrc/jDKSesppNhGRlBAKIZRERBbqm6YtnbfMY7lud64UwiuKnigGQliQuWOyKQykgAAOw==";
    this.allowedTags = {"ul":["id","class","style","dir","lang","title"],
                    "li":["id","class","style","dir","lang","title"]};
    this.allowedStyles = {"list-style": /.*/,"list-style-image": /.*/,"list-style-position": /.*/,"list-style-type": /.*/};
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

    this.onClick = function(e, editor){
        if(typeof(window.clipboardData)=="undefined") {
            alert("Your navigateur preferences don't allow this action. Please use CTRL + C");
        }else{
            editor.setCommand("copy");
        }
    }
}
	    
function wysiwyg_paste() {
		
    wysiwyg_btn.call(this);
		
    this.name = this.command = "paste";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAIQUAD04KTRLY2tXQF9vj414WZWIbXmOrpqbmpGjudClFaezxsa0cb/I1+3YitHa7PrkIPHvbuPs+/fvrvv8/f///////////////////////////////////////////////yH5BAEAAB8ALAAAAAAWABYAAAWN4CeOZGmeaKqubGsusPvBSyFJjVDs6nJLB0khR4AkBCmfsCGBQAoCwjF5gwquVykSFbwZE+AwIBV0GhFog2EwIDchjwRiQo9E2Fx4XD5R+B0DDAEnBXBhBhN2DgwDAQFjJYVhCQYRfgoIDGiQJAWTCQMRiwwMfgicnVcAAAMOaK+bLAOrtLUyt7i5uiUhADs=";

    this.onClick = function(e, editor){
        if(typeof(window.clipboardData)=="undefined") {
            alert("Your navigateur preferences don't allow this action. Please use CTRL + V");
        }else{
            editor.setCommand("copy");
        }
    }
}
	    
function wysiwyg_cut() {
		
    wysiwyg_btn.call(this);
		
    this.name = this.command = "cut";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAIQSAB1ChBFNsRJTySJYwjljwkxwl19vj1dusYODhl6MnHmOrpqbmpGjuaezxrCztcDCxL/I18rL1P///////////////////////////////////////////////////////yH5BAEAAB8ALAAAAAAWABYAAAVu4CeOZGmeaKqubDs6TNnEbGNApNG0kbGMi5trwcA9GArXh+FAfBAw5UexUDAQESkRsfhJPwaH4YsEGAAJGisRGAQY7UCC9ZAXBB+74LGCRxIEHwAHdWooDgGJcwpxDisQBQRjIgkDCVlfmZqbmiEAOw==";
		
    this.onClick = function(e, editor){
        if(typeof(window.clipboardData)=="undefined") {
            alert("Your navigateur preferences don't allow this action. Please use CTRL + X");
        }else{
            editor.setCommand("copy");
        }
    }
}
	    
function wysiwyg_outdent() {
		
    wysiwyg_btn.call(this);
		
    this.name = this.command = "outdent";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAMIHAAAAADljwliE35GjuaezxtDV3NHa7P///yH5BAEAAAcALAAAAAAWABYAAAM2eLrc/jDKCQG9F2i7u8agQgyK1z2EIBil+TWqEMxhMczsYVJ3e4ahk+sFnAgtxSQDqWw6n5cEADs=";
    this.allowedTags = {"blockquote":["id","class","style","dir","lang","title"]};
    this.allowedStyles = {"margin": /.*/,"border": /.*/,"padding": /.*/};
		  
}
	    
function wysiwyg_indent() {
		
    wysiwyg_btn.call(this); 
		
    this.name = this.command = "indent";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAOMIAAAAADljwl9vj1iE35GjuaezxtDV3NHa7P///////////////////////////////yH5BAEAAAgALAAAAAAWABYAAAQ7EMlJq704650B/x8gemMpgugwHJNZXodKsO5oqUOgo5KhBwWESyMQsCRDHu9VOyk5TM9zSpFSr9gsJwIAOw==";
    this.allowedTags = {"blockquote":["id","class","style","dir","lang","title"]};
    this.allowedStyles = {"margin": /.*/,"border": /.*/,"padding": /.*/};
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
    this.allowedTags = {"a":["id","class","style","dir","lang","title","accesskey","tabindex","charset","coords","href","hreflang","name","rel","rev","shape","target"]};
		
    this.onClick = function(e, editor){
        var link = prompt('URL','http:\/\/');
        if(link && link.length > 0){
            editor.setCommand("createLink", link);
        }
    }
}
	    
function wysiwyg_unlink() {
    
    wysiwyg_btn.call(this);	
    
    this.name = this.command = "unlink";
    this.category = "insert";
    this.icon = "data:image/gif;base64,R0lGODlhFgAWAOMKAB1ChDRLY19vj3mOrpGjuaezxrCztb/I19Ha7Pv8/f///////////////////////yH5BAEKAA8ALAAAAAAWABYAAARY8MlJq7046827/2BYIQVhHg9pEgVGIklyDEUBy/RlE4FQF4dCj2AQXAiJQDCWQCAEBwIioEMQBgSAFhDAGghGi9XgHAhMNoSZgJkJei33UESv2+/4vD4TAQA7";
		
      
}

function wysiwyg_selectSquel() {
    
	
    this.name = this.command = "formatBlock";
    this.category = "format";
    this.values = {"p":"Paragraph","h1":"Heading 1","h2":"Heading 2","h3":"Heading 3","h4":"Heading 4","h5":"Heading 5","h6":"Heading 6","pre":"Preformatted","blockquote":"Blockquote"};
    this.allowedTags = {"h1":["id","class","style","dir","lang","title"],
                        "h2":["id","class","style","dir","lang","title"],
                        "h3":["id","class","style","dir","lang","title"],
                        "h4":["id","class","style","dir","lang","title"],
                        "h5":["id","class","style","dir","lang","title"],
                        "h6":["id","class","style","dir","lang","title"]};
    this.template = function(i, val){
	return '<div class="option" data-option="' + i + '"><' + i + '>' + val + '</' + i + '></div>';
    };
    this.defaultText = 'Font Family';
    this.getAdmin = function(){
        return '<div style=";height:25px;border:0;" class="HTML5editorAction select" data-name="' + this.name + '" data-command="' + this.command + '"><span class="temoin temoinSelect">' + this.defaultText + '</span></div>';
    }
		
    this.onClick = function(e, editor){
	var html = "<style>.option{cursor:pointer}</style>";
	for (i in this.values) {
            html += this.template(i, this.values[i]);
        }
	var prop = $('.popover',editor.toolbar).css({width:"150px",height:"200px"}).appendTo($(e.target).closest(".btn")).show().contents().find("body").empty().append(html);
        var $this = this;
	$(".option",prop).on("mousedown",function(e){
	    editor.setCommand($this.command,$(this).data("option"));
	    $('.popover',editor.toolbar).hide();
	    $(".option",prop).off("mousedown");
	});
    }
    
    this.setCurrentValue = function(elmt, type){
        $(".temoin", elmt).text(this.values[type]);
    }		
}
	    
function wysiwyg_formatBlock() {
    
    wysiwyg_selectSquel.call(this);
		
    this.name = this.command = "formatBlock";
    this.category = "format";
    this.values = {"p":"Paragraph","h1":"Heading 1","h2":"Heading 2","h3":"Heading 3","h4":"Heading 4","h5":"Heading 5","h6":"Heading 6","pre":"Preformatted","blockquote":"Blockquote"};
    this.allowedTags = {"h1":["id","class","style","dir","lang","title"],
                        "h2":["id","class","style","dir","lang","title"],
                        "h3":["id","class","style","dir","lang","title"],
                        "h4":["id","class","style","dir","lang","title"],
                        "h5":["id","class","style","dir","lang","title"],
                        "h6":["id","class","style","dir","lang","title"]};
		    
    this.template = function(i, val){
	return '<div class="option" data-option="' + i + '"><' + i + '>' + val + '</' + i + '></div>';
    };
    this.defaultText = 'Format';	
}

function wysiwyg_fontName() {
    
    wysiwyg_selectSquel.call(this);
		
    this.name = this.command = "fontName";
    this.category = "format";
    this.values = {"arial, helvetica, sans-serif":"Arial",
                "'arial black', avant garde;":"Arial Black",
                "'book antiqua', palatino":"Book Antiqua",
                "'comic sans ms', sans-serif":"Comic Sans MS",
                "courier new, courier":"Courier New",
                "georgia, palatino":"Georgia",
                "helvetica":"Helvetica",
                "impact, chicago":"Impact",
                "symbol":"Symbol",
                "tahoma, arial, helvetica, sans-serif":"Tahoma",
                "terminal, monaco":"Terminal",
                "'times new roman', times":"Times New Roman",
                "'trebuchet ms', geneva":"Trebuchet MS",
                "verdana, geneva":"Verdana",
                "webdings":"Webdings",
                "wingdings, 'zapf dingbats'":"Wingdings"};
    this.template = function(i, val){
	return '<div class="option" data-option="' + i + '" style="font-family:' + i + '">' + val + '</div>';
    };
    this.defaultText = 'Font Family';
    this.allowedStyles = {"font-family": /.*/};	
}

function wysiwyg_fontSize() {
    
    wysiwyg_selectSquel.call(this);
		
    this.name = this.command = "fontSize";
    this.category = "format";
    this.values = {"1":"8","2":"10","3":"12","4":"14","5":"18","6":"24","7":"36"};
    this.template = function(i, val){
	return '<div class="option" data-option="' + i + '" style="font-size:' + val + 'pt">' + val + '</div>';
    };
    this.defaultText = 'Font Size';
    this.allowedStyles = {"font-size": /.*/};	
}


function wysiwyg_colorPicker() {
		
    this.pickerColorImg = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAALQAAAC0CAIAAACyr5FlAAAACXBIWXMAAAsTAAALEwEAmpwYAAAKT2lDQ1BQaG90b3Nob3AgSUNDIHByb2ZpbGUAAHjanVNnVFPpFj333vRCS4iAlEtvUhUIIFJCi4AUkSYqIQkQSoghodkVUcERRUUEG8igiAOOjoCMFVEsDIoK2AfkIaKOg6OIisr74Xuja9a89+bN/rXXPues852zzwfACAyWSDNRNYAMqUIeEeCDx8TG4eQuQIEKJHAAEAizZCFz/SMBAPh+PDwrIsAHvgABeNMLCADATZvAMByH/w/qQplcAYCEAcB0kThLCIAUAEB6jkKmAEBGAYCdmCZTAKAEAGDLY2LjAFAtAGAnf+bTAICd+Jl7AQBblCEVAaCRACATZYhEAGg7AKzPVopFAFgwABRmS8Q5ANgtADBJV2ZIALC3AMDOEAuyAAgMADBRiIUpAAR7AGDIIyN4AISZABRG8lc88SuuEOcqAAB4mbI8uSQ5RYFbCC1xB1dXLh4ozkkXKxQ2YQJhmkAuwnmZGTKBNA/g88wAAKCRFRHgg/P9eM4Ors7ONo62Dl8t6r8G/yJiYuP+5c+rcEAAAOF0ftH+LC+zGoA7BoBt/qIl7gRoXgugdfeLZrIPQLUAoOnaV/Nw+H48PEWhkLnZ2eXk5NhKxEJbYcpXff5nwl/AV/1s+X48/Pf14L7iJIEyXYFHBPjgwsz0TKUcz5IJhGLc5o9H/LcL//wd0yLESWK5WCoU41EScY5EmozzMqUiiUKSKcUl0v9k4t8s+wM+3zUAsGo+AXuRLahdYwP2SycQWHTA4vcAAPK7b8HUKAgDgGiD4c93/+8//UegJQCAZkmScQAAXkQkLlTKsz/HCAAARKCBKrBBG/TBGCzABhzBBdzBC/xgNoRCJMTCQhBCCmSAHHJgKayCQiiGzbAdKmAv1EAdNMBRaIaTcA4uwlW4Dj1wD/phCJ7BKLyBCQRByAgTYSHaiAFiilgjjggXmYX4IcFIBBKLJCDJiBRRIkuRNUgxUopUIFVIHfI9cgI5h1xGupE7yAAygvyGvEcxlIGyUT3UDLVDuag3GoRGogvQZHQxmo8WoJvQcrQaPYw2oefQq2gP2o8+Q8cwwOgYBzPEbDAuxsNCsTgsCZNjy7EirAyrxhqwVqwDu4n1Y8+xdwQSgUXACTYEd0IgYR5BSFhMWE7YSKggHCQ0EdoJNwkDhFHCJyKTqEu0JroR+cQYYjIxh1hILCPWEo8TLxB7iEPENyQSiUMyJ7mQAkmxpFTSEtJG0m5SI+ksqZs0SBojk8naZGuyBzmULCAryIXkneTD5DPkG+Qh8lsKnWJAcaT4U+IoUspqShnlEOU05QZlmDJBVaOaUt2ooVQRNY9aQq2htlKvUYeoEzR1mjnNgxZJS6WtopXTGmgXaPdpr+h0uhHdlR5Ol9BX0svpR+iX6AP0dwwNhhWDx4hnKBmbGAcYZxl3GK+YTKYZ04sZx1QwNzHrmOeZD5lvVVgqtip8FZHKCpVKlSaVGyovVKmqpqreqgtV81XLVI+pXlN9rkZVM1PjqQnUlqtVqp1Q61MbU2epO6iHqmeob1Q/pH5Z/YkGWcNMw09DpFGgsV/jvMYgC2MZs3gsIWsNq4Z1gTXEJrHN2Xx2KruY/R27iz2qqaE5QzNKM1ezUvOUZj8H45hx+Jx0TgnnKKeX836K3hTvKeIpG6Y0TLkxZVxrqpaXllirSKtRq0frvTau7aedpr1Fu1n7gQ5Bx0onXCdHZ4/OBZ3nU9lT3acKpxZNPTr1ri6qa6UbobtEd79up+6Ynr5egJ5Mb6feeb3n+hx9L/1U/W36p/VHDFgGswwkBtsMzhg8xTVxbzwdL8fb8VFDXcNAQ6VhlWGX4YSRudE8o9VGjUYPjGnGXOMk423GbcajJgYmISZLTepN7ppSTbmmKaY7TDtMx83MzaLN1pk1mz0x1zLnm+eb15vft2BaeFostqi2uGVJsuRaplnutrxuhVo5WaVYVVpds0atna0l1rutu6cRp7lOk06rntZnw7Dxtsm2qbcZsOXYBtuutm22fWFnYhdnt8Wuw+6TvZN9un2N/T0HDYfZDqsdWh1+c7RyFDpWOt6azpzuP33F9JbpL2dYzxDP2DPjthPLKcRpnVOb00dnF2e5c4PziIuJS4LLLpc+Lpsbxt3IveRKdPVxXeF60vWdm7Obwu2o26/uNu5p7ofcn8w0nymeWTNz0MPIQ+BR5dE/C5+VMGvfrH5PQ0+BZ7XnIy9jL5FXrdewt6V3qvdh7xc+9j5yn+M+4zw33jLeWV/MN8C3yLfLT8Nvnl+F30N/I/9k/3r/0QCngCUBZwOJgUGBWwL7+Hp8Ib+OPzrbZfay2e1BjKC5QRVBj4KtguXBrSFoyOyQrSH355jOkc5pDoVQfujW0Adh5mGLw34MJ4WHhVeGP45wiFga0TGXNXfR3ENz30T6RJZE3ptnMU85ry1KNSo+qi5qPNo3ujS6P8YuZlnM1VidWElsSxw5LiquNm5svt/87fOH4p3iC+N7F5gvyF1weaHOwvSFpxapLhIsOpZATIhOOJTwQRAqqBaMJfITdyWOCnnCHcJnIi/RNtGI2ENcKh5O8kgqTXqS7JG8NXkkxTOlLOW5hCepkLxMDUzdmzqeFpp2IG0yPTq9MYOSkZBxQqohTZO2Z+pn5mZ2y6xlhbL+xW6Lty8elQfJa7OQrAVZLQq2QqboVFoo1yoHsmdlV2a/zYnKOZarnivN7cyzytuQN5zvn//tEsIS4ZK2pYZLVy0dWOa9rGo5sjxxedsK4xUFK4ZWBqw8uIq2Km3VT6vtV5eufr0mek1rgV7ByoLBtQFr6wtVCuWFfevc1+1dT1gvWd+1YfqGnRs+FYmKrhTbF5cVf9go3HjlG4dvyr+Z3JS0qavEuWTPZtJm6ebeLZ5bDpaql+aXDm4N2dq0Dd9WtO319kXbL5fNKNu7g7ZDuaO/PLi8ZafJzs07P1SkVPRU+lQ27tLdtWHX+G7R7ht7vPY07NXbW7z3/T7JvttVAVVN1WbVZftJ+7P3P66Jqun4lvttXa1ObXHtxwPSA/0HIw6217nU1R3SPVRSj9Yr60cOxx++/p3vdy0NNg1VjZzG4iNwRHnk6fcJ3/ceDTradox7rOEH0x92HWcdL2pCmvKaRptTmvtbYlu6T8w+0dbq3nr8R9sfD5w0PFl5SvNUyWna6YLTk2fyz4ydlZ19fi753GDborZ752PO32oPb++6EHTh0kX/i+c7vDvOXPK4dPKy2+UTV7hXmq86X23qdOo8/pPTT8e7nLuarrlca7nuer21e2b36RueN87d9L158Rb/1tWeOT3dvfN6b/fF9/XfFt1+cif9zsu72Xcn7q28T7xf9EDtQdlD3YfVP1v+3Njv3H9qwHeg89HcR/cGhYPP/pH1jw9DBY+Zj8uGDYbrnjg+OTniP3L96fynQ89kzyaeF/6i/suuFxYvfvjV69fO0ZjRoZfyl5O/bXyl/erA6xmv28bCxh6+yXgzMV70VvvtwXfcdx3vo98PT+R8IH8o/2j5sfVT0Kf7kxmTk/8EA5jz/GMzLdsAAAAgY0hSTQAAeiUAAICDAAD5/wAAgOkAAHUwAADqYAAAOpgAABdvkl/FRgAATdJJREFUeNrsvduOLMuOJEbzyJ4eCAIE6EmP+v9fmycJGAzUFU7TA6/ucalc55zd0Ai7UFgrKysr08ODzovRSIKk/P3199fd1/h7C/7++ls4/v76Wzj+/vpbOP7++k8TDoqIkP7AnnBHlWJPt1/GI7YfeXkg29+sj7fXsL1RPl7eP1fTHvSVMdcaF7O97f1H8m5xvFzb7ZWQ/i19Tdz/PhfXl5XvkD9Tnhd0XdP7hT3dmQw+2LdqXRCZWwidU4QiIAkIBRB/QgT2qD5p5IVC4rXxxhDUDW0/2IvsAyH1jqRAhIBtDfwpxLsJIaCvxD+MQhGgrrIvcVCYPxL2d/7O/jQEZDzjf1v/iyCu3F4pHIx3EQBxEaTU268rqXeEXy0hIgK7GlKAWMsqAbVMv/B+J/wy/K3sOinrpS0XU5/NZcOwihe2x8sP0P/4D3YJWPcbIG3H4oJdKnJRqBvquxAPQjZie3y/KAT9JuTHiFBU/PpTTtcta+cPAiEJv0UufveXGw+x3gmkcFKILvWAiEkQ6vPqg1n/U3I7UuJIoInNurPx2SWfNzKKffG2SheYlLrlk/J42u0lYkvYtw8oDZWrdSEvFcW8Jzj/x//wuwl0JWZyYFddezx8U/J2hyShjtLYP06xSEhKRT9qjNfQNEDetZLTOCrsQrncQfjJ76oKdT0utrGpaLfURa9uAdXUqHRltBxJbseoDAf6ySCxHrgRYh1XkjvRpcGvJP8KpYlcWgF/c9M1FKZmQ+1xylPfhbQrQ4SpVZBH0A4ABed//+/+87q2uPq8g0B/jd0ItCtH08r2Dnmi0G1J7EwTM5c/smxG3OBQISbi8Sd+X1PIkHtoeqkOIiDt/uedMuFg2TfA/y63PB7Ym6dEMUQjNtw1XehEvzdi4GKsNIwEJDRnCHxeT9idVJ9uBENkuyWyFXdDlFo5rSq7/oKrN8CsPZgWuq11sZOE4Of//r/KosHXi1B+gmaf60GdjtjsxW5xlFWoteRn5oavHh5L4/gndyVFlppgnIF2mNBOH1aBXV4TJ6hbcNvtUZLCOkSLPoB7nXmT3DWjsO1KHA63TmiHFtJ8AIR9oWlehlWzMyHi1s6WC4GSAMKDoIy8yu7spIBI2hx2LUmw2zV2vVLaiiL88JwC0zDC0VzmMCY6IEoZtuxQzKPf68XxEqEoFGpboWGdiB57tAuy3ejL9Osb5UwDFKH6drJemkKaMgxN141dtNF8ByGHiAy/CxARUQ0Trn5VzHNaK0N4/SMdWyowqGz6t7myJETUtjbte1jMEUI1WvSoJqqiIGZ5axpOPstRt80IaYmP9uvxz1MR2CLaLUgBR94aSmVS/Lf4f/7bfys/BWBKK8KJgwt2Nxnu2Q7sbjSQOg4tRqOMsHNgqXJ7alEbgGjzsrieBRWBUAUI4WU+dpOb6hthkoBV9ZmFkh5umVmRsFCpu9Oi5waWGkRJjy6hR1MtKV0YTY2jxXmQ/ric/uVK0H5LEcEAyxlE22k3cpTw7915KzPuxmHkRY6IYBFLD5H6cE4m3gH3H9thiwf9BKZXqBdffI18U21R1J3T9ivlHqJDRClNhm4i+aZAEeF1HhWULjEfbnmlK8glGu/u4wVeCf3KenlHT9KTW66qRVgNP9CbFdfiFq9Yyq92fyfC+FgxtS7Gft8ksS5AIzy92ULNx6oLfuLhKSH8cE7Yp5kWQjOSA+a3CxRN5mPh4dXYaax4FuWFm1AwggcMc0jDjOAiMRRAMzpcER1N39/ffwgpomYa7DyE8sYIReKn3Nwx0Th+A0jH0hx/CkYqVw/U3DoTtp3xowTYVVEH4z0YuA2h/kC6toaIhoWGCIa7BBY5iLpQQA1LAYYhLk2HVETcItUUVlN0LmUKs3dDPcZN6SExSI0Vm0sjqT9URD6cZ0WjQ9y8trAaCE2XwaRZfxGM9NjCfjV4IjeTagGckNNcFpWypQXcQTRspjbwo/yShBdRoXgcsLB0oX2nrz1DTrhlZ9hXAiO0uEJGeBsMOC7DQMaV8xaUZPmDCg+56I6LXSGV5kiGjmaph7mcKDSN57HWTO1CN6AEIEM0BEkADeuRmkbFZYQQcjItJJSONE1GgNAuz9xVoYh85Dw1vIsUAgEyWiPs7LhqIESGezGmKw01UtnAOIeSfL2Kwue5b6yJruZB6MC4HeBwJTWDGVslQAUAxk2Fe18iHP4CP7uDnK4bDa4h3YLaPxq+RNdaSlePG0avcYUa8IimPokLUHVLoQV+mEcFV2UC6BARYojOuv2OMcFVjwAyaOdNITJiK3KvA1Y0NMDWnT7mCqrDX8M152AnQ0CR0DGQD3+mDNM8AW+EfJgME+F6jTiH6rcTw+8nh1AwhuiktCNKgkICdJ+4LzZVtYhCy3JS/dY5WEJQIyTR8qpHeUDqcV/6IsiIBi5A4R+Zw62OfyNNc3rtoXiVQkAjrLTbZCfALQhKtKlt88PiaJhPZRggDHf6Q21QVAhQI/6zi6ntD4zBQg2DKZREc68yRLW3snOvGQpBba1uBenpEwFHnlao7bVaWOQypB/OM2IAuhI4JENvtybpXq8/kpQBgqIQjzJceWjE7WE1/N7TDZp7/TQFLEJQHbZx9aChW5Wigatqua5KOQK2GeEWYlm3QDg8eueI304x5eE+u6SGrDjVb7AfP4l1+9HyddDPrytpj4L9Ctv7gBZfha+FlOvS1M3TkUFAZEA9GKDr8TitjvSZ1EX+xfwdFRGlq1eFlutva63IQEVE75ZLpOHUj5ynjFC0Rxy4IQCZ+2z7rwIThWEyZMozXyNQGnaeflHtLezWeojjsU7zKshyS2fE4pS4PpAMtWHQJSAyyQOSuzRWyRj23n4vMCkQHuHRaSXSwpkyPAN5Rxkutza5LlPnSxEK5+LJOYymboAQdt9sygi0ckhbbixkBB6hrhsxhIkAj56zaKmL1eYtQgoqLeKAxi7aj+a3mkM648009OEU4sM5ScEMfYpKfRGEaWig23qGijEvS9IpGRSFn8bITJmHq+0U0ddut4ZKmOG4/Jammw0nUAt9/a0kdHMC5MtjE9KWUAj9IVP8qoa5Qv7YkDvTdnlHlZ4BMLOlLfHtCjAOm6JdqkeuiCPqUJjKsJgAMsKA5daOiFFNsEakH0YiFE1uCIzKRCaowsDPGLZXxUyjr4Mh5tIXLUJS2+1KwdIPf35s1S6Z5vCE3JZxTwAjtLVMkUNY4XZRLXYeQnOKtEEAudtxxupfEZnr32p7JaX5HBy1xFqumkPaeAbpozR/zv98zeK2sxcfvF7A9m+/AOH9a7Q+G8tS/N9ZqoJogIzGska8IFGKsSTrutep/TEvm92uKu/G/VV9OKdoiOgh7nM0vVYx+ijV4kj1bCHuEGiICwWHLy10qqv40FnhyYmQ/mNaGW1ilGpuu7i4u8N8v7bzXPG7IQu+f7hQl7jQLxXu0sVnF1Lrz/hS0Fa8ifPiecS/oetiNW6fpWyH1vMlK5SEnPy0osBl22w7CvY2epEACftsG59SMtvR62KUr6m3+sj5Q4CDGJHdV/CggzUHU9/RbzkM35ER+SYVDIhSj1DIR/GJNP42A6kMRtS8ZKGtS2PnFaIRRarf/KYlnWsByCDbecMnPJlBX1wm70aYRIOW1IkFyUCysCqyyRE0UxRCgZJ5GboqMbXFwZ/RMC7TMKWAbiZMeC1HApq6KyvD5mR4ni3gPLuNh1lBkWHZaY9KGo2gDBMRgWBKiYbpjkMa+yp5bW60xd0lFdEPf04ZwiEclmGIfNQolWCqECFgkns7JPPYXZeTC3OLQdSgiqLshR2q5pBS3eu0W0AV2J+QMoUEyqzoiOMXZiXWGvGligz3l/vWYTjU4CDHEJUmLmnM6DHqdiyVMlnKMDWKLlbGfQ5VhsMXCyWEQ2CeaVi1NBNAGmoKPGBB5uSGnUT3hDM3VLqV7u+UqPpB2yRa1AW/JDpfUJf04fljy5SDNJdfRQ6UWQtmmx12GagEhXj0LfliFR6VBsrj1xcV++yioKX4zIeW6a8xD7spxBbKikm069chcqR6DuEYfu85F5/D8VptWZWjAawNA7UYetlquexh7jPdccil24uniw6aWRlCEAPu71SCHURz1izxNsLzkDQxLPLB6nAEDITNsdDwOlWarKR0twe50xTRj/xMHmHxDhqQDFI8cQE5WqSF5PMEz3KITMqRKINHvCHPzMDEcE7WuYY6eOhggYooONXtiKbWk1SCVLOW6H7o4beIR2RYPTAyvAcYQTs6Ap80LTiQOsuzukfkndKvT+3nu4HaSVuiC5DZSFJkhlgoqAJVTg/lDxEFBjmAIUw4MwWZLHFhnkGKhV4GvLmb4hdjyGVAMpAy2gi/ye3LDBy3jiQ8BDdxnkBoPA/YTHPYQkZk4UlqOm0QoR9LCwmbj+zBsftU4cG5aSUHEkzy47TIKmKH80eGbijhmCE9M4RDfSvyEPrq2W1HJ9M76H/QT/ERzpeUumhBesYmiPPmny0hAdNxLVFi1oX5+uixB1Kuw4EylXVYymlLEYR0F4kbLX9ckL4kJSmdFcefRRsUECdLcs9Sfm2DZ507vw/TpRuzhEPO006XHGIygSCu2UU4/HfAUfZGFQICA8ACCnXVmEE34Y81lIRvqXsh9NvvChtKmUJFCLbfCNM6hBzCITgCLTOU9TDsCxEvBbP2IDWzuGFuGMi5cXFQDOj0T5WOghuUrnELNFzOXKvpiWnRF0TJaWg/ZYJKCCqSc/iQw0TV2FaBMxnFwpy5Qc5gDaLwZTkaKddJP4G7odS0Osa1b/kMXGma9ED6qZx2GeD88DypYReGOw00LeJC3pgKRyUgXQM3D7QST7Jyv8oX4gwLHhLu60o9PZkPqED4HzLJWS4IIgZRgS130O3LaBCNKUCpPHsyxBbWhjqRkwXWiDnD0pzqiP44adIgsyxLvwyZzCuRMI2DqoIhcojvN4RHBNlcWMfJ1vYN1mCbpu8XbIKkdbH50toQulyfnzUpuzhFJkn4QmfooxmP54c/P6kzXONSZISOO5qDdkRktJFV6Dige0nqmdGUKG3SJQ2d0ebvawBBs0tMOHkqmGGApsfLA3KIHPQgZVsu43r0UhmSPKD4lTsr4rkPJhZbfqhr3FVJt02WLhl2/LarGqxVjoK51ECLo5hvO5qIFpIGQ4yWdU1opJF0O4TnG4YWodiCuO/0bFeVj88Pz3PZVdtz8qboaiuGCX6asfbc+XQHkcK9NKvFqG7z+wKZj1GHsIkLpm24wzfOGwgfrkvGIUvFy1Yy4rSWVoFTSg8ofhHKXUwwQ+Kui0zUTpocTFMtkNnWmi+YgJTdNvtyFO5NinwauTS/deWxpY+SnIKy3ultXOBO3myqtnXvB9MujzI/+Plxr+VAusu2/My8p5fBz1K/wGCOFQnOwUb/7SWmdg/DkJdy2nJv45m+zOnyj0lOjw3dgqMS6h/LHAad38TzEFDkYxSXKvjIPLKb624+EMnYeJvyKoSp5ez50wUF04kFmOREKA+WsE+HXwbc1z48de5Jz8NVsxNXDj94DnihQY++2QGXoldKVWmHNkHWFnmf4ZXb9k/hidAWSFsTO/1R0xx5fo72QERmq+ocwtkqbAxf0ACiEuSL7PaWamGrCDJATgGyzthVSc/+rf7MmbRDBvqJhf8W7LnE+WfwBtBwYkSxFRbxDw5NBlqrIZT0gNrBY9Mopiq66PiTYJg6rGXJZeeKLRVsIEUtsXgGsupj9HxgpoIWXZKLdvCfy05Plp9xNl1yfuQ8E8L07TjQynyj1OZj580EImo5NWqWJsOlDq+ORbVUR83tQQpPQgOY8dsQBZ7pN5MqcrqqxhSeXJBBOCBD4SdE8qgq0uSMmZRE9VeR642348iYNEpDLLSikvClbYlxVbFoO2wR354effnzpxfyFkmocQ7NbyL4CcdTsZCNTXkoOLN8wTgKIkVpZPK4LDqlYxKYhSYyHCKE/HpsMsNjDUHBNJ/jBwFLOQUhYKTYZMpH5BQS8hHjPLq2UKfTJre2cdyzmMaIJy7P5lc64ALxABV+4yf9vCnkJKZDM3K6+sMpMsOWRHLmcI/B9DGCCtDqfVDlVYb8oGUDPTkXjP7uhFTaLBA6xzz8Fpy24WFZzOad9Cs8WQ7K6XbL+AmMYt9lgVmUavhAFDgZJ2I0dn+QXINDUiBNFkMzEK2EQNIVnbnZpjMg0/ZVPDo8heZznOZzQJTykVbfwaWAE8HgnsHltoyQGEc32FBVHuG0TulVdnDOnwSJTj0RlUYEYfd8w9UfyJk7nwipYy/JzpNWxsFWkCLBp5lOBar8DBevFb0kP4obk3IrEknCWP0MIcjlhny4DJ2gim/7lKEi5ltEHHdkCWdUmM5wH8Bk+GOE2YUYZT5SGhUU9IqdtTbIuTyFKUJm0xa+VgOVbO/BKX4G50fPM4W5FWr2kn2jLLZyHHVNZxGDRysZVyH4YEQj17FDjgGWh8UDFaJqiwrhEITWCyEXmYIfqcJQ7CWVSUzX0LhArI8yISAHoEGUHei+kml9RmbEAG4/hARjY0OWqYrTwxaa5CphRnHSX3xCTr81zAriqsB3AZ1V/gcPUoKIpy1Rp8lAobt8thPqPH+lcFCJROIU8Nh1UFUUYWUkXThOhmjTD6BLtPkcJaoO1H5gVQRO1TaZk+AFqowRQL9yQBKRMebrqDp+RvHIyvVCQrU9qUYtiC5ga9eDcpqzFCa0gSxeD+aYZyu+kwQrOAANyxLYfqC5vV5oi4CjwJi9PUtPTYAVD9LPnkIhc8hJTpqAm89h5+vDKuUODRBOaLAutRF8zPgNN/VRdZimUyAuN4RwxH4H6d4SJRzO7e95KgtMdFDp+3oKzC6a8jg/8nO2WjrhR3i2OsZmrgXFFKlgfAaXSou52AHTrdRDL1DHBsSUr5z/0h+c9Ce1g7KdAYwqMhcRkX8TmVVC5ts+g3aQNJlR4JzGO1T+XRcMaQPBJt3mnRSl/ER4kmv133qyuO+QLL0PkkFFxKJnEPRdNwSCPdZuQv1fyhahJDzaF332UEV9a32PtfbecI4spkLrxYFWuW4h9nSqSYh0wnEo11UjSZDlqAIu1PPIToiTGBvqaJaQYVAKT4qrwemiW5WnFFh4674FJmUAM4jmSRkYXUpbs49O8HfuVaTZpJJZ655nYjCjLNZut8uI1eMM4K1K8OQU+VDmWkA7pYpVTSWrVPq4E3uQ/OTeSRbtAPZ1u0ZuK85oNne6IkUXlA9NOMJBgIWEQ+RE9o+purJptRaQITJJQKYzDnAY20M8lGBVE0beyh1r9RxR2D1QIzBxJQE3hqfgJE8LZcFTcAp/KJapzp5DxAeEYPauNVmfmYVxrIohK3IJ3l+VGxzVlUGb2ITnjAYxs2S5u6LEFPlBWEFbPXAGgLV0O5HFEEbzoVzrAGdAjJY2NM+n0sCQo4rmAyioVEAxDMInjWAVts2m38y1+3G/nz/524/MExG69n4F+HgOTyacMTMyikaRerIixex49biosr7ooYMs69WoVwhLWIiiw4yWOixgFKdbFo9po71X1Pyj2TkZhGUgRytgjbgPRPUgiG4Q2XOCiY2gmk25x+R3RoIXmLCpn0NbnGOMLhk4xR4cihH9hNC6mM0I/gbEWF5eIBCkL9/dbPDUmkN5vUOIG8MDb61DGBTARkDinrCClMcPmYLT0IMPf86KXHupjVRNZ5rpligL+qZ2ow9jDDmm0zpnh2HcUhYLMFqJiACdT9aqTzMr5OnFfgmJJjgK6aqCUfrmsJ2d97GUKFnKnqycStTdBQuTse4o1usJrFxcX/rZIMc4mfLjmigKfrNqPZGuXsHnNXAq1Oa/VUy11/lzLf3p1WS9OCSFoK8+9Ue5dn4M8fPBnIKG3Vrx43CmMUJhhM9RWVTXsUehfdEFB4hwgQ05oHOjhl2zv4G/k3oGQ1oGw44f0jDiR3hCToFm/TCyeMkQNVzWFw+QVSUa9KcEnewsB2zG1uQrFo14kPEaVKiArx5B8AlSlQeJMIeDUw4tHGYExRxitT6VgQrOIdoniJoylNZCJtBKeIV2NMXKhbYMMhwzUhGVYZehnaxm2g/u3Z2pPD5ynr4nweh2Q2DPDO6pvuROVrkSlrrH1tWukobVLaAXjwUVid2m0/LeM+gdYRXzZEJ7CY1k58bRynwHHcEN38btYha9a8utcata8UIqN0HunG6JTyRZsxadNt1ibkvCeZCoWo3esjLTzUoeOnunlubr+eJWa3eBdxIbrcaYrBJZh0WK+7MQwBzBncKmP3AGzgFvsFFEDcsV2rEcqPrCgUu1D0SXlqh7D7FeorJyYbVVhMSWLt+nrHheSImKbe+IUsf0NtC22k9mywoHvJUdM5a2h/VD+OZYFrrVhcxy9rGsO2EkdFSJlCGpM7whBGQhHiNX3ByELEbrjVp4s8fVfhZR2FiX7M9sG5xp74oIEyG1xNtPwkhZt5sETTclo6Xelxy8LKUHW3vf3qPy0mG3FycXkfspJVvGkKfIjHC5GhsKKD9RWVinMVecTE60Ejbc9EC+yEzvZtfkI9JbFbaYesJZ2KP8gD8ef7tZab1Ol6I3k/TwC44ISVkPkmdS9FJu3ZSyb1XWSNYG90VfN7gDpqd7/zw/cs5qOZNK4qCjGkNW5Od6h7snKHJtD1w+R9WptwU65DhR+cSWK2xB9/KtozTHiE5kptbOkO4ZfFL3M5wRyp4TlR0+4NZLuDmpmmuVrphdLHQBZ/JY8iRO0R9hRyYEEKYoRGmpPy4khUvNFFvnQLnp4MwV0N3VhrQMrey2OkxJRbaneOLNOp1kg7ghcobii+oQLxBpO2IMLKy+fy4SLcNEuWmMI1UbtvgzC8cK4U8HfH6K/FBmeM4LbcqLykZgw0NwSOba2ZyFLqibgk5cbWne2iC+KsrrsC7a8esrdrPCKcYxjoQEh+Bcbcr0pa9eTbMjW6JKKqBku4jeybfKrdBqhrISKK8BLcSCR1k/kJ8P5ylD5BQjvJbr1sRC8vmj3KSuDYSrQKNyFZClZ+7mcPQ62HnHb2wpN5kJScuAnG19XqOwiK00gSCXzoabWdtNds/j5RK5rvgWSk/DzS2s5SmivuJtd49FKfOqnbf2+bz0zV8XKq18bYtpryVMupJ9evA9KfODOeWAFybNqBk7uoSF0yhrrpt9GdcpBGt/jnvnI1pu7PEQ5gL2Xo0ksThE5R9BQPnE46NVD39CCSz5+rWhoXUCko7PLNTdy/e+RN1rbMzWpBedtnpEMdAUZ/6bshxtub26mWg3v7WBB6IbBcZyItm4pbuy1EoB+FoX4mvGXR/5OV2XjvRD0+Fg7f+B9biEYyd38ylKNd9Pq0grtOZWimuuS2rCYKRKxZ2+1p+lxHBftLYVs8C7jah74aaj65fWxmlJG+qy0GsQ4Emg0x+Y5rDFA22Pbd2QYdWGt6BBtU1bI8FWu7cvFKsoNPxol4nKc2d+qNb9wTzljL2da5ByXBfbHl+HgzRodcADkK3V5sMJ3A7erbbIhfOUI8LuU/bI6ri5h2+LlqXxSOtudcO85x27e0s3z3XduoRYJsvn18vdNK7etC7NSmusv3na5m7G9T1A/MicJRnv693Mg76NteGlPeztFJwvpWTuyIKgxGKGs/8WU60BFNcpBdsgjtdBOHLpPnNnVnYrOAVTThQCM1uEkqt/Gqajb9UWt5UjfN1g3q34BmP6yDnFm5rQFZw/CDflQKuFCSL7gXZ0uFhzrOULrRmNLgip9KS3LhmhJYV8t/DyOdD8KdRlmNWe3Ve3iu+uqtcWMpHG7ZNedOkBxx6IM9TzuSboO9XA059uVmytZlnSxT6w1stBWiJVNmBUu0Xc2zBrtDPttCqp0LjjuJUwDHwmF92W/oFFK/PhBN72A9owUF5mzqD0HRat/ZXKe5HqpLBhMT63Wk4vzWtui5z6ike1cOPaI+hpuS9Kr1kWk/r5YDuuj/mgRa5txlHjXsaqr/E8jmyzWvNpp8OsnE3HzZ5jezWJencdd9D/rVZ+cPIeNd0ardiKEYbwjHDreJD0p1v6YDnwMIqOF6vNZ6s9G6o05YTR+P0wGs5xXOop36WED1v6YAj5212cTzZFZH5wrj7HLGggdT2Oy9b0MVVdOaxSvQ1pWfq6snLtXDM22Q2lQ9/BopcpOvGBHCHRq8C2xnjLcD10ULnZPmLVeVhOaUsF7Ji73glKNwktp2/8lBRebX3/dGu8UjjdNgeMbbjQMoVqG2PW++fLvt89X5rIHdvjmTR00xznDcp+yXivLerqR/BNmJdTh+tQnz290VLiAZVuCSK4BYcrjMvSoXLV38hf9UX3PApqfFafzLWFM0/u4hbhXxJbGa0ckutGCsq266uFrUXrJbnJG+vd91WkTbBBawwn67lrea0l+/nBOWuNqVS2OHazI/IwIRT792j5RrlTj/qbt3He6D6LVpaCvnWT+Wq75WLHs2cfasrMFvDqpSr81id7CMTNrCguBcsPTt277ejYjuyjG+Uy++PWhvMSf8/rTn9E5270eHf9vMRVfJ3HKjezVPA8UvbpCvR+qwVXXfeLfyQP/ml7PKICYzxEjdvlPyEIFxGx8s48lbeB+lU73wzevTgUD7AM1hmhV3l794kD50BWKM/fHP+X2cLXO9+K5uTVt+NzjPFwII2tf3t589XZf/pabbfg0cf/XpBXzIMqqkVLePI3/8j35OLp8Td4411z6J0W0Q9Ul2SXRjc/uaV+XS8C68zomuhDdE239CnC/QjmDNJTMhza5do6ZQrHki7KVgef1nMiiZPaPEnZ5ki0Eck+n4AR0ma77DZCTq6Etg3RdNIuGzCtQsrsoSyWFCPX/jZb59iqblzodY0o5H1NWwSLnjXiMqCQstYrJOI+a3djmR85z+qBMy/28BbOfbKKnZkE2YKYb+RZX5HoRgY79FHRPUWtt0gn7gdqvQAEv55A3hNoZC6J/SeN865LbhfNG+PzpY6+3enKL3+g8xeT/bK9T7v9DILhAmLf4h+v4sIphHzWp/lgtV/m2HeLGIkVHU4/RfP38BCDyauYt29zSCcet1mesxS3ZmVd9IVqeL/obw7m4iuZWZE3t+MlAngH+vEoxl/iYA90tvQ55irs3xzCl3yELEGf/OYU8A8WnT7H/BPIi3fg82XR2dmfX3/rm3/Uvj9Q9fTGQ7JyYc51enONm4rpU0uBmfRpLcE8aETV1lRFZOvdfUONlOrBZIMw1hLZ/mCfzL5i/mxHjgnVxTWMbZUx+Gt55rK46hx02UJ6F0oql1kue2Z9o6vW3O8govXxb8h5bwh25zJgq+1xu4BtdCc3SL4NpHVf7cNzGjmGcz+K/gwXxleV/7SkVA27xXKVy8Rf7iMyelPuPruiU7/2B94qh6DBo/8WC22Uhc1dotzbmrqkze3gzq5auFS8kNhaU+gdiWauGFM+3rkjd5q6rvu6Yi0PmE2RsEUouJjmdWbp2rqae1Zl3dr18WdQURV/jQG8plYTsM3RwFyPHHtJIkSGzBFm0KdTlRhzm1AkC1kTvTVC4zpmp1+fTJ2Eoe0cdguCNSjp49eXYYyxeoWMYz112LXf0sl0GXi4FGI0XjNVqMgSok3XSUnDoipzxBDbnPechTmqw/eNwuijjLuu27pEbovmMl5DP2LNuXUxkDe0xi7hIUm9iLD1d814dlEhfbJiMsiWSIh7g3+9b59JpRTYyLWuaHHvtMWaXBSdrKT58k3ZVG3LxaxzjHq/+X2V/bfVakumMsqZ+x7fbDbXdeN+xfXkWE4D5Ubp6fZALqtfizy9v+BnKN/Sk3zFjfAwr9qmxIzF2UdpmS3BUgPpt7ffguVuxx6c8H9m0TZ7b+AObCQKIbvG7YuHoxdKVIeB3ghSu3nIlYw75kOFKN5m6eVm/EYS6je+TQP/0ODzL6PZp5BDLkmWniD6Amb81fdfA0PBvdfPL9IrvFloT8JdS5puY1f9GiedFcrOVzrg97Ap9u7ycklU8DfAVH8NEz9DdU2Zr20Q38V7z7SxR906bsv9FkUiqyKRZ5qbLrVFHSDYktE7q/Uh75DmeZRzgeHVUn32aAtobs7k08Hb2g3pOuRp20s+nu11Zutl6ZBDMGzMVroit6tkH0p0DeXuq/k+kpNmtflQfCXyPMbjlJ0g/QYgSeuwJbvtuJ6qpQARe8FuDn/qQ3+uBY9bsV7VWXDLxmIZaoFrjv6Bdcy1pi9dUNV9X6esQ6uelog7oCap50VP2SRg1ec1rlJ2yb1ipkFY/0B5qTDgG8MWz99SzvJATnRdtMW461CPB5TqIplMKcFNjWsfrCprMIT3b8NEiSEY5UvLOk/yivLeYpkt+qzGaFrDBe91JJ81Mt/WjRwrW/OzUGMkl14EuPHMeKupvbPAZ/VYf6PSyasxbFesrzRB+RqPvrONS/3Ws3P0q8d0R9e+Qh7fLPp2h3VxofcauT9BNJ9WfMeElS+Y6l86fvwgu+XJZezFi795dY3XjgKAdw4b7ZfywDR+otY/+ILUhRj3FKfcQr5bF52xOEULrPscm/CX3gFXOsamOd5TS9cVj8cVy92K8ecHsI88kQaf99mzvBSk3CpP/T29grscJ14LLeQPnByuvC++ZmXl2zwLNjzpeaufVi+XycphaMhftDO/u4F3uXD8SeD6DVfFXxDCsaUI5TfUACthjTuIYGmKqwF8DskKwrtyNC8S+6Rirq7wjVv0SPFHJ/g/F17IA49NLhVfugiHyC/J9fGslzdbNxpRBo9MoNdg4OorXezeZynnlzt2GR6u5rpBl7IxXFZ6e4vk8ko+FKOxOjy8+0TyinM8CVZz/+Sep3LPxLtidtjTAlu/3heJ+tXPWDufcAcff1EeeLivN8L0gbaBqLywiH+l9qBheA3M8wTnhcOBu/ujvyWYt0iLyyp/I7Le3Oc2Cm7puNQ2aJNi/dppvFq5KdRKC8gfEhixOnXyECZexIbf8Rz5gMaZ5hA+ytm7l/MFwRgPau4J4/3VrcKSw+E3hvPZ5b17AXATyr7cEDxDOj1elzfOxcuK+ep1YyEYv2wqnnf3xaviB+/o6lXrveQ/xrLeXnQ6vmPPyBdh6BcJAvzOJb7BFNokJ/kO9n9idPOuBuy3sPLLFAjWUo+VYCxtXuATU14vNRf6FGt9sJ3DWyCGDy4OHrHpHhXiEkeOh6PIr2hut0TnJxTtRVXfMe7GaB0r/3zRt1rp4nM8LVf/eMXJQrhd3Ljzcbfbedsfwy/s87bVKos7cquh8QpcyB/Q5l/KZfQX4XhiMcsXKmR98T3D/utFd1dv5RVtbyx/uFz5fY9/fftv7u7y3cyK/JpXe9XKl6hw0xxPZ+8djv/6i3/28qdFjyAY9+8/XfGd/uJ3a/rDFScI9g+vGC+2bvwiXvKbzeebtrjGT/+S79cz8U8I01Ntzl+54u8F/OG3+ofs4u9VonwgfyhnX38Db37zP/P9r36/h0X/S1eM/8lWDBH56F9zVpzE8LXV/kOB/+sWzb/ovf8irWRVGn+NuuNn/HWHcPwlmkP+cs2Bv0Jz/EUrHs6P+Is0h8j/XJrjCz/8/3ua469a8V+qOeRvzfH/A82B/1zN8Suk942V1T8OIb75tL9Wc/yz633DHfjXLFrlX/J1s9PjPZPzfbh9k/3+BxGLXz7tr9Uc/6qv/yzNIf+yRd9pjnud9HJ+via4P8GdfxTZ3+0I5YbE8A8Y69tFj38WPeHNurnx1/+hRT/UjXznoX2VkNq/xy+pYr4lqt6Ow4Xodrsj+McRw/ez9PT9ypPmbxuoXx+/PwScv9Fj/5CW4xc82Mc3+1C+SPX8Yz4Hf9/Vf0I43pHGf8x8y1sK5Btd93rf+IcpEPm2Pe7TouVPFn0nHDtB9de/+jV/+rCofywPyef86XMq8iXnMV614l1Nhq6cw2/aIT+kqcdDrcY/kKP+Q23B79K9+6I/xJ2/9HLB+sq8WPn4G7PsG013u8wLeX1rBynfHTm544br3kZzI/ThQrD5RsvdmQ3iLYP8q7fx/Myt5rjl1b4s+sbWffSPihvkmca7fvqQ3Za83C59Zl1j5cw/bP8TIVfuRiO8Fh9SH7P2utLK9ZlmfKfLN8dn88M24uV75cn6IdB7DgPvjrJ80Sft1qw8VL/f+3NbrcPl46hvTa6uu3bLFh9N1O4i5dvbL8+36Fe09jWguup63hkJ7HTa25j29va/Ey8fFCAfWQI3FG8+BML3nvGH26GSlUr+pDz40Pitk2b41pXv3Ufj747gE4/9yoyRu0Xfbqg6cvfC9vumaehDVdq24qcGge++793W6Stf61e1gRvumkcrOWv41jWSOznjs7e5jYm8aaW/tLp+NHcXnTHuNceXONA3FUO8p3T86lW8xNFjhbjxaPy+9OWvNRc53vShhAd3Mzlewv6lRvGjgl+M0Z9yRKIVveriUnzfheLrRpx/ig2+XEYsmvPb6Sq/lrTyUsmCN6vK70ADXV2J6Xwf/nm18K+3GR+MVXNc6zLlC7gZd2p9dc5fDp68kpC36tBIUh+XV42HRcsfgM78TeBedOq40xnxPbiEsuM3OPxXP1AeSN9fJzvkYXdrmz/zm2jyj5pvRufkOfcWovyuRv+7ePwpQOUdN/nXlkGzBib+A5XwX6Bkvzpb3694+75Muf1y0b/r3Y/+ivjLK/L2nAOg7or7uTHLgqG8aOgOdbwWwcrNcIF7v7EPj9FlDom+Nvh+d7Mvuil9jheJ53NQKG9AwLW9w/voxi/pwfhwrGCjvNYe/yFvZt7Vur/ophfWwtgZDF/6HL9OEVhllWvvmqf6h2ticDx7ptbegTek2i/Rpac+ZHOd9PespG5zbC93eqRD+ls9hz4rcnnUHOaQ3tZi6ust+i6reO1M/uIB8W6i7K1ER3/yl/qZX7thfYGgfpO5+i0crN5Rrbns0yQU/a5iZo+mPxxo3Vh5g4nh0o5q77bFaDmXoyws+va+2b1XFu9myUMgj9qqJnOE/uDw11u8jEuDZon2ZJSll2zvK8ebGS+kkNWn7NL37X7CIVsXyq2Zpq3V/l2bZj+pT7S3rT1eO4Pp1kaMJLauel9U4/ZOuPvSXQ1+JtstEHzhfGHBvXMmW+/ca+Z7Yg0Tr1oEX8RY4AUH7XwOrP1480+xDH7PElhKNZW7TP8BRRXVJrd+iedoFq9sMly6MwGPcKask1akjVnfBxQhNQcEbSo7+Kym5W3dvQN7HKgPTWFkQ3jhbz4HKjuV4Z8n2br+EBKtiTBeEz98Dr96C+o0J4CXtPae8NIeE8tov2oFLMuA5IvboQMkd6jwsnoscsqOvfS1rt33q3M9b6AyXj6CrT/+vuJgFYuI9vnC4BdZzVgQLyW3XDWHBiJBAhpTfppTiYCVk/AAFRKuDXwYEaqtokJUcKbjj+njk5DTB6u/ojcHxzIdWpYu3CJienObmQnX6UTr/Ynq/BMKDYw1VX9p1NhL5roFFnwDcx3RMSkT0QIp5z9Vy2gQvRcjfCYqWg86ArZQW2U2lK/O7S1ei0bIIJexk8tYaYX32D9ia+mr13icNj4nmlamHKE82CdcQ3ujgY82J8M6rXMlRhFrU05ZW54b6KzLXNTwOZp31Pu/b/0VL311FlCRTZLzwLEvOtc67t2+RVmuhVG9x2l2Z8oDu/TB2saObVmTraUlYxJAVyHXRW+dOzbLeEvZXh6rlyYsWay1kyPvkpBYOxj1bV52+nMKsNJf9qb+zXixj5m9tHTvv8IUHXd4E2+ghLdoIE7gftuXp/5katI+mfTflkVf5oo89tDu3WW/6SWyhCh88rdeV3yz/Wu0Mi/zU0tn/NbJ4CYv86GNvFs9DN1MfY3hCUncHH9zmkPTIUJZ3YdcCwUvRUOLYCO8Z96MusZy/LZxHovw7BNo+iwOc+k4BZOisNmveR/mflqusRbA6+cT9jy6+z8oA4RASADr6gXbYOzt7KM1yqYorJd6ao5lwASWPtTNSjP3/sIp7xfQHn8m2gxCyGWkeJgtcA1M7Pk2gFGHzHg8IUrObeIgZkx4nGU+0fimYHmvsro9fTSUbPMVwgaFAxMHxS5gVLfrPjU6d7OGYgLKOdEyACno6ZbkjGjrWpe/gpbSz4aE6GOsUAPHr2Pbt4mQObDdfpz9/qOt2MPaOIBYR3SgdddGW/cW2pAymueDEAYafD6kgINsxLk55b038zI1Ag3bQJ+HTe3TtisqbOSkLgRLB+H0O6P/I9oomuSQbsvN1+BuOPo9/oMVuUPMG0cbc56L7lBHrXijEfSFtnAGfS5RO7xXuDTnsWrPl+OpfZ4Aepkyz4JPtxgVvM+RYo0FZQjwmeMLSv4tqDhfZ12f+3Tupwnff15oAciVZyDPeO3LwMl1fDjm/ZjoX9t/f9eYJ6GLDSeSV6/rlBqEOS/rPkXGzTbPC9qhf5IqRHFI6ekKxKy/Pr9LIrIlCFKQGlqQ8W35PRpztlR0smnwsCOMaDfCJgXjvcOHoTCPW04FIFskniu2URgQ1oFr8F8Ej9AykLycswpllSo4Y2qxGUdFzTwmRRHqmUKgIkEKQRLqi4aYX0Q7mQOALwkBJ5nzkYFQjPQiDFiw2YFin1O7JxmRT4FSBJOcEKWBBrnHiKFnvqOxwYzZI7Z65p13rUe6MHxOuY4HvsNJC6XDXZq+TcU8IYfIFALn7kOj677V9EHv06bY1N86B7sGYuOtySaxH6xtwK7NXT6hH/dDthXPVWdvmKnsw4e2Re89a9+Xi9U56gNH+zaLHCbApvNK/+W652Iaa791H4N3o5pFgI8JGG5no1wSCjd+e3eTuXRm1RGnko59zRbQmhrq7tiSsn/gG0SGovscC+uGNy3Xn1OyrP2i73NZFs629D6pSF+T3lgHz0hbMSTQ1HUEkcO3DHDsFuHAbVg7KQN9JlQIMuciy0tsmfrVADvumalIu3zO5wKf+7z3vRnGlYhCyBRXHneQBr4h2t0y919y8Xhr9Kd3ttm//QLITv15HB/9a0JIfumqeLNo/IJz8KLrZmqOTdEtu/vsIuGhyCX85M9M6/7UpF7vs1T76Pa5b3iqcovN5s1saQ/S+MpLWR64tX6ZbMCHqQmbfIbKrZmSp4xDTpHzDlLaHvOL8eIrrQ+vnQeeynm6Up4+RVem2CRg/4acOX0+/r3OCNY116wP49xXss+UvYOvNEfwuvwMmwvBOApgNHSGTTjMCZnX+cJYld2aZFoemEOXcWOR6Vvqk9e5sj2sOprCss06RKbgCOGYIkPmXHHFTaKwjEy+EeT1MlaXiS1590VPpBqYm8H1ITxFRuz0UcKh4ULr6mY1MAV7ddOl2qcPzI3ShPnE/NDv2sbqY0BL3BAen4BpvoZcF0rCbdX8VePcQvNXLuZR/ypvzt6vYa2+Dk15cvu2F2IvitCmd58IpKfIKJ1xXjSH3uNjb8Ft5aM/88ru4cPEjNsOuKMO3rLVUxRyPm/vN43CHgqp8VB2+sKZusZX415uJ/doZnNUvqGBPfd5uPItN0juKntTZOB+0aePh3mR37bZeG8ue21Ijc8pzzLxjiQ1g7IL9hCZdW0r0vQHO/8sKHhghPxah7wtOnf4CORu/KLrrjfiy5lK2EkhX/ZpuW6ztuB7Sjqk56ua7s2s9eH072HA52zFY1hT3Lf+3L1+mrHDZ3WLU5GTcoqcCCcPizXPHxlcA22TPBeFEbM9Y8g8IipcvU5s/MOI8j2h0E32KJ8DU+R0KFDHRTFjIR/MVdF3E387iSguAGHK9VFEEn/g5ivpqpTtMk7PKhAl5ed10UkZa7kxlRsidZHkk2rwmV/AAbtuTlHQ3ZQ4JWC65rgLYu51xssh1PuqRXlobt9t3nFZtD4cy1i9hVXnFyfwS1hd7stEns7sk6rYNj5NzPDh3OfX3t0T3fvGQH7OyltFGLA1v0gK4EaBcQgmAhI4IiODckKGkDwF6Sbl3O4G0Czjm6PQL1O1iYF2RlumOYWVrLiZ9NoWncOI7UEX4SFyRi7yojku+YxKIuvC3KpZ1MmJCbAgE+DQ6s5Xe9y4zhvApqtf58ulDKR2ThwwQ4DM5M+ijBXmoa1enAuVGW1wXmM/f86C6IwgCDjzgg5jjcZcM328yDNcSdvyQc+sDyERBoXTUhYCY9zNPSbwBc6eSAZIbxvrk3XZWIIQWAKDaOmNYL+xS0Pq0ikY9CB2CCfSBNIkGqIjJbpEe4KTMEM46fhYPJlZEAMsKa2YJDkHsveZ0BaOU0CyCPyQSTkECmZYbZ+81Suenlg6d5CDU3AKGrwRpxJQUjv3E7B9NeJnszWUz6yZxkjUOhJwUTm6MXrohGd6FopMhHQICKpgkkbaJCNBYFC6Z4SYyIeF4aDPMN1SbrIMYTcaj4oMR/0DrDZozHF3ZvKpkCRoMgk8C5V7JFM4AFCG+BIFEzLJ6cRTKBmmPE4a/HOJpHcEXB6ZP8BPS3CewWXOeIMqI5HntFhRyqQcJdqckEEcQlMhh+tAjGTQ2C0pWo26KDTAFK1VG4LvKkHhCDYXBADwOXsWaEu55S0/WlDlGiK0W+cm9B4DACk/4gj6WQ/Q7OHyWHfHd/NFKu8wmsBiBbvQ0IFFv1VIeG7oauOzDCHlJH6kklnh6uWPWG061iILBMS+1GWsW6srcL4QkMLs2ZWcW+Ec5JQirURsnEs8RX6aGddY99nWqitLRffSBJSxNLPyAOfK6zjBDeq4dPggd2jmnZSpz33lrl3k8MtQQn2eftjdu63+4hTV2t7zO5zutvGZLA1GCIHuTUXeG2/oA9B4aTggIUXnXQDA3xCDlyyWfH78mFu4RwiobAyGfghZCxxVMgJnoTr7Ptl8VJwiJ+gBreHobNfhOZcFi+/lDUYOWVgD7nS4yQvkPXj3CT1L2rQJDOGMIpfLALcqLAEEoqBrub7u0hk0Vb2FuGoOAltJdhSQZHUE4ng6Esrt3psH5CZQGNuMsZeXXDsOsmmOKTxFTuD0eIBzK28gF1iiFR1JfLhj/VYry0wz0/yMLJiL0oy7k85FaFQ4iaOkh5ZjYbEQduq5O5C89MaTcj+x5OtjSyi48pm4OuHCcPVkcXLWIJaZbQExEgTjlabuT4aD1atXkl9U01XuWPPSHKnrmQ1ZmsKjjOyerM+IfFbAkm59IQa+yuUyjDzU+yaKtAIbZ1WxwK7PzwY933ZhHJdwG3c9XkTcJIoXs9ip+1ls9x8kLm4TrM85cHkm98+7g3fe4K3Eoi1W8/3Lip+acL5yGK/fY41gz7XNCm/IQ9oM4RPg8b7T98XgDT6/bdXy3kbk2n1g5Qq9L/mbhhK8jAP/jiNxix7dCsrG7h2Pi+7J+vlFA4/LhtygMc88GUg5pNuiz0fh+PkTmfidv+sOKaVlvGVnilQ0NPdCUPe2enFBVKmqyoSv1+Dzs1OC2Gg2DMhXqo5gqzG/FKxu1YS8L/xxdBy19JWEFUXy2Z52yBkJwx/KxColQQyr4BGL2q5yntZBih3ru1aaSq95CFAi0SiOdacvbLfyOdpy98OYXDyuLiQW5bE71Z/zqaVkF2BcVB4uRzHlPP7lsTjQ5x2B5oUrrd82iFKR49KNg8/oc1/x3Ft9ZMr+50/A6F9HVq8VVi+JwW5crhmLedf4Bfvu9jzcr/rthS1mOEcIuOE37rrZNg54y4rM7zgOBZl0FBjhaqdT5kVyDT43frR7z3B/ie7mTxF1NmPVoyXPtAisBtiA6rGs0S4XZxMyorQJhdVQCQVBzNRCoeDgRCJCBnw1gWqwFS1YrRmmkI1EyID2WOUjfdHNr2+lCY1Cqwg5iMfm9U4n12NWyxESBVi6IqEASjmF0wGPpXDs3IrIvO6bWvUEC47Lrpc+PzuFyuMadMgciEwKMEk485ZVQ8TgEjlLFhQeOL0aC/4v/PFJeFGclYWTdiMYVeKOuxYFpuregqvU3Pz4TahnUBQEZYoIZUBmdJho3CavaEBVy4GiA6fIJE/gbJbPCtmjagGJtU4YWIzpOLqj6R5oZ2eCta4qdkodd3Mpng6hwqoWbH8Cw7SDK9K6LFgcacid+PpOMvabp8BLFpCWHHMhpCJCWWRSq2rtPmced0l1dqmDNan2vYXMxs+bF85v5OqomIHPnYuyY7AKMSsJ5y6NlveS8/jAlRiIpZI0oQ7EIWSaQAN02UB4cUW3VLlktTtW8r9THjNysTN5hl+gkig85SbZyZUh6Hio1UuuKTcNsFZXc72T+FqSsWUdTUPEintBSKbiuNbpJLqEFqD2whTDOc4LWFA2xW6Kr9fMR3OazY2susadfcoo4sx00CYis2UPGSqcreq65RDToPMy6a3fhbLX9Asw3HW0H+e1zLZJNXr2qm/ywoCciyfJZmKq2HwdKruRvXtbim3RvtNxv2zpZ2zzXNnt9NqyvscrP4K6PtbL0h/G4Ip8Zthyz8lp5F3zHFoWzc6hyYd0sq8jlpWszjE8A6u2SOoxNqmO9JbnjlxnwOrUulXs0pclx0luz54+mrgn3RuaKQweW/m6R2p8twSJcyRfOvQEIiXLVvXLXhIwW+TkglK0JaTOEChicaYt0ksK4kNurWHBnbtQ6sIL44KEwtAZVrXHBJ9nLdSMn0wuUV60CaJ4BV94SZ//kEZ+GJ3qSlEhABdd8IjOAgKAMqMKp3NBnNEFifr2sn7NR3ojz9v+p3sXvQmiJM42e6y++ogqPisABJZWaDNct97PhQWiOtZOr7g0gHGi3FJzsGa60y7anHsCy2dyaCtgZkvM44Z9ZS+ZlodgBdofEMQZed5DBBZIQf4NQpEjeGUgw4s7TTKiCEBLuit8PYEWKWYZfohOL7b+nKzeG9Gmh9Y3M3L04k3JYKeBEOEZB+6U6h+kwoOQ+He4F2SiNX1pFWtXzIJFPbtZ4VLnk+lxUAjTb60HD5Y+W4FEZ3ePWeFOb2CNIyxTtrdCAjI8KYTDByGzq252pIDZwypcaEtZMPunRVWd0R3Vm3qk6XZfAHEZswI+D8vSfh/k2QCSw1XPROYqONcfdTmAod/aWsMXYmjQoPN8TpEsZA7ah5sVo0x4Oy44S8M2l9FSRI5WcHSgNS4jh9Nf3AwCTpBI2AOtWJLxo6lkYi5rd8aDBomiAwvMfBYj6yUQKgM6gls7eDQth92vas0lh0e4SfqyMGTGziuj2QjCOmZRM41YSmXoIFt0Lw/PHmZk8/gVKctpayDpRQsFMk2iq77cN+SgKw2YdwcNsxchYJCEItsSjSV8v2l3IPAEU6HOsjI1+/lhHL6161kdPJWiguzVcGtdKlWOkBgVGaoed3fXqGUJy33irM620jRdc0GKj8TWHyXralUbr4TlTndc9VQh5AhP7mj/5tmHrKVZxRGc6wNdriSBPNXintjwFllK6Nf5OcyR434B0mzQNr/mCDE7WGRs70bB5kKjKYkN6ueqQtBGzEAcTGDiAsbnoLS6Zutd6L0BzE2yLOESEA602VoZV8HybaDIIdSITokAzqv8d2JJ09MhaXjPgK1fJluHSyBQOYdiQAgGRIWDosG0Cq+Otc3oPtIaP9hFkoDpBq04m9lQUKU3XMqi5RawEL3/kbQ+VwINZlfaZ+sMRadPBfG0FbY3BL6X3zryFdQ4No9uRn68cuGXEpGmiBeG0t6i9HOSl5qgVvSHCK1uaSwbCXkET0tdYPrWdaG9mMGO8eKSxL/W8CKwoGuX+6SUaoPbt6pFO7FHc79j6QTXhd6Y7LnHg1W72DqCViAuK1CxtsPT2Etdc5aythlk13INXjjMJXdZXj3Nm1zFutPsyTMuaiocUnaiHSMcFF2rWNaZ7N6GJM+10QcPEZLDOMlQFPNheilwBVVNzeXhdFx3yZ45BdUQaskuMomdO/HUp1Rb7GohSHR7YeoLc/CsldEIcS6Jphxo2T9urYkY2PQMeN86t90U8tCLXBsHXj1QRLYCDZLVij2hd8xB9pE8rOWK071lgIP+2KBU7IteW69JLjpV9rVS0ri8mpTWz08EWMMi0xbKuotqXpNGq5rsDtvASnWnUDiIATkEpA5RyFQoaHes8aeoFB1BOQ5qrFIUqtxbPAdhB9H3Jog8tpARUOpQ4SCVEDNsFBCzAGcgFkvvsuPe4yAGoLG3QAimq2d/PKgawusJivKJWjc9UoaQikqkFvZFhXXOGWqOa3TqDSiGWSLnvyvSm4vXEDkEtLg9+F1DbC+nYUyaHXmpHCrUQbXG0n4ZYq0kY/YJg0sc4cbnh5HeWqrIiaT1mmSEZy/We85YeQdoqs1Q5AFn4lmitFpeVOM+c+otj8Cg+KuidnvJEYki4XFEE3g0/rQB4cMcagIC9W6EAx6SmAdKFRwELQaOviXDxLmR/iPmJqJ5i8UMfVmww8bJqn5zx9+iBLhrXUAew8ew3CADvwGyJ3XVgDhqo+QhEIiaOvaqIB7BoTw84BIwimrglW3D4ysvmqBa5EJPDgWavhDGTGFY5CIqwGcqc/fCi0usGSGvIsM1hyhn9q8hMSKU8ZIBcgDDD7iixx3Z2Dp1X4D7DAlflDSjcwCzazWrpzlZ+pcI5wgybIoAZkZZoBAcAgPpDr9dXmZkRSu2+Yf57Mieqg7ZwkFS7q4G1V9sDEJkw62QYrJFegyIoUoTzK9kFOKIJkCz7LEU25FUyoCLkjF3LdAI660iOslY8QwjNzsgA2ELDRsbRQvQ/Zz2ubHpCG+HASwxvS13+E2TV79j2M4PV/jB2tQoDqmumjMIy5OZTCl9XDeiqjypjexoAhRglnawK5qnD/qOMHRz1HmgSDd56lhNUNVKbqx4htxYoiwdGAaSCusVTnO8Ez2q/rABzWtvO23JCcMrki2DKSKkGioXicshoHoC2n5F/yMe9ASwyQ1S17V6IFuxlfBEd1hhxQNRRUVrdNfOoHvR/PwwzLdluW2XFCLkEJnAESuyLPIImjeT9xXLG+FGQ/CphRDlk7LLCoq8zHBHqic8rU9/VedUZ0NEaERfbqBMs1gyhtF6R3uGfzdCtNn4NNbm9PBN8Z5U8AjWLnPC1Yn2/i0IMkcQ0MmltNrVSWjltXNI5DShkSk+zFJFRBaAowByEKfQCP+Hrx6DScSbceg0hLHVetOuJ+TGsQJl7XrWlGWGVgPnSIJBMpAYReODVu0IlU5S8B0c8avBfSQiZSspSHdNewaG6wwRNl0S2rDaIy5ucJbo5RyJ0SpCq+1gUw8jNlzXyo8h1fGx2luns0OJOJZNOOLspUBwu55CpcPRbPM+R4Wv5gfhoMzWWzeFg5GYHQF3xOoR0wzIfriqMHj21nitjcHygm3RS1b2ZPvkPl9kbl2BMmecVarJNokGCX32/BBpbfBaOXN7pggFrXwgskC69u/TnYNZAAGcmiZtcFOe0iFVNaLxI7DwgE3OTNh34lxvpsXW9apW317WL0OC/YVQ3qOTYYP3NUWOwNGlzXEaFaY01iA9Wa4p12Yt9ukFXCcW7FVeTlyjFv22vIe6qo+X/rby1PSdyVj7bHX/iAayqGx3dssusPrwJ9LPKPlg7GQY3lQY/ZrWP9mKNfNQRWTHDk+nralubVzr9d0FqcNpHF5d+E2pOboWKQlFtKp18pD5R/5iYbfpooFIszExYsNa2pBSkaK3UU+msYnN4NIZks69q6MUekLYxpM1svai8VDZnpoxknVkVrdSZCgMd4LCQY0Cd0Htrd2KvGcj2HDDwzAMi3RqMoiURUYSeWa46VoVYHHwyuv3m2rBS9N6FsUprGcwNXEMGszFIQa0e2kexfvywv0pDKG2P4EzvZl9UomuFQyyqsFBbcRMNV7JhRrTNRKCsZTSEKGQh/96bUsfyAYD2VGf4Wn5aJhDGA1Oya38lk7JZR/8xihZD+URJ1S4Nd21u035TN+DJJzmeBfiEFIwIUOoKoh+tkWGSMVnxtCYmgTTt0sUU6MYUM3TRNnr8uSqDW7ErlqJQiEVVZlsUa3pByUgVMAqUlWdsGRy6rwTuhQH6ZC24dWP2sFWXaYN2eI8frEF2xtrdbhmNrZ2zwiWO5FIdtKKHjwPIhaLWnhCcNCZMIihXCgTCC9DY7CBCAgHoCwd6Pxv1ztEVpE6Spys3BqsMfzHPJshW6ZkVUX0czI4sGzof6EdENGeWnZbN5OyDBHhIDqbQkUGIy727kziXECijRhSgThMkKG/P9YlO+Gr1GqMkly73El7xqjkka9v6dkQARBjpQkOOptimF1onX09Dch9bgmoVezDa2tkNmjUsQ1NascIjKUzCIeklREILRIIaVgmVqUFCH8qSOQp0eU6yJLVNmCFAlKd+r5mgDKQIgXymUkrELtF0fLz8LMSw/ZCPmYOkAEQ5F8/hIzByxYYuyHmMk8SAVoUT0oF8L4dd7sd+1wCRALDonaUEDjR3KYZolzoHHGo4Q5GvyQDc4owr8Frae1r4hI9arIYuTSedGVDcfezSP8SFNmsDzTqooPoQS/AMsgMUgVMow8KiRuiySuDVSX3oXgLVtHqp6QNtyGFg+paU9p5ZHVX+5zhwDG8M5fq2fxmtwnZxCgZQI1+ZRDTSG9AOKLe2VReuGid6uAzF4RCVTu4ZAlTo7doKTej/hsMHZ6oC4glMzRlIukr7tBVsxJ6bAIxkDq2ZZjIOThJONNCCrrX9HrDjYs4O2hQLOe5MF1Wr6pMBCTfwfd7KdG0dIBv3BFoXDWAp0w6GtWm0tn4FbRTGWmypdtQcIai5IPBoEd/wf/5X/8rRwSBg1tT5pj7wNajspmYfCY56HlQW9rMdENwTLnwKK3nQ5xVw8Clj4LlWlXju5UTF/sgO4kOC7IOqOrNuSLjz+HhmGtrps4OCJzLBbiPGSe9DfaswZ196RLxThwOyD6YVdZJout4bQtAMKLB0mAlyUdrfBUurPbRoYwWXqn6zI8YsZ2Z34MY/i+V4knuAPRzmupHePTRm8FTQjBNIjWv2vVsGOyB1hTGHdLUkm0KIxvFGxvtQcpOppJO52wxm3FT7LDFOxvBGEK/kj5ncqBeY4EJLPvsR6u46JpCzYZYVbGaVmTlTHU2iOQyeXKx+NJK36I+peqKo3mIPTngPHBI8g+qaOew1kQ1dAvIkKJKHrRPykmgQX3zxBtexyCb8tzqaiki+D/+/d9XOncrHd26wV6Hxo9dzWx/yN86BclDNx99biJ5N0YBN53o357fnhzLG9421UbzkG9fIysqLnd/1T5+rJQpXHb6duMfViyvu6t3HQn6j1j7OdZffWZJTUCffYRg7902QltoLy5kExF0oeHevF7a+WG2ewu55eWVy8BVbc37c/rsyqKjLHPt2cxNWh9NXdfCFQ0dE9GXtt7xvRhaV8geq3ax18y6bKw1WGjlZBrXP5v7iTbKEK1QpVQ2qpVB9tvBzuBqVa+9JqLPq0aHb7v45mBpigD/+3/5L/vY7ZuTSSn2wTprxvmmFnS3RoAS7iCKnLMU9GOhll2mZxQSjVD6ss9yAJYDP4J+IsWBDRZFA6Y7O7MkhtFm+HE1slMNQ0gDDdQL8zo7raIPmEK0bfTmglhHDa2CFLve8gKJiaVbyCYlGXyyjTNrHV+xdk5tT7q5qXOB/+3fPnW62H2L3jczhCNYNe4SIdLgaAmXaiaIrBlapmO0hYSJR/DfEULDbV5GQN7rpGEfT5aDXKVIVAH5YpMlhq+EVDjdoxb0RJmgs8W3eSPIqESy1IG9sVFmeNBCWaK6uBRBvp24VJCxo+ZVBEsC8SnVajMbRGaxQnWeQpuGjjZCrrRwuy3Z9svgBfyvx8dJPCj7x5aAI9aEXKn2rTnepVz5eeDTtT+4XkZMbN2SECXDPdGwzwbF1muQq4HOV6wTfi1uQcVXXEYtQVrNZEalDCuT5XP9SPW2pMspk5sJG619LSJRUeNPsPe3laihRJMkbf0vmuu7qbJkW960mL8ZFon/5RiUboUvzRCxpGbNVR9slWOl0JrpaZLpexaloiyBv4z8WZaWIFfRcroQVEtSSYnuHN02daNvSFeT+UYgeFT+kMtwomwsiwxk6sfogZD3svd80vYxoVhHeiyuKNElCI0XFn+F4YCrVzc5hhkFwWxii0zGdSw1CsGawZcyhK5dJLKpxuATCvDvY3QZ3sY+pN8WGgVormFH9yr5XQXWbaVS85hl6aZQLTeE4ODIUFEXc8BlvlDYSOTfehEIRYa1acnbxG2GdbZt6Rg6ktnN65SXNl+Y3CoIVjeLi5wyJodKMObT5LUdUqAP4Uv1y6VWPe9O3orUKYjZrpDWimnN3fJm7hw3J3MZJSUUweFU/WzWnm2uE+ggtwlf7Y1CMUSaFs1ehqCQ620Aqx812D3lfVhbOQCsgwtZBLaK/gc316DbyDhHudolmulmIZRbYEfIAGEXC+FCdulODy/NufvqpUrDArJileS07jSMmtTSi0m9WkKzbixZOmz9VWthjkboCmuETcVYejtdu9q67NJT/cRFapZ8eIZoVI54Cw8WmOtn5fw9UYIucGgRM/O4lLuFsj2mhYNVjjqSLsJ5FUmoZ6tri79Ytw/OdAQWm+GElRTu7RBbdi+uOHuwF/dMqhVPMe1iXyKCcrcc2Txmc9xY3XiR3eg9T8vm+rOFuPCpwd0PQu1OFgCGI4Xd/iCDPPn76++vv7/+/vrjr/93ANrDU7YEpFHqAAAAAElFTkSuQmCC";
    this.pickerColorImgObj;
    this.palette = ["#000000","#434343","#666666","#999999","#B7B7B7","#cccccc","#D9D9D9","#EFEFEF","#F3F3F3","#ffffff",
		    "#980000","red","#F90","yellow","lime","cyan","#4A86E8","blue","#90F","magenta",
		    "#E6B8AF","#F4CCCC","#FCE5CD","#FFF2CC","#D9EAD3","#D0E0E3","#C9DAF8","#CFE2F3","#D9D2E9","#EAD1DC",
		    "#DD7E6B","#EA9999","#F9CB9C","#FFE599","#B6D7A8","#A2C4C9","#A4C2F4","#9FC5E8","#B4A7D6","#D5A6BD",
		    "#CC4125","#E06666","#F6B26B","#FFD966","#93C47D","#76A5AF","#6D9EEB","#6FA8DC","#8E7CC3","#C27BA0",
		    "#A61C00","#C00","#E69138","#F1C232","#6AA84F","#45818E","#3C78D8","#3D85C6","#674EA7","#A64D79",
		    "#85200C","#900"," #B45F06"," #BF9000","#38761D","#134F5C","#15C","#0B5394","#351C75","#741B47",
		    "#5B0F00","#600","#783F04","#7F6000","#274E13","#0C343D","#1C4587","#073763","#20124D","#4C1130"];
    
    this.getAdmin = function(){
        this.pickerColorImgObj = new Image();
        this.pickerColorImgObj.src = this.pickerColorImg;
        return '<div class="HTML5editorAction" style="position:relative;width:25px;height:25px;position:relative;background:url(' + this.icon + ') no-repeat  center 2px;" data-name="' + this.name + '"data-command="' + this.command + '"><div class="temoin" style="position:absolute;top:19px;height:3px;left:2px;right:3px;"></div></div>';
    }
		
    this.onClick = function(e, editor){
	var html = '<style>.pick{float:left;width:16px;height:16px;margin:1px;cursor:pointer}.pick:hover{outline:1px solid #777}</style><div style="width: 180px;">';
	for (var i = 0; i < this.palette.length; i++) {
            html += '<div class="pick" style="background:' + this.palette[i] + '"></div>';
        }
	html += '<div class="picker" style="display:none">';
	html += '<div style="float:left;width:22px;height:22px;margin: 1px;border: 1px solid #999;" class="picked_color_preview"></div>';
        html += '<div style="float:left;width:50px;height:22px;margin-left: 7px;" class="picked_color_rgb"></div><div style="clear:both"></div>';
        html += '<canvas style="width:180px;height:180px;" class="mycolorpicker"></canvas></div>';
	var prop = $('.popover',editor.toolbar).css({width:"200px",height:"165px"}).appendTo($(e.target).closest(".btn")).show().contents().find("body").empty().append(html);
	var temoin = $(".temoin",e.target);
        var c = $('.mycolorpicker',prop).get(0);
        var ctx = c.getContext('2d');
        c.width  = this.pickerColorImgObj.width;
        c.height = this.pickerColorImgObj.height;
        ctx.drawImage(this.pickerColorImgObj,0,0);
        var $this = this;
	$(c,prop).on("mousemove.Wcolorpicker", function(event){
            var x = event.originalEvent.layerX - c.offsetLeft;
            var y = event.originalEvent.layerY - c.offsetTop;
            var img_data = ctx.getImageData(x, y, 1, 1).data;
            var rgb = $this.rgbToHex(img_data[0],img_data[1],img_data[2]);
            $('.picked_color_preview',prop).css("background-color",rgb);
            $('.picked_color_rgb',prop).text(rgb);
        });
	$(c,prop).on("click.Wcolorpicker", function(event){
            editor.setCommand($this.command,$('.picked_color_rgb',prop).text());
	    temoin.css("background-color",$('.picked_color_rgb',prop).text());
            $('.popover',editor.toolbar).hide();
	    $(c,prop).off(".Wcolorpicker");
	    event.stopPropagation();
        });
	$(".pick",prop).on("mousedown.Wcolorpicker", function(event){
            editor.setCommand($this.command,$(this).css("background-color"));
	    temoin.css("background-color",$(this).css("background-color"));
            $('.popover',editor.toolbar).hide();
	    $(c,prop).off(".Wcolorpicker");
	    event.preventDefault();
        });
    }
    
    this.setCurrentValue = function(elmt, color){
        $(".temoin", elmt).css("background-color",color);
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
    this.allowedStyles = {"color": /.*/};
    
}

function wysiwyg_hiliteColor() {
    
    wysiwyg_colorPicker.call(this);
		
    this.name = this.command = "backColor";
    this.category = "format";
    this.icon = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAe5JREFUeNqMUz1LI1EUPe9FRJGIsqAgmDjuBKZKoYuCilqk0EYrG3+AYiUWIhayKUQQwWA5hUHFzir+ghijRjEiFpIwJCAWwoZMxM6YzOydcSabHaPxwuFy7nvncO/7YKoK6Po/pNMdkOWx4ObmUVDT3mvOXB2cECXoNny+P3pnZ/l3dc2BcafBWCwm4fBwBKenklksl8vY2prG+fk7TyQk7OxM4+rK5B8MMDqaQnd3DtGoWDFYXo4gHhfx+PgDZ2ciFhcjGBhImeu5HHB/D1xeWgbh8CC83hxKpZK5oTobM9vciGIReH1FV3UHJ4riRibT/p8wGAxgeFiBx5PH0JCCjY0AdfITLy9oIpNjMpZtg3FN05ggqMzIxM1swOtVTe7xqMzvzxDPTJJBoK8P/b29mKFbmWtAnXh+JgdWgN9fmKDW1w3x0xMKNzfIUqNJ/pnw9vaB5i+YBtRuLfE8bUt+6GB7m2NvL1bhX4mN9YrB7m7REvJvi41gq6ucHk3NSX4JgrCWSChTTnE6DUQiQCpVQ7WwwOFyuXwEORQK6aIoXhwc4DocRv/KCiBJdU5dlhlsMef8mDG2RMKWukIr3PE4rvN52OJZqrURWgkthGZCo3V2xtzMeQtumhc0b7KnR9vPZnFnGbzVgPlt/gowAIULFFoZgfsOAAAAAElFTkSuQmCC";
    this.allowedStyles = {"background-color": /.*/};
}

function wysiwyg_insertImage() {
    
    wysiwyg_btn.call(this);
		
    this.name = this.command = "insertImage";
    this.category = "insert";
    this.icon = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAIfSURBVDjLpZPNS5RRFMZ/577v+L5jmlmNoBgE4iLIWkgxmTtx4R8QLXLRB1GYG4lAwlWkCH1sShcRuIgWYUQoBIUVgojLyowWLSRhSCNtchzn672nxYxT6hRBD/cuzuW5D+c5H6Kq/A9cgM6+0VtBTk4tJwM/kS7BspvDsAc7w4w8uXGyxwUIrHRev9AcqYlERMRFAS3+E1RBdSNWglyGs9eenwbyAsuJwIvsjUjX7QfU7duF51gC9cBUYYT8NYJjhM8fZ+nvuUg2EClaSKbBGJfGhv0cjLbiGAfVAMQFEYwIIgZjDCHHYO2WGmzY9DwfP1yRz/cv0KLJLQLZTIpsah1EULVYDbDWIICq4khALpNE1W7PQBW+xmN8W4qTtTmsBvxIL5IJ6pECp8ZbYX0tDmpKC3xZLCe0kPr1oBFUU0XyCmEWFnT7HNgC3zhlGMcr6TtITJBLvKK6+jtX7z/ElDV4cGJzBn9COv6MPZXTNDcfpX53I6/nnrL+ftKPdtfddAHUWgRYmp8rKRAKPabtSAeBCThc287Eh1GiTS3Mfxq75OZnLd+coYG+YvQ7rtzpJyQVdBw4B8DltnuMzw4DY74LsDNs4jaXqqotl3wLC4KFw+panLnYNG9jU/S2jzD44gx+vlYpF2CHZx6dH3h5LJnVJmtL7dJxf+bdtNdyqJXx2WHKxGXqzSTAkPzrOke76waBLqASWAWGZ+7Gen8CJf/dMYh8E3AAAAAASUVORK5CYII=";

    this.allowedTags = {"img":["id","src","alt","title","class","style","dir","lang","title"]};
		
    this.onClick = function(e, editor){
	var html = 'URL <input type="text" class="URLimg"><input type="button" value="Add" class="addIMG">';
	var prop = $('.popover',editor.toolbar).css({width:"300px",height:"50px"}).appendTo(e.target.parentNode).show().contents().find("body").empty().append(html);
        var $this = this;
	$(".addIMG",prop).on("mousedown",function(event){
            console.dir($(".URLimg",prop).val());
	    editor.setCommand($this.command,$(".URLimg",prop).val());
	    $('.popover',editor.toolbar).hide();
	    $(".addIMG",prop).off("mousedown");
	});
    }
		
}