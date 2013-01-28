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
?>
<link rel="stylesheet" href="<?php echo BASE_PATH; ?>lib/cms.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo BASE_PATH; ?>admin/style.css" type="text/css" media="all" />
<SCRIPT LANGUAGE="Javascript" SRC="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"> </SCRIPT>
<script>window.jQuery || document.write('<script src="' + BASE_PATH + 'lib/jquery/jquery-1.9.0.min.js"><\/script>')</script>
<script type="text/javascript" src="<?php echo BASE_PATH; ?>lib/upload/parsimonyUpload.js"></script>
<style>
    body{min-width: 870px;background: #fff;}
    #explorerWrap{background: #fff;font-family: arial, sans-serif;font-size: 13px;width: 100%;height:544px;display:table;/*display: -webkit-box;display: -moz-box;display: box;-webkit-box-orient: horizontal;-moz-box-orient: horizontal;box-orient: horizontal;*/}
    #explorer{display:table-cell;width: 200px;overflow-y:scroll;height:100%;vertical-align: top;list-style: none;padding:0;margin:0;}
    #explorer ul{display:none;list-style: none;}
    #explorer li.file{display:none}
    #explorer ul{padding-left:20px}
    #explorer > ul li{display:none;}
    #explorer .icondir{background:url(<?php echo BASE_PATH; ?>admin/img/directory.png) no-repeat;float:left;margin-right:3px;width:16px;height:16px}
    #explorer li.dir{padding-left:20px;line-height:20px;cursor:pointer;border:1 #fff solid}
    #explorer li.dir:hover,.explorer_file:hover,.explorer_file_selected {
	border: solid 1px #b8d6fb;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	box-shadow: inset 0 0 1px white;
	-moz-box-shadow: inset 0 0 1px white;
	-webkit-box-shadow: inset 0 0 1px white;
	background: -webkit-gradient(linear, center top, center bottom, from(#fafbfd), to(#ebf3fd));
	background: -moz-linear-gradient(top, #fafbfd, #ebf3fd);
	background: -webkit-gradient(linear, center top, center bottom, from(#fafbfd), to(#ebf3fd));
    }
    .explorer_file{position:relative;width:100px;height:100px;margin:5px;text-align:center;border:1px #ccc solid;float:left;border-radius:4px;padding-top:15px}
    .explorer_file_name{position:absolute;line-height: 15px;bottom:2px;text-overflow: ellipsis;white-space: nowrap;width: 90px;overflow: hidden;padding: 0 5px;font-size: 13px;}
    #path{background: white;border-bottom: 1px solid #CCC;padding: 5px;color: #333;}
    #explorercontainer{display:table-cell;overflow-y: scroll;/*-webkit-box-flex: 1;-moz-box-flex: 1;box-flex: 1;*/}
    #explorerfiles{min-width:465px;height:100%;overflow: hidden;}
</style>
<script>
    
    if(typeof parent.callbackExplorer != "function") parent.callbackExplorer = function(file){return false;};
    
    $(document).ready(function() {
        top.ParsimonyAdmin.resizeConfBox();
	$("#explorer").on("click","li.dir",function(){
	    var path = $(this).attr("path");
	    list(path);
	    $(this).next().find(" > li.dir").show();
	    $(this).next().slideToggle("fast");
	});
	$("#explorercontainer").on("dblclick",".dir", function(){
	    var path = $(this).find(".explorer_file_name").attr("path");
	    list(path);
	});
	$("#explorerWrap").on("click",".explorer_file",function(){
	    $(".explorer_file_selected").removeClass("explorer_file_selected");
	    $(this).addClass("explorer_file_selected");
            var file = "<?php echo BASE_PATH; ?>" + $(".explorer_file_name",this).attr("file").replace('/<?php echo PROFILE_PATH; ?>','<?php echo BASE_PATH; ?>').substring(1);
            console.log("4");
            console.dir(parent.callbackExplorer);
            parent.callbackExplorer.apply(false, [file]);
	});
	$("#explorercontainer").parsimonyUpload({ajaxFile: "<?php echo BASE_PATH; ?>admin/action",
	    ajaxFileParams: {action: "upload",path: "<?php echo PROFILE_PATH ?>" + $("#path").text().substring(1),MODULE: "<?php echo MODULE ?>",THEME: "<?php echo THEME ?>",THEMETYPE: "<?php echo THEMETYPE ?>",THEMEMODULE: "<?php echo THEMEMODULE ?>"},
	    start:function(file){console.log("Start load : " + file.name)},
	    onProgress:function(file, progress){console.log("Load:  " + file.name + " - " + progress + " %</div>")},
	    stop:function(response){
		list($("#path").text().substring(1));
	    }
	});
    });
    
    function list(path) {
	$.post("<?php echo BASE_PATH; ?>admin/files", { dirPath: path },
	function(data) {
	    $("#explorercontainer").html(data);
	    $("#path").text(path);
	    $("#explorercontainer").parsimonyUpload('changeUploadParams',{action: "upload",path: "<?php echo PROFILE_PATH ?>" + $("#path").text().substring(1),MODULE: "<?php echo MODULE ?>",THEME: "<?php echo THEME ?>",THEMETYPE: "<?php echo THEMETYPE ?>",THEMEMODULE: "<?php echo THEMEMODULE ?>"});

	});
    }
</script>
<div>
    <div id="path">/</div>
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
			echo '<li class="dir" path="/' . str_replace(dirname($_SERVER['SCRIPT_FILENAME']).'/'.PROFILE_PATH, '', $name) . '"><span class="icondir"></span>' . $object->getBasename() .'</li>' ;
                    }
                }
		$depth = $objects->getDepth();
	    }
            echo str_repeat('</ul>', $depth - 0);
	    ?>
        </ul>
	<div id="explorercontainer">
            <div id="explorerfiles">
                <?php
                $dirPath = $path;
                include('files.php');
                ?>
            </div>
	</div>
    </div>
</div>