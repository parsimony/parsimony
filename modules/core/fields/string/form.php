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
if(isset($this->unique) && $this->unique):
?>
<script>
	$(document).ready(function() {
		$(document).on("blur keyup", "#<?php $fieldName ?>", function(){
			$.post(BASE_PATH + '<?php echo $this->entity->getModule(); ?>/callField',{ entity:"<?php echo $this->entity->getName(); ?>", fieldName:"<?php echo $this->name; ?>", method:'checkUnique', chars: this.value <?php if($row) echo ',id:"' . $row->getId()->value .'"' ?>}, function(data){
			if(data == 1){
				$(".info_<?php $fieldName ?>").empty();
			}else{
				$(".info_<?php $fieldName ?>").text("<?php echo t('It already exists , please choose another') ?>");
			}
			});
		});
		$("#<?php $fieldName ?>").trigger("keyup");
	});
</script>
<?php
endif;
echo $this->displayLabel($fieldName); 
?>
<input type="text" autocomplete="off" name="<?php echo $this->name ?>" id="<?php echo $fieldName ?>" class="<?php echo $this->name ?>" value="<?php echo s($value) ?>" <?php if (!empty($this->regex)) echo 'pattern="' . $this->regex . '"' ?> <?php if ($this->required) echo 'required' ?> />
<?php if(isset($this->unique) && $this->unique): ?>
	<div class="infoUnique info_<?php $fieldName ?>"></div>
<?php endif; ?>