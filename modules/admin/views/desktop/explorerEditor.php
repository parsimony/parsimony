<?php
$path = \app::$request->getParam('file');
$techName = str_replace('\\', '__', str_replace('/', '__', str_replace('.', '', $path)));
?>
<div class="panel" id="tab-<?php echo $techName ?>" data-path="<?php echo PROFILE_PATH.$path; ?>">
<div class="toolbarEditor">
    <input type="button" class="saveCode" value="<?php echo t('Save', FALSE); ?>" />
    <select class="historyfile" style="width:100px"><option value="none"><?php echo t('History', FALSE); ?></option>
	<?php
        $backups = glob('var/backup/' . PROFILE . '/' . PROFILE_PATH . $path . '-*.bak');
        if(is_array($backups)){
            $backup = array_reverse($backups);
            foreach ($backup as $filename) {
                preg_match('@var/backup/'.PROFILE.'/' . PROFILE_PATH . $path . '-(.*).bak@', $filename, $date);
                if(isset($date[1])) echo '<option value="' . $date[1] . '">' . date('l jS \of F Y h:i:s A', $date[1]) . '</option>';
            }
        }
	?>
    </select>
    <input type="button" onclick="replaces('<?php echo $techName ?>')" style="float: right;" value="<?php echo t('Format', FALSE); ?>" />
    <input type="button" onclick="$(this).next().slideToggle('fast');" style="float: right;" value="<?php echo t('Search / Replace', FALSE); ?>" />
    <div class="subToolbarEditor">
	<input type="text" id="query" >
	<input type="button" onclick="search('<?php echo $techName ?>')" value="<?php echo t('Search', FALSE); ?>" />
	<input type="text" id="replace">
	<input type="button" onclick="replaces('<?php echo $techName ?>')" value="<?php echo t('Replace', FALSE); ?>" />
    </div>
</div>
<div style="clear:both"></div>
    <textarea id="code-<?php echo $techName ?>"><?php
	if(is_file(PROFILE_PATH.$path)){
            echo file_get_contents(PROFILE_PATH.$path);
        }
    ?></textarea>
</div>