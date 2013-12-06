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
.dynamic{display: none;margin-top: 5px}
.static{margin-top: 10px}
.static label,.dynamic label{display: block;border-top-left-radius: 2px;border-top-right-radius: 2px;background-color: #F1F1F1;margin: 5px 0;padding: 4px;width: 80px;text-align: center;}
.static span,.dynamic span{padding: 0 5px}
.btnvalues {width: 29px;height: 8px;margin-bottom: -1px;background:#D3D3D3;border-radius:80px;position:relative;box-shadow:inset 0 1px 2px #AAA;}
.labelvalues {cursor: pointer;display: inline-block;}
.dynamic.start,.dynamic.end{border: 1px solid #CCC;padding: 0px 0 7px;}
.opacity{opacity: 0.6}
.defaultrange{display: none;margin: 5px 0 !important;}
</style>
<script type="text/javascript">
	$(document).ready(function() {
		$('.checkvalues').trigger('change');
		$('.tpl').trigger('change');
		$('.startnow').trigger('change');
		$('.startend').trigger('change');
	});
	var date;
	date = new Date();
	date = date.getUTCFullYear() + '-' +
    ('00' + (date.getUTCMonth()+1)).slice(-2) + '-' +
    ('00' + date.getUTCDate()).slice(-2) + ' ' + 
    ('00' + date.getUTCHours()).slice(-2) + ':' + 
    ('00' + date.getUTCMinutes()).slice(-2) + ':' + 
    ('00' + date.getUTCSeconds()).slice(-2);

	 var isChecked = false;
	$('.adminzonecontent').on('change','.checkvalues', function(){
		isChecked = $('.checkvalues', parent).prop('checked');
		var parent = $(this).parent().parent();
		if(isChecked == '') isChecked == false;
		if(isChecked == true){
			$('.dynamic', parent).css('display','block');
			$('.static', parent).css('display','none');
			$('.static input', parent).removeAttr('name');
		}else{
			$('.dynamic', parent).css('display','none');
			$('.static', parent).css('display','block');
			$('.dynamic input[type="hidden"]', parent).removeAttr('name');
		}
	});	
		$('.adminzonecontent').on('change','select.date', function(){
			$(this).val() == 'datetimerange';
			if($(this).val() == 'daterange') $('.static input').attr('type','datetime-local');
			else $('.static input').attr('type','date');
		});
		$('.adminzonecontent').on('change','.startnow , .startend', function(){
			var parent = $(this).parent();
			isChecked = $('.startnow, .startend', parent).prop('checked');
			if(isChecked == '') isChecked == false;
			if(isChecked == false){
				parent.addClass('opacity');	
				parent.next().removeClass('opacity');
			}
			else{
				var nextdiv = parent.next();
				nextdiv.addClass('opacity');
				parent.removeClass('opacity');
			}
		});
		$('.adminzonecontent').on('change','select.tpl', function(){
			var name = $(this).attr('name'); 
			var prop = $(this).val(); 
			var range = name.replace('[tpl]', '')  + '[default][rangeEnd]'; 
			if(prop != 'range') $('input[name="'+ range +'"').css('display','none');
			else $('input[name="'+ range +'"').css('display','block');
		});
</script>
<div style="margin : 10px;">
	<div class="placeholder"><label>Block ident</label>
		<input type="text" name="blockquery" value="<?php if($this->getConfig('blockquery')) echo $this->getConfig('blockquery'); ?>">
	</div>
<?php
$blockquery = $this->getConfig('blockquery') ? $this->getConfig('blockquery') : 'rapports';
$block = \app::getModule($_POST['MODULE'])->getPage(\app::$request->getParam('IDPage'))->searchBlock($blockquery);
if ($block) {
	$properties = $this->getConfig('properties');
	$selected = $block->getConfig('selected');
	foreach ($selected as $key => $value) {
		if (isset($value['filter'])) { 
			if(isset($value['alias'])){
				$name = $value['alias'];
				$field = new \core\fields\alias ($name, array('label' => $name , 'calculation' => ' ( '. $value['calculated']. ' ) '));
			}else{
				$name = $value['table'] . '.' . $value['property'];
				$table = $value['table'];
				$property = $value['property'];
				list($module, $entity) = explode('_', $table, 2);
				$field = \app::getModule($module)->getEntity($entity)->getField($property);
			}
			if(get_class($field) === 'core\fields\date' || get_class($field) === 'core\fields\publication'){
				$cssname = str_replace('.', '', $name);
				?><style>
					#<?php echo $cssname ?>_checkvalues {display:none;}
					#<?php echo $cssname ?>_checkvalues + .labelvalues span {position: absolute;background: #FFF;display: block;left: -2px;top: -1px;width: 10px;height: 10px;transition: .1s;border-radius: 25px;box-shadow: 0 1px 2px #AAA;}
					#<?php echo $cssname ?>_checkvalues:checked + .labelvalues span {margin-left:25px;}
					#<?php echo $cssname ?>_checkvalues:checked + .labelvalues .btnvalues {background:#44C5EC;}
					#<?php echo $cssname ?>_startnow {display:none;}
					#<?php echo $cssname ?>_startnow + .labelvalues span {position: absolute;background: #FFF;display: block;left: -2px;top: -1px;width: 10px;height: 10px;transition: .1s;border-radius: 25px;box-shadow: 0 1px 2px #AAA;}
					#<?php echo $cssname ?>_startnow:checked + .labelvalues span {margin-left:25px;}
					#<?php echo $cssname ?>_startnow:checked + .labelvalues .btnvalues {background:#44C5EC;}
					#<?php echo $cssname ?>_startend {display:none;}
					#<?php echo $cssname ?>_startend + .labelvalues span {position: absolute;background: #FFF;display: block;left: -2px;top: -1px;width: 10px;height: 10px;transition: .1s;border-radius: 25px;box-shadow: 0 1px 2px #AAA;}
					#<?php echo $cssname ?>_startend:checked + .labelvalues span {margin-left:25px;}
					#<?php echo $cssname ?>_startend:checked + .labelvalues .btnvalues {background:#44C5EC;}
				</style>
			<div style="display: flex">
				<div class="placeholder" style="order: 1;flex: 1 1 auto;align-self: auto;min-width: 40%;margin: 10px 4% 10px 8%;min-height: 50%;">
					<label>Template <?php echo $name  ?></label>
					<select name="properties[<?php echo $name ?>][tpl]" class="date">
						<option <?php if(isset($properties[$name]['tpl']) && $properties[$name]['tpl'] === 'datetimerange') echo 'selected="selected"'; ?>>datetimerange</option>
						<option <?php if(isset($properties[$name]['tpl']) && $properties[$name]['tpl'] === 'daterange') echo 'selected="selected"'; ?>>daterange</option>
					</select>
				</div>
				<div class="" style="order: 2;flex: 1 1 auto;align-self: auto;min-width: 40%;margin: 10px 4% 10px 0%;min-height: 50%;">
					<label style="display: block;margin: 5px 7px 0px 0px;color: #464646;padding: 3px 7px;font-size: 14px;border-bottom: 0px;border-top-left-radius: 2px;border-top-right-radius: 2px;background-color: #F1F1F1;">Default value <?php echo $name  ?></label>
					<div style="margin: 15px 0;line-height: 15px;"><span style="margin-right: 10px">Static values</span>
					<input type="checkbox" name="properties[<?php echo $name ?>][default][state]" id="<?php echo $cssname ?>_checkvalues" class="checkvalues" <?php if(isset($properties[$name]['default']['state'])) echo 'checked="checked"'; ?>>
					<label for="<?php echo $cssname ?>_checkvalues" class="labelvalues">
						<div class="btnvalues"><span></span></div>
					</label>
					<span style="margin-left: 10px">Dynamic values from now</span>
					<div>(If empty, no default values)</div>
					</div>
					
					<div class="static">
						<label>Starting date</label><input type="date" name="properties[<?php echo $name ?>][default][start]" <?php if(isset($properties[$name]['default']['start'])) echo 'value="'.$properties[$name]['default']['start'].'"'; ?>/>						
					</div>
					<div class="static" style="margin-bottom: 15px">
						<label>Ending date</label><input type="date" name="properties[<?php echo $name ?>][default][end]" <?php if(isset($properties[$name]['default']['end'])) echo 'value="'.$properties[$name]['default']['end'].'"'; ?>/>
					</div>
					<div class="dynamic start"><label>Starting date</label>
						<div class="opacity" style="margin: 8px 0;padding-bottom: 5px;line-height: 15px;border-bottom: 1px dashed #ccc;"><span style="margin-right: 10px">Now</span>
							<input type="checkbox" name="properties[<?php echo $name ?>][default][now-start]" <?php if(isset($properties[$name]['default']['now-start'])) echo 'checked="checked"'; ?> id="<?php echo $cssname ?>_startnow" class="startnow">
							<label for="<?php echo $cssname ?>_startnow" style="background: none;display: inline-block;margin: 0;padding: 0" class="labelvalues">
								<div class="btnvalues"><span></span></div>
							</label>
						</div>
						
						<div style="margin-top : 5px">
							<select name="properties[<?php echo $name ?>][default][select-start]" style="width : 90px;display: block;margin-bottom: 5px;">
								<option value="+" <?php if(isset($properties[$name]['default']['select-start'])) echo 'selected="selected"'; ?>>After now</option>
								<option value="-" <?php if(isset($properties[$name]['default']['select-start'])) echo 'selected="selected"'; ?>>Before now</option>
							</select>
							<span>Year</span><input type="number" name="properties[<?php echo $name ?>][default][year-start]" min="0" <?php if(isset($properties[$name]['default']['year-start'])) echo 'value="'.$properties[$name]['default']['year-start'].'"'; ?> class="year" style="width : 40px">
							<span>Month</span><input type="number" name="properties[<?php echo $name ?>][default][month-start]" min="0" <?php if(isset($properties[$name]['default']['month-start'])) echo 'value="'.$properties[$name]['default']['month-start'].'"'; ?> style="width : 40px">
							<span>Day</span><input min="0" name="properties[<?php echo $name ?>][default][day-start]" <?php if(isset($properties[$name]['default']['day-start'])) echo 'value="'.$properties[$name]['default']['day-start'].'"'; ?> style="width : 40px" type="number">
						</div>
					</div>
					<div class="dynamic end" style="margin-bottom: 15px"><label>Ending date</label>
						<div class="opacity" style="margin: 8px 0;padding-bottom: 5px;line-height: 15px;border-bottom: 1px dashed #ccc;"><span style="margin-right: 10px">Now</span>
							<input type="checkbox" name="properties[<?php echo $name ?>][default][now-end]" <?php if(isset($properties[$name]['default']['now-end'])) echo 'checked="checked"'; ?> id="<?php echo $cssname ?>_startend" class="startend">
							<label for="<?php echo $cssname ?>_startend" style="background: none;display: inline-block;margin: 0;padding: 0" class="labelvalues">
								<div class="btnvalues"><span></span></div>
							</label>
						</div>
						
						<div style="margin-top : 5px">
							<select name="properties[<?php echo $name ?>][default][select-end]" style="width : 90px;display: block;margin-bottom: 5px;">
								<option value="+" <?php if(isset($properties[$name]['default']['select-end'])) echo 'selected="selected"'; ?>>After now</option>
								<option value="-" <?php if(isset($properties[$name]['default']['select-end'])) echo 'selected="selected"'; ?>>Before now</option>
							</select>
							<span>Year</span><input type="number" name="properties[<?php echo $name ?>][default][year-end]" <?php if(isset($properties[$name]['default']['year-end'])) echo 'value="'.$properties[$name]['default']['year-end'].'"'; ?> min="0" style="width : 40px">
							<span>Month</span><input type="number" name="properties[<?php echo $name ?>][default][month-end]" <?php if(isset($properties[$name]['default']['month-end'])) echo 'value="'.$properties[$name]['default']['month-end'].'"'; ?> min="0" style="width : 40px">
							<span>Day</span><input min="0" name="properties[<?php echo $name ?>][default][day-end]" <?php if(isset($properties[$name]['default']['day-end'])) echo 'value="'.$properties[$name]['default']['day-end'].'"'; ?> style="width : 40px" type="number">
						</div>	
					</div>
				</div>
			</div>
				<?php	
			}elseif (get_class($field) === 'core\fields\boolean' || get_class($field) === 'core\fields\state') {
			?>
			<div style="display: flex">
				<div class="placeholder" style="order: 1;flex: 1 1 auto;align-self: auto;min-width: 40%;margin: 10px 4% 10px 8%;min-height: 50%;">
					<label>Template <?php echo $name  ?></label>
					<select name="properties[<?php echo $name ?>][tpl]" class="tpl">
						<option <?php if(isset($properties[$name]['tpl']) && $properties[$name]['tpl'] === 'string') echo 'selected="selected"'; ?>>string</option>			
						<option <?php if(isset($properties[$name]['tpl']) && $properties[$name]['tpl'] === 'select') echo 'selected="selected"'; ?>>select</option>
					</select>
				</div>
				<div class="placeholder" style="order: 2;flex: 1 1 auto;align-self: auto;min-width: 40%;margin: 10px 4% 10px 0%;min-height: 50%;">
					<label>Default value <?php echo $name  ?></label>
					<input type="text" name="properties[<?php echo $name ?>][default][rangeStart]" <?php if(isset($properties[$name]['default']['boolstring'])) echo 'value="'.$properties[$name]['default']['rangeStart'].'"'; ?>/>	
				</div>
			</div>	
			<?php }
			else{		
				?>
			<div style="display: flex">
				<div class="placeholder" style="order: 1;flex: 1 1 auto;align-self: auto;min-width: 40%;margin: 10px 4% 10px 8%;min-height: 50%;">
					<label>Template <?php echo $name  ?></label>
					<select name="properties[<?php echo $name ?>][tpl]" class="tpl">
						<option <?php if(isset($properties[$name]['tpl']) && $properties[$name]['tpl'] === 'string') echo 'selected="selected"'; ?>>string</option>			
						<option <?php if(isset($properties[$name]['tpl']) && $properties[$name]['tpl'] === 'choice') echo 'selected="selected"'; ?>>choice</option>
						<option <?php if(isset($properties[$name]['tpl']) && $properties[$name]['tpl'] === 'range') echo 'selected="selected"'; ?>>range</option>
					</select>
				</div>
				<div class="placeholder" style="order: 2;flex: 1 1 auto;align-self: auto;min-width: 40%;margin: 10px 4% 10px 0%;min-height: 50%;">
					<label>Default value <?php echo $name  ?></label>
					<input type="text" placeholder="Min" name="properties[<?php echo $name ?>][default][rangeStart]" <?php if(isset($properties[$name]['default']['rangeStart'])) echo 'value="'.$properties[$name]['default']['rangeStart'].'"'; ?>/>	
					<input type="text" placeholder="Max" class="defaultrange" name="properties[<?php echo $name ?>][default][rangeEnd]" <?php if(isset($properties[$name]['default']['rangeEnd'])) echo 'value="'.$properties[$name]['default']['rangeEnd'].'"'; ?>/>	
				</div>
			</div>
			<?php
			}
		}
	}
}
?>
	
</div>