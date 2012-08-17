function blockAdminCSS() {
    
    this.init = function () {
	
	//ui update
	$("#panelcss").on("keyup change",".liveconfig", function(event){
	    var nbstyle = document.getElementById("current_stylesheet_nb").value;
	    var nbrule = document.getElementById("current_stylesheet_nb_rule").value;
	    var stylesh = ParsimonyAdmin.currentDocument.styleSheets[nbstyle].cssRules[nbrule];
	    if(typeof stylesh != "undefined") var rules = stylesh.style.cssText + this.getAttribute("name") + ": " + this.value + ";";
	    else rules = this.getAttribute("name") + ": " + this.value + ";";
	    blockAdminCSS.setCss(nbstyle, nbrule, document.getElementById("current_selector_update").value + "{" + rules + "}");
	});
	
	
	$("#panelcss").on('click',"#csspicker", function(e){
	    e.preventDefault();
	    e.stopPropagation();
            $("#threed").show();
	    function destroyCSSpicker(){
		$('#container',ParsimonyAdmin.currentBody).off(".csspicker");
		$("#csspicker").removeClass("active");
		$("#blockOverlay").removeClass("csspicker");
	    }
	    if($(this).hasClass("active")){
		destroyCSSpicker();
		return false;
	    }
	    ParsimonyAdmin.closeParsiadminMenu();
	    $("#blockOverlay").addClass("csspicker");
	    $('#container',ParsimonyAdmin.currentBody).on('mouseover.csspicker',"*", function(event) {
		event.stopPropagation();
		var offset = $(this).offset();
		var offsetFrame = $("#parsiframe").offset();
		$("#blockOverlay").css({
		    "display":"block",
		    "top":offset.top + "px",
		    "left":offset.left + offsetFrame.left + 40 +  "px",
		    "width":$(this).outerWidth() + "px",
		    "height":$(this).outerHeight() + "px"
		});
	    });
	    $("#csspicker").addClass("active");
	    $('#container',ParsimonyAdmin.currentBody).on('click.csspicker',"*",function(e){
		e.preventDefault();
		e.stopPropagation();
                $("#threed").hide();
                $(ParsimonyAdmin.currentBody).add("#blockOverlay").css('-webkit-transform','initial');
                $(ParsimonyAdmin.currentBody).removeClass("threed");
		$(".cssPicker").removeClass("cssPicker");
		$(this).addClass("cssPicker");
		blockAdminCSS.getCSSForCSSpicker();
		var title = CSSTHEMEPATH;
		if(this.id.length > 0 && $(".selectorcss[selector='#" + this.id + "']", $("#changecsscode")).length == 0) blockAdminCSS.addNewSelectorCSS( title, "#" + this.id)
		var forbidClasses = ",selection-block,block,container,selection-container,";
		$.each(this.classList, function(index, value) {
		    if($(".selectorcss[selector='." + value + "']").length == 0 && forbidClasses.indexOf("," + value+ ",") == "-1"){ blockAdminCSS.addNewSelectorCSS( title, "." + value);}
		});
		var good = false;
		var selectProp = "";
		if(this.id == ""){
		    if($(this).attr('class') != undefined && $(this).attr('class') != "") selectProp = ("." + $(this).attr("class").replace(" ",".")).replace(".cssPicker","");
		    $(this).parentsUntil("body").each(function(){
			if(!good){
			    var selectid = "";
			    var selectclass = "";
			    if($(this).attr('id') != undefined) selectid = "#" + $(this).attr('id');
			    else{
				if($(this).attr('class') != undefined && $(this).attr('class') != "") selectclass = "." + $(this).attr("class").replace(" ",".");
			    }
			    selectProp = selectid + selectclass.replace(".clearboth","").replace(".parsieditinline","") + " " + selectProp;
			    if(selectid != "") good = true;
			}
		    });
		    selectProp = selectProp.replace(/\s\s+/g," ");
		    if($(".selectorcss[selector='" + selectProp + "']", $("#changecsscode")).length == 0) blockAdminCSS.addNewSelectorCSS( title, selectProp);
		}
                
		destroyCSSpicker();
		return false;
	    });
	});
	
    }
}

blockAdminCSS.csseditors = [];

blockAdminCSS.updateCSSUI = function (cssprop) {
    $("#current_selector_update,#current_selector_update_prev").val(cssprop.selector);
    $("#changecsspath").val(cssprop.filePath);
    $("#panelcss *[data-initial]").val('');
    $.each(cssprop.values, function(i,item){
	$("#panelcss [css=" + i + "]").val(item);
    });
}
	
blockAdminCSS.setCss = function (nbstyle, nbrule, rule) {
    if(nbrule == null){
	nbRule = ParsimonyAdmin.currentDocument.styleSheets[nbstyle].cssRules.length;
	if(nbRule > 0) nbrule = ParsimonyAdmin.currentDocument.styleSheets[nbstyle].cssRules.length - 1;
    }
    if(typeof ParsimonyAdmin.currentDocument.styleSheets[nbstyle].cssRules[nbrule] != "undefined") ParsimonyAdmin.currentDocument.styleSheets[nbstyle].removeRule(nbrule);
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
    var selectorPrev = $("#current_selector_update_prev").val();
    if(selectorPrev.length > 0){
	var nbstyle = $("#current_stylesheet_nb").val();
	var nbrule = $("#current_stylesheet_nb_rule").val();
	blockAdminCSS.setCss(nbstyle, nbrule, selectorPrev + "{" + ($("#current_stylesheet_rules").val() || " ") + "}");
    }
    document.getElementById("typeofinput").value = "form";
    document.getElementById("current_stylesheet_rules").value = "";
    document.getElementById("current_stylesheet_nb_rule").value = "";
    $("#changecssformcode").removeClass("none");
            
    var styleSheets = ParsimonyAdmin.currentDocument.styleSheets;
    for (var i = 0; i < styleSheets.length; i++){
	if(styleSheets[i].href != null && !!styleSheets[i].href && styleSheets[i].href.match(new RegExp(filePath))){
	    $("#current_stylesheet_nb").val(i);
	    $.each(styleSheets[i].cssRules, function(nbrule) {
		if(this.selectorText == selector){
		    $("#current_stylesheet_nb_rule").val(nbrule);
		    $("#current_stylesheet_rules").val(styleSheets[i].cssRules[nbrule].style.cssText);
		}
	    });
	    if($("#current_stylesheet_nb_rule").val().length == 0){
		var nbRule = ParsimonyAdmin.currentDocument.styleSheets[i].cssRules.length;
		if(nbRule > 0) nbrule = ParsimonyAdmin.currentDocument.styleSheets[i].cssRules.length - 1;
		$("#current_stylesheet_nb_rule").val(nbRule);
	    }
	}
    }

    if($(selector,ParsimonyAdmin.currentBody).length == 1){
	$(selector,ParsimonyAdmin.currentBody).parsimonyDND('destroy');
	$(selector,ParsimonyAdmin.currentBody).parsimonyDND({
	    stopResizable : function(event, ui) {
		$("#form_css input[css=width]").val(ui.width() + "px");
		$("#form_css input[css=height]").val(ui.height() + "px");
		$("#form_css input[css=left]").val(ui.css('left'));
		$("#form_css input[css=top]").val(ui.css('top'));
	    },
	    stopDraggable: function(event, ui) {
		$("#form_css input[css=left]").val(ui.css('left'));
		$("#form_css input[css=top]").val(ui.css('top'));
	    }
	});
    }
}
blockAdminCSS.CSSeditor = function (id) {
    editor = CodeMirror(function(node) {
	document.getElementById(id).parentNode.insertBefore(node, document.getElementById(id).nextSibling);
    }, {
	mode:"text/css",
	value: document.getElementById(id).value,
	lineNumbers: false,
	gutter:true,
	autoClearEmptyLines:true,
	onGutterClick: function(c, n) {
	    var info = c.lineInfo(n);
	    if (info.lineClass == "barre"){
		c.setLineClass(n, "");
		c.clearMarker(n);
	    }else{
		c.setMarker(n, '<span class="activebtn">×</span>');
		c.setLineClass(n, "barre");
	    }
	    this.onChange(c);
	},
	onChange: function(c) {
	    var textarea = document.getElementById(c.id);
	    var nbstyle = textarea.getAttribute('data-nbstyle');
	    var nbrule = textarea.getAttribute('data-nbrule');
	    var selector = textarea.getAttribute('data-selector');
	    var code = selector + '{';
	    for(var i = 0;i < c.lineCount(); i++){
		if(c.lineInfo(i).lineClass != "barre") code += c.getLine(i);
	    }
	    blockAdminCSS.setCss(nbstyle, nbrule, code + "}");
	    textarea.value = c.getValue();
	}/*,,
	extraKeys: { 
	    "Ctrl-V": function(c, e) { console.dir(e); }
	    }
	onCursorActivity: function(c) {
	    var cursorLine = c.getCursor().line;
	    var lineCount = c.lineCount();
	    for(var i = 0;i < lineCount; i++){alert();
		if(c.getLine(i) == "" && cursorLine != i) c.removeLine(i);
		c.setLine(i,c.getLine(i).replace(/(\r\n|\n|\r)/gm,"").replace(";",';\r\n'));
		c.indentLine(i);
	    } 

	}*/
    });
    editor.id = id;
    blockAdminCSS.csseditors.push(editor);
}
blockAdminCSS.getCSSForCSSpicker = function () {
    var json = '[';
    this.openCSSCode();
    var elmt = $('.cssPicker',ParsimonyAdmin.currentBody).removeClass('cssPicker').get(0);
    var styleSheets = ParsimonyAdmin.currentDocument.styleSheets;
    for (var i = 0; i < styleSheets.length; i++){
	if(styleSheets[i].cssRules !== null && styleSheets[i].href != null && !!styleSheets[i].href && styleSheets[i].href.indexOf("iframe.css") == "-1" && styleSheets[i].href.indexOf("/" + window.location.host + BASE_PATH + "lib") == "-1"){
	    $.each(styleSheets[i].cssRules, function(nbrule) {
		if(elmt.webkitMatchesSelector(this.selectorText)){
		    var url = styleSheets[i].href.replace("http://" + window.location.host,"").substring(BASE_PATH.length);
		    blockAdminCSS.addSelectorCSS(url, this.selectorText, this.style.cssText.replace(/;[^a-z\-]/g, ";\n"), i , nbrule);
		    json += '{"nbstyle":"' + i + '","nbrule":"' + nbrule + '","url":"' + url + '","selector":"' + this.selectorText + '"},';
		}
	    });
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
    + '<input type="hidden" name="selectors[' + id + '][file]" value="' + url + '"><input type="hidden" name="selectors[' + id + '][selector]" value="' + selector + '">'
    + '<textarea  class="csscode" id="' + id + '" name="selectors[' + id + '][code]" data-nbstyle="' + nbstyle + '" data-nbrule="' + nbrule + '" data-selector="' + selector + '">' + styleCSS.replace(/;/,";\n").replace("\n\n","\n") + '</textarea>';
    $("#changecsscode").prepend(code);
    this.CSSeditor(id);
}
	
blockAdminCSS.openCSSForm = function () {
    $("#right_sidebar").removeClass("close");
    $("#changecssform").removeClass('none');
    $("#changecsscode").addClass('none');
    $("#switchtocode").removeClass('active');
    $("#switchtovisuel").addClass('active');
    $("#typeofinput").val("form");
    $('#css_panel').show();
    $("#goeditcss").hide();
}
blockAdminCSS.openCSSCode = function () {
    $("#right_sidebar").removeClass("close");
    $("#changecsscode").removeClass('none');
    $("#changecssform").addClass('none');
    $("#switchtovisuel").removeClass('active');
    $("#switchtocode").addClass('active');
    $("#typeofinput").val("code");
    $('#css_panel').show();
    $("#goeditcss").hide();
    $("#changecsscode").empty();
    $.each(blockAdminCSS.csseditors,function(i, el){
	blockAdminCSS.csseditors.splice(i,i+1);
    });
}
    

ParsimonyAdmin.setPlugin(new blockAdminCSS());