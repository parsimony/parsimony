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
$val = $row->getId()->value;
$stamp = strtotime($value);
?>

<div>
    <?php if ($this->use == 'normal') : ?>
        <label>
	    <?php echo $this->label ?>
	    <?php if (!empty($this->text_help)): ?>
		<span class="tooltip ui-icon ui-icon-info" data-tooltip="<?php echo t($this->text_help) ?>"></span>
	    <?php endif; ?>
        </label>
        <div class="field-date-container" style="display:inline-block">
	    <?php
	    $temp = $this->templateForms;
	    if($value == '0000-00-00 00:00:00'){
		$dateNull = true;
		$currentMonth = '';
	    }else{
		$dateNull = false;
		$currentMonth = date('m', $stamp);
	    }
	    $elmts = array('year' => array('value'=> ($dateNull ? '' : date('Y', $stamp)), 'pattern'=>'^[12][0-9]{3}$', 'width'=>'40'),
			    'day' => array('value'=> ($dateNull ? '' : date('d', $stamp)), 'pattern'=>'(?:([01]?[0-9]|2[0-3]):)?([0-5][0-9])', 'width'=>'25'),
			    'hour' => array('value'=> ($dateNull ? '' : date('H', $stamp)), 'pattern'=>'(?:([01]?[0-9]|2[0-3]):)?([0-5][0-9])', 'width'=>'25'),
			    'minute' => array('value'=> ($dateNull ? '' : date('i', $stamp)), 'pattern'=>'(?:([01]?[0-9]|2[0-3]):)?([0-5][0-9])', 'width'=>'25'),
			    'second' => array('value'=> ($dateNull ? '' : date('s', $stamp)), 'pattern'=>'(?:([01]?[0-9]|2[0-3]):)?([0-5][0-9])', 'width'=>'25'));
	    
	    $select = '<select class="field-date-month"  name="'.$this->name.'[month]" style="vertical-align: top;height: 28px;width: 70px;font-size: 13px;"><option></option>';
	    $months = array('01' => t('Jan', false), '02' => t('Feb', false), '03' => t('Mar', false), '04' => t('Apr', false), '05' => t('May', false), '06' => t('Jun', false), '07' => t('Jul', false), '08' => t('Aug', false), '09' => t('Sep', false), '10' => t('Oct', false), '11' => t('Nov', false), '12' => t('Dec', false));
	    foreach ($months as $key => $month) {
		if ($key == $currentMonth)
		    $select .= '<option value="' . $key . '" selected="selected">' . $month . '</option>';
		else
		    $select .= '<option value="' . $key . '">' . $month . '</option>';
	    }
	    $select .='</select> ';
	    
	    $temp = str_replace('%month%', $select, $temp);
	    
	    foreach ($elmts as $key => $v) {
		$temp = str_replace('%'.$key.'%', '<input type="text" class="field-date-'.$key.'" style="width:'.$v['width'].'px" name="'.$this->name.'['.$key.']" pattern="'.$v['pattern'].'" value="'.$v['value'].'" />', $temp);
	    }
	    echo $temp;
	    ?>
        </div>

    <?php elseif ($this->use == 'creation') : ?>
        <input type="hidden" name="<?php echo $this->name ?>" value="<?php echo s($value) ?>">
    <?php elseif ($this->use == 'update') : ?>
    <?php endif; ?>
</div>