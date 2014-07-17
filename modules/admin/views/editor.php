<?php if (!isset($editorMode)) $editorMode = 'text/html'; ?>
<script>
    codeEditor = "";
    $(document).on("change","#historyfile",function(){
	if(this.value != "none"){
	    $.post("getBackUp",{TOKEN: "<?php echo TOKEN; ?>", 
		replace: this.value,
		file: "<?php echo s($path); ?>",
		content: codeEditor.getValue().replace('"','\"')
	    },function(data){
		codeEditor.setValue(data);
		codeEditor.save();
	    });
	}
    });
    window.onload = function() {
        codeEditor = CodeMirror.fromTextArea(document.getElementById("editor"), {
            lineNumbers: true,
            matchBrackets: true,
            mode: "<?php echo $editorMode; ?>",
            indentUnit: 4,
            indentWithTabs: true,
            enterMode: "keep",
            tabMode: "shift",
	    lineWrapping: true,
            extraKeys: {"Ctrl-S": function() {$(".save a").trigger("click");return false; }}
        });
	codeEditor.on("cursorActivity", function(c) {
	    codeEditor.removeLineClass(hlLine, "background", "activeline");
	    hlLine = codeEditor.getCursor().line;
	    codeEditor.addLineClass(hlLine, "background", "activeline");
	});
	codeEditor.on("change", function(c, n) {
	    if(typeof editorChange == "function") editorChange();
	});
	codeEditor.on("blur", function(c) {
	    codeEditor.save()
	});
	var hlLine = codeEditor.addLineClass(0, "background", "activeline");
	$("#changeModeid").val('<?php echo $editorMode; ?>');
    };
    var lastPos = null, lastQuery = null, marked = [];
    function unmark() {
        for (var i = 0; i < marked.length; ++i) marked[i].clear();
        marked.length = 0;
    }

    function search() {
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

    function replaces() {
        unmark();
        var text = document.getElementById("query").value,
        replace = document.getElementById("replace").value;
        if (!text) return;
        
        for (var cursor = codeEditor.getSearchCursor(text); cursor.findNext();)
            cursor.replace(replace);
    }
    
</script>
<style>.location{padding: 2px;color:#444;background:#E3E3E3;border: 1px #ccc solid;font-size: 10px;width: 100%;z-index: 9999;}</style>
<?php
app::$response->addCSSFile('lib/CodeMirror/lib/codemirror.css');
app::$response->addJSFile('lib/CodeMirror/lib/codemirror.js');
app::$response->addJSFile('lib/CodeMirror/addon/format/formatting.js');
?>

<style type="text/css" media="screen">
    #codeeditor { margin: 0;position: absolute;top: 0;bottom: 0;left: 0;right: 0;}
    select {text-transform: capitalize;padding-top: 2px;padding-bottom: 2px;}
    .adminzonecontent{min-width:900px}
    .CodeMirror {background: white;min-width: 870px;margin: 0 15px 0 0;font-size: 13px;height: auto}
    .CodeMirror-scroll {min-width: 870px;padding-right:0}
    .activeline {background: rgba(232, 242, 255, 0.33) !important;}
    .toolbarEditor{background: #F8F8F8;padding: 5px;position: relative;z-index: 999;box-shadow: 1px 1px 4px #BBB;}
    .subToolbarEditor{display:none;padding-top: 5px;width: 100%;text-align: right;}
    .subToolbarEditor input{height: 20px}
    .subToolbarEditor input[type="button"]{padding: 2px 12px 3px 12px;}
</style>
<div class="toolbarEditor">
    <select id="historyfile" style="width:100px"><option value="none"><?php echo t('History'); ?></option>
	<?php
        $backups = glob('var/backup/' . PROFILE . '/' . $path . '-*.bak');
        if(is_array($backups)){
            $backup = array_reverse($backups);
            foreach ($backup as $filename) {
                preg_match('@var/backup/'.PROFILE.'/' . $path . '-(.*).bak@', $filename, $date);
                if(isset($date[1])) echo '<option value="' . $date[1] . '">' . date('l jS \of F Y h:i:s A', $date[1]) . '</option>';
            }
        }
	?>
    </select>
    <input type="button" onclick="codeEditor.autoFormatRange(codeEditor.getCursor(true), codeEditor.getCursor(false));" style="float: right;" value="<?php echo t('Format'); ?>" />
    <input type="button" onclick="$(this).next().slideToggle('fast');" style="float: right;" value="<?php echo t('Search / Replace'); ?>" />
    <div class="subToolbarEditor">
	<input type="text" id="query" >
	<input type="button" onclick="search()" value="<?php echo t('Search'); ?>" />
	<input type="text" id="replace">
	<input type="button" onclick="replaces()" value="<?php echo t('Replace'); ?>" />
    </div>
</div>
<div style="clear:both"></div>
<textarea id="editor" name="editor"><?php
	$code = '';
	$base= str_replace('profiles/www/', '', $path);
	if (!file_exists($path) && file_exists($base)) {	
		\tools::createDirectory(str_replace(basename($path), '',$base));
		copy($base, $path);
	}
	$code = file_get_contents($path);
	$code = preg_replace('#.*<\?php __halt_compiler\(\); \?>#Usi', '', $code);
	echo s($code);
	?></textarea>
<div class="location">Location : <?php echo $path; ?></div>
