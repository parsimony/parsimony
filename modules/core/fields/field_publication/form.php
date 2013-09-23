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
 * @package core/fields
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$visibility = $this->name . '_visibility';
$status = $this->name . '_status';
if($value !== FALSE) $stamp = strtotime(s($value));
else $stamp = time();
?>
<script>
	function lead(val, length) {
		var val = val + '';
		while (val.length < length) val = '0' + val;
		return val;
	}
</script>
<style>
	.pubstatus input.active, .pubstatus input:hover{color: #777;box-shadow: inset 0px 1px 3px rgba(0, 0, 0, 0.2);}
	.pubstatus input {float: left;width: 30%;padding: 3px;cursor: pointer;}
	.visib{margin:2px 0 2px 5px;}
	.visib label{padding: 4px 8px;display: inline-block;}
	.visib input[type='radio']{position: relative;top: 3px}
	.slide{cursor:pointer}
</style>
<?php
echo $this->displayLabel($fieldName);
?>
<div id="publishForm<?php echo $fieldName; ?>">
	<div class="slide"><span class="ui-icon ui-icon-arrowthickstop-1-s" style="display: inline-block;vertical-align: text-bottom;"></span><span style="font-weight: bold;"><?php echo t('Visibility', False) ?> :</span> <span class="visibstatus"></span></div>
	<div class="none">
		<div class="visib">
			<input type="radio" name="<?php echo $this->name ?>_visibility" class="public" data-name="Public" data-name="public" data-val="1" value="1"><label><?php echo t('Public', False) ?></label>
		</div>
		<div class="visib">
			<input type="radio" name="<?php echo $this->name ?>_visibility" class="private" data-name="Private" data-val="0" value="0"><label><?php echo t('Private', False) ?></label>
		</div>
		<div class="visib">
			<input type="radio" class="password" data-name="Password" name="<?php echo $this->name ?>_visibility" data-val="2" value="2"><label><?php echo t('Password protected', False) ?></label>
			<input style="margin-top: 5px" class="none passname" type="text">
		</div>
	</div>
	<div style="padding: 2px 0 0" class="slide">
		<span class="ui-icon ui-icon-arrowthickstop-1-s" style="display: inline-block;vertical-align: text-bottom;"></span><span style="font-weight: bold;" for="<?php echo $this->name ?>">Status :</span> <span class="pubstatuslabel"></span>
	</div>
	<div class="pubstatus none" style="margin: 5px 0">
		<input type="button" style="border-radius: 5px 0 0 5px;" data-val="2" value="<?php echo t('Pending', FALSE); ?>">
		<input type="button" value="<?php echo t('Draft', FALSE); ?>" data-val="1">
		<input type="button" style="border-radius: 0 5px 5px 0;" data-val="0" value="<?php echo t('Publish', FALSE); ?>" class="publish">
		<input type="hidden" class="publishstatus" name="<?php echo $this->name . '_status' ?>">
	</div>
	<div style="clear: both;padding: 5px 0;min-width: 237px;" >
		<span style="font-weight: bold;"><?php echo t('Publish', false); ?> <?php echo t('Immediately', false); ?></span><span style="padding-left:5px"><?php echo t('Or', false); ?></span>
		<span style="padding-left:5px" class="slide"><?php echo t('Edit Planning', false); ?> <img src="<?php echo BASE_PATH ?>admin/img/calendar.gif" style="padding-left:5px;cursor:pointer"/></span>
		<div class="none publishcl">
			<?php
			$locale = \app::$request->getLocale();
			$lang = '<input type="text" class="datesql adddd" value="' . date('d', $stamp) . '" />';
			$m = date('m', $stamp);
			$select = ' <select type="text" class="datesql addmm">';
			$month = array('01' => t('Jan', false), '02' => t('Feb', false), '03' => t('Mar', false), '04' => t('Apr', false), '05' => t('May', false), '06' => t('Jun', false), '07' => t('Jul', false), '08' => t('Aug', false), '09' => t('Sep', false), '10' => t('Oct', false), '11' => t('Nov', false), '12' => t('Dec', false));
			foreach ($month as $key => $month) {
				if ($key == $m)
					$select .= '<option value="' . $key . '" selected="selected">' . $key . '-' . $month . '</option>';
				else
					$select .= '<option value="' . $key . '">' . $key . '-' . $month . '</option>';
			}
			$select .='</select> ';
			if ($locale == 'fr_FR')
				echo $lang . $select;
			else
				echo $select . $lang . ',';
			?>
			<input type="text" class="datesql addyyyy" pattern="^[12][0-9]{3}$" value="<?php echo date('Y', $stamp); ?>" />
			@ <input class="datesql addhour" type="text" pattern="(?:([01]?[0-9]|2[0-3]):)?([0-5][0-9])" value="<?php echo date('H', $stamp); ?>"> : 
			<input type="text" class="addminut datesql" maxlength="2" pattern="(?:([01]?[0-9]|2[0-3]):)?([0-5][0-9])" value="<?php echo date('i', $stamp); ?>">
			<input type="hidden" class="addsecond datesql" pattern="(?:([01]?[0-9]|2[0-3]):)?([0-5][0-9])" value="<?php echo date('s', $stamp); ?>">
		</div>
		<input type="hidden" class="datestatus" name="<?php echo $this->name ?>">
	</div> 
</div>
<script>
	$(document).ready(function() {

		var myForm = $("#publishForm<?php echo $fieldName; ?>").closest("form");

		myForm.on('change','.datesql', function(e) { 
			var sqltime = lead($('.addyyyy', myForm).val(),4) + '-' + lead($('.addmm', myForm).val(),2) + '-' + lead($('.adddd', myForm).val(),2) + ' ' + lead($('.addhour', myForm).val(),2) + ':' + lead($('.addminut', myForm).val(),2) + ':' + lead($('.addsecond', myForm).val(),2);
			$('.datestatus', myForm).val(sqltime);
		})
				
		.on('change','.visib input',function(){
			$('.visibstatus', myForm).text($(this).data("name"));
		})
		
		.on('change','.visib input[type=radio]',function(){
			if($(this).hasClass('password')){
				$('input.passname', myForm).show();
			}else{
				$('input.passname', myForm).hide();
			}
		})
			
		.on('click','.publish',function(){
			$('.publishcl', myForm).hide();
		})
		
		.on('click','.pubstatus input',function(){
			$('.pubstatus input', myForm).removeClass('active');
			$(this).addClass('active');
			$('.pubstatuslabel', myForm).text(this.value);
			var pub = $(this).data("val");
			if(pub == "2"){
				pub = t('Save as Pending');
				$('.publishstatus', myForm).val('2');
			}else if(pub == "1"){
				pub = t('Save Draft');
				$('.publishstatus', myForm).val('1');
			}else {
				pub = t('Publish');
				$('.publishstatus', myForm).val('0');
			}
			$('input[name="add"]', myForm).val(pub);
		})
				
		.on('click','.slide',function(){
			$(this).next().slideToggle("fast");
		});

	<?php 
	/* For update */
	if($value != FALSE): ?>
		if("<?php echo s($row->$visibility); ?>" <= 2){
			$('.visib input[value="<?php echo $row->$visibility; ?>"]').trigger('click');
		}else{
			$('.visib input[data-val="2"]').trigger('click');
			$('.passname').val("<?php echo s($row->$visibility); ?>");
		}

		$('.pubstatus input[data-val="<?php echo s($row->$status); ?>"]').trigger('click');
	<?php else: /* For form add */ ?> 
		$('.pubstatus input[data-val="0"]').trigger('click');
		$('.public', myForm).trigger('click');
	<?php endif; ?>
	
	$('.datesql', myForm).trigger('change');
	$('.datesql',myForm).trigger('change');
});
</script>
