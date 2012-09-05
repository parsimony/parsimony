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
 * to contact@parsimony.mobi so we can send you a copy immediately.
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
$val = $row->getId()->value;
$stamp = strtotime(s($value));
?>

<div id="mydate<?php echo $val; ?>">
    <?php if ($this->use == 'normal') : ?>
        <label>
	    <?php echo $this->label ?>
	    <?php if (!empty($this->text_help)): ?>
		<span class="tooltip ui-icon ui-icon-info" data-tooltip="<?php echo t($this->text_help) ?>"></span>
	    <?php endif; ?>
        </label>
        <div>
	    <?php
	    $locale = \app::$request->getLocale();
	    $lang = '<input type="text" class="datesql adddd" style="width: 25px;" value="' . date('d', $stamp) . '" />';
	    $m = date('m', $stamp);
	    $select = ' <select type="text" class="datesql addmm" style="vertical-align: top;height: 28px;width: 70px;font-size: 13px;">';
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
    	<input type="text" class="datesql addyyyy" style="width: 40px;" pattern="^[12][0-9]{3}$" value="<?php echo date('Y', $stamp); ?>" />
    	@ <input class="datesql addhour" type="text" style="width: 25px;" pattern="(?:([01]?[0-9]|2[0-3]):)?([0-5][0-9])" value="<?php echo date('H', $stamp); ?>"> : 
    	<input type="text" class="addminut datesql" style="width: 25px;" maxlength="2" pattern="(?:([01]?[0-9]|2[0-3]):)?([0-5][0-9])" value="<?php echo date('i', $stamp); ?>">
    	<input type="hidden" class="addsecond datesql" pattern="(?:([01]?[0-9]|2[0-3]):)?([0-5][0-9])" value="<?php echo date('s', $stamp); ?>">
        </div>
        <input type="hidden" class="datestatus" name="<?php echo $this->name ?>">
    <?php elseif ($this->use == 'creation') : ?>
        <input type="hidden" name="<?php echo $this->name ?>" value="<?php echo s($value) ?>">
    <?php elseif ($this->use == 'update') : ?>
    <?php endif; ?>
</div>
<script>
    
    if(typeof lead != 'function'){
	function lead(val, length) {
	    var val = val + '';
	    while (val.length < length)  val = '0' + val;
	    return val;
	}
    }
    
    var myForm = $("#mydate<?php echo $val; ?>").closest("form");
    $(myForm).on('change','.datesql', function(e) { 
	var sqltime = lead($('.addyyyy', myForm).val(),4) + '-' + lead($('.addmm', myForm).val(),2) + '-' + lead($('.adddd', myForm).val(),2) + ' ' + lead($('.addhour', myForm).val(),2) + ':' + lead($('.addminut', myForm).val(),2) + ':' + lead($('.addsecond', myForm).val(),2);
	$('.datestatus', myForm).val(sqltime);           
    });
    $('.datesql',myForm).trigger('change');

</script>