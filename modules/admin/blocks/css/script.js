function blockAdminCSS() {

    this.initBefore = function () {
	
	/* Open CSS Filepath*/
	$('#panelcss').on('click','#opencssfilepath',function(){
	    $('#opencssfilepath2').slideToggle();
	}); 
    }
    
    this.initIframe = function () {
	
	/* Find all CSS files */
	var styleSheets = ParsimonyAdmin.currentDocument.styleSheets;
	$("#changecsspath").html('');
	for (var i = 0; i < styleSheets.length; i++){
	    if(styleSheets[i].href && styleSheets[i].href.match(new RegExp("/" + window.location.host + "/")) && !styleSheets[i].href.match(new RegExp("/" + window.location.host + BASE_PATH + "lib")) && styleSheets[i].href != "http://" + window.location.host + BASE_PATH + 'admin/iframe.css' )
		$("#changecsspath").append("<option>" + styleSheets[i].href.replace("http://" + window.location.host,"").substring(BASE_PATH.length) + "</option>");
	}
    }
    
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
	
	
	$(".subSidebar").on('click',"#csspicker", function(e){
	    e.preventDefault();
	    e.stopPropagation();
	    ParsimonyAdmin.closeParsiadminMenu();
	    $("#blockOverlay").addClass("csspicker");
	    $('#container',ParsimonyAdmin.currentBody).on('mouseover.csspicker',"*", function(event) {
		event.stopPropagation();
		var offset = $(this).offset();
		var offsetFrame = $("#parsiframe").offset();
		$("#blockOverlay").css({
		    "display":"block",
		    "top":offset.top + offsetFrame.top + "px",
		    "left":offset.left + offsetFrame.left +  "px",
		    "width":$(this).outerWidth() + "px",
		    "height":$(this).outerHeight() + "px"
		});
	    });
	    $("#csspicker").addClass("active");
	    $('#container',ParsimonyAdmin.currentBody).on('click.csspicker',"*",function(e){
		e.preventDefault();
		e.stopPropagation();
		$(".cssPicker").removeClass("cssPicker");
		$(this).addClass("cssPicker");
		blockAdminCSS.getCSSForCSSpicker();
		var title = CSSTHEMEPATH;
		if(this.id != "" && $(".selectorcss[selector='#" + this.id + "']").length == 0) blockAdminCSS.addNewSelectorCSS( title, "#" + this.id)
		$.each($(this).attr('class').replace('  ',' ').split(' '), function(index, value) {
		    if(value.length > 0 && $(".selectorcss[selector='." + value + "']").length == 0 && value != "selection-block") blockAdminCSS.addNewSelectorCSS( title, "." + value);
		});
		var good = false;
		var selectProp = this.tagName.toLowerCase();
		if(this.id == ""){
		    if($(this).attr('class') != undefined && $(this).attr('class') != "") selectProp = selectProp + ("." + $(this).attr("class").replace(" ",".")).replace(".cssPicker","");
		    $(this).parentsUntil("body").each(function(){
			if(!good){
			    var selectid = "";
			    var selectclass = "";
			    if($(this).attr('id') != undefined) selectid = "#" + $(this).attr('id');
			    if($(this).attr('class') != undefined && $(this).attr('class') != "") selectclass = "." + $(this).attr("class").replace(" ",".");
			    selectProp = selectid + selectclass.replace("  ","").replace(".block","").replace(".wysiwyg","") + " " + selectProp;
			    if(selectid != "") good = true;
			}
		    });
		    blockAdminCSS.addNewSelectorCSS( title, selectProp);
		}
                
		$('#container',ParsimonyAdmin.currentBody).off(".csspicker");
		$("#csspicker").removeClass("active");
		$("#blockOverlay").removeClass("csspicker");
		return false;
	    });
	});
	
	$(document).add('#config_tree_selector').on('click',".cssblock",function(e){ 
	    e.preventDefault();
	    var filePath = CSSTHEMEPATH;
	    if(ParsimonyAdmin.whereIAm(ParsimonyAdmin.inProgress)=='page') filePath = CSSPAGEPATH;
	    blockAdminCSS.displayCSSConf(filePath, "#" + ParsimonyAdmin.inProgress);
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
    }else{
	ParsimonyAdmin.currentDocument.styleSheets[nbstyle].removeRule(nbrule);
    }
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
		$("#current_stylesheet_nb_rule").val(null);
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
	},
	onCursorActivity: function(c) {
	    var cursorLine = c.getCursor().line;
	    for(var i = 0;i < c.lineCount() - 1; i++){
		if(c.getLine(i) == "" && cursorLine != i) c.removeLine(i);
	    //editor.setLine(i,editor.getLine(i).replace(/([a-z\-]);([a-z\-])/g, "$1;"));
	    //editor.indentLine(i);
	    } 
	}
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
	if(styleSheets[i].cssRules !== null && styleSheets[i].href != null && !!styleSheets[i].href && !styleSheets[i].href.match(new RegExp("/" + window.location.host + BASE_PATH + "lib"))){
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
		nbrule = null;
	    }
	}
    }
    this.addSelectorCSS( path, selector, code, nbstyle, nbrule);
    this.setCss(nbstyle, nbrule, selector + "{" + code + "}");
}
blockAdminCSS.addSelectorCSS = function (url, selector, styleCSS, nbstyle, nbrule) {
    var id = 'idcss' + nbstyle + nbrule;
    var code = '<div class="selectorcss" title="' + url + '" selector="' + selector + '"><div style="text-shadow: 0px 1px 0px white;width:160px;word-break: break-all;"><b>' + selector + '</b> <small>in ' + url.replace(/^.*[\/\\]/g, '') + '</small></div><div class="gotoform" onclick="blockAdminCSS.displayCSSConf(\'' + url + '\',\'' + selector + '\')">'+ t('Go to form') +'</div></div>'
    + '<input type="hidden" name="selectors[' + id + '][file]" value="' + url + '"><input type="hidden" name="selectors[' + id + '][selector]" value="' + selector + '">'
    + '<textarea  class="csscode" id="' + id + '" name="selectors[' + id + '][code]" data-nbstyle="' + nbstyle + '" data-nbrule="' + nbrule + '" data-selector="' + selector + '">' + styleCSS.replace(/;/,";\n").replace("\n\n","\n") + '</textarea>';
    $("#changecsscode").prepend(code);
    this.CSSeditor(id);
}
	
blockAdminCSS.openCSSForm = function () {
    ParsimonyAdmin.openRightCSSPanel();
    $("#changecssform").removeClass('none');
    $("#changecsscode").addClass('none');
    $("#switchtocode").removeClass('active');
    $("#switchtovisuel").addClass('active');
    $("#typeofinput").val("form");
    $('#css_panel').show();
    $("#goeditcss").hide();
}
blockAdminCSS.openCSSCode = function () {
    ParsimonyAdmin.openRightCSSPanel();
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