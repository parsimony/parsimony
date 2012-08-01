<script>
    var editor;
    $(document).on("change","#historyfile",function(){
        $.post("getBackUp",{
            replace: $(this).val(),
            file: "<?php echo s($path); ?>",
            content: editor.getValue().replace('"','\"')
        },function(data){
            editor.setValue(data);
            editor.save();
        });
    });
    window.onload = function() {
        editor = CodeMirror.fromTextArea(document.getElementById("editor"), {
            lineNumbers: true,
            matchBrackets: true,
            mode: "application/x-httpd-php",
            indentUnit: 4,
            indentWithTabs: true,
            enterMode: "keep",
            tabMode: "shift",
	    lineWrapping: true,
	    onCursorActivity: function() {
		editor.setLineClass(hlLine, null, null);
		hlLine = editor.setLineClass(editor.getCursor().line, "activeline");
	    },
            extraKeys: {"Ctrl-S": function() {$(".save a").trigger("click");return false; }},
            onBlur: function(){editor.save();},
	    onChange: function(){editorChange();}
        });
	var hlLine = editor.setLineClass(0, "activeline");
    };
    var lastPos = null, lastQuery = null, marked = [];
    function changeTheme(theme) {
        editor.setOption('theme', theme);
	$('<link rel="stylesheet" href="<?php echo BASE_PATH; ?>lib/CodeMirror/theme/' + theme + '.css">').appendTo("body");
    }
    function changeMode(mode) {
        editor.setOption('mode', mode);
    }   
    function unmark() {
        for (var i = 0; i < marked.length; ++i) marked[i].clear();
        marked.length = 0;
    }

    function search() {
        unmark();                     
        var text = document.getElementById("query").value;
        if (!text) return;
        for (var cursor = editor.getSearchCursor(text); cursor.findNext();)
            marked.push(editor.markText(cursor.from(), cursor.to(), "searched"));

        if (lastQuery != text) lastPos = null;
        var cursor = editor.getSearchCursor(text, lastPos || editor.getCursor());
        if (!cursor.findNext()) {
            cursor = editor.getSearchCursor(text);
            if (!cursor.findNext()) return;
        }
        editor.setSelection(cursor.from(), cursor.to());
        lastQuery = text; lastPos = cursor.to();
    }

    function replaces() {
        unmark();
        var text = document.getElementById("query").value,
        replace = document.getElementById("replace").value;
        if (!text) return;
        
        for (var cursor = editor.getSearchCursor(text); cursor.findNext();)
            cursor.replace(replace);
    }
    
</script>
<style>.location{padding: 2px;color:#444;background:#E3E3E3;border: 1px #ccc solid;font-size: 10px;width: 100%;z-index: 9999;}</style>
<link rel="stylesheet" href="<?php echo BASE_PATH; ?>lib/CodeMirror/lib/codemirror.css">
<script src="<?php echo BASE_PATH; ?>lib/CodeMirror/lib/codemirror.js"></script>

<link rel="stylesheet" href="<?php echo BASE_PATH; ?>lib/CodeMirror/theme/default.css">

<script src="<?php echo BASE_PATH; ?>lib/CodeMirror/mode/xml/xml.js"></script>
<script src="<?php echo BASE_PATH; ?>lib/CodeMirror/mode/css/css.js"></script>
<script src="<?php echo BASE_PATH; ?>lib/CodeMirror/mode/javascript/javascript.js"></script>
<script src="<?php echo BASE_PATH; ?>lib/CodeMirror/mode/php/php.js"></script>
<script src="<?php echo BASE_PATH; ?>lib/CodeMirror/mode/htmlmixed/htmlmixed.js"></script>
<script src="<?php echo BASE_PATH; ?>lib/CodeMirror/mode/clike/clike.js"></script>
<?php /*
<script src="<?php echo BASE_PATH; ?>lib/CodeMirror/lib/util/dialog.js"></script>
<link rel="stylesheet" href="<?php echo BASE_PATH; ?>lib/CodeMirror/lib/util/dialog.css">
*/ ?>
<script src="<?php echo BASE_PATH; ?>lib/CodeMirror/lib/util/searchcursor.js"></script>
<script src="<?php echo BASE_PATH; ?>lib/CodeMirror/lib/util/search.js"></script>

<style type="text/css" media="screen">
    #editor { margin: 0;position: absolute;top: 0;bottom: 0;left: 0;right: 0;}
    select {text-transform: capitalize;padding-top: 2px;padding-bottom: 2px;}
    .adminzonecontent{min-width:900px}
    .CodeMirror {background: white;width: 900px;margin: 0 15px 0 0;}
    .CodeMirror-scroll {margin-top: 7px;height: 100%;width: 900px;overflow-y: hidden;overflow-x: auto;}
    .activeline {background: rgba(232, 242, 255, 0.33) !important;}
</style>
</head>
<select onchange="changeMode(this.value);">
    <option value="text/x-php">PHP</option>
    <option value="text/html">HTML</option>
    <option value="text/css">CSS</option>
    <option value="text/javascript">JS</option>
</select>
<select title="theme" onchange="changeTheme(this.value);">
    <option selected>default</option>
    <option>night</option>
    <option>monokai</option>
    <option>neat</option>
    <option>elegant</option>
    <option>cobalt</option>
    <option>eclipse</option>
    <option>rubyblue</option>
</select>
<select id="historyfile" style="width:100px">
    <?php
    $backup = array_reverse(glob('profiles/'.PROFILE.'/backup/' . $path . '-*.bak'));
    foreach ($backup as $filename) {
        preg_match('@backup/' . $path . '-(.*).bak@', $filename, $date);
        echo '<option value="' . $date[1] . '">' . date('l jS \of F Y h:i:s A', $date[1]) . '</option>';
    }
    ?>
</select>
<input type="button" onclick="$(this).next().slideToggle();" style="float: right;" value="<?php echo t('Search / Replace', FALSE); ?>" />
<div style="display:none;padding-top: 5px;padding-left: 5px;">
    <input type="text" id="query" >
    <input type="button" onclick="search()" value="Search" />
    <input type="text" id="replace">
    <input type="button" onclick="replaces()" value="Replace" />
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
