<?php if (!isset($editorMode)) $editorMode = 'text/html'; ?>
<script>
    codeEditor = "";
    $(document).on("change","#historyfile",function(){
	if(this.value != "none"){
	    $.post("getBackUp",{
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
	    onCursorActivity: function() {
		codeEditor.setLineClass(hlLine, null, null);
		hlLine = codeEditor.setLineClass(codeEditor.getCursor().line, "activeline");
	    },
            extraKeys: {"Ctrl-S": function() {$(".save a").trigger("click");return false; }},
            onBlur: function(){codeEditor.save();},
	    onChange: function(){if(typeof editorChange == "function") editorChange();}
        });
	var hlLine = codeEditor.setLineClass(0, "activeline");
	$("#changeModeid").val('<?php echo $editorMode; ?>');
    };
    var lastPos = null, lastQuery = null, marked = [];
    function changeTheme(theme) {
        codeEditor.setOption('theme', theme);
	$('<link rel="stylesheet" href="<?php echo BASE_PATH; ?>lib/CodeMirror/theme/' + theme + '.css">').appendTo("body");
    }
    function changeMode(mode) {
        codeEditor.setOption('mode', mode);
    }
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
app::$request->page->addCSSFile(BASE_PATH . 'lib/CodeMirror/lib/codemirror.css');
app::$request->page->addCSSFile(BASE_PATH . 'lib/CodeMirror/theme/default.css');
app::$request->page->addJSFile(BASE_PATH . 'lib/CodeMirror/lib/codemirror.js');
app::$request->page->addJSFile(BASE_PATH . 'lib/CodeMirror/mode/xml/xml.js');
app::$request->page->addJSFile(BASE_PATH . 'lib/CodeMirror/mode/css/css.js');
app::$request->page->addJSFile(BASE_PATH . 'lib/CodeMirror/mode/javascript/javascript.js');
app::$request->page->addJSFile(BASE_PATH . 'lib/CodeMirror/mode/php/php.js');
app::$request->page->addJSFile(BASE_PATH . 'lib/CodeMirror/mode/htmlmixed/htmlmixed.js');
app::$request->page->addJSFile(BASE_PATH . 'lib/CodeMirror/mode/clike/clike.js');
app::$request->page->addJSFile(BASE_PATH . 'lib/CodeMirror/lib/util/searchcursor.js');
app::$request->page->addJSFile(BASE_PATH . 'lib/CodeMirror/lib/util/search.js');
app::$request->page->addJSFile(BASE_PATH . 'lib/CodeMirror/lib/util/formatting.js');
?>

<style type="text/css" media="screen">
    #codeeditor { margin: 0;position: absolute;top: 0;bottom: 0;left: 0;right: 0;}
    select {text-transform: capitalize;padding-top: 2px;padding-bottom: 2px;}
    .adminzonecontent{min-width:900px}
    .CodeMirror {background: white;width: 870px;margin: 0 15px 0 0;font-size: 13px;}
    .CodeMirror-scroll {height: 100%;width: 870px;overflow-y: hidden;overflow-x: auto;}
    .activeline {background: rgba(232, 242, 255, 0.33) !important;}
    .toolbarEditor{background: #F8F8F8;padding: 5px;position: relative;z-index: 999;box-shadow: 1px 1px 4px #BBB;}
    .subToolbarEditor{display:none;padding-top: 5px;width: 100%;text-align: right;}
    .subToolbarEditor input{height: 20px}
    .subToolbarEditor input[type="button"]{padding: 2px 12px 3px 12px;}
</style>
<div class="toolbarEditor">
    <select id="changeModeid" onchange="changeMode(this.value);">
	<option value="text/html">HTML</option>
	<option value="application/x-httpd-php">PHP</option>
	<option value="text/css">CSS</option>
	<option value="text/javascript">JS</option>
    </select>
    <select title="theme" onchange="changeTheme(this.value);">
	<option selected>default</option>
	<option>ambiance</option>
	<option>blackboard</option>
	<option>cobalt</option>
	<option>elegant</option>
	<option>vibrant-ink</option>
	<option>xq-dark</option>
	<option>night</option>
	<option>monokai</option>
	<option>neat</option>
	<option>elegant</option>
	<option>cobalt</option>
	<option>eclipse</option>
	<option>rubyblue</option>
    </select>
    <select id="historyfile" style="width:100px"><option value="none"><?php echo t('History', FALSE); ?></option>
	<?php
	$backup = array_reverse(glob('profiles/' . PROFILE . '/backup/' . $path . '-*.bak'));
	foreach ($backup as $filename) {
	    preg_match('@backup/' . $path . '-(.*).bak@', $filename, $date);
	    echo '<option value="' . $date[1] . '">' . date('l jS \of F Y h:i:s A', $date[1]) . '</option>';
	}
	?>
    </select>
    <input type="button" onclick="if($('#changeModeid').val() == 'application/x-httpd-php'){alert('PHP mode formatting is not available.');} codeEditor.autoFormatRange(codeEditor.getCursor(true), codeEditor.getCursor(false));" style="float: right;" value="<?php echo t('Format', FALSE); ?>" />
    <input type="button" onclick="$(this).next().slideToggle('fast');" style="float: right;" value="<?php echo t('Search / Replace', FALSE); ?>" />
    <div class="subToolbarEditor">
	<input type="text" id="query" >
	<input type="button" onclick="search()" value="<?php echo t('Search', FALSE); ?>" />
	<input type="text" id="replace">
	<input type="button" onclick="replaces()" value="<?php echo t('Replace', FALSE); ?>" />
    </div>
</div>
<div style="clear:both"></div>
<textarea id="editor" name="editor"><?php
	$code = '';
	if (file_exists($path))
	    $code = file_get_contents($path);
	$code = preg_replace('#.*<\?php __halt_compiler\(\); \?>#Usi', '', $code);
	echo s($code);
	?></textarea>
<div class="location">Location : <?php echo $path; ?></div>
