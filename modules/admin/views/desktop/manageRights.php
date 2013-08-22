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
$role = app::getModule('core')->getEntity('role');
?>
<style>
	th,td{height: 23px;width: 87px;}
	td{padding:5px 2px 5px 15px;text-align: center !important}
	.active{background:#AAA} 
	.modulename{font-size: 13px;color: #777;letter-spacing: 1.2px;}
	#enablemodule{margin-bottom: 10px;color: #464646;padding: 3px 7px;font-size: 14px;position: relative;}
	.firsttd{font-size: 18px;letter-spacing: 2px;vertical-align: middle}
	.secondtd{text-transform: capitalize;color: #444;font-size: 12px;text-align: left !important;line-height: 22px;}
	.entity{font-weight: bold;}
	.rolecss{color: #555;text-transform: capitalize;margin-left: 10px;}
	/*.disabled{background-color: #F1F1F1;}*/
	.fieldbg{background-color: rgb(250, 252, 251) !important;}
	input[type='checkbox'] {top : 2px !important;}
	.fieldname{padding : 0 10px 0 30px; text-align:left !important;}
	.legmod{display: block;text-transform: capitalize;margin: 4px 7px 0px 5px;color: #464646;padding: 3px 7px;font-size: 14px;border: 1px solid #DFDFDF;border-radius: 5px;background-color: #F1F1F1;
			background-image: -ms-linear-gradient(top,#F9F9F9,#ECECEC);background-image: -moz-linear-gradient(top,#F9F9F9,#ECECEC);background-image: -o-linear-gradient(top,#F9F9F9,#ECECEC);background-image: -webkit-gradient(linear,left top,left bottom,from(#F9F9F9),to(#ECECEC));background-image: -webkit-linear-gradient(top,#F9F9F9,#ECECEC);
			background-image: linear-gradient(top,#F9F9F9,#ECECEC);}
	.fieldsetmod{background: rgb(252, 252, 252);border: 1px solid #CCC;margin: 10px;border-radius: 8px;padding-bottom: 10px;}</style>
<script>
$(document).ready(function() {
	$(".modelArea").on('change', 'input[type="checkbox"]', function(){
		var obj = {};
		var entity = '';
		var modelArea = $(this).closest(".modelArea");
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
			modelArea[0].querySelector(".modelSerialize").value = JSON.stringify(obj);
	});
});
</script>

<div class="adminzone" id="admin_rights">
	<div id="conf_box_title"><?php echo t('Manage Rights') ?></div>
	<div class="adminzonemenu">
		<?php
		$class = ' firstpanel';
		foreach ($role->select() as $key => $line) {
			echo '<div class="adminzonetab' . $class . '"><a href="#tabs-' . $line->id_role . '" class="ellipsis">' . ucfirst($line->name) . '</a></div>';
			$class = '';
		}
		?>
	</div>
	<div class="adminzonecontent">
		<form action="" method="POST" target="formResult">
			<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />
			<?php foreach ($role->select() as $key => $line) : ?>
				<div id="tabs-<?php echo $line->id_role; ?>" class="admintabs">
					<div style="padding:0 10px">
						<h2 class="rolecss"><?php echo t('%s role', array($line->name)); ?></h2>
						<table style="width:100%">
							<thead>
								<tr>
									<th></th>
									<th>Anonymous<span class="tooltip ui-icon ui-icon-info floatright" data-tooltip="<?php echo t('The only right of reading content and in some cases<br> to add, delete or modify his own content') ;?>"></span></th>
									<th>Editor<span class="tooltip ui-icon ui-icon-info floatright" data-tooltip="<?php echo t('The right of editing content & pages') ;?>"></span></th>
									<th>Developer<span class="tooltip ui-icon ui-icon-info floatright" data-tooltip="<?php echo t('All web development rights : Design, Module, blocks, database & so on') ;?>"></span></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="entities" style="width: 160px;"><?php echo t('Status of', FALSE); ?> <span style="text-transform: capitalize"><?php echo $line->name; ?></span></td>
									<td style="height: 40px;"><input type="radio" name="type[<?php echo $line->id_role; ?>]" value="0" <?php if($line->state == "0") echo 'checked="checked"';  ?> /></td>
									<td style="height: 40px;"><input type="radio" name="type[<?php echo $line->id_role; ?>]" value="1" <?php if($line->state == "1") echo 'checked="checked"';  ?> /></td>
									<td style="height: 40px;"><input type="radio" name="type[<?php echo $line->id_role; ?>]" value="2" <?php if($line->state == "2") echo 'checked="checked"';  ?> /></td>
								</tr>
							</tbody>
						</table>
					   <br>
					</div>

					<div style="clear:both"></div> 
					<fieldset class="fieldsetmod">
						<legend class="legmod"><label class="modulename"><?php echo t('Module', FALSE); ?> :</label>
							<select name="module" onchange="$(this).closest('.admintabs').find('.rightbox').hide();$('#rights-<?php echo $line->id_role ?>-' + this.value).show()">
								<?php
								$modules = \app::$config['modules']['active'];
								unset($modules['admin']);
								foreach ($modules as $moduleName => $type) {
									echo '<option value="' . $moduleName . '">' . $moduleName . '</option>';
								}
								?>
							</select>
						</legend>
						<?php
						foreach (\app::$config['modules']['active'] as $moduleName => $type) {
							echo '<div id="rights-' . $line->id_role. '-' . $moduleName . '" class="rightbox';
							if ($moduleName != 'core')
								echo ' none';
							echo '">';
							?>
						   <div id="enablemodule">
								<label><?php echo t('Enable the %s module for %s role', array(ucfirst($moduleName), $line->name)) ;?> ?</label><input type="hidden" name="modulerights[<?php echo $line->id_role; ?>][<?php echo $moduleName; ?>]" value="0">
								<input type="checkbox" name="modulerights[<?php echo $line->id_role; ?>][<?php echo $moduleName; ?>]" <?php if (\app::getModule($moduleName)->getRights($line->id_role)) echo 'checked'; ?>>
						   </div>
						   <?php $module = app::getModule($moduleName);
								$models = $module->getModel();
								if(count($models) > 0) : ?>
						   <h2><?php echo t('Models', FALSE); ?></h2>
							<table style="margin: 0 auto">
								<thead>
									<tr><th><?php echo t('Name', FALSE); ?></th><th><?php echo t('Display', FALSE); ?></th><th><?php echo t('Insert', FALSE); ?></th><th><?php echo t('Update', FALSE); ?></th><th><?php echo t('Delete', FALSE); ?></th></tr>
								</thead>
								<tbody class="modelArea">
									<?php
									$obj = new \stdClass();
									foreach ($models as $modelName => $model) {
										$myModel = $module->getEntity($modelName);
										$rights = $myModel->getRights($line->id_role);
										$obj->$modelName = new \stdClass();
										$obj->$modelName->rights = $rights;
										$obj->$modelName->fields = new \stdClass();
										?>
										<tr class="line entity">
											<?php
											echo '<td class="secondtd entity">' . $modelName . '</td>
										<td><input type="checkbox" class="display" ' . ($rights & DISPLAY ? 'checked="checked"' : '') . '></td>
											<td><input type="checkbox" class="insert" ' . ($rights & INSERT ? 'checked="checked"' : '') . '></td>
												<td><input type="checkbox" class="update" ' . ($rights & UPDATE ? 'checked="checked"' : '') . '></td>
													<td><input type="checkbox" class="delete" ' . ($rights & DELETE ? 'checked="checked"' : '') . '></td>';

											foreach ($myModel->getFields() as $fieldName => $field) {
												$rights = $field->getRights($line->id_role);
												if($rights === null) $rights = 0;
												$obj->$modelName->fields->$fieldName = $rights;
													echo '<tr class="fieldbg"><td class="fieldname">'. $fieldName .'</td>'.
													'<td><input type="checkbox" class="display" ' . ($rights & DISPLAY ? 'checked="checked"' : '') . '></td>
													<td><input type="checkbox" class="insert" ' . ($rights & INSERT ? 'checked="checked"' : '') . '></td>
													<td><input type="checkbox" class="update" ' . ($rights & UPDATE ? 'checked="checked"' : '') . '></td>
													<td class="disabled"></td>
													</tr>';
											}
									 }

									echo '<input type="hidden" class="modelSerialize" name="modelsrights[' . $line->id_role . '][' . $moduleName . ']" value=\''.  json_encode($obj).'\'>';

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
										<th><?php echo t('Name', FALSE) ?></th>
										<th><?php echo t('Display', FALSE) ?></th>
									</tr>
								</thead>
								<tbody>
									 <?php
									 foreach ($pages as $id_page => $page) {
										$displayChecked = '';
										if ($page->getRights($line->id_role) & DISPLAY)
											$displayChecked = 'checked="checked"';
										?>
										<tr class="line">
										   <?php echo '
										   <td class="secondtd" style="width:200px;">' . s($page->getTitle()) . '</td>
										<td><input type="hidden" name="pagesrights[' . $line->id_role . '][' . $moduleName . '][' . $page->getId() . '][display]" value="0"><input type="checkbox" name="pagesrights[' . $line->id_role . '][' . $moduleName . '][' . $page->getId() . '][display]" class="display" ' . $displayChecked . '></td>';
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
				</div>
				<?php endforeach; ?>
				<br>
				<input type="hidden" name="action" value="saveRights">
			</form>
	</div>
	<div class="adminzonefooter">
		<div id="save_page" class="save ellipsis" onclick="$('form').trigger('submit');return false;"><?php echo t('Save', FALSE); ?></div>
	</div>
</div>
