function blockAdminCSS() {

    this.currentIdStylesheet = 0;
    this.currentIdRule = 0;
    this.currentIdMedia = 0;
    this.currentMediaText = "";
    this.currentRule;
    this.currentFile;

    
    this.loadCreationMode = function () {
	
	/* Init visual tool (drag 'n drop/resize/move block) */
	
	/* Manage Visual tool for Deag 'n drop */
	$(".parsimonyMove").on("mousedown.creation",function(e){
		e.stopImmediatePropagation();
		document.getElementById("overlays").style.pointerEvents = "all";  
		var dndstart = {
		    left : isNaN(parseFloat(blockAdminCSS.currentRule.style.left)) ? 0 : blockAdminCSS.currentRule.style.left,
		    top : isNaN(parseFloat(blockAdminCSS.currentRule.style.top)) ? 0 : blockAdminCSS.currentRule.style.top,
		    pageX : e.pageX,
		    pageY : e.pageY,
		    rule : blockAdminCSS.currentRule
		};
		
		$(document).add(ParsimonyAdmin.currentDocument).on("mousemove.parsimonyDND",dndstart,function(e){
		    dndstart.rule.style.left = parseFloat(dndstart.left) + e.pageX - dndstart.pageX + "px";
		    dndstart.rule.style.top = parseFloat(dndstart.top) + e.pageY - dndstart.pageY + "px";
		    document.getElementById("box_top").value = dndstart.rule.style.top || "";
		    document.getElementById("box_left").value = dndstart.rule.style.left || "";
		    blockAdminCSS.updatePosition(ParsimonyAdmin.currentDocument.getElementById(ParsimonyAdmin.inProgress).getBoundingClientRect());
		})
		.on("mouseup.parsimonyDND",dndstart,function(e){
		    document.getElementById("overlays").style.pointerEvents = "none";
		    $("#box_top").val(dndstart.rule.style.top !== 'auto' ? dndstart.rule.style.top  : '').trigger("change");
		    $("#box_left").val(dndstart.rule.style.left !== 'auto' ? dndstart.rule.style.left : '').trigger("change");
		    $(document).add(ParsimonyAdmin.currentDocument).off("mousemove").off("mouseup");
                    blockAdminCSS.checkChanges();
		});
	    });

	    /* Manage Visual tool for Resize */
	    $(".parsimonyResize").on("mousedown.creation",function(e){
		e.stopImmediatePropagation();
		document.getElementById("overlays").style.pointerEvents = "all";
		var DNDiframe = ParsimonyAdmin.currentDocument.getElementById(ParsimonyAdmin.inProgress);
		var bounds = DNDiframe.getBoundingClientRect();
		var dndstart = {
		    DNDiframe : DNDiframe,
		    width : bounds.width,
		    height : bounds.height,
		    left : isNaN(parseInt(blockAdminCSS.currentRule.style.left)) ? 0 : blockAdminCSS.currentRule.style.left,
		    top : isNaN(parseInt(blockAdminCSS.currentRule.style.top)) ? 0 : blockAdminCSS.currentRule.style.top,
		    pageX : e.pageX,
		    pageY : e.pageY,
		    dir : this.getAttribute('class').replace("parsimonyResize ", ""),
		    rule : blockAdminCSS.currentRule
		};

		$(document).add(ParsimonyAdmin.currentDocument).on("mousemove.parsimonyDND",dndstart,function(e){
		    switch(dndstart.dir){
			case "se":
			    dndstart.rule.style.width = parseInt(dndstart.width) + (e.pageX - dndstart.pageX) + "px";
			    dndstart.rule.style.height = parseInt(dndstart.height) + (e.pageY - dndstart.pageY) + "px";
			    break;
			case "nw":
			    dndstart.rule.style.top = parseInt(dndstart.top) + (e.pageY - dndstart.pageY) + "px";
			    dndstart.rule.style.left = parseInt(dndstart.left) + (e.pageX - dndstart.pageX) + "px";
			    dndstart.rule.style.width = parseInt(dndstart.width) - (e.pageX - dndstart.pageX) + "px";
			    dndstart.rule.style.height = parseInt(dndstart.height) - (e.pageY - dndstart.pageY) + "px";
			    break;
			case "ne":
			    dndstart.rule.style.top = parseInt(dndstart.top) + (e.pageY - dndstart.pageY) + "px";
			    dndstart.rule.style.width = parseInt(dndstart.width) + (e.pageX - dndstart.pageX) + "px";
			    dndstart.rule.style.height = parseInt(dndstart.height) - (e.pageY - dndstart.pageY) + "px";
			    break;
			case "sw":
			    dndstart.rule.style.left = parseInt(dndstart.left) + (e.pageX - dndstart.pageX) + "px";
			    dndstart.rule.style.width = parseInt(dndstart.width) - (e.pageX - dndstart.pageX) + "px";
			    dndstart.rule.style.height = parseInt(dndstart.height) + (e.pageY - dndstart.pageY) + "px";
			    break;
		    }
		    blockAdminCSS.updatePosition(dndstart.DNDiframe.getBoundingClientRect());
		    document.getElementById("box_width").value = dndstart.rule.style.width;
		    document.getElementById("box_height").value = dndstart.rule.style.height;
		    document.getElementById("box_top").value = dndstart.rule.style.top;
		    document.getElementById("box_left").value = dndstart.rule.style.left;
		})
		.on("mouseup",dndstart,function(e){
		    document.getElementById("overlays").style.pointerEvents = "none";
		    $("#box_width").val(dndstart.rule.style.width !== 'auto' ? dndstart.rule.style.width : '').trigger("change");
		    $("#box_height").val(dndstart.rule.style.height !== 'auto' ? dndstart.rule.style.height : '').trigger("change");
		    $("#box_top").val(dndstart.rule.style.top !== 'auto' ? dndstart.rule.style.top  : '').trigger("change");
		    $("#box_left").val(dndstart.rule.style.left !== 'auto' ? dndstart.rule.style.left : '').trigger("change");
		    $(document).add(ParsimonyAdmin.currentDocument).off("mousemove").off("mouseup");
                    blockAdminCSS.checkChanges();
		});
	    });
	

        $("#panelcss").on("blur.creation", ".csscode", function(e) {
	    blockAdminCSS.checkChanges();
	})
        /* CSSLight */
        .on("click.creation", ".CSSLighthider", function(e) {
            var line = this.parentNode.parentNode;
            var lineNb = line.dataset.line;
            var texta = line.parentNode.previousSibling;
            texta.focus(); //to init
            var id = texta.id;
	    if(line.classList.contains("CSSLightbarre")){
                line.classList.remove('CSSLightbarre');
                delete blockAdminCSS.codeEditors[id].rm[lineNb];
            }else{
                line.classList.add('CSSLightbarre');
                blockAdminCSS.codeEditors[id].rm[lineNb] = lineNb;
            }
            blockAdminCSS.codeEditors[id].draw();
	})
	
	/* Init current CSS rule editor focus */
	.on("focus.creation", ".csscode", function(e) {
	    blockAdminCSS.setCurrentRule(this.dataset.nbstyle, this.dataset.nbrule, this.dataset.nbmedia);
	})
        
        /* Init current CSS rule editor focus */
	.on("change.creation", "#currentMdq", function(e) {
	    document.getElementById("css_panel").style.display = 'none';
	})
		
	/* Save changes */
	.on("click.creation", "#savemycss", function(e) {
	    e.preventDefault();
	    $.post(BASE_PATH + "admin/saveCSS", {
		changes: ParsimonyAdmin.CSSValuesChanges
	    },function(data) {
		ParsimonyAdmin.execResult(data);
	    });
            blockAdminCSS.checkChanges();
	})

	/* Reinit changes */
	.on("click.creation","#reinitcss", function(e) {
	    e.preventDefault();
	    for(var file in ParsimonyAdmin.CSSValuesChanges){
		for(var key in ParsimonyAdmin.CSSValuesChanges[file]){
		    var selector = ParsimonyAdmin.CSSValuesChanges[file][key];
		    var selectors = blockAdminCSS.findSelectorsByElement(selector.selector, selector.media);
		    var selectorName = selector.media.replace(/\s+/g, "")  + selector.selector;
                    if(typeof selectors[file] !== "undefined"){
                        var infos = selectors[file][selectorName];
                        if(infos){
                             var oldValue = "";
                            if(typeof ParsimonyAdmin.CSSValues[file][selectorName] !== "undefined"){
                                oldValue = ParsimonyAdmin.CSSValues[file][selectorName].p 
                            }
                            blockAdminCSS.setCurrentRule(infos.nbStylesheet, infos.nbRule, infos.nbMedia);
                            blockAdminCSS.setCss(blockAdminCSS.currentRule, oldValue);
                            var editor = document.getElementById('idcss' + infos.nbStylesheet + "_" + infos.nbRule + "_" + infos.nbMedia);
                            if(editor) editor.value = oldValue;
                        }
                    }
		    delete ParsimonyAdmin.CSSValuesChanges[file][key];
		}
	    }
            blockAdminCSS.checkChanges();
            document.getElementById('css_panel').style.display = 'none';
            blockAdminCSS.updatePosition(ParsimonyAdmin.currentDocument.getElementById(ParsimonyAdmin.inProgress).getBoundingClientRect());
	})
	
	/* Allow to add media query shorthand in dropdown */
	.on("click.creation", "#addMdq", function(e) {
	    var media = blockAdminCSS.addMediaQueries($("#mdqMinWidthValue").val(), $("#mdqMaxWidthValue").val());
	    $("#currentMdq").val(media);
	})

	/* Manage CSS updates with visual forms */
	.on("keyup.creation change.creation",".liveconfig", function(){
	    
	    /* Get the CSS property in camelCase notation( usefull for firefox) */
            var jsProp = this.dataset.css.replace(/-([a-z])/g, function(s, t) {
                return t.toUpperCase();
            });
            /* Set style in current CSS rule */
            if(this.value.length > 0){
                blockAdminCSS.currentRule.style[jsProp] = this.value;
            }else{
                blockAdminCSS.currentRule.style[jsProp] = "";
            }
	    
	    /* Update position of visual tool */
            blockAdminCSS.updatePosition(ParsimonyAdmin.currentDocument.getElementById(ParsimonyAdmin.inProgress).getBoundingClientRect());
	    
	    /* Save change, we update from our old CSS string ( from CSSValuesChanges obj ) because shorthand propeties are exploded in CSS rules ( background => background-color..etc */
	    var currentSelector = document.getElementById("current_selector_update").value;
	    var oldCssText = "", newCssText;
            var oldSelector = ParsimonyAdmin.CSSValuesChanges[blockAdminCSS.currentFile][blockAdminCSS.currentMediaText.replace(/\s+/g, '') + currentSelector];
            if(typeof oldSelector !== "undefined") oldCssText = oldSelector.value.trim();
	    if(oldCssText.length == 0) oldCssText = this.dataset.css + ":" + this.value + ";";
            /* We check if property is already set */
            if(oldCssText.match(new RegExp("[; ]?" + this.dataset.css + "[^;]*"))){
                if(this.value.length > 0){
                    newCssText = oldCssText.replace(new RegExp("([; ]?)(" + this.dataset.css + "[^;]*)"), "$1" + this.dataset.css + ": " + this.value);
                }else{ /* if there is no value we delete property */
                    newCssText = oldCssText.replace(new RegExp("([; ]?)" + this.dataset.css + "[^;]*[;]?"), "$1");
                }
            }else{
               if(this.value.length > 0){
                   newCssText = oldCssText + (oldCssText.substring(oldCssText.length - 1) == ";" ? "" : ";" ) + this.dataset.css + ":" + this.value + ";";
               }else{
                   newCssText = oldCssText;
               }
            }
            
	    blockAdminCSS.setCssChange(blockAdminCSS.currentFile, currentSelector, newCssText, blockAdminCSS.currentMediaText);
        })
        .on("blur.creation",".liveconfig", function(){
            blockAdminCSS.checkChanges();
        })
	
        /* Explorer */
	.on('click.creation',".explorer",function(){
            window.callbackExplorerID = this.getAttribute("rel");
            window.callbackExplorer = function (file){
                $("#" + window.callbackExplorerID).val(file);
                $("#" + window.callbackExplorerID).trigger('change');
                window.callbackExplorer = function(file){return false;};
                ParsimonyAdmin.explorer.close();
            }
            ParsimonyAdmin.displayExplorer();
	})
        
        /* Tabs of visual panel css */
        .on('click.creation','#css_menu .cssTab',function(){
            document.querySelector("#css_menu .cssTab.active").classList.remove("active");
            this.classList.add("active");
            $(".panelcss_tab").addClass("hiddenTab");
            document.getElementById(this.getAttribute("rel")).classList.remove("hiddenTab");
        })
        
        /* Input selecto management : current_selector_update */
        .on("change.creation",'#changecsspath',function(){
            if($('#current_selector_update').val().length > 2) blockAdminCSS.displayCSSConf($('#changecsspath').val(),$('#current_selector_update').val());
        })
        .on("click",'#goeditcss',function(){
            var selector = $('#current_selector_update').val();
            var path = $('#changecsspath').val();
            if($("#panelcss").hasClass('CSSCode')) {
                blockAdminCSS.openCSSCode();
                blockAdminCSS.addNewSelectorCSS( path, selector);
            }else{
                blockAdminCSS.displayCSSConf(path,selector);
            }
        })
        .on("keypress.creation",'#current_selector_update',function(e){
            var code = e.keyCode || e.which; 
            if(code === 13) {
                $("#goeditcss").trigger("click");
            }
        })
        .on('keyup.creation keydown.creation',"#current_selector_update", function(event) {
            event.stopPropagation();
            var code = event.keyCode || event.which; 
            if(code !== 13) {
                if (event.type === 'keyup') {
                    try{ /* In case of bad selector */
                         $('.cssPicker',ParsimonyAdmin.currentDocument).removeClass('cssPicker');
                        $(this.value,ParsimonyAdmin.currentDocument).addClass('cssPicker');
                    }catch(E){}
                }
                $("#panelcss").addClass("CSSSearch");
            }
            document.getElementById("css_panel").style.display = 'none';
        })
        
        /* Opacity visual tool */
        .on('change.creation','#slider-range-max', function( ) {
            var val = this.value;
            if(val == 1) val = '';
            document.getElementById("positioning_opacity").value = val;
            trigger(document.getElementById("positioning_opacity"), "change");
        })
        
        /* Color picker */
        .on('click.creation','.colorpicker2, .colorpicker3',function(){
            if(this.classList.contains("colorpicker2")){
                currentColorPicker = $(this);
            }else{
                currentColorPicker = $(this).prev().focus();
            }
            picker.el.style.display = "block";
            picker.el.style.top = ($(this).offset().top) + 20 + "px";
            picker.el.style.left = ($(this).offset().left - 200) + "px";
        })
        .on('blur.creation','.colorpicker2',function(){
            picker.el.style.display = "none";
        })
        
        /* Representation schemas */
        .on('keyup.creation change.creation','.representation input:not(".resultcss")',function(){
            obj = $(this).closest('.representation')[0];
            var top = obj.querySelector('.repr_top');
            var right = obj.querySelector('.repr_right');
            var bottom = obj.querySelector('.repr_bottom');
            var left = obj.querySelector('.repr_left');
            var init =  obj.getAttribute("init");
            if(!top.value) top.value = init;
            if(!right.value) right.value = init;
            if(!bottom.value) bottom.value = init;
            if(!left.value) left.value = init;
            top = top.value;
            right = right.value;
            bottom = bottom.value;
            left = left.value;
            var result = "";

            if(top == bottom && top == right && top == left){
                result = top;
            }else if(right == left && top == bottom ){
                result = top + ' ' + right;
            }else if(right == left & top != bottom){
                result = top + ' ' + right + ' ' + bottom;
            } else{
                result = top + ' ' + right + ' ' + bottom + ' ' + left;
            }

            obj.querySelector('.resultcss').value = result;
            trigger(obj.querySelector('.resultcss'), "change");
        })
        .on('keyup.creation change.creation','.resultcss',function(){
            obj = $(this).closest('.representation')[0];
            var top = obj.querySelector('.repr_top');
            var right = obj.querySelector('.repr_right');
            var bottom = obj.querySelector('.repr_bottom');
            var left = obj.querySelector('.repr_left');
            var expl = obj.querySelector('.resultcss').value.trim();
            if(expl.length > 0){
                expld = expl.split(' ');
                switch(expld.length){
                    case 1 : 
                        top.value = expl;
                        right.value = expl;
                        bottom.value = expl;
                        left.value = expl;
                        break;
                    case 2 :
                        top.value = expld[0];
                        right.value = expld[1];
                        bottom.value = expld[0];
                        left.value = expld[1];
                        break;
                    case 3 :
                        top.value = expld[0];
                        right.value = expld[1];
                        bottom.value = expld[2];
                        left.value = expld[1];
                        break;
                    case 4 :
                        top.value = expld[0];
                        right.value = expld[1];
                        bottom.value = expld[2];
                        left.value = expld[3];
                        break;
                }
            }
        })
        
        /* Spinner */
        .on("keydown", ".spinner", function (event) {
            if (event.keyCode == 40 || event.keyCode == 38) {
                event.preventDefault();
                var num = this.value;
                if(num!='') num = parseInt(num);
                else num = 0;
                var text = this.value.replace(num,'') || this.dataset.sufix || "";
                if (event.keyCode == 40) {
                    this.value = (num - 1) + text;
                } else if (event.keyCode == 38) {
                    this.value = (num + 1) + text;
                }
                trigger(this, "change");
            }
        })
        
        /* Autocomlete */
        .on("click.creation", ".autocomplete", function(){
            /* We clear datalist */
            document.getElementById("parsidatalist").innerHTML = "";
            this.setAttribute("list","parsidatalist");
            if(this.id == "current_selector_update"){
                $.getJSON( "admin/getCSSSelectors?filePath=" + document.getElementById("changecsspath").value, function(data){
                    $.each( data, function(i, value){
                         options += '<option value="' + value + '" />';
                    });
                     document.getElementById("parsidatalist").innerHTML = options;
                });
            }else{
                var options = "";
                $.each( $.parseJSON(this.dataset.options), function(i, value){
                     options += '<option value="' + value + '" />';
                });
                document.getElementById("parsidatalist").innerHTML = options;
            }
        });
        
        /* Font text-decoration */
        $(".decoration").on("click.creation", ".optionDeco", function(e){
            this.classList.toggle("active");
            var deco = '';
            $('.active', e.delegateTarget).each(function(){
                deco = deco + this.dataset.val + ' ';
            });
            deco = deco.replace(/[\s]{2,}/g,' ');
            document.getElementById("css-decoration").value = deco;
            trigger(document.getElementById("css-decoration"), "change");
        })
        .on("change.creation",".prop_text-decoration",function(){
            var container = this.parentNode;
            var value = this.value.replace(/[\s]{2,}/g,' ');
            var values = value.split(" ");
            $('.active', container).each(function(){
                this.classList.remove("active");
            });
            for(var i = 0, len = values.length; i < len; i++){
                container.querySelector('[data-val="' + values[i] + '"]').classList.add("active");
            }
        });
        
        /* Font style */
        $(".fontstyle").on("click.creation", ".optionFontStyle", function(){
            var input = this.querySelector("input");
            if(this.classList.contains("active")){
                this.classList.remove("active");
                input.value = "";
            }else{
                this.classList.add("active");
                input.value = this.dataset.val;
            }
            trigger(input, "change");
        })
        .on("change.creation",".prop_font-weight, .prop_font-style",function(){
            var container = this.parentNode;
            if(this.value == ""){
                container.classList.remove("active");
            }else{
                container.classList.add("active");
            }
        });

        /* Font alignement */
        $(".alignement").on("click.creation", ".optionAlign", function(){
            var input = document.getElementById("text_align");
            if(this.classList.contains("active")){
                this.classList.remove("active");
                input.value = "";
            }else{
                var container = this.parentNode;
                if(container.querySelector(".active")){
                    container.querySelector(".active").classList.remove("active");
                }
                this.classList.add("active");
                input.value = this.dataset.val; 
            }
            
            trigger(input, "change");
        })
        .on("change.creation",".prop_text-align",function(){
            var container = this.parentNode;
            if(this.value == ""){
                if(container.querySelector(".active")){
                    container.querySelector(".active").classList.remove("active");
                }
            }else{
                container.querySelector('[data-val="' + this.value.toLowerCase() + '"]').classList.add("active");
            }
        });
        
        /* Background Tab */ 
        $("#panelcss_tab_background").on("change", ".ruleBack", function(){
            var back_im = document.querySelector(".prop_background-image").value;
            var color = document.querySelector(".prop_background-color").value;
            if(back_im.length > 0){
                back_im = "url(" + back_im + ") ";
            }else if(color.length == 0){
                back_im = " url(/parsimony_cms/admin/img/transparent.png) "
            }

            var size = document.querySelector(".prop_background-size").value;
            var back = color + ' ' + back_im + ' ' +
                       document.querySelector(".prop_background-repeat").value + ' ' +
                       document.querySelector(".prop_background-position").value + ' ' +
                       (size.length > 0 ? '/' + size + ' ' : ' ') +
                       document.querySelector(".prop_background-attachment").value + ' ' +
                       document.querySelector(".prop_background-origin").value + ' ' +
                       document.querySelector(".prop_background-clip").value;
            back = back.replace(/[\s]{2,}/g,' ');
            document.querySelector(".prop_background").value = back.replace(" url(/parsimony_cms/admin/img/transparent.png)", "");
            trigger(document.querySelector(".prop_background"), "change");
            document.getElementById("backTest").style.background = back;
        });

        $("#backTest").on("mousedown.creation", function(e){
            e.stopImmediatePropagation();
            document.getElementById("overlays").style.pointerEvents = "all";  
            var dndstart = {left: isNaN(parseInt(this.style.left))? 0 : parseInt(this.style.left),
                            top: isNaN(parseInt(this.style.top))? 0 :  parseInt(this.style.top),
                            pageX : e.pageX, pageY : e.pageY};

            $(document).on("mousemove.parsimonyDND", dndstart, function(e){
                var left = e.pageX - dndstart.pageX;
                var top = e.pageY - dndstart.pageY;
                document.querySelector(".prop_background-position").value = left + "px " + top + "px";
                document.getElementById("backTest").style.backgroundPosition = left + "px " + top + "px";
            })
            .on("mouseup.parsimonyDND",dndstart,function(e){
                trigger(document.querySelector(".prop_background-position"), "change");
                document.getElementById("overlays").style.pointerEvents = "none";
                $(document).off("mousemove").off("mouseup");
            });
        });
        
        /* shadowWidget init*/
        $('.shadowWidget').on("change", ".rulePart",function(e){
            var container = e.delegateTarget;
            var el = container.querySelector(".resultShadow");
            el.value = container.querySelector('.h-offset').value.trim() + ' ' + container.querySelector('.v-offset').value.trim() + ' ' + container.querySelector('.blurShadow').value.trim() + ' ' + container.querySelector('.colorShadow').value.trim();
            trigger(el, "change");
            container.querySelector(".pointer").style.left = parseInt(container.querySelector('.h-offset').value) + 16 + "px";
            container.querySelector(".pointer").style.top = parseInt(container.querySelector('.v-offset').value) + 16 + "px";
        })
        .on("change", ".resultShadow",function(e){
            var container = e.delegateTarget;
            var el = container.querySelector(".resultShadow");
            var res = el.value.split(" ");
            container.querySelector(".h-offset").value = res[0];
            container.querySelector(".v-offset").value = res[1];
            if(res[2]) container.querySelector(".blurShadow").value = res[2];
            if(res[3]) container.querySelector(".colorShadow").value = res[3];
        })
        .on("mousedown.creation", ".pointer",function(e){
            e.stopImmediatePropagation();
            document.getElementById("overlays").style.pointerEvents = "all";  
            var dndstart = {left: isNaN(parseInt(this.style.left))? 0 : parseInt(this.style.left) - 16,
                            top: isNaN(parseInt(this.style.top))? 0 :  parseInt(this.style.top) - 16,
                            pointer: this,
                            container : e.delegateTarget,
                            pageX : e.pageX, pageY : e.pageY};

            $(document).on("mousemove.parsimonyDND", dndstart, function(e){
                var left = e.pageX - dndstart.pageX;
                var top = e.pageY - dndstart.pageY;
                var left2 = 16 + left + dndstart.left;
                var top2 = 16 + top + dndstart.top;
                if(left2 > 32) left2 = 32;
                else if(left2 < 0) left2 = 0;
                if(top2 > 32) top2 = 32;
                else if(top2 < 0) top2 = 0; 
                dndstart.container.querySelector('.h-offset').value = left2 - 16 + "px";
                dndstart.container.querySelector('.v-offset').value = top2 - 16 + "px";
                trigger(dndstart.container.querySelector('.v-offset'), "change");
            })
            .on("mouseup.parsimonyDND",dndstart,function(e){
                document.getElementById("overlays").style.pointerEvents = "none";
                $(document).off("mousemove").off("mouseup");
            });
        });
        
        /* Borders */
        $('#panelcss_tab_border_general').on("click.creation", ".borderMarkers", function(e){
            e.delegateTarget.querySelector('.active').classList.remove("active");
            this.classList.add("active");
            trigger(e.delegateTarget.querySelector('.prop_' + this.dataset.targetcss), "change");
        })
        .on("change.creation", ".rulePart", function(e){
            var container = e.delegateTarget;
            var elmt = container.querySelector('.prop_' + container.querySelector('.active').dataset.targetcss);
            elmt.value = (container.querySelector('.borderWidth').value.trim() || "1px") + ' ' + (container.querySelector('.borderStyle').value.trim() || "solid" ) + ' ' + (container.querySelector('.borderColor').value.trim() || "#000000");
            trigger(elmt, "change");
        })
        .on("click.creation", ".clearBorder", function(e){
            var container = e.delegateTarget;
            var elmt = container.querySelector('.prop_' + container.querySelector('.active').dataset.targetcss);
            elmt.value = "";
            trigger(elmt, "change");
        })
        .on("change.creation", ".liveconfig", function(e){
            var container = e.delegateTarget;
            var value = this.value.trim();
            if(value.length > 0){
                var cut = value.split(" ");
                for(var i = 0, len = cut.length; i < len ; i++){
                    cut[i] = cut[i].trim();
                    if(cut[i].substring(0,1) == "#"){
                        container.querySelector('.borderColor').value = cut[i];
                    }else if(",none,solid,dashed,dotted,double,groove,ridge,inset,outset,".indexOf(cut[i]) != -1){
                        container.querySelector('.borderStyle').value = cut[i];
                    }else{
                        container.querySelector('.borderWidth').value = cut[i];
                    }
                }
                container.querySelector('.borderMarkers[data-targetcss="' + this.dataset.css + '"]').classList.add("modifiedBorder");
            }else{
                container.querySelector('.borderWidth').value = "";
                container.querySelector('.borderColor').value = "";
                container.querySelector('.borderStyle').value = "";
                container.querySelector('.borderMarkers[data-targetcss="' + this.dataset.css + '"]').classList.remove("modifiedBorder");
            }
        });
	
	/* Manage CSSpicker */
	$("#right_sidebar").on('click.creation',".cssPickerBTN", function(e){
	    e.preventDefault();
	    e.stopPropagation();
            $("#threed").show();
	    function destroyCSSpicker(){
		$('#container',ParsimonyAdmin.currentBody).off(".csspicker");
                $("#rotatex,#rotatey").val(0);
                $("#rotatez").val(300);
		$(".cssPickerBTN").removeClass("active");
                $('.cssPicker',ParsimonyAdmin.currentDocument).removeClass('cssPicker');
	    }
	    if($(this).hasClass("active")){
		destroyCSSpicker();
		return false;
	    }
	    ParsimonyAdmin.closeParsiadminMenu();
	    $('#container',ParsimonyAdmin.currentBody).on('mouseover.csspicker',"*", function(event) {
		event.stopPropagation();
		var elmt = ParsimonyAdmin.currentDocument.querySelector('.cssPicker');
		if(elmt) elmt.classList.remove("cssPicker");
                this.classList.add("cssPicker");
	    });
	    $(".cssPickerBTN").addClass("active");
	    $('#container',ParsimonyAdmin.currentBody).on('click.csspicker',"*",function(e){
		e.preventDefault();
		e.stopPropagation();
                $("#threed").hide();
                ParsimonyAdmin.$currentBody.css('transform','initial').css('-webkit-transform','initial').removeClass("threed");
		$('.cssPicker',ParsimonyAdmin.currentBody).removeClass("cssPicker");
		$(this).addClass("cssPicker");
		blockAdminCSS.openCSSCode();
		var proposals = [];
		if(this.id.length > 0 && $(".selectorcss[selector='#" + this.id + "']", $("#changecsscode")).length == 0) proposals.push("#" + this.id);
		var selectProp = "";
		var forbidClasses = ",selection-block,block,container,selection-container,parsieditinline,cssPicker,";
		$.each(this.classList, function(index, value) {
		    if($(".selectorcss[selector='." + value + "']").length == 0 && forbidClasses.indexOf("," + value + ",") == "-1"){
			proposals.push("." + value);
			selectProp = "." + value + " ";
		    }
		});
		var good = false;
		if(this.id == ""){
		    $(this).parentsUntil("body").each(function(){
			if(!good){
			    var selectid = "";
			    var selectclass = "";
			    if(this.getAttribute('id') != undefined) selectid = "#" + this.getAttribute('id');
			    else{
				$.each(this.classList, function(index, value) {
				    if(forbidClasses.indexOf("," + value + ",") == "-1") selectclass = "." + value;
				});
			    }
			    selectProp = selectid + selectclass + " " + selectProp;
			    if(selectid != "") good = true;
			}
		    });
		    selectProp = selectProp.replace(/\s+/g," ");
		    if($(".selectorcss[selector='" + selectProp + "']", $("#changecsscode")).length == 0) proposals.push(selectProp);
		}
		
                blockAdminCSS.getCSSSelectorForElement(this, proposals);
		destroyCSSpicker();
		return false;
	    });
	});
	
	/* Manage change CSS mode */
	$("#changecssformcode").on('click.creation','#switchtovisuel,#switchtocode',function(){
	    var currentSelector = document.getElementById("current_selector_update").value;
            if(this.id == 'switchtovisuel'){ /* switch to viual form */
                blockAdminCSS.openCSSForm();
            }else{ /* switch to code mode */
                blockAdminCSS.openCSSCode();
            }
            if(currentSelector != ""){
                 blockAdminCSS.displayCSSConf(document.getElementById("changecsspath").value, currentSelector);
            }else{
                document.getElementById("css_panel").style.display = 'none';
            }
        });

            
        /* CSSpicker 3D */
        $("#threed").on('change.creation','.ch',function(){
            var requestAnim = window.requestAnimationFrame || window.mozRequestAnimationFrame;
            requestAnim(function () {
                var x  = document.getElementById("rotatex").value;
                var y = document.getElementById("rotatey").value;
                var z = document.getElementById("rotatez").value;
                if(!ParsimonyAdmin.currentBody.classList.contains("threed")) ParsimonyAdmin.currentBody.classList.add("threed");
                var style = 'rotateX(' + (x/10) + 'deg) rotateY(' + (y/10) + 'deg) translateZ(' + z + 'px);box-shadow: '+ (-(y/10)) + 'px ' + (x/10) + 'px 3px #aaa;background-color:#fff';
                if(typeof ParsimonyAdmin.currentBody.style.transform != "undefined"){
                   ParsimonyAdmin.currentBody.style.transform = 'rotateX(' + x + 'deg) rotateY(' + y + 'deg) perspective(1000px)';
                   blockAdminCSS.iframeStyleSheet.deleteRule("0");
                   blockAdminCSS.iframeStyleSheet.insertRule('.threed * {transform:' + style + ';}',"0"); 
                }else{
                   ParsimonyAdmin.currentBody.style.webkitTransform = 'rotateX(' + x + 'deg) rotateY(' + y + 'deg) perspective(1000px)';
                   blockAdminCSS.iframeStyleSheet.deleteRule("0");
                   blockAdminCSS.iframeStyleSheet.insertRule('.threed * {-webkit-transform:' + style + ';}',"0"); 
                }
            });
        });
        
        blockAdminCSS.iframeStyleSheet = ParsimonyAdmin.currentDocument.styleSheets[ParsimonyAdmin.currentDocument.styleSheets.length-1];

	/* Shortcut : Save on CTRL+S */
	$(document).on("keydown.creation", function(e) {
	    if (e.keyCode === 83 && e.ctrlKey) {
	      e.preventDefault();
	      $("#savemycss").trigger("click");
	    }
	});
	
    }
    
    this.unloadCreationMode = function(){
	$("#panelcss").off('.creation', "**");
	$("#colorjack_square").hide();
	$(document).add(ParsimonyAdmin.currentDocument).off(".parsimonyDND");
        $('#container',ParsimonyAdmin.currentBody).off(".csspicker");
	$(".parsimonyMove").off(".creation");
	$(".parsimonyResize").off(".creation");
        $("#changecssformcode").off(".creation");
        $("#right_sidebar").off(".creation");
        $(".decoration").off(".creation");
        $("#panelcss_tab_background").off(".creation");
        $("#backTest").off(".creation");
        $("#threed").off(".creation");
        $(document).off(".creation");
	$("#parsimonyDND").hide();
    }
}

blockAdminCSS.codeEditors = [];

blockAdminCSS.setCss = function (rule, code) {
    rule.style.cssText = code;
}

blockAdminCSS.setCurrentRule = function (idStyle, idRule, idMedia) {
    if(idMedia == ""){
	this.currentRule = ParsimonyAdmin.currentDocument.styleSheets[idStyle].cssRules[idRule];
    }else{
	this.currentRule = ParsimonyAdmin.currentDocument.styleSheets[idStyle].cssRules[idMedia].cssRules[idRule];
    }
}

blockAdminCSS.checkChanges = function () {
    var cpt = 0;
    for(var file in ParsimonyAdmin.CSSValuesChanges){
        for(var key in ParsimonyAdmin.CSSValuesChanges[file]){
            cpt++;
        }
    }
    document.getElementById("nbChanges").textContent = " " + cpt + " changes";
    if(cpt > 0) document.getElementById("toolChanges").classList.add('toolactive');
    else document.getElementById("toolChanges").classList.remove('toolactive');
}

blockAdminCSS.updatePosition  = function (bounds) {
    var DNDadmin = document.getElementById("parsimonyDND");
    DNDadmin.style.cssText = "display:block;top:" + bounds.top + "px;left:" + (bounds.left + ParsimonyAdmin.iframe.offsetLeft + 40) + "px;width:" + bounds.width + "px;height:" + bounds.height + "px";
}

/* Keep CSS changes in an object */
blockAdminCSS.setCssChange = function (path, selector, value, media) {
    if(typeof ParsimonyAdmin.CSSValuesChanges[path] == "undefined") ParsimonyAdmin.CSSValuesChanges[path] = {}
    ParsimonyAdmin.CSSValuesChanges[path][media.replace(/\s+/g, '') + selector] = {"value" : value, "selector": selector, "media" : media};
}
    
blockAdminCSS.displayCSSConf = function (filePath, selector) {
    
    /* Clean form, but keep media query setting */
    var media = document.getElementById("currentMdq").value;
    document.getElementById("form_css").reset(); // reset all input except inputs hidden
    $(".modifiedBorder").removeClass("modifiedBorder"); // clean borders visual tools
    document.getElementById("currentMdq").value = media;
    
    /* Get existing code, last changes in priority */
    var code = "";
    var ident = media.replace(/\s+/g, '') + selector;
    var CSSRule = ParsimonyAdmin.CSSValues[filePath][media + selector];
    if(typeof CSSRule != "undefined" && typeof CSSRule["p"] != "undefined"){
	code = CSSRule["p"].trim();
    }
    var CSSRuleChanges = ParsimonyAdmin.CSSValuesChanges[filePath];
    if(typeof CSSRuleChanges == "undefined" || typeof CSSRuleChanges[ident] == "undefined" ) blockAdminCSS.setCssChange(filePath, selector, code, media);
    CSSRuleChanges = ParsimonyAdmin.CSSValuesChanges[filePath][ident];
    code = CSSRuleChanges.value.trim();

    /* Init form */
    document.getElementById("current_selector_update").value = selector;
    document.getElementById("changecsspath").value = filePath;
    
    /* Init vars to locate CSS rule : this.currentIdStylesheet, this.currentIdRule, this.currentIdMedia */
    blockAdminCSS.mapSelectorWithStylesheet(filePath, selector);

    if(document.getElementById("panelcss").classList.contains('CSSCode')) {
        blockAdminCSS.openCSSCode();
	/* If code mode is enable */
	$("#changecsscode").empty();
	blockAdminCSS.addSelectorCSS(filePath, selector, code.replace(/;[^a-zA-Z\-]+/gm, ";\n"), this.currentIdStylesheet , this.currentIdRule, this.currentMediaText, this.currentIdMedia);

    }else{
        blockAdminCSS.openCSSForm();
	/* If visual mode is enable, we fill the visual CSS Form  */
	if(code.length > 0){
	    var properties = code.split(";");
	    if(Array.isArray(properties)){
		properties.forEach(function(item){
		    var properties = item.split(":");
		    if(Array.isArray(properties) && properties.length == 2){
                        var elmt = document.querySelector(".prop_" + properties[0].trim());
                        if(elmt){
                            elmt.value = properties[1].trim();
                            trigger(elmt, "change");
                        }
		    }
		});
	    }else{
		var property = code.split(":");
		if(Array.isArray(property) && property.length == 2){
                    var elmt = document.querySelector(".prop_" + property[0].trim());
                    if(elmt){
                        elmt.value = property[1].trim();
                        trigger(elmt, "change");
                    }
		}
	    }
	}
    }
    document.getElementById("panelcss").classList.remove('CSSSearch')
}

blockAdminCSS.mapSelectorWithStylesheet = function (path, selector) {
    /* Search for an existing cssRule to update it in the future */
    var matches = blockAdminCSS.findSelectorsByElement(selector);
    var file = matches[path];
    
    /* By default */
    var media = document.getElementById("currentMdq").value;
    this.currentFile = path;
    this.currentIdStylesheet = 0;
    this.currentIdRule = ParsimonyAdmin.currentDocument.styleSheets[0].cssRules.length;
    this.currentIdMedia = "";
    this.currentMediaText = "";
    
    if(typeof file != "undefined" && typeof file.nbStylesheet != "undefined"){
	/* By default if we found the stylesheet of theme */
	this.currentIdStylesheet = file.nbStylesheet;
	this.currentIdRule = file.nbRules;
	var rule = file[media.replace(/\s+/g, '') + selector];
	if(typeof rule != "undefined"){
	    /* If we found the good rule */
	    this.currentIdStylesheet = rule.nbStylesheet;
	    this.currentIdRule = rule.nbRule;
	    this.currentIdMedia = rule.nbMedia;
	    this.currentMediaText = rule.media;
	    blockAdminCSS.setCurrentRule(this.currentIdStylesheet, this.currentIdRule, this.currentIdMedia);
	    return true;
	}
    }
    /* If CSS rule doesn't exists we create it, If it's a media we wrap rule with media declaration */
    
    if(media.length > 0){
	ParsimonyAdmin.currentDocument.styleSheets[this.currentIdStylesheet].insertRule(media + " { " + selector + " { } }", this.currentIdRule);
	this.currentIdMedia = this.currentIdRule;
	this.currentMediaText = media;
	this.currentIdRule = 0;
    }else{
	ParsimonyAdmin.currentDocument.styleSheets[this.currentIdStylesheet].insertRule(selector + "{}", this.currentIdRule);
    }

    blockAdminCSS.setCurrentRule(this.currentIdStylesheet, this.currentIdRule, this.currentIdMedia);
}

blockAdminCSS.findSelectorsByElement = function (elmt, mediaFilter) {
    document.getElementById("toolChanges").style.display = "block";
    var matchesSelector = (document.documentElement.webkitMatchesSelector || document.documentElement.mozMatchesSelector || document.documentElement.matchesSelector);
    var result = {};
    var styleSheets = ParsimonyAdmin.currentDocument.styleSheets;
    var url = "";
    for (var i = 0; i < styleSheets.length; i++){
	if(styleSheets[i].cssRules !== null && styleSheets[i].href != null && !!styleSheets[i].href && styleSheets[i].href.indexOf("iframe.css") == "-1"){
            url = styleSheets[i].href;
	    /* If file is not the concat file we clean the path */
	    if(url.indexOf("/cssconcat_") == "-1"){
		url = url.replace("http://" + window.location.host,"").substring(BASE_PATH.length);
		result[url] = {"nbStylesheet" : i, "nbRules" : styleSheets[i].cssRules.length};
	    }
	    var nbRules = styleSheets[i].cssRules.length;
	    for(var j = 0; j < nbRules; j++) {
		var media = "";
                var rule = styleSheets[i].cssRules[j];
		/* If it's a media rule we check each style rule */
		if(typeof rule.media != "undefined"){
		    media = "@media " + rule.media.mediaText;
		    
		    /* Add this media queries in shorthand list */
		    media2 = media.replace(/\s+/g,"");
		    blockAdminCSS.addMediaQueries((media2.match(/\min-width:[0-9]+/) || "").toString().replace("min-width:", ""),
		    (media2.match(/\max-width:[0-9]+/) || "").toString().replace("max-width:", ""));
		    
		    for(var h = 0; h < rule.cssRules.length; h++) {
			mediaRule = styleSheets[i].cssRules[j].cssRules[h];
			try{
			    if(typeof elmt == 'string' ? (elmt == mediaRule.selectorText) : matchesSelector.call(elmt,mediaRule.selectorText)){
				/* if we search for a media we check */ 
				if(mediaFilter == media || typeof mediaFilter == "undefined"){
				    result[url][media2 + mediaRule.selectorText] = {"nbStylesheet" : i, "nbRule" : h, "nbMedia" : j, "selector" : mediaRule.selectorText, "media" : media };
				}
			    }
			}catch(Error){}
		    }
	        }else{ /* if it's a style rule */
		    /* If source map tell us we parse another CSS file */
		    if(rule.selectorText == '.parsimonyMarker') {
			/* We set last id for CSS file just before*/
			if(j > 0) {
			    result[url]["nbStylesheet"] =  i;
			    result[url]["nbRules"] =  j;
			}
			/* We init the next CSS file */
			url = rule.style['backgroundImage'].replace(/"/g,"").split('url(')[1].replace("http://" + window.location.host + BASE_PATH,"").split(')')[0];
                        result[url] = {"nbStylesheet" : i,"nbRules": styleSheets[i].cssRules.length - 1};
		    }else if(typeof mediaFilter == "undefined" || mediaFilter == ""){
			try{
			    if(typeof elmt == 'string' ? (elmt == rule.selectorText) : matchesSelector.call(elmt,rule.selectorText)){
				result[url][rule.selectorText] = {"nbStylesheet" : i, "nbRule" : j, "nbMedia" : "", "selector" : rule.selectorText, "media" : "" };
			    }
			}catch(Error){}
		    }
		}
	    }
	}
    }
    return result;
}

blockAdminCSS.getCSSSelectorForElement = function (elmt, proposals) {
    var matches = blockAdminCSS.findSelectorsByElement(elmt);
    var found = false;
    var delta = 0;
    var nbStylesheet, nbRules;
    for(var file in matches){
	/* We delete all selectors of iframe.css and lib/_*_.css */
	if(file.indexOf("iframe.css") == "-1" && file.indexOf("lib/") == "-1"){
            var selectors = matches[file];
	    for(var key2 in selectors){
		if(key2 != 'nbStylesheet' && key2 != 'nbRules'){
		    selectors[key2].filePath = file;
		    var selector = selectors[key2];
		    /* If a proposal already exists we remove it from proposal list */
		    if(file == CSSTHEMEPATH && proposals.indexOf(selector) > -1) delete proposals[proposals.indefOf(selector)];
		    blockAdminCSS.addSelectorCSS(file, selector.selector, "", selector.nbStylesheet , (selector.nbRule + delta), selector.media, selector.nbMedia);
		    found = true;
		}
	    }
	    if(file == CSSTHEMEPATH){
		nbStylesheet = selectors['nbStylesheet'];
		nbRules = selectors['nbRules'];
		
		 /* Add proposals */
		if(proposals.length > 0){
		    proposals.forEach(function(selector){
			nbRules++;
			blockAdminCSS.addSelectorCSS(CSSTHEMEPATH, selector, "", nbStylesheet, nbRules, "", "");
			ParsimonyAdmin.currentDocument.styleSheets[nbStylesheet].insertRule(selector + "{}",nbRules);
			delta++;
		    });
		}
	    }
	    delete selectors['nbStylesheet'];
	    delete selectors['nbRules'];
	}else{
	    delete matches[file];
	}
    }
    
    /* If a selector has been found, we get his cssText from server */
    if(found){
	$.post(BASE_PATH + "admin/getCSSSelectorsRules", {
	    matches: matches
	},function(data) {
	    $.each(data, function(i,item) {
		/* We set cssText value to selector's textarea */
                var id = 'idcss' + item.nbStylesheet + "_" + item.nbRule + "_" + item.nbMedia;
		document.getElementById(id).value = item.cssText;
                blockAdminCSS.codeEditors[id].draw(false);
		// Add new selectors to ParsimonyAdmin.CSSValues to save initial behavior
		if(typeof ParsimonyAdmin.CSSValues[item.filePath] == "undefined") ParsimonyAdmin.CSSValues[item.filePath] = {};
		ParsimonyAdmin.CSSValues[item.filePath][item.media.replace(/\s+/g, "") + item.selector] = item.CSSValues;
	    });
	});
    }
}

blockAdminCSS.addNewSelectorCSS = function (path, selector) {
    /* Get rule id, if not exist we create one */
    this.mapSelectorWithStylesheet(path, selector);
    
    var media = this.currentMediaText;
    if(media.length > 0){
	var media = document.getElementById("currentMdq").value;
    }
    /* Get code  */
    var code = this.currentRule.cssText.split("{")[1].split("}")[0];
    this.addSelectorCSS( path, selector, code, this.currentIdStylesheet, this.currentIdRule, media, this.currentIdMedia);
    this.setCss(this.currentRule, code);
}

blockAdminCSS.addSelectorCSS = function (url, selector, styleCSS, nbstyle, nbrule, media, nbmedia) {
    var id = 'idcss' + nbstyle + "_" + nbrule + "_" + nbmedia;
    var code = '<div class="selectorcss" title="' + url + '" selector="' + selector + '"><div class="selectorTitle"><b>' + selector + '</b> <small>in ' + url.replace(/^.*[\/\\]/g, '') + '</small></div><div class="gotoform" onclick="$(\'#panelcss\').removeClass(\'CSSCode\');blockAdminCSS.displayCSSConf(\'' + url + '\',\'' + selector + '\')"> '+ t('Visual') +' </div></div>';
    if(typeof media != "undefined" && media.toString().length > 0) code += '<div class="mediaQueriesTitle">' + media + '</div>';
    code += '<textarea class="csscode CSSLighttexta" id="' + id + '" spellcheck="false" name="selectors[' + id + '][code]" data-nbstyle="' + nbstyle + '" data-nbrule="' + nbrule + '" data-media="' + media + '" data-nbmedia="' + nbmedia + '" data-path="' + url + '" data-selector="' + encodeURIComponent(selector) + '">' + styleCSS.replace(/  /g," ").replace(/;/g,";\n").replace("\n\n","\n") + '</textarea>';
    $("#changecsscode").append(code);
    blockAdminCSS.codeEditors[id] = new CSSlight(document.getElementById(id));
}

blockAdminCSS.addMediaQueries = function (minWidth, maxWidth) {
    var media = "@media screen";
    var mediaText = [];
    if(minWidth.length > 0){
	media += " and (min-width: " + minWidth + "px)";
	mediaText.push("min-width: " + minWidth + "px");
    }
    if(maxWidth.length > 0){
	media += " and (max-width: " + maxWidth + "px)";
	mediaText.push("max-width: " + maxWidth + "px");
    }
    if(mediaText.length > 0 && $('#currentMdq option[value="' + media + '"]').length == 0){
	$("#currentMdq").append('<option value="' + media + '">' + mediaText.join(", ") + '</option>');
    }
    $(this).parent().parent().hide();
    return media;
}

blockAdminCSS.openCSSForm = function () {
    blockAdminCSS.openCSSPanel();
    document.getElementById("css_panel").style.display = 'block';
    $("#panelcss").removeClass("CSSCode CSSSearch").addClass("CSSForm").show();
    ParsimonyAdmin.setCookie("cssMode", "visual", 999);
}

blockAdminCSS.openCSSCode = function () {
    blockAdminCSS.openCSSPanel();
    document.getElementById("css_panel").style.display = 'block';
    $("#panelcss").removeClass("CSSForm CSSSearch").addClass("CSSCode");
    $("#changecsscode").empty();
    ParsimonyAdmin.setCookie("cssMode", "code", 999);
}

blockAdminCSS.openCSSPanel = function () {
    $("#right_sidebar .contenttab > .active").removeClass("active");
    $(".panelcss").addClass("active");
}
    

ParsimonyAdmin.setPlugin(new blockAdminCSS());


function CSSlight(elmt) {

    this.textarea = elmt;
    this.rm = [];

    var obj = document.createElement("div");
    obj.classList.add("CSSLightcontainer");
    var gutter = document.createElement("div");
    gutter.classList.add("CSSLightguttercss");
    this.hilight = document.createElement("div");
    this.hilight.classList.add("CSSLighthilight");

    obj = this.textarea.parentNode.insertBefore(obj,this.textarea);
    obj.appendChild(gutter);
    obj.appendChild(this.textarea);
    obj.appendChild(this.hilight);
    
    this.draw = function(change){
 
        var content = this.textarea.value;
        var highlighted = '';
        var gutter = '<span class="CSSLightgutter"><span class="CSSLighthider"></span></span>';
        var line = 2;
        var search = ":";
        var error = false;
        while(content.length > 0){
            var char = content.match(/[:;\n]/);
            if(char !== null){
                if(!error && char[0] !== search) error = true;
                switch(char[0]){
                    case "\n":
                        var search = ":";
                        var cont = content.substring(0, char.index).trim();
                        if(cont.length > 0){
                             error = true;
                             highlighted += '<span class="CSSLightother">' + content.substring(0, char.index) + '</span>';
                        }
                        highlighted += '</div><div class="CSSLightline' + (this.rm.indexOf(line.toString()) == "-1" ? "" : " CSSLightbarre") + '" data-line="' + line + '">' + gutter;
                        line++;
                        break;
                    case ":":
                        var search = ";";
                        highlighted += '<span class="' + (error ? "CSSLightother" : "CSSLightproperty") + '">' + content.substring(0, char.index) + '</span><span class="CSSLighttwopoints">:</span>';
                        break;
                    case ";":
                        var search = "\n";
                        highlighted += '<span class="' + (error ? "CSSLightother" : "CSSLightvalue") + '">' + content.substring(0, char.index) + '</span><span class="CSSLightcoma">;</span>';
                        break;
                }
                content = content.substring(char.index + 1);
            }else{
                highlighted += '<span class="CSSLightother">' + content.substring(0) + '</span>';
                content = "";
            }
        }
        line++;
        this.hilight.innerHTML = '<div class="CSSLightline' + (this.rm.indexOf("1") == "-1" ? "" : " CSSLightbarre") + '" data-line="1">' + gutter + highlighted + '</div>';
        this.textarea.style.height = this.hilight.getBoundingClientRect().height + "px";
        if(change != false){
            /* Preview changes on CSS code mode && save changes in a temp object */
            var value = this.textarea.value;
            if(this.rm.length > 0){
                var lines = value.split("\n");
                for(var key in this.rm){
                    if(typeof lines[key-1] != "undefined") lines[key-1] = "";
                }
                value = lines.join("\n");
            }

            blockAdminCSS.setCss(blockAdminCSS.currentRule, value);
            blockAdminCSS.setCssChange(this.textarea.dataset.path, decodeURIComponent(this.textarea.dataset.selector), this.textarea.value, this.textarea.dataset.media);
        }
    }

    this.rePos = function(){
        this.hilight.scrollLeft = this.scrollLeft;
        this.hilight.scrollTop = this.scrollTop;
    }
    
    this.draw(false);
    elmt.addEventListener("input", this.draw.bind(this), true);
    elmt.addEventListener("scroll", this.rePos, true);

};
