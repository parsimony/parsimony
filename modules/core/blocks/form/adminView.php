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
 * @package core/blocks
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>
<style>
	#regenerateview{pointer-events: none;background-image: url('<?php echo BASE_PATH ?>admin/img/padlockopen.png');width: 16px;height: 16px;background-repeat: no-repeat;border: none;box-shadow: none;margin-left: 5px;}
	#regenerateview:checked{background-image: url('<?php echo BASE_PATH ?>admin/img/padlockclosed.png')}
	#regenerateview:hover{background: url('<?php echo BASE_PATH ?>admin/img/padlockopen.png') rgb(251, 251, 251);box-shadow: none;background-repeat: no-repeat;border-color: none;}
	#regenerateview:checked:hover {background: url('<?php echo BASE_PATH ?>admin/img/padlockclosed.png') rgb(251, 251, 251);box-shadow: none;background-repeat: no-repeat;border-color: none;}
	#regenerateview[type='checkbox']:checked::before{content : " "}
</style>
<?php if ($this->getConfig('mode') !== 'r') : ?>
	<div class="placeholder">
		<label><?php echo t('Select a table', FALSE) ?></label>
		<select name="entity" id="entity">
			<?php foreach (\app::$config['modules']['active'] as $module => $type) : ?>
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
<?php endif; ?>
<br>
<div class="placeholder">
    <label><?php echo t('Success Message', FALSE); ?></label>
    <input type="text" name="success" value="<?php echo $this->getConfig('success'); ?>">
</div>
<div class="placeholder">
    <label><?php echo t('Fail Message', FALSE); ?></label>
    <input type="text" name="fail" value="<?php echo $this->getConfig('fail'); ?>">
</div>
<?php if ($this->getConfig('mode') !== 'r') : ?>
	<div style="padding:9px 0">
	    <label><?php echo t('Lock the view', FALSE); ?></label>
	    <input type="hidden" value="0" name="regenerateview" />
	    <input type="checkbox" id="regenerateview" name="regenerateview" value="1" <?php
	if ($this->getConfig('regenerateview') == 1)
		echo ' checked="checked"';
	?> />
	</div>
	<br>
	<?php
	$path = PROFILE_PATH . $this->getConfig('pathOfView');
	$editorMode = 'application/x-httpd-php';
	include('modules/admin/views/desktop/editor.php');
	?>
	<script>
		var markerChangeEditor = false;
		var myForm = $("#entity").closest("form");

		$(myForm).on("change", "select", function() {
			markerChangeEditor = true;
			var db = $("#entity").val().split(" - ");
			if ($("#regenerateview").is(":checked")) {
				if (confirm(t("If you confirm, all your changes will be removed"))) {
					$("#regenerateview").prop("checked", false);
				} else {
					return false;
				}
			}
			$.post(BASE_PATH + 'core/callBlock', {module: "<?php $mod = $_POST['typeProgress'] == 'theme' ? THEMEMODULE : MODULE; echo $mod; ?>", idPage: "<?php if ($_POST['typeProgress'] == 'page') echo $_POST['IDPage']; ?>", theme: "<?php if ($_POST['typeProgress'] == 'theme') echo THEME; ?>", id: "<?php echo $_POST['idBlock']; ?>", method: 'generateView', args: "module=" + db[0] + "&entity=" + db[1]}, function(data) {
				codeEditor.setValue(data);
				codeEditor.refresh();
			});
		});

		function editorChange() {
			if (markerChangeEditor == false) {
				$("#regenerateview").prop("checked", true);
			} else {
				markerChangeEditor = false;
			}
		}
	</script>
<?php endif; ?>