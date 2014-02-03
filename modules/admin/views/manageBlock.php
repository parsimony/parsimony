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
if (is_object($block) == NULL) {
	echo t('No config for this block');
} else {
	?>
	<script type="text/javascript">
		$(document).ready(function() {
			$(document).on('click', "#save_page", function(event) {
				event.preventDefault();
				$('#save_configs').trigger('click');
				return false;
			})
			.on('click', "#btnNewCSSFile", function() {
				var file = $('#newCSSFile').val();
				var pos = $('#posNewCSSFile').val();
				$('#cssFilesCont').append('<div><div class="remitem" onclick="$(this).parent().remove();"></div><input type="hidden" name="CSSFiles[' + file + ']" value="' + pos + '">' + file + ' - ' + pos + '</div>');
				return false;
			})
			.on('click', "#btnNewJSFile", function() {
				var file = $('#newJSFile').val();
				var pos = $('#posNewJSFile').val();
				$('#jsFilesCont').append('<div><div class="remitem" onclick="$(this).parent().remove();"></div><input type="hidden" name="JSFiles[' + file + ']" value="' + pos + '">' + file + ' - ' + pos + '</div>');
				return false;
			});
			<?php if(method_exists($block, 'forkAction')): ?>
			$(document).on('click', "#createNewBlock", function(event) {
				event.preventDefault();
				$.post(BASE_PATH + "core/callBlock", {idPage:"<?php if($typeProgress == 'page') echo $_POST['IDPage']; ?>", theme: "<?php if($typeProgress === 'theme') echo $_POST['THEME']; ?>", id:"<?php echo $block->getId(); ?>", method:'fork', newName: $("#nameNewBlock").val(),newModule :  $("#nameTargetModule").val()},function(data){
					top.ParsimonyAdmin.execResult(data);
					top.ParsimonyAdmin.loadBlock("panelblocks");
				});
				return false;
			}).on("keyup", "#nameNewBlock", function(){
				this.value = this.value.toLowerCase().replace(/[^a-zA-Z]+/,"");
			});
			<?php endif; ?>
			$('input[name="getVars"]').val($.param(window.parent.$_GET));
			$('input[name="postVars"]').val($.param(window.parent.$_POST));
		});
	</script>
	<style>
		#posNewCSSFile ,#posNewJSFile{width: 70px;margin: 0;}
		#cssFilesCont > div, #jsFilesCont > div{padding:7px;margin:2px 0;border:1px solid #ddd}
		.rem{float: left;padding: 0 5px;cursor: pointer;}
		.blockhead{width: 45%;float: left;clear: none;margin: 0 2%;}
		.clear{clear : both}
		.padd{padding-top: 5px;}
		#block_conf select[multiple]{background-image: none !important}
		#block_conf select[multiple]:enabled:hover{background-image: none !important}
		.adminzonecontent{min-height: 500px}
	</style>
	<div id="block_conf" class="adminzone">
		<div id="conf_box_title"><?php echo t('Configuration').' #'.$_POST['idBlock']; ?></div>
		<div class="adminzonemenu">
			<div class="firstpanel adminzonetab"><a href="#accordionBlockConfig" class="ellipsis"><?php echo t('Specific'); ?></a></div>
			<div class="adminzonetab"><a href="#accordionBlockConfigGeneral" class="ellipsis"><?php echo t('General'); ?></a></div>
		</div>
		<div class="adminzonecontent">
			<form method="POST" id="form_confs" target="formResult" action="" style="height: 100%;">
				<div id="accordionBlockConfig" class="admintabs">
					<?php
					echo $block->getAdminView();
					?>
					<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />
					<input type="hidden" name="MODULE" value="<?php echo $_POST['MODULE']; ?>" />
					<input type="hidden" name="THEMEMODULE" value="<?php echo $_POST['THEMEMODULE']; ?>" />
					<input type="hidden" name="THEME" value="<?php echo $_POST['THEME']; ?>" />
					<input type="hidden" name="THEMETYPE" value="<?php echo $_POST['THEMETYPE']; ?>" />
					<input type="hidden" name="getVars" />
					<input type="hidden" name="postVars" />
					<input type="hidden" name="idBlock" value="<?php echo $_POST['idBlock']; ?>" />
					<input type="hidden" name="parentBlock" value="<?php echo $_POST['parentBlock']; ?>" />
					<input type="hidden" name="IDPage" value="<?php echo $_POST['IDPage']; ?>" />
					<input type="hidden" name="typeProgress" value="<?php echo $_POST['typeProgress']; ?>" />
					<input type="hidden" name="action" value="saveBlockConfigs" />
				</div>
				<div id="accordionBlockConfigGeneral" class="admintabs">
					<div class="clear">
						<h3>HTML, CSS & cache</h3>
						<div class="placeholder blockhead">
							<label> <?php echo t('Header Title'); ?></label> <input type="text" name="headerTitle" value="<?php echo $block->getConfig('headerTitle') ?>">
						</div>
						<div class="placeholder blockhead">
							<label><?php echo t('Add CSS Classes'); ?></label> <input type="text" name="cssClasses" value="<?php echo $block->getConfig('cssClasses') ?>">
						</div>
						<div class="placeholder blockhead">
							<label><?php echo t('HTML5 Tags'); ?></label>
							<select name="tag" style="height : 25px">
								<?php if ($block->getConfig('tag') !== false) echo '<option value="' . $block->getConfig('tag') . '">' . $block->getConfig('tag') . '</option>' ?>
								<option value="div">div</option>
								<option value="header">header</option>
								<option value="footer">footer</option>
								<option value="section">section</option>
								<option value="article">article</option>
								<option value="aside">aside</option>
								<option value="hgroup">hgroup</option>
								<option value="nav">nav</option>
							</select>
						</div>
						<div class="placeholder blockhead">
							<label><?php echo t('Cache Seconds'); ?></label> <input type="text" name="maxAge" value="<?php echo $block->getConfig('maxAge') ?>">
						</div>
					</div>
					<div class="clear padd"> 
						<h3>Display</h3>
						<div class="placeholder blockhead">
							<label><?php echo t('Only for the following modules'); ?></label>
							<select name="allowedModules[]" multiple="multiple">
								<?php
								$allowedModules = (array) $block->getConfig('allowedModules');
								$modules = \app::$config['modules']['active'];
								foreach ($modules as $moduleName => $state) {
									echo '<option' . (in_array($moduleName, $allowedModules) ? ' selected="selected"' : '') . '>' . $moduleName . '</option>';
								}
								?>
							</select>
						</div>
						<div class="placeholder blockhead">
							<label><?php echo t('Permissions: only for the selected groups'); ?></label>
							<select name="allowedRoles[]" multiple="multiple">
								<?php
								$allowedRoles = (array) $block->getConfig('allowedRoles');
								$obj = \app::getModule('core')->getEntity('role');
								foreach ($obj as $row) {
									echo '<option value="' . $row->id_role . '"' . (in_array($row->id_role, $allowedRoles) ? ' selected="selected"' : '') . '>' . $row->name . '</option>';
								}
								?>
							</select>
						</div>
					</div>
					<div class="clear padd"> 
						<h3>Ajax load</h3>
						<div class="placeholder blockhead">
							<label><?php echo t('Reload the block every X seconds'); ?></label> <input type="text" name="ajaxReload" value="<?php echo $block->getConfig('ajaxReload') ?>">
						</div>
						<div class="placeholder blockhead"> 
							<label><?php echo t('Ajax On Page Load'); ?></label> <input type="hidden" name="ajaxLoad" value=""> <input style="margin-top: 2px;margin-left: 150px;" type="checkbox" name="ajaxLoad" <?php if ($block->getConfig('ajaxLoad') !== false && $block->getConfig('ajaxLoad') != 0) echo ' checked="checked"'; ?>>
						</div>
					</div>
					<div class="clear padd"> 
						<h3>Include CSS & JS</h3>
						<div class="placeholder blockhead">
							<label><?php echo t('CSS Files'); ?></label>
							<input type="text" id="newCSSFile" placeholder="http://example.com/css.css or lib/fancybox/example.css">
							<select id="posNewCSSFile">
								<option value="header">Header</option>
								<option value="footer">Footer</option>
							</select>
							<input type="button" id="btnNewCSSFile" value="<?php echo t('Add CSS File'); ?>">
							<div id="cssFilesCont">
								<?php
								$files = $block->getConfig('CSSFiles');
								if (!empty($files)) {
									foreach ($files as $file => $pos) {
										echo '<div><div class="remitem" onclick="$(this).parent().remove();">X</div><input type="hidden" name="CSSFiles[' . $file . ']" value="' . $pos . '">' . $file . ' - ' . $pos . '</div>';
									}
								}
								?>
							</div>
						</div>
						<div class="placeholder blockhead">
							<label><?php echo t('JS Files'); ?></label>
							<input type="text" id="newJSFile" placeholder="http://example.com/css.css or lib/fancybox/example.js">
							<select id="posNewJSFile">
								<option value="header">Header</option>
								<option value="footer">Footer</option>
							</select>
							<input type="button" id="btnNewJSFile" value="<?php echo t('Add JS File'); ?>">
							<div id="jsFilesCont">
								<?php
								$files = $block->getConfig('JSFiles');
								if (!empty($files)) {
									foreach ($files as $file => $pos) {
										echo '<div><div class="remitem" onclick="$(this).parent().remove();"></div><input type="hidden" name="JSFiles[' . $file . ']" value="' . $pos . '">' . $file . ' - ' . $pos . '</div>';
									}
								}
								?>
							</div>
						</div>
					</div>
					<div class="clear padd"> 
						<h3>Mode</h3>
						<div class="placeholder blockhead">
							<input type="radio" name="mode" value="r" <?php if ($block->getConfig('mode') === 'r') echo ' checked="checked"'; ?> /> Read-only 
							<input type="radio" name="mode" value="" <?php if ($block->getConfig('mode') === false) echo ' checked="checked"'; ?> /> Read/Write 
						</div>
					</div>
					<?php if(method_exists($block, 'forkAction')): ?>
						<div class="clear padd">
							<h3><?php echo t('Export this configuration in a new block'); ?></h3>
							<div class="placeholder blockhead">
								<?php
								$ownModules = \app::$config['modules']['active'];
								unset($ownModules['core']);
								unset($ownModules['blog']);
								if(count($ownModules) > 0):
									?>
									<label><?php echo t('Name of the new block'); ?></label>
									<input type="text" id="nameNewBlock">
									<label><?php echo t('Target module'); ?></label>
									<select id="nameTargetModule" name="module" >
										<?php
										foreach ($ownModules as $moduleName => $module) {
											if ($moduleName != 'admin' && $moduleName != 'core' && $moduleName != 'blog')
												echo '<option>' . $moduleName . '</option>';
										}
										?>
									</select>
									<input type="button" id="createNewBlock" value="<?php echo t('Fork'); ?>">
								<?php else: ?>
									You have to create at least one module.
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<input type="submit" class="none" id="save_configs">
			</form>
		</div>
		<div class="adminzonefooter">
			<div id="save_page" class="save ellipsis"><?php echo t('Save'); ?></div>
		</div>
	</div>
	<?php
}
?>