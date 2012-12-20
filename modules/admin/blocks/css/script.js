function blockAdminCSS() {
    
    this.loadCreationMode = function () {
	
	//ui update
	$("#panelcss").on("keyup.creation change.creation",".liveconfig", function(event){
	    var nbstyle = document.getElementById("current_stylesheet_nb").value;
	    var nbrule = document.getElementById("current_stylesheet_nb_rule").value;
	    var stylesh = ParsimonyAdmin.currentDocument.styleSheets[nbstyle].cssRules[nbrule];
	    if(typeof stylesh != "undefined") var rules = stylesh.style.cssText + this.getAttribute("name") + ": " + this.value + ";";
	    else rules = this.getAttribute("name") + ": " + this.value + ";";
	    blockAdminCSS.setCss(nbstyle, nbrule, document.getElementById("current_selector_update").value + "{" + rules + "}");
            ParsimonyAdmin.$currentDocument.find(".parsimonyDND").parsimonyDND("updatePosition");
        })
	.on('click.creation',".explorer",function(event){
            window.callbackExplorerID = $(this).attr('rel');
            window.callbackExplorer = function (file){
                $("#" + window.callbackExplorerID).val("url( " + file + ")");
                $("#" + window.callbackExplorerID).trigger('keyup');
            }
            ParsimonyAdmin.displayConfBox(BASE_PATH + "admin/explorer","Explorer");
	});
	$("#right_sidebar").on('click.creation',".cssPickerBTN", function(e){
	    e.preventDefault();
	    e.stopPropagation();
            $("#threed").show();
	    function destroyCSSpicker(){
		$('#container',ParsimonyAdmin.currentBody).off(".csspicker");
                $("#rotatex,#rotatey").val(0);
                $("#rotatez").val(300);
		$(".cssPickerBTN").removeClass("active");
	    }
	    if($(this).hasClass("active")){
		destroyCSSpicker();
		return false;
	    }
	    ParsimonyAdmin.closeParsiadminMenu();
	    $('#container',ParsimonyAdmin.currentBody).on('mouseover.csspicker',"*", function(event) {
		event.stopPropagation();
                $(".cssPicker",ParsimonyAdmin.currentBody).removeClass("cssPicker");
                this.classList.add("cssPicker");
	    });
	    $(".cssPickerBTN").addClass("active");
	    $('#container',ParsimonyAdmin.currentBody).on('click.csspicker',"*",function(e){
		e.preventDefault();
		e.stopPropagation();
                $("#threed").hide();
                ParsimonyAdmin.$currentBody.css('-webkit-transform','initial').removeClass("threed");
		$(".cssPicker").removeClass("cssPicker");
		$(this).addClass("cssPicker");
		blockAdminCSS.getCSSForCSSpicker();
		var title = CSSTHEMEPATH;
		if(this.id.length > 0 && $(".selectorcss[selector='#" + this.id + "']", $("#changecsscode")).length == 0) blockAdminCSS.addNewSelectorCSS( title, "#" + this.id)
		var forbidClasses = ",selection-block,block,container,selection-container,";
		$.each(this.classList, function(index, value) {
		    if($(".selectorcss[selector='." + value + "']").length == 0 && forbidClasses.indexOf("," + value+ ",") == "-1" && value != "parsieditinline"){ blockAdminCSS.addNewSelectorCSS( title, "." + value);}
		});
		var good = false;
		var selectProp = "";
		if(this.id == ""){
		    if(this.getAttribute('class') != undefined && this.getAttribute('class') != "") selectProp = ("." + this.getAttribute("class").replace(" ","."));
		    $(this).parentsUntil("body").each(function(){
			if(!good){
			    var selectid = "";
			    var selectclass = "";
			    if(this.getAttribute('id') != undefined) selectid = "#" + this.getAttribute('id');
			    else{
				if(this.getAttribute('class') != undefined && this.getAttribute('class') != "") selectclass = "." + this.getAttribute("class").replace(" ",".");
			    }
			    selectProp = selectid + selectclass + " " + selectProp;
			    if(selectid != "") good = true;
			}
		    });
		    selectProp = selectProp.replace(".cssPicker","").replace(".clearboth","").replace(".parsieditinline","").replace(/\s\s+/g," ");
		    if($(".selectorcss[selector='" + selectProp + "']", $("#changecsscode")).length == 0) blockAdminCSS.addNewSelectorCSS( title, selectProp);
		}
                
		destroyCSSpicker();
		return false;
	    });
	});
        
        blockAdminCSS.iframeStyleSheet = ParsimonyAdmin.currentDocument.styleSheets[ParsimonyAdmin.currentDocument.styleSheets.length-1];

	/* Shortcut : Save on CTRL+S */
	$(document).on("keydown.creation", function(e) {
	    if (e.keyCode == 83 && e.ctrlKey) {
	      e.preventDefault();
	      $("#savemycss").trigger("click");
	    }
	});
	
    }
    
    this.unloadCreationMode = function(){
	$("#panelcss").off('.creation');
	$('.parsimonyDND',ParsimonyAdmin.currentDocument).parsimonyDND('destroy');
	$("#colorjack_square").hide();
    }
}

blockAdminCSS.csseditors = [];

blockAdminCSS.updateCSSUI = function (cssprop) {
    $("#current_selector_update,#current_selector_update_prev").val(cssprop.selector);
    $("#changecsspath").val(cssprop.filePath);
    $("#changecssform input,select").val('');
    $.each(cssprop.values, function(i,item){
	$("#panelcss [css=" + i + "]").val(item);
    });
}
	
blockAdminCSS.setCss = function (nbstyle, nbrule, rule) {
    if(nbrule == null){
	nbRule = ParsimonyAdmin.currentDocument.styleSheets[nbstyle].cssRules.length;
	if(nbRule > 0) nbrule = nbRule - 1;
    }
    if(typeof ParsimonyAdmin.currentDocument.styleSheets[nbstyle].cssRules[nbrule] != "undefined") ParsimonyAdmin.currentDocument.styleSheets[nbstyle].deleteRule(nbrule);
    ParsimonyAdmin.currentDocument.styleSheets[nbstyle].insertRule(rule,nbrule);
}
    
blockAdminCSS.displayCSSConf = function (filePath,selector) {
    blockAdminCSS.openCSSForm();
    ParsimonyAdmin.postData(BASE_PATH + "admin/getCSSSelectorRules" ,{
	TOKEN:TOKEN,
	selector: selector,
	filePath: filePath
    } ,function(data){
	$("#css_panel input[type!=\"hidden\"]").val("");
	blockAdminCSS.updateCSSUI($.parseJSON(data));
	
    });
    var selectorPrev = document.getElementById("current_selector_update_prev").value;
    if(selectorPrev.length > 0){
	var nbstyle = document.getElementById("current_stylesheet_nb").value;
	var nbrule = document.getElementById("current_stylesheet_nb_rule").value;
	blockAdminCSS.setCss(nbstyle, nbrule, selectorPrev + "{" + (document.getElementById("current_stylesheet_rules").value || " ") + "}");
    }
    document.getElementById("typeofinput").value = "form";
    document.getElementById("current_stylesheet_rules").value = "";
    document.getElementById("current_stylesheet_nb_rule").value = "";
            
    var styleSheets = ParsimonyAdmin.currentDocument.styleSheets;
    for (var i = 0; i < styleSheets.length; i++){
	if(styleSheets[i].href != null && !!styleSheets[i].href && styleSheets[i].href.match(new RegExp(filePath))){
	    document.getElementById("current_stylesheet_nb").value = i;
	    $.each(styleSheets[i].cssRules, function(nbrule) {
		if(this.selectorText == selector){
		    document.getElementById("current_stylesheet_nb_rule").value = nbrule;
		    document.getElementById("current_stylesheet_rules").value = styleSheets[i].cssRules[nbrule].style.cssText;
		}
	    });
	    if(document.getElementById("current_stylesheet_nb_rule").value.length == 0){
		var nbRule = ParsimonyAdmin.currentDocument.styleSheets[i].cssRules.length;
		if(nbRule > 0) nbrule = ParsimonyAdmin.currentDocument.styleSheets[i].cssRules.length - 1;
		document.getElementById("current_stylesheet_nb_rule").value = nbRule;
	    }
	}
    }

    if($(selector,ParsimonyAdmin.currentBody).length == 1){
	$(selector,ParsimonyAdmin.currentBody).parsimonyDND('destroy').parsimonyDND({
	    stopResizable : function(event, ui) {
		$("#form_css input[css=width]").val((ui.width() != 'auto' ? ui.width() : '') + "px");
		$("#form_css input[css=height]").val((ui.height() != 'auto' ? ui.height() : '') + "px");
		$("#form_css input[css=left]").val((ui.css('left') != 'auto' ? ui.css('left') : ''));
		$("#form_css input[css=top]").val((ui.css('top') != 'auto' ? ui.css('top')  : ''));
	    },
	    stopDraggable: function(event, ui) {
		$("#form_css input[css=left]").val((ui.css('left') != 'auto' ? ui.css('left')  : ''));
		$("#form_css input[css=top]").val((ui.css('top') != 'auto' ? ui.css('top')  : ''));
	    }
	});
    }
}
blockAdminCSS.CSSeditor = function (id) {
    editor = CodeMirror(function(node) {
	document.getElementById(id).parentNode.insertBefore(node, document.getElementById(id).nextSibling);
    }, {
	gutters: ["guttercss"],
	mode:"text/css",
	value: document.getElementById(id).value,
	lineNumbers: false,
	autoClearEmptyLines:true
    });
    editor.id = id;
    editor.on("change", function(c, change) {
	    var textarea = document.getElementById(c.id);
	    var nbstyle = textarea.getAttribute('data-nbstyle');
	    var nbrule = textarea.getAttribute('data-nbrule');
	    var selector = decodeURIComponent(textarea.getAttribute('data-selector'));
	    var code = selector + '{';
	var cptL = c.lineCount();
	for(var i = 0;i < cptL; i++){
	    if(c.lineInfo(i).textClass != "barre") code += c.getLine(i);
	    }
	    blockAdminCSS.setCss(nbstyle, nbrule, code + "}");
	    textarea.value = c.getValue();
    });
    editor.on("gutterClick", function(c, n) {
	var info = c.lineInfo(n);
	if (info.textClass == "barre"){
	    c.removeLineClass(n, "text", "barre");
	    c.setGutterMarker(n, "guttercss", null);
	}else{
	    var elmt = document.createElement("span");
	    elmt.classList.add("activebtn");
	    elmt.innerHTML = "×";
	    c.setGutterMarker(n, "guttercss", elmt);
	    c.addLineClass(n, "text", "barre");
	    }
	c._handlers['change'][0](c);
    });
    blockAdminCSS.csseditors.push(editor);
}
blockAdminCSS.getCSSForCSSpicker = function () {
    var matchesSelector = (document.documentElement.webkitMatchesSelector || document.documentElement.mozMatchesSelector || document.documentElement.oMatchesSelector || document.documentElement.matchesSelector);
    var json = '[';
    this.openCSSCode();
    var elmt = $('.cssPicker',ParsimonyAdmin.currentBody).removeClass('cssPicker').get(0);
    var styleSheets = ParsimonyAdmin.currentDocument.styleSheets;
    for (var i = 0; i < styleSheets.length; i++){
	if(styleSheets[i].cssRules !== null && styleSheets[i].href != null && !!styleSheets[i].href && styleSheets[i].href.indexOf("iframe.css") == "-1" && styleSheets[i].href.indexOf("/" + window.location.host + BASE_PATH + "lib") == "-1"){
            for(j=0; j < styleSheets[i].cssRules.length;j++) {
                var rule = styleSheets[i].cssRules[j];
		if(matchesSelector.call(elmt,rule.selectorText)){
		    var url = styleSheets[i].href.replace("http://" + window.location.host,"").substring(BASE_PATH.length);
		    blockAdminCSS.addSelectorCSS(url, rule.selectorText, rule.style.cssText.replace(/;[^a-zA-Z\-]+/gm, ";\n"), i , j);
		    json += '{"nbstyle":"' + i + '","nbrule":"' + j + '","url":"' + url + '","selector":"' + rule.selectorText + '"},';
		}
	    }
	}
    }
    if(json.length > 1){
	json = json.substring(0, json.length - 1) + ']';
	$.post(BASE_PATH + "admin/getCSSSelectorsRules", {
	    json: json
	},function(data) {
	    $.each(data, function(i,item) {
		var id = 'idcss' + item.nbstyle + item.nbrule;
		document.getElementById(id).value = item.cssText;
	    });
	    $.each(blockAdminCSS.csseditors,function(i, el){
		el.setValue(document.getElementById(el.id).value);
	    });
	});
    }
            
}
blockAdminCSS.addNewSelectorCSS = function (path, selector) {
    var code = '';
    var nbstyle = '';
    var nbrule = '';
    var styleSheets = ParsimonyAdmin.currentDocument.styleSheets;
    for (var i = 0; i < styleSheets.length; i++){
	if(styleSheets[i].href != null && !!styleSheets[i].href && styleSheets[i].href.match(new RegExp(path))){
	    nbstyle = i;
	    $.each(styleSheets[i].cssRules, function(indexRule) {
		if(this.selectorText == selector){
		    nbrule = indexRule;
		    code = styleSheets[i].cssRules[nbrule].style.cssText;
		}
	    });
	    if(nbrule.length == 0){
		nbrule = ParsimonyAdmin.currentDocument.styleSheets[i].cssRules.length;
	    }
	}
    }
    this.addSelectorCSS( path, selector, code, nbstyle, nbrule);
    this.setCss(nbstyle, nbrule, selector + "{" + code + "}");
}
blockAdminCSS.addSelectorCSS = function (url, selector, styleCSS, nbstyle, nbrule) {
    var id = 'idcss' + nbstyle + nbrule;
    var code = '<div class="selectorcss" title="' + url + '" selector="' + selector + '"><div class="selectorTitle"><b>' + selector + '</b> <small>in ' + url.replace(/^.*[\/\\]/g, '') + '</small></div><div class="gotoform" onclick="blockAdminCSS.displayCSSConf(\'' + url + '\',\'' + selector + '\')"> '+ t('Visual') +' </div></div>'
    + '<input type="hidden" name="selectors[' + id + '][file]" value="' + url + '"><input type="hidden" name="selectors[' + id + '][selector]" value="' + encodeURIComponent(selector) + '">'
    + '<textarea  class="csscode" id="' + id + '" name="selectors[' + id + '][code]" data-nbstyle="' + nbstyle + '" data-nbrule="' + nbrule + '" data-selector="' + encodeURIComponent(selector) + '">' + styleCSS.replace(/;/,";\n").replace("\n\n","\n") + '</textarea>';
    $("#changecsscode").prepend(code);
    this.CSSeditor(id);
}
	
blockAdminCSS.openCSSForm = function () {
    $("#right_sidebar .active").removeClass("active");
    $(".panelcss").addClass("active");
    $("#panelcss").removeClass("CSSCode CSSSearch").addClass("CSSForm").show();
    document.getElementById("typeofinput").value = "form";
}
blockAdminCSS.openCSSCode = function () {
    $("#right_sidebar .active").removeClass("active");
    $(".panelcss").addClass("active");
    $("#panelcss").removeClass("CSSForm CSSSearch").addClass("CSSCode");
    document.getElementById("typeofinput").value = "code";
    $("#changecsscode").empty();
    $.each(blockAdminCSS.csseditors,function(i, el){
	blockAdminCSS.csseditors.splice(i,i+1);
    });
}
    

ParsimonyAdmin.setPlugin(new blockAdminCSS());