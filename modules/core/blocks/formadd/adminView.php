<div class="placeholder">
    <label><?php echo t('Select a table', FALSE) ?></label>
    <select name="entity" id="entity">
	<?php foreach (\app::$activeModules as $module => $type) : ?>
    	<optgroup label="<?php echo $module ?>">
		<?php
		foreach (\app::getModule($module)->getModel() as $model => $entity) :
		    if ($this->getConfig('entity') != '' && $module . ' - ' . $model == $this->getConfig('module') . ' - ' . $this->getConfig('entity'))
			$selected = ' selected="selected"';
		    else
			$selected = '';
		    ?>
		    <option value="<?php echo $module . ' - ' . $model ?>"<?php echo $selected ?>><?php echo $model ?></option>
		<?php endforeach; ?>
    	</optgroup>
	<?php endforeach; ?>
    </select>
</div>
<br>
<div class="placeholder">
    <label><?php echo t('Success Message', FALSE); ?></label>
    <input type="text" name="success" value="<?php echo $this->getConfig('success'); ?>">
</div>
<div class="placeholder">
    <label><?php echo t('Fail Message', FALSE); ?></label>
    <input type="text" name="fail" value="<?php echo $this->getConfig('fail'); ?>">
</div>
<div style="padding:9px 0">
    <label><?php echo t('Regenerate the view', FALSE); ?> ? </label>
    <input type="hidden" value="0" name="regenerateview" />
    <input type="checkbox" id="regenerateview" name="regenerateview" value="1" <?php
	if ($this->getConfig('regenerateview') == 1)
	    echo ' checked="checked"';
	?> />
</div>
<br>
<?php
$path = PROFILE_PATH .$this->getConfig('pathOfView');
include('modules/admin/views/web/editor.php');
?>
<script>
    
    var myForm = $("#entity").closest("form");
    
    $(myForm).on("change","select",function(){
	var db = $("#entity").val().split(" - ");
        if($("#regenerateview").is(":checked")){
            $.post(BASE_PATH+'core/callBlock',{module:"<?php $mod = $_POST['typeProgress']=='theme' ? THEMEMODULE : MODULE; echo $mod; ?>", idPage:"<?php if($_POST['typeProgress']=='page') echo $_POST['IDPage']; ?>",theme: "<?php if($_POST['typeProgress']=='theme') echo THEME; ?>", id:"<?php echo $_POST['idBlock']; ?>", method:'generateView', args:"module=" + db[0] + "&entity=" + db[1]},function(data){
                editor.setValue(data);
                $("#regenerateview").attr("checked","checked");
                editor.refresh();
            });
        }
    });
   
    function editorChange(){
	$("#regenerateview").removeAttr("checked");
    }
</script>