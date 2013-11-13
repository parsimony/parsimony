<?php
/**
 * Parsimony
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@parsimony-cms.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Parsimony to newer
 * versions in the future. If you wish to customize Parsimony for your
 * needs please refer to http://www.parsimony.mobi for more information.
 *
 * @authors Julien Gras et Benoît Lorillot
 * @copyright  Julien Gras et Benoît Lorillot
 * @version  Release: 1.0
 * @category  Parsimony
 * @package admin
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/* TODO
 *   filter => images, video......
 *   variable pour savoir ou envoyer value ds le cadre du file picker
 *   ouvrir fichier dans editeir de code ou editeur image
 *   path par de depart du tree
 */
app::$request->page->addCSSFile('lib/cms.css');
app::$request->page->addCSSFile('lib/CodeMirror/lib/codemirror.css');
app::$request->page->addCSSFile('modules/admin/css/ui.css');
app::$request->page->addJSFile('lib/CodeMirror/lib/codemirror.js');
app::$request->page->addJSFile('lib/CodeMirror/addon/format/formatting.js');
app::$request->page->addJSFile('lib/upload/parsimonyUpload.js');
?>

<script src="<?php echo BASE_PATH; ?>lib/jquery/jquery-2.0.2.min.js"></script>
<?php echo app::$request->page->printInclusions() ?>

<style>
    body{min-width: 500px;background: #fff;-webkit-touch-callout: none;-webkit-user-select: none;-khtml-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;}
    #explorerWrap{background: #fff;font-family: arial, sans-serif;font-size: 13px;width: 100%;height:100%;display:table;}
    #explorer{display: table-cell;width: 200px;overflow-y: auto;height: 100%;border-right: 1px solid #D3D5DB;vertical-align: top;list-style: none;padding: 0;margin: 0;background: #f9f9f9;}
    #explorer ul{display:none;list-style: none;}
    #explorer li.file{display:none}
    #explorer ul{padding-left:20px}
    #explorer > ul li{display:none;}
    #explorer .icondir{background:url(<?php echo BASE_PATH; ?>admin/img/explorersprite.png) 5px -84px no-repeat;}
    #explorer li.dir{padding-left: 25px;line-height: 25px;cursor: pointer;color: #333;border: 1px solid transparent;}
    #explorer li.dir:hover,.explorer_file:hover,.explorer_file_selected {border: solid 1px #b8d6fb;box-shadow: inset 0 0 1px white;background-color: #ebf3fd;}
    #path{background: white;border-bottom: 1px solid #D3D5DB;padding: 5px;color: #333;line-height: 20px;}
    #rightPart{display:table-cell;position: relative;}
    #explorerfiles{min-width:465px;height:100%;overflow: hidden;}
    #tabs li {cursor: pointer;display: inline-block;margin-left : 1px;background: #BBB;line-height: 15px;height: 26px;margin-top: 5px;border-bottom: 0;}
    #tabs li > div {border-bottom: 0;padding: 5px 4px;color: rgb(255, 255, 255);}
    #tabs .active, #tabs li:hover {background: #F8F8F8;}
    #tabs .active > div, #tabs li:hover > div{color :#777;}
    #tabs {position: absolute;width: 100%;z-index: 99;border-bottom: 1px solid #D3D5DB;margin: 0;padding: 0;padding-left: 5px;}
    .panel {position: absolute;width: 100%;height: 100%;padding-top: 31px;box-sizing: border-box;}
    .CodeMirror {height: 100%;line-height: 17px;}
    #explorercontainer{position: absolute;height: 100%;width: 100%;}
    .close {padding: 0px 3px 1px 3px;border-radius: 5px;margin-left: 5px;background: #BBB;border: 1px solid rgb(228, 221, 221);font-size: 11px;}
    #tabs .active .close, #tabs li:hover .close {background: #F8F8F8;border: 1px solid #777;color: #777;}
    select {text-transform: capitalize;padding-top: 2px;padding-bottom: 2px;}
    .adminzonecontent{min-width:900px}
    .CodeMirror {background: white;font-size: 13px;padding-top: 36px;box-sizing: border-box;}
    .CodeMirror-scroll {min-width: 870px;padding-right:0}
    .activeline {background: rgba(232, 242, 255, 0.33) !important;}
    .toolbarEditor {background: #F8F8F8;padding: 5px;position: absolute;z-index: 999;width: 100%;border-bottom: 1px solid rgb(221, 221, 221);border-top: 1px solid rgb(221, 221, 221);}
    .toolbarEditor input[type="button"] {margin-right: 3px;}
    .subToolbarEditor{display:none;padding-top: 5px;width: 100%;text-align: right;}
    .subToolbarEditor input{height: 20px}
    .subToolbarEditor input[type="button"]{padding: 2px 12px 3px 12px;}
    .location{padding: 2px;color:#444;background:#E3E3E3;border: 1px #ccc solid;font-size: 10px;width: 100%;z-index: 9999;}
    .unsaved{font-weight:bold}
    .unsaved .name:after{ content:"*";}
    .explorer_file_name {position: absolute;bottom: 2px;text-overflow: ellipsis;white-space: nowrap;width: 85px;overflow: hidden;padding: 0 4px;font-size: 13px;line-height: 30px;}
    .explorer_file, .explorer_new {position: relative;width: 90px;height: 90px;margin: 5px;text-align: center;border: 1px #f9f9f9 solid;float: left;border-radius: 4px;padding-top: 6px;}
	.explorer_file.file{background:url(<?php echo BASE_PATH; ?>admin/img/explorersprite.png) 21px -136px no-repeat;}
	.explorer_file.dir{background:url(<?php echo BASE_PATH; ?>admin/img/explorersprite.png) 24px 10px no-repeat;}
    #dirsandfiles{bottom: 0;right: 0;top: 72px;left: 10px;position: absolute;overflow: auto;}
    #editpictures{display: none;}
	#uploadProgress span{display: block;background:#aaa;position:absolute;height:100%;}
	.explorer_new > div {font-size: 60px;color: #777;}
</style>
<script>
    
    var editors = {};
    var pictures = {};
    
    if(typeof opener.callbackExplorer != "function") opener.callbackExplorer = function(file){return false;};
    
    $(document).ready(function() {
	$("#explorer").on("click","li.dir",function(){
	    var path = $(this).attr("path");
	    list(path);
	    $(this).next().find(" > li.dir").show();
	    $(this).next().slideToggle("fast");
            $(".panel").hide();
            $("#explorerfiles").show();
	});
        $("#tabs").on("click","li",function(){
            $(".panel").hide();
            $("#tabs li").removeClass("active");
            $(this).addClass("active");
            var panel = this.id;
            if(panel == "exploreTab") $("#explorerfiles").show();
            else if(this.classList.contains('pict')) {
                //addPicture(this.getAttribute('title'),this.id);
                pictures["tab-" + this.id].reDraw();
            }
            else $("#tab-" + panel).show();
	})
        .on("click",".close", function(e){
            e.stopPropagation();
            var tab = $(this).closest("li");
            if(!tab.hasClass("unsaved") || confirm("This file isn't saved, do you really want to close it ?")){
                var panel = tab.attr('id');
                if(tab.hasClass("file")){
                    editors[panel] = "";
                    $("#tab-" + panel).add(tab).remove();
                }
                else {
                    $("#editpictures").css('display','none');
                    tab.remove();         
                }
                $("#explorerfiles").show();
                $('#tabs li:last').trigger('click');
            }
            
	});


	$("#explorercontainer").on("dblclick",".explorer_file", function(){
            var path = $(this).find(".explorer_file_name").attr("path");
            if($(this).hasClass("dir")){
                    list(path);
            }else pictureOrFile(path);
	});
                
       $("#explorerfiles").parsimonyUpload({ajaxFile: "<?php echo BASE_PATH; ?>admin/action",
	    ajaxFileParams: {action: "upload",path: $("#path").text(),MODULE: "<?php echo MODULE ?>",THEME: "<?php echo THEME ?>",THEMETYPE: "<?php echo THEMETYPE ?>",THEMEMODULE: "<?php echo THEMEMODULE ?>"},
	    start:function(file){
                var obj = $("#explorerfiles").data('uploadParams');
                obj.path = $("#path").text();
                $("#explorerfiles").data('uploadParams',obj);
				var marker = document.createElement('div');
				marker.className = "explorer_file file";
				marker.id = "uploadProgress";
				marker.innerHTML = '<span></span><div class="explorer_file_name" path="">' + file.name + '</div>';
		 		document.getElementById("dirsandfiles").appendChild(marker);
			},
			onProgress:function(file, progress){
				document.querySelector("#uploadProgress span").style.width = progress + "%";
			},
			stop:function(response){
				document.getElementById("uploadProgress").remove();
                if(typeof response.name != "undefined"){
                    list($("#path").text().replace('<?php echo PROFILE_PATH; ?>',''));
                }else{
                    opener.ParsimonyAdmin.execResult(response);
                }
	    }
	});
        
	$("#explorerWrap").on("click",".explorer_file",function(){
            $(".explorer_file_selected").removeClass("explorer_file_selected");
            this.classList.add("explorer_file_selected");
            if( !this.classList.contains("dir") ){
                var file = $(".explorer_file_name",this).attr("path").replace('<?php echo PROFILE_PATH; ?>','');
                opener.callbackExplorer.apply(false, [file]);
            }
            
	})
       .on("click",".new",function() {
		var folder = prompt("Please enter a folder name");
		var html = '';
			if (folder != null) { 
				var idpath = document.getElementById('path').textContent;
				var path = idpath +'/'+ folder ;
			if (folder.indexOf(".") !=-1) {
					html =  '<div class="explorer_file file"><div class="explorer_file_name" path="'+ path +'">' + folder +'</div></div>';
					$.post("<?php echo BASE_PATH; ?>admin/saveCode", { file: path , code : '' },function(data) { 
					if(data == '1')	list($("#path").text().replace('<?php echo PROFILE_PATH; ?>',''));
					else alert('The file has not been created ');
				});
			}else{			
					html =  '<div class="explorer_file dir"><div class="explorer_file_name" path="'+ path +'">' + folder +'</div></div>';
					$.post("<?php echo BASE_PATH; ?>admin/createDir", { directory: path},function(data) { 
						if(data == '1')	list($("#path").text().replace('<?php echo PROFILE_PATH; ?>',''));
						else alert('The folder has not been created ');
					});
				}	
			}  
		})
        .on("change",".historyfile",function(e){
            e.stopPropagation();
            var panel = $(this).closest(".panel");
            var path = panel.data("path");
            var name = panel.attr("id").substring(4);
            var codeEditor = editors["tab-" + name];
            if(this.value != "none"){
                $.post("getBackUp",{
                    replace: this.value,
                    file: path,
                    content: codeEditor.getValue().replace('"','\"')
                },function(data){
                    codeEditor.setValue(data);
                    codeEditor.save();
                    $("#" + name).removeClass("unsaved");
                });
            }
        })
        
        .on("click",".saveCode", function(e){
            e.stopPropagation();
            var panel = $(this).closest(".panel");
            var path = panel.data("path");
            var name = panel.attr("id").substring(4);
            var code = editors["tab-" + name].getValue();
            $.post("<?php echo BASE_PATH; ?>admin/saveCode", { file: path, code : code },
            function(data) {
                $("#" + name).removeClass("unsaved");
            });
        });

    });
     var lastPos = null, lastQuery = null, marked = [];
    function unmark() {
        for (var i = 0; i < marked.length; ++i) marked[i].clear();
        marked.length = 0;
    }

    function search(id) {
        var codeEditor = editors["tab-" + id];
        unmark();                     
        var text = document.getElementById("query").value;
        if (!text) return;
        for (var cursor = codeEditor.getSearchCursor(text); cursor.findNext();)
            marked.push(codeEditor.markText(cursor.from(), cursor.to(), "searched"));

        if (lastQuery != text) lastPos = null;
        var cursor = codeEditor.getSearchCursor(text, lastPos || codeEditor.getCursor());
        if (!cursor.findNext()) {
            cursor = codeEditor.getSearchCursor(text);
            if (!cursor.findNext()) return;
        }
        codeEditor.setSelection(cursor.from(), cursor.to());
        lastQuery = text; lastPos = cursor.to();
    }

    function replaces(id) {
        var codeEditor = editors["tab-" + id];
        unmark();
        var text = document.getElementById("query").value,
        replace = document.getElementById("replace").value;
        if (!text) return;
        
        for (var cursor = codeEditor.getSearchCursor(text); cursor.findNext();)
            cursor.replace(replace);
    }
    function format(id) {
        var codeEditor = editors["tab-" + id];
        codeEditor.autoFormatRange(codeEditor.getCursor(true), codeEditor.getCursor(false));
    }
    function list(path) {
	$.post("<?php echo BASE_PATH; ?>admin/files", { dirPath: path },
	function(data) {
	    $("#explorerfiles").html(data);
	});
    }
    
    function pictureOrFile(path){
        var extension = path.substr((~-path.lastIndexOf(".") >>> 0) + 2);
        var array_img = new Array('jpeg', 'png', 'gif', 'jpg');
        var name = path.replace(/\\/g,'/').replace( /.*\//, '' );
        var techName = path.replace("\\","__").replace(/\//g,"__").replace(".","");
        var completename = '<?php echo BASE_PATH ?>' + path;
        $("#tabs li").removeClass("active");
        if($('#' + techName).length == 0){
        if(array_img.indexOf(extension) > -1) {
            addPicture(completename,techName);
            
            $("#tabs").append('<li id="' + techName + '" title="<?php echo BASE_PATH ?>' + path + '" class="active pict"><div><span class="name">' + name + '</span><span class="close">x</span></div></li>');
            
        }
        else{
            addFile(name,techName,path);
            document.getElementById('explorerfiles').style.display = 'block';
            document.getElementById('editpictures').style.display = 'none';
            $("#tabs").append('<li id="' + techName + '" title="<?php echo PROFILE_PATH ?>' + path + '" class="active file"><div><span class="name">' + name + '</span><span class="close">x</span></div></li>');
        }
        }else{$('#' + techName).trigger('click');
        }
    }
    
    function addPicture(completename,techName){
            $(".panel").hide();
            document.getElementById('editpictures').style.display = 'block';         
            pictures["tab-" + techName] = new pictureEditor(completename,techName);
    }
    function addFile(name,techName,path) {
            
            $.post("<?php echo BASE_PATH; ?>admin/explorerEditor", { file: path },
            function(data) {
            $(".panel").hide();
            $("#tab-" + techName).show();
            $("#explorercontainer").append(data);
            editors["tab-" + techName] = CodeMirror.fromTextArea(document.getElementById("code-" + techName), {
                mode: "css",
                tabMode: "indent",
                lineNumbers: true,
                lineWrapping: true,
                extraKeys: {"Ctrl-S": function(c) {$("#tab-" + c.techName + " .saveCode").trigger("click");return false; }}
            });
            editors["tab-" + techName].techName = techName;
            editors["tab-" + techName].on("change", function(c, n) {
                $("#" + c.techName).addClass("unsaved");  
        });
    }); 
    }
   
</script>
<div id="explorerWrap">
    <ul id="explorer">
        <?php
        $path = dirname($_SERVER['SCRIPT_FILENAME']) . '/' . PROFILE_PATH;
        $depth = 0;
        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($objects as $name => $object) {
            if ($objects->getDepth() > $depth)
                echo '<ul>';
            elseif ($objects->getDepth() < $depth)
                echo str_repeat('</ul>', $depth - $objects->getDepth());
            if ($object->getBasename() != '.' && $object->getBasename() != '..') {
                if ($object->isDir()){
                    $name = str_replace('\\','/',$name);
                    echo '<li class="dir icondir" path="' . str_replace(dirname($_SERVER['SCRIPT_FILENAME']).'/'.PROFILE_PATH, '', $name) . '">' . $object->getBasename() .'</li>' ;
                }
            }
            $depth = $objects->getDepth();
        }
        echo str_repeat('</ul>', $depth - 0);
        ?>
    </ul>
    <div id="rightPart">
        <ul id="tabs">
            <li id="exploreTab">
                <div>Explorer</div>
            </li>
        </ul>
        <div id="explorercontainer">
            <div class="panel" id="explorerfiles">
                <?php
                $dirPath = $path;
                include('files.php');
                ?>
            </div>
            <div class="panel" id="editpictures">
                <?php
                $dirPath = $path;
                include('pictures.php');
                ?>
            </div> 
        </div>
    </div>
</div>