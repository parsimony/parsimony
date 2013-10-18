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

$datestart = isset($_POST['filter'][$property]['start']) ? s($_POST['filter'][$property]['start']) : '';
$dateend = isset($_POST['filter'][$property]['end']) ? s($_POST['filter'][$property]['end']) : '';
?>
<div class="daterangefilter <?php echo $property ?>_filter">
    <label for="<?php echo $property ?>_filter_start" class="filtertitle">
	<?php echo $field->label ?>
    </label>
	<input type="hidden" name="filter[<?php echo $property ?>][start]" id="<?php echo $property ?>_filter_start_hid" value="<?php echo $datestart; ?>" />
	<input type="hidden" name="filter[<?php echo $property ?>][end]" id="<?php echo $property ?>_filter_end_hid" value="<?php echo $dateend; ?>" />
    <input type="date" id="<?php echo $property ?>_filter_start" onblur="document.getElementById('<?php echo $property ?>_filter_start_hid').value  = (this.value ? this.value + ' 00:00:00' : '')"  value="<?php echo substr($datestart, 0, 10) ?>" />
	<input type="date"id="<?php echo $property ?>_filter_end" onblur="document.getElementById('<?php echo $property ?>_filter_end_hid').value = (this.value ? this.value + ' 00:00:00' : '')" value="<?php echo substr($dateend, 0, 10) ?>" />
</div>