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
 * @copyright Julien Gras et Benoît Lorillot
 * 
 * @category Parsimony
 * @package admin
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
$role = app::getModule('core')->getEntity('role');
?>
<style>
	th,td{height: 23px;width: 87px;}
	.tooltip{margin-left: -20px}
	td{padding:5px 2px 5px 15px;}
	.active{background:#AAA} 
	.modulename{font-size: 13px;color: #777;letter-spacing: 1.2px;top: 1px;position: relative;}
	.enablemodule{margin-bottom: 10px;color: #464646;padding: 3px 7px;font-size: 14px;position: relative;}
	.firsttd{font-size: 18px;letter-spacing: 2px;vertical-align: middle}
	.secondtd{text-transform: capitalize;color: #444;font-size: 12px;text-align: left !important;line-height: 22px;}
	.entity{font-weight: bold;}
	.rolecss{color: #555;text-transform: capitalize;margin-left: 10px;}
	.fieldbg{background-color: rgb(250, 252, 251) !important;}
	.fieldname{padding : 0 10px 0 30px; text-align:left !important;}
	.legmod{display: block;text-transform: capitalize;margin: 4px 7px 0px 5px;color: #464646;padding: 3px 7px;font-size: 14px;border: 1px solid #f0f0f0;border-radius: 5px;background-color: #F1F1F1;
			background-image: -ms-linear-gradient(top,#F9F9F9,#ECECEC);background-image: -moz-linear-gradient(top,#F9F9F9,#ECECEC);background-image: -webkit-gradient(linear,left top,left bottom,from(#F9F9F9),to(#ECECEC));background-image: -webkit-linear-gradient(top,#F9F9F9,#ECECEC);
			background-image: linear-gradient(top,#F9F9F9,#ECECEC);}
	.fieldsetmod{background: rgb(252, 252, 252);border: 1px solid #f0f0f0;margin: 10px;border-radius: 8px;padding-bottom: 10px;}
</style>
<script>
$(document).ready(function() {
	$(".modelArea").on('change', 'input[type="checkbox"]', function(){
		
		var obj = {};
		var entity = $(this).closest('td').attr('class');
		var tr = $(this).closest('tr')[0];
		var modelArea = tr.parentNode;

		if( tr.classList.contains("entity")){
			var crud =  this.className.replace('onOff','');
			var prop = this.checked;
			var target = '.fieldbg .' + entity + ' input[type="checkbox"]' + '.' + crud;
			$(target, modelArea).prop( "checked", prop);
		}
		
		$("tr", modelArea).each(function(){
			
				/* Entities */
				if(this.classList.contains("entity")){
					entity = this.querySelector("td").textContent;
					var rights = 0;
					if(this.querySelector(".display").checked){
						rights += 1;
					}
					if(this.querySelector(".insert").checked){
						rights += 2;
					}
					if(this.querySelector(".update").checked){
						rights += 4;
					}
					if(this.querySelector(".delete").checked){
						rights += 8;
					}
					obj[entity] = {"rights" : rights, "fields" : {}};
				}
				
				/* Fields */
				if(this.classList.contains("fieldbg")){
					field = this.querySelector("td").textContent;
					var rights = 0;
					if(this.querySelector(".display").checked){
						rights += 1;
					}
					if(this.querySelector(".insert").checked){
						rights += 2;
					}
					if(this.querySelector(".update").checked){
						rights += 4;
					}
					obj[entity]["fields"][field] = rights;
				}
			});
			modelArea.querySelector(".modelSerialize").value = JSON.stringify(obj);
	});
});
</script>

<div class="adminzone" id="admin_rights">
	<div id="conf_box_title"><?php echo t('Manage Rights') ?></div>
	<div class="adminzonemenu">
		<?php
		foreach ($role->select() as $key => $row) {
			$class = $row->id_role == 2 ? ' firstpanel' : '';
			echo '<div class="adminzonetab' . $class . '"><a href="#tabs-' . $row->id_role . '" class="ellipsis">' . ucfirst($row->name) . '</a></div>';
		}
		?>
	</div>
	<div class="adminzonecontent">
		<form action="" method="POST" target="formResult">
			<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />
			<?php foreach ($role->select() as $key => $row) : ?>
				<div id="tabs-<?php echo $row->id_role; ?>" class="admintabs">
					<h2 class="rolecss"><?php echo t('%s role', array($row->name)); ?></h2>
					<?php if($row->id_role == 1): ?>
						<div style="padding: 10px;">Admin has all rights</div>
					<?php else: 
						$disabled = $_SESSION['id_role'] == $row->id_role ? TRUE : FALSE;
					?>
					<fieldset class="fieldsetmod">
						<legend class="legmod"><label class="modulename"><?php echo t('Module'); ?> :</label>
							<select name="module" onchange="$(this).closest('.admintabs').find('.rightbox').hide();$('#rights-<?php echo $row->id_role ?>-' + this.value).show()">
								<?php
								$modules = \app::$activeModules;
								unset($modules['admin']);
								foreach ($modules as $moduleName => $type) {
									echo '<option value="' . $moduleName . '">' . $moduleName . '</option>';
								}
								?>
							</select>
						</legend>
						<?php
						foreach (\app::$activeModules as $moduleName => $type) {
							$module = app::getModule($moduleName);
							echo '<div id="rights-' . $row->id_role. '-' . $moduleName . '" class="rightbox' . ($moduleName !== 'core' ? ' none' : '') . '">';
							?>
							<div class="enablemodule<?php echo $moduleName === 'core' ? ' none' : ''; ?>">
								<input type="hidden" name="modulerights[<?php echo $row->id_role; ?>][<?php echo $moduleName; ?>]" value="0">
								<input type="checkbox" class="onOff" name="modulerights[<?php echo $row->id_role; ?>][<?php echo $moduleName; ?>]"<?php if ($module->getRights($row->id_role) || $moduleName === 'core') echo ' checked' . ($disabled === TRUE ? ' disabled' : '') ; ?>>
								<label><?php echo t('Enable the %s module for %s role', array(ucfirst($moduleName), $row->name)) ;?></label>
							</div>
						   <?php
								$models = $module->getModel();
								if(count($models) > 0) : ?>
						   <h2><?php echo t('Models'); ?></h2>
							<table style="margin: 0 auto">
								<thead>
									<tr><th><?php echo t('Name'); ?></th><th><?php echo t('Display'); ?></th><th><?php echo t('Insert'); ?></th><th><?php echo t('Update'); ?></th><th><?php echo t('Delete'); ?></th></tr>
								</thead>
								<tbody class="modelArea">
									<?php
									$obj = new \stdClass();
									foreach ($models as $modelName => $model) {
										$myModel = $module->getEntity($modelName);
										$rights = $myModel->getRights($row->id_role);
										$ownRights = $myModel->getRights($_SESSION['id_role']);
										$obj->$modelName = new \stdClass();
										$obj->$modelName->rights = $rights;
										$obj->$modelName->fields = new \stdClass();
										?>
										<tr class="line entity">
											<?php
											echo '<td class="secondtd entity">' . $modelName . '</td>
										<td class="' . $modelName . '"><input type="checkbox" class="display onOff" ' . ($rights & DISPLAY ? 'checked="checked"' : '') . ($ownRights & DISPLAY && $disabled === FALSE ? '' : ' disabled') . '></td>
											<td class="' . $modelName . '"><input type="checkbox" class="insert onOff" ' . ($rights & INSERT ? 'checked="checked"' : '') . ($ownRights & INSERT && $disabled === FALSE ? '' : ' disabled') . '></td>
												<td class="' . $modelName . '"><input type="checkbox" class="update onOff" ' . ($rights & UPDATE ? 'checked="checked"' : '') . ($ownRights & UPDATE &&  $disabled === FALSE ? '' : ' disabled') . '></td>
													<td class="' . $modelName . '"><input type="checkbox" class="delete onOff" ' . ($rights & DELETE ? 'checked="checked"' : '') . ($ownRights & DELETE &&  $disabled === FALSE ? '' : ' disabled') . '></td>';

											foreach ($myModel->getFields() as $fieldName => $field) {
												if($field->entity->getName() === $modelName) { /* avoid pb with extended entities */
													$rights = $field->getRights($row->id_role);
													$ownRights = $field->getRights($_SESSION['id_role']);
													if($rights === null) $rights = 0;
													$obj->$modelName->fields->$fieldName = $rights;
														echo '<tr class="fieldbg"><td class="fieldname">'. $fieldName .'</td>'.
														'<td class="' . $modelName . '"><input type="checkbox" class="display onOff" ' . ($rights & DISPLAY ? 'checked="checked"' : '') . ($ownRights & DISPLAY &&  $disabled === FALSE ? '' : ' disabled') . '></td>
														<td class="' . $modelName . '"><input type="checkbox" class="insert onOff" ' . ($rights & INSERT ? 'checked="checked"' : '') . ($ownRights & INSERT &&  $disabled === FALSE ? '' : ' disabled') . '></td>
														<td class="' . $modelName . '"><input type="checkbox" class="update onOff" ' . ($rights & UPDATE ? 'checked="checked"' : '') . ($ownRights & UPDATE &&  $disabled === FALSE ? '' : ' disabled') . '></td>
														<td class="disabled"></td>
														</tr>';
												}
											}
									 }
									echo '<input type="hidden" class="modelSerialize" name="modelsrights[' . $row->id_role . '][' . $moduleName . ']" value=\''.  json_encode($obj).'\'>';
									 ?>
								</tbody>
							</table>
							<?php endif;
							$pages = $module->getPages();
							if(count($pages) > 0){
								?>
							<h2>Pages</h2>
							<table style="margin: 0 auto">
								<thead>
									<tr>
										<th><?php echo t('Name') ?></th>
										<th><?php echo t('Display') ?></th>
									</tr>
								</thead>
								<tbody>
									 <?php
									 foreach ($pages as $id_page => $page) {
										$displayChecked = '';
										if ($page->getRights($row->id_role) & DISPLAY)
											$displayChecked = 'checked="checked"';
										?>
										<tr class="line">
										   <?php echo '
										   <td class="secondtd" style="width:200px;">' . s($page->getTitle()) . '</td>
										<td><input type="hidden" name="pagesrights[' . $row->id_role . '][' . $moduleName . '][' . $page->getId() . '][display]" value="0"><input type="checkbox" name="pagesrights[' . $row->id_role . '][' . $moduleName . '][' . $page->getId() . '][display]" class="display onOff" ' . $displayChecked . ($page->getRights($_SESSION['id_role']) & DISPLAY || $disabled === FALSE ? '' : ' disabled') . '></td>';
									}
									?>
										</tr>
								</tbody>
							</table>
								<?php } ?>
						<br>
						</div>
						<div class="clearboth"></div>
					<?php
				}
				?>
					</fieldset>
					<?php endif; ?>
				</div>
				<?php endforeach; ?>
				<br>
				<input type="hidden" name="action" value="saveRights">
			</form>
	</div>
	<div class="adminzonefooter">
		<button id="save_page" class="save highlight" onclick="$('form').trigger('submit');return false;"><?php echo t('Save'); ?></button>
	</div>
</div>
