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
if(isset($this->unique) && $this->unique):
?>
<script>
    $(document).ready(function() {
	$(document).on("keyup", "#insert_<?php echo $this->name ?>", function(){
	    $.post(BASE_PATH + '<?php echo $this->module; ?>/callField',{module:"<?php echo $this->module; ?>", entity:"<?php echo $this->entity; ?>", fieldName:"<?php echo $this->name; ?>", method:'checkUnique', args:'chars=' + this.value}, function(data){
		if(data == 1){
		    $(".info_insert_<?php echo $this->name ?>").html();
		}else{
		    $(".info_insert_<?php echo $this->name ?>").text("<?php echo t('It already exist, please choose another') ?>");
		}
	    });
	});
    });
</script>
<?php
endif;
?>
<div class="placeholder">
    <label for="<?php echo $this->name ?>">
	<?php echo $this->label ?>
	<?php if (!empty($this->text_help)): ?>
    	<span class="tooltip ui-icon ui-icon-info" data-tooltip="<?php echo t($this->text_help) ?>"></span>
	<?php endif; ?>
    </label>
    <input type="text" autocomplete="off" name="<?php echo $this->name ?>" id="insert_<?php echo $this->name ?>" class="<?php echo $this->name ?>" value="<?php echo $this->default ?>" <?php if (!empty($this->regex)) echo 'pattern="' . $this->regex . '"' ?> <?php if ($this->required) echo 'required' ?> />
    <?php if(isset($this->unique) && $this->unique): ?>
	<div class="infoUnique info_insert_<?php echo $this->name ?>"></div>
    <?php endif; ?>
</div>