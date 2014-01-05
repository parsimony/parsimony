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
?>
<script type="text/javascript">
<?php
if (isset($this->unique) && $this->unique):
	?>
	$(document).on("keyup", "#<?php echo $fieldName ?>", function(){
			$.get(BASE_PATH + '<?php echo $this->entity->getModule(); ?>/callField',{ entity:"<?php echo $this->entity->getName(); ?>", fieldName:"<?php echo $this->name; ?>", method:'checkUnique', chars: this.value <?php if($row) echo ',id:"' . $row->getId()->value .'"' ?>}, function(data){
			if(data == 1){
				$(".info_<?php echo $fieldName ?>").empty();
			}else{
				$(".info_<?php echo $fieldName ?>").text("<?php echo t('It already exist, please choose another') ?>");
			}
		});
	});
	<?php
endif;
?>
	$(document).on ('blur','textarea[name="<?php echo $tableName ?>[<?php echo $this->propertyToURL ?>]"],input[name="<?php echo $tableName ?>[<?php echo $this->propertyToURL ?>]"]',function() {
		if(this.value.length > 0 && $('#<?php echo $fieldName ?>').val().length == 0){
			$('#<?php echo $fieldName ?>').addClass('active');
			$.post(BASE_PATH + "admin/titleToUrl", {TOKEN: TOKEN ,url: $(this).val()},
			function(data) {
				$('#<?php echo $fieldName ?>').val(data);
			});
		}
	});
</script>
<style>
	#<?php echo $fieldName ?>{border: 0;background: none;width: 90%;box-shadow: none;height: 21px;line-height: 1px;margin: 7px 0;color: #555;}
	#<?php echo $fieldName ?>:focus{background: #fff;}
</style>

<span><?php echo ucfirst($this->label) ?> : </span>
<input type="text" autocomplete="off" id="<?php echo $fieldName ?>" name="<?php echo $tableName ?>[<?php echo $this->name ?>]" class="<?php echo $this->name ?>" value="<?php echo s($value) ?>" <?php if (!empty($this->regex)) echo 'pattern="' . $this->regex . '"' ?> <?php if ($this->required) echo 'required' ?> />
<?php if (isset($this->unique) && $this->unique): ?>
	<div class="infoUnique info_<?php echo $fieldName ?>"></div>
<?php endif; ?>