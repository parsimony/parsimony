function blockAdminBlocks() {
    
    this.startTypeCont = "theme";
    this.startIdParentBlock = "";
    this.stopIdParentBlock = "";
    this.idNextBlock = "";
    this.idBlock = "";
    this.blockType = "";
    this.moveBlock = false;
    this.dragLastDomId = "";
    this.dragMiddle = 0;
    this.blocks = [];

    this.initBefore = function () {
        $(document).on('mouseover',"#admin", function(e) {
            if(ParsimonyAdmin.moveBlock) ParsimonyAdmin.returnToShelter();
        });
	
        document.getElementById('admintoolbar').onselectstart = new Function ("return false");
    }
    
    this.initIframe = function () {
	
    }
    
    this.init = function () {
        var $this = this;
            
        //destruction of a block
        $(document).add('#config_tree_selector').on('click',".config_destroy",function(e){
            var blockInst = (typeof $this.blocks["block_" + this.classList[1]] != "undefined") ? $this.blocks["block_" + this.classList[1]] : $this.blocks['block'];
            blockInst.onDelete.apply(this, [e]);
        });
	
        //design a block
        $(document).add('#config_tree_selector').on('click',".cssblock",function(e){
            var blockInst = (typeof $this.blocks["block_" + this.classList[1]] != "undefined") ? $this.blocks["block_" + this.classList[1]] : $this.blocks['block'];
            blockInst.onDesign.apply(this, [e]);
        });
        
        //configure a block
        $(document).add('#config_tree_selector').on('click',".configure_block",function(e){
            var blockInst = (typeof $this.blocks["block_" + this.classList[1]] != "undefined") ? $this.blocks["block_" + this.classList[1]] : $this.blocks['block'];
            blockInst.onConfigure.apply(this, [e]);
        });

        /* HTML5 drag n drop*/
        $("#modulespages").on('dragstart',".admin_core_block", function( event ){
            var evt = event.originalEvent;
            evt.dataTransfer.setDragImage(document.getElementById($(this).attr("id")),15,15);
            evt.dataTransfer.setData("Text", $(this).attr("id"));
            evt.dataTransfer.setData('text/plain', "addBlock");
            evt.dataTransfer.effectAllowed = 'copy';
            $this.startDragging();
            $this.blockType = this.id;
        });
        $("#menu").add('#paneltree').on('dragstart',".move_block",function( event ){  
            var evt = event.originalEvent;
            var elmt = $("#" + ParsimonyAdmin.inProgress,ParsimonyAdmin.currentBody);
            var InProgressId = elmt.attr("id");
            evt.dataTransfer.setDragImage($("#" + InProgressId,ParsimonyAdmin.currentBody).get(0),15,15);
            evt.dataTransfer.setData("Text", InProgressId);
            evt.dataTransfer.setData('text/plain', "moveBlock");
            evt.dataTransfer.effectAllowed = 'copyMove';
            $this.startDragging();
            $this.startTypeCont = ParsimonyAdmin.whereIAm(ParsimonyAdmin.inProgress);
            if(elmt.parent().closest(".container").hasClass("container_page")) $this.startIdParentBlock = elmt.parent().closest(".container").data('page');
            else $this.startIdParentBlock = elmt.parent().closest(".container").attr('id');
            if($this.startIdParentBlock == 'content') $this.startIdParentBlock = $(".container_page",ParsimonyAdmin.currentBody).data('page');
            $this.idBlock = InProgressId;
        });
	
        $('#conf_box_content').on('click',"#dialog-ok",function(e){
            e.preventDefault();
            $this.idBlock = $("#dialog-id").val();
            if($this.idBlock != ''){
                if( $this.idNextBlock == '' || $this.stopIdParentBlock == '' || ParsimonyAdmin.whereIAm("dropInTree") == '') alert("stop");
                ParsimonyAdmin.changeBlockPosition($this.blockType,$this.idBlock,$this.idNextBlock,'',$this.stopIdParentBlock,'',ParsimonyAdmin.whereIAm("dropInTree"),"addBlock");
            }else{
                alert(t('Please enter your ID'));
            }
            ParsimonyAdmin.closeConfBox();
        });
	       
	
    }
    
    this.startDragging = function () {
        var $this = this;
        this.moveBlock = true;
        $(ParsimonyAdmin.currentBody).append($("#dropInPage" ));
        ParsimonyAdmin.openRightTreePanel(); 
        $(ParsimonyAdmin.currentBody).add('#paneltree').on('dragenter.drag','.block,#dropInTree,.tree_selector', function(e) {
            e.stopImmediatePropagation();
            //if(e.type == 'dragenter' || Math.floor ( Math.random() * 12 ) == 3) {
            var isContainer = false;
            if((this.classList.contains("container") && !this.classList.contains("tree_selector")) || this.id =='treedom_container') isContainer = true;
            if(e.type == 'dragenter' || ($this.dragLastDomId != this.id ||
                ( $this.dragMiddlePos == 1 && (e.originalEvent.pageY > $this.dragMiddle)) ||
                ( $this.dragMiddlePos == 0 && (e.originalEvent.pageY < $this.dragMiddle)))){
                var theBlock = this;
                if(this.classList.contains("tree_selector")) theBlock = $("#" + this.id.split("treedom_")[1],ParsimonyAdmin.currentBody).get(0);
                var theBlockTree = document.getElementById("treedom_" + theBlock.id);
                var dropInPage = $( "#dropInPage",ParsimonyAdmin.currentBody).get(0);
                $this.dragLastDomId = this.id;
                $this.dragMiddle = $(this).offset().top + this.offsetHeight/2;
                if(e.originalEvent.pageY < $this.dragMiddle && !isContainer){
                    $this.dragMiddlePos = 1;
                    $(theBlock).before(dropInPage);
                    theBlockTree.parentNode.insertBefore(document.getElementById( "dropInTree" ),theBlockTree);
                }else{
                    $this.dragMiddlePos = 0;
                    if(theBlock.classList.contains("container") && $(theBlock).children(".dropInContainer").length > 0){
                        $(".dropInContainerChild:first",theBlock).append(dropInPage);
                        theBlockTree.appendChild(document.getElementById( "dropInTree" ),theBlockTree);
                    }else if(theBlock.classList.contains("container") && !isContainer){
                        theBlock.parentNode.insertBefore(dropInPage,theBlock);
                        theBlockTree.parentNode.insertBefore(document.getElementById( "dropInTree" ),theBlockTree);
                    }else if(theBlock.parentNode.classList.contains("container") && !isContainer){
                        theBlock.parentNode.insertBefore(dropInPage,theBlock.nextSibling);
                        theBlockTree.parentNode.insertBefore(document.getElementById( "dropInTree" ),theBlockTree.nextSibling);
                    }
                }
            }
            dropInPage = theBlock = theBlockTree = null;
            return false;
        });
	
        $(ParsimonyAdmin.currentBody).add('#paneltree').on('dragover.drag','.block,#dropInTree,.tree_selector', function(e) {
            e.stopImmediatePropagation();
            return false;
        });


    }
    
    this.stopDragging = function () {
        $(ParsimonyAdmin.currentBody).add('#paneltree').off('.drag');
        this.moveBlock = false;
    }
    
    this.loadEditMode = function () {
        $this = this;
        
        $(ParsimonyAdmin.currentDocument).on('click.edit','.block',function(e){
            var blockInst = (typeof $this.blocks["block_" + this.classList[1]] != "undefined") ? $this.blocks["block_" + this.classList[1]] : $this.blocks['block'];
            blockInst.onClickEdit.apply(this, [e]);
        });
    }
	
    this.loadCreationMode = function () {
        $this = this;
	
        $(ParsimonyAdmin.currentBody).on('click.creation','.block',function(e){
            var blockInst = (typeof $this.blocks["block_" + this.classList[1]] != "undefined") ? $this.blocks["block_" + this.classList[1]] : $this.blocks['block'];
            blockInst.onClickCreation.apply(this, [e]);
        });
	
        $(ParsimonyAdmin.currentBody).on('mouseover.creation',".block", function(event) {
            event.stopImmediatePropagation();
            var offset = $(this).offset();
            var offsetFrame = $("#parsiframe").offset();
            if(ParsimonyAdmin.inProgress != this.id) $("#blockOverlay").css({
                "display":"block",
                "top":offset.top + "px",
                "left":offset.left + offsetFrame.left + "px",
                "width":$(this).outerWidth() + "px",
                "height":$(this).outerHeight() + "px"
            })
            else $("#blockOverlay").hide();
        });
	
        /* Hide overlay when user don't pick a block */
        $(document).on('mouseover.creation',"body", function(event) {
            $("#blockOverlay").hide();
        });
	
        $(ParsimonyAdmin.currentBody).add('#paneltree').on('dragover.creation dragenter.creation','.marqueurdragndrop', function(e) {
            e.stopImmediatePropagation();
            return false;
        });
	
        $(ParsimonyAdmin.currentBody).add('#paneltree').on('drop.creation','.container,.tree_selector',function( event ){
            $this.stopDragging();
            event.stopPropagation();
            var evt = event.originalEvent;
            evt.stopPropagation();
            var elmt = $( "#dropInPage" ,ParsimonyAdmin.currentBody);
            if(elmt.is(':visible')){
                if(elmt.closest(".container").hasClass("container_page")) $this.stopIdParentBlock = elmt.closest(".container").data('page');
                else $this.stopIdParentBlock = elmt.closest(".container").attr('id');
                $this.idNextBlock = elmt.next(".block").attr('id');
                if(evt.dataTransfer.getData('text/plain') == 'addBlock'){
                    ParsimonyAdmin.displayConfBox("#dialog","Entrez un identifiant pour ce nouveau bloc");
                    $("#dialog-id").val('').trigger("focus");
                }
                else if(evt.dataTransfer.getData('text/plain') == 'moveBlock'){
                    if($this.idBlock == '' || $this.idNextBlock == '' || $this.startIdParentBlock == '' || $this.stopIdParentBlock == '' || $this.startTypeCont == '' || ParsimonyAdmin.whereIAm("dropInTree") == '') alert("stop");
                    ParsimonyAdmin.changeBlockPosition('',$this.idBlock,$this.idNextBlock,$this.startIdParentBlock,$this.stopIdParentBlock,$this.startTypeCont,ParsimonyAdmin.whereIAm("dropInTree"),"moveBlock");
                }
            }
        });
    }
    
    this.setBlock = function (block) {
        this.blocks[block.name] = block;
    }

    
}


function block() {
    
    this.name = "block";

    this.onClickEdit = function (e) {
	e.stopPropagation();
    }
    
    this.onClickCreation = function (e) {
        e.stopPropagation();
        ParsimonyAdmin.selectBlock(this.id);

        if(e.trad != true && e.link != true) ParsimonyAdmin.closeParsiadminMenu();
        ParsimonyAdmin.addTitleParsiadminMenu('#' + ParsimonyAdmin.inProgress);
        ParsimonyAdmin.addOptionParsiadminMenu('<a href="#" class="configure_block" rel="getViewConfigBlock" title="Configuration"><span class="ui-icon ui-icon-wrench floatleft"></span>'+ t('Configure') +'</a>');
        ParsimonyAdmin.addOptionParsiadminMenu('<a href="#" class="cssblock"><span class="ui-icon ui-icon-pencil floatleft"></span>'+ t('Design') +'</a>');
        if(this.id != "container") ParsimonyAdmin.addOptionParsiadminMenu('<a href="#" draggable="true" class="move_block" style="cursor:move"><span class="ui-icon ui-icon-arrow-4 floatleft"></span>'+ t('Move') +'</a>');
        if(this.id != "container" && this.id != "content") ParsimonyAdmin.addOptionParsiadminMenu('<a href="#" class="config_destroy"><span class="ui-icon ui-icon-closethick floatleft"></span>'+ t('Delete') +'</a>');
        ParsimonyAdmin.openParsiadminMenu(e.pageX || ($(window).width()/2),e.pageY || ($(window).height()/2));
    }
    
    this.onConfigure = function () {
        var parentId = '';
        var inProgress = $("#treedom_" + ParsimonyAdmin.inProgress);
        if(inProgress.length > 0){
            if(inProgress.parent().closest(".container").attr("id") == "treedom_content") parentId = inProgress.parent().closest("#treedom_content").data('page');
            else parentId = inProgress.parent().closest(".container").attr('id').replace("treedom_","");
        }
        ParsimonyAdmin.displayConfBox(BASE_PATH + "admin/action",$(this).attr('title'),"TOKEN=" + TOKEN + "&idBlock=" + ParsimonyAdmin.inProgress + "&parentBlock=" + parentId + "&typeProgress=" + ParsimonyAdmin.typeProgress + "&action=" + $(this).attr('rel') +"&IDPage=" + $(".container_page",ParsimonyAdmin.currentBody).data('page'));
    }
    
    this.onDesign = function (e) {
        e.preventDefault();
        var filePath = CSSTHEMEPATH;
        if(ParsimonyAdmin.whereIAm(ParsimonyAdmin.inProgress)=='page') filePath = CSSPAGEPATH;
        blockAdminCSS.displayCSSConf(filePath, "#" + ParsimonyAdmin.inProgress);
    }
    
    this.onCreate = function () {
	
    }
    
    this.onDelete = function (e) {
        ParsimonyAdmin.destroyBlock();
    }
    
    this.onSaveConfig = function () {
	
    }
    
}